<?php

declare(strict_types=1);

$driver = getenv('DB_CONNECTION') ?: 'mysql';
$driver = strtolower($driver);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT');
$database = getenv('DB_DATABASE') ?: 'novedades';
$username = getenv('DB_USERNAME') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';
$charset = getenv('DB_CHARSET') ?: 'utf8mb4';

$dsn = getenv('DB_DSN') ?: '';

if ($dsn === '') {
    switch ($driver) {
        case 'pgsql':
        case 'postgres':
        case 'postgresql':
            $driver = 'pgsql';
            $port = $port ?: '5432';
            $dsn = sprintf('pgsql:host=%s;port=%s;dbname=%s', $host, $port, $database);
            break;
        case 'sqlite':
            $rootPath = defined('ROOT_PATH') ? ROOT_PATH : dirname(__DIR__, 2);
            $databasePath = getenv('DB_DATABASE') ?: ($rootPath . '/database/database.sqlite');
            $dsn = 'sqlite:' . $databasePath;
            break;
        case 'sqlsrv':
        case 'mssql':
            $driver = 'sqlsrv';
            $port = $port ?: '1433';
            $dsn = sprintf('sqlsrv:Server=%s,%s;Database=%s', $host, $port, $database);
            break;
        case 'mysql':
        default:
            $driver = 'mysql';
            $port = $port ?: '3306';
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $database, $charset);
            break;
    }
}

$persistent = filter_var(getenv('DB_PERSISTENT') ?: false, FILTER_VALIDATE_BOOL);

return [
    'driver' => $driver,
    'dsn' => $dsn,
    'host' => $host,
    'port' => $port,
    'database' => $database,
    'username' => $username,
    'password' => $password,
    'charset' => $charset,
    'options' => [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        \PDO::ATTR_EMULATE_PREPARES => false,
        \PDO::ATTR_PERSISTENT => $persistent,
    ],
];
