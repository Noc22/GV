<?php
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$query = "SELECT DISTINCT nombre_comun FROM inventario_forestal ORDER BY nombre_comun";
$result = pg_query($conn, $query);

$especies = [];

while ($row = pg_fetch_assoc($result)) {
    $especies[] = $row['nombre_comun'];
}

header('Content-Type: application/json');
echo json_encode($especies);
