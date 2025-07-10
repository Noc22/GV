<?php
$conn = pg_connect("host=localhost port=5432 dbname=Censo_arb user=nicolasotalora password=user");

$sql = "SELECT gid, estructura_id, tipo_area, ST_AsGeoJSON(the_geom) AS geometry FROM estructuras";
$result = pg_query($conn, $sql);

$features = [];

while ($row = pg_fetch_assoc($result)) {
  $features[] = [
    "type" => "Feature",
    "geometry" => json_decode($row['geometry']),
    "properties" => [
      "gid" => $row['gid'],
      "estructura_id" => $row['estructura_id'],
      "tipo_area" => $row['tipo_area'] // ← esto es necesario
    ]
  ];
}

echo json_encode([
  "type" => "FeatureCollection",
  "features" => $features
]);
?>
