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


function insertarProvedor($pdo, $nombre, $telefono, $direccion) {
    try {
        // Consulta de inserción con placeholders
        $query = "INSERT INTO Proveedores (Nombre, Telefono, Direccion) 
                  VALUES (:nombre, :telefono, :direccion)";
        $stmt = $pdo->prepare($query);
        
        // Vincular los parámetros de la consulta con las variables de la función
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "<p>Proveedor agregada con éxito.</p>";
        } else {
            echo "<p>Error al agregar Proveedor.</p>";
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

    insertarProvedor($pdo, $nombre, $telefono, $direccion);
}

function obtenerProvedor($pdo,$estatus) {
    $query = "
    SELECT 
        p.idProveedor, p.Nombre, p.Telefono, p.Direccion 
    FROM Proveedores p
    WHERE p.estatus = :estatus
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$proveedores = obtenerProvedor($pdo,'Activo');
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
    <title>Lista de Proveedores</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <link rel="stylesheet" type="text/css" href="styleListas.css">
</head>
<body>

<h1>Lista de Proveedores</h1>

<table border="1">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acciones</th> <!-- Columna para editar y eliminar -->
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($proveedores as $proveedor) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($proveedor['Nombre']) . "</td>";
            echo "<td>" . htmlspecialchars($proveedor['Telefono']) . "</td>";
            echo "<td>" . htmlspecialchars($proveedor['Direccion']) . "</td>";
            echo "<td>
             <span>
                <a href='editar_Provedor.php?id=" . $proveedor['idProveedor'] . "'>
                    <img src='imagenes/Editar.png' alt='Editar'>
                </a>
               <a href='eliminar_Provedor.php?id=" . $proveedor['idProveedor'] . "' onclick=\"return confirm('¿Estás seguro de que deseas eliminar este proveedor?');\">
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
