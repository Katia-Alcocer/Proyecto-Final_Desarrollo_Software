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

    $query = "SELECT * FROM Ofertas WHERE idOferta = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idOferta, PDO::PARAM_INT);
    $stmt->execute();
    $oferta = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oferta) {
        die("Oferta no encontrada.");
    }
} else {
    die("ID de Oferta no especificado.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fechaInicio = htmlspecialchars(trim($_POST['fecha_inicio']));
    $fechaFin = htmlspecialchars(trim($_POST['fecha_fin']));
    $porcentajeDescuento = htmlspecialchars(trim($_POST['porcentaje_descuento']));

    try {
        $query = "UPDATE Ofertas 
                  SET FechaInicio = :fechaInicio, FechaFin = :fechaFin, PorcentajeDescuento = :porcentajeDescuento
                  WHERE idOferta = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->bindParam(':porcentajeDescuento', $porcentajeDescuento);
        $stmt->bindParam(':id', $idOferta, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: MostraOfertasComiciones.php?mensaje=Oferta actualizada exitosamente");
            exit();
        } else {
            echo "<p>Error al actualizar la oferta.</p>";
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
    <title>Editar Oferta</title>
    <link rel="stylesheet" type="text/css" href="styleEditar.css">
</head>
<body>
    <h1>Editar Oferta</h1>
    <form method="POST">
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" value="<?php echo htmlspecialchars($oferta['FechaInicio']); ?>" required>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" name="fecha_fin" value="<?php echo htmlspecialchars($oferta['FechaFin']); ?>" required>

        <label for="porcentaje_descuento">Porcentaje de Descuento (%):</label>
        <input type="number" step="0.01" name="porcentaje_descuento" value="<?php echo htmlspecialchars($oferta['PorcentajeDescuento']); ?>" required>
        
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
