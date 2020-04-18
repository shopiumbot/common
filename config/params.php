<?php

return [
    'adminEmail' => 'dev@pixelion.com.ua',
    'maxUploadImageSize' => [
        'width' => 1200,
        'height' => 1200
    ],
    'plan' => [
        // Basic
        1 => [
            'product_limit' => 3000,
            'product_upload_files' => 1
        ],
        // Standard
        2 => [
            'product_limit' => 10000,
            'product_upload_files' => 3
        ],
        // Premium
        3 => [
            'product_limit' => 25000,
            'product_upload_files' => 5
        ],
    ]
];
