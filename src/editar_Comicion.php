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

    $query = "SELECT * FROM Comisiones WHERE idComision = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idComision, PDO::PARAM_INT);
    $stmt->execute();
    $comision = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$comision) {
        die("Comisión no encontrada.");
    }
} else {
    die("ID de Comisión no especificado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $porcentaje_comision = htmlspecialchars(trim($_POST['porcentaje_comision']));

    try {
        $query = "UPDATE Comisiones 
                  SET porcentaje_comision = :porcentaje_comision 
                  WHERE idComision = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':porcentaje_comision', $porcentaje_comision);
        $stmt->bindParam(':id', $idComision, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: MostraOfertasComiciones.php?mensaje=Comisión actualizada exitosamente");
            exit();
        } else {
            echo "<p>Error al actualizar la comisión.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Comisión</title>
    <link rel="stylesheet" type="text/css" href="styleEditar.css">
</head>
<body>
    <h1>Editar Comisión</h1>
    <form method="POST">
        <label for="porcentaje_comision">Porcentaje de Comisión (%):</label>
        <input type="number" step="0.01" name="porcentaje_comision" value="<?php echo htmlspecialchars($comision['porcentaje_comision']); ?>" required>
        <div class="form-container">
        <div>
        <button class="act" type="submit">Guardar Cambios</button>
        </div>
        <div>
        <button class="exit-button" type="button" onclick="window.location.href='MostraOfertasComiciones.php'">Salir sin Guardar</button>
        </div></div>
    </form>
</body>
</html>
