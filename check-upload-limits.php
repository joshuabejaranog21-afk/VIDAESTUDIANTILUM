<?php
/**
 * Script para verificar los límites de subida de archivos en PHP
 * Accede a este archivo desde: http://localhost/vidaEstudiantil/check-upload-limits.php
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Límites de Subida</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .error-box {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
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
            background: #667eea;
            color: white;
        }
        tr:hover {
            background: #f5f5f5;
        }
        .value {
            font-weight: bold;
            color: #667eea;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .instructions {
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Verificar Límites de Subida de PHP</h1>

        <?php
        $upload_max = ini_get('upload_max_filesize');
        $post_max = ini_get('post_max_size');
        $memory = ini_get('memory_limit');
        $max_execution = ini_get('max_execution_time');
        $max_input_time = ini_get('max_input_time');

        // Convertir a bytes para comparación
        function parse_size($size) {
            $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
            $size = preg_replace('/[^0-9\.]/', '', $size);
            if ($unit) {
                return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
            }
            return round($size);
        }

        $upload_max_bytes = parse_size($upload_max);
        $post_max_bytes = parse_size($post_max);
        $recommended_size = 100 * 1024 * 1024; // 100MB

        // Determinar el estado
        if ($upload_max_bytes >= $recommended_size && $post_max_bytes >= $recommended_size) {
            echo '<div class="success-box">';
            echo '<strong>✅ Configuración correcta!</strong><br>';
            echo 'Tu servidor está configurado para subir archivos grandes.';
            echo '</div>';
        } else {
            echo '<div class="error-box">';
            echo '<strong>❌ Configuración insuficiente</strong><br>';
            echo 'Los límites actuales son muy bajos para subir PDFs de anuarios (necesitas al menos 100MB).';
            echo '</div>';
        }
        ?>

        <h2>📊 Configuración Actual</h2>
        <table>
            <tr>
                <th>Configuración</th>
                <th>Valor Actual</th>
                <th>Recomendado</th>
                <th>Estado</th>
            </tr>
            <tr>
                <td><strong>upload_max_filesize</strong><br><small>Tamaño máximo por archivo</small></td>
                <td class="value"><?php echo $upload_max; ?></td>
                <td>100M</td>
                <td><?php echo $upload_max_bytes >= $recommended_size ? '✅' : '❌'; ?></td>
            </tr>
            <tr>
                <td><strong>post_max_size</strong><br><small>Tamaño máximo del POST</small></td>
                <td class="value"><?php echo $post_max; ?></td>
                <td>100M</td>
                <td><?php echo $post_max_bytes >= $recommended_size ? '✅' : '❌'; ?></td>
            </tr>
            <tr>
                <td><strong>memory_limit</strong><br><small>Memoria máxima PHP</small></td>
                <td class="value"><?php echo $memory; ?></td>
                <td>256M</td>
                <td><?php echo parse_size($memory) >= 256*1024*1024 ? '✅' : '⚠️'; ?></td>
            </tr>
            <tr>
                <td><strong>max_execution_time</strong><br><small>Tiempo máximo de ejecución</small></td>
                <td class="value"><?php echo $max_execution; ?>s</td>
                <td>300s</td>
                <td><?php echo $max_execution >= 300 ? '✅' : '⚠️'; ?></td>
            </tr>
            <tr>
                <td><strong>max_input_time</strong><br><small>Tiempo máximo de entrada</small></td>
                <td class="value"><?php echo $max_input_time; ?>s</td>
                <td>300s</td>
                <td><?php echo $max_input_time >= 300 ? '✅' : '⚠️'; ?></td>
            </tr>
        </table>

        <div class="info-box">
            <strong>ℹ️ Información:</strong><br>
            Tu archivo actual tiene <strong>97 MB</strong>, pero el límite está en <strong><?php echo $upload_max; ?></strong>.
        </div>

        <h2>🛠️ Cómo Solucionar el Problema</h2>

        <div class="instructions">
            <h3>Opción 1: Editar php.ini (Recomendado)</h3>
            <ol>
                <li>Haz clic en el <strong>ícono de WAMP</strong> en la barra de tareas</li>
                <li>Ve a: <strong>PHP → php.ini</strong></li>
                <li>Busca las siguientes líneas y cámbielas a:
                    <pre>
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300</pre>
                </li>
                <li><strong>Guarda el archivo</strong></li>
                <li><strong>Reinicia WAMP</strong> (clic derecho en el ícono → Restart All Services)</li>
                <li>Recarga esta página para verificar los cambios</li>
            </ol>
        </div>

        <div class="instructions">
            <h3>Opción 2: Usar URL en lugar de subir archivo</h3>
            <p>Si no puedes cambiar la configuración de PHP, puedes:</p>
            <ol>
                <li>Subir el PDF a Google Drive, Dropbox o similar</li>
                <li>Obtener el enlace público del archivo</li>
                <li>En el formulario de anuario, seleccionar <strong>"Usar URL"</strong></li>
                <li>Pegar el enlace del PDF</li>
            </ol>
        </div>

        <div class="warning-box">
            <strong>⚠️ Nota Importante:</strong><br>
            Después de modificar php.ini, <strong>DEBES reiniciar todos los servicios de WAMP</strong> para que los cambios surtan efecto.
        </div>

        <h2>📁 Ubicación del archivo php.ini</h2>
        <div class="info-box">
            <strong>Archivo actual:</strong><br>
            <code><?php echo php_ini_loaded_file(); ?></code>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
            <a href="<?php echo $_SERVER['HTTP_REFERER'] ?? '/vidaEstudiantil/pages/anuarios/admin/'; ?>"
               style="display: inline-block; background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
                ← Volver a Administrar Anuarios
            </a>
        </div>
    </div>
</body>
</html>
