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

function obtenerMedicamentosPorEstatus($pdo, $estatus) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad, M.PrecioCompra, M.PrecioVenta, 
            E.MedRegresable, P.Nombre AS Proveedor, M.Descripcion, M.Estatus
        FROM Medicamento M
        JOIN ClasificacionM C ON M.idClasificacion = C.idClasificacion
        JOIN EliminacionMedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN Proveedores P ON M.idProveedor = P.idProveedor
        WHERE M.Estatus = :estatus";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener medicamentos con estatus "Vendido"
$medicamentos = obtenerMedicamentosPorEstatus($pdo, 'Vendido');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos Vendidos</title>
    <link rel="stylesheet" type="text/css" href="styleListas.css">
</head>
<body>

<h1>Medicamentos Vendidos</h1>

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
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="window.location.href='ListaMedicamento.php';">Regresar</button>

</body>
</html>
