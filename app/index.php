<?php
// Conexión a la base de datos
$servername = "db";  // Cambia esto si tu servidor de base de datos está en otro lugar
$username = "usuario1";   // Tu usuario de base de datos
$password = "contrasenyaUsuario1"; // Tu contraseña de base de datos
$dbname = "cine";  // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

//vble. para guardar mensajes
$message = "";

// Inserción de pelis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_movie"])) {
    $titulo = $_POST["titulo"];
    $director = $_POST["director"];
    $nota = $_POST["nota"];
    $anyo = $_POST["anyo"];
    $presupuesto = $_POST["presupuesto"];
    $img_base64 = $_POST["img_base64"];
    $url_trailer = $_POST["url_trailer"];

    $sql = "INSERT INTO peliculas (titulo, director, nota, anyo, presupuesto, img_base64, url_trailer)
            VALUES ('$titulo', '$director', '$nota', '$anyo', '$presupuesto', '$img_base64', '$url_trailer')";

    if ($conn->query($sql) === TRUE) {
        $message = "Se ha añadido una nueva película: " . $titulo . ".";
    } else {
        $message = "Error al intentar añadir la nueva película: " . $conn->error;
    }
}

//Eliminación de peli
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_movie"])) {
    $id = $_POST["id"];

    $sql = "DELETE FROM peliculas WHERE id= $id";

    if ($conn->query($sql) === TRUE) {
        $message = "Se ha borrado la pelicula: " . $id . " correctamente";
    } else {
        $message = "Error al intentar borrar la pelicula " . $id;
    }

    // Para dirigir a la misma página y evitar reenvío del formulario al recargar página

    //header("Location: " . $_SERVER["PHP_SELF"] . "?message=" . urlencode($message));
    //exit;
}


// Obtener todas las películas de la base de datos
$sql = "SELECT * FROM peliculas";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Películas</title>
    <script>
        // Mostrar alerta si hay mensaje
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>

<body onload="showAlert('<?php echo htmlspecialchars($message); ?>')">

    <h1>Lista de Películas</h1>

    <!-- Mostrar las películas en una tabla -->
    <table border="1" cellspacing="0" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Director</th>
            <th>Año</th>
            <th>Nota</th>
            <th>Presupuesto</th>
            <th>Imagen</th>
            <th>URL del Trailer</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {
            // Mostrar datos de cada fila
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>" . $row["id"] . "</td>
                    <td>" . $row["titulo"] . "</td>
                    <td>" . $row["director"] . "</td>
                    <td>" . $row["anyo"] . "</td>
                    <td>" . $row["nota"] . "</td>
                    <td>" . $row["presupuesto"] . "</td>
                    <td><img src='data:image/jpeg;base64," . $row["img_base64"] . "' alt='" . $row["titulo"] . "' width='100'></td>
                    <td><a href='" . $row["url_trailer"] . "' target='_blank'>Ver Trailer</a></td>
                    
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No hay películas disponibles</td></tr>";
        }

        ?>
    </table>

    <h2>Añadir Película</h2>
    <form method="POST">
        <label>Título:</label>
        <input type="text" name="titulo" required><br>
        <label>Director:</label>
        <input type="text" name="director" required><br>
        <label>Año:</label>
        <input type="number" name="anyo" required><br>
        <label>Nota:</label>
        <input type="number" step="0.1" name="nota" required><br>
        <label>Presupuesto:</label>
        <input type="number" name="presupuesto" required><br>
        <label>Imagen:</label>
        <input type="url" name="img_base64"><br>
        <label>URL del Trailer:</label>
        <input type="url" name="url_trailer"><br>
        <button type="submit" name="add_movie">Añadir Película</button>
    </form>

    <h2>Borrar Película</h2>
    <form method="POST">
        <label>ID de la película a borrar:</label>
        <input type="number" name="id" required><br>
        <button type="submit" name="delete_movie">Borrar Película</button>
    </form>


</body>

</html>

<?php
// Cerrar la conexión
$conn->close();
?>