<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
<<<<<<< HEAD
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Exception;
use Carbon\Carbon;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
=======
use Exception;
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

class NextJSBridgeService
{
    protected $baseUrl;
    protected $apiToken;
    protected $timeout;
<<<<<<< HEAD
    protected $retryAttempts;
    protected $retryDelay;
    protected $circuitBreakerKey;
    protected $circuitBreakerThreshold;
    protected $circuitBreakerTimeout;
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

    public function __construct()
    {
        $this->baseUrl = config('fingerprint.nextjs_integration.api_url');
        $this->apiToken = config('fingerprint.nextjs_integration.api_token');
<<<<<<< HEAD
        $this->timeout = config('fingerprint.nextjs_integration.timeout', 30);
        $this->retryAttempts = config('fingerprint.nextjs_integration.retry_attempts', 3);
        $this->retryDelay = config('fingerprint.nextjs_integration.retry_delay', 1000); // milliseconds
        $this->circuitBreakerKey = 'nextjs_circuit_breaker';
        $this->circuitBreakerThreshold = config('fingerprint.nextjs_integration.circuit_breaker_threshold', 5);
        $this->circuitBreakerTimeout = config('fingerprint.nextjs_integration.circuit_breaker_timeout', 300); // seconds
    }

    /**
     * Forward attendance data to NextJS API with optimizations
     */
    public function forwardAttendance(array $attendanceData): array
    {
        if ($this->isCircuitBreakerOpen()) {
            return [
                'success' => false,
                'message' => 'Circuit breaker is open, skipping request',
                'circuit_breaker' => true
            ];
        }

        try {
            $endpoint = $this->baseUrl . config('fingerprint.nextjs_integration.endpoints.attendance');
            $requestId = uniqid('req_');
            
            $response = $this->makeRequestWithRetry('POST', $endpoint, [
                'deviceId' => $attendanceData['device_id'] ?? null,
                'userId' => $attendanceData['device_user_id'] ?? null,
                'timestamp' => $attendanceData['attendance_time'] ?? null,
                'type' => $attendanceData['attendance_type'] ?? 0,
                'verificationType' => $attendanceData['verification_type'] ?? 1,
                'workCode' => $attendanceData['work_code'] ?? null,
                'rawData' => $attendanceData['raw_data'] ?? null,
                'source' => 'laravel-bridge',
                'request_id' => $requestId,
                'timestamp' => now()->toISOString()
            ]);

            if ($response['success']) {
                $this->recordCircuitBreakerSuccess();
                
                Log::info('Attendance forwarded to NextJS successfully', [
                    'attendance_id' => $attendanceData['id'] ?? null,
                    'device_user_id' => $attendanceData['device_user_id'] ?? null,
                    'request_id' => $requestId,
                    'response_time' => $response['response_time'] ?? null
=======
        $this->timeout = 30;
    }

    /**
     * Forward attendance data to NextJS API
     */
    public function forwardAttendance(array $attendanceData): array
    {
        try {
            $endpoint = $this->baseUrl . config('fingerprint.nextjs_integration.endpoints.attendance');
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0'
                ])
                ->post($endpoint, [
                    'deviceId' => $attendanceData['device_id'] ?? null,
                    'userId' => $attendanceData['device_user_id'] ?? null,
                    'timestamp' => $attendanceData['attendance_time'] ?? null,
                    'type' => $attendanceData['attendance_type'] ?? 0,
                    'verificationType' => $attendanceData['verification_type'] ?? 1,
                    'workCode' => $attendanceData['work_code'] ?? null,
                    'rawData' => $attendanceData['raw_data'] ?? null,
                    'source' => 'laravel-bridge'
                ]);

            if ($response->successful()) {
                Log::info('Attendance forwarded to NextJS successfully', [
                    'attendance_id' => $attendanceData['id'] ?? null,
                    'device_user_id' => $attendanceData['device_user_id'] ?? null
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ]);

                return [
                    'success' => true,
                    'message' => 'Attendance forwarded successfully',
<<<<<<< HEAD
                    'data' => $response['data'],
                    'request_id' => $requestId,
                    'response_time' => $response['response_time'] ?? null
                ];
            } else {
                $this->recordCircuitBreakerFailure();
                
                Log::error('Failed to forward attendance to NextJS', [
                    'error' => $response['error'],
                    'attendance_data' => $attendanceData,
                    'request_id' => $requestId,
                    'attempts' => $response['attempts'] ?? null
=======
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to forward attendance to NextJS', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'attendance_data' => $attendanceData
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to forward attendance',
<<<<<<< HEAD
                    'error' => $response['error'],
                    'request_id' => $requestId
                ];
            }
        } catch (Exception $e) {
            $this->recordCircuitBreakerFailure();
            
=======
                    'error' => $response->body()
                ];
            }
        } catch (Exception $e) {
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            Log::error('Exception while forwarding attendance to NextJS', [
                'error' => $e->getMessage(),
                'attendance_data' => $attendanceData
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while forwarding attendance',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Get sync status from NextJS with caching
     */
    public function getSyncStatus(): array
    {
        // Check cache first
        $cacheKey = "nextjs_sync_status_global";
        $cachedStatus = Cache::get($cacheKey);
        
        if ($cachedStatus) {
            return [
                'success' => true,
                'data' => $cachedStatus,
                'cached' => true
            ];
        }

        if ($this->isCircuitBreakerOpen()) {
            return [
                'success' => false,
                'message' => 'Circuit breaker is open, skipping request',
                'circuit_breaker' => true
            ];
        }

        try {
            $endpoint = $this->baseUrl . '/monitoring/sync-status';
            
            $response = $this->makeRequestWithRetry('GET', $endpoint);

            if ($response['success']) {
                $this->recordCircuitBreakerSuccess();
                
                // Cache the result for 5 minutes
                Cache::put($cacheKey, $response['data'], 300);
                
                return [
                    'success' => true,
                    'data' => $response['data'],
                    'cached' => false,
                    'response_time' => $response['response_time'] ?? null
                ];
            } else {
                $this->recordCircuitBreakerFailure();
                
                return [
                    'success' => false,
                    'message' => 'Failed to get sync status',
                    'error' => $response['error']
                ];
            }
        } catch (Exception $e) {
            $this->recordCircuitBreakerFailure();
            
=======
     * Get sync status from NextJS
     */
    public function getSyncStatus(): array
    {
        try {
            $endpoint = $this->baseUrl . '/monitoring/sync-status';
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0'
                ])
                ->get($endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to get sync status',
                    'error' => $response->body()
                ];
            }
        } catch (Exception $e) {
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            return [
                'success' => false,
                'message' => 'Exception occurred while getting sync status',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Notify NextJS about device status change with optimizations
     */
    public function notifyDeviceStatus(string $deviceId, string $status, array $additionalData = []): array
    {
        if ($this->isCircuitBreakerOpen()) {
            return [
                'success' => false,
                'message' => 'Circuit breaker is open, skipping request',
                'circuit_breaker' => true
            ];
        }

        try {
            $endpoint = $this->baseUrl . '/monitoring/device-status';
            $notificationId = uniqid('notify_');
            
            $response = $this->makeRequestWithRetry('POST', $endpoint, array_merge([
                'deviceId' => $deviceId,
                'status' => $status,
                'timestamp' => now()->toISOString(),
                'source' => 'laravel-bridge',
                'notification_id' => $notificationId
            ], $additionalData));

            if ($response['success']) {
                $this->recordCircuitBreakerSuccess();
                
                Log::info('Device status notification sent to NextJS', [
                    'device_id' => $deviceId,
                    'status' => $status,
                    'notification_id' => $notificationId,
                    'response_time' => $response['response_time'] ?? null
                ]);

                return [
                    'success' => true,
                    'message' => 'Device status notification sent successfully',
                    'data' => $response['data'],
                    'notification_id' => $notificationId,
                    'response_time' => $response['response_time'] ?? null
                ];
            } else {
                $this->recordCircuitBreakerFailure();
                
                Log::error('Failed to send device status notification to NextJS', [
                    'error' => $response['error'],
                    'device_id' => $deviceId,
                    'notification_id' => $notificationId,
                    'attempts' => $response['attempts'] ?? null
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to notify device status',
                    'error' => $response['error'],
                    'notification_id' => $notificationId
                ];
            }
        } catch (Exception $e) {
            $this->recordCircuitBreakerFailure();
            
            Log::error('Exception while sending device status notification to NextJS', [
                'error' => $e->getMessage(),
                'device_id' => $deviceId
            ]);

=======
     * Notify NextJS about device status change
     */
    public function notifyDeviceStatus(string $deviceId, string $status, array $additionalData = []): array
    {
        try {
            $endpoint = $this->baseUrl . '/monitoring/device-status';
            
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0'
                ])
                ->post($endpoint, array_merge([
                    'deviceId' => $deviceId,
                    'status' => $status,
                    'timestamp' => now()->toISOString(),
                    'source' => 'laravel-bridge'
                ], $additionalData));

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Device status notification sent successfully',
                    'data' => $response->json()
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Failed to notify device status',
                    'error' => $response->body()
                ];
            }
        } catch (Exception $e) {
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            return [
                'success' => false,
                'message' => 'Exception occurred while notifying device status',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Test connection to NextJS API with health check
=======
     * Test connection to NextJS API
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
     */
    public function testConnection(): array
    {
        try {
            $endpoint = $this->baseUrl . '/health';
<<<<<<< HEAD
            $testId = uniqid('test_');
            
            $startTime = microtime(true);
            
            $response = Http::timeout(10) // Shorter timeout for health check
                ->withHeaders([
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0',
                    'X-Test-ID' => $testId
                ])
                ->get($endpoint);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($response->successful()) {
                $responseData = $response->json();
                
                return [
                    'success' => true,
                    'message' => 'Connection to NextJS API successful',
                    'data' => $responseData,
                    'response_time' => $responseTime,
                    'test_id' => $testId,
                    'circuit_breaker_status' => $this->getCircuitBreakerStatus()
=======
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0'
                ])
                ->get($endpoint);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection to NextJS API successful',
                    'response_time' => $response->handlerStats()['total_time'] ?? null
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Connection to NextJS API failed',
                    'status' => $response->status(),
<<<<<<< HEAD
                    'error' => $response->body(),
                    'response_time' => $responseTime,
                    'test_id' => $testId
=======
                    'error' => $response->body()
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Exception occurred while testing connection',
<<<<<<< HEAD
                'error' => $e->getMessage(),
                'circuit_breaker_status' => $this->getCircuitBreakerStatus()
=======
                'error' => $e->getMessage()
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Send bulk attendance data to NextJS with optimizations
     */
    public function forwardBulkAttendance(array $attendanceDataArray): array
    {
        if ($this->isCircuitBreakerOpen()) {
            return [
                'success' => false,
                'message' => 'Circuit breaker is open, skipping request',
                'circuit_breaker' => true
            ];
        }

        try {
            $endpoint = $this->baseUrl . '/fingerprint/bulk-attendance';
            $batchId = uniqid('batch_');
=======
     * Send bulk attendance data to NextJS
     */
    public function forwardBulkAttendance(array $attendanceDataArray): array
    {
        try {
            $endpoint = $this->baseUrl . '/fingerprint/bulk-attendance';
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            
            $formattedData = array_map(function($attendance) {
                return [
                    'deviceId' => $attendance['device_id'] ?? null,
                    'userId' => $attendance['device_user_id'] ?? null,
                    'timestamp' => $attendance['attendance_time'] ?? null,
                    'type' => $attendance['attendance_type'] ?? 0,
                    'verificationType' => $attendance['verification_type'] ?? 1,
                    'workCode' => $attendance['work_code'] ?? null,
                    'rawData' => $attendance['raw_data'] ?? null
                ];
            }, $attendanceDataArray);

<<<<<<< HEAD
            $response = $this->makeRequestWithRetry('POST', $endpoint, [
                'attendances' => $formattedData,
                'source' => 'laravel-bridge',
                'batch_id' => $batchId,
                'timestamp' => now()->toISOString()
            ]);

            if ($response['success']) {
                $this->recordCircuitBreakerSuccess();
                
                Log::info('Bulk attendance forwarded to NextJS successfully', [
                    'count' => count($attendanceDataArray),
                    'batch_id' => $batchId
=======
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Token' => $this->apiToken,
                    'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0'
                ])
                ->post($endpoint, [
                    'attendances' => $formattedData,
                    'source' => 'laravel-bridge',
                    'batch_id' => uniqid('batch_')
                ]);

            if ($response->successful()) {
                Log::info('Bulk attendance forwarded to NextJS successfully', [
                    'count' => count($attendanceDataArray)
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ]);

                return [
                    'success' => true,
                    'message' => 'Bulk attendance forwarded successfully',
<<<<<<< HEAD
                    'data' => $response['data'],
                    'batch_id' => $batchId
                ];
            } else {
                $this->recordCircuitBreakerFailure();
                
                Log::error('Failed to forward bulk attendance to NextJS', [
                    'error' => $response['error'],
                    'count' => count($attendanceDataArray),
                    'batch_id' => $batchId
=======
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to forward bulk attendance to NextJS', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'count' => count($attendanceDataArray)
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to forward bulk attendance',
<<<<<<< HEAD
                    'error' => $response['error'],
                    'batch_id' => $batchId
                ];
            }
        } catch (Exception $e) {
            $this->recordCircuitBreakerFailure();
            
=======
                    'error' => $response->body()
                ];
            }
        } catch (Exception $e) {
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            Log::error('Exception while forwarding bulk attendance to NextJS', [
                'error' => $e->getMessage(),
                'count' => count($attendanceDataArray)
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred while forwarding bulk attendance',
                'error' => $e->getMessage()
            ];
        }
    }
<<<<<<< HEAD

    /**
     * Make HTTP request with retry mechanism
     */
    private function makeRequestWithRetry(string $method, string $endpoint, array $data = []): array
    {
        $lastException = null;
        
        for ($attempt = 1; $attempt <= $this->retryAttempts; $attempt++) {
            try {
                $startTime = microtime(true);
                
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                        'X-API-Token' => $this->apiToken,
                        'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0',
                        'X-Request-ID' => uniqid('req_'),
                        'X-Attempt' => $attempt
                    ])
                    ->send($method, $endpoint, $data);
                
                $responseTime = round((microtime(true) - $startTime) * 1000, 2);
                
                // Log performance metrics
                $this->logPerformanceMetrics($endpoint, $method, $responseTime, $attempt, $response->status());
                
                if ($response->successful()) {
                    return [
                        'success' => true,
                        'data' => $response->json(),
                        'response_time' => $responseTime,
                        'attempt' => $attempt
                    ];
                } else {
                    $lastException = new Exception("HTTP {$response->status()}: {$response->body()}");
                    
                    // Don't retry on client errors (4xx)
                    if ($response->status() >= 400 && $response->status() < 500) {
                        break;
                    }
                }
                
            } catch (Exception $e) {
                $lastException = $e;
                
                Log::warning('NextJS API request attempt failed', [
                    'attempt' => $attempt,
                    'endpoint' => $endpoint,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Wait before retry (exponential backoff)
            if ($attempt < $this->retryAttempts) {
                $delay = $this->retryDelay * pow(2, $attempt - 1); // Exponential backoff
                usleep($delay * 1000); // Convert to microseconds
            }
        }
        
        return [
            'success' => false,
            'error' => $lastException ? $lastException->getMessage() : 'Unknown error',
            'attempts' => $this->retryAttempts
        ];
    }

    /**
     * Check if circuit breaker is open
     */
    private function isCircuitBreakerOpen(): bool
    {
        $failures = Cache::get($this->circuitBreakerKey . '_failures', 0);
        $lastFailure = Cache::get($this->circuitBreakerKey . '_last_failure');
        
        if ($failures >= $this->circuitBreakerThreshold) {
            if ($lastFailure && Carbon::parse($lastFailure)->addSeconds($this->circuitBreakerTimeout)->isFuture()) {
                return true;
            } else {
                // Reset circuit breaker after timeout
                $this->resetCircuitBreaker();
            }
        }
        
        return false;
    }

    /**
     * Record circuit breaker failure
     */
    private function recordCircuitBreakerFailure(): void
    {
        $failures = Cache::get($this->circuitBreakerKey . '_failures', 0) + 1;
        
        Cache::put($this->circuitBreakerKey . '_failures', $failures, 3600);
        Cache::put($this->circuitBreakerKey . '_last_failure', now()->toISOString(), 3600);
        
        if ($failures >= $this->circuitBreakerThreshold) {
            Log::warning('NextJS Circuit breaker opened', [
                'failures' => $failures,
                'threshold' => $this->circuitBreakerThreshold
            ]);
        }
    }

    /**
     * Record circuit breaker success
     */
    private function recordCircuitBreakerSuccess(): void
    {
        $this->resetCircuitBreaker();
    }

    /**
     * Reset circuit breaker
     */
    private function resetCircuitBreaker(): void
    {
        Cache::forget($this->circuitBreakerKey . '_failures');
        Cache::forget($this->circuitBreakerKey . '_last_failure');
    }

    /**
     * Log performance metrics
     */
    private function logPerformanceMetrics(string $endpoint, string $method, float $responseTime, int $attempt, int $statusCode): void
    {
        $metricsKey = 'nextjs_api_metrics';
        
        $metrics = [
            'endpoint' => $endpoint,
            'method' => $method,
            'response_time' => $responseTime,
            'attempt' => $attempt,
            'status_code' => $statusCode,
            'timestamp' => now()->toISOString()
        ];
        
        // Store in Redis for real-time monitoring
        Redis::lpush($metricsKey, json_encode($metrics));
        Redis::ltrim($metricsKey, 0, 999); // Keep last 1000 metrics
        Redis::expire($metricsKey, 3600); // 1 hour TTL
        
        // Log slow requests
        if ($responseTime > 5000) { // 5 seconds
            Log::warning('Slow NextJS API response', $metrics);
        }
    }

    /**
     * Get API performance metrics
     */
    public function getPerformanceMetrics(int $limit = 100): array
    {
        try {
            $metricsKey = 'nextjs_api_metrics';
            $rawMetrics = Redis::lrange($metricsKey, 0, $limit - 1);
            
            $metrics = array_map(function($metric) {
                return json_decode($metric, true);
            }, $rawMetrics);
            
            // Calculate statistics
            $responseTimes = array_column($metrics, 'response_time');
            $statusCodes = array_column($metrics, 'status_code');
            
            $stats = [
                'total_requests' => count($metrics),
                'avg_response_time' => !empty($responseTimes) ? round(array_sum($responseTimes) / count($responseTimes), 2) : 0,
                'min_response_time' => !empty($responseTimes) ? min($responseTimes) : 0,
                'max_response_time' => !empty($responseTimes) ? max($responseTimes) : 0,
                'success_rate' => !empty($statusCodes) ? round((count(array_filter($statusCodes, fn($code) => $code >= 200 && $code < 300)) / count($statusCodes)) * 100, 2) : 0,
                'circuit_breaker_status' => $this->getCircuitBreakerStatus()
            ];
            
            return [
                'success' => true,
                'metrics' => $metrics,
                'statistics' => $stats
            ];
            
        } catch (Exception $e) {
            Log::error('Error getting NextJS API metrics', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to get performance metrics'
            ];
        }
    }

    /**
     * Get circuit breaker status
     */
    public function getCircuitBreakerStatus(): array
    {
        $failures = Cache::get($this->circuitBreakerKey . '_failures', 0);
        $lastFailure = Cache::get($this->circuitBreakerKey . '_last_failure');
        $isOpen = $this->isCircuitBreakerOpen();
        
        return [
            'is_open' => $isOpen,
            'failures' => $failures,
            'threshold' => $this->circuitBreakerThreshold,
            'last_failure' => $lastFailure,
            'timeout_seconds' => $this->circuitBreakerTimeout
        ];
    }

    /**
     * Batch forward multiple attendance records with connection pooling
     */
    public function batchForwardAttendance(array $attendanceGroups): array
    {
        if ($this->isCircuitBreakerOpen()) {
            return [
                'success' => false,
                'message' => 'Circuit breaker is open, skipping batch request',
                'circuit_breaker' => true
            ];
        }

        try {
            $results = [];
            $endpoint = $this->baseUrl . config('fingerprint.nextjs_integration.endpoints.attendance');
            
            // Use HTTP pool for concurrent requests
            $responses = Http::pool(function (Pool $pool) use ($attendanceGroups, $endpoint) {
                foreach ($attendanceGroups as $index => $attendanceData) {
                    $pool->timeout($this->timeout)
                        ->withHeaders([
                            'Content-Type' => 'application/json',
                            'X-API-Token' => $this->apiToken,
                            'User-Agent' => 'Laravel-Fingerprint-Bridge/1.0',
                            'X-Batch-Index' => $index
                        ])
                        ->post($endpoint, [
                            'deviceId' => $attendanceData['device_id'] ?? null,
                            'userId' => $attendanceData['device_user_id'] ?? null,
                            'timestamp' => $attendanceData['attendance_time'] ?? null,
                            'type' => $attendanceData['attendance_type'] ?? 0,
                            'verificationType' => $attendanceData['verification_type'] ?? 1,
                            'workCode' => $attendanceData['work_code'] ?? null,
                            'rawData' => $attendanceData['raw_data'] ?? null,
                            'source' => 'laravel-bridge'
                        ]);
                }
            });
            
            $successCount = 0;
            $failureCount = 0;
            
            foreach ($responses as $index => $response) {
                if ($response->successful()) {
                    $successCount++;
                    $results[$index] = [
                        'success' => true,
                        'data' => $response->json()
                    ];
                } else {
                    $failureCount++;
                    $results[$index] = [
                        'success' => false,
                        'error' => $response->body(),
                        'status' => $response->status()
                    ];
                }
            }
            
            // Update circuit breaker based on results
            if ($failureCount > $successCount) {
                $this->recordCircuitBreakerFailure();
            } else {
                $this->recordCircuitBreakerSuccess();
            }
            
            Log::info('Batch attendance forwarding completed', [
                'total' => count($attendanceGroups),
                'success' => $successCount,
                'failures' => $failureCount
            ]);
            
            return [
                'success' => true,
                'results' => $results,
                'summary' => [
                    'total' => count($attendanceGroups),
                    'success' => $successCount,
                    'failures' => $failureCount,
                    'success_rate' => round(($successCount / count($attendanceGroups)) * 100, 2)
                ]
            ];
            
        } catch (Exception $e) {
            $this->recordCircuitBreakerFailure();
            
            Log::error('Exception in batch attendance forwarding', [
                'error' => $e->getMessage(),
                'count' => count($attendanceGroups)
            ]);
            
            return [
                 'success' => false,
                 'message' => 'Batch forwarding failed: ' . $e->getMessage()
             ];
         }
     }

     /**
      * Queue device status notification for later retry
      */
     private function queueDeviceStatusNotification(string $deviceId, string $status, array $metadata = []): void
     {
         try {
             $queueKey = 'nextjs_device_status_queue';
             $notification = [
                 'device_id' => $deviceId,
                 'status' => $status,
                 'metadata' => $metadata,
                 'queued_at' => now()->toISOString(),
                 'retry_count' => 0
             ];
             
             Redis::lpush($queueKey, json_encode($notification));
             Redis::expire($queueKey, 86400); // 24 hours TTL
             
             Log::info('Device status notification queued for retry', [
                 'device_id' => $deviceId,
                 'status' => $status
             ]);
             
         } catch (Exception $e) {
             Log::error('Failed to queue device status notification', [
                 'error' => $e->getMessage(),
                 'device_id' => $deviceId
             ]);
         }
     }

     /**
      * Process queued device status notifications
      */
     public function processQueuedNotifications(int $batchSize = 10): array
     {
         try {
             $queueKey = 'nextjs_device_status_queue';
             $notifications = [];
             $processed = 0;
             $failed = 0;
             
             for ($i = 0; $i < $batchSize; $i++) {
                 $notificationJson = Redis::rpop($queueKey);
                 if (!$notificationJson) {
                     break;
                 }
                 
                 $notification = json_decode($notificationJson, true);
                 if (!$notification) {
                     continue;
                 }
                 
                 $result = $this->notifyDeviceStatus(
                     $notification['device_id'],
                     $notification['status'],
                     $notification['metadata'] ?? []
                 );
                 
                 if ($result['success']) {
                     $processed++;
                 } else {
                     $failed++;
                     
                     // Re-queue if retry count is less than max
                     $notification['retry_count'] = ($notification['retry_count'] ?? 0) + 1;
                     if ($notification['retry_count'] < 3) {
                         Redis::lpush($queueKey, json_encode($notification));
                     }
                 }
                 
                 $notifications[] = [
                     'notification' => $notification,
                     'result' => $result
                 ];
             }
             
             return [
                 'success' => true,
                 'processed' => $processed,
                 'failed' => $failed,
                 'notifications' => $notifications
             ];
             
         } catch (Exception $e) {
             Log::error('Error processing queued notifications', [
                 'error' => $e->getMessage()
             ]);
             
             return [
                 'success' => false,
                 'message' => 'Failed to process queued notifications'
             ];
         }
     }

     /**
      * Check if error is temporary and worth retrying
      */
     private function isTemporaryFailure(string $error): bool
     {
         $temporaryErrors = [
             'timeout',
             'connection refused',
             'network unreachable',
             'temporary failure',
             'service unavailable',
             '502',
             '503',
             '504'
         ];
         
         $errorLower = strtolower($error);
         
         foreach ($temporaryErrors as $tempError) {
             if (strpos($errorLower, $tempError) !== false) {
                 return true;
             }
         }
         
         return false;
     }

     /**
      * Get comprehensive service health status
      */
     public function getServiceHealth(): array
     {
         try {
             $connectionTest = $this->testConnection();
             $circuitBreakerStatus = $this->getCircuitBreakerStatus();
             $performanceMetrics = $this->getPerformanceMetrics(50);
             
             $queueKey = 'nextjs_device_status_queue';
             $queueLength = Redis::llen($queueKey);
             
             $health = [
                 'overall_status' => 'healthy',
                 'connection' => $connectionTest,
                 'circuit_breaker' => $circuitBreakerStatus,
                 'performance' => $performanceMetrics['statistics'] ?? [],
                 'queue_length' => $queueLength,
                 'last_check' => now()->toISOString()
             ];
             
             // Determine overall health
             if (!$connectionTest['success'] || $circuitBreakerStatus['is_open']) {
                 $health['overall_status'] = 'unhealthy';
             } elseif ($queueLength > 100 || ($performanceMetrics['statistics']['avg_response_time'] ?? 0) > 5000) {
                 $health['overall_status'] = 'degraded';
             }
             
             return [
                 'success' => true,
                 'health' => $health
             ];
             
         } catch (Exception $e) {
             return [
                 'success' => false,
                 'message' => 'Failed to get service health',
                 'error' => $e->getMessage()
             ];
         }
     }

     /**
      * Clear all caches related to NextJS bridge
      */
     public function clearCaches(): array
     {
         try {
             $patterns = [
                 'nextjs_sync_status_*',
                 'nextjs_api_metrics',
                 'nextjs_circuit_breaker_*'
             ];
             
             $clearedKeys = 0;
             
             foreach ($patterns as $pattern) {
                 $keys = Redis::keys($pattern);
                 if (!empty($keys)) {
                     Redis::del($keys);
                     $clearedKeys += count($keys);
                 }
             }
             
             Log::info('NextJS bridge caches cleared', [
                 'cleared_keys' => $clearedKeys
             ]);
             
             return [
                 'success' => true,
                 'message' => 'Caches cleared successfully',
                 'cleared_keys' => $clearedKeys
             ];
             
         } catch (Exception $e) {
             Log::error('Failed to clear NextJS bridge caches', [
                 'error' => $e->getMessage()
             ]);
             
             return [
                 'success' => false,
                 'message' => 'Failed to clear caches',
                 'error' => $e->getMessage()
             ];
         }
     }
 }
=======
}
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
