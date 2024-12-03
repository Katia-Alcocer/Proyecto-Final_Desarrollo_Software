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

// Consulta para obtener todas las ofertas disponibles
$queryOfertas = "SELECT o.idOferta, m.Nombre AS Medicamento, o.FechaInicio, o.FechaFin, o.PorcentajeDescuento 
                 FROM Ofertas o
                 INNER JOIN Medicamento m ON o.idMedicamento = m.idMedicamento
                 WHERE o.FechaFin >= CURDATE()";
$stmtOfertas = $pdo->prepare($queryOfertas);
$stmtOfertas->execute();
$ofertas = $stmtOfertas->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener todas las comisiones
$queryComisiones = "SELECT c.idComision, m.Nombre AS Medicamento, c.porcentaje_comision 
                    FROM Comisiones c
                    INNER JOIN Medicamento m ON c.idMedicamento = m.idMedicamento";
$stmtComisiones = $pdo->prepare($queryComisiones);
$stmtComisiones->execute();
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
   
    <link rel="stylesheet" type="text/css" href="styleListas.css">
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
                <th>Editar</th>
                <th>Eliminar</th>
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
                        <?php  echo "<td>

                            <span>
                                 <a href='editar_Oferta.php?id=" . $oferta['idOferta'] . "'>
                                     <img src='imagenes/Editar.png' alt='Editar'>
                                 </a>
                            </span>
                                </td>"; ?>
                            <?php  echo "<td>
                            <span>
                                <a href='eliminar_Oferta.php?id=" . $oferta['idOferta']. "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta Oferta?');\">
                                 <img src='imagenes/Eliminar.png' alt='Eliminar'>
                                </a>
                             </span>
                                </td>"; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No hay ofertas disponibles.</td>
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
                <th>Editar</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($comisiones) > 0): ?>
                <?php foreach ($comisiones as $comision): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comision['idComision']); ?></td>
                        <td><?php echo htmlspecialchars($comision['Medicamento']); ?></td>
                        <td><?php echo htmlspecialchars($comision['porcentaje_comision']); ?>%</td>
                        <?php  echo "<td>

                            <span>
                                 <a href='editar_Comicion.php?id=" . $comision['idComision'] . "'>
                                     <img src='imagenes/Editar.png' alt='Editar'>
                                 </a>
                            </span>
                                </td>"; ?>
                            <?php  echo "<td>
                            <span>
                                <a href='eliminar_Comicion.php?id=" . $comision['idComision']. "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta Comicion?');\">
                                 <img src='imagenes/Eliminar.png' alt='Eliminar'>
                                </a>
                             </span>
                                </td>"; ?>
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No hay comisiones registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <button onclick="window.location.href='pagina_admin.html';">Volver</button>
</body>
</html>
