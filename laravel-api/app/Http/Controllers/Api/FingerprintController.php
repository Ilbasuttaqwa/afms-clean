<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FingerprintDeviceService;
use App\Services\AttendanceService;
use App\Services\NextJSBridgeService;
<<<<<<< HEAD
use App\Jobs\ProcessFingerprintAttendance;
use App\Jobs\SyncFingerprintDevices;
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
<<<<<<< HEAD
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

class FingerprintController extends Controller
{
    protected $fingerprintService;
    protected $attendanceService;
    protected $bridgeService;

    public function __construct(
        FingerprintDeviceService $fingerprintService,
        AttendanceService $attendanceService,
        NextJSBridgeService $bridgeService
    ) {
        $this->fingerprintService = $fingerprintService;
        $this->attendanceService = $attendanceService;
        $this->bridgeService = $bridgeService;
    }

    /**
<<<<<<< HEAD
     * Receive attendance data from fingerprint device (Real-time with Queue)
     */
    public function receiveAttendance(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        
        try {
            // Rate limiting for device requests
            $deviceId = $request->input('device_id');
            $rateLimitKey = 'fingerprint_device:' . $deviceId;
            $rateLimit = config('fingerprint.security.rate_limit_per_minute', 1000);
            
            if (RateLimiter::tooManyAttempts($rateLimitKey, $rateLimit)) {
                throw new ThrottleRequestsException('Too many requests from device');
            }
            
            RateLimiter::hit($rateLimitKey, 60); // 1 minute window

            // Enhanced validation for real-time processing
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string|max:50',
                'user_id' => 'required|string|max:50',
                'timestamp' => 'required|date|before_or_equal:now|after:' . now()->subDays(7)->toDateString(),
                'verify_type' => 'required|integer|in:1,2,3,4,5', // Extended verification types
                'in_out_mode' => 'required|integer|in:0,1,2,3,4,5', // Extended modes
                'work_code' => 'nullable|integer|min:0|max:255',
                'raw_data' => 'nullable|string|max:1000',
            ]);

            if ($validator->fails()) {
                Log::warning('Fingerprint attendance validation failed', [
                    'device_id' => $deviceId,
                    'errors' => $validator->errors()->toArray(),
                    'request_data' => $request->all()
                ]);
                
=======
     * Receive attendance data from fingerprint device
     */
    public function receiveAttendance(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string',
                'user_id' => 'required|string',
                'timestamp' => 'required|date',
                'verify_type' => 'required|integer',
                'in_out_mode' => 'required|integer',
                'work_code' => 'integer',
            ]);

            if ($validator->fails()) {
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $attendanceData = $validator->validated();
            
<<<<<<< HEAD
            // Security: Check if device is allowed
            if (!$this->isDeviceAllowed($deviceId, $request->ip())) {
                Log::warning('Unauthorized device attempt', [
                    'device_id' => $deviceId,
                    'ip' => $request->ip(),
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized device'
                ], 403);
            }

            // Transform data for processing
            $processedData = [
                'employee_id' => $attendanceData['user_id'],
                'device_user_id' => $attendanceData['user_id'],
                'attendance_time' => $attendanceData['timestamp'],
                'attendance_type' => $attendanceData['in_out_mode'],
                'verification_type' => $attendanceData['verify_type'],
                'work_code' => $attendanceData['work_code'] ?? 0,
                'raw_data' => $attendanceData['raw_data'] ?? json_encode($attendanceData),
                'source_ip' => $request->ip(),
                'received_at' => now()->toISOString(),
            ];
            
            // Log incoming request with performance metrics
            Log::info('Fingerprint attendance received', [
                'device_id' => $deviceId,
                'employee_id' => $processedData['employee_id'],
                'attendance_type' => $processedData['attendance_type'],
                'verification_type' => $processedData['verification_type'],
                'source_ip' => $request->ip(),
            ]);

            // Dispatch to queue for real-time processing
            $job = ProcessFingerprintAttendance::dispatch(
                $processedData,
                $deviceId,
                true // Real-time processing
            );

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            // Update device last activity
            $this->updateDeviceActivity($deviceId);
            
            // Return immediate response for device
            return response()->json([
                'success' => true,
                'message' => 'Attendance data queued for processing',
                'job_id' => $job->getJobId(),
                'response_time_ms' => $responseTime,
                'timestamp' => now()->toISOString(),
            ]);

        } catch (ThrottleRequestsException $e) {
            Log::warning('Rate limit exceeded for device', [
                'device_id' => $deviceId ?? 'unknown',
                'ip' => $request->ip(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Rate limit exceeded. Please slow down requests.',
                'retry_after' => 60
            ], 429);
            
        } catch (\Exception $e) {
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            Log::error('Error processing fingerprint attendance', [
                'device_id' => $deviceId ?? 'unknown',
                'error' => $e->getMessage(),
                'response_time_ms' => $responseTime,
                'request_data' => $request->all(),
=======
            // Log incoming request
            Log::info('Fingerprint attendance received', $attendanceData);

            // Process attendance data
            $result = $this->attendanceService->processAttendance($attendanceData);

            if ($result['success']) {
                // Forward to NextJS using bridge service
                $bridgeResult = $this->bridgeService->forwardAttendance($attendanceData);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Attendance data processed successfully',
                    'data' => $result['data'],
                    'bridge_status' => $bridgeResult
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 400);

        } catch (\Exception $e) {
            Log::error('Error processing fingerprint attendance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ]);

            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Internal server error',
                'error_code' => 'PROCESSING_ERROR'
=======
                'message' => 'Internal server error'
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ], 500);
        }
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(Request $request): JsonResponse
    {
        try {
            $deviceId = $request->input('device_id');
            
            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID is required'
                ], 422);
            }

            $status = $this->fingerprintService->getDeviceStatus($deviceId);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting device status', [
                'error' => $e->getMessage(),
                'device_id' => $request->input('device_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get device status'
            ], 500);
        }
    }

    /**
<<<<<<< HEAD
     * Sync device data (Async with Queue)
=======
     * Sync device data
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
     */
    public function syncDevice(Request $request): JsonResponse
    {
        try {
<<<<<<< HEAD
            $validator = Validator::make($request->all(), [
                'device_id' => 'required|string|max:50',
                'full_sync' => 'boolean',
                'priority' => 'string|in:high,normal,low'
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $deviceId = $request->input('device_id');
            $fullSync = $request->boolean('full_sync', false);
            $priority = $request->input('priority', 'normal');
            
            // Check if device exists and is active
            if (!$this->fingerprintService->deviceExists($deviceId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device not found or inactive'
                ], 404);
            }

            // Dispatch sync job with priority
            $job = SyncFingerprintDevices::dispatch([$deviceId], $fullSync);
            
            if ($priority === 'high') {
                $job->onQueue('fingerprint-realtime');
            }

            Log::info('Device sync job dispatched', [
                'device_id' => $deviceId,
                'full_sync' => $fullSync,
                'priority' => $priority,
                'job_id' => $job->getJobId(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Device sync job queued successfully',
                'job_id' => $job->getJobId(),
                'full_sync' => $fullSync,
                'estimated_completion' => now()->addMinutes($fullSync ? 10 : 2)->toISOString(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error dispatching device sync', [
                'error' => $e->getMessage(),
                'device_id' => $request->input('device_id'),
                'trace' => $e->getTraceAsString()
=======
            $deviceId = $request->input('device_id');
            
            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID is required'
                ], 422);
            }

            $result = $this->fingerprintService->syncDevice($deviceId);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['data'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Error syncing device', [
                'error' => $e->getMessage(),
                'device_id' => $request->input('device_id')
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ]);

            return response()->json([
                'success' => false,
<<<<<<< HEAD
                'message' => 'Failed to queue device sync'
=======
                'message' => 'Failed to sync device'
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ], 500);
        }
    }

    /**
     * Test device connection
     */
    public function testConnection(Request $request): JsonResponse
    {
        try {
            $deviceId = $request->input('device_id');
            
            if (!$deviceId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Device ID is required'
                ], 422);
            }

            $result = $this->fingerprintService->testConnection($deviceId);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'response_time' => $result['response_time'] ?? null
            ]);

        } catch (\Exception $e) {
            Log::error('Error testing device connection', [
                'error' => $e->getMessage(),
                'device_id' => $request->input('device_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to test device connection'
            ], 500);
        }
    }

    /**
     * Forward attendance data to NextJS (Bridge endpoint)
     */
    public function forwardToNextJS(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'attendance_data' => 'required|array',
                'attendance_data.device_id' => 'required|string',
                'attendance_data.device_user_id' => 'required|string',
                'attendance_data.attendance_time' => 'required|date',
                'attendance_data.attendance_type' => 'required|integer|in:0,1',
                'attendance_data.verification_type' => 'integer|in:1,2,3'
            ]);

            $result = $this->bridgeService->forwardAttendance($validated['attendance_data']);

            return response()->json($result, $result['success'] ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to forward attendance to NextJS',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sync status from NextJS (Bridge endpoint)
     */
    public function getSyncStatus(): JsonResponse
    {
        try {
            $result = $this->bridgeService->getSyncStatus();
            return response()->json($result, $result['success'] ? 200 : 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get sync status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}