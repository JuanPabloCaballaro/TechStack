<?php
/**
 * Conexión segura a la Base de Datos utilizando PDO.
 * TechTrack - Sistema de Gestión de Servicios Técnicos.
 */

$host = 'localhost';
$db   = 'techtrack_db';
$user = 'root'; // Ajustar según configuración de entorno
$pass = '';     // Ajustar según configuración de entorno
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // En producción, no mostrar el error detallado al usuario.
     error_log("Error de conexión: " . $e->getMessage());
     die("Error crítico de sistema. Por favor, intente más tarde.");
}

// $pdo está disponible para ser incluido en otros scripts.
?>
