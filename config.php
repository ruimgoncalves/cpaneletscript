<?php

return [
    'testing' => false,
    'minDays' => 15, // Minimum days to wait before requesting new certificates
    'cpanel' => [
        'host' => 'https://127.0.0.1:2083', // 'https://mydomain.com:2083' // ip or domain complete with its protocol and port
        'username' => '', // CPanel username
        'password' => '', // CPanel user password
        // 'token' => '' // CPanel api access token, use this instead of password, comment the above line
    ],
    'accounts' => [
        [
            'email' => 'info@mydomain.com',
            'publicPath' => '../public_html', // related to the script location, no trailing /
            'domains' => ['mydomain.com', 'www.mydomain.com'],
            'disabled' => false,
        ],
    ]
];