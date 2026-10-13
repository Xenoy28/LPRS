<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;dbname=lprs;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

function peut_acceder(array $canal): bool {
    return $canal['type_acces'] === 'tous' || $canal['type_acces'] === $_SESSION['user_role'];
}

function e($texte) {
    return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
}