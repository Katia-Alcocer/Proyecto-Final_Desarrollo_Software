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

// Iniciar sesión
session_start();

// Verificar si hay una sucursal guardada en la sesión
if (!isset($_SESSION['idSucursal'])) {
    die("No se ha definido la sucursal en la sesión.");
}

$idSucursalSesion = $_SESSION['idSucursal']; // ID de la sucursal guardada en la sesión

// Función para obtener todos los medicamentos que corresponden a la sucursal de la sesión
function obtenerMedicamentos($pdo, $idSucursal) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad,
            E.MedRegresable, E.Detalle, P.Nombre AS Proveedor, M.fechaCaducidad, M.DiasRestantes, M.EstadoCaducidad
        FROM Medicamento M
        JOIN ClasificacionM C ON M.idClasificacion = C.idClasificacion
        JOIN EliminacionMedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN Proveedores P ON M.idProveedor = P.idProveedor
        WHERE M.Estatus IN ('Disponible', 'Caducado') 
        AND M.idSucursal = ?"; // Filtrar por la sucursal de la sesión
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$idSucursal]); // Pasar el ID de la sucursal a la consulta
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$medicamentos = obtenerMedicamentos($pdo, $idSucursalSesion);

?>

<?php
// Verificar si hay un mensaje en la URL
if (isset($_GET['mensaje'])) {
    $tipo = $_GET['tipo'] ?? 'info';
    $color = $tipo === 'error' ? 'red' : 'green';
    echo "<div style='color: $color;'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
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

<h1>Lista Caducidades</h1>

<!-- Tabla para mostrar los medicamentos -->
<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Clasificación</th>
            <th>Cantidad</th>
            <th>Medicación Regresable</th>
            <th>Caracteristicas</th>
            <th>Proveedor</th>
            <th>Fecha de Caducidad</th>
            <th>Días Restantes para Caducar</th>
            <th>Estatus Caducidad</th>
            <th>Eliminar</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $medicamento): ?>
            <tr>
                <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Detalle']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Proveedor']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['fechaCaducidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['DiasRestantes']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['EstadoCaducidad']); ?></td>
               
                <td>
                    <a href="eliminar_caducidad.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este medicamento?');">
                        <img src="../imagenes/Eliminar.png" alt="Eliminar">
                    </a>
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
