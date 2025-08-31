<?php

namespace App\Jobs;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Services\AttendanceService;
use App\Services\NextJSBridgeService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class ProcessFingerprintAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $backoff = [30, 60, 120]; // Exponential backoff
    public $maxExceptions = 3;

    protected array $attendanceData;
    protected string $deviceId;
    protected bool $isRealtime;

    /**
     * Create a new job instance.
     */
    public function __construct(array $attendanceData, string $deviceId, bool $isRealtime = true)
    {
        $this->attendanceData = $attendanceData;
        $this->deviceId = $deviceId;
        $this->isRealtime = $isRealtime;
        
        // Set queue based on priority
        $this->onQueue($isRealtime ? 'fingerprint-realtime' : 'fingerprint-processing');
    }

    /**
     * Execute the job.
     */
    public function handle(AttendanceService $attendanceService, NextJSBridgeService $nextjsService): void
    {
        $startTime = microtime(true);
        
        try {
            Log::info('Processing fingerprint attendance', [
                'device_id' => $this->deviceId,
                'employee_id' => $this->attendanceData['employee_id'] ?? null,
                'is_realtime' => $this->isRealtime,
                'job_id' => $this->job->getJobId(),
            ]);

            // Validate device exists and is active
            $device = FingerprintDevice::where('device_id', $this->deviceId)
                ->where('is_active', true)
                ->first();

            if (!$device) {
                throw new Exception("Device {$this->deviceId} not found or inactive");
            }

            // Check for duplicate attendance within time window
            if ($this->isDuplicateAttendance()) {
                Log::warning('Duplicate attendance detected, skipping', [
                    'device_id' => $this->deviceId,
                    'employee_id' => $this->attendanceData['employee_id'] ?? null,
                ]);
                return;
            }

            // Process attendance through service
            $attendance = $attendanceService->processAttendance(
                $this->deviceId,
                $this->attendanceData
            );

            if (!$attendance) {
                throw new Exception('Failed to process attendance data');
            }

            // Send to NextJS if enabled and realtime
            if ($this->isRealtime && config('fingerprint.nextjs_integration.enabled')) {
                $this->forwardToNextJS($nextjsService, $attendance);
            }

            // Update device last sync time
            $device->update([
                'last_sync_at' => now(),
                'status' => 'online'
            ]);

            // Cache recent attendance for duplicate detection
            $this->cacheAttendanceRecord($attendance);

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::info('Fingerprint attendance processed successfully', [
                'attendance_id' => $attendance->id,
                'device_id' => $this->deviceId,
                'processing_time_ms' => $processingTime,
                'is_realtime' => $this->isRealtime,
            ]);

            // Update metrics
            $this->updateProcessingMetrics($processingTime, true);

        } catch (Exception $e) {
            $processingTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('Failed to process fingerprint attendance', [
                'device_id' => $this->deviceId,
                'error' => $e->getMessage(),
                'processing_time_ms' => $processingTime,
                'attempt' => $this->attempts(),
                'max_tries' => $this->tries,
            ]);

            // Update metrics
            $this->updateProcessingMetrics($processingTime, false);

            // Update device status on repeated failures
            if ($this->attempts() >= $this->tries) {
                FingerprintDevice::where('device_id', $this->deviceId)
                    ->update(['status' => 'error']);
            }

            throw $e;
        }
    }

    /**
     * Check if this attendance record is a duplicate
     */
    protected function isDuplicateAttendance(): bool
    {
        $employeeId = $this->attendanceData['employee_id'] ?? null;
        $attendanceTime = $this->attendanceData['attendance_time'] ?? null;
        
        if (!$employeeId || !$attendanceTime) {
            return false;
        }

        $cacheKey = "attendance_check:{$this->deviceId}:{$employeeId}:" . 
                   date('Y-m-d-H-i', strtotime($attendanceTime));
        
        return Cache::has($cacheKey);
    }

    /**
     * Cache attendance record for duplicate detection
     */
    protected function cacheAttendanceRecord(FingerprintAttendance $attendance): void
    {
        $cacheKey = "attendance_check:{$attendance->device_id}:{$attendance->employee_id}:" . 
                   $attendance->attendance_time->format('Y-m-d-H-i');
        
        Cache::put($cacheKey, $attendance->id, now()->addMinutes(10));
    }

    /**
     * Forward attendance data to NextJS
     */
    protected function forwardToNextJS(NextJSBridgeService $nextjsService, FingerprintAttendance $attendance): void
    {
        try {
            $response = $nextjsService->sendAttendanceData([
                'id' => $attendance->id,
                'device_id' => $attendance->device_id,
                'employee_id' => $attendance->employee_id,
                'device_user_id' => $attendance->device_user_id,
                'attendance_time' => $attendance->attendance_time->toISOString(),
                'attendance_type' => $attendance->attendance_type,
                'verification_type' => $attendance->verification_type,
                'status' => $attendance->status,
                'processed_at' => now()->toISOString(),
            ]);

            Log::info('Attendance forwarded to NextJS', [
                'attendance_id' => $attendance->id,
                'response_status' => $response['status'] ?? 'unknown',
            ]);

        } catch (Exception $e) {
            Log::warning('Failed to forward attendance to NextJS', [
                'attendance_id' => $attendance->id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail the job if NextJS forwarding fails
        }
    }

    /**
     * Update processing metrics
     */
    protected function updateProcessingMetrics(float $processingTime, bool $success): void
    {
        if (!config('fingerprint.monitoring.metrics_enabled')) {
            return;
        }

        $metricsKey = 'fingerprint_processing_metrics';
        $metrics = Cache::get($metricsKey, [
            'total_processed' => 0,
            'total_failed' => 0,
            'avg_processing_time' => 0,
            'last_updated' => now(),
        ]);

        $metrics['total_processed']++;
        if (!$success) {
            $metrics['total_failed']++;
        }
        
        // Calculate rolling average
        $metrics['avg_processing_time'] = (
            ($metrics['avg_processing_time'] * ($metrics['total_processed'] - 1)) + $processingTime
        ) / $metrics['total_processed'];
        
        $metrics['last_updated'] = now();

        Cache::put($metricsKey, $metrics, now()->addHour());
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        Log::error('ProcessFingerprintAttendance job failed permanently', [
            'device_id' => $this->deviceId,
            'employee_id' => $this->attendanceData['employee_id'] ?? null,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);

        // Update device status
        FingerprintDevice::where('device_id', $this->deviceId)
            ->update([
                'status' => 'error',
                'last_error' => $exception->getMessage(),
            ]);
    }
}