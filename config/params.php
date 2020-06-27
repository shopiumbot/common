<?php

return [
    'adminEmail' => 'dev@pixelion.com.ua',
    'payment'=>[
        'liqpay'=>[
            'public_key'=>'',
            'private_key'=>'',
        ]
    ],
    'maxUploadImageSize' => [
        'width' => 800,
        'height' => 800
    ],
    'plan' => [
        1 => [
            'name'=>'Basic',
            'product_limit' => 200,
            'product_upload_files' => 1
        ],
        2 => [
            'name'=>'Standard',
            'product_limit' => 5000,
            'product_upload_files' => 3
        ],
        3 => [
            'name'=>'Premium',
            'product_limit' => true,
            'product_upload_files' => true
        ]
    ]
];
