<?php

// connexion à la base de données:
$dbHost = 'localhost';
$dbName = 'OC-mini-jeu-de-combat';
$dbUser = 'root';
$dbPass = 'root';

$dsn = "mysql:host=$dbHost;dbname=$dbName";

try {
    $options = array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,);
    $db = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Erreur de connexion à la base de donnée :'.$e->getMessage());
}
