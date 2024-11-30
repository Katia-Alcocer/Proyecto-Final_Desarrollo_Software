<?php
require 'conexion.php';
$host = 'db'; 
$dbname = 'FARMACIA';
$username = 'user';
$password = 'user_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $s) {
    die("Error en la conexión: " . $s->getMessage());
}


function insertarSucursal($pdo, $nombre, $telefono, $direccion, $usuario, $clave) {
    try {
        // Consulta de inserción con placeholders
        $query = "INSERT INTO Sucursales (Nombre, Telefono, Direccion, Usuario, Clave) 
                  VALUES (:nombre, :telefono, :direccion, :usuario, :clave)";
        $stmt = $pdo->prepare($query);
        
        // Vincular los parámetros de la consulta con las variables de la función
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':clave', $clave);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<p>Sucursal agregada con éxito.</p>";
        } else {
            echo "<p>Error al agregar Sucursal.</p>";
        }
    } catch (PDOException $e) {
        echo "Error en la inserción: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir datos del formulario
    $nombre = $_POST['Nombre'];
    $telefono = $_POST['Telefono'];
    $direccion = $_POST['Direccion'];
    $usuario = $_POST['Usuario'];
    $clave = $_POST['Clave']; // Encriptar la contraseña

   // $clave = password_hash($_POST['Clave'], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Llamar a la función para insertar la sucursal con los datos del usuario
    insertarSucursal($pdo, $nombre, $telefono, $direccion, $usuario, $clave);
}

function obtenerSucursales($pdo, $estatus) {
    $query = "
    SELECT 
        s.idSucursal, s.Nombre, s.Telefono, s.Direccion, s.Usuario, s.Clave 
    FROM Sucursales s
    WHERE s.estatus = :estatus
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$sucursales = obtenerSucursales($pdo, 'Activo');
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
    <title>Lista de Sucursales</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleListas.css">
</head>
<body>

<h1>Lista de Sucursales</h1>

<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Ver Empleados</th>
            <th>Medicamento Vendido</th>
            <th>Medicamento Disponible</th>
            <th>Acciones</th> <!-- Columna para editar y eliminar -->
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($sucursales as $sucursal) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($sucursal['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($sucursal['Telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($sucursal['Direccion']) . "</td>";
            echo "<td>" . htmlspecialchars($sucursal['Usuario']) . "</td>";
            echo "<td>" . htmlspecialchars($sucursal['Clave']) . "</td>";

            echo "<td>  <a href='Listaempleados.php?id=" . $sucursal['idSucursal'] . "'>
                    <img src='imagenes/ver.png' alt='Editar'>
                </a></td>";

            echo "<td>  <a href='MedicamentoVendido.php?id=" . $sucursal['idSucursal'] . "'>
                    <img src='imagenes/MedVendido.png' alt='Editar'>
                </a></td>";

            echo "<td>  <a href='ListaMedicamento.php?id=" . $sucursal['idSucursal'] . "'>
                    <img src='imagenes/MedDispo.png' alt='Editar'>
                </a></td>";

            echo "<td>

            <span>
                <a href='editar_Sucursal.php?id=" . $sucursal['idSucursal'] . "'>
                    <img src='imagenes/Editar.png' alt='Editar'>
                </a>
                <a href='eliminar_Sucursal.php?id=" . $sucursal['idSucursal']. "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar esta sucursal?');\">
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
