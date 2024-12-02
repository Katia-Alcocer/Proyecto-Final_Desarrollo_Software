<?php
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

// Consultar medicamentos disponibles
$medicamentos = $pdo->query("SELECT M.idMedicamento, M.Nombre, M.PrecioVenta, M.Cantidad, C.idClasificacion, C.Tipo
                             FROM Medicamento M
                             JOIN ClasificacionM C ON M.idClasificacion = C.idClasificacion
                             WHERE M.Estatus = 'Disponible'")->fetchAll(PDO::FETCH_ASSOC);


// Consultar empleados y sucursales
$empleados = $pdo->query("SELECT idEmpleado, Nombre FROM Empleados")->fetchAll(PDO::FETCH_ASSOC);
$sucursales = $pdo->query("SELECT idSucursal, Nombre FROM Sucursales")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idSucursal = $_POST['idSucursal'];
    $idEmpleado = $_POST['idEmpleado'];
    $fechaVenta = date('Y-m-d H:i:s');
    $medicamentosVendidos = $_POST['medicamento'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];


    try {
        $pdo->beginTransaction();

        // Insertar la venta
        $stmtVenta = $pdo->prepare("INSERT INTO Ventas (idSucursal, idEmpleado, FechaVenta) VALUES (?, ?, ?)");
        $stmtVenta->execute([$idSucursal, $idEmpleado, $fechaVenta]);
        $idVenta = $pdo->lastInsertId();

        // Manejo de cada medicamento vendido
        foreach ($medicamentosVendidos as $index => $idMedicamento) {
            $cantidad = filter_var($cantidades[$index], FILTER_VALIDATE_INT);
            if ($cantidad && $cantidad > 0) {
                // Obtener información del medicamento
            
                $stmtMed = $pdo->prepare("SELECT M.PrecioVenta, M.Cantidad, C.idClasificacion, O.PorcentajeDescuento, CM.porcentaje_comision, 
                                          IF(CM.PorcentajeDescuento > 0, 1, 0) AS TieneOferta 
                                          FROM Medicamento M
                                          LEFT JOIN Comisiones CM ON M.idMedicamento = CM.idMedicamento
                                          JOIN ClasificacionM C ON M.idClasificacion = C.idClasificacion
                                          JOIN Ofertas O ON O.idMedicamento = M.idMedicamento
                                          WHERE M.idMedicamento = ?");
                
                $stmtMed->execute([$idMedicamento]);
                $medicamento = $stmtMed->fetch(PDO::FETCH_ASSOC);

                if (!$medicamento || $medicamento['Cantidad'] < $cantidad) {
                    throw new Exception("Stock insuficiente para el medicamento con ID: $idMedicamento");
                }

                // Verificar en Ofertas
    $stmtOferta = $pdo->prepare("SELECT COUNT(*) FROM Ofertas WHERE idMedicamento = ?");
    $stmtOferta->execute([$idMedicamento]);
    $tieneOferta = $stmtOferta->fetchColumn() > 0;

    // Verificar en Comisiones
    $stmtComision = $pdo->prepare("SELECT COUNT(*) FROM Comisiones WHERE idMedicamento = ?");
    $stmtComision->execute([$idMedicamento]);
    $tieneComision = $stmtComision->fetchColumn() > 0;

    echo json_encode([
        'tieneOferta' => $tieneOferta,
        'tieneComision' => $tieneComision
    ]);

                $precioVenta = $medicamento['PrecioVenta'];
                $descuento = $medicamento['Descuento'];
                $precioFinal = $precioVenta * (1 - $descuento / 100) * $cantidad;

                // Restar cantidad de Medicamento
                $stmtUpdate = $pdo->prepare("UPDATE Medicamento SET Cantidad = Cantidad - ? WHERE idMedicamento = ?");
                $stmtUpdate->execute([$cantidad, $idMedicamento]);

                // Insertar detalle de venta
                $stmtDetalle = $pdo->prepare("INSERT INTO Detalle_Ventas (idVenta, idMedicamento, Cantidad, PrecioTotal) VALUES (?, ?, ?, ?)");
                $stmtDetalle->execute([$idVenta, $idMedicamento, $cantidad, $precioFinal]);

                // Si tiene clasificación especial (idClasificacion = 4)
                if ($medicamento['idClasificacion'] == 4) {
                    $nombrePaciente = $_POST['NombrePaciente'];
                    $nombreDoctor = $_POST['NombreDoctor'];
                    $telefonoDoctor = $_POST['TelefonoDoctor'];
                    $cedulaDoctor = $_POST['CedulaDoctor'];

                    $stmtUpdateVenta = $pdo->prepare("UPDATE Ventas 
                                                      SET NombrePaciente = ?, NombreDoctor = ?, TelefonoDoctor = ?, CedulaDoctor = ? 
                                                      WHERE idVenta = ?");
                    $stmtUpdateVenta->execute([$nombrePaciente, $nombreDoctor, $telefonoDoctor, $cedulaDoctor, $idVenta]);
                }

                // Manejo de comisiones
               /* $stmtComision = $pdo->prepare("SELECT porcentaje_comision FROM Comisiones WHERE idMedicamento = ?");
                $stmtComision->execute([$idMedicamento]);
                $porcentajeComision = $stmtComision->fetchColumn() ?? 0;
                if ($porcentajeComision > 0) {
                    $montoComision = $precioVenta * $porcentajeComision / 100 * $cantidad;
                    $stmtActualizarComision = $pdo->prepare("UPDATE Empleados SET Comisiones = Comisiones + ? WHERE idEmpleado = ?");
                    $stmtActualizarComision->execute([$montoComision, $idEmpleado]);
                }*/
            }
        }

        $pdo->commit();
        echo "<p style='color:green;'>Venta realizada con éxito.</p>";
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<p style='color:red;'>Error al realizar la venta: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Venta</title>
    <link rel="stylesheet" type="text/css" href="styleVentas.css">
    <script>
 // Convertir empleados en JavaScript desde PHP
 const empleadosPorSucursal = <?= json_encode($empleadosPorSucursal = $pdo->query("SELECT idEmpleado, Nombre, idSucursal FROM Empleados")->fetchAll(PDO::FETCH_ASSOC)); ?>;

        function cargarEmpleados(idSucursal) {
        const selectEmpleado = document.getElementById('idEmpleado');
        selectEmpleado.innerHTML = '<option value="">Seleccione un empleado</option>';

        empleadosPorSucursal.forEach(empleado => {
            if (empleado.idSucursal == idSucursal) {
                const option = document.createElement('option');
                option.value = empleado.idEmpleado;
                option.textContent = empleado.Nombre;
                selectEmpleado.appendChild(option);
            }
        });
    }
    const tieneOferta = medicamentoSelect.options[medicamentoSelect.selectedIndex].dataset.oferta;
    const tieneComision = medicamentoSelect.options[medicamentoSelect.selectedIndex].dataset.comision;
    document.getElementById("tieneOferta").value = tieneOferta ? "Sí" : "No";
    document.getElementById("tieneComision").value = tieneComision ? "Sí" : "No";

    function actualizarTotal(precio) {
    const totalInput = document.getElementById("totalPagar");
    let totalActual = parseFloat(totalInput.value || 0);
    totalInput.value = ('precioUnitario' * 'cantidad').toFixed(2);
}
function actualizarDatosMedicamento() {
        const selectMedicamento = document.getElementById('medicamento');
        const cantidadStock = document.getElementById('cantidadStock');
        const precioUnitario = document.getElementById('precioUnitario');

        const optionSeleccionada = selectMedicamento.options[selectMedicamento.selectedIndex];
        cantidadStock.value = optionSeleccionada.dataset.cantidad || '';
        precioUnitario.value = `$${optionSeleccionada.dataset.precio || ''}`;
    }

    function verificarClasificacion() {
            const medicamentoSelect = document.getElementById("medicamento");
            const clasificacion = medicamentoSelect.options[medicamentoSelect.selectedIndex].dataset.clasificacion;
            const camposExtras = document.getElementById("camposExtras");

            if (clasificacion === "4") {
                camposExtras.style.display = "block";
            } else {
                camposExtras.style.display = "none";
            }
        }

    </script>
</head>
<body>
    <h1>Realizar Venta</h1>

    <div class="Formulario">
    <form method="POST" action="pagina_admin.html">

    <div class="arriba">
    <label for="idSucursal">Sucursal:</label>
<select name="idSucursal" id="idSucursal" onchange="cargarEmpleados(this.value)" required>
    <option value="">Seleccione una sucursal</option>
    <?php foreach ($sucursales as $sucursal): ?>
        <option value="<?= $sucursal['idSucursal'] ?>"><?= $sucursal['Nombre'] ?></option>
    <?php endforeach; ?>
</select><br><br>

<label for="idEmpleado">Empleado:</label>
<select name="idEmpleado" id="idEmpleado" required>
    <option value="">Seleccione un empleado</option>
</select>


<div>
    <label for="medicamento">Medicamento:</label>
    <select name="medicamento" id="medicamento" onchange="actualizarDatosMedicamento(); verificarClasificacion()" required>
        <option value="">Seleccione un medicamento</option>
        <?php foreach ($medicamentos as $medicamento): ?>
            <option value="<?= $medicamento['idMedicamento'] ?>" 
                data-clasificacion="<?= $medicamento['idClasificacion'] ?>" 
                data-cantidad="<?= $medicamento['Cantidad'] ?>" 
                data-precio="<?= $medicamento['PrecioVenta'] ?>">
                <?= $medicamento['Nombre'] ?> - Tipo: <?= $medicamento['Tipo'] ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

<!-- Cuadro de stock -->
<div>
    <label>Cantidad en Stock:</label>
    <input type="text" id="cantidadStock" readonly>
</div>

<!-- Cuadro de precio -->
<div>
    <label>Precio Unitario:</label>
    <input type="text" id="precioUnitario" readonly>
</div>


<label>¿Tiene Oferta?</label> 
<input type="text" id="tieneOferta" disabled><br>

<label>¿Tiene Comisión?</label> 
<input type="text" id="tieneComision" disabled><br>

</div>
<div class="abajo">
<label for="cantidad">Cantidad:</label>
        <input type="number" name="cantidad" min="1" required><br>

        <div id="camposExtras" style="display: none;">
            <h3>Datos Adicionales: </h3>
            <label>Nombre del Paciente:</label> <input type="text" name="NombrePaciente"><br>
            <label>Nombre del Doctor:</label> <input type="text" name="NombreDoctor"><br>
            <label>Teléfono del Doctor:</label> <input type="text" name="TelefonoDoctor"><br>
            <label>Cédula del Doctor:</label> <input type="text" name="CedulaDoctor"><br>
        </div>
        
        

        <label>Total a Pagar:</label> 
        <input type="text" id="totalPagar" readonly><br>

        <button class="act" type="submit">Registrar Venta</button>
        <button class="exit-button" type="button" onclick="window.location.href='pagina_admin.html'">Salir sin Guardar</button>
        </div>
        
    </form>
    </div>
</body>
</html>