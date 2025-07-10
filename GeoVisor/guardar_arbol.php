<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión
$conn = pg_connect("host=localhost port=5432 dbname=Censo_arb user=nicolasotalora password=user");
if (!$conn) {
    die("Error de conexión a la base de datos.");
}
// --- AJUSTAR SECUENCIA DE ID AUTOMÁTICAMENTE ---
$ajustar_sql = "SELECT setval('inventario_forestal_id_seq', (SELECT MAX(arbol_id) FROM inventario_forestal))";
pg_query($conn, $ajustar_sql);
// Recolección de datos del formulario
$campos = [
    'familia', 'genero', 'especie', 'nombre_comun',
    'cap1_cm','alt_fuste', 'alt_total',
    'diametro_copa', 'estado_fitosanitario','densidad',
    'observaciones', 'coord_n', 'coord_w'
];
// Validar campos requeridos
foreach (['nombre_comun', 'coord_n', 'coord_w'] as $requerido) {
    if (empty($_POST[$requerido])) {
        die("Falta el campo obligatorio: $requerido");
    }
}
// Extraer valores en el mismo orden
$valores = [];
foreach ($campos as $campo) {
    $valores[$campo] = $_POST[$campo] ?? null;
}
// Convertir a geom
$lat = floatval($valores['coord_n']);
$lng = floatval($valores['coord_w']);
$geom = "ST_SetSRID(ST_MakePoint($lng, $lat), 4326)";
// Construir consulta SQL
$sql = "
    INSERT INTO inventario_forestal (
        familia, genero, especie, nombre_comun,
        cap1_cm, alt_fuste, alt_total,
        diametro_copa, estado_fitosanitario,
        densidad, observaciones, geom
    )
    VALUES (
        $1, $2, $3, $4,
        $5, $6, $7, $8,
        $9, $10, $11,
        $12
    )
";
// Ejecutar
$params = array_values($valores);
$params[19] = $geom; // sobreescribe la última con ST_Geom
// Ejecutar con query extendido (no puede parametrizar geom, así que insertamos como texto)
$sql_final = "
    INSERT INTO inventario_forestal (
        familia, genero, especie, nombre_comun,
        cap1_cm, alt_fuste, alt_total,
        diametro_copa, estado_fitosanitario,
        densidad, observaciones, geom
    )
    VALUES (
        $1, $2, $3, $4,
        $5, $6, $7, $8,
        $9, $10, $11, $geom
    )
";
$sql_final = str_replace('$geom', $geom, $sql_final);
$result = pg_query_params($conn, $sql_final, array_slice($params, 0, 11));
if ($result) {
    echo "Árbol registrado correctamente. <a href='formulario_arbol.html'>Ingresar otro</a>";
} else {
    echo "Error al registrar: " . pg_last_error($conn);
}
?>
