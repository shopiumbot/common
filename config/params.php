<?php

return [
    'adminEmail' => 'info@shopiumbot.com',
    'payment' => [
        'liqpay' => [
            'public_key' => 'i50530989846',
            'private_key' => 'LtoClvytIkRP2wRophiuEAIL6XFenIX9WnFlVCNR',
            'provider' => '632593626:TEST:i56982357197', //635983722:LIVE:i50530989846
        ]
    ],
    'maxUploadImageSize' => [
        'width' => 1200,
        'height' => 1200
    ],
    'plan' => [
        1 => [
            'name' => 'Basic',
            'product_limit' => 200,
            'product_upload_files' => 1,
            'prices' => [
                1 => 300,
                12 => 270
            ]
        ],
        2 => [
            'name' => 'Standard',
            'product_limit' => 5000,
            'product_upload_files' => 3,
            'prices' => [
                1 => 700,
                12 => 650
            ]
        ],
        3 => [
            'name' => 'Premium',
            'product_limit' => true,
            'product_upload_files' => true,
            'prices' => [
                1 => 2500,
                12 => 2200
            ]
        ]
    ]
];
