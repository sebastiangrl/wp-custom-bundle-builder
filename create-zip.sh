#!/bin/bash
#
# Script para crear un ZIP del plugin listo para subir a WordPress
#
# Uso: chmod +x create-zip.sh && ./create-zip.sh

echo "🎁 Creando ZIP del plugin..."

# Ir al directorio padre
cd "$(dirname "$0")"

# Nombre del archivo ZIP
ZIP_NAME="wp-custom-bundle-builder-v1.0.0.zip"

# Excluir archivos innecesarios
zip -r "../$ZIP_NAME" . \
  -x "*.DS_Store" \
  -x "*.git*" \
  -x "*.zip" \
  -x "create-zip.sh" \
  -x "node_modules/*" \
  -x ".vscode/*" \
  -x ".idea/*"

echo "✅ ZIP creado: $ZIP_NAME"
echo "📍 Ubicación: $(cd .. && pwd)/$ZIP_NAME"
echo ""
echo "📦 Puedes subirlo a WordPress en:"
echo "   WordPress Admin → Plugins → Añadir Nuevo → Subir Plugin"
echo ""
echo "🎉 ¡Listo para instalar!"
