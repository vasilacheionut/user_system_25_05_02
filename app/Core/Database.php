<?php
class Database {
    private static $pdo;

    public static function connect() {
        if (!self::$pdo) {
            $config = require __DIR__ . '/../../config/config.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset=utf8mb4";
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }
        return self::$pdo;
    }
}
