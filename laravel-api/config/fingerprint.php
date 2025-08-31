<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fingerprint Device Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your fingerprint device settings for AFMS.
    | This includes device connection, authentication, and processing settings.
    |
    */

    'device' => [
        'type' => env('FINGERPRINT_DEVICE_TYPE', 'solution-type-100c'),
        'ip' => env('FINGERPRINT_DEVICE_IP', '192.168.1.100'),
        'port' => env('FINGERPRINT_DEVICE_PORT', 4370),
        'username' => env('FINGERPRINT_DEVICE_USERNAME', 'admin'),
        'password' => env('FINGERPRINT_DEVICE_PASSWORD', '123456'),
        'timeout' => env('FINGERPRINT_DEVICE_TIMEOUT', 30),
        'retry_attempts' => env('FINGERPRINT_DEVICE_RETRY_ATTEMPTS', 3),
        'connection_pool_size' => env('DEVICE_CONNECTION_POOL_SIZE', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Processing Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how fingerprint data is processed and synchronized.
    |
    */

    'processing' => [
        'enabled' => env('FINGERPRINT_PROCESSING_ENABLED', true),
        'batch_size' => env('FINGERPRINT_BATCH_SIZE', 100),
        'timeout' => env('FINGERPRINT_TIMEOUT', 30),
        'retry_attempts' => env('FINGERPRINT_RETRY_ATTEMPTS', 3),
        'max_concurrent_jobs' => env('FINGERPRINT_MAX_CONCURRENT_JOBS', 10),
        'job_timeout' => env('FINGERPRINT_JOB_TIMEOUT', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for real-time notifications.
    |
    */

    'webhook' => [
        'enabled' => env('WEBHOOK_ENABLED', true),
        'url' => env('WEBHOOK_URL', ''),
        'secret' => env('WEBHOOK_SECRET', ''),
    ],

    'sync' => [
        'auto_sync_enabled' => env('AUTO_SYNC_ENABLED', true),
        'sync_interval_minutes' => env('SYNC_INTERVAL_MINUTES', 5),
        'batch_size' => env('SYNC_BATCH_SIZE', 100),
        'max_concurrent_syncs' => env('MAX_CONCURRENT_SYNCS', 5),
        'retry_attempts' => env('SYNC_RETRY_ATTEMPTS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Real-time Processing Configuration
    |--------------------------------------------------------------------------
    */

    'realtime' => [
        'enabled' => env('FINGERPRINT_REALTIME_ENABLED', true),
        'queue_connection' => env('FINGERPRINT_QUEUE_CONNECTION', 'redis'),
        'queue_name' => env('FINGERPRINT_QUEUE_NAME', 'fingerprint-processing'),
        'max_concurrent_jobs' => env('FINGERPRINT_MAX_CONCURRENT_JOBS', 10),
        'job_timeout' => env('FINGERPRINT_JOB_TIMEOUT', 300),
        'failed_job_retry_after' => env('FINGERPRINT_FAILED_JOB_RETRY_AFTER', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    */

    'database' => [
        'connection_pool_size' => env('DB_POOL_SIZE', 20),
        'query_timeout' => env('DB_QUERY_TIMEOUT', 30),
        'bulk_insert_chunk_size' => env('DB_BULK_INSERT_CHUNK_SIZE', 1000),
        'enable_query_log' => env('DB_ENABLE_QUERY_LOG', false),
        'use_read_write_connections' => env('DB_USE_READ_WRITE_CONNECTIONS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('FINGERPRINT_CACHE_ENABLED', true),
        'store' => env('FINGERPRINT_CACHE_STORE', 'redis'),
        'device_status_ttl' => env('CACHE_DEVICE_STATUS_TTL', 300),
        'attendance_summary_ttl' => env('CACHE_ATTENDANCE_SUMMARY_TTL', 3600),
        'employee_data_ttl' => env('CACHE_EMPLOYEE_DATA_TTL', 1800),
        'prefix' => env('CACHE_PREFIX', 'afms_fingerprint'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring and Health Checks
    |--------------------------------------------------------------------------
    */

    'monitoring' => [
        'enabled' => env('FINGERPRINT_MONITORING_ENABLED', true),
        'health_check_interval' => env('FINGERPRINT_HEALTH_CHECK_INTERVAL', 300),
        'metrics_enabled' => env('FINGERPRINT_METRICS_ENABLED', true),
        'alert_thresholds' => [
            'device_offline_minutes' => env('ALERT_DEVICE_OFFLINE_MINUTES', 10),
            'queue_size_warning' => env('ALERT_QUEUE_SIZE_WARNING', 1000),
            'processing_delay_seconds' => env('ALERT_PROCESSING_DELAY_SECONDS', 60),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    */

    'security' => [
        'rate_limit_per_minute' => env('FINGERPRINT_RATE_LIMIT', 1000),
        'allowed_ips' => array_filter(explode(',', env('FINGERPRINT_ALLOWED_IPS', ''))),
        'require_device_auth' => env('FINGERPRINT_REQUIRE_DEVICE_AUTH', true),
        'encrypt_device_data' => env('FINGERPRINT_ENCRYPT_DEVICE_DATA', true),
        'api_key_rotation_days' => env('API_KEY_ROTATION_DAYS', 30),
    ],

    'logging' => [
        'enabled' => env('FINGERPRINT_LOGGING_ENABLED', true),
        'level' => env('FINGERPRINT_LOG_LEVEL', 'info'),
        'channel' => env('FINGERPRINT_LOG_CHANNEL', 'daily'),
        'separate_error_log' => env('FINGERPRINT_SEPARATE_ERROR_LOG', true),
        'log_sql_queries' => env('FINGERPRINT_LOG_SQL_QUERIES', false),
    ],
];