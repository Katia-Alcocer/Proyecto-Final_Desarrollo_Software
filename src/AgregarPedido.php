<?php
// Conexión a la base de datos
$host = 'db'; // El nombre del contenedor de la base de datos en Docker Compose
$dbname = 'FARMACIA';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}

// Procesar el formulario de pedido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idSucursal = $_POST['idSucursal'];
    $idProveedor = $_POST['idProveedor'];
    $fechaPedido = date('Y-m-d');
    $estado = 'Pendiente';
    $medicamentos = $_POST['medicamento'];
    $cantidades = $_POST['cantidad'];

    try {
        // Insertar el pedido
        $stmt = $pdo->prepare("INSERT INTO Pedidos (idSucursal, idProveedor, FechaPedido, Estado) VALUES (?, ?, ?, ?)");
        $stmt->execute([$idSucursal, $idProveedor, $fechaPedido, $estado]);
        $idPedido = $pdo->lastInsertId();

        // Insertar detalles del pedido
        $stmtDetalle = $pdo->prepare("INSERT INTO Detalle_Pedidos (idPedido, idMedicamento, Cantidad, Precio) VALUES (?, ?, ?, ?)");
        foreach ($medicamentos as $index => $idMedicamento) {
            $cantidad = $cantidades[$index];
            $precio = $pdo->query("SELECT PrecioVenta FROM Medicamento WHERE idMedicamento = $idMedicamento")->fetchColumn();
            $stmtDetalle->execute([$idPedido, $idMedicamento, $cantidad, $precio]);
        }

        echo "Pedido realizado con éxito.";
    } catch (PDOException $e) {
        echo "Error al realizar el pedido: " . $e->getMessage();
    }
}

if (isset($_POST['submit'])) {
    $medicamentos = $_POST['medicamentos'] ?? [];
    $cantidades = $_POST['cantidades'] ?? [];

    $pdo->beginTransaction();
    try {
        foreach ($medicamentos as $index => $medicamento) {
            $cantidad = $cantidades[$index] ?? null;

            // Validar que la cantidad no esté vacía, sea numérica y mayor a 0
            if (!empty($cantidad) && is_numeric($cantidad) && $cantidad > 0) {
                $stmt = $pdo->prepare("INSERT INTO pedidos (MedicamentoID, Cantidad) VALUES (?, ?)");
                $stmt->execute([$medicamento, $cantidad]);
            }
        }
        $pdo->commit();
        echo "Pedido realizado con éxito.";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error al realizar el pedido: " . $e->getMessage();
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
        <?php foreach ($medicamentos as $medicamento): ?>
            <input type="checkbox" name="medicamento[]" value="<?= $medicamento['idMedicamento'] ?>">
            <?= $medicamento['Nombre'] ?>
            <input type="number" name="cantidad[]" min="1" placeholder="Cantidad"><br>
        <?php endforeach; ?>

        <button type="submit">Realizar Pedido</button>
    </form>
</body>
</html>
