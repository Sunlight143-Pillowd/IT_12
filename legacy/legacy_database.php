<?php

$host = 'localhost';
$port = '3306';
$name = 'davao_boss_computer';
$user = 'root';
$pass = '';

$dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

$pdo = null;

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    error_log('Legacy storefront DB unavailable: ' . $e->getMessage());
    $pdo = null;
}
