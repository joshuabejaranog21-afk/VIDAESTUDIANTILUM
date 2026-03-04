# Sistema de Fotos de Estudiantes con Enlaces de Flickr

## ¿Qué se implementó?

Se ha implementado un sistema completo para gestionar fotos de estudiantes en anuarios usando **enlaces de Flickr** (o cualquier URL de imagen), evitando así tener que subir archivos al servidor.

---

## Características Principales

✅ **Sin subida de archivos** - Solo necesitas pegar el enlace de Flickr
✅ **Gestión individual** - Agregar/editar/eliminar fotos una por una
✅ **Importación masiva** - Importar múltiples fotos mediante CSV
✅ **Búsqueda por matrícula** - Los estudiantes pueden buscar sus propias fotos
✅ **Filtros avanzados** - Filtrar por anuario, año, matrícula o nombre
✅ **Vista previa** - Ver la imagen antes de guardar

---

## Archivos Creados

### 1. APIs (Backend)

**Ubicación:** `assets/API/anuarios/admin/fotos-estudiantes/`

- **`listar.php`** - Lista todas las fotos (con filtro opcional por anuario)
- **`crear.php`** - Agrega una nueva foto con URL de Flickr
- **`actualizar.php`** - Actualiza los datos de una foto existente
- **`eliminar.php`** - Elimina una foto

### 2. Interfaz Administrativa

**Ubicación:** `pages/anuarios/admin/fotos-estudiantes.php`

Panel de administración completo con:
- Tabla de todas las fotos
- Formulario para agregar/editar fotos individuales
- Sistema de importación masiva por CSV
- Filtros de búsqueda
- Vista previa de imágenes

### 3. Vista Pública (Ya existía, mejorada)

**Ubicación:** `pages/anuarios/mis-fotos.php`

Los estudiantes pueden:
- Buscar sus fotos por matrícula
- Filtrar por año
- Ver todas sus fotos en los diferentes anuarios

---

## Cómo Usar el Sistema

### Opción 1: Agregar Fotos Individualmente

1. **Accede al panel administrativo:**
   ```
   https://tu-dominio.com/pages/anuarios/admin/fotos-estudiantes.php
   ```

2. **Haz clic en "Agregar Foto"**

3. **Obtén el enlace de Flickr:**
   - Abre la foto en Flickr
   - Haz clic derecho sobre la imagen
   - Selecciona "Copiar dirección de imagen"
   - El formato será algo como: `https://live.staticflickr.com/65535/12345678_abcdef123.jpg`

4. **Completa el formulario:**
   - Selecciona el anuario
   - Ingresa la matrícula del estudiante
   - Pega la URL de Flickr
   - Opcionalmente: nombre, carrera, facultad

5. **Guarda** y la foto se agregará inmediatamente

### Opción 2: Importación Masiva (Recomendado para muchas fotos)

1. **Haz clic en "Importar Múltiples"**

2. **Prepara tus datos en formato CSV:**

```csv
matricula,nombre_estudiante,carrera,facultad,foto_url,anio
1220593,Juan Pérez,Ingeniería en Sistemas,Ingeniería,https://live.staticflickr.com/65535/foto1.jpg,2024
1220594,María García,Medicina,Salud,https://live.staticflickr.com/65535/foto2.jpg,2024
1220595,Carlos López,Arquitectura,Diseño,https://live.staticflickr.com/65535/foto3.jpg,2024
```

3. **Pasos:**
   - Descarga la plantilla CSV (botón en el modal)
   - Llena tus datos en Excel o Google Sheets
   - Copia todo y pega en el área de texto
   - Haz clic en "Validar Datos"
   - Si todo está correcto, haz clic en "Importar"

4. **El sistema importará todas las fotos automáticamente**

---

## Cómo Obtener Enlaces de Flickr

### ⚠️ IMPORTANTE: Álbum vs Fotos Individuales

Si tienes un enlace como este:
```
https://www.flickr.com/gp/universidaddemontemorelos/e24v5Vi63h
```

**Ese es un álbum completo**, no una foto individual. Necesitas extraer el enlace de CADA foto por separado.

### 🎯 Solución Rápida: Herramienta de Extracción

**Usa nuestra herramienta automática:**
```
pages/anuarios/admin/extraer-flickr.php
```

Esta herramienta te permite:
1. Ejecutar un script en la consola de Flickr
2. Extraer todas las URLs de fotos del álbum automáticamente
3. Asignar datos (matrícula, nombre) a cada foto
4. Exportar todo a CSV para importación masiva

---

### Método 1: Enlace Directo de Imagen (Individual)

1. Ve a Flickr y abre **UNA FOTO ESPECÍFICA** (no el álbum)
2. Clic derecho sobre la imagen → "Copiar dirección de imagen"
3. Obtendrás algo como:
   ```
   https://live.staticflickr.com/65535/53123456789_abc123def4_b.jpg
   ```

### Método 2: Desde el Álbum → Foto Individual

1. Abre el álbum: `https://www.flickr.com/gp/universidaddemontemorelos/e24v5Vi63h`
2. **Haz clic en UNA foto específica** del estudiante
3. Se abre la foto individual con URL:
   ```
   https://www.flickr.com/photos/universidaddemontemorelos/53123456789/
   ```
4. Ahora clic derecho en la imagen → "Copiar dirección de imagen"
5. Obtienes: `https://live.staticflickr.com/65535/53123456789_abc.jpg`
6. Repite para cada estudiante

### Método 3: Múltiples Pestañas (Más Rápido)

1. Abre el álbum
2. Haz **Ctrl+Clic** en varias fotos para abrirlas en pestañas
3. En cada pestaña, clic derecho → "Copiar dirección de imagen"
4. Pega todas las URLs juntas en un documento
5. Luego importa masivamente

**Nota:** Necesitas un enlace individual por cada estudiante.

---

## Estructura de la Base de Datos

La tabla `VRE_ANUARIOS_FOTOS_ESTUDIANTES` ya estaba creada y tiene estos campos:

```sql
ID                  - ID único de la foto
ID_ANUARIO         - ID del anuario al que pertenece
MATRICULA          - Matrícula del estudiante (requerida)
NOMBRE_ESTUDIANTE  - Nombre completo
CARRERA            - Carrera del estudiante
FACULTAD           - Facultad
FOTO_URL           - URL de Flickr (campo principal)
ANIO               - Año de la foto
ACTIVO             - S/N (si está visible o no)
```

---

## Ejemplo de Uso Completo

### Escenario: Tienes 500 fotos en Flickr del anuario 2024

1. **Organiza tus datos en Excel:**
   ```
   | Matrícula | Nombre          | Carrera              | Facultad    | URL Flickr                                      | Año  |
   |-----------|-----------------|----------------------|-------------|-------------------------------------------------|------|
   | 1220593   | Juan Pérez      | Ing. Sistemas        | Ingeniería  | https://live.staticflickr.com/.../foto1.jpg     | 2024 |
   | 1220594   | María García    | Medicina             | Salud       | https://live.staticflickr.com/.../foto2.jpg     | 2024 |
   ```

2. **Exporta como CSV** o simplemente copia todo

3. **Ve al sistema → Importar Múltiples**

4. **Selecciona el anuario 2024**

5. **Pega los datos y valida**

6. **Importa** - El sistema procesará las 500 fotos automáticamente

7. **Listo!** Ahora los estudiantes pueden buscar sus fotos por matrícula

---

## Para los Estudiantes

### Cómo buscar tu foto

1. Ve a: `https://tu-dominio.com/pages/anuarios/mis-fotos.php`

2. Ingresa tu matrícula (ej: 1220593)

3. Opcionalmente filtra por año

4. Haz clic en "Buscar"

5. Verás todas tus fotos organizadas por año

6. Puedes hacer clic en "Ver Anuario" para ver el anuario completo

---

## Ventajas de Usar Enlaces de Flickr

✅ **Cero ocupación de espacio** en tu servidor
✅ **Flickr optimiza** las imágenes automáticamente
✅ **CDN de Flickr** = carga ultrarrápida desde cualquier parte del mundo
✅ **Fácil de actualizar** - Solo cambias el enlace si necesitas modificar la foto
✅ **No necesitas código de subida** de archivos
✅ **Backups automáticos** - Flickr se encarga de eso

---

## URLs de Acceso

### Para Administradores:

- **Gestionar Anuarios:**
  `pages/anuarios/admin/index.php`

- **Gestionar Fotos de Estudiantes:**
  `pages/anuarios/admin/fotos-estudiantes.php`

- **🆕 Extraer Fotos de Álbum de Flickr:**
  `pages/anuarios/admin/extraer-flickr.php`
  (Herramienta para extraer todas las fotos de un álbum automáticamente)

### Para Estudiantes:

- **Ver Mis Fotos:**
  `pages/anuarios/mis-fotos.php`

- **Ver Anuarios Públicos:**
  `pages/anuarios/index.php`

---

## APIs Disponibles

### Admin APIs (requieren autenticación)

```
GET  /assets/API/anuarios/admin/fotos-estudiantes/listar.php
     ?id_anuario=1  (opcional)

POST /assets/API/anuarios/admin/fotos-estudiantes/crear.php
     Parámetros: id_anuario, matricula, nombre_estudiante, carrera,
                 facultad, foto_url, anio, activo

POST /assets/API/anuarios/admin/fotos-estudiantes/actualizar.php
     Parámetros: id, id_anuario, matricula, nombre_estudiante, carrera,
                 facultad, foto_url, anio, activo

DELETE /assets/API/anuarios/admin/fotos-estudiantes/eliminar.php
       ?id=123
```

### Public APIs

```
GET /assets/API/anuarios/mis-fotos.php
    ?matricula=1220593
    &anio=2024  (opcional)
    &id_anuario=1  (opcional)
```

---

## Solución de Problemas

### "La URL no es válida"
- Verifica que la URL comience con `http://` o `https://`
- Prueba copiar nuevamente el enlace desde Flickr

### "Ya existe una foto para esta matrícula"
- Cada estudiante solo puede tener una foto por anuario
- Si necesitas cambiarla, edita la existente en lugar de crear una nueva

### "No se encontraron fotografías"
- Verifica que la matrícula esté escrita correctamente
- Asegúrate de que las fotos estén marcadas como "Activas"

### La foto no se muestra
- Verifica que el enlace de Flickr aún sea válido
- Algunos enlaces de Flickr pueden tener permisos de privacidad

---

## Próximos Pasos Recomendados

1. **Importar fotos existentes** - Si ya tienes fotos en el servidor, puedes migrarlas poco a poco
2. **Establecer convención de nombres** en Flickr para facilitar la organización
3. **Crear álbumes en Flickr** por año/anuario para mejor organización
4. **Capacitar al personal** sobre cómo obtener los enlaces de Flickr

---

## Soporte

Si tienes alguna duda o problema:
1. Revisa este documento
2. Verifica los logs del navegador (F12 → Console)
3. Contacta al administrador del sistema

---

**Implementado:** 2025
**Versión:** 1.0
**Base de datos:** pruebasumadmin
