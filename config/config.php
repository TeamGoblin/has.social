<?php

try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env');
    $dotenv->load();
} catch (Exception $e) {
    // Run install
    include __DIR__ . '/../core/install.php';
    die();
}

/* Database Params */
$db_config = [
    'driver'    => $_ENV['DRIVER'],
    'host'      => $_ENV['HOST'],
    'database'  => $_ENV['DATABASE'],
    'username'  => $_ENV['USERNAME'],
    'password'  => $_ENV['PASSWORD'],
    'port'      => $_ENV['PORT'],
];