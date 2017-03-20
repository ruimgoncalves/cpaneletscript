<?php

// you can use any logger according to Psr\Log\LoggerInterface
class Logger {
    function __call($name, $arguments) {
        $str = $arguments[0];
        for ($i = 1; $i < sizeof($arguments); $i++)
            $str .= '\n' . $arguments[$i];
        if (php_sapi_name() == 'cli')
            echo date('Y-m-d H:i:s')." [$name] ${str}\n";
        else
            echo date('Y-m-d H:i:s')." [$name] ${str}<br/>";
    }
}

$certInfo = [
    'countryCode' => 'PT',
    'state' => 'Lisbon',
    'contact' => ['mailto:my@email.com'] // optional
];

return [
    'logger' => new Logger(),
    'minDays' => 15, // Minimum days to wait before requesting new certificates
    'storagePath' => '.certificates', // the folder where certificates are  located, related or absolute, DO NOT expose to the internet
    'cpanel' => [
        'host' => 'https://127.0.0.1:2083', // 'https://mydomain.com:2083' // ip or domain complete with its protocol and port
        'username' => '',
        'password' => ''
    ],
    'domains' => [
        'mydomain.com' => [
            'publicPath' => '../public_html', // related to the script location
            'certInfo' => $certInfo,
            'and' => ['www.mydomain.com'] // and alternative domains
        ],
        'sub.mydomain.com' => [
            'publicPath' => '../../public_html/sub/public', // related to the script location
            'certInfo' => $certInfo
        ]
    ]
];