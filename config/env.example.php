<?php
    require __DIR__ . '/local.development.php';

    // Database
    $settings['db1']['username'] = 'root';
    $settings['db1']['password'] = '';
    $settings['db1']['database'] = 'packing';

    $settings['db2']['host'] = '192.168.10.18';
    $settings['db2']['username'] = 'nspwinapp';
    $settings['db2']['password'] = '21sPn7$';
    $settings['db2']['database'] = 'nsp';

    $settings['api_auth'] = [
        'users' => [
            'api-admin' => 'secret',
            'api-user' => 'secret',
        ],
    ];
?>
