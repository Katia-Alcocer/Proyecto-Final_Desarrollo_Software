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
        echo "<p>Sucursal eliminada con éxito.</p>";
    } else {
        echo "<p>Error al eliminar la Sucursal.</p>";
    }
} else {
    echo "<p>ID de Sucursal no especificado.</p>";
}

echo '<a href="ListaSucursales.php">Volver a la lista de sucursales</a>';
?>
