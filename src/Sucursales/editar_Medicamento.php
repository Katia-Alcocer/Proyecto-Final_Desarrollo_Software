<?php
session_start(); // Iniciar sesión

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

// Verificar si se recibió un ID de medicamento
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMedicamento = $_GET['id'];

    // Obtener los datos del medicamento
    $query = "SELECT * FROM Medicamento WHERE idMedicamento = :idMedicamento";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idMedicamento', $idMedicamento);
    $stmt->execute();
    $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medicamento) {
        die("Medicamento no encontrado.");
    }
} else {
    die("ID de medicamento no válido.");
}

// Obtener el nombre de la sucursal desde la sesión (esto asume que la sucursal está guardada en la sesión)
$idSucursal = $_SESSION['idSucursal']; // Asegúrate de que el idSucursal esté guardado en la sesión

// Actualizar datos del medicamento si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $cantidad = $_POST['cantidad'];
    $precio_c = $_POST['precio_c'];
    $precio_v = $_POST['precio_v'];
    $descripcion = strtoupper($_POST['descripcion']);
    $idClasificacion = $_POST['idClasificacion'];
    $idEliminacion = $_POST['idEliminacion'];
    $idProveedor = $_POST['idProveedor'];
    $fechaCaducidad = $_POST['fechaCaducidad'];

    // No permitir modificar la sucursal
    $query = "UPDATE Medicamento 
              SET Nombre = :nombre, Cantidad = :cantidad, PrecioCompra = :precio_c, 
                  PrecioVenta = :precio_v, Descripcion = :descripcion, 
                  idClasificacion = :idClasificacion, idEliminacion = :idEliminacion, idProveedor = :idProveedor, fechaCaducidad = :fechaCaducidad
              WHERE idMedicamento = :idMedicamento AND idSucursal = :idSucursal"; // Aseguramos que no cambie la sucursal

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_c', $precio_c);
    $stmt->bindParam(':precio_v', $precio_v);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':idClasificacion', $idClasificacion);
    $stmt->bindParam(':idEliminacion', $idEliminacion);
    $stmt->bindParam(':idProveedor', $idProveedor);
    $stmt->bindParam(':idMedicamento', $idMedicamento);
    $stmt->bindParam(':fechaCaducidad', $fechaCaducidad);
    $stmt->bindParam(':idSucursal', $idSucursal);

    if ($stmt->execute()) {
        // Redirigir a la lista de medicamentos después de la actualización
        header("Location:ListaMedicamentos.php");
        exit;
    } else {
        echo "<p>Error al actualizar el medicamento.</p>";
    }
    
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <link rel="stylesheet" type="text/css" href="../styleEditar.css">
</head>
<body>
   
    <h1>Editar Medicamento</h1>
    <form method="POST">
        <div class="form-container">
            <div>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" required>

                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($medicamento['Cantidad']); ?>" required>

                <label for="precio_c">Precio Compra:</label>
                <input type="number" step="0.01" id="precio_c" name="precio_c" value="<?php echo htmlspecialchars($medicamento['PrecioCompra']); ?>" required>

                <label for="precio_v">Precio Venta:</label>
                <input type="number" step="0.01" id="precio_v" name="precio_v" value="<?php echo htmlspecialchars($medicamento['PrecioVenta']); ?>" required>
               
                <label for="fecha_caducidad">Fecha de Caducidad:</label>
                <input type="date" id="fecha_caducidad" name="fecha_caducidad" value="<?php echo htmlspecialchars($medicamento['fechaCaducidad']); ?>" required>
                
                <button class="act" type="submit">Actualizar</button>
            </div>

            <div>
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($medicamento['Descripcion']); ?>" required>
                
                <label for="clasificacion">Clasificación:</label>
                <select id="clasificacion" name="idClasificacion" required>
                    <?php
                    $clasificacion = $pdo->query("SELECT idClasificacion, Tipo FROM ClasificacionM")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($clasificacion as $clas) {
                        $selected = $clas['idClasificacion'] == $medicamento['idClasificacion'] ? 'selected' : '';
                        echo "<option value='{$clas['idClasificacion']}' $selected>{$clas['Tipo']}</option>";
                    }
                    ?>
                </select>

                <label for="eliminacion">En caso de no venderse:</label>
                <select id="eliminacion" name="idEliminacion" required>
                    <?php
                    $eliminacion = $pdo->query("SELECT idEliminacion, MedRegresable FROM EliminacionMedicamento")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($eliminacion as $eli) {
                        $selected = $eli['idEliminacion'] == $medicamento['idEliminacion'] ? 'selected' : '';
                        echo "<option value='{$eli['idEliminacion']}' $selected>{$eli['MedRegresable']}</option>";
                    }
                    ?>
                </select>

                <label for="provedor">Proveedor:</label>
                <select id="provedor" name="idProveedor" required>
                    <?php
                    $provedores = $pdo->query("SELECT idProveedor, Nombre FROM Proveedores")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($provedores as $provedor) {
                        $selected = $provedor['idProveedor'] == $medicamento['idProveedor'] ? 'selected' : '';
                        echo "<option value='{$provedor['idProveedor']}' $selected>{$provedor['Nombre']}</option>";
                    }
                    ?>
                </select>

                <!-- Aquí no incluimos el campo de sucursal porque no se debe modificar -->
                <input type="hidden" name="idSucursal" value="<?php echo $idSucursal; ?>">

                <button class="exit-button" type="button" onclick="window.location.href='ListaMedicamentos.php';">Salir sin Guardar</button>
            </div>
        </div>

    </form>
</body>
</html>
