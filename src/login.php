<?php
session_start();

$host = 'db';
$dbname = 'FARMACIA';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

$errorMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = "Correo electrónico inválido.";
    } else {
        try {
            // Consulta SQL para verificar el usuario
            $sql = "SELECT idSucursal, Usuario, Clave FROM Sucursales WHERE Usuario = :email AND Clave = :password";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Guardar información del usuario en la sesión
                $_SESSION['user_id'] = $user['idSucursal'];

                // Redirigir según el idSucursal
                if ($user['idSucursal'] == 1) {
                    header("Location: pagina_admin.html"); // Página de administrador
                } else {
                    header("Location: pagina_sucursar.html"); // Página de sucursal
                }
                exit();
            } else {
                $errorMsg = "Usuario o contraseña incorrectos.";
            }
        } catch (Exception $e) {
            error_log("Error en inicio de sesión: " . $e->getMessage());
            $errorMsg = "Hubo un problema al procesar la solicitud. Intenta de nuevo.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Pildora</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleLogin.css">
</head>
<body>
  <div class="login">
    <h2>Iniciar Sesión</h2>
    <img src="imagenes/Login1.jpg" alt="Ícono de inicio de sesión">
    <form id="loginForm" method="POST" action="login.php">
        <label for="email">Usuario:</label>
        <input type="email" id="email" name="email" required>
        
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Ingresar</button>
    </form>

  </div>
</body>
</html>
