<?php
session_start();  // Asegúrate de iniciar la sesión

try {
    // Conexión a la base de datos
    $pdo = new PDO("mysql:host=db;dbname=FARMACIA;charset=utf8", 'user', 'user_password');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recuperar el idSucursal de la sesión
    $idSucursal = isset($_SESSION['idSucursal']) ? $_SESSION['idSucursal'] : null;

    // Verificar si el idSucursal está disponible
    if ($idSucursal) {
        // Consultar el nombre de la sucursal
        $stmtSucursal = $pdo->prepare("SELECT nombre FROM Sucursales WHERE idSucursal = :idSucursal");
        $stmtSucursal->bindParam(':idSucursal', $idSucursal);
        $stmtSucursal->execute();
        $sucursal = $stmtSucursal->fetch(PDO::FETCH_ASSOC);
    } else {
        die("No se encontró una sucursal en la sesión.");
    }

    // Obtener clasificaciones, eliminaciones, proveedores
    $clasificacion = $pdo->query("SELECT idClasificacion, Tipo FROM ClasificacionM")->fetchAll(PDO::FETCH_ASSOC);
    $eliminacion = $pdo->query("SELECT idEliminacion, MedRegresable FROM EliminacionMedicamento")->fetchAll(PDO::FETCH_ASSOC);
    $provedores = $pdo->query("SELECT idProveedor, Nombre FROM Proveedores")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die("Error al conectar con la base de datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Medicamentos</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="../styleAgregar.css">
</head>
<body>
    <h1>Registro de Medicamento</h1>
    <form id="employee-form" action="ListaMedicamentos.php" method="post">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>
    <div class="form-group">
        <label for="clasificacion">Clasificación:</label>
    <select id="clasificacion" name="idClasificacion" required>
        <option value="">Selecciona una Clasificación</option>
          <?php foreach ($clasificacion as $clas): ?>
              <option value="<?php echo htmlspecialchars($clas['idClasificacion']); ?>">
                 <?php echo htmlspecialchars($clas['Tipo']); ?>
              </option>
          <?php endforeach; ?>
    </select>
    </div>
    <div class="form-group">
        <label for="cantidad">Cantidad:</label>
        <input type="text" id="cantidad" name="cantidad" required>
    </div>

    <div class="form-group">
        <label for="precio_c">Precio Compra:</label>
        <input type="text" id="precio_c" name="precio_c">
    </div>

    <div class="form-group">
        <label for="precio_v">Precio Venta:</label>
        <input type="text" id="precio_v" name="precio_v" required>
    </div>
    <div class="form-group">
        <label for="eliminacion">En caso de no venderse:</label>
        <select id="eliminacion" name="idEliminacion" required>
            <option value="">Selecciona una Opción</option>
            <?php foreach ($eliminacion as $eli): ?>
                <option value="<?php echo $eli['idEliminacion']; ?>"><?php echo $eli['MedRegresable']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="provedor">Proveedor:</label>
        <select id="provedor" name="idProveedor" required>
            <option value="">Selecciona un Proveedor</option>
            <?php foreach ($provedores as $provedor): ?>
                <option value="<?php echo $provedor['idProveedor']; ?>"><?php echo $provedor['Nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="fechaCaducidad">Fecha de Caducidad:</label>
      <input type="date" id="fechaCaducidad" name="fechaCaducidad" required>
    </div>

    <div class="form-group">
        <label for="descripcion">Descripción:</label>
        <input type="text" id="descripcion" name="descripcion" required>
    </div>

    <!-- Mostrar la sucursal de la sesión, no editable -->
    <div class="form-group">
        <label for="sucursal">Sucursal:</label>
        <input type="text" id="sucursal" name="sucursal" value="<?php echo htmlspecialchars($sucursal['nombre']); ?>" readonly />
    </div>

    <input type="hidden" id="estatus" name="estatus" value="Disponible">

    <button type="submit">Agregar Medicamento</button>
    <button type="button" class="exit-button" onclick="window.location.href='../pagina_sucursar.html';">Salir</button>
    </form>
</body>
</html>
