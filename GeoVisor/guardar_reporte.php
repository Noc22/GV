<?php
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

if (!$conn) {
  die("Error de conexión");
}

$tipo = $_POST['tipo'];
$lat = $_POST['lat'];
$lng = $_POST['lng'];
$fecha = $_POST['fecha'];
$novedad_id = $_POST['novedad_id'];
$arbol_id = $_POST['arbol_id'];
$tipo_area = $_POST['tipo_area'];
$ubicacion = $_POST['ubicacion'];
$estado_fitosanitario = $_POST['estado_fitosanitario'];
$afectacion = $_POST['afectacion'];
$poda = $_POST['poda'];
$tratamiento = $_POST['tratamiento'];
$observaciones = $_POST['observaciones'];


$sql = "INSERT INTO reportes (tipo, geom, fecha, novedad_id, arbol_id, tipo_area, ubicacion, estado_fitosanitario, afectacion, poda, tratamiento, observaciones) 
        VALUES ($1, ST_SetSRID(ST_MakePoint($2, $3), 4326), $4, $5, $6, $7, $8, $9, $10, $11, $12, $13)";
$params = [$tipo, $lng, $lat, $fecha, $novedad_id, $arbol_id, $tipo_area, $ubicacion, $estado_fitosanitario, $afectacion, $poda, $tratamiento, $observaciones]; 

$result = pg_query_params($conn, $sql, $params);

if ($result) {
  echo "Reporte guardado correctamente.";
} else {
  echo "Error al guardar: " . pg_last_error($conn);
}

pg_close($conn);
?>
