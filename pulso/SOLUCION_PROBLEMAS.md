# 🔧 Solución de Problemas - Módulo Pulso

## ❌ Problema: "Las fotos no se muestran en la tabla"

### Diagnóstico
1. **Ejecuta el script de prueba:**
   ```
   http://localhost/vidaEstudiantil/pulso/test_fotos.php
   ```
   Este script te mostrará:
   - Las rutas de las fotos en la base de datos
   - Las rutas completas que se están generando
   - Vista previa de cada foto
   - Estado de la carpeta uploads

### Causas Comunes

#### 1. **El campo FOTO_URL está vacío o NULL**
**Solución:**
- Las fotos se mostrarán con la imagen por defecto (avatar gris)
- Para agregar fotos:
  1. Ve a `pulso/admin.php`
  2. Edita el colaborador
  3. Sube una foto o agrega una URL

#### 2. **La ruta en FOTO_URL no es correcta**
**Verificar en la base de datos:**
```sql
SELECT ID, NOMBRE, FOTO_URL FROM VRE_PULSO_EQUIPOS;
```

**Rutas válidas:**
- `uploads/pulso/fotos/colaborador_123456.jpg` (ruta relativa)
- `https://ejemplo.com/foto.jpg` (URL completa)

**Rutas INCORRECTAS:**
- `/uploads/pulso/fotos/foto.jpg` (no debe empezar con /)
- `C:\wamp\uploads\foto.jpg` (ruta absoluta del servidor)

#### 3. **Los archivos no se subieron correctamente**
**Verificar:**
1. Navega a: `C:\WAMP64\www\vidaEstudiantil\uploads\pulso\fotos\`
2. Verifica que existan archivos con nombres como: `colaborador_12345678_1234567890.jpg`

**Si no hay archivos:**
- Verifica permisos de escritura
- Revisa que el formulario permita subir archivos
- Checa el tamaño del archivo (máximo 5MB)

#### 4. **Error de permisos en uploads**
**En Windows (WAMP):**
- Normalmente no hay problemas de permisos
- Si hay error, ejecuta WAMP como administrador

**En Linux:**
```bash
sudo chmod -R 755 uploads/pulso/fotos/
sudo chown -R www-data:www-data uploads/pulso/fotos/
```

## 🔍 Cómo Corregir Datos Existentes

### Si ya tienes colaboradores SIN fotos:

**Opción 1: Subir foto por el admin**
1. Ve a `pulso/admin.php`
2. Haz clic en editar (botón azul)
3. Selecciona "Subir archivo"
4. Elige una foto
5. Guarda

**Opción 2: Actualizar directamente en BD**
```sql
UPDATE VRE_PULSO_EQUIPOS
SET FOTO_URL = 'uploads/pulso/fotos/nombre_archivo.jpg'
WHERE ID = 1;
```

## 🖼️ Imagen por Defecto

Si no hay foto, se muestra: `pulso/default-avatar.svg`

**Para cambiar la imagen por defecto:**
1. Reemplaza el archivo `pulso/default-avatar.svg`
2. O edita las rutas en:
   - `admin.php` (línea ~209)
   - `index.php` (línea ~136)

## 🐛 Debugging Avanzado

### Ver errores de JavaScript
1. Abre la consola del navegador (F12)
2. Ve a la pestaña "Console"
3. Busca errores en rojo

### Ver errores de PHP
En `pulso/admin.php` y `pulso/index.php`, asegúrate que tengan:
```php
error_reporting(E_ALL);
ini_set('display_errors', '1');
```

### Verificar respuesta de API
En la consola del navegador (F12), pestaña "Network":
1. Recarga la página
2. Busca `leer.php`
3. Haz clic y ve a "Response"
4. Verifica que `FOTO_URL` tenga un valor

## 📝 Actualizar Fotos Masivamente

Si necesitas actualizar varios colaboradores:

```sql
-- Ver todos sin foto
SELECT ID, NOMBRE FROM VRE_PULSO_EQUIPOS WHERE FOTO_URL IS NULL OR FOTO_URL = '';

-- Poner foto por defecto a todos
UPDATE VRE_PULSO_EQUIPOS
SET FOTO_URL = 'https://via.placeholder.com/200?text=Sin+Foto'
WHERE FOTO_URL IS NULL OR FOTO_URL = '';
```

## 🎯 Checklist de Verificación

- [ ] La tabla `VRE_PULSO_EQUIPOS` existe
- [ ] La carpeta `uploads/pulso/fotos/` existe
- [ ] La carpeta tiene permisos de escritura
- [ ] El archivo `pulso/default-avatar.svg` existe
- [ ] Las rutas en la base de datos no empiezan con `/`
- [ ] El servidor web puede acceder a la carpeta uploads
- [ ] No hay errores en la consola del navegador
- [ ] La API `leer.php` devuelve datos correctos

## 💡 Tips

1. **Usa rutas relativas**, no absolutas
2. **No incluyas el siteURL** en la base de datos, solo la ruta relativa
3. **Verifica el tamaño** de las imágenes (no más de 5MB)
4. **Usa formatos estándar**: JPG, PNG, GIF, WEBP
5. **Ejecuta `test_fotos.php`** cuando tengas dudas

---

Si el problema persiste, ejecuta:
```
http://localhost/vidaEstudiantil/pulso/verificar_instalacion.php
```
