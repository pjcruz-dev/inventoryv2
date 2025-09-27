<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains security-related configuration options for the
    | application including password policies, rate limiting, and validation.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */
    'password_policy' => [
        'min_length' => env('PASSWORD_MIN_LENGTH', 8),
        'require_uppercase' => env('PASSWORD_REQUIRE_UPPERCASE', true),
        'require_lowercase' => env('PASSWORD_REQUIRE_LOWERCASE', true),
        'require_numbers' => env('PASSWORD_REQUIRE_NUMBERS', true),
        'require_symbols' => env('PASSWORD_REQUIRE_SYMBOLS', true),
        'max_age_days' => env('PASSWORD_MAX_AGE_DAYS', 90),
        'prevent_reuse_count' => env('PASSWORD_PREVENT_REUSE_COUNT', 5),
        'lockout_attempts' => env('PASSWORD_LOCKOUT_ATTEMPTS', 5),
        'lockout_duration_minutes' => env('PASSWORD_LOCKOUT_DURATION_MINUTES', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limiting' => [
        'api_requests' => [
            'max_attempts' => env('RATE_LIMIT_API_REQUESTS', 100),
            'decay_minutes' => env('RATE_LIMIT_API_DECAY_MINUTES', 1),
        ],
        'login_attempts' => [
            'max_attempts' => env('RATE_LIMIT_LOGIN_ATTEMPTS', 5),
            'decay_minutes' => env('RATE_LIMIT_LOGIN_DECAY_MINUTES', 15),
        ],
        'file_uploads' => [
            'max_attempts' => env('RATE_LIMIT_FILE_UPLOADS', 10),
            'decay_minutes' => env('RATE_LIMIT_FILE_DECAY_MINUTES', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Security
    |--------------------------------------------------------------------------
    */
    'file_upload' => [
        'max_size_kb' => env('FILE_UPLOAD_MAX_SIZE_KB', 10240), // 10MB
        'allowed_types' => [
            'csv' => ['text/csv', 'application/csv', 'application/vnd.ms-excel'],
            'pdf' => ['application/pdf'],
            'image' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'document' => ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        ],
        'dangerous_extensions' => [
            'php', 'phtml', 'php3', 'php4', 'php5', 'pl', 'py', 'jsp', 'asp', 'sh', 'cgi'
        ],
        'scan_for_malware' => env('FILE_UPLOAD_SCAN_MALWARE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Input Validation
    |--------------------------------------------------------------------------
    */
    'input_validation' => [
        'max_string_length' => env('INPUT_MAX_STRING_LENGTH', 255),
        'max_text_length' => env('INPUT_MAX_TEXT_LENGTH', 65535),
        'sanitize_html' => env('INPUT_SANITIZE_HTML', true),
        'detect_sql_injection' => env('INPUT_DETECT_SQL_INJECTION', true),
        'detect_xss' => env('INPUT_DETECT_XSS', true),
        'detect_path_traversal' => env('INPUT_DETECT_PATH_TRAVERSAL', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Security
    |--------------------------------------------------------------------------
    */
    'session' => [
        'lifetime_minutes' => env('SESSION_LIFETIME_MINUTES', 120),
        'regenerate_on_login' => env('SESSION_REGENERATE_ON_LOGIN', true),
        'secure_cookies' => env('SESSION_SECURE_COOKIES', true),
        'http_only_cookies' => env('SESSION_HTTP_ONLY_COOKIES', true),
        'same_site' => env('SESSION_SAME_SITE', 'strict'),
    ],

    /*
    |--------------------------------------------------------------------------
    | CSRF Protection
    |--------------------------------------------------------------------------
    */
    'csrf' => [
        'enabled' => env('CSRF_ENABLED', true),
        'token_lifetime_minutes' => env('CSRF_TOKEN_LIFETIME_MINUTES', 120),
        'regenerate_on_use' => env('CSRF_REGENERATE_ON_USE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Logging
    |--------------------------------------------------------------------------
    */
    'audit_logging' => [
        'enabled' => env('AUDIT_LOGGING_ENABLED', true),
        'log_level' => env('AUDIT_LOG_LEVEL', 'info'),
        'retention_days' => env('AUDIT_LOG_RETENTION_DAYS', 365),
        'log_sensitive_data' => env('AUDIT_LOG_SENSITIVE_DATA', false),
        'events' => [
            'auth' => ['login', 'logout', 'failed_login', 'password_change'],
            'data' => ['create', 'update', 'delete', 'view'],
            'file' => ['upload', 'download', 'delete'],
            'system' => ['configuration_change', 'user_creation', 'permission_change'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    */
    'security_headers' => [
        'x_frame_options' => env('SECURITY_HEADER_X_FRAME_OPTIONS', 'DENY'),
        'x_content_type_options' => env('SECURITY_HEADER_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
        'x_xss_protection' => env('SECURITY_HEADER_X_XSS_PROTECTION', '1; mode=block'),
        'referrer_policy' => env('SECURITY_HEADER_REFERRER_POLICY', 'strict-origin-when-cross-origin'),
        'content_security_policy' => env('SECURITY_HEADER_CSP', "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';"),
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist/Blacklist
    |--------------------------------------------------------------------------
    */
    'ip_filtering' => [
        'enabled' => env('IP_FILTERING_ENABLED', false),
        'whitelist' => array_filter(explode(',', env('IP_WHITELIST', ''))),
        'blacklist' => array_filter(explode(',', env('IP_BLACKLIST', ''))),
        'block_suspicious_ips' => env('IP_BLOCK_SUSPICIOUS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Two-Factor Authentication
    |--------------------------------------------------------------------------
    */
    'two_factor' => [
        'enabled' => env('TWO_FACTOR_ENABLED', false),
        'required_for_admin' => env('TWO_FACTOR_REQUIRED_ADMIN', true),
        'required_for_all' => env('TWO_FACTOR_REQUIRED_ALL', false),
        'issuer' => env('TWO_FACTOR_ISSUER', 'Inventory Management System'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Encryption
    |--------------------------------------------------------------------------
    */
    'encryption' => [
        'encrypt_sensitive_data' => env('ENCRYPT_SENSITIVE_DATA', true),
        'sensitive_fields' => [
            'email', 'phone', 'address', 'notes', 'serial_number'
        ],
    ],
];
