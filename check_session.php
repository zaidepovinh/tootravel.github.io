<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'isLoggedIn' => isset($_SESSION['user_id']),
    'role' => $_SESSION['role'] ?? null,
    'username' => $_SESSION['username'] ?? null,
    'avatar' => $_SESSION['avatar'] ?? null
]);