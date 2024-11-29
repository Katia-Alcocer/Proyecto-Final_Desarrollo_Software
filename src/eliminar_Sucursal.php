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

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idSucursal = $_GET['id'];
    
    // Cambiar el estatus a "Inactivo" en lugar de eliminar
    $query = "UPDATE Sucursales SET estatus = 'Inactivo' WHERE idSucursal = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idSucursal, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ListaSucursales.php?mensaje=Sucursal Eliminada.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ListaSucursales.php?mensaje=Error al eliminar la sucursal.");
        exit;
    }
} else {
    header("Location: ListaSucursales.php?mensaje=ID de Sucursal no válido o no especificado.");
    exit;
}
?>
