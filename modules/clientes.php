<?php
/**
 * Lógica de Negocio para Clientes.
 * TechTrack - Sistema de Gestión de Servicios Técnicos.
 */

require_once __DIR__ . '/../includes/conexion.php';

/**
 * Obtener lista de clientes para selectores.
 */
function obtenerClientes() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, nombre FROM clientes ORDER BY nombre ASC");
    return $stmt->fetchAll();
}

/**
 * Registrar un nuevo cliente.
 */
function registrarCliente($nombre, $telefono, $email, $direccion) {
    global $pdo;
    $sql = "INSERT INTO clientes (nombre, telefono, email, direccion) 
            VALUES (:nombre, :telefono, :email, :direccion)";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':nombre' => $nombre,
        ':telefono' => $telefono,
        ':email' => $email,
        ':direccion' => $direccion
    ]);
}
?>
