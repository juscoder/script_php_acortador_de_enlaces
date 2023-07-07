<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "id19761972_url_user";
$password = "@16N9upYekJ[@snv";
$dbname = "id19761972_url";

// Conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Error al conectar a la base de datos: " . $conn->connect_error);
}

// Verificar si se ha enviado un enlace para acortar
if (isset($_POST['url'])) {
    $url_largo = $_POST['url'];

    // Verificar si el enlace ya está acortado en la base de datos
    $query = "SELECT short_code FROM short_urls WHERE long_url = '$url_largo'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $url_acortado = getBaseUrl() . $row['short_code'];
    } else {
        // Generar un código único para el enlace acortado
        $short_code = generarCodigoUnico();

        // Insertar el enlace en la base de datos
        $query = "INSERT INTO short_urls (long_url, short_code, created_at) VALUES ('$url_largo', '$short_code', NOW())";
        if ($conn->query($query) === TRUE) {
            $url_acortado = getBaseUrl() . $short_code;
        } else {
            echo "Error al acortar el enlace: " . $conn->error;
        }
    }

   /* if (isset($url_acortado)) {
        echo "Enlace acortado: <a href='$url_acortado' target='_blank'>$url_acortado</a>";
    }*/
}

// Redirección del enlace acortado
if (isset($_GET['url'])) {
    $code = $_GET['url'];

    // Consultar la URL original correspondiente al código acortado en la base de datos
    $query = "SELECT long_url FROM short_urls WHERE short_code = '$code'";
    $result = $conn->query($query);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $url_original = $row['long_url'];

        if (strpos($url_original, 'http://') === 0 || strpos($url_original, 'https://') === 0) {
            header("Location: $url_original");
        } else {
            header("Location: http://$url_original");
        }

        exit();
    } else {
        // Si no se encuentra el enlace acortado, redirigir a una página de error o página principal
        header("Location: /404.php");
        exit();
    }
}


function generarCodigoUnico() {
    // Generar un código único para el enlace acortado
    $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $codigo = '';
    for ($i = 0; $i < 6; $i++) {
        $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    return $codigo;
}

function getBaseUrl() {
    // Obtener la URL base actual
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host . '/';
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Acortador de Enlaces</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    </head>
    <body class="bg-gray-100 flex items-center justify-center h-screen">
        <div class="w-full max-w-md p-4">
            <h1 class="text-4xl font-bold mb-8 text-center">Acortador de Enlaces</h1>

            <form method="POST" action="" class="flex items-center">
                <input type="text" name="url" placeholder="Ingrese un enlace largo" required class="w-full p-2 border border-gray-300 rounded-l" />
                <input type="submit" value="Acortar" class="px-4 py-2 bg-blue-500 text-white rounded-r hover:bg-blue-600 cursor-pointer" />
            </form>
            <label class="block text-gray-700 text-sm font-bold mb-2 mt-5" for="fromName">Enlace acortado:</label>
            <div class="w-full p-3 border border-gray-300 rounded">
                <?php
                    echo "<a href='$url_acortado' target='_blank'>$url_acortado</a>";
                ?>
            </div>
            <div class="mt-4 text-center">
                <a class="text-blue-500 underline font-bold hover:text-blue-darker" href="https://jusapp.000webhostapp.com/">Acortar otro URL</a>
            </div>
        </div>
    </body>
</html>