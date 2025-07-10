<?php
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

// Puedes hacer un SELECT con ST_Y/ST_X o ST_AsText
$sql = "SELECT ST_Y(geom) AS lat, ST_X(geom) AS lng FROM inventario_forestal";
$result = pg_query($conn, $sql);

$puntos = [];
while ($row = pg_fetch_assoc($result)) {
  $puntos[] = [(float)$row['lat'], (float)$row['lng']];
}

echo json_encode($puntos);
?>
