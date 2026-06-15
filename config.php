<?php
// config.php - Configuración de la base de datos y sesiones
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'USER');   // cámbialo por el de tu hosting
define('DB_PASS', 'PASS');
define('DB_NAME', 'DB');

// Conexión a la base de datos
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    http_response_code(500);
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}
$conn->set_charset('utf8mb4');

// Función para verificar si el usuario está autenticado
function verificarSesion() {
    if (!isset($_SESSION['usuario_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
    return $_SESSION['usuario_id'];
}

// Función para responder en JSON
function responder($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
?>