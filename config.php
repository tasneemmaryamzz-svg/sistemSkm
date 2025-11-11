<?php
session_start();

$pdo = new PDO('mysql:host=localhost;dbname=skm_system;charset=utf8mb4','root','', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);

function is_logged() {
    return isset($_SESSION['user']);
}

function require_login() {
    if (!is_logged()) {
        header('Location: login.php');
        exit;
    }
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_role($role) {
    require_login();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        echo "Akses ditolak: untuk $role sahaja.";
        exit;
    }
}
