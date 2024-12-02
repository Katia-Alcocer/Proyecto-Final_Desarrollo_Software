<?php
$host = 'db';
$dbname = 'FARMACIA';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . htmlspecialchars($e->getMessage()));
}

if (!isset($_GET['id'])) {
    die("ID de medicamento no especificado.");
}

$idMedicamento = (int)$_GET['id'];

// Obtener el nombre del medicamento
$query = "SELECT Nombre FROM Medicamento WHERE idMedicamento = :idMedicamento";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':idMedicamento', $idMedicamento);
$stmt->execute();
$medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medicamento) {
    die("Medicamento no encontrado.");
}

// Verificar si ya existe una oferta activa para este medicamento
$queryCheck = "SELECT COUNT(*) FROM Comisiones WHERE idMedicamento = :idMedicamento";
$stmtCheck = $pdo->prepare($queryCheck);
$stmtCheck->bindParam(':idMedicamento', $idMedicamento);
$stmtCheck->execute();
$comisionExistente = $stmtCheck->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($comisionExistente > 0) {
        //echo "<p style='color:red;'>Este medicamento ya tiene una comisión activa.</p>";
        header("Location: ChecarCaducidades.php?mensaje=" . urlencode("Este medicamento ya tiene una comisión activa") . "&tipo=error");

                exit;
    } else {
        $porcentajeDescuento = filter_input(INPUT_POST, 'porcentajeDescuento', FILTER_VALIDATE_FLOAT);

        if ($porcentajeDescuento === false || $porcentajeDescuento <= 0 || $porcentajeDescuento > 100) {
            echo "<p style='color:red;'>Error: Por favor ingrese un porcentaje de descuento válido entre 1 y 100.</p>";
        } else {
            $queryInsert = "INSERT INTO Comisiones (idMedicamento, porcentaje_comision) VALUES (:idMedicamento, :porcentajeDescuento)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->bindParam(':idMedicamento', $idMedicamento);
            $stmtInsert->bindParam(':porcentajeDescuento', $porcentajeDescuento);
        
            if ($stmtInsert->execute()) {
                header("Location: ChecarCaducidades.php?mensaje=" . urlencode("Oferta agregada exitosamente"));
                exit;
            } else {
                echo "<p style='color:red;'>Error al agregar la oferta.</p>";
            }
        }
        
    }
}

// Verificar si hay un mensaje en la URL
if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Comisión</title>
    <link rel="stylesheet" href="styleEditar.css">
</head>
<body>
    <h1>Agregar Comisión al Medicamento</h1>
    
    <form method="POST">
        <label for="nombre">Medicamento:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" readonly><br>

        <label for="porcentajeDescuento">Porcentaje de Comisión (%):</label>
        <input type="number" name="porcentajeDescuento" id="porcentajeDescuento" min="1" max="100" required><br>

        <button class="act" type="submit">Agregar Comisión</button>
        <button type="button" class="exit-button" onclick="window.location.href='ChecarCaducidades.php';">Cancelar</button>
    </form>
</body>
</html>

