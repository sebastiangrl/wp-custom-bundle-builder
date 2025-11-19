# Changelog

Todos los cambios notables de este proyecto serán documentados en este archivo.

## [1.2.0] - 2025-11-12

### 🎨 Rediseño Completo Ultra Minimalista Blanco

#### Nuevo Layout de Columnas
- **Columnas invertidas:** Productos a la izquierda, Bundle a la derecha
- **Grid responsive:** 1fr 400px (productos | bundle visual)
- **Espaciado amplio:** 40px de separación para respiración visual

#### Diseño Ultra Minimalista
- **Todo blanco:** Eliminados todos los colores azules
- **Sin fondos:** Productos sin backgrounds ni cards
- **Transparencia:** Columna del bundle completamente transparente
- **Solo imágenes circulares:** Imagen del producto en círculo (80px)
- **Tipografía limpia:** Font system stack moderna

#### Grid de Imágenes del Bundle
- **NUEVO:** Grid de solo imágenes en lugar de cards
- **Imágenes múltiples:** Si añades 3 unidades del mismo producto, aparecen 3 imágenes
- **Grid adaptativo:** `repeat(auto-fill, minmax(90px, 1fr))`
- **Imágenes circulares:** Border-radius 50% con animación fadeIn
- **Sin fondos ni bordes:** Solo las imágenes puras en el grid

#### Controles Rediseñados
- **Botones +/-:** 36px × 36px, minimalistas
- **Border-radius completo:** 50px (forma pastilla)
- **Hover inteligente:** Fondo negro al pasar el mouse
- **Input central:** 40px de ancho, transparente
- **Sin bordes internos:** Solo borde exterior en el contenedor

#### Botón "Añadir al Carrito"
- **Background negro:** Contraste fuerte sobre blanco
- **Border-radius 50px:** Completamente redondeado
- **Sin mayúsculas:** Texto normal (no uppercase)
- **Hover suave:** Transform translateY(-1px)
- **Disabled limpio:** Gris claro #ddd

### 📱 Responsive Mejorado

#### Desktop (>992px)
- Grid 1fr 400px
- Productos a la izquierda (scrollable)
- Bundle sticky a la derecha

#### Tablet (768-992px)
- 1 columna vertical
- Productos primero, bundle después
- Grid de imágenes: 80px mínimo

#### Mobile (<768px)
- Imágenes de producto 70px
- Grid del bundle optimizado
- Espaciado reducido a 32px

#### Mobile pequeño (<480px)
- Layout centrado
- Grid 3 columnas fijas
- Controles más compactos

### 🗑️ Eliminado

#### Elementos Removidos del Frontend
- ❌ Cards de productos (`.wcbb-selected-item`)
- ❌ Backgrounds de contenedores
- ❌ Colores azules (#3498db, #2c3e50, etc.)
- ❌ Sombras y bordes decorativos
- ❌ Descripción de productos
- ❌ Contador "espacios restantes"
- ❌ Mensaje "Bundle vacío"
- ❌ Galería de WooCommerce por defecto

#### CSS Limpiado
- Eliminadas variables de color antiguas
- Eliminados estilos `.wcbb-style-*`
- Eliminados efectos de sombra
- Simplificado a palette blanco/negro/gris

### 🔧 Cambios Técnicos

#### Template (bundle-builder-template.php)
- Estructura invertida: productos izq, bundle der
- Grid `.wcbb-images-grid` para imágenes
- Eliminada clase `.wcbb-selected-products`
- Añadido CSS inline para ocultar galería WC

#### CSS (bundle-builder.css)
- **REESCRITURA COMPLETA:** De 600+ a ~400 líneas
- Variables minimalistas: 9 colores básicos
- Grid system actualizado
- Animaciones simplificadas
- Responsive optimizado

#### JavaScript (bundle-builder.js)
- `updateSelectedProducts()` rediseñado
- Ahora genera grid de imágenes individuales
- Loop por cantidad: añade múltiples imágenes del mismo producto
- Eliminada lógica de cards
- Cache actualizado (`.wcbb-images-grid`)

### 🌐 Variables CSS Actualizadas

```css
:root {
    --wcbb-text-dark: #1a1a1a;
    --wcbb-text-medium: #666666;
    --wcbb-text-light: #999999;
    --wcbb-border-light: #eeeeee;
    --wcbb-border-color: #dddddd;
    --wcbb-black: #000000;
    --wcbb-white: #ffffff;
    --wcbb-error-color: #ff4444;
    --wcbb-success-color: #00cc66;
}
```

### 📖 Documentación

#### Archivos Modificados (3)
1. **templates/bundle-builder-template.php**
   - Columnas invertidas
   - Grid de imágenes añadido
   - Galería WC ocultada
   - Estructura simplificada

2. **assets/css/bundle-builder.css**
   - Reescritura completa
   - Diseño ultra minimalista blanco
   - Grid de imágenes circulares
   - Responsive optimizado

3. **assets/js/bundle-builder.js**
   - updateSelectedProducts() con grid de imágenes
   - Múltiples imágenes por cantidad
   - Cache actualizado

### ✅ Testing

#### Funcionalidades Verificadas
- [x] Columnas en orden correcto (productos izq, bundle der)
- [x] Imágenes circulares en productos
- [x] Grid de imágenes sin fondos
- [x] Múltiples imágenes por cantidad funcionan
- [x] Botones +/- con hover negro
- [x] Botón añadir al carrito negro
- [x] Todo blanco, sin azules
- [x] Responsive en tablet y mobile
- [x] Animaciones suaves

#### Compatibilidad
- ✅ Bundles de v1.1.1 siguen funcionando
- ✅ Configuraciones del admin se mantienen
- ✅ No requiere reconfiguración
- ✅ Retrocompatible con productos existentes

---

## [1.1.1] - 2025-11-05

### 🐛 Correcciones Críticas

#### Error "Sorry, this product cannot be purchased"
- **SOLUCIONADO:** El producto Bundle ahora es siempre comprable
- Modificado `is_purchasable()` para no requerir precio base
- Añadido `get_price()` retornando 0 (precio dinámico)
- Añadido `has_options()` indicando que requiere configuración
- El precio se calcula dinámicamente según productos seleccionados

### ✨ Nueva Característica: Soporte para Variaciones

#### Productos Variables y Variaciones
- **NUEVO:** Soporte completo para variaciones de productos
- Puedes añadir variaciones específicas a los bundles
- Las variaciones se muestran con sus atributos:
  - Ejemplo: "Camiseta (Talla: M, Color: Rojo)"
- Cada variación tiene su precio e imagen individual
- Selector en admin actualizado: "Buscar productos y variaciones..."

#### Casos de Uso
- Bundle de ropa con diferentes tallas y colores
- Bundle de comida con diferentes tamaños
- Bundle de tecnología con diferentes especificaciones

### 🎨 Diseño Mejorado: Selector de Cantidad Ultra Minimalista

#### Nuevo Diseño del Selector
- **Border-radius completo (50px)** - Forma de pastilla
- **Sin fondos en botones** - Solo bordes
- **Un solo contenedor** unificado
- **Los 3 elementos centrados** sin separación
- **Hover inteligente:** El botón cambia al color de acento

#### Comparación Visual
**Antes:**
```
┌────┐  ┌────┐  ┌────┐
│ -  │  │ 0  │  │ +  │  ← Botones separados con fondo
└────┘  └────┘  └────┘
```

**Ahora:**
```
╭─────────────────╮
│  -   │  0  │  + │  ← Un contenedor minimalista
╰─────────────────╯
```

#### CSS Actualizado
- `border-radius: 50px` en el contenedor
- `background-color: transparent` en botones
- Bordes solo en el input central
- Transiciones suaves en hover

### 🔧 Cambios Técnicos

#### Archivos Modificados (4)
1. **includes/class-wc-product-bundle-builder.php**
   - Nuevo método `get_price()` retornando 0
   - Nuevo método `has_options()` retornando true
   - Modificado `is_purchasable()` sin validación de precio padre

2. **templates/bundle-builder-template.php**
   - Detección de productos variables
   - Iteración sobre variaciones disponibles
   - Extracción de atributos de variaciones
   - Construcción de nombres descriptivos

3. **includes/class-bundle-product-type.php**
   - Texto actualizado: "Buscar productos y variaciones..."
   - Descripción: "Selecciona los productos y variaciones..."

4. **assets/css/bundle-builder.css**
   - Selector completo rediseñado
   - Border-radius en forma de pastilla
   - Sin fondos en botones por defecto
   - Hover con color de acento

### 📖 Documentación

#### Nuevo Documento
- **ACTUALIZACION-v1.1.1.md** - Guía completa de la actualización
  - Explicación del error corregido
  - Cómo usar variaciones
  - Comparación visual del nuevo diseño
  - Casos de uso con ejemplos

### ✅ Compatibilidad

#### Retrocompatibilidad
- ✅ Bundles de v1.1.0 siguen funcionando
- ✅ Productos simples funcionan igual
- ✅ Configuraciones se mantienen
- ✅ No requiere reconfiguración

#### Testing Realizado
- [x] Producto Bundle se abre sin error
- [x] Variaciones se pueden seleccionar
- [x] Variaciones se muestran con atributos
- [x] Nuevo diseño del selector aplicado
- [x] Botones +/- funcionan correctamente
- [x] Hover cambia el color
- [x] Se añade al carrito correctamente
- [x] Variaciones visibles en el carrito

---

## [1.1.0] - 2025-11-05

### 🎨 Nuevas Características de Diseño

#### Personalización Avanzada desde el Admin
- **Color del Contenedor:** Selector de color para el panel del bundle (#2c3e50 por defecto)
- **Color del Botón:** Selector de color para el botón "Añadir al Carrito" (#27ae60 por defecto)
- **Color de Acento:** Selector de color para botones +/- y elementos interactivos (#3498db por defecto)
- **Border Radius:** Control deslizante 0-50px para redondeo de esquinas (4px por defecto)
- **Estilos de Diseño:** 3 opciones visuales
  - Minimalista (Flat) - Sin sombras, diseño plano
  - Con Sombras Suaves - Efecto Material Design
  - Con Bordes - Estilo tradicional con bordes de 2px

#### CSS Minimalista y Flat
- Rediseño completo del CSS con enfoque minimalista
- Uso de variables CSS para personalización dinámica
- Eliminación de sombras excesivas y efectos innecesarios
- Mejor contraste y legibilidad
- Paleta de colores moderna y profesional
- Transiciones suaves y sutiles

#### Mejoras de UX
- Scrollbar personalizado en la lista de productos seleccionados
- Animaciones fade-in al añadir productos
- Texto "Cantidad: X" más claro que "× X"
- Mejor espaciado y alineación de elementos
- Imágenes optimizadas (100px en desktop, 90px en mobile)

### ✅ Bundles Incompletos

#### Nueva Opción: Permitir Bundles Incompletos
- **Activado (por defecto):** Cliente puede añadir bundles sin completar
  - Mensaje: "Añadiendo bundle con X de Y productos..."
  - Botón: "Añadir Bundle Incompleto"
  
- **Desactivado:** Cliente debe completar el bundle
  - Mensaje de advertencia: "Te faltan X producto(s) para completar el bundle"
  - Botón habilitado solo cuando está completo

#### Comportamiento Mejorado del Botón
- **0 productos:** "Selecciona Productos" (deshabilitado)
- **Algunos productos (incompleto permitido):** "Añadir Bundle Incompleto" (habilitado)
- **Bundle completo:** "✨ Añadir Bundle Completo" (habilitado)
- Texto dinámico según el estado

### 🐛 Correcciones Importantes

#### Error "Error al añadir el bundle al carrito"
- **SOLUCIONADO:** Ya no aparece cuando el bundle no está completo
- Ahora se puede configurar si se permiten bundles incompletos
- Mejor validación frontend y backend
- Mensajes de error más descriptivos

#### Mejoras en la Lógica del JavaScript
- Función `resetBundle()` para limpiar después de añadir al carrito
- Mejor manejo de estados (loading, disabled, active)
- Validación mejorada antes de enviar al servidor
- Feedback visual inmediato

### 📱 Responsive Mejorado

#### Breakpoints Optimizados
- **Desktop (>992px):** Grid de 380px + 1fr, contenedor sticky
- **Tablet (768-992px):** 1 columna, bundle al final
- **Mobile (<768px):** Cards centradas, controles optimizados
- **Mobile pequeño (<480px):** Padding reducido, fuentes más pequeñas

#### Mejoras Mobile
- Imágenes reducidas a 90px en mobile
- Mejor uso del espacio vertical
- Controles centrados y más grandes
- Scrolling optimizado

### 🎯 Mejoras en el Panel Admin

#### Organización por Secciones
- **⚙️ Configuración General:** Cantidad máxima, bundles incompletos, precio oculto
- **🎨 Personalización de Diseño:** Todos los controles de estilo
- **📦 Productos del Bundle:** Selector de productos

#### Color Picker Integrado
- Uso de `wp-color-picker` nativo de WordPress
- Preview en tiempo real del color seleccionado
- Paleta de colores sugeridos
- Guardado automático con el producto

### 🌐 Internacionalización Mejorada

#### Nuevos Textos Traducibles
- `bundle_complete` - "¡Bundle completo! No puedes añadir más productos."
- `bundle_incomplete` - "Te faltan %d producto(s) para completar el bundle."
- `add_complete` - "✨ Añadir Bundle Completo"
- `add_incomplete` - "Añadir Bundle Incompleto"
- `select_products_btn` - "Selecciona Productos"
- `adding_incomplete` - "Añadiendo bundle con %d productos..."

#### Variables de Formato de Moneda
- `currency_symbol` - Símbolo de moneda ($, €, etc.)
- `currency_format` - Formato de precio
- `currency_decimals` - Número de decimales
- `currency_decimal` - Separador decimal
- `currency_thousand` - Separador de miles

### 📖 Documentación

#### Nueva Documentación
- **PERSONALIZATION-GUIDE.md:** Guía completa de personalización
  - Explicación de cada opción
  - Ejemplos de combinaciones de colores
  - Casos de uso reales
  - Recomendaciones de diseño

- **README.md Actualizado:**
  - Sección de personalización expandida
  - Tabla de comportamiento del botón
  - Ejemplos visuales mejorados
  - Changelog integrado

### 🔧 Cambios Técnicos

#### Variables CSS
```css
:root {
    --wcbb-container-color: #2c3e50;
    --wcbb-button-color: #27ae60;
    --wcbb-accent-color: #3498db;
    --wcbb-border-radius: 4px;
}
```

#### Nuevos Data Attributes
- `data-allow-incomplete` - Configuración de bundles incompletos
- `data-design-style` - Estilo de diseño seleccionado

#### Clases CSS Condicionales
- `.wcbb-style-minimal` - Estilo minimalista
- `.wcbb-style-shadow` - Estilo con sombras
- `.wcbb-style-border` - Estilo con bordes

### ⚡️ Rendimiento

#### Optimizaciones
- CSS más ligero y eficiente (reducción de ~30%)
- Eliminación de selectores redundantes
- Uso de variables CSS para reducir duplicación
- Animaciones optimizadas con `transform` en lugar de `margin`/`padding`
- Lazy loading de imágenes en productos

#### Compatibilidad
- Compatibilidad mejorada con temas de WordPress
- No hay conflictos con otros plugins de WooCommerce
- Funciona con page builders (Elementor, WPBakery, etc.)

---

## [1.0.0] - 2025-11-04

### 🎉 Lanzamiento Inicial

#### Características Principales
- Tipo de producto personalizado "Bundle Builder"
- Selección de productos con límite configurable
- Cálculo automático de precios
- Contenedor visual personalizable (color)
- Integración completa con carrito de WooCommerce
- Panel de administración intuitivo
- Frontend responsive
- Validación AJAX

#### Funcionalidades
- Añadir/quitar productos con botones +/-
- Contador en tiempo real (X/Y)
- Preview del bundle en panel izquierdo
- Botón "Añadir al Carrito" con validación
- Mensajes de estado (éxito, error, advertencia)

#### Tecnologías
- PHP 7.4+
- JavaScript (jQuery)
- CSS3 con Flexbox y Grid
- AJAX para interacciones
- WooCommerce 8.0+ API

---

## Leyenda

- 🎨 Nueva característica de diseño
- ✅ Mejora o adición de funcionalidad
- 🐛 Corrección de bug
- 📱 Mejora responsive
- 🎯 Mejora de UX/UI
- 🔧 Cambio técnico
- 📖 Documentación
- ⚡️ Mejora de rendimiento
- 🌐 Internacionalización
