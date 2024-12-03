<?php
session_start();

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

// Verificar si se recibió un ID de empleado
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idEmpleado = $_GET['id'];

    // Obtener los datos del empleado
    $query = "SELECT * FROM Empleados WHERE idEmpleado = :idEmpleado";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idEmpleado', $idEmpleado);
    $stmt->execute();
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        die("Empleado no encontrado.");
    }
} else {
    die("ID de empleado no válido.");
}

// Obtener la sucursal de la sesión
$idSucursal = isset($_SESSION['idSucursal']) ? $_SESSION['idSucursal'] : null;
if (!$idSucursal) {
    die("No se encontró una sucursal en la sesión.");
}

// Actualizar datos del empleado si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $apellido_p = strtoupper($_POST['apellido_p']);
    $apellido_m = strtoupper($_POST['apellido_m']);
    $telefono = $_POST['telefono'];
    $curp = strtoupper($_POST['curp']);
    $rfc = strtoupper($_POST['rfc']);
    $idPuesto = $_POST['idPuesto'];

    // Actualizamos el empleado, pero mantenemos la sucursal de la sesión
    $query = "UPDATE Empleados 
              SET Nombre = :nombre, ApellidoP = :apellido_p, ApellidoM = :apellido_m, 
                  Telefono = :telefono, CURP = :curp, RFC = :rfc, idPuesto = :idPuesto, idSucursal = :idSucursal 
              WHERE idEmpleado = :idEmpleado";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido_p', $apellido_p);
    $stmt->bindParam(':apellido_m', $apellido_m);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':curp', $curp);
    $stmt->bindParam(':rfc', $rfc);
    $stmt->bindParam(':idPuesto', $idPuesto);
    $stmt->bindParam(':idSucursal', $idSucursal); // Usar la sucursal de la sesión
    $stmt->bindParam(':idEmpleado', $idEmpleado);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: ObtenerEmpleados.php?mensaje=Empleado actualizado con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: ObtenerEmpleados.php?mensaje=Error al actualizar el empleado.");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="stylesheet" type="text/css" href="../styleEditar.css">
</head>
<body>
    <h1>Editar Empleado</h1>
    <div class="mover">
    <form method="POST">
    <div class="form-container">
    <div>
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['Nombre']); ?>" required>

        <label for="apellido_p">Apellido Paterno:</label>
        <input type="text" id="apellido_p" name="apellido_p" value="<?php echo htmlspecialchars($empleado['ApellidoP']); ?>" required>

        <label for="apellido_m">Apellido Materno:</label>
        <input type="text" id="apellido_m" name="apellido_m" value="<?php echo htmlspecialchars($empleado['ApellidoM']); ?>">

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empleado['Telefono']); ?>" required>

        <button class="act" type="submit">Actualizar</button>
    </div>
    <div>
        <label for="curp">CURP:</label>
        <input type="text" id="curp" name="curp" value="<?php echo htmlspecialchars($empleado['CURP']); ?>" required>

        <label for="rfc">RFC:</label>
        <input type="text" id="rfc" name="rfc" value="<?php echo htmlspecialchars($empleado['RFC']); ?>" required>

        <label for="puesto">Puesto:</label>
        <select id="puesto" name="idPuesto" required>
            <!-- Opciones de puestos -->
            <?php
            $puestos = $pdo->query("SELECT idPuesto, Puesto FROM Puesto")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($puestos as $puesto) {
                $selected = $puesto['idPuesto'] == $empleado['idPuesto'] ? 'selected' : '';
                echo "<option value='{$puesto['idPuesto']}' $selected>{$puesto['Puesto']}</option>";
            }
            ?>
        </select>

        <label for="sucursal">Sucursal:</label>
        <!-- Mostrar la sucursal de la sesión, no editable -->
        <input type="text" id="sucursal" name="idSucursal" value="<?php echo htmlspecialchars($idSucursal); ?>" readonly required>

        <button class="exit-button" type="button" onclick="window.location.href='ObtenerEmpleados.php';">Salir sin Guardar</button>
    </div></div>
    </form>
    </div>
</body>
</html>
