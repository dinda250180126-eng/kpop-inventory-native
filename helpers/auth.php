<?php
session_start();

function checkAuth() {
    if (!isset($_SESSION['user'])) {
        header('Location: /public/login.php');
        exit;
    }
}

function checkRole($role) {
    checkAuth();
    if ($_SESSION['user']['role'] !== $role) {
        http_response_code(403);
        die('403 Forbidden: Halaman ini hanya untuk ' . strtoupper($role));
    }
}