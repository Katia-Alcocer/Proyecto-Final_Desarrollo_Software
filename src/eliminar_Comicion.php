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
    $idComision = intval($_GET['id']); 

    try {
        $query = "DELETE FROM Comisiones WHERE idComision = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $idComision, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: MostraOfertasComiciones.php?mensaje=Comisión eliminada exitosamente");
            exit();
        } else {
            echo "<p>Error al eliminar la comisión.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
} else {
    die("ID de Comisión no especificado.");
}
?>
