<?php
// Conexión a la base de datos
$pdo = new PDO("mysql:host=db;dbname=FARMACIA;charset=utf8", 'user', 'user_password');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener las sucursales y los puestos
$sucursales = $pdo->query("SELECT idSucursal, nombre FROM Sucursales")->fetchAll(PDO::FETCH_ASSOC);
$puestos = $pdo->query("SELECT idPuesto, Puesto, salario FROM Puesto")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleados</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
   
    <link rel="stylesheet" type="text/css" href="styleAgregar.css">
    <script>
        // Función para autocompletar el salario según el puesto
        function actualizarSalario() {
            var puestoSeleccionado = document.getElementById("puesto").value;
            var salario = document.getElementById("salario");
            var puestos = <?php echo json_encode($puestos); ?>;

            for (var i = 0; i < puestos.length; i++) {
                if (puestos[i].idPuesto == puestoSeleccionado) {
                    salario.value = puestos[i].salario;
                    break;
                }
            }
        }
    </script>
</head>
<body>
    <h1>Registro de Empleados</h1>
    <form id="employee-form" action="ListaSucursales.php" method="post">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" required>
    </div>
    <div class="form-group">
        <label for="apellido_p">Apellido Paterno:</label>
        <input type="text" id="apellido_p" name="apellido_p" required>
    </div>
    <div class="form-group">
        <label for="apellido_m">Apellido Materno:</label>
        <input type="text" id="apellido_m" name="apellido_m">
    </div>
    <div class="form-group">
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="telefono" required>
    </div>
    <div class="form-group">
        <label for="curp">CURP:</label>
        <input type="text" id="curp" name="curp" required>
    </div>
    <div class="form-group">
        <label for="rfc">RFC:</label>
        <input type="text" id="rfc" name="rfc" required>
    </div>
    <div class="form-group">
        <label for="puesto">Puesto:</label>
        <select id="puesto" name="idPuesto" onchange="actualizarSalario()" required>
            <option value="">Selecciona un puesto</option>
            <?php foreach ($puestos as $puesto): ?>
                <option value="<?php echo $puesto['idPuesto']; ?>"><?php echo $puesto['Puesto']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="salario">Salario:</label>
        <input type="number" id="salario" name="salario" required readonly>
    </div>
    <div class="form-group full-width">
        <label for="sucursal">Sucursal:</label>
        <select id="sucursal" name="idSucursal" required>
            <option value="">Selecciona una sucursal</option>
            <?php foreach ($sucursales as $sucursal): ?>
                <option value="<?php echo $sucursal['idSucursal']; ?>"><?php echo $sucursal['nombre']; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
        <button type="submit">Agregar Empleado</button>
        <button type="button" class="exit-button" onclick="window.location.href='ListaSucursales.php';">Salir</button>
    </form>
</body>
</html>
