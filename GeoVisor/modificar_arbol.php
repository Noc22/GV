<?php
header("Content-Type: application/json");
$data = json_decode(file_get_contents("php://input"), true);

$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$id = $data["id"];
$lat = $data["lat"];
$lng = $data["lng"];

$sql = "UPDATE inventario_forestal SET geom = ST_SetSRID(ST_MakePoint($1, $2), 4326) WHERE id = $3";
$result = pg_query_params($conn, $sql, [$lng, $lat, $id]);

if ($result) {
  echo json_encode(["mensaje" => "Ubicación modificada correctamente"]);
} else {
  echo json_encode(["error" => "Error al modificar"]);
}
?>
