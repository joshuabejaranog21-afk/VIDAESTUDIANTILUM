# Módulo Pulso - Gestión de Equipo de Colaboración

## Descripción
Módulo para gestionar y mostrar el equipo de colaboración que ha trabajado en el sistema a lo largo de los años. Permite filtrar colaboradores por año y administrar su información.

## Características
- ✅ Visualización de colaboradores con foto, nombre, cargo y periodo
- ✅ Filtro por año para ver equipos específicos
- ✅ Panel de administración completo (CRUD)
- ✅ Diseño responsive y moderno
- ✅ Integración con el sistema de autenticación existente

## Instalación

### 1. Ejecutar Script SQL
Ejecuta el siguiente script en tu base de datos (si aún no existe la tabla):

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

### 2. Insertar Datos de Prueba (Opcional)
```sql
INSERT INTO VRE_PULSO_EQUIPOS (NOMBRE, CARGO, ANIO, PERIODO, ACTIVO) VALUES
('Juan Pérez González', 'Desarrollador Frontend', 2023, 'Enero - Junio 2023', 'S'),
('María González López', 'Coordinadora de Proyecto', 2024, 'Enero - Actualidad', 'S'),
('Carlos Ramírez', 'Diseñador UX/UI', 2023, 'Agosto - Diciembre 2023', 'S'),
('Ana Martínez', 'Desarrolladora Backend', 2024, 'Enero - Actualidad', 'S');
```

### 3. Verificar Rutas
Asegúrate de que los siguientes archivos estén en su lugar:

**APIs (assets/API/pulso/):**
- `leer.php` - Obtener colaboradores (con filtro opcional por año)
- `crear.php` - Crear nuevo colaborador
- `actualizar.php` - Actualizar colaborador existente
- `borrar.php` - Eliminar (desactivar) colaborador
- `anios.php` - Obtener lista de años disponibles
- `upload_foto.php` - Subir fotografías de colaboradores

**Páginas (pulso/):**
- `index.php` - Página pública de visualización
- `admin.php` - Panel de administración

**Carpetas:**
- `uploads/pulso/fotos/` - Almacena las fotografías subidas (debe tener permisos de escritura)

## Uso

### Vista Pública
Accede a: `http://tu-dominio/pulso/`

Aquí podrás:
- Ver todos los colaboradores
- Filtrar por año específico
- Ver información detallada de cada colaborador

### Panel de Administración
Accede a: `http://tu-dominio/pulso/admin.php`

Aquí podrás:
- Crear nuevos colaboradores
- Editar información existente
- Eliminar colaboradores
- Gestionar el orden de visualización
- **Subir fotografías desde tu computadora** (JPG, PNG, GIF, WEBP hasta 5MB)
- Usar URLs externas para las fotografías

## Estructura de Datos

### Campos de la Tabla

| Campo | Tipo | Descripción |
|-------|------|-------------|
| ID | INT | Identificador único |
| NOMBRE | VARCHAR(200) | Nombre completo del colaborador |
| CARGO | VARCHAR(100) | Cargo o posición |
| ANIO | YEAR | Año de colaboración |
| PERIODO | VARCHAR(50) | Periodo específico (ej: "Enero - Junio 2024") |
| FOTO_URL | VARCHAR(500) | URL de la fotografía |
| BIO | TEXT | Biografía o descripción breve |
| ORDEN | INT | Orden de visualización (menor = primero) |
| ACTIVO | ENUM('S','N') | Estado del registro |
| FECHA_CREACION | DATETIME | Fecha de creación del registro |

## Subida de Fotografías

### Dos Opciones Disponibles:

1. **Subir Archivo** (Recomendado)
   - Selecciona la opción "Subir archivo" en el formulario
   - Haz clic en "Elegir archivo" y selecciona una imagen desde tu computadora
   - Formatos permitidos: JPG, JPEG, PNG, GIF, WEBP
   - Tamaño máximo: 5MB
   - Vista previa automática antes de guardar
   - Las imágenes se guardan en `uploads/pulso/fotos/`

2. **Usar URL Externa**
   - Selecciona la opción "Usar URL"
   - Pega la URL completa de la imagen (ej: https://ejemplo.com/foto.jpg)
   - Útil para imágenes ya alojadas en otros sitios

### Permisos de Carpeta
Asegúrate de que la carpeta `uploads/pulso/fotos/` tenga permisos de escritura:
```bash
chmod 755 uploads/pulso/fotos/
```

## Notas Técnicas

- Las APIs usan la función `security()` para validar sesiones
- Los colaboradores eliminados se marcan como inactivos (ACTIVO='N')
- El filtro por año muestra colaboradores cuyo ANIO coincida exactamente
- Las fotos subidas se renombran con un ID único para evitar conflictos
- Las fotos tienen una imagen por defecto si no se proporciona ninguna

## Personalización

### Cambiar imagen por defecto
Edita la línea en `index.php`:
```javascript
const fotoUrl = colaborador.FOTO_URL || 'ruta/a/imagen/default.png';
```

### Ajustar orden de visualización
Modifica el campo ORDEN en la base de datos. Números menores aparecen primero.

## Soporte
Para reportar problemas o sugerencias, contacta al equipo de desarrollo.
