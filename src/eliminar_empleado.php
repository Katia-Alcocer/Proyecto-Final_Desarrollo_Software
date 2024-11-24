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
        // Redirigir con mensaje de éxito
        header("Location: Listaempleados.php?mensaje=Empleado eliminado con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: Listaempleados.php?mensaje=Error al eliminar el empleado.");
        exit;
    }
} else {
    // Redirigir con mensaje de error si el ID no es válido
    header("Location: Listaempleados.php?mensaje=ID de empleado no válido.");
    exit;
}
?>