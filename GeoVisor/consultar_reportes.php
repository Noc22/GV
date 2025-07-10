<?php
header("Content-Type: application/json");
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$data = json_decode(file_get_contents("php://input"), true);
$id = $data["id"] ?? null;
$fecha = $data["fecha"] ?? null;

$sql = "SELECT novedad_id, tipo, observaciones, fecha FROM reportes WHERE true";
$params = [];
$idx = 1;

if ($id) {
  $sql .= " AND novedad_id = $" . $idx++;
  $params[] = $id;
}

if ($fecha) {
  $sql .= " AND fecha::date = $" . $idx++;
  $params[] = $fecha;
}

$result = pg_query_params($conn, $sql, $params);
$reportes = [];

while ($row = pg_fetch_assoc($result)) {
  $reportes[] = $row;
}

echo json_encode($reportes);
?>