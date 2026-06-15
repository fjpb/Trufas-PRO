<?php
require_once '../config.php';
$usuario_id = verificarSesion();

$method = $_SERVER['REQUEST_METHOD'];

// GET: listar melgas del usuario
if ($method === 'GET') {
    $stmt = $conn->prepare("SELECT id, nombre, ubicacion, total_arboles, fecha_creacion FROM melgas WHERE usuario_id = ? ORDER BY id DESC");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $melgas = [];
    while ($row = $result->fetch_assoc()) {
        // Obtener estadísticas de árboles para cada melga
        $melga_id = $row['id'];
        $arboles_query = $conn->prepare("SELECT COUNT(*) as total, SUM(en_produccion) as productores FROM arboles WHERE melga_id = ?");
        $arboles_query->bind_param("i", $melga_id);
        $arboles_query->execute();
        $stats = $arboles_query->get_result()->fetch_assoc();
        $row['total_arboles_real'] = $stats['total'] ?? 0;
        $row['productores'] = $stats['productores'] ?? 0;
        $melgas[] = $row;
    }
    responder($melgas);
}

// POST: crear nueva melga
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $nombre = $data['nombre'] ?? '';
    $ubicacion = $data['ubicacion'] ?? '';
    $num_arboles = intval($data['num_arboles'] ?? 0);

    if (empty($nombre) || $num_arboles < 1) {
        responder(['error' => 'Nombre y número de árboles válidos requeridos'], 400);
    }

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO melgas (usuario_id, nombre, ubicacion, total_arboles) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $usuario_id, $nombre, $ubicacion, $num_arboles);
        $stmt->execute();
        $melga_id = $conn->insert_id;

        // Crear árboles
        $stmt_arbol = $conn->prepare("INSERT INTO arboles (melga_id, codigo, en_produccion) VALUES (?, ?, 0)");
        for ($i = 1; $i <= $num_arboles; $i++) {
            $codigo = substr($nombre, 0, 3) . '-' . $i;
            $stmt_arbol->bind_param("is", $melga_id, $codigo);
            $stmt_arbol->execute();
        }
        $conn->commit();
        responder(['success' => true, 'melga_id' => $melga_id]);
    } catch (Exception $e) {
        $conn->rollback();
        responder(['error' => 'Error al crear melga: ' . $e->getMessage()], 500);
    }
}

// DELETE: eliminar melga (y sus árboles y cosechas por cascada)
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    if ($id <= 0) responder(['error' => 'ID inválido'], 400);
    $stmt = $conn->prepare("DELETE FROM melgas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $id, $usuario_id);
    if ($stmt->execute()) {
        responder(['success' => true]);
    } else {
        responder(['error' => 'No se pudo eliminar'], 500);
    }
}
?>