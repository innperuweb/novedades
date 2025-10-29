<?php

declare(strict_types=1);

final class Database
{
    private static ?\PDO $connection = null;

    public static function connect(): \PDO
    {
        if (self::$connection instanceof \PDO) {
            return self::$connection;
        }

        $config = require CONFIG_PATH . '/database.php';

        $dsn = $config['dsn'] ?? '';
        $username = $config['username'] ?? null;
        $password = $config['password'] ?? null;
        $options = $config['options'] ?? [];

        self::$connection = new \PDO($dsn, $username, $password, $options);

        return self::$connection;
    }

    public static function disconnect(): void
    {
        self::$connection = null;
    }
}
