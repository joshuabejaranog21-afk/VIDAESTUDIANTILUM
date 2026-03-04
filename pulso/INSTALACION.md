# Guía de Instalación Rápida - Módulo Pulso

## 🚀 Pasos de Instalación

### 1. Verificar que la tabla existe
La tabla `VRE_PULSO_EQUIPOS` ya debe estar creada en tu base de datos. Si no existe, ejecuta:

```sql
CREATE TABLE VRE_PULSO_EQUIPOS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    NOMBRE VARCHAR(200) NOT NULL,
    CARGO VARCHAR(100) NOT NULL,
    ANIO YEAR NOT NULL,
    PERIODO VARCHAR(50),
    FOTO_URL VARCHAR(500),
    BIO TEXT,
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S','N') DEFAULT 'S',
    FECHA_CREACION DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=INNODB;
```

### 2. Verificar permisos de carpetas
Asegúrate de que la carpeta de uploads tenga permisos de escritura:

**En Windows (WAMP/XAMPP):**
- No necesitas hacer nada, los permisos ya están configurados

**En Linux/Mac:**
```bash
chmod -R 755 uploads/pulso/fotos/
```

### 3. Verificar instalación
Accede a: `http://localhost/vidaEstudiantil/pulso/verificar_instalacion.php`

Este script verificará:
- ✓ Todos los archivos necesarios
- ✓ Permisos de carpetas
- ✓ Conexión a base de datos
- ✓ Existencia de la tabla

### 4. Agregar datos de prueba (Opcional)
```sql
INSERT INTO VRE_PULSO_EQUIPOS (NOMBRE, CARGO, ANIO, PERIODO, ACTIVO) VALUES
('Juan Pérez', 'Desarrollador Frontend', 2024, 'Enero - Actualidad', 'S'),
('María González', 'Coordinadora', 2024, 'Marzo - Actualidad', 'S'),
('Carlos Ramírez', 'Diseñador UX/UI', 2023, 'Todo el año', 'S');
```

## 📁 Archivos Creados

```
vidaEstudiantil/
├── assets/
│   └── API/
│       └── pulso/
│           ├── leer.php          # Obtener colaboradores
│           ├── crear.php         # Crear colaborador
│           ├── actualizar.php    # Actualizar colaborador
│           ├── borrar.php        # Eliminar colaborador
│           ├── anios.php         # Obtener años disponibles
│           └── upload_foto.php   # Subir fotografías
├── pulso/
│   ├── index.php                 # Vista pública
│   ├── admin.php                 # Panel de administración
│   ├── README.md                 # Documentación completa
│   ├── INSTALACION.md           # Esta guía
│   └── verificar_instalacion.php # Script de verificación
└── uploads/
    └── pulso/
        └── fotos/                # Carpeta para fotos subidas
            ├── .htaccess         # Seguridad
            └── .gitignore        # Control de versiones
```

## 🎯 Acceso al Sistema

### Vista Pública
📍 `http://localhost/vidaEstudiantil/pulso/`
- Ver colaboradores
- Filtrar por año
- Información detallada de cada colaborador

### Panel de Administración
📍 `http://localhost/vidaEstudiantil/pulso/admin.php`
- Crear/Editar/Eliminar colaboradores
- **Subir fotos desde tu computadora** ⬆️
- Usar URLs de imágenes externas
- Gestionar orden de visualización

## 📸 Cómo Subir Fotos

### Opción 1: Subir Archivo (Recomendado)
1. En el panel de admin, haz clic en "Nuevo Colaborador"
2. Selecciona la opción "Subir archivo"
3. Haz clic en "Elegir archivo"
4. Selecciona una imagen de tu computadora
5. Verás una vista previa automática
6. Completa los demás datos y guarda

**Formatos permitidos:** JPG, JPEG, PNG, GIF, WEBP
**Tamaño máximo:** 5MB

### Opción 2: Usar URL Externa
1. Selecciona la opción "Usar URL"
2. Pega la URL completa de la imagen
3. Ejemplo: `https://ejemplo.com/foto.jpg`

## ⚙️ Características Principales

✅ **Subida de archivos desde la computadora**
✅ Vista previa de imágenes antes de guardar
✅ Filtro dinámico por año
✅ Panel de administración completo
✅ Diseño responsive
✅ Validación de sesiones
✅ Seguridad en uploads

## 🔧 Solución de Problemas

### Error: "No se puede subir la imagen"
- Verifica que `uploads/pulso/fotos/` tenga permisos de escritura
- Verifica que la imagen no sea mayor a 5MB
- Verifica que el formato sea JPG, PNG, GIF o WEBP

### Error: "Método de acceso incorrecto"
- Asegúrate de estar logueado en el sistema
- Verifica que las cookies estén habilitadas

### No se muestran las imágenes
- Verifica que la ruta en `FOTO_URL` sea correcta
- Para archivos subidos debe ser: `uploads/pulso/fotos/nombre_archivo.jpg`
- Para URLs externas debe ser: `https://ejemplo.com/imagen.jpg`

## 📞 Soporte

Si encuentras algún problema:
1. Ejecuta `verificar_instalacion.php`
2. Revisa los errores mostrados
3. Consulta el README.md para más detalles

---

**¡Listo!** Ya puedes empezar a usar el módulo Pulso 🎉
