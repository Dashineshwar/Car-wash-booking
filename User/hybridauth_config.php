<?php
return [
    'callback' => 'http://localhost/alphasphinx/User/social_login.php', // Replace with your redirect URI
    'providers' => [
        'Google' => [
            'enabled' => true,
            'keys' => [
                'id' => '871916824098-ndi5i297tl0b76phik8ut40ms1jptkh1.apps.googleusercontent.com', // Your Client ID
                'secret' => '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', // Your Client Secret
            ],
        ],
    ],
    'debug_mode' => false,
    'debug_file' => __DIR__ . '/hybridauth.log',
];
