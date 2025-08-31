<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
<<<<<<< HEAD
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
use App\Models\FingerprintAttendance;
use App\Models\Employee;
use App\Models\AttendanceRule;
use Carbon\Carbon;
<<<<<<< HEAD
use Illuminate\Support\Collection;
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

class AttendanceService
{
    /**
     * Process attendance data from fingerprint device
     */
    public function processAttendance(array $attendanceData): array
    {
        try {
            DB::beginTransaction();

            // Validate and normalize data
            $normalizedData = $this->normalizeAttendanceData($attendanceData);
            
            // Check for duplicates
            if ($this->isDuplicateAttendance($normalizedData)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'Duplicate attendance record detected'
                ];
            }

            // Find employee
            $employee = $this->findEmployee($normalizedData['user_id']);
            if (!$employee) {
                Log::warning('Employee not found for attendance', [
                    'user_id' => $normalizedData['user_id'],
                    'device_id' => $normalizedData['device_id']
                ]);
            }

            // Create attendance record
            $attendance = FingerprintAttendance::create([
                'device_id' => $normalizedData['device_id'],
                'device_user_id' => $normalizedData['user_id'],
                'employee_id' => $employee ? $employee->id : null,
                'attendance_time' => $normalizedData['timestamp'],
                'attendance_type' => $normalizedData['in_out_mode'],
                'verification_type' => $this->getVerificationType($normalizedData['verify_type']),
                'work_code' => $normalizedData['work_code'] ?? 1,
                'raw_data' => json_encode($attendanceData),
                'processed_at' => now()
            ]);

            // Process attendance rules if employee found
            if ($employee) {
                $this->processAttendanceRules($attendance, $employee);
            }

            // Log successful processing
            Log::info('Attendance processed successfully', [
                'attendance_id' => $attendance->id,
                'employee_id' => $employee ? $employee->id : null,
                'device_id' => $normalizedData['device_id'],
                'timestamp' => $normalizedData['timestamp']
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Attendance processed successfully',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'employee_id' => $employee ? $employee->id : null,
                    'employee_name' => $employee ? $employee->name : null,
                    'timestamp' => $attendance->attendance_time,
                    'type' => $attendance->attendance_type == 0 ? 'check_in' : 'check_out',
                    'verification' => $attendance->verification_type
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error processing attendance', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $attendanceData
            ]);

            return [
                'success' => false,
                'message' => 'Failed to process attendance: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Normalize attendance data
     */
    private function normalizeAttendanceData(array $data): array
    {
        return [
            'device_id' => $data['device_id'],
            'user_id' => $data['user_id'],
            'timestamp' => Carbon::parse($data['timestamp']),
            'verify_type' => (int) $data['verify_type'],
            'in_out_mode' => (int) $data['in_out_mode'],
            'work_code' => isset($data['work_code']) ? (int) $data['work_code'] : 1
        ];
    }

    /**
<<<<<<< HEAD
     * Check for duplicate attendance with caching
     */
    private function isDuplicateAttendance(array $data): bool
    {
        $timeWindow = config('fingerprint.sync.duplicate_check_window', 5); // minutes
        $cacheKey = "attendance_check_{$data['device_id']}_{$data['user_id']}_{$data['in_out_mode']}";
        
        // Check cache first for recent duplicates
        $recentAttendance = Cache::get($cacheKey);
        if ($recentAttendance) {
            $lastTime = Carbon::parse($recentAttendance);
            if ($data['timestamp']->diffInMinutes($lastTime) < $timeWindow) {
                return true;
            }
        }
        
        $startTime = $data['timestamp']->copy()->subMinutes($timeWindow);
        $endTime = $data['timestamp']->copy()->addMinutes($timeWindow);

        $exists = FingerprintAttendance::where('device_id', $data['device_id'])
=======
     * Check for duplicate attendance
     */
    private function isDuplicateAttendance(array $data): bool
    {
        $timeWindow = 5; // 5 minutes window
        $startTime = $data['timestamp']->copy()->subMinutes($timeWindow);
        $endTime = $data['timestamp']->copy()->addMinutes($timeWindow);

        return FingerprintAttendance::where('device_id', $data['device_id'])
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
            ->where('device_user_id', $data['user_id'])
            ->where('attendance_type', $data['in_out_mode'])
            ->whereBetween('attendance_time', [$startTime, $endTime])
            ->exists();
<<<<<<< HEAD
            
        // Cache the timestamp for future duplicate checks
        if (!$exists) {
            Cache::put($cacheKey, $data['timestamp']->toISOString(), $timeWindow * 60);
        }
        
        return $exists;
    }

    /**
     * Find employee by device user ID with caching
     */
    private function findEmployee(string $deviceUserId): ?Employee
    {
        $cacheKey = "employee_device_user_{$deviceUserId}";
        
        return Cache::remember($cacheKey, config('fingerprint.cache.ttl.employee_lookup', 3600), function () use ($deviceUserId) {
            // Try to find by device_user_id first
            $employee = Employee::where('device_user_id', $deviceUserId)->first();
            
            if (!$employee) {
                // Try to find by employee_id if device_user_id matches
                $employee = Employee::where('id', $deviceUserId)->first();
            }

            return $employee;
        });
=======
    }

    /**
     * Find employee by device user ID
     */
    private function findEmployee(string $deviceUserId): ?Employee
    {
        // Try to find by device_user_id first
        $employee = Employee::where('device_user_id', $deviceUserId)->first();
        
        if (!$employee) {
            // Try to find by employee_id if device_user_id matches
            $employee = Employee::where('id', $deviceUserId)->first();
        }

        return $employee;
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
    }

    /**
     * Get verification type string
     */
    private function getVerificationType(int $verifyType): string
    {
        $types = [
            1 => 'fingerprint',
            15 => 'face',
            2 => 'password',
            3 => 'card',
            4 => 'combination'
        ];

        return $types[$verifyType] ?? 'unknown';
    }

    /**
     * Process attendance rules
     */
    private function processAttendanceRules(FingerprintAttendance $attendance, Employee $employee): void
    {
        try {
            // Get active attendance rules
            $rules = AttendanceRule::where('is_active', true)->get();
            
            foreach ($rules as $rule) {
                $this->applyAttendanceRule($attendance, $employee, $rule);
            }

        } catch (\Exception $e) {
            Log::error('Error processing attendance rules', [
                'attendance_id' => $attendance->id,
                'employee_id' => $employee->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Apply specific attendance rule
     */
    private function applyAttendanceRule(FingerprintAttendance $attendance, Employee $employee, AttendanceRule $rule): void
    {
        // This is where you would implement specific business rules
        // For example:
        // - Late arrival detection
        // - Early departure detection
        // - Overtime calculation
        // - Break time validation
        
        Log::info('Applying attendance rule', [
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'attendance_id' => $attendance->id,
            'employee_id' => $employee->id
        ]);
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats(array $filters = []): array
    {
        try {
            $query = FingerprintAttendance::query();

            // Apply filters
            if (isset($filters['date_from'])) {
                $query->where('attendance_time', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('attendance_time', '<=', $filters['date_to']);
            }

            if (isset($filters['device_id'])) {
                $query->where('device_id', $filters['device_id']);
            }

            if (isset($filters['employee_id'])) {
                $query->where('employee_id', $filters['employee_id']);
            }

            $stats = [
                'total_records' => $query->count(),
                'check_ins' => $query->clone()->where('attendance_type', 0)->count(),
                'check_outs' => $query->clone()->where('attendance_type', 1)->count(),
                'today_records' => $query->clone()->whereDate('attendance_time', today())->count(),
                'this_week_records' => $query->clone()->whereBetween('attendance_time', [
                    now()->startOfWeek(),
                    now()->endOfWeek()
                ])->count(),
                'verification_types' => $query->clone()
                    ->select('verification_type', DB::raw('count(*) as count'))
                    ->groupBy('verification_type')
                    ->pluck('count', 'verification_type')
                    ->toArray()
            ];

            return [
                'success' => true,
                'data' => $stats
            ];

        } catch (\Exception $e) {
            Log::error('Error getting attendance stats', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get attendance statistics'
            ];
        }
    }

    /**
<<<<<<< HEAD
     * Get recent attendance records with caching
=======
     * Get recent attendance records
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
     */
    public function getRecentAttendance(int $limit = 50): array
    {
        try {
<<<<<<< HEAD
            $cacheKey = "recent_attendance_{$limit}";
            
            $records = Cache::remember($cacheKey, config('fingerprint.cache.ttl.recent_attendance', 60), function () use ($limit) {
                return FingerprintAttendance::with('employee')
                    ->select(['id', 'device_id', 'device_user_id', 'employee_id', 'attendance_time', 'attendance_type', 'verification_type', 'processed_at'])
                    ->orderBy('attendance_time', 'desc')
                    ->limit($limit)
                    ->get()
                    ->map(function ($record) {
                        return [
                            'id' => $record->id,
                            'device_id' => $record->device_id,
                            'device_user_id' => $record->device_user_id,
                            'employee_name' => $record->employee ? $record->employee->name : 'Unknown',
                            'attendance_time' => $record->attendance_time,
                            'attendance_type' => $record->attendance_type == 0 ? 'check_in' : 'check_out',
                            'verification_type' => $record->verification_type,
                            'processed_at' => $record->processed_at
                        ];
                    });
            });
=======
            $records = FingerprintAttendance::with('employee')
                ->orderBy('attendance_time', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($record) {
                    return [
                        'id' => $record->id,
                        'device_id' => $record->device_id,
                        'device_user_id' => $record->device_user_id,
                        'employee_name' => $record->employee ? $record->employee->name : 'Unknown',
                        'attendance_time' => $record->attendance_time,
                        'attendance_type' => $record->attendance_type == 0 ? 'check_in' : 'check_out',
                        'verification_type' => $record->verification_type,
                        'processed_at' => $record->processed_at
                    ];
                });
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3

            return [
                'success' => true,
                'data' => $records
            ];

        } catch (\Exception $e) {
            Log::error('Error getting recent attendance', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to get recent attendance'
            ];
        }
    }
<<<<<<< HEAD

    /**
     * Bulk process attendance records for better performance
     */
    public function bulkProcessAttendance(array $attendanceRecords): array
    {
        try {
            $results = [
                'processed' => 0,
                'failed' => 0,
                'duplicates' => 0,
                'errors' => []
            ];
            
            $chunkSize = config('fingerprint.database.bulk_insert_chunk_size', 100);
            $chunks = array_chunk($attendanceRecords, $chunkSize);
            
            foreach ($chunks as $chunk) {
                DB::beginTransaction();
                
                try {
                    $validRecords = [];
                    
                    foreach ($chunk as $record) {
                        $normalizedData = $this->normalizeAttendanceData($record);
                        
                        if ($this->isDuplicateAttendance($normalizedData)) {
                            $results['duplicates']++;
                            continue;
                        }
                        
                        $employee = $this->findEmployee($normalizedData['user_id']);
                        
                        $validRecords[] = [
                            'device_id' => $normalizedData['device_id'],
                            'device_user_id' => $normalizedData['user_id'],
                            'employee_id' => $employee ? $employee->id : null,
                            'attendance_time' => $normalizedData['timestamp'],
                            'attendance_type' => $normalizedData['in_out_mode'],
                            'verification_type' => $this->getVerificationType($normalizedData['verify_type']),
                            'work_code' => $normalizedData['work_code'] ?? 1,
                            'raw_data' => json_encode($record),
                            'processed_at' => now(),
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                    
                    if (!empty($validRecords)) {
                        FingerprintAttendance::insert($validRecords);
                        $results['processed'] += count($validRecords);
                    }
                    
                    DB::commit();
                    
                } catch (\Exception $e) {
                    DB::rollBack();
                    $results['failed'] += count($chunk);
                    $results['errors'][] = $e->getMessage();
                    
                    Log::error('Error in bulk attendance processing chunk', [
                        'error' => $e->getMessage(),
                        'chunk_size' => count($chunk)
                    ]);
                }
            }
            
            // Clear related caches after bulk processing
            $this->clearAttendanceCaches();
            
            Log::info('Bulk attendance processing completed', $results);
            
            return [
                'success' => true,
                'results' => $results
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in bulk attendance processing', [
                'error' => $e->getMessage(),
                'total_records' => count($attendanceRecords)
            ]);
            
            return [
                'success' => false,
                'message' => 'Bulk processing failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get attendance metrics for monitoring
     */
    public function getAttendanceMetrics(array $options = []): array
    {
        try {
            $timeframe = $options['timeframe'] ?? '24h';
            $cacheKey = "attendance_metrics_{$timeframe}";
            
            $metrics = Cache::remember($cacheKey, config('fingerprint.cache.ttl.metrics', 300), function () use ($timeframe) {
                $startTime = match($timeframe) {
                    '1h' => now()->subHour(),
                    '24h' => now()->subDay(),
                    '7d' => now()->subWeek(),
                    '30d' => now()->subMonth(),
                    default => now()->subDay()
                };
                
                $query = FingerprintAttendance::where('created_at', '>=', $startTime);
                
                return [
                    'total_records' => $query->count(),
                    'check_ins' => $query->clone()->where('attendance_type', 0)->count(),
                    'check_outs' => $query->clone()->where('attendance_type', 1)->count(),
                    'unique_employees' => $query->clone()->whereNotNull('employee_id')->distinct('employee_id')->count(),
                    'unique_devices' => $query->clone()->distinct('device_id')->count(),
                    'processing_rate' => $this->calculateProcessingRate($startTime),
                    'verification_breakdown' => $query->clone()
                        ->select('verification_type', DB::raw('count(*) as count'))
                        ->groupBy('verification_type')
                        ->pluck('count', 'verification_type')
                        ->toArray(),
                    'hourly_distribution' => $this->getHourlyDistribution($startTime)
                ];
            });
            
            return [
                'success' => true,
                'metrics' => $metrics,
                'timeframe' => $timeframe,
                'generated_at' => now()->toISOString()
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting attendance metrics', [
                'error' => $e->getMessage(),
                'options' => $options
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to get attendance metrics'
            ];
        }
    }

    /**
     * Calculate processing rate (records per minute)
     */
    private function calculateProcessingRate(Carbon $startTime): float
    {
        $totalRecords = FingerprintAttendance::where('created_at', '>=', $startTime)->count();
        $minutes = $startTime->diffInMinutes(now());
        
        return $minutes > 0 ? round($totalRecords / $minutes, 2) : 0;
    }

    /**
     * Get hourly distribution of attendance records
     */
    private function getHourlyDistribution(Carbon $startTime): array
    {
        return FingerprintAttendance::where('created_at', '>=', $startTime)
            ->select(DB::raw('HOUR(attendance_time) as hour'), DB::raw('count(*) as count'))
            ->groupBy(DB::raw('HOUR(attendance_time)'))
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }

    /**
     * Clear attendance-related caches
     */
    private function clearAttendanceCaches(): void
    {
        $patterns = [
            'recent_attendance_*',
            'attendance_metrics_*',
            'attendance_stats_*'
        ];
        
        foreach ($patterns as $pattern) {
            $keys = Cache::getRedis()->keys($pattern);
            if (!empty($keys)) {
                Cache::getRedis()->del($keys);
            }
        }
    }

    /**
     * Get real-time attendance feed using Redis
     */
    public function getRealtimeAttendanceFeed(int $limit = 20): array
    {
        try {
            $feedKey = 'realtime_attendance_feed';
            
            // Get recent records from Redis list
            $records = Redis::lrange($feedKey, 0, $limit - 1);
            
            $decodedRecords = array_map(function($record) {
                return json_decode($record, true);
            }, $records);
            
            return [
                'success' => true,
                'data' => $decodedRecords,
                'count' => count($decodedRecords)
            ];
            
        } catch (\Exception $e) {
            Log::error('Error getting realtime attendance feed', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to get realtime feed'
            ];
        }
    }

    /**
     * Add attendance to real-time feed
     */
    public function addToRealtimeFeed(array $attendanceData): void
    {
        try {
            $feedKey = 'realtime_attendance_feed';
            $maxFeedSize = config('fingerprint.realtime.feed_max_size', 1000);
            
            // Add to the beginning of the list
            Redis::lpush($feedKey, json_encode($attendanceData));
            
            // Trim the list to maintain max size
            Redis::ltrim($feedKey, 0, $maxFeedSize - 1);
            
            // Set expiration for the feed
            Redis::expire($feedKey, config('fingerprint.realtime.feed_ttl', 3600));
            
        } catch (\Exception $e) {
            Log::error('Error adding to realtime feed', [
                'error' => $e->getMessage(),
                'data' => $attendanceData
            ]);
        }
    }
=======
>>>>>>> 0e32eed2a081eaba4f9a9655e9cacaab8f8c41b3
}