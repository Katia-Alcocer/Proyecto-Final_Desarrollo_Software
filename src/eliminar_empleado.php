<?php
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

// Verificar si se envió el ID del empleado a eliminar
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idEmpleado = $_GET['id'];

    // Preparar la consulta para eliminar
    $query = "DELETE FROM Empleados WHERE idEmpleado = :idEmpleado";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idEmpleado', $idEmpleado);

    if ($stmt->execute()) {
        echo "<p>Empleado eliminado con éxito.</p>";
    } else {
        echo "<p>Error al eliminar el empleado.</p>";
    }
} else {
    echo "<p>ID de empleado no válido.</p>";
}
?>
<a href="Listaempleados.php">Volver a la lista de empleados</a>

