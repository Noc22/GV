<?php
session_start();
header("Content-Type: application/json");

$conn = pg_connect("host=localhost dbname=Censo_arb user=nicolasotalora password=user");
if (!$conn) {
  echo json_encode(["error" => "Error de conexión"]);
  exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

  // Login de usuario (admin o invitado)
  case 'login':
    $correo = $_POST['correo'] ?? '';
    $clave = $_POST['clave'] ?? '';

    if (!$correo || !$clave) {
      echo json_encode(["error" => "Correo y contraseña requeridos"]);
      exit;
    }

    $sql = "SELECT * FROM usuarios WHERE correo = $1";
    $result = pg_query_params($conn, $sql, [$correo]);
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($clave, $user['clave'])) {
      $_SESSION['usuario'] = $user['nombre'];
      $_SESSION['rol'] = $user['rol'];
      echo json_encode(["mensaje" => "Login exitoso", "rol" => $user['rol'], "usuario" => $user['nombre']]);
    } else {
      echo json_encode(["error" => "Credenciales inválidas"]);
    }
    break;
    // Cerrar sesión
    case 'logout':
      session_destroy();
      echo json_encode(["mensaje" => "Sesión cerrada"]);
      break;

  // Obtener rol del usuario logueado
    case 'rol':
        echo json_encode(["rol" => $_SESSION['rol'] ?? null]);
        break;

  default:
    echo json_encode(["error" => "Acción inválida"]);
}
?>