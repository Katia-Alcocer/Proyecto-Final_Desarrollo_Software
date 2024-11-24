<?php
// Conexión a la base de datos
$pdo = new PDO("mysql:host=db;dbname=FARMACIA;charset=utf8", 'user', 'user_password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleAgregar.css">
</head>
<body>
    <h1>Agregar Nueva Sucursal</h1>
    <form id="employee-form" action="ListaSucursales.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="Nombre" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="Telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="Direccion">

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="Usuario">

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="Clave" required>

        <button type="submit">Agregar Sucursal</button>
        <button class="exit-button" onclick="window.location.href='pagina_admin.html';">Salir</button>
    </form>
</body>
</html>
