<?php
header("Content-Type: application/json");
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$data = json_decode(file_get_contents("php://input"), true);
$ids = $data["ids"] ?? [];

if (count($ids) === 0) {
  echo json_encode(["error" => "No hay IDs"]);
  exit;
}

$eliminados = [];

// Preparar eliminación y registrar eliminados
foreach ($ids as $id) {
  $res = pg_query_params($conn, "SELECT reporte_id, tipo, observaciones, fecha FROM reportes WHERE reporte_id = $1", [$id]);
  $r = pg_fetch_assoc($res);
  if ($r) {
    $eliminados[] = $r;
    pg_query_params($conn, "DELETE FROM reportes WHERE reporte_id = $1", [$id]);
  }
}

echo json_encode([
  "mensaje" => count($eliminados) . " reporte(s) eliminado(s).",
  "eliminados" => $eliminados
]);
?>