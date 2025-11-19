# 📦 INSTALACIÓN RÁPIDA

## Pasos para instalar en WordPress

### Opción 1: Subir vía FTP/SFTP
```bash
1. Comprime la carpeta wp-custom-bundle-builder en un ZIP
2. Conéctate a tu servidor por FTP
3. Navega a /wp-content/plugins/
4. Sube la carpeta wp-custom-bundle-builder
5. Ve a WordPress Admin → Plugins → Activar "Custom Bundle Builder"
```

### Opción 2: Subir desde WordPress Admin
```bash
1. Comprime la carpeta wp-custom-bundle-builder en un ZIP
2. Ve a WordPress Admin → Plugins → Añadir Nuevo
3. Haz clic en "Subir Plugin"
4. Selecciona el archivo ZIP
5. Haz clic en "Instalar ahora"
6. Activa el plugin
```

---

## ✅ CHECKLIST DE INSTALACIÓN

- [ ] WordPress 6.0+ instalado
- [ ] WooCommerce 8.0+ activo
- [ ] PHP 7.4+ en el servidor
- [ ] Plugin subido a /wp-content/plugins/
- [ ] Plugin activado
- [ ] No hay errores en WordPress Admin

---

## 🎯 PRIMER USO

### 1. Crear tu primer Bundle (5 minutos)

**Paso 1:** Ir a Productos → Añadir Nuevo

**Paso 2:** Configurar:
- Nombre: "Bundle Personalizado"
- Tipo de producto: "Bundle Builder"

**Paso 3:** En pestaña "Bundle Builder":
- Cantidad máxima: 4
- Color: #3498db (o el que prefieras)
- Productos: Selecciona 3-5 productos de tu tienda

**Paso 4:** Publicar

**Paso 5:** Ver el producto en el frontend

---

## 🎨 RESULTADO ESPERADO

Deberías ver:

```
┌─────────────────────────────────────────────────┐
│              BUNDLE PERSONALIZADO                │
├──────────────────────┬──────────────────────────┤
│  TU BUNDLE (0/4)     │  PRODUCTOS DISPONIBLES   │
│  ┌─────────────────┐ │  ┌────────────────────┐  │
│  │                 │ │  │ Producto 1  $10.00 │  │
│  │  Selecciona     │ │  │ [-] [0] [+]        │  │
│  │  productos →    │ │  ├────────────────────┤  │
│  │                 │ │  │ Producto 2  $15.00 │  │
│  │  Espacios: 4    │ │  │ [-] [0] [+]        │  │
│  │  Total: $0.00   │ │  ├────────────────────┤  │
│  │                 │ │  │ Producto 3  $12.00 │  │
│  │  [Añadir]       │ │  │ [-] [0] [+]        │  │
│  └─────────────────┘ │  └────────────────────┘  │
└──────────────────────┴──────────────────────────┘
```

---

## 🔍 VERIFICACIÓN

### Frontend:
1. ✅ Se muestra el layout de 2 columnas
2. ✅ Los botones +/- funcionan
3. ✅ El contador se actualiza (X/4)
4. ✅ El precio total se calcula
5. ✅ Se puede añadir al carrito

### Admin:
1. ✅ Aparece "Bundle Builder" en tipos de producto
2. ✅ Se muestra la pestaña "Bundle Builder"
3. ✅ El selector de color funciona
4. ✅ Se pueden seleccionar productos

---

## 🐛 SI ALGO NO FUNCIONA

### Problema: No aparece el tipo "Bundle Builder"
```
Solución:
1. Desactiva y reactiva el plugin
2. Limpia la caché de WordPress
3. Verifica que WooCommerce esté activo
```

### Problema: Layout roto o sin estilos
```
Solución:
1. Limpia caché del navegador (Ctrl+Shift+R)
2. Verifica que los archivos CSS estén en:
   /wp-content/plugins/wp-custom-bundle-builder/assets/css/
3. Ve a Settings → Permalinks → Guardar (flush rewrite rules)
```

### Problema: Botones no funcionan
```
Solución:
1. Abre la consola del navegador (F12)
2. Verifica errores de JavaScript
3. Asegúrate que jQuery esté cargado
4. Verifica que el archivo JS esté en:
   /wp-content/plugins/wp-custom-bundle-builder/assets/js/
```

### Problema: AJAX error al añadir al carrito
```
Solución:
1. Verifica permisos de archivos (644 para archivos, 755 para carpetas)
2. Desactiva otros plugins temporalmente
3. Cambia al tema Twenty Twenty-Four temporalmente
4. Revisa error_log de PHP
```

---

## 📞 SOPORTE TÉCNICO

### Debug Mode
Activa el modo debug de WordPress:

En wp-config.php:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Los errores se guardarán en: `/wp-content/debug.log`

---

## 🚀 PRÓXIMOS PASOS

1. ✅ **Instalar y activar el plugin**
2. ✅ **Crear tu primer bundle**
3. ✅ **Personalizar colores según tu marca**
4. ✅ **Añadir productos permitidos**
5. ✅ **Probar en frontend**
6. ⭐ **Personalizar con snippets (ver EXAMPLES.md)**
7. ⭐ **Aplicar descuentos especiales**
8. ⭐ **Optimizar imágenes**

---

## 📚 DOCUMENTACIÓN

- **README.md** - Documentación completa
- **EXAMPLES.md** - Ejemplos y snippets
- **CHANGELOG.md** - Historial de versiones

---

## 🎉 ¡TODO LISTO!

Tu plugin está completo y listo para usar. 

**Características incluidas:**
✅ Layout de 2 columnas responsive
✅ Límite de productos personalizable
✅ Selector de color del contenedor
✅ Suma automática de precios
✅ Validación frontend + backend
✅ Integración total con WooCommerce
✅ Panel de administración completo
✅ Compatible con WordPress 6.8.3 y WooCommerce 10.3.4

**Disfruta creando bundles increíbles!** 🎁
