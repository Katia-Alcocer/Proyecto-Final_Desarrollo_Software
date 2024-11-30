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

// Función para insertar un empleado
function insertarEmpleado($pdo, $nombre, $apellido_p, $apellido_m, $telefono, $curp, $rfc, $idPuesto, $idSucursal) {
    // Consulta para obtener el salario basado en el puesto
    $query = "SELECT salario FROM Puesto WHERE idPuesto = :idPuesto";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idPuesto', $idPuesto);
    $stmt->execute();
    $puesto = $stmt->fetch(PDO::FETCH_ASSOC);
    $salario = $puesto['salario'];

    // Inserción de los datos del empleado
    $query = "INSERT INTO Empleados (Nombre, ApellidoP, ApellidoM, Telefono, CURP, RFC, Salario, idSucursal, idPuesto) 
              VALUES (:nombre, :apellido_p, :apellido_m, :telefono, :curp, :rfc, :salario, :idSucursal, :idPuesto)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido_p', $apellido_p);
    $stmt->bindParam(':apellido_m', $apellido_m);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':curp', $curp);
    $stmt->bindParam(':rfc', $rfc);
    $stmt->bindParam(':salario', $salario);
    $stmt->bindParam(':idSucursal', $idSucursal);
    $stmt->bindParam(':idPuesto', $idPuesto);

    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: Listaempleados.php?mensaje=Empleado agregado con éxito.");
        exit;
    } else {
        // Redirigir con mensaje de error
        header("Location: Listaempleados.php?mensaje=Error al agregar el empleado.");
        exit;
    }
}

function obtenerEmpleados($pdo,$estatus) {
    $query = "
    SELECT 
        e.idEmpleado, e.Nombre, e.ApellidoP, e.ApellidoM, e.Telefono, e.CURP, e.RFC, e.Salario, 
        s.Nombre AS Sucursal, p.Puesto AS Puesto
    FROM Empleados e
    JOIN Sucursales s ON e.idSucursal = s.idSucursal
    JOIN Puesto p ON e.idPuesto = p.idPuesto
    WHERE e.estatus = :estatus";
    
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $query .= " WHERE e.idSucursal = :id"; 
    }
    
    $stmt = $pdo->prepare($query);
    
    // Asignar el parámetro solo si es necesario
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    }
    $stmt->bindParam(':estatus', $estatus);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Si se envió el formulario, insertar el nuevo empleado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $apellido_p = strtoupper($_POST['apellido_p']);
    $apellido_m = strtoupper($_POST['apellido_m']);
    $telefono = $_POST['telefono'];
    $curp = strtoupper($_POST['curp']);
    $rfc = strtoupper($_POST['rfc']);
    $idPuesto = $_POST['idPuesto'];
    $idSucursal = $_POST['idSucursal'];

    insertarEmpleado($pdo, $nombre, $apellido_p, $apellido_m, $telefono, $curp, $rfc, $idPuesto, $idSucursal);
}

// Obtener todos los empleados para mostrar
$empleados = obtenerEmpleados($pdo,'Activo');
?>
<?php
// Verificar si hay un mensaje en la URL
if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>

<!-- Aquí continúa el resto de tu código para listar los empleados -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empleados</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleListas.css">
    <style>
        /* El estilo permanece igual */
    </style>
</head>
<body>

<h1>Lista de Empleados</h1>
<!-- Tabla para mostrar los empleados -->
<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Apellido Paterno</th>
            <th>Apellido Materno</th>
            <th>Teléfono</th>
            <th>CURP</th>
            <th>RFC</th>
            <th>Salario</th>
            <th>Sucursal</th>
            <th>Puesto</th>
            <th>Acciones</th> <!-- Columna para acciones de editar y eliminar -->
        </tr>
    </thead>
    <tbody>
        <?php
        // Mostrar los empleados en la tabla
        foreach ($empleados as $empleado) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($empleado['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['ApellidoP']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['ApellidoM']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['Telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['CURP']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['RFC']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['Salario']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['Sucursal']) . "</td>";
            echo "<td>" . htmlspecialchars($empleado['Puesto']) . "</td>";
            echo "<td>
             <span>
                    <a href='editar_empleado.php?id=" . htmlspecialchars($empleado['idEmpleado']) . "'>
                        <img src='imagenes/Editar.png' alt='Editar'>
                    </a>
                    <a href='eliminar_empleado.php?id=" . htmlspecialchars($empleado['idEmpleado']) . "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este empleado?');\">
                    <img src='imagenes/Eliminar.png' alt='Eliminar'>
                    </a>
                     </span>
                  </td>";
            echo "</tr>";
        }
        
        ?>
    </tbody>
</table>

<button onclick="window.location.href='pagina_admin.html';">Regresar</button>

</body>
</html>

