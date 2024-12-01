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
$queryCheck = "SELECT COUNT(*) FROM Ofertas WHERE idMedicamento = :idMedicamento AND FechaFin >= CURDATE()";
$stmtCheck = $pdo->prepare($queryCheck);
$stmtCheck->bindParam(':idMedicamento', $idMedicamento);
$stmtCheck->execute();
$ofertaExistente = $stmtCheck->fetchColumn();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($ofertaExistente > 0) {
        echo "<p> Este medicamento ya tiene una oferta activa.</p>";
    } else {
        $fechaFin = $_POST['fechaFin'];
        $porcentajeDescuento = $_POST['porcentajeDescuento'];
        $fechaInicio = date('Y-m-d');

        if (empty($fechaFin) || empty($porcentajeDescuento) || !is_numeric($porcentajeDescuento) || $porcentajeDescuento <= 0 || $porcentajeDescuento > 100) {
            echo "<p>Error: Por favor ingrese una fecha de fin válida y un porcentaje de descuento entre 1 y 100.</p>";
        } else {
            $queryInsert = "INSERT INTO Ofertas (idMedicamento, FechaInicio, FechaFin, PorcentajeDescuento) VALUES (:idMedicamento, :fechaInicio, :fechaFin, :porcentajeDescuento)";
            $stmtInsert = $pdo->prepare($queryInsert);
            $stmtInsert->bindParam(':idMedicamento', $idMedicamento);
            $stmtInsert->bindParam(':fechaInicio', $fechaInicio);
            $stmtInsert->bindParam(':fechaFin', $fechaFin);
            $stmtInsert->bindParam(':porcentajeDescuento', $porcentajeDescuento);

            if ($stmtInsert->execute()) {
                header("Location: ChecarCaducidades.php?mensaje=Oferta%20agregada%20exitosamente");
                exit;
            } else {
                echo "<p>Error al agregar la oferta.</p>";
            }
        }
    }
}
?>
<?php
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
    <title>Agregar Oferta</title>
    <link rel="stylesheet" href="styleEditar.css">
    <style>
        .mensaje-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 10px 0;
            font-size: 16px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <h1>Agregar Oferta al Medicamento</h1>
    
    <form method="POST" action="">
        <label for="nombre">Medicamento:</label>
        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" readonly><br>

        <label for="fechaFin">Fecha de Fin:</label>
        <input type="date" name="fechaFin" id="fechaFin" required><br>

        <label for="porcentajeDescuento">Porcentaje de Descuento (%):</label>
        <input type="number" name="porcentajeDescuento" id="porcentajeDescuento" min="1" max="100" required><br>

        <button class="act" type="submit">Agregar Oferta</button>
        <button type="button" class="exit-button" onclick="window.location.href='ChecarCaducidades.php';">Cancelar</button>
    </form>
</body>
</html>
