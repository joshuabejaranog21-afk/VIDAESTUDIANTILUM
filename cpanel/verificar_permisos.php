<?php
include('assets/API/db.php');
$db = new Conexion();

echo "<h2>Módulos en SYSTEM_MODULOS:</h2>";
$sql = $db->query("SELECT * FROM SYSTEM_MODULOS ORDER BY ID");
echo "<table border='1' style='border-collapse:collapse;'>";
echo "<tr><th>ID</th><th>NOMBRE</th><th>SLUG</th><th>DESCRIPCION</th><th>ACTIVO</th></tr>";
while ($row = $db->recorrer($sql)) {
    echo "<tr>";
    echo "<td>{$row['ID']}</td>";
    echo "<td>{$row['NOMBRE']}</td>";
    echo "<td><strong>{$row['SLUG']}</strong></td>";
    echo "<td>{$row['DESCRIPCION']}</td>";
    echo "<td>{$row['ACTIVO']}</td>";
    echo "</tr>";
}
echo "</table>";

echo "<hr>";
echo "<h2>Crear módulo 'home' si no existe:</h2>";

// Verificar si existe
$check = $db->query("SELECT * FROM SYSTEM_MODULOS WHERE SLUG = 'home'");
if ($db->rows($check) > 0) {
    echo "<p style='color:green;'>✅ El módulo 'home' YA EXISTE</p>";
} else {
    echo "<p style='color:orange;'>⚠️ El módulo 'home' NO EXISTE. Creando...</p>";

    $insert = "INSERT INTO SYSTEM_MODULOS (NOMBRE, SLUG, DESCRIPCION, ICONO, ACTIVO)
               VALUES ('Configuración Home', 'home', 'Configuración del sitio público', 'home', 'S')";

    if ($db->query($insert)) {
        echo "<p style='color:green;'>✅ Módulo 'home' creado exitosamente</p>";

        // Dar permisos al superusuario
        $moduleId = $db->insert_id;
        $db->query("INSERT INTO SYSTEM_PERMISOS (ID_ROL, ID_MODULO, VER, CREAR, EDITAR, ELIMINAR)
                    VALUES (1, $moduleId, 'S', 'S', 'S', 'S')");

        echo "<p style='color:green;'>✅ Permisos otorgados al rol 1 (superusuario)</p>";
    } else {
        echo "<p style='color:red;'>❌ Error al crear módulo: " . $db->error . "</p>";
    }
}

echo "<hr>";
echo "<a href='configuracion/home/' style='background:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>→ Ir a Configuración Home</a>";
?>
