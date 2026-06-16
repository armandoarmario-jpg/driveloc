<?php
session_start();

$host = 'localhost';
$dbname = 'driveloc';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Erro na conexão: ' . $e->getMessage());
}

define('BASE_URL', '/driveloc');

function db() {
    global $pdo;
    return $pdo;
}

function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '');
}