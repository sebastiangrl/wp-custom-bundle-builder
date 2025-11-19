# Custom Bundle Builder for WooCommerce

Plugin de WordPress/WooCommerce que permite crear bundles personalizados con diseño minimalista y flat, completamente personalizable desde el admin.

## ✨ Características Principales

- 🎨 **Diseño Minimalista y Flat** - Interfaz moderna y limpia
- 🎨 **Personalización Completa** - Colores, border-radius y estilos configurables
- 📦 **Bundles Flexibles** - Permite bundles completos e incompletos
- 💰 **Cálculo Automático** - Precio total calculado en tiempo real
- 📱 **100% Responsive** - Funciona perfecto en todos los dispositivos
- ⚡️ **Optimizado** - Código limpio y rendimiento excepcional

## 📋 Requisitos

- WordPress 6.0 o superior
- WooCommerce 8.0 o superior (Probado con 10.3.4)
- PHP 7.4 o superior

## 🚀 Instalación

1. **Subir el plugin a WordPress:**
   - Copia toda la carpeta `wp-custom-bundle-builder` a `/wp-content/plugins/`
   - O comprime la carpeta en un ZIP y súbela desde el panel de WordPress

2. **Activar el plugin:**
   - Ve a WordPress Admin → Plugins
   - Busca "Custom Bundle Builder for WooCommerce"
   - Haz clic en "Activar"

## 🎯 Cómo Usar

### Crear un Producto Bundle

1. **Ir a Productos → Añadir Nuevo**

2. **Seleccionar tipo de producto:**
   - En el panel "Datos del producto"
   - Selecciona "Bundle Builder" en el desplegable de tipo de producto

3. **Configurar el Bundle:**
   Ve a la pestaña "Bundle Builder" donde encontrarás 3 secciones:

   ### ⚙️ Configuración General
   
   **Cantidad máxima de productos:**
   - Define cuántos productos puede seleccionar el cliente
   - Por defecto: 4
   
   **Permitir bundles incompletos:**
   - ✅ Activado: Cliente puede añadir bundles sin completar
   - ❌ Desactivado: Cliente DEBE completar el bundle
   - Por defecto: Activado
   
   **Ocultar precio hasta selección:**
   - Oculta el precio total hasta que se seleccionen productos

   ### 🎨 Personalización de Diseño
   
   **Color del contenedor:**
   - Color de fondo del panel del bundle
   - Por defecto: #2c3e50 (azul oscuro)
   - Usa el selector de color visual
   
   **Color del botón añadir:**
   - Color del botón "Añadir al Carrito"
   - Por defecto: #27ae60 (verde)
   
   **Color de acento:**
   - Color de botones +/- y elementos interactivos
   - Por defecto: #3498db (azul)
   
   **Border radius (px):**
   - Redondeo de esquinas (0 = cuadrado, 20 = muy redondeado)
   - Por defecto: 4px
   - Rango: 0-50px
   
   **Estilo de diseño:**
   - **Minimalista (Flat):** Sin sombras, diseño plano
   - **Con sombras suaves:** Efecto Material Design
   - **Con bordes:** Estilo tradicional con bordes de 2px

   ### 📦 Productos del Bundle
   
   **Productos permitidos:**
   - Busca y selecciona los productos disponibles
   - Puedes seleccionar múltiples productos
   - Solo estos productos aparecerán en la lista

4. **Publicar el producto**

## 🎨 Guía de Personalización

### Ejemplos de Diseño

#### Minimalista Moderno
```
Color Contenedor: #2c3e50
Color Botón: #27ae60
Color Acento: #3498db
Border Radius: 4px
Estilo: Minimalista (Flat)
```

#### Vibrante y Llamativo
```
Color Contenedor: #e74c3c
Color Botón: #f39c12
Color Acento: #3498db
Border Radius: 12px
Estilo: Con Sombras Suaves
```

#### Elegante y Oscuro
```
Color Contenedor: #0a0e27
Color Botón: #00d4ff
Color Acento: #7b2cbf
Border Radius: 2px
Estilo: Con Bordes
```

**Ver más ejemplos en:** [PERSONALIZATION-GUIDE.md](PERSONALIZATION-GUIDE.md)

## 💡 Comportamiento del Botón

### Con Bundles Incompletos PERMITIDOS ✅

| Productos | Texto del Botón | ¿Funciona? |
|-----------|-----------------|------------|
| 0 de 4 | "Selecciona Productos" | ❌ Deshabilitado |
| 2 de 4 | "Añadir Bundle Incompleto" | ✅ Añade al carrito |
| 4 de 4 | "✨ Añadir Bundle Completo" | ✅ Añade al carrito |

### Con Bundles Incompletos NO PERMITIDOS ❌

| Productos | Texto del Botón | ¿Funciona? |
|-----------|-----------------|------------|
| 0 de 4 | "Selecciona Productos" | ❌ Deshabilitado |
| 2 de 4 | "Añadir al Carrito" | ⚠️ Muestra advertencia |
| 4 de 4 | "✨ Añadir Bundle Completo" | ✅ Añade al carrito |

## 🖼️ Vista del Cliente

```
┌─────────────────────────────┬──────────────────────────────┐
│  TU BUNDLE (2/4)            │  PRODUCTOS DISPONIBLES       │
│  ┌────────────────────────┐ │  ┌────────────────────────┐  │
│  │ [Color Personalizado]  │ │  │ [Imagen] Producto A    │  │
│  │                        │ │  │ Descripción corta      │  │
│  │ [img] Café Americano   │ │  │ $10.00                 │  │
│  │ Cantidad: 2            │ │  │ [-] [2] [+]            │  │
│  │ $20.00            [×]  │ │  ├────────────────────────┤  │
│  │                        │ │  │ [Imagen] Croissant     │  │
│  │ Espacios restantes: 2  │ │  │ Delicioso croissant    │  │
│  │ Total: $20.00          │ │  │ $4.00                  │  │
│  │                        │ │  │ [-] [0] [+]            │  │
│  │ [Añadir Bundle]        │ │  └────────────────────────┘  │
│  └────────────────────────┘ │                              │
└─────────────────────────────┴──────────────────────────────┘
```

## 📱 Diseño Responsive

- **Desktop (>992px):** Layout en 2 columnas, contenedor sticky
- **Tablet (768-992px):** Layout en 1 columna, bundle abajo
- **Mobile (<768px):** Cards centradas, imágenes más pequeñas

## 📂 Estructura de Archivos

```
wp-custom-bundle-builder/
├── wp-custom-bundle-builder.php     # Archivo principal
├── README.md                        # Este archivo
├── PERSONALIZATION-GUIDE.md         # Guía de personalización
├── VISUAL-GUIDE.md                  # Guía visual
├── includes/
│   ├── class-bundle-product-type.php    # Tipo de producto
│   ├── class-wc-product-bundle-builder.php  # Clase del producto
│   ├── class-bundle-admin.php           # Panel admin
│   ├── class-bundle-ajax-handler.php    # Handlers AJAX
│   └── class-bundle-cart-handler.php    # Integración carrito
├── assets/
│   ├── css/
│   │   ├── bundle-builder.css       # Estilos frontend (minimalista)
│   │   └── admin-bundle.css         # Estilos admin
│   └── js/
│       ├── bundle-builder.js        # JavaScript frontend
│       └── admin-bundle.js          # JavaScript admin (color picker)
└── templates/
    └── bundle-builder-template.php  # Template HTML
```

## 🔧 Solución de Problemas

### El plugin no aparece
- ✅ Verifica que WooCommerce esté activo
- ✅ Revisa la versión de PHP (mínimo 7.4)

### No se muestran los productos
- ✅ Asegúrate de haber seleccionado productos en "Productos permitidos"
- ✅ Verifica que los productos estén publicados y con stock

### Los colores no se aplican
- ✅ Limpia la caché del navegador (Ctrl + F5)
- ✅ Verifica que hayas guardado el producto después de cambiar colores
- ✅ Comprueba que no haya CSS personalizado sobreescribiendo

### El botón dice "Error al añadir el bundle"
- ✅ **SOLUCIONADO:** Ahora puedes configurar si permitir bundles incompletos
- ✅ Si no permites incompletos, completa todos los espacios del bundle
- ✅ Si permites incompletos, se añadirá con los productos seleccionados

### AJAX no funciona
- ✅ Verifica que jQuery esté cargado
- ✅ Revisa la consola del navegador (F12) para errores
- ✅ Asegúrate de que los permisos del servidor permitan AJAX

## 📝 Notas Técnicas

### Variables CSS Personalizables

El plugin usa CSS Variables para fácil personalización:

```css
:root {
    --wcbb-container-color: #2c3e50;
    --wcbb-button-color: #27ae60;
    --wcbb-accent-color: #3498db;
    --wcbb-border-radius: 4px;
}
```

Estas se generan automáticamente desde el admin.

### Almacenamiento en el Carrito

```php
array(
    'wcbb_bundle_id' => 123,
    'wcbb_selected_products' => array(
        45 => 2,  // ID Producto => Cantidad
        67 => 1,
    ),
    'wcbb_bundle_price' => 35.00,
    'wcbb_bundle_config' => array(
        'max_quantity' => 4,
        'container_color' => '#2c3e50',
        // ...
    )
)
```

## 🆘 Soporte y Documentación

- 📖 **Guía de Personalización:** [PERSONALIZATION-GUIDE.md](PERSONALIZATION-GUIDE.md)
- 🖼️ **Guía Visual:** [VISUAL-GUIDE.md](VISUAL-GUIDE.md)
- 📝 **Changelog:** [CHANGELOG.md](CHANGELOG.md)

## 📄 Licencia

GPL v2 o posterior

## ✨ Changelog v1.1.0

### 🎨 Nuevas Características
- ✅ Diseño completamente minimalista y flat
- ✅ Personalización de colores desde el admin (3 colores configurables)
- ✅ Border radius configurable (0-50px)
- ✅ 3 estilos de diseño: Minimalista, Sombras, Bordes
- ✅ Opción para permitir bundles incompletos
- ✅ Mensajes mejorados y contextuales
- ✅ Mejor feedback visual en tiempo real

### 🐛 Correcciones
- ✅ **CORREGIDO:** Error "Error al añadir el bundle al carrito" con bundles incompletos
- ✅ **MEJORADO:** Validación más inteligente del botón añadir
- ✅ **MEJORADO:** Textos dinámicos según estado del bundle
- ✅ **MEJORADO:** UX al añadir productos al bundle

---

**¡Disfruta creando bundles increíbles con diseño minimalista!** 🎉✨

