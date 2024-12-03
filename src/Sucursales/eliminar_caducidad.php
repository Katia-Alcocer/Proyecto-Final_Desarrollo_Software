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

// Verifica si se pasó un ID por el método GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMedicamento = $_GET['id'];

    // Cambia el estatus del medicamento a "Eliminado"
    $query = "UPDATE Medicamento SET Estatus = 'Eliminado' WHERE idMedicamento = :idMedicamento";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idMedicamento', $idMedicamento, PDO::PARAM_INT);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ChecarCaducidades.php?mensaje=Medicamento eliminado con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ChecarCaducidades.php?mensaje=Error al eliminar el Medicamento.");
        exit;
    }
} else {
    // Redirige si no se pasa un ID válido
    header("Location: ChecarCaducidades.php?mensaje=ID no especificado");
    exit();
}
?>