<?php
// Script para corregir definitivamente las URLs
include("assets/API/db.php");

$db = new Conexion();

echo "<h2>Corrección Final de URLs</h2><hr>";

// Opción 1: Ruta relativa desde pages/repositorio/index.php
$url1 = "../../uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg";

// Opción 2: Ruta absoluta desde dominio
$url2 = "/vidaEstudiantil/uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg";

echo "<h3>Probando diferentes rutas:</h3>";
echo "<p><strong>Opción 1 (relativa desde pages/repositorio):</strong></p>";
echo "<code>$url1</code><br>";
echo "<img src='$url1' style='max-width: 200px; border: 2px solid blue;' onerror='this.alt=\"ERROR\"'><br><br>";

echo "<p><strong>Opción 2 (absoluta desde dominio):</strong></p>";
echo "<code>$url2</code><br>";
echo "<img src='$url2' style='max-width: 200px; border: 2px solid green;' onerror='this.alt=\"ERROR\"'><br><br>";

echo "<hr>";
echo "<h3>¿Cuál funcionó?</h3>";
echo "<p>Indica cuál de las dos imágenes se muestra correctamente y actualizaremos la base de datos con ese formato.</p>";

echo "<form method='post'>";
echo "<button type='submit' name='opcion' value='1' class='btn'>Usar Opción 1 (relativa)</button> ";
echo "<button type='submit' name='opcion' value='2' class='btn'>Usar Opción 2 (absoluta)</button>";
echo "</form>";

if (isset($_POST['opcion'])) {
    $opcion = $_POST['opcion'];

    if ($opcion == '1') {
        $nuevaUrl = "../../uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg";
    } else {
        $nuevaUrl = "/vidaEstudiantil/uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg";
    }

    $query = "UPDATE VRE_REPOSITORIO_FOTOS SET FOTO_URL = '$nuevaUrl' WHERE ID = 1";
    $db->query($query);

    echo "<hr>";
    echo "<p style='color: green;'>✓ URL actualizada a: <code>$nuevaUrl</code></p>";
    echo "<p><a href='pages/repositorio/index.php'>Ir al Repositorio para verificar</a></p>";
}
?>
