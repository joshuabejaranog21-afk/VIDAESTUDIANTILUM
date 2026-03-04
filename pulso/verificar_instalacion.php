<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Verificación de Instalación - Módulo Pulso</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

// Verificar archivos
$archivos = [
    'APIs' => [
        '../assets/API/pulso/leer.php',
        '../assets/API/pulso/crear.php',
        '../assets/API/pulso/actualizar.php',
        '../assets/API/pulso/borrar.php',
        '../assets/API/pulso/anios.php',
        '../assets/API/pulso/upload_foto.php'
    ],
    'Páginas' => [
        'index.php',
        'admin.php'
    ],
    'Carpetas' => [
        '../uploads/pulso/fotos/'
    ]
];

echo "<h2>Verificando Archivos y Carpetas</h2>";

foreach ($archivos as $categoria => $lista) {
    echo "<h3>$categoria</h3><ul>";
    foreach ($lista as $archivo) {
        $existe = file_exists($archivo);
        $clase = $existe ? 'ok' : 'error';
        $texto = $existe ? '✓ Existe' : '✗ NO EXISTE';
        echo "<li class='$clase'>$archivo - $texto</li>";
    }
    echo "</ul>";
}

// Verificar permisos de escritura
echo "<h2>Verificando Permisos</h2>";
$carpetaUploads = '../uploads/pulso/fotos/';
if (file_exists($carpetaUploads)) {
    if (is_writable($carpetaUploads)) {
        echo "<p class='ok'>✓ La carpeta $carpetaUploads tiene permisos de escritura</p>";
    } else {
        echo "<p class='error'>✗ La carpeta $carpetaUploads NO tiene permisos de escritura</p>";
        echo "<p class='warning'>Ejecuta: chmod 755 $carpetaUploads</p>";
    }
} else {
    echo "<p class='error'>✗ La carpeta $carpetaUploads no existe</p>";
}

// Verificar conexión a BD
echo "<h2>Verificando Base de Datos</h2>";
try {
    include('../assets/API/db.php');
    $db = new Conexion();
    echo "<p class='ok'>✓ Conexión a base de datos exitosa</p>";

    // Verificar tabla
    $result = $db->query("SHOW TABLES LIKE 'VRE_PULSO_EQUIPOS'");
    if ($db->rows($result) > 0) {
        echo "<p class='ok'>✓ Tabla VRE_PULSO_EQUIPOS existe</p>";

        // Contar registros
        $count = $db->query("SELECT COUNT(*) as total FROM VRE_PULSO_EQUIPOS");
        $row = $count->fetch_assoc();
        echo "<p class='ok'>✓ La tabla tiene {$row['total']} registros</p>";
    } else {
        echo "<p class='error'>✗ La tabla VRE_PULSO_EQUIPOS NO existe</p>";
        echo "<p class='warning'>Ejecuta el script SQL de instalación</p>";
    }

} catch (Exception $e) {
    echo "<p class='error'>✗ Error de conexión: " . $e->getMessage() . "</p>";
}

echo "<h2>Resumen</h2>";
echo "<p>Si todos los elementos muestran ✓ en verde, la instalación está completa.</p>";
echo "<p><a href='index.php'>Ir a Vista Pública</a> | <a href='admin.php'>Ir a Administración</a></p>";
?>
