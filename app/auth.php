<?php
require_once __DIR__ . '/config.php';

function login_required() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . url('login.php'));
        exit;
    }
}

function admin_required() {
    login_required();
    if ($_SESSION['user_tipo'] !== 'admin') {
        header('Location: ' . url('index.php'));
        exit;
    }
}