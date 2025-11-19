/**
 * JavaScript Admin para Bundle Builder
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // Inicializar color picker
        if ($('.wcbb-color-picker').length) {
            $('.wcbb-color-picker').wpColorPicker();
        }

        // Mostrar/ocultar panel según el tipo de producto
        $('select#product-type').on('change', function() {
            const productType = $(this).val();
            
            if (productType === 'bundle_builder') {
                $('.show_if_bundle_builder').show();
                $('.hide_if_bundle_builder').hide();
                
                // Mostrar tabs relevantes
                $('.general_options').addClass('active').show();
                $('.inventory_options').hide();
                $('.shipping_options').hide();
            }
        }).trigger('change');

        // Validar antes de publicar
        $('#publish, #save-post').on('click', function(e) {
            const productType = $('select#product-type').val();
            
            if (productType === 'bundle_builder') {
                const allowedProducts = $('#_bundle_allowed_products').val();
                const maxQuantity = $('#_bundle_max_quantity').val();
                
                if (!allowedProducts || allowedProducts.length === 0) {
                    e.preventDefault();
                    alert('Por favor, selecciona al menos un producto para el bundle.');
                    return false;
                }
                
                if (!maxQuantity || maxQuantity < 1) {
                    e.preventDefault();
                    alert('Por favor, establece una cantidad máxima válida (mínimo 1).');
                    return false;
                }
            }
        });

    });

})(jQuery);
