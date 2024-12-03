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

// Iniciar sesión
session_start();

// Verificar si hay una sucursal guardada en la sesión
if (!isset($_SESSION['idSucursal'])) {
    die("No se ha definido la sucursal en la sesión.");
}

$idSucursalSesion = $_SESSION['idSucursal']; // ID de la sucursal guardada en la sesión

// Consulta para obtener todas las ofertas disponibles para la sucursal de la sesión
$queryOfertas = "SELECT o.idOferta, m.Nombre AS Medicamento, o.FechaInicio, o.FechaFin, o.PorcentajeDescuento 
                 FROM Ofertas o
                 INNER JOIN Medicamento m ON o.idMedicamento = m.idMedicamento
                 WHERE o.FechaFin >= CURDATE() AND m.idSucursal = ?";
$stmtOfertas = $pdo->prepare($queryOfertas);
$stmtOfertas->execute([$idSucursalSesion]);
$ofertas = $stmtOfertas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener todas las comisiones para la sucursal de la sesión
$queryComisiones = "SELECT c.idComision, m.Nombre AS Medicamento, c.porcentaje_comision 
                    FROM Comisiones c
                    INNER JOIN Medicamento m ON c.idMedicamento = m.idMedicamento
                    WHERE m.idSucursal = ?";
$stmtComisiones = $pdo->prepare($queryComisiones);
$stmtComisiones->execute([$idSucursalSesion]);
$comisiones = $stmtComisiones->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Ofertas y Comisiones</title>
    <link rel="stylesheet" type="text/css" href="../styleListas.css">
</head>
<body>
    <h1>Ofertas y Comisiones</h1>

    <!-- Tabla de Ofertas -->
    <h2>Ofertas Disponibles</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID Oferta</th>
                <th>Medicamento</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Porcentaje Descuento</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if (count($ofertas) > 0): ?>
                <?php foreach ($ofertas as $oferta): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($oferta['idOferta']); ?></td>
                        <td><?php echo htmlspecialchars($oferta['Medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($oferta['FechaInicio']); ?></td>
                        <td><?php echo htmlspecialchars($oferta['FechaFin']); ?></td>
                        <td><?php echo htmlspecialchars($oferta['PorcentajeDescuento']); ?>%</td>
                     
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay ofertas disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabla de Comisiones -->
    <h2>Comisiones</h2>
    <table border="1">
        <thead>
            <tr>
                <th>ID Comisión</th>
                <th>Medicamento</th>
                <th>Porcentaje Comisión</th>
                
            </tr>
        </thead>
        <tbody>
            <?php if (count($comisiones) > 0): ?>
                <?php foreach ($comisiones as $comision): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comision['idComision']); ?></td>
                        <td><?php echo htmlspecialchars($comision['Medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($comision['porcentaje_comision']); ?>%</td>
                     
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay comisiones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button onclick="window.location.href='../pagina_sucursar.html';">Volver</button>
</body>
</html>
