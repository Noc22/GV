<?php
$conn = pg_connect("host=localhost port=5432 dbname=Censo_arb user=nicolasotalora password=user");

$sql = "SELECT gid, ST_AsGeoJSON(the_geom) AS geometry FROM carreteras";
$result = pg_query($conn, $sql);

$features = [];

while ($row = pg_fetch_assoc($result)) {
    $features[] = [
        "type" => "Feature",
        "geometry" => json_decode($row['geometry']),
        "properties" => [
            "gid" => $row['gid']
        ]
    ];
}

echo json_encode([
    "type" => "FeatureCollection",
    "features" => $features
]);
?>
