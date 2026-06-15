<?php
require_once '../config.php';
$usuario_id = verificarSesion();

$sql = "SELECT c.fecha, m.nombre as melga, a.codigo as arbol, c.cantidad_trufas, c.kg, (c.kg / c.cantidad_trufas) as kg_por_trufa, c.observaciones, c.calidad
        FROM cosechas c
        JOIN arboles a ON c.arbol_id = a.id
        JOIN melgas m ON a.melga_id = m.id
        WHERE m.usuario_id = ?
        ORDER BY c.fecha DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

$filename = "cosechas_trufa_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Fecha', 'Melga', 'Árbol', 'N° trufas', 'Kg', 'Kg/trufa', 'Observaciones', 'Calidad']);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['fecha'],
        $row['melga'],
        $row['arbol'],
        $row['cantidad_trufas'],
        $row['kg'],
        round($row['kg_por_trufa'], 2),
        $row['observaciones'],
        $row['calidad']
    ]);
}
fclose($output);
exit;
?>