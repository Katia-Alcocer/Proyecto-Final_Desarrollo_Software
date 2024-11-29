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
    $idProveedor = $_GET['id'];
    
    // Cambiar el estatus a "Inactivo" en lugar de eliminar
    $query = "UPDATE Proveedores SET estatus = 'Inactivo' WHERE idProveedor = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idProveedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ListaProvedores.php?mensaje=Proveedor eliminado.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ListaProvedores.php?mensaje=Error al eliminar proveedor.");
        exit;
    }
} else {
    header("Location: ListaProvedores.php?mensaje=ID de proveedor no válido o no especificado.");
    exit;
}
?>

