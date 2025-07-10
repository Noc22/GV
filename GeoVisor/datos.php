<?php
// Conexión a la base de datos
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");
if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "No se pudo conectar a la base de datos"]);
    exit;
}

// Verifica si se está solicitando filtro por estructura
$estructura = $_GET['estructura'] ?? null;

if ($estructura) {
    // Consulta árboles dentro de una estructura específica
    $sql = "
        SELECT 
            arbol.arbol_id, arbol.nombre_comun, arbol.familia, arbol.especie,
            arbol.dap1_m, arbol.alt_total, arbol.estado_fitosanitario,
            arbol.vol1_total_m3, arbol.biomasa_total_kg, arbol.carbono_kg,
            ST_AsGeoJSON(arbol.geom) AS geometry
        FROM inventario_forestal AS arbol
        JOIN estructuras AS zona ON ST_Within(arbol.geom, zona.geom)
        WHERE zona.nombre = $1
    ";
    $result = pg_query_params($conn, $sql, [$estructura]);

} else {
    // Consulta todos los árboles
    $sql = "
        SELECT 
            arbol_id, nombre_comun, familia, especie,
            dap1_m, alt_total, estado_fitosanitario,
            vol1_total_m3, biomasa_total_kg, carbono_kg,
            ST_AsGeoJSON(geom) AS geometry
        FROM inventario_forestal
    ";
    $result = pg_query($conn, $sql);
}

// Validación de la consulta
if (!$result) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Error en la consulta SQL"]);
    exit;
}

// Convertir resultados a formato GeoJSON
$features = [];

while ($row = pg_fetch_assoc($result)) {
    $features[] = [
        "type" => "Feature",
        "geometry" => json_decode($row['geometry']),
        "properties" => [
            "arbol_id" => $row['arbol_id'],
            "nombre_comun" => $row['nombre_comun'],
            "familia" => $row['familia'],
            "especie" => $row['especie'],
            "dap1_m" => $row['dap1_m'],
            "alt_total" => $row['alt_total'],
            "estado_fitosanitario" => $row['estado_fitosanitario'],
            "vol1_total_m3" => $row['vol1_total_m3'],
            "biomasa_total_kg" => $row['biomasa_total_kg'],
            "carbono_kg" => $row['carbono_kg']
        ]
    ];
}

// Construcción del GeoJSON final
$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];

// Respuesta JSON
header('Content-Type: application/json');
echo json_encode($geojson);
?>
