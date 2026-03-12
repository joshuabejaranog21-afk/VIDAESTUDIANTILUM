<?php
include('../cpanel/assets/API/db.php');
$db = new Conexion();

echo "<style>
body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
.success { background: #d4edda; color: #155724; padding: 15px; margin: 10px 0; border-radius: 5px; }
.error { background: #f8d7da; color: #721c24; padding: 15px; margin: 10px 0; border-radius: 5px; }
.info { background: #d1ecf1; color: #0c5460; padding: 15px; margin: 10px 0; border-radius: 5px; }
</style>";

echo "<h1>Creando Tabla VRE_INSTALACIONES</h1>";

// Crear tabla
$createTable = "CREATE TABLE IF NOT EXISTS `VRE_INSTALACIONES` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOMBRE` varchar(200) NOT NULL,
  `DESCRIPCION` text,
  `UBICACION` varchar(200) DEFAULT NULL,
  `CAPACIDAD` varchar(100) DEFAULT NULL,
  `HORARIO` varchar(200) DEFAULT NULL,
  `SERVICIOS` text DEFAULT NULL,
  `TIPO` enum('ACADEMICA','DEPORTIVA','CULTURAL','OTRO') DEFAULT 'OTRO',
  `IMAGEN_URL` varchar(500) DEFAULT NULL,
  `GALERIA` text DEFAULT NULL,
  `RESPONSABLE_NOMBRE` varchar(200) DEFAULT NULL,
  `RESPONSABLE_CONTACTO` varchar(200) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `TELEFONO` varchar(20) DEFAULT NULL,
  `DISPONIBLE_RESERVA` enum('S','N') DEFAULT 'N',
  `ACTIVO` enum('S','N') DEFAULT 'S',
  `ORDEN` int DEFAULT 0,
  `FECHA_CREACION` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

try {
    $db->query($createTable);
    echo "<div class='success'>✅ Tabla VRE_INSTALACIONES creada correctamente</div>";
} catch (Exception $e) {
    echo "<div class='error'>❌ Error al crear tabla: " . $e->getMessage() . "</div>";
    exit;
}

// Insertar datos de ejemplo
$inserts = [
    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Auditorio Principal', 'Auditorio con capacidad para eventos masivos, conferencias y presentaciones especiales.', 'Edificio Central, Planta Baja', '500 personas', 'Lunes a Viernes: 8:00 AM - 10:00 PM\nSábados: 9:00 AM - 6:00 PM', 'Sistema de sonido profesional, Proyección HD, Aire acondicionado, Escenario amplio', 'CULTURAL', 'Coordinación de Eventos', 'S', 1)",

    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Biblioteca Central', 'Biblioteca universitaria con amplio acervo bibliográfico, salas de estudio y computadoras.', 'Edificio de Biblioteca, 3 pisos', '200 personas', 'Lunes a Viernes: 7:00 AM - 11:00 PM\nSábados: 9:00 AM - 8:00 PM\nDomingos: 10:00 AM - 6:00 PM', 'WiFi gratuito, Computadoras, Cubículos de estudio, Salas grupales', 'ACADEMICA', 'Dirección de Biblioteca', 'S', 2)",

    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Gimnasio Universitario', 'Gimnasio completo con máquinas cardiovasculares, pesas y área de funcional.', 'Centro Deportivo, Ala Norte', '80 personas', 'Lunes a Viernes: 6:00 AM - 10:00 PM\nSábados: 8:00 AM - 8:00 PM\nDomingos: 8:00 AM - 4:00 PM', 'Vestidores, Regaderas, Casilleros, Entrenadores certificados', 'DEPORTIVA', 'Coordinación Deportiva', 'S', 3)",

    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Canchas de Fútbol', 'Dos canchas de fútbol profesional con pasto sintético y gradas.', 'Campus Deportivo, Área Sur', '100 espectadores por cancha', 'Lunes a Domingo: 6:00 AM - 10:00 PM', 'Iluminación nocturna, Gradas, Vestidores, Estacionamiento', 'DEPORTIVA', 'Coordinación Deportiva', 'S', 4)",

    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Cafetería Central', 'Cafetería principal del campus con diversas opciones de comida y bebida.', 'Edificio Central, Planta Baja', '150 personas', 'Lunes a Viernes: 7:00 AM - 8:00 PM\nSábados: 9:00 AM - 4:00 PM', 'Mesas amplias, WiFi, Enchufes, Terraza al aire libre', 'OTRO', 'Servicios Estudiantiles', 'S', 5)",

    "INSERT INTO `VRE_INSTALACIONES` (`NOMBRE`, `DESCRIPCION`, `UBICACION`, `CAPACIDAD`, `HORARIO`, `SERVICIOS`, `TIPO`, `RESPONSABLE_NOMBRE`, `ACTIVO`, `ORDEN`) VALUES ('Laboratorio de Cómputo', 'Laboratorio equipado con computadoras de última generación para estudiantes.', 'Edificio de Ingeniería, 2do piso', '40 computadoras', 'Lunes a Viernes: 7:00 AM - 10:00 PM\nSábados: 9:00 AM - 4:00 PM', 'Software especializado, Impresoras, Escáner, Internet de alta velocidad', 'ACADEMICA', 'Coordinación de TI', 'S', 6)"
];

$success = 0;
$errors = 0;

foreach ($inserts as $index => $insert) {
    try {
        $db->query($insert);
        echo "<div class='success'>✅ Instalación " . ($index + 1) . " insertada</div>";
        $success++;
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error en instalación " . ($index + 1) . ": " . $e->getMessage() . "</div>";
        $errors++;
    }
}

echo "<hr>";
echo "<h2>Resumen Final:</h2>";
echo "<div class='success'>✅ Instalaciones insertadas: $success</div>";
if ($errors > 0) {
    echo "<div class='error'>❌ Errores: $errors</div>";
}

echo "<div class='info' style='margin-top:20px;'>";
echo "<strong>¡Listo!</strong> Ahora puedes ir a ver las instalaciones:<br><br>";
echo "<a href='instalaciones.php' style='display:inline-block;background:#11cdef;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;font-weight:bold;'>→ Ver Instalaciones</a>";
echo "</div>";
?>
