<?php
$host = 'localhost';
$port = '3306';       // MAMP on Mac: usually '8889'
$name = 'davao_boss_computer';
$user = 'root';
$pass = '';            // MAMP on Mac: usually 'root'

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Shown directly since this is a local dev setup, not production.
    exit('Database connection failed: ' . $e->getMessage()
        . ' — make sure MySQL is running and the "' . $name . '" database exists.');
}
