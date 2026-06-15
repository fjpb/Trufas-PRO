<?php
require_once '../config.php';
$usuario_id = verificarSesion();

$total_arboles = $conn->query("SELECT COUNT(*) as total FROM arboles a JOIN melgas m ON a.melga_id = m.id WHERE m.usuario_id = $usuario_id")->fetch_assoc()['total'];
$en_produccion = $conn->query("SELECT COUNT(*) as total FROM arboles a JOIN melgas m ON a.melga_id = m.id WHERE m.usuario_id = $usuario_id AND a.en_produccion = 1")->fetch_assoc()['total'];
$total_kg = $conn->query("SELECT SUM(c.kg) as total FROM cosechas c JOIN arboles a ON c.arbol_id = a.id JOIN melgas m ON a.melga_id = m.id WHERE m.usuario_id = $usuario_id")->fetch_assoc()['total'] ?? 0;
$total_trufas = $conn->query("SELECT SUM(c.cantidad_trufas) as total FROM cosechas c JOIN arboles a ON c.arbol_id = a.id JOIN melgas m ON a.melga_id = m.id WHERE m.usuario_id = $usuario_id")->fetch_assoc()['total'] ?? 0;
$total_melgas = $conn->query("SELECT COUNT(*) as total FROM melgas WHERE usuario_id = $usuario_id")->fetch_assoc()['total'];

responder([
    'total_arboles' => $total_arboles,
    'en_produccion' => $en_produccion,
    'total_kg' => round($total_kg, 1),
    'total_trufas' => $total_trufas,
    'total_melgas' => $total_melgas
]);
?>