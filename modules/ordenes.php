<?php
/**
 * Lógica de Negocio para Órdenes de Servicio.
 * TechTrack - Sistema de Gestión de Servicios Técnicos.
 */

require_once __DIR__ . '/../includes/conexion.php';

/**
 * Obtener todas las órdenes de servicio con el nombre del cliente.
 */
function obtenerOrdenes($estado = null) {
    global $pdo;
    $sql = "SELECT o.*, c.nombre as cliente_nombre 
            FROM ordenes_servicio o 
            JOIN clientes c ON o.cliente_id = c.id";
    
    if ($estado) {
        $sql .= " WHERE o.estado = :estado";
    }
    
    $sql .= " ORDER BY o.fecha_entrada DESC";
    
    $stmt = $pdo->prepare($sql);
    if ($estado) {
        $stmt->bindParam(':estado', $estado);
    }
    $stmt->execute();
    return $stmt->fetchAll();
}

/**
 * Crear una nueva orden de servicio.
 */
function crearOrden($cliente_id, $equipo, $descripcion_problema) {
    global $pdo;
    $sql = "INSERT INTO ordenes_servicio (cliente_id, equipo, descripcion_problema, estado) 
            VALUES (:cliente_id, :equipo, :descripcion_problema, 'Pendiente')";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([
        ':cliente_id' => $cliente_id,
        ':equipo' => $equipo,
        ':descripcion_problema' => $descripcion_problema
    ]);
}

/**
 * Actualizar el estado de una orden.
 */
function actualizarEstadoOrden($id, $nuevo_estado) {
    global $pdo;
    $estados_validos = ['Pendiente', 'En proceso', 'Finalizado'];
    if (!in_array($nuevo_estado, $estados_validos)) return false;

    $sql = "UPDATE ordenes_servicio SET estado = :estado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':estado' => $nuevo_estado, ':id' => $id]);
}

/**
 * Eliminar una orden (Borrado físico).
 */
function eliminarOrden($id) {
    global $pdo;
    $sql = "DELETE FROM ordenes_servicio WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    return $stmt->execute([':id' => $id]);
}
?>
