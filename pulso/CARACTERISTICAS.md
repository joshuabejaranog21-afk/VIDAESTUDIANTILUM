# 📋 Características Completas del Módulo Pulso

## ✨ Características Principales

### 1. 📸 Sistema de Subida de Fotografías
- **Subir archivos desde la computadora** ⬆️
  - Formatos: JPG, JPEG, PNG, GIF, WEBP
  - Tamaño máximo: 5MB
  - Vista previa en tiempo real
  - Nombres únicos automáticos
  - Almacenamiento en `uploads/pulso/fotos/`

- **Usar URLs externas**
  - Soporte para imágenes alojadas en otros sitios
  - Validación de URLs
  - Fallback a imagen por defecto

### 2. 🔍 Filtro por Año
- Selector dinámico de años disponibles
- Filtro "Todos" para ver todo el equipo
- Actualización instantánea sin recargar página
- Badges interactivos con animación

### 3. 👥 Vista Pública (`/pulso/`)
- Diseño en tarjetas responsive
- Información por colaborador:
  - Foto de perfil circular
  - Nombre completo
  - Cargo/Posición
  - Periodo de trabajo
  - Biografía (opcional)
- Animaciones suaves al hacer hover
- Compatible con móviles y tablets

### 4. ⚙️ Panel de Administración (`/pulso/admin.php`)

#### Tabla de Datos
- DataTables con funciones avanzadas:
  - Búsqueda en tiempo real
  - Ordenamiento por columnas
  - Paginación
  - Exportar datos
- Columnas mostradas:
  - ID
  - **Miniatura de foto** (circular, 50x50px)
  - Nombre
  - Cargo
  - Año
  - Periodo
  - Estado (badge activo/inactivo)
  - Acciones (editar/eliminar)

#### Formulario Modal
- Campos disponibles:
  - ✅ Nombre completo (requerido)
  - ✅ Cargo (requerido)
  - ✅ Año (requerido, numérico)
  - Periodo (ej: "Enero - Junio 2024")
  - Fotografía (archivo o URL)
  - Biografía (texto largo)
  - Orden de visualización (numérico)
  - Estado (activo/inactivo)

- **Selector de tipo de foto:**
  - Radio buttons para elegir entre archivo o URL
  - Cambio dinámico entre opciones
  - Vista previa antes de guardar

### 5. 🔒 Seguridad
- Validación de sesiones con función `security()`
- Protección contra archivos PHP en uploads
- Validación de tipos de archivo
- Validación de tamaño de archivo
- Escape de caracteres en SQL
- .htaccess configurado para seguridad

### 6. 🎨 Diseño y UX
- CSS modular en archivo `pulso.css`
- Animaciones suaves y transiciones
- Hover effects en tarjetas
- Badges coloridos para estados
- Iconos intuitivos
- Responsive design completo
- Loading spinners durante carga

### 7. 📊 APIs REST Completas

| API | Método | Descripción |
|-----|--------|-------------|
| `leer.php` | GET | Obtener todos los colaboradores (con filtro opcional por año) |
| `crear.php` | POST | Crear nuevo colaborador |
| `actualizar.php` | POST | Actualizar colaborador existente |
| `borrar.php` | POST | Eliminar (desactivar) colaborador |
| `especifico.php` | GET | Obtener un colaborador por ID |
| `anios.php` | GET | Obtener lista de años disponibles |
| `upload_foto.php` | POST | Subir fotografía |

### 8. 🔄 Funciones CRUD Completas
- **Create**: Formulario completo con validaciones
- **Read**: Vista pública y tabla en admin
- **Update**: Edición inline con modal
- **Delete**: Soft delete (marca como inactivo)

### 9. 📱 Responsive Design
- Adaptable a todos los tamaños de pantalla
- Grid flexible de 1-3 columnas según dispositivo
- Botones y badges optimizados para móvil
- Tabla scrolleable en pantallas pequeñas

### 10. ⚡ Rendimiento
- Carga asíncrona con Fetch API
- No recarga de página en filtros
- Imágenes optimizadas con lazy loading
- Consultas SQL optimizadas

## 🛠️ Tecnologías Utilizadas

### Backend
- PHP 7.4+
- MySQL/MariaDB
- MySQLi (prepared statements)

### Frontend
- HTML5
- CSS3 (animaciones, transitions, flexbox, grid)
- JavaScript ES6+ (async/await, fetch, arrow functions)
- jQuery (solo para DataTables)
- Bootstrap 5
- DataTables

### Librerías
- Bootstrap 5 (UI framework)
- DataTables (tablas avanzadas)
- FileReader API (vista previa de imágenes)
- FormData API (subida de archivos)

## 📁 Estructura de Archivos

```
vidaEstudiantil/
│
├── assets/
│   └── API/
│       └── pulso/
│           ├── leer.php           (API: Leer colaboradores)
│           ├── crear.php          (API: Crear colaborador)
│           ├── actualizar.php     (API: Actualizar colaborador)
│           ├── borrar.php         (API: Eliminar colaborador)
│           ├── especifico.php     (API: Obtener uno específico)
│           ├── anios.php          (API: Obtener años)
│           └── upload_foto.php    (API: Subir fotos)
│
├── pulso/
│   ├── index.php                  (Vista pública)
│   ├── admin.php                  (Panel de administración)
│   ├── pulso.css                  (Estilos del módulo)
│   ├── README.md                  (Documentación técnica)
│   ├── INSTALACION.md             (Guía de instalación)
│   ├── CARACTERISTICAS.md         (Este archivo)
│   └── verificar_instalacion.php  (Script de verificación)
│
└── uploads/
    └── pulso/
        └── fotos/
            ├── .htaccess          (Seguridad)
            ├── .gitignore         (Control de versiones)
            └── [fotos subidas]
```

## 🎯 Casos de Uso

### 1. Universidad/Institución Educativa
- Mostrar equipo docente por año académico
- Historial de coordinadores de carrera
- Staff administrativo por periodo

### 2. Empresa/Startup
- Equipo fundador y colaboradores
- Empleados por año de ingreso
- Historial de cambios de personal

### 3. ONG/Organización
- Voluntarios y colaboradores
- Equipos de proyectos específicos
- Directivas por periodo

### 4. Proyecto Open Source
- Contributors principales
- Mantenedores por año
- Equipo de desarrollo

## 📈 Funcionalidades Futuras (Opcionales)

- [ ] Exportar colaboradores a PDF
- [ ] Importar desde Excel/CSV
- [ ] Múltiples fotos por colaborador
- [ ] Integración con redes sociales
- [ ] Sistema de roles y permisos
- [ ] Estadísticas y reportes
- [ ] Notificaciones por email
- [ ] Historial de cambios (audit log)

## 🏆 Ventajas del Módulo

✅ **Fácil de usar**: Interfaz intuitiva
✅ **Completo**: CRUD completo con todas las funciones
✅ **Seguro**: Validaciones y protecciones implementadas
✅ **Rápido**: Carga asíncrona sin recargas
✅ **Moderno**: Diseño actual y responsive
✅ **Flexible**: Adaptable a diferentes necesidades
✅ **Documentado**: Documentación completa incluida
✅ **Mantenible**: Código limpio y organizado

---

**Versión**: 1.0.0
**Última actualización**: 2025
**Desarrollado para**: Vida Estudiantil - Sistema de Gestión
