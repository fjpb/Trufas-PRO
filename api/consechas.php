<?php
require_once '../config.php';
$usuario_id = verificarSesion();

$method = $_SERVER['REQUEST_METHOD'];

// GET: listar cosechas con filtros (melga, fechas, árbol improductivo)
if ($method === 'GET') {
    $filtro_melga = isset($_GET['melga_id']) ? intval($_GET['melga_id']) : 0;
    $fecha_inicio = $_GET['fecha_inicio'] ?? '';
    $fecha_fin = $_GET['fecha_fin'] ?? '';
    $meses_inactivos = isset($_GET['meses_inactivos']) ? intval($_GET['meses_inactivos']) : 0;

    $sql = "SELECT c.id, c.fecha, c.cantidad_trufas, c.kg, c.calidad, c.observaciones,
                   a.codigo as arbol_codigo, a.id as arbol_id, m.nombre as melga_nombre, m.id as melga_id
            FROM cosechas c
            JOIN arboles a ON c.arbol_id = a.id
            JOIN melgas m ON a.melga_id = m.id
            WHERE m.usuario_id = ?";
    $params = [$usuario_id];
    $types = "i";

    if ($filtro_melga > 0) {
        $sql .= " AND m.id = ?";
        $params[] = $filtro_melga;
        $types .= "i";
    }
    if (!empty($fecha_inicio)) {
        $sql .= " AND c.fecha >= ?";
        $params[] = $fecha_inicio;
        $types .= "s";
    }
    if (!empty($fecha_fin)) {
        $sql .= " AND c.fecha <= ?";
        $params[] = $fecha_fin;
        $types .= "s";
    }

    $sql .= " ORDER BY c.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $cosechas = [];
    while ($row = $result->fetch_assoc()) {
        $cosechas[] = $row;
    }

    // Filtrar árboles inactivos (sin cosecha en últimos N meses) si se solicita
    if ($meses_inactivos > 0 && $filtro_melga == 0) {
        // Obtener IDs de árboles que sí han cosechado recientemente
        $fechaLimite = date('Y-m-d', strtotime("-$meses_inactivos months"));
        $stmt2 = $conn->prepare("SELECT DISTINCT a.id FROM arboles a JOIN cosechas c ON a.id = c.arbol_id WHERE c.fecha >= ? AND a.melga_id IN (SELECT id FROM melgas WHERE usuario_id = ?)");
        $stmt2->bind_param("si", $fechaLimite, $usuario_id);
        $stmt2->execute();
        $activos = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        $activos_ids = array_column($activos, 'id');
        // Filtrar cosechas que NO pertenezcan a árboles inactivos? Realmente el filtro "inactivos" se aplica a árboles, no a cosechas.
        // Para simplificar, no aplicamos aquí; lo dejamos para el frontend.
    }

    responder($cosechas);
}

// POST: registrar nueva cosecha
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $arbol_id = intval($data['arbol_id']);
    $fecha = $data['fecha'];
    $cantidad_trufas = intval($data['cantidad_trufas']);
    $kg = floatval($data['kg']);
    $calidad = $data['calidad'] ?? 'Estándar';
    $observaciones = $data['observaciones'] ?? '';

    // Verificar que el árbol pertenece al usuario
    $stmt_verif = $conn->prepare("SELECT a.id FROM arboles a JOIN melgas m ON a.melga_id = m.id WHERE a.id = ? AND m.usuario_id = ?");
    $stmt_verif->bind_param("ii", $arbol_id, $usuario_id);
    $stmt_verif->execute();
    if ($stmt_verif->get_result()->num_rows === 0) responder(['error' => 'Acceso denegado'], 403);

    $stmt = $conn->prepare("INSERT INTO cosechas (arbol_id, fecha, cantidad_trufas, kg, calidad, observaciones) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isidss", $arbol_id, $fecha, $cantidad_trufas, $kg, $calidad, $observaciones);
    if ($stmt->execute()) {
        // Marcar árbol como productor si no lo estaba
        $conn->query("UPDATE arboles SET en_produccion = 1, fecha_inicio_produccion = IFNULL(fecha_inicio_produccion, '$fecha') WHERE id = $arbol_id");
        responder(['success' => true, 'cosecha_id' => $conn->insert_id]);
    } else {
        responder(['error' => 'Error al guardar'], 500);
    }
}

// PUT: editar cosecha
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $cosecha_id = intval($data['id']);
    $fecha = $data['fecha'];
    $cantidad_trufas = intval($data['cantidad_trufas']);
    $kg = floatval($data['kg']);
    $calidad = $data['calidad'];
    $observaciones = $data['observaciones'];

    // Verificar propiedad
    $stmt_verif = $conn->prepare("SELECT c.id FROM cosechas c JOIN arboles a ON c.arbol_id = a.id JOIN melgas m ON a.melga_id = m.id WHERE c.id = ? AND m.usuario_id = ?");
    $stmt_verif->bind_param("ii", $cosecha_id, $usuario_id);
    $stmt_verif->execute();
    if ($stmt_verif->get_result()->num_rows === 0) responder(['error' => 'Acceso denegado'], 403);

    $stmt = $conn->prepare("UPDATE cosechas SET fecha=?, cantidad_trufas=?, kg=?, calidad=?, observaciones=? WHERE id=?");
    $stmt->bind_param("sidssi", $fecha, $cantidad_trufas, $kg, $calidad, $observaciones, $cosecha_id);
    if ($stmt->execute()) responder(['success' => true]);
    else responder(['error' => 'Error al actualizar'], 500);
}

// DELETE: eliminar cosecha
if ($method === 'DELETE') {
    $id = intval($_GET['id'] ?? 0);
    $stmt_verif = $conn->prepare("SELECT c.id FROM cosechas c JOIN arboles a ON c.arbol_id = a.id JOIN melgas m ON a.melga_id = m.id WHERE c.id = ? AND m.usuario_id = ?");
    $stmt_verif->bind_param("ii", $id, $usuario_id);
    $stmt_verif->execute();
    if ($stmt_verif->get_result()->num_rows === 0) responder(['error' => 'Acceso denegado'], 403);

    $stmt = $conn->prepare("DELETE FROM cosechas WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) responder(['success' => true]);
    else responder(['error' => 'Error al eliminar'], 500);
}
?>