<?php
$host = 'localhost';
$dbname = 'grud';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dsn = 'mysql:host=localhost;dbname=gestion_produits_nouveau;charset=utf8';
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}
?>