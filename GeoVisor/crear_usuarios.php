<?php
$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");

$clave_admin = password_hash("user", PASSWORD_DEFAULT);
$clave_invitado = password_hash("user2", PASSWORD_DEFAULT);

// Limpia anteriores si existen
pg_query($conn, "DELETE FROM usuarios WHERE correo IN ('admin@correo.com', 'invitado@correo.com')");

pg_query_params($conn,
  "INSERT INTO usuarios (nombre, correo, clave, rol) VALUES ($1, $2, $3, $4)",
  ['Administrador', 'admin@correo.com', $clave_admin, 'admin']
);

pg_query_params($conn,
  "INSERT INTO usuarios (nombre, correo, clave, rol) VALUES ($1, $2, $3, $4)",
  ['Invitado', 'invitado@correo.com', $clave_invitado, 'invitado']
);

echo "Usuarios creados con éxito.";
