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
    $idProveedor = $_GET['id'];
    
    $query =  "DELETE FROM Proveedores WHERE idProveedor = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idProveedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<p>Proveedor eliminado con éxito.</p>";
    } else {
        echo "<p>Error al eliminar el Proveedor.</p>";
    }
} else {
    echo "<p>ID de Proveedor no especificado.</p>";
}

echo '<a href="ListaProvedores.php">Volver a la lista de Proveedores</a>';
?>
