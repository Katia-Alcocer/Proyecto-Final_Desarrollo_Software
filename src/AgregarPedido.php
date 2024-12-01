<?php
// Conexión a la base de datos
$host = 'db'; 
$dbname = 'FARMACIA';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . htmlspecialchars($e->getMessage()));
}

// Procesar el formulario de pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idSucursal = $_POST['idSucursal'];
    $idProveedor = $_POST['idProveedor'];
    $fechaPedido = date('Y-m-d');
    $estado = 'Pendiente';
    $medicamentos = $_POST['medicamento'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if (empty($medicamentos) || empty($cantidades)) {
        echo "<p style='color:red;'>Debe seleccionar al menos un medicamento con cantidad válida.</p>";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insertar el pedido
            $stmt = $pdo->prepare("INSERT INTO Pedidos (idSucursal, idProveedor, FechaPedido, Estado) VALUES (?, ?, ?, ?)");
            $stmt->execute([$idSucursal, $idProveedor, $fechaPedido, $estado]);
            $idPedido = $pdo->lastInsertId();

            // Insertar detalles del pedido
            $stmtDetalle = $pdo->prepare("INSERT INTO Detalle_Pedidos (idPedido, idMedicamento, Cantidad, Precio) VALUES (?, ?, ?, ?)");
            foreach ($medicamentos as $index => $idMedicamento) {
                $cantidad = filter_var($cantidades[$index], FILTER_VALIDATE_INT);
                if ($cantidad && $cantidad > 0) {
                    $stmtPrecio = $pdo->prepare("SELECT PrecioVenta FROM Medicamento WHERE idMedicamento = ?");
                    $stmtPrecio->execute([$idMedicamento]);
                    $precio = $stmtPrecio->fetchColumn();
                    
                    if ($precio !== false) {
                        $stmtDetalle->execute([$idPedido, $idMedicamento, $cantidad, $precio]);
                    } else {
                        echo "<p style='color:red;'>Medicamento con ID " . htmlspecialchars($idMedicamento) . " no encontrado.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Cantidad inválida para el medicamento: " . htmlspecialchars($idMedicamento) . "</p>";
                }
            }

            $pdo->commit();
            echo "<p style='color:green;'>Pedido realizado con éxito.</p>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Error al realizar el pedido: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Consultar medicamentos y proveedores
$medicamentos = $pdo->query("SELECT idMedicamento, Nombre FROM Medicamento WHERE Estatus = 'Disponible'")->fetchAll(PDO::FETCH_ASSOC);
$proveedores = $pdo->query("SELECT idProveedor, Nombre FROM Proveedores")->fetchAll(PDO::FETCH_ASSOC);
$sucursales = $pdo->query("SELECT idSucursal, Nombre FROM Sucursales")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
    <link rel="stylesheet" type="text/css" href="stylePedidos.css">
    <script>
        // Habilitar o deshabilitar el campo de cantidad según el checkbox
        function toggleCantidad(checkbox, index) {
            const cantidadInput = document.getElementById('cantidad_' + index);
            cantidadInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                cantidadInput.value = ''; // Limpia el valor si está deshabilitado
            }
        }
    </script>
</head>
<body>
    <h1>Realizar Pedido</h1>
    <form method="POST">
        <label for="idSucursal">Sucursal:</label>
        <select name="idSucursal" required>
            <?php foreach ($sucursales as $sucursal): ?>
                <option value="<?= $sucursal['idSucursal'] ?>"><?= $sucursal['Nombre'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="idProveedor">Proveedor:</label>
        <select name="idProveedor" required>
            <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?= $proveedor['idProveedor'] ?>"><?= $proveedor['Nombre'] ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <h3>Medicamentos</h3>
        <?php foreach ($medicamentos as $index => $medicamento): ?>
            <input type="checkbox" name="medicamento[]" value="<?= $medicamento['idMedicamento'] ?>" 
                   onclick="toggleCantidad(this, <?= $index ?>)">
            <?= $medicamento['Nombre'] ?>
            <input type="number" name="cantidad[]" id="cantidad_<?= $index ?>" min="1" placeholder="Cantidad" disabled required><br>
        <?php endforeach; ?>

        <button type="submit">Realizar Pedido</button>
    </form>
</body>
</html>
