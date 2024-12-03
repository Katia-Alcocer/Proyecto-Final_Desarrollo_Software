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
    $idOferta = intval($_GET['id']); 

    try {
        $query = "DELETE FROM Ofertas WHERE idOferta = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $idOferta, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: MostraOfertasComiciones.php?mensaje=Oferta eliminada exitosamente");
            exit();
        } else {
            echo "<p>Error al eliminar la oferta.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
} else {
    die("ID de Oferta no especificado.");
}
?>
