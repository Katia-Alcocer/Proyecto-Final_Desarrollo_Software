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

if (isset($_GET['id'])) {
    $idSucursal = $_GET['id'];
    
    $query = "DELETE FROM Sucursales WHERE idSucursal = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idSucursal, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ListaSucursales.php?mensaje=Sucursal eliminada con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ListaSucursales.php?mensaje=Error al eliminar Sucursal.");
        exit;
    }
} else {
    header("Location: ListaSucursales.php?mensaje=ID de Sucursal no especificado.");
        exit;
}

?>
