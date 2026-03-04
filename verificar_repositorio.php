<?php
/**
 * Script de Verificación del Módulo de Repositorio de Fotografías
 *
 * Este script verifica que todas las tablas, archivos y configuraciones
 * del módulo estén correctamente instalados.
 */

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificación - Repositorio de Fotos</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #007bff;
            padding-bottom: 10px;
        }
        h2 {
            color: #555;
            margin-top: 30px;
        }
        .success {
            color: #28a745;
            padding: 10px;
            background: #d4edda;
            border-left: 4px solid #28a745;
            margin: 10px 0;
        }
        .error {
            color: #dc3545;
            padding: 10px;
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            margin: 10px 0;
        }
        .warning {
            color: #856404;
            padding: 10px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            margin: 10px 0;
        }
        .info {
            color: #004085;
            padding: 10px;
            background: #cce5ff;
            border-left: 4px solid #007bff;
            margin: 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: white;
        }
        .status-ok {
            color: #28a745;
            font-weight: bold;
        }
        .status-fail {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Verificación del Módulo de Repositorio de Fotografías</h1>
        <p><strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "</p>
";

// Conexión a la base de datos
try {
    include('assets/API/db.php');
    $db = new Conexion();
    echo "<div class='success'>✅ Conexión a la base de datos establecida correctamente</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Error al conectar a la base de datos: " . $e->getMessage() . "</div>";
    exit();
}

// Verificar tablas
echo "<h2>1. Verificación de Tablas en Base de Datos</h2>";
echo "<table>";
echo "<tr><th>Tabla</th><th>Estado</th><th>Registros</th></tr>";

$tablas = [
    'VRE_ESTUDIANTES',
    'VRE_REPOSITORIO_FOTOS',
    'VRE_REPOSITORIO_REFERENCIAS'
];

$todas_tablas_ok = true;

foreach ($tablas as $tabla) {
    $sql = $db->query("SHOW TABLES LIKE '$tabla'");
    if ($db->rows($sql) > 0) {
        $count_sql = $db->query("SELECT COUNT(*) as total FROM $tabla");
        $count = $db->recorrer($count_sql);
        echo "<tr><td>$tabla</td><td class='status-ok'>✅ Existe</td><td>" . $count['total'] . "</td></tr>";
    } else {
        echo "<tr><td>$tabla</td><td class='status-fail'>❌ No existe</td><td>-</td></tr>";
        $todas_tablas_ok = false;
    }
}

echo "</table>";

if (!$todas_tablas_ok) {
    echo "<div class='error'>❌ Algunas tablas no existen. Ejecuta el script: assets/db/install_repositorio.sql</div>";
}

// Verificar archivos de API
echo "<h2>2. Verificación de Archivos de API</h2>";
echo "<table>";
echo "<tr><th>Archivo</th><th>Estado</th></tr>";

$archivos_api = [
    'assets/API/repositorio/upload.php',
    'assets/API/repositorio/listar.php',
    'assets/API/repositorio/eliminar.php',
    'assets/API/repositorio/actualizar-estudiante.php'
];

$todos_archivos_ok = true;

foreach ($archivos_api as $archivo) {
    if (file_exists($archivo)) {
        echo "<tr><td>$archivo</td><td class='status-ok'>✅ Existe</td></tr>";
    } else {
        echo "<tr><td>$archivo</td><td class='status-fail'>❌ No existe</td></tr>";
        $todos_archivos_ok = false;
    }
}

echo "</table>";

if (!$todos_archivos_ok) {
    echo "<div class='error'>❌ Algunos archivos de API no existen. Verifica la instalación.</div>";
}

// Verificar páginas
echo "<h2>3. Verificación de Páginas</h2>";
echo "<table>";
echo "<tr><th>Archivo</th><th>Estado</th></tr>";

$archivos_paginas = [
    'pages/repositorio/index.php'
];

foreach ($archivos_paginas as $archivo) {
    if (file_exists($archivo)) {
        echo "<tr><td>$archivo</td><td class='status-ok'>✅ Existe</td></tr>";
    } else {
        echo "<tr><td>$archivo</td><td class='status-fail'>❌ No existe</td></tr>";
    }
}

echo "</table>";

// Verificar carpeta de uploads
echo "<h2>4. Verificación de Carpetas de Uploads</h2>";

$upload_dir = 'uploads/repositorio';

if (file_exists($upload_dir)) {
    if (is_writable($upload_dir)) {
        echo "<div class='success'>✅ Carpeta '$upload_dir' existe y tiene permisos de escritura</div>";
    } else {
        echo "<div class='warning'>⚠️ Carpeta '$upload_dir' existe pero NO tiene permisos de escritura<br>";
        echo "Ejecuta: chmod 777 $upload_dir</div>";
    }
} else {
    echo "<div class='warning'>⚠️ Carpeta '$upload_dir' NO existe<br>";
    echo "Ejecuta: mkdir -p $upload_dir && chmod 777 $upload_dir</div>";
}

// Verificar módulo en navegación
echo "<h2>5. Verificación del Módulo en el Sistema</h2>";

$sql = $db->query("SELECT * FROM SYSTEM_MODULOS WHERE SLUG = 'repositorio'");
if ($db->rows($sql) > 0) {
    $modulo = $db->recorrer($sql);
    echo "<div class='success'>✅ Módulo registrado en el sistema";
    echo "<ul>";
    echo "<li><strong>Nombre:</strong> " . $modulo['NOMBRE'] . "</li>";
    echo "<li><strong>Descripción:</strong> " . $modulo['DESCRIPCION'] . "</li>";
    echo "<li><strong>Estado:</strong> " . ($modulo['ACTIVO'] == 'S' ? 'Activo' : 'Inactivo') . "</li>";
    echo "</ul></div>";
} else {
    echo "<div class='warning'>⚠️ Módulo NO registrado en SYSTEM_MODULOS. Ejecuta el script de instalación.</div>";
}

// Verificar estudiante de ejemplo
echo "<h2>6. Estudiante de Ejemplo</h2>";

$sql = $db->query("SELECT * FROM VRE_ESTUDIANTES WHERE MATRICULA = '1220593'");
if ($db->rows($sql) > 0) {
    $estudiante = $db->recorrer($sql);
    echo "<div class='success'>✅ Estudiante de ejemplo encontrado";
    echo "<ul>";
    echo "<li><strong>Matrícula:</strong> " . $estudiante['MATRICULA'] . "</li>";
    echo "<li><strong>Nombre:</strong> " . $estudiante['NOMBRE'] . " " . $estudiante['APELLIDO'] . "</li>";
    echo "<li><strong>Carrera:</strong> " . $estudiante['CARRERA'] . "</li>";
    echo "<li><strong>Semestre:</strong> " . $estudiante['SEMESTRE'] . "</li>";
    echo "</ul></div>";
} else {
    echo "<div class='info'>ℹ️ No se encontró el estudiante de ejemplo (1220593)</div>";
}

// Resumen final
echo "<h2>📋 Resumen de Verificación</h2>";

$total_checks = 0;
$passed_checks = 0;

// Contar verificaciones
$total_checks += count($tablas);
$passed_checks += ($todas_tablas_ok ? count($tablas) : 0);

$total_checks += count($archivos_api);
$passed_checks += ($todos_archivos_ok ? count($archivos_api) : 0);

$total_checks += 1; // carpeta uploads
$passed_checks += (file_exists($upload_dir) && is_writable($upload_dir) ? 1 : 0);

$total_checks += 1; // módulo en sistema
$sql = $db->query("SELECT * FROM SYSTEM_MODULOS WHERE SLUG = 'repositorio'");
$passed_checks += ($db->rows($sql) > 0 ? 1 : 0);

$porcentaje = round(($passed_checks / $total_checks) * 100);

echo "<div class='info'>";
echo "<strong>Verificaciones completadas:</strong> $passed_checks de $total_checks ($porcentaje%)<br>";
if ($porcentaje == 100) {
    echo "<h3 style='color: #28a745;'>🎉 ¡Instalación completa y funcionando correctamente!</h3>";
    echo "<p>Puedes acceder al módulo desde el menú de navegación: <strong>Repositorio de Fotos</strong></p>";
    echo "<p>Matrícula de ejemplo para probar: <strong>1220593</strong></p>";
} else {
    echo "<h3 style='color: #dc3545;'>⚠️ La instalación está incompleta</h3>";
    echo "<p>Revisa los errores anteriores y consulta el archivo REPOSITORIO_FOTOS_README.md</p>";
}
echo "</div>";

echo "
    </div>
</body>
</html>";
?>
