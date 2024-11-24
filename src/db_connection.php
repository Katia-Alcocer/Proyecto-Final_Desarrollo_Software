<?php
$host = "db"; // Nombre del servicio MySQL en docker-compose.yml
$username = "root"; // Usuario de MySQL definido en docker-compose.yml
$password = "clave"; // Contraseña de MySQL definida en docker-compose.yml
$dbname = "FARMACIA"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($host, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>
