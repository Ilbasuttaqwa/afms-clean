<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
<<<<<<< HEAD
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\FingerprintDevice;
use App\Models\FingerprintAttendance;
use Carbon\Carbon;
=======
use App\Models\FingerprintDevice;
use App\Models\FingerprintAttendance;
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

class FingerprintDeviceService
{
    protected $deviceConfig;

    public function __construct()
    {
        $this->deviceConfig = [
            'ip' => config('fingerprint.device_ip'),
            'port' => config('fingerprint.device_port'),
            'username' => config('fingerprint.device_username'),
            'password' => config('fingerprint.device_password'),
            'timeout' => config('fingerprint.device_timeout', 30)
        ];
    }

    /**
     * Get device status
     */
    public function getDeviceStatus(string $deviceId): array
    {
        try {
            $device = FingerprintDevice::where('device_id', $deviceId)->first();
            
            if (!$device) {
                return [
                    'success' => false,
                    'message' => 'Device not found',
                    'status' => 'unknown'
                ];
            }

            // Test connection to device
            $connectionTest = $this->testDeviceConnection($device->ip_address, $device->port);
            
            // Update device status
            $device->update([
                'status' => $connectionTest['success'] ? 'online' : 'offline',
                'last_ping' => now(),
                'updated_at' => now()
            ]);

            return [
                'success' => true,
                'device_id' => $device->device_id,
                'device_name' => $device->device_name,
                'ip_address' => $device->ip_address,
                'port' => $device->port,
                'status' => $device->status,
                'last_sync' => $device->last_sync,
                'last_ping' => $device->last_ping,
                'total_users' => $device->total_users,
                'total_records' => $device->total_records,
                'response_time' => $connectionTest['response_time'] ?? null
            ];

        } catch (\Exception $e) {
            Log::error('Error getting device status', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get device status',
                'status' => 'error'
            ];
        }
    }

    /**
     * Test device connection
     */
    public function testConnection(string $deviceId): array
    {
        try {
            $device = FingerprintDevice::where('device_id', $deviceId)->first();
            
            if (!$device) {
                return [
                    'success' => false,
                    'message' => 'Device not found'
                ];
            }

            $startTime = microtime(true);
            $result = $this->testDeviceConnection($device->ip_address, $device->port);
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return [
                'success' => $result['success'],
                'message' => $result['message'],
                'response_time' => $responseTime . 'ms'
            ];

        } catch (\Exception $e) {
            Log::error('Error testing device connection', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Connection test failed'
            ];
        }
    }

    /**
     * Sync device data
     */
    public function syncDevice(string $deviceId): array
    {
        try {
            $device = FingerprintDevice::where('device_id', $deviceId)->first();
            
            if (!$device) {
                return [
                    'success' => false,
                    'message' => 'Device not found'
                ];
            }

            // Test connection first
            $connectionTest = $this->testDeviceConnection($device->ip_address, $device->port);
            
            if (!$connectionTest['success']) {
                return [
                    'success' => false,
                    'message' => 'Device is offline or unreachable'
                ];
            }

            // Simulate sync process (in real implementation, use SOAP/TCP communication)
            $syncResult = $this->performDeviceSync($device);
            
            if ($syncResult['success']) {
                $device->update([
                    'last_sync' => now(),
                    'total_records' => $syncResult['total_records'],
                    'status' => 'online',
                    'updated_at' => now()
                ]);
            }

            return $syncResult;

        } catch (\Exception $e) {
            Log::error('Error syncing device', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Sync failed'
            ];
        }
    }

    /**
     * Test device connection via TCP/HTTP
     */
    private function testDeviceConnection(string $ip, int $port): array
    {
        try {
            // Try HTTP connection first
            $response = Http::timeout($this->deviceConfig['timeout'])
                ->get("http://{$ip}:{$port}/cgi-bin/AttendanceInquiry.cgi");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Device is online and responding'
                ];
            }

            // Try TCP socket connection as fallback
            $socket = @fsockopen($ip, $port, $errno, $errstr, $this->deviceConfig['timeout']);
            
            if ($socket) {
                fclose($socket);
                return [
                    'success' => true,
                    'message' => 'Device is reachable via TCP'
                ];
            }

            return [
                'success' => false,
                'message' => "Connection failed: {$errstr} ({$errno})"
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Perform actual device sync
     */
    private function performDeviceSync(FingerprintDevice $device): array
    {
        try {
            // In real implementation, this would:
            // 1. Connect to device via SOAP/TCP
            // 2. Retrieve attendance records
            // 3. Parse and store data
            // 4. Return sync results

            // Simulate sync process
            $newRecords = rand(5, 25);
            $totalRecords = $device->total_records + $newRecords;

            // Simulate creating attendance records
            for ($i = 0; $i < $newRecords; $i++) {
                FingerprintAttendance::create([
                    'device_id' => $device->id,
                    'device_user_id' => 'USER_' . rand(1, 100),
                    'attendance_time' => now()->subMinutes(rand(1, 1440)),
                    'attendance_type' => rand(0, 1), // 0: in, 1: out
                    'verification_type' => 'fingerprint',
                    'raw_data' => json_encode([
                        'device_id' => $device->device_id,
                        'timestamp' => now()->toISOString(),
                        'sync_batch' => uniqid()
                    ])
                ]);
            }

            Log::info('Device sync completed', [
                'device_id' => $device->device_id,
                'new_records' => $newRecords,
                'total_records' => $totalRecords
            ]);

            return [
                'success' => true,
                'message' => 'Sync completed successfully',
                'data' => [
                    'new_records' => $newRecords,
                    'total_records' => $totalRecords,
                    'sync_time' => now()->toISOString()
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Device sync failed', [
                'device_id' => $device->device_id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Get all devices with caching
=======
     * Get all devices
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
     */
    public function getAllDevices(): array
    {
        try {
<<<<<<< HEAD
            $cacheKey = 'fingerprint_devices_all';
            $devices = Cache::remember($cacheKey, config('fingerprint.cache.ttl.devices', 300), function () {
                return FingerprintDevice::select([
                    'id', 'device_id', 'device_name', 'ip_address', 'port', 
                    'status', 'last_sync', 'last_ping', 'total_users', 
                    'total_records', 'created_at', 'updated_at'
                ])->get();
            });
=======
            $devices = FingerprintDevice::all();
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            
            return [
                'success' => true,
                'data' => $devices->map(function ($device) {
                    return [
                        'id' => $device->id,
                        'device_id' => $device->device_id,
                        'device_name' => $device->device_name,
                        'ip_address' => $device->ip_address,
                        'port' => $device->port,
                        'status' => $device->status,
                        'last_sync' => $device->last_sync,
                        'last_ping' => $device->last_ping,
                        'total_users' => $device->total_users,
                        'total_records' => $device->total_records,
                        'created_at' => $device->created_at,
                        'updated_at' => $device->updated_at
                    ];
                })
            ];

        } catch (\Exception $e) {
            Log::error('Error getting all devices', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get devices'
            ];
        }
    }
<<<<<<< HEAD

    /**
     * Check if device exists
     */
    public function deviceExists(string $deviceId): bool
    {
        $cacheKey = "device_exists_{$deviceId}";
        
        return Cache::remember($cacheKey, config('fingerprint.cache.ttl.device_exists', 600), function () use ($deviceId) {
            return FingerprintDevice::where('device_id', $deviceId)->exists();
        });
    }

    /**
     * Get device by ID with caching
     */
    public function getDevice(string $deviceId): ?FingerprintDevice
    {
        $cacheKey = "device_{$deviceId}";
        
        return Cache::remember($cacheKey, config('fingerprint.cache.ttl.device_info', 300), function () use ($deviceId) {
            return FingerprintDevice::where('device_id', $deviceId)->first();
        });
    }

    /**
     * Update device activity with optimized caching
     */
    public function updateDeviceActivity(string $deviceId, array $data = []): bool
    {
        try {
            $device = $this->getDevice($deviceId);
            
            if (!$device) {
                return false;
            }

            $updateData = array_merge([
                'last_ping' => now(),
                'updated_at' => now()
            ], $data);

            $updated = $device->update($updateData);

            if ($updated) {
                // Clear related caches
                $this->clearDeviceCache($deviceId);
            }

            return $updated;

        } catch (\Exception $e) {
            Log::error('Error updating device activity', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }

    /**
     * Bulk sync devices for better performance
     */
    public function bulkSyncDevices(array $deviceIds, array $options = []): array
    {
        try {
            $results = [];
            $chunkSize = $options['chunk_size'] ?? config('fingerprint.database.bulk_insert_chunk_size', 100);
            $maxConcurrent = $options['max_concurrent'] ?? config('fingerprint.sync.max_concurrent_syncs', 5);
            
            $deviceChunks = array_chunk($deviceIds, $chunkSize);
            
            foreach ($deviceChunks as $chunk) {
                $devices = FingerprintDevice::whereIn('device_id', $chunk)
                    ->where('is_active', true)
                    ->get();
                
                foreach ($devices as $device) {
                    $syncResult = $this->performDeviceSync($device);
                    $results[$device->device_id] = $syncResult;
                    
                    // Clear device cache after sync
                    $this->clearDeviceCache($device->device_id);
                }
            }
            
            return [
                'success' => true,
                'results' => $results,
                'total_processed' => count($results)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in bulk sync devices', [
                'device_ids' => $deviceIds,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Bulk sync failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get device health metrics
     */
    public function getDeviceHealthMetrics(string $deviceId): array
    {
        try {
            $device = $this->getDevice($deviceId);
            
            if (!$device) {
                return [
                    'success' => false,
                    'message' => 'Device not found'
                ];
            }

            $lastHour = Carbon::now()->subHour();
            $last24Hours = Carbon::now()->subDay();
            
            $metrics = [
                'device_id' => $deviceId,
                'status' => $device->status,
                'uptime_percentage' => $this->calculateUptimePercentage($device),
                'last_sync' => $device->last_sync,
                'last_ping' => $device->last_ping,
                'attendance_count_last_hour' => FingerprintAttendance::where('device_id', $device->id)
                    ->where('created_at', '>=', $lastHour)
                    ->count(),
                'attendance_count_last_24h' => FingerprintAttendance::where('device_id', $device->id)
                    ->where('created_at', '>=', $last24Hours)
                    ->count(),
                'total_records' => $device->total_records,
                'sync_frequency' => $this->calculateSyncFrequency($device)
            ];
            
            return [
                'success' => true,
                'metrics' => $metrics
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting device health metrics', [
                'device_id' => $deviceId,
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to get device metrics'
            ];
        }
    }

    /**
     * Clear device-related caches
     */
    private function clearDeviceCache(string $deviceId): void
    {
        $cacheKeys = [
            "device_{$deviceId}",
            "device_exists_{$deviceId}",
            'fingerprint_devices_all'
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Calculate device uptime percentage
     */
    private function calculateUptimePercentage(FingerprintDevice $device): float
    {
        // Simple calculation based on last ping time
        if (!$device->last_ping) {
            return 0.0;
        }
        
        $hoursSinceLastPing = Carbon::parse($device->last_ping)->diffInHours(now());
        $maxHours = 24; // Consider last 24 hours
        
        if ($hoursSinceLastPing >= $maxHours) {
            return 0.0;
        }
        
        return round(((($maxHours - $hoursSinceLastPing) / $maxHours) * 100), 2);
    }

    /**
     * Calculate sync frequency
     */
    private function calculateSyncFrequency(FingerprintDevice $device): string
    {
        if (!$device->last_sync) {
            return 'never';
        }
        
        $hoursSinceLastSync = Carbon::parse($device->last_sync)->diffInHours(now());
        
        if ($hoursSinceLastSync < 1) {
            return 'frequent';
        } elseif ($hoursSinceLastSync < 6) {
            return 'regular';
        } elseif ($hoursSinceLastSync < 24) {
            return 'infrequent';
        } else {
            return 'rare';
        }
    }
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
}