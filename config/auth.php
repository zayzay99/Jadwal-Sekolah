<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Guard untuk siswa
        'siswa' => [
            'driver' => 'session',
            'provider' => 'siswas',
        ],

        // Guard untuk guru
        'guru' => [
            'driver' => 'session',
            'provider' => 'gurus',
        ],

        // Guard untuk admin 
        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
    ],


    ],

    'providers' => [
        // Default user (optional, bisa tidak dipakai tapi harus ada)
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // Provider untuk siswa
        'siswas' => [
            'driver' => 'eloquent',
            'model' => App\Models\Siswa::class,
        ],

        // Provider untuk guru
        'gurus' => [
            'driver' => 'eloquent',
            'model' => App\Models\Guru::class,
        ],
        
        // Provider untuk admin 
        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,
    ],


    ],

    'passwords' => [
        // Untuk user biasa (default Laravel)
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Untuk guru (jika ingin pakai reset password juga)
        'gurus' => [
            'provider' => 'gurus',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Untuk siswa (jika ingin pakai reset password juga)
        'siswas' => [
            'provider' => 'siswas',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
