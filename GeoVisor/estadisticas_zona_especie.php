<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
// Conexión a PostgreSQL
$conn = pg_connect("host=localhost port=5432 dbname=Censo_arb user=nicolasotalora password=user");

if (!$conn) {
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit;
}

// Estadísticas: cantidad de árboles por estructura y especie
$sql = "
    SELECT 
        zona.estructura AS estructura,
        arbol.nombre_comun AS especie,
        COUNT(*) AS cantidad
    FROM 
        estructuras AS zona
    JOIN 
        inventario_forestal AS arbol
    ON 
        ST_Within(arbol.geom, zona.the_geom)
    GROUP BY 
        zona.estructura, arbol.nombre_comun
    ORDER BY 
        zona.estructura, cantidad DESC
";

$result = pg_query($conn, $sql);

if (!$result) {
    echo json_encode(["error" => "Error en la consulta SQL"]);
    exit;
}

$data = [];

while ($row = pg_fetch_assoc($result)) {
    $data[] = [
        "estructura" => $row['estructura'],
        "especie" => $row['especie'],
        "cantidad" => (int)$row['cantidad']
    ];
}

echo json_encode($data);
?>
