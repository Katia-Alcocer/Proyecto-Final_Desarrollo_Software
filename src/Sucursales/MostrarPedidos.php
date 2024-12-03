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

// Obtener los pedidos con estado "Aprobado" y que correspondan a la sucursal de la sesión
$stmt = $pdo->prepare("SELECT p.idPedido, dp.Cantidad, p.Estado, m.Nombre 
        FROM Detalle_Pedidos dp 
        JOIN Medicamento m ON dp.idMedicamento = m.idMedicamento
        JOIN Pedidos p ON dp.idPedido = p.idPedido
        WHERE p.Estado = 'Aprobado' AND m.idSucursal = ?
        ORDER BY p.idPedido DESC");
$stmt->execute([$idSucursalSesion]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .pedido {
            background-color: #ffffff;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 8px;
            width: 250px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .pedido h3 {
            margin-top: 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
        }
        .aprobado {
            color: green;
        }
        .rechazado {
            color: red;
        }
        .pendiente {
            color: orange;
        }
        .act {
            background-color: #42a5f5;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .button-container {
            display: flex;
            justify-content: center; /* Centrar el botón */
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Gestión de Pedidos</h1>
    <div class="container">
        <?php if (count($pedidos) > 0): ?>
            <?php foreach ($pedidos as $pedido): ?>
                <div class="pedido">
                    <h3><?php echo htmlspecialchars($pedido['Nombre']); ?></h3>
                    <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($pedido['Cantidad']); ?></p>
                    <p><strong>Estado:</strong> 
                        <span class="<?php echo strtolower($pedido['Estado']); ?>">
                            <?php echo htmlspecialchars($pedido['Estado']); ?>
                        </span>
                    </p>
                    <form method="POST">
                        <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['idPedido']); ?>">
                        <div class="buttons">
                            <em>Acción completada</em>
                        </div>
                        <div class="buttons">
                        <?php if ($pedido['Estado'] === 'Pendiente'): ?>
                            <button type="submit" name="accion" value="Recibido">Recibido</button>
                            <button type="submit" name="accion" value="Incompleto">Incompleto</button>
                        <?php else: ?>
                            <em>Acción completada</em>
                        <?php endif; ?>
                    </div>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No hay pedidos aprobados para mostrar.</p>
        <?php endif; ?>
    </div>
    <div class="button-container">
        <button class="act" onclick="window.location.href='../pagina_sucursar.html';">Regresar</button>
    </div>
</body>
</html>
