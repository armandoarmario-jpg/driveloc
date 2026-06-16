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
define('UPLOAD_DIR', __DIR__ . '/../uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');

function db() {
    global $pdo;
    return $pdo;
}

function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return BASE_URL . ($path !== '' ? '/' . $path : '');
}

function upload_path(string $path = ''): string {
    $path = ltrim($path, '/\\');
    return rtrim(UPLOAD_DIR, DIRECTORY_SEPARATOR) . ($path !== '' ? DIRECTORY_SEPARATOR . $path : '');
}

function upload_url(string $path = ''): string {
    $path = ltrim($path, '/\\');
    return UPLOAD_URL . ($path !== '' ? '/' . $path : '');
}