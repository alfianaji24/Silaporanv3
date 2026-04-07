<?php

/**
 * Konfigurasi Tema Aplikasi Mobile
 * Semua tema didefinisikan di sini untuk kemudahan maintenance
 * Tambahkan tema baru dengan struktur yang sama
 */

return [
    'schemes' => [
        'green' => [
            'name' => 'Green (Default)',
            'icon' => 'ti ti-leaf',
            'primary' => '#32745e',
            'primary_light' => '#58907D',
            'bg_body' => '#f0fdf9',
            'rgb' => '50, 116, 94',
            'secondary' => '#3ab58c',
            'bg_gradient' => 'linear-gradient(135deg, #064e3b 0%, #065f46 100%)',
        ],
        'blue' => [
            'name' => 'Blue (Ocean)',
            'icon' => 'ti ti-wave',
            'primary' => '#0d47a1',
            'primary_light' => '#1976d2',
            'bg_body' => '#eff6ff',
            'rgb' => '13, 71, 161',
            'secondary' => '#1976d2',
            'bg_gradient' => 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%)',
        ],
        'red' => [
            'name' => 'Red (Passion)',
            'icon' => 'ti ti-heart',
            'primary' => '#b71c1c',
            'primary_light' => '#d32f2f',
            'bg_body' => '#fef2f2',
            'rgb' => '183, 28, 28',
            'secondary' => '#d32f2f',
            'bg_gradient' => 'linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%)',
        ],
        'orange' => [
            'name' => 'Orange (Sunset)',
            'icon' => 'ti ti-sun',
            'primary' => '#e65100',
            'primary_light' => '#f57c00',
            'bg_body' => '#fff8f1',
            'rgb' => '230, 81, 0',
            'secondary' => '#f57c00',
            'bg_gradient' => 'linear-gradient(135deg, #7c2d12 0%, #9a3412 100%)',
        ],
        'purple' => [
            'name' => 'Purple (Royal)',
            'icon' => 'ti ti-crown',
            'primary' => '#4a148c',
            'primary_light' => '#7b1fa2',
            'bg_body' => '#faf5ff',
            'rgb' => '74, 20, 140',
            'secondary' => '#7b1fa2',
            'bg_gradient' => 'linear-gradient(135deg, #4c1d95 0%, #5b21b6 100%)',
        ],
        'dark' => [
            'name' => 'Dark (Night)',
            'icon' => 'ti ti-moon',
            'primary' => '#bb86fc',
            'primary_light' => '#9a6bdb',
            'bg_body' => '#121212',
            'rgb' => '187, 134, 252',
            'secondary' => '#cf6679',
            'bg_gradient' => 'linear-gradient(135deg, #121212 0%, #1e1e1e 100%)',
        ],
    ],
    
    /**
     * Tema default jika tidak ada yang dipilih
     */
    'default' => 'green',
];
