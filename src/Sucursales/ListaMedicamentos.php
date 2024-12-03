<?php 
session_start(); // Iniciar sesión para obtener el idSucursal de la sesión

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

// Obtener idSucursal desde la sesión
$idSucursal = isset($_SESSION['idSucursal']) ? $_SESSION['idSucursal'] : null;
if (!$idSucursal) {
    die("No se encontró una sucursal en la sesión.");
}

// Función para insertar un medicamento
function insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus, $fechaCaducidad, $idSucursal) {
    $query = "
        INSERT INTO Medicamento (Nombre, idClasificacion, Cantidad, PrecioCompra, PrecioVenta, idEliminacion, idProveedor, Descripcion, Estatus, fechaCaducidad, idSucursal) 
        VALUES (:nombre, :idClasificacion, :cantidad, :precio_c, :precio_v, :idEliminacion, :idProveedor, :descripcion, :estatus, :fechaCaducidad, :idSucursal)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':idClasificacion', $idClasificacion);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_c', $precio_c);
    $stmt->bindParam(':precio_v', $precio_v);
    $stmt->bindParam(':idEliminacion', $idEliminacion);
    $stmt->bindParam(':idProveedor', $idProveedor);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':fechaCaducidad', $fechaCaducidad);
    $stmt->bindParam(':idSucursal', $idSucursal);

    if ($stmt->execute()) {
        echo "<p>Medicamento agregado con éxito.</p>";
    } else {
        echo "<p>Error al agregar el medicamento.</p>";
    }
}

// Función para obtener todos los medicamentos
function obtenerMedicamentosPorEstatusYSucursal($pdo, $estatus, $idSucursal) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad, M.PrecioCompra, M.PrecioVenta, 
            E.MedRegresable, P.Nombre AS Proveedor, M.Descripcion, M.Estatus
        FROM Medicamento M
        JOIN ClasificacionM C ON M.idClasificacion = C.idClasificacion
        JOIN EliminacionMedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN Proveedores P ON M.idProveedor = P.idProveedor
        WHERE M.Estatus = :estatus AND M.idSucursal = :idSucursal";
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':idSucursal', $idSucursal);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Si se envió el formulario, insertar el nuevo medicamento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $idClasificacion = $_POST['idClasificacion'];
    $cantidad = $_POST['cantidad'];
    $precio_c = $_POST['precio_c'];
    $precio_v = $_POST['precio_v'];
    $idEliminacion = $_POST['idEliminacion'];
    $idProveedor = $_POST['idProveedor'];
    $descripcion = strtoupper($_POST['descripcion']);
    $estatus = 'Disponible'; // Estatus por defecto
    $fechaCaducidad = $_POST['fechaCaducidad'];

    insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus, $fechaCaducidad, $idSucursal);
}

// Obtener medicamentos con estatus "Disponible"
$medicamentos = obtenerMedicamentosPorEstatusYSucursal($pdo, 'Disponible', $idSucursal);

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
    <title>Lista de Medicamentos</title>
    <link rel="stylesheet" type="text/css" href="../styleListas.css">
</head>
<body>

<h1>Lista de Medicamentos</h1>

<!-- Tabla para mostrar los medicamentos -->
<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Clasificación</th>
            <th>Cantidad</th>
            <th>Precio Compra</th>
            <th>Precio Venta</th>
            <th>Medicación Regresable</th>
            <th>Proveedor</th>
            <th>Descripción</th>
            <th>Estatus</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $medicamento): ?>
            <tr>
                <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioCompra']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioVenta']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Proveedor']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Descripcion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Estatus']); ?></td>
                <td>
                <span>
                    <a href="editar_medicamento.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>">
                        <img src="../imagenes/Editar.png" alt="Editar">
                    </a>
                    <a href="eliminar_medicamento.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este medicamento?');">
                        <img src="../imagenes/Eliminar.png" alt="Eliminar">
                    </a>
                </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="window.location.href='../pagina_sucursar.html';">
    Regresar
</button>

</body>
</html>
