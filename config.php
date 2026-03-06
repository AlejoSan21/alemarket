<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "localhost";
$db = "alemarket";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error conexión: " . $e->getMessage());
}
function formatoPeso($numero) {
    return '$ ' . number_format($numero, 0, ',', '.');
}