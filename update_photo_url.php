<?php
include("assets/API/db.php");

$db = new Conexion();

// Actualizar la URL con la ruta absoluta correcta
$nuevaUrl = "/vidaEstudiantil/uploads/repositorio/1220593/foto_1761141888_68f8e4806c4bf.jpg";

$query = "UPDATE VRE_REPOSITORIO_FOTOS SET FOTO_URL = '$nuevaUrl' WHERE ID = 1";
$db->query($query);

echo "<h2>URL Actualizada</h2>";
echo "<p>Nueva URL: <code>$nuevaUrl</code></p>";
echo "<hr>";
echo "<p><strong>Prueba la imagen:</strong></p>";
echo "<img src='$nuevaUrl' style='max-width: 400px; border: 2px solid blue;'>";
echo "<hr>";
echo "<p><a href='pages/repositorio/index.php'>Ir al Repositorio</a></p>";
?>
