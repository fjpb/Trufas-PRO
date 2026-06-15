<?php
require_once '../config.php';
$usuario_id = verificarSesion();

$method = $_SERVER['REQUEST_METHOD'];
$melga_id = intval($_GET['melga_id'] ?? 0);

// Verificar que la melga pertenece al usuario
function verificarMelga($melga_id, $conn, $usuario_id) {
    $stmt = $conn->prepare("SELECT id FROM melgas WHERE id = ? AND usuario_id = ?");
    $stmt->bind_param("ii", $melga_id, $usuario_id);
    $stmt->execute();
    return $stmt->get_result()->num_rows > 0;
}

// GET: listar árboles de una melga
if ($method === 'GET' && $melga_id) {
    if (!verificarMelga($melga_id, $conn, $usuario_id)) responder(['error' => 'Acceso denegado'], 403);
    $stmt = $conn->prepare("SELECT id, codigo, en_produccion, fecha_inicio_produccion, estado_sanitario, ultima_revision FROM arboles WHERE melga_id = ? ORDER BY codigo");
    $stmt->bind_param("i", $melga_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $arboles = [];
    while ($row = $result->fetch_assoc()) {
        $arboles[] = $row;
    }
    responder($arboles);
}

// PUT: actualizar un campo de un árbol
if ($method === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $arbol_id = intval($data['arbol_id'] ?? 0);
    $campo = $data['campo'] ?? '';
    $valor = $data['valor'] ?? '';

    // Verificar que el árbol pertenece al usuario a través de la melga
    $stmt_verif = $conn->prepare("SELECT a.id FROM arboles a JOIN melgas m ON a.melga_id = m.id WHERE a.id = ? AND m.usuario_id = ?");
    $stmt_verif->bind_param("ii", $arbol_id, $usuario_id);
    $stmt_verif->execute();
    if ($stmt_verif->get_result()->num_rows === 0) responder(['error' => 'Acceso denegado'], 403);

    $campos_permitidos = ['en_produccion', 'fecha_inicio_produccion', 'estado_sanitario', 'ultima_revision'];
    if (!in_array($campo, $campos_permitidos)) responder(['error' => 'Campo no permitido'], 400);

    if ($campo === 'en_produccion') {
        $valor = $valor ? 1 : 0;
        $stmt = $conn->prepare("UPDATE arboles SET en_produccion = ? WHERE id = ?");
        $stmt->bind_param("ii", $valor, $arbol_id);
    } else {
        $stmt = $conn->prepare("UPDATE arboles SET $campo = ? WHERE id = ?");
        $stmt->bind_param("si", $valor, $arbol_id);
    }
    if ($stmt->execute()) {
        responder(['success' => true]);
    } else {
        responder(['error' => 'Error al actualizar'], 500);
    }
}
?>