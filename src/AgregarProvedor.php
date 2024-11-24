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
    <title>Registro de Proveedores</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleAgregar.css">
</head>
<body>
    <h1>Agregar Nuevo Proveedor</h1>
    <form id="employee-form" action="ListaProvedores.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="Nombre" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="Telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="Direccion">

        <button type="submit">Agregar Provedor</button>
        <button type="button" class="exit-button" onclick="window.location.href='pagina_admin.html';">Salir</button>
    </form>
</body>
</html>