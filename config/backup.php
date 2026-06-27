<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Path
    |--------------------------------------------------------------------------
    */

    'backup_path' => storage_path('app/backups'),

    /*
    |--------------------------------------------------------------------------
    | Database Backup
    |--------------------------------------------------------------------------
    */

    'database' => [
        'connections' => [
            'mysql',
        ],

        'exclude' => [
            // Add tables to exclude here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Files Backup
    |--------------------------------------------------------------------------
    */

    'files' => [
        'include' => [
            base_path('app'),
            base_path('config'),
            base_path('database'),
            base_path('resources'),
            base_path('routes'),
            base_path('.env'),
        ],

        'exclude' => [
            base_path('vendor'),
            base_path('node_modules'),
            base_path('storage'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Temporary Directory
    |--------------------------------------------------------------------------
    */

    'temporary_directory' => storage_path('app/backup-temp'),

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        'notification_channel' => 'mail',
        'mail' => [
            'to' => env('BACKUP_EMAIL', 'admin@example.com'),
        ],
    ],

];
