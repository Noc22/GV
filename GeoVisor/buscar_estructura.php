<?php
header("Content-Type: application/json");
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$q = $_GET["q"] ?? '';
$modo = $_GET["modo"] ?? '';

if (!$q) {
  echo json_encode([]);
  exit;
}

// Modo lista: solo sugerencias
if ($modo === "lista") {
  $sql = "
    SELECT DISTINCT estructura AS nombre
    FROM estructuras
    WHERE estructura ILIKE $1 OR tipo_area ILIKE $1 OR estructura ILIKE $1
    LIMIT 10;
  ";
  $res = pg_query_params($conn, $sql, ['%' . $q . '%']);
  $lista = [];
  while ($row = pg_fetch_assoc($res)) $lista[] = $row;
  echo json_encode($lista);
  exit;
}

// Modo normal: devolver una geometría
$sql = "
  SELECT estructura, tipo_area, ST_AsGeoJSON(the_geom) AS geometry
  FROM estructuras
  WHERE estructura ILIKE $1 OR tipo_area ILIKE $1 OR estructura ILIKE $1
  LIMIT 1;
";
$res = pg_query_params($conn, $sql, ['%' . $q . '%']);
echo json_encode(pg_fetch_assoc($res));
?>
