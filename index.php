<?php
/**
 * Dashboard Principal - Sistema TechTrack.
 */

require_once 'includes/conexion.php';
require_once 'modules/ordenes.php';
require_once 'modules/clientes.php';

// Obtener el estado seleccionado (por defecto 'Pendiente')
$estado_actual = isset($_GET['estado']) ? $_GET['estado'] : 'Pendiente';

// Procesar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    if ($_POST['accion'] === 'nueva_orden') {
        $cliente_id = $_POST['cliente_id'];
        $equipo = $_POST['equipo'];
        $problema = $_POST['descripcion_problema'];
        if (crearOrden($cliente_id, $equipo, $problema)) {
            header("Location: index.php?estado=Pendiente&msg=creado");
            exit;
        }
    }
    
    if ($_POST['accion'] === 'cambiar_estado') {
        $id = $_POST['id'];
        $nuevo_estado = $_POST['nuevo_estado'];
        if (actualizarEstadoOrden($id, $nuevo_estado)) {
            header("Location: index.php?estado=$nuevo_estado&msg=actualizado");
            exit;
        }
    }

    if ($_POST['accion'] === 'eliminar_orden') {
        $id = $_POST['id'];
        if (eliminarOrden($id)) {
            header("Location: index.php?estado=$estado_actual&msg=eliminado");
            exit;
        }
    }
}

// Obtener datos reales de la base de datos
$ordenes = obtenerOrdenes($estado_actual);
$clientes = obtenerClientes();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechTrack - Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .nav-pills .nav-link { border-radius: 10px; font-weight: 600; color: #6c757d; }
        .nav-pills .nav-link.active { background-color: #0d6efd; color: white; }
        .table-premium thead { background-color: #f1f3f5; }
        .table-premium th { font-weight: 600; font-size: 0.85rem; color: #495057; text-transform: uppercase; }
        .badge-status { border-radius: 20px; padding: 5px 12px; }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <main class="col-lg-10">
            <!-- Header section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">TechTrack <span class="badge bg-secondary fs-6">v1.0</span></h1>
                    <p class="text-muted small mb-0">Gestión de Ordenes de Servicio para Técnicos</p>
                </div>
                <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaOrden">
                    + Nueva Orden
                </button>
            </div>

            <!-- Feedback Messages -->
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    Operación realizada con éxito.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Navigation Tabs (Status Filter) -->
            <ul class="nav nav-pills mb-4 gap-2 bg-white p-2 rounded shadow-sm d-inline-flex">
                <li class="nav-item">
                    <a class="nav-link <?php echo $estado_actual === 'Pendiente' ? 'active' : ''; ?>" href="index.php?estado=Pendiente">📥 Pendientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $estado_actual === 'En proceso' ? 'active' : ''; ?>" href="index.php?estado=En proceso">⚙️ En Proceso</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $estado_actual === 'Finalizado' ? 'active' : ''; ?>" href="index.php?estado=Finalizado">✅ Finalizados</a>
                </li>
            </ul>

            <!-- Order Table -->
            <div class="card shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-premium table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Folio</th>
                                    <th>Cliente</th>
                                    <th>Equipo</th>
                                    <th>Problema</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($ordenes)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            No hay registros bajo el estado "<?php echo $estado_actual; ?>".
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach($ordenes as $orden): ?>
                                    <tr>
                                        <td class="ps-4"><strong>#<?php echo str_pad($orden['id'], 3, '0', STR_PAD_LEFT); ?></strong></td>
                                        <td><?php echo htmlspecialchars($orden['cliente_nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($orden['equipo']); ?></td>
                                        <td class="text-truncate" style="max-width: 200px;"><?php echo htmlspecialchars($orden['descripcion_problema']); ?></td>
                                        <td><small class="text-muted"><?php echo date('d/m/y H:i', strtotime($orden['fecha_entrada'])); ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <?php if($estado_actual === 'Pendiente'): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="cambiar_estado">
                                                        <input type="hidden" name="id" value="<?php echo $orden['id']; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="En proceso">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Iniciar">▶️</button>
                                                    </form>
                                                <?php elseif($estado_actual === 'En proceso'): ?>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="accion" value="cambiar_estado">
                                                        <input type="hidden" name="id" value="<?php echo $orden['id']; ?>">
                                                        <input type="hidden" name="nuevo_estado" value="Finalizado">
                                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Finalizar">✔️</button>
                                                    </form>
                                                <?php endif; ?>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="confirmarEliminar(<?php echo $orden['id']; ?>)" title="Eliminar">🗑️</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal: Nueva Orden (Sin cambios, solo por completitud del archivo editado) -->
<div class="modal fade" id="modalNuevaOrden" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Nueva Orden de Servicio</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php" method="POST">
                <input type="hidden" name="accion" value="nueva_orden">
                <div class="modal-body pb-3">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Cliente</label>
                        <select name="cliente_id" class="form-select" required>
                            <option value="">Seleccionar cliente...</option>
                            <?php foreach($clientes as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Equipo / Modelo</label>
                        <input type="text" name="equipo" class="form-control" placeholder="Ej: Notebook HP..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Descripción del Problema</label>
                        <textarea name="descripcion_problema" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form oculto para eliminación -->
<form id="formEliminar" method="POST" style="display:none;">
    <input type="hidden" name="accion" value="eliminar_orden">
    <input type="hidden" name="id" id="eliminarId">
</form>

<script>
function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de que deseas eliminar esta orden?')) {
        document.getElementById('eliminarId').value = id;
        document.getElementById('formEliminar').submit();
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
