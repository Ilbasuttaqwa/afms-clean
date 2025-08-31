<?php

namespace App\Jobs;

use App\Models\FingerprintDevice;
use App\Services\FingerprintDeviceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

class SyncFingerprintDevices implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 2;
    public $backoff = [60, 180]; // 1 minute, 3 minutes

    protected array $deviceIds;
    protected bool $fullSync;

    /**
     * Create a new job instance.
     */
    public function __construct(array $deviceIds = [], bool $fullSync = false)
    {
        $this->deviceIds = $deviceIds;
        $this->fullSync = $fullSync;
        $this->onQueue('device-sync');
    }

    /**
     * Execute the job.
     */
    public function handle(FingerprintDeviceService $deviceService): void
    {
        $startTime = microtime(true);
        $syncedDevices = 0;
        $failedDevices = 0;

        try {
            Log::info('Starting device synchronization', [
                'device_count' => count($this->deviceIds),
                'full_sync' => $this->fullSync,
                'job_id' => $this->job->getJobId(),
            ]);

            // Get devices to sync
            $devices = $this->getDevicesToSync();

            if ($devices->isEmpty()) {
                Log::info('No devices to synchronize');
                return;
            }

            // Process devices in chunks to avoid memory issues
            $chunkSize = config('fingerprint.sync.batch_size', 10);
            $maxConcurrent = config('fingerprint.sync.max_concurrent_syncs', 5);
            
            $devices->chunk($chunkSize)->each(function ($deviceChunk) use (&$syncedDevices, &$failedDevices, $deviceService) {
                $this->syncDeviceChunk($deviceChunk, $deviceService, $syncedDevices, $failedDevices);
            });

            $processingTime = round((microtime(true) - $startTime) * 1000, 2);

            Log::info('Device synchronization completed', [
                'synced_devices' => $syncedDevices,
                'failed_devices' => $failedDevices,
                'processing_time_ms' => $processingTime,
                'full_sync' => $this->fullSync,
            ]);

            // Update sync metrics
            $this->updateSyncMetrics($syncedDevices, $failedDevices, $processingTime);

        } catch (Exception $e) {
            Log::error('Device synchronization failed', [
                'error' => $e->getMessage(),
                'synced_devices' => $syncedDevices,
                'failed_devices' => $failedDevices,
                'attempt' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Get devices that need synchronization
     */
    protected function getDevicesToSync()
    {
        $query = FingerprintDevice::where('is_active', true);

        if (!empty($this->deviceIds)) {
            $query->whereIn('device_id', $this->deviceIds);
        } elseif (!$this->fullSync) {
            // Only sync devices that haven't been synced recently
            $syncInterval = config('fingerprint.sync.sync_interval_minutes', 5);
            $query->where(function ($q) use ($syncInterval) {
                $q->whereNull('last_sync_at')
                  ->orWhere('last_sync_at', '<', now()->subMinutes($syncInterval));
            });
        }

        return $query->orderBy('last_sync_at', 'asc')
                    ->orderBy('priority', 'desc')
                    ->get();
    }

    /**
     * Synchronize a chunk of devices
     */
    protected function syncDeviceChunk($devices, FingerprintDeviceService $deviceService, &$syncedDevices, &$failedDevices): void
    {
        foreach ($devices as $device) {
            try {
                $this->syncSingleDevice($device, $deviceService);
                $syncedDevices++;
                
                // Small delay to prevent overwhelming devices
                usleep(100000); // 100ms
                
            } catch (Exception $e) {
                $failedDevices++;
                
                Log::warning('Failed to sync device', [
                    'device_id' => $device->device_id,
                    'device_ip' => $device->ip_address,
                    'error' => $e->getMessage(),
                ]);

                // Update device status
                $device->update([
                    'status' => 'error',
                    'last_error' => $e->getMessage(),
                    'last_sync_attempt_at' => now(),
                ]);
            }
        }
    }

    /**
     * Synchronize a single device
     */
    protected function syncSingleDevice(FingerprintDevice $device, FingerprintDeviceService $deviceService): void
    {
        Log::debug('Syncing device', [
            'device_id' => $device->device_id,
            'ip_address' => $device->ip_address,
        ]);

        // Test device connectivity
        $isOnline = $deviceService->testDeviceConnection(
            $device->ip_address,
            $device->port
        );

        if (!$isOnline) {
            throw new Exception('Device is offline or unreachable');
        }

        // Get device status and info
        $deviceInfo = $deviceService->getDeviceInfo($device->ip_address, $device->port);
        
        // Sync attendance data if this is a full sync
        if ($this->fullSync) {
            $this->syncDeviceAttendanceData($device, $deviceService);
        }

        // Update device record
        $device->update([
            'status' => 'online',
            'last_sync_at' => now(),
            'last_ping_at' => now(),
            'last_error' => null,
            'device_info' => $deviceInfo,
        ]);

        // Cache device status for quick access
        $cacheKey = "device_status:{$device->device_id}";
        Cache::put($cacheKey, [
            'status' => 'online',
            'last_sync' => now()->toISOString(),
            'info' => $deviceInfo,
        ], config('fingerprint.cache.device_status_ttl', 300));

        Log::debug('Device synced successfully', [
            'device_id' => $device->device_id,
            'status' => 'online',
        ]);
    }

    /**
     * Sync attendance data from device
     */
    protected function syncDeviceAttendanceData(FingerprintDevice $device, FingerprintDeviceService $deviceService): void
    {
        try {
            // Get attendance records from device since last sync
            $lastSyncTime = $device->last_sync_at ?? now()->subDays(1);
            $attendanceRecords = $deviceService->getAttendanceRecords(
                $device->ip_address,
                $device->port,
                $lastSyncTime
            );

            if (!empty($attendanceRecords)) {
                Log::info('Syncing attendance records from device', [
                    'device_id' => $device->device_id,
                    'record_count' => count($attendanceRecords),
                ]);

                // Process attendance records in batches
                $batchSize = config('fingerprint.database.bulk_insert_chunk_size', 100);
                $batches = array_chunk($attendanceRecords, $batchSize);

                foreach ($batches as $batch) {
                    foreach ($batch as $record) {
                        // Dispatch individual processing jobs for real-time handling
                        ProcessFingerprintAttendance::dispatch(
                            $record,
                            $device->device_id,
                            false // Not real-time since it's batch sync
                        );
                    }
                }
            }

        } catch (Exception $e) {
            Log::warning('Failed to sync attendance data from device', [
                'device_id' => $device->device_id,
                'error' => $e->getMessage(),
            ]);
            // Don't fail the entire sync for attendance data issues
        }
    }

    /**
     * Update synchronization metrics
     */
    protected function updateSyncMetrics(int $syncedDevices, int $failedDevices, float $processingTime): void
    {
        if (!config('fingerprint.monitoring.metrics_enabled')) {
            return;
        }

        $metricsKey = 'device_sync_metrics';
        $metrics = Cache::get($metricsKey, [
            'total_syncs' => 0,
            'total_devices_synced' => 0,
            'total_devices_failed' => 0,
            'avg_sync_time' => 0,
            'last_sync' => null,
        ]);

        $metrics['total_syncs']++;
        $metrics['total_devices_synced'] += $syncedDevices;
        $metrics['total_devices_failed'] += $failedDevices;
        
        // Calculate rolling average
        $metrics['avg_sync_time'] = (
            ($metrics['avg_sync_time'] * ($metrics['total_syncs'] - 1)) + $processingTime
        ) / $metrics['total_syncs'];
        
        $metrics['last_sync'] = now();

        Cache::put($metricsKey, $metrics, now()->addDay());
    }

    /**
     * Handle job failure
     */
    public function failed(Exception $exception): void
    {
        Log::error('SyncFingerprintDevices job failed permanently', [
            'device_ids' => $this->deviceIds,
            'full_sync' => $this->fullSync,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}