/**
 * Bundle Builder JavaScript - Vanilla JS
 * Maneja la interactividad del bundle builder con diseño minimalista
 */

(function($) {
    'use strict';

    class BundleBuilder {
        constructor(container) {
            this.container = container;
            this.bundleId = container.dataset.bundleId;
            this.minQuantity = parseInt(container.dataset.minQuantity) || 4; // now treated as minimum
            this.allowIncomplete = container.dataset.allowIncomplete === 'yes';
            this.designStyle = container.dataset.designStyle || 'minimal';
            this.selectedProducts = {};
            this.totalCount = 0;
            this.totalPrice = 0;

            this.init();
        }

        init() {
            this.cacheElements();
            this.bindEvents();
            this.updateUI();
        }

        cacheElements() {
            this.$container = $(this.container);
            this.$increaseButtons = this.$container.find('.wcbb-increase-qty');
            this.$decreaseButtons = this.$container.find('.wcbb-decrease-qty');
            this.$quantityInputs = this.$container.find('.wcbb-quantity-input');
            this.$addToCartBtn = this.$container.find('.wcbb-add-to-cart-btn');
            this.$imagesGrid = this.$container.find('.wcbb-images-grid');
            this.$currentCount = this.$container.find('.wcbb-current-count');
            this.$priceAmount = this.$container.find('.wcbb-price-amount');
            this.$messages = this.$container.find('.wcbb-messages');
        }

        bindEvents() {
            // Sincronización de botones de variación personalizados con selector oculto
            this.$container.on('change', '.wcbb-variation-radio', (e) => {
                const $radio = $(e.currentTarget);
                const variationId = $radio.val();
                const productId = $radio.data('product-id');
                
                // Sincronizar con el selector oculto
                const $select = this.$container.find(`.wcbb-variation-select[data-product-id="${productId}"]`);
                if ($select.length) {
                    $select.val(variationId).trigger('change');
                }
            });

            // Botones aumentar cantidad
            this.$increaseButtons.on('click', (e) => {
                const productId = $(e.currentTarget).data('product-id');
                this.increaseQuantity(productId);
            });

            // Botones disminuir cantidad
            this.$decreaseButtons.on('click', (e) => {
                const productId = $(e.currentTarget).data('product-id');
                this.decreaseQuantity(productId);
            });

            // Botón añadir al carrito
            this.$addToCartBtn.on('click', () => {
                this.addToCart();
            });
        }

        increaseQuantity(productId) {
            // No hay límite máximo, solo stock

            // Obtener datos del producto
            const $productItem = this.$container.find(`.wcbb-product-item[data-product-id="${productId}"]`);
            const isVariable = $productItem.data('is-variable') === 'yes';
            
            let actualProductId = productId;
            let productName = $productItem.data('product-name');
            let productPrice = parseFloat($productItem.data('product-price'));
            let productImage = $productItem.find('.wcbb-product-image img').attr('src'); // Imagen por defecto
            
            // Si es producto variable, obtener la variación seleccionada
            if (isVariable) {
                const $variationSelect = $productItem.find('.wcbb-variation-select');
                const selectedVariationId = $variationSelect.val();
                
                if (!selectedVariationId) {
                    this.showMessage('Please select an option first.', 'warning');
                    return;
                }
                
                // Usar el ID de la variación
                actualProductId = selectedVariationId;
                const $selectedOption = $variationSelect.find('option:selected');
                productName = $selectedOption.data('name');
                productPrice = parseFloat($selectedOption.data('price'));
                // Para variaciones, SIEMPRE usar la imagen de la variación desde data-image
                const variationImage = $selectedOption.data('image');
                if (variationImage) {
                    productImage = variationImage;
                }
            }

            // Inicializar si no existe (usando actualProductId que puede ser variación)
            if (!this.selectedProducts[actualProductId]) {
                this.selectedProducts[actualProductId] = {
                    id: actualProductId,
                    parentId: productId,
                    name: productName,
                    price: productPrice,
                    image: productImage,
                    quantity: 0,
                    isVariation: isVariable
                };
            }

            // Aumentar cantidad
            this.selectedProducts[actualProductId].quantity++;
            this.totalCount++;
            this.totalPrice += productPrice;

            // Actualizar UI
            this.updateProductQuantity(productId);
            this.updateSelectedProducts();
            this.updateUI();
        }

        decreaseQuantity(productId) {
            // Obtener producto item para verificar si es variable
            const $productItem = this.$container.find(`.wcbb-product-item[data-product-id="${productId}"]`);
            const isVariable = $productItem.data('is-variable') === 'yes';
            
            let actualProductId = productId;
            
            // Si es variable, obtener la variación seleccionada
            if (isVariable) {
                const $variationSelect = $productItem.find('.wcbb-variation-select');
                const selectedVariationId = $variationSelect.val();
                
                if (selectedVariationId) {
                    actualProductId = selectedVariationId;
                }
            }
            
            if (!this.selectedProducts[actualProductId] || this.selectedProducts[actualProductId].quantity === 0) {
                return;
            }

            // Disminuir cantidad
            this.selectedProducts[actualProductId].quantity--;
            this.totalCount--;
            this.totalPrice -= this.selectedProducts[actualProductId].price;

            // Si llega a 0, eliminar del objeto
            if (this.selectedProducts[actualProductId].quantity === 0) {
                delete this.selectedProducts[actualProductId];
            }

            // Actualizar UI
            this.updateProductQuantity(productId);
            this.updateSelectedProducts();
            this.updateUI();
        }

        updateProductQuantity(productId) {
            const $productItem = this.$container.find(`.wcbb-product-item[data-product-id="${productId}"]`);
            const isVariable = $productItem.data('is-variable') === 'yes';
            
            let quantity = 0;
            
            if (isVariable) {
                // Sumar cantidades de todas las variaciones de este producto
                for (const selectedId in this.selectedProducts) {
                    if (this.selectedProducts[selectedId].parentId === productId) {
                        quantity += this.selectedProducts[selectedId].quantity;
                    }
                }
            } else {
                // Producto simple
                quantity = this.selectedProducts[productId] ? this.selectedProducts[productId].quantity : 0;
            }
            
            const $input = this.$container.find(`.wcbb-quantity-input[data-product-id="${productId}"]`);
            const $decreaseBtn = this.$container.find(`.wcbb-decrease-qty[data-product-id="${productId}"]`);
            // No need to disable increase button anymore
            const $increaseBtn = this.$container.find(`.wcbb-increase-qty[data-product-id="${productId}"]`);

            // Actualizar input
            $input.val(quantity);

            // Habilitar/deshabilitar botón disminuir
            if (quantity > 0) {
                $decreaseBtn.prop('disabled', false).removeClass('disabled');
            } else {
                $decreaseBtn.prop('disabled', true).addClass('disabled');
            }

            // Botón aumentar siempre habilitado (salvo stock, que se controla aparte)
            $increaseBtn.prop('disabled', false).removeClass('disabled');
        }

        updateSelectedProducts() {
            // Obtener contenedor de imágenes
            const $imagesGrid = this.$container.find('.wcbb-images-grid');
            $imagesGrid.empty();

            // Si no hay productos seleccionados, no mostrar nada (grid vacío)
            if (Object.keys(this.selectedProducts).length === 0) {
                return;
            }

            // Añadir imágenes al grid - una imagen por cada unidad
            for (const productId in this.selectedProducts) {
                const product = this.selectedProducts[productId];
                
                // Obtener el nombre de la variación (solo la parte después del guión)
                let variationText = '';
                if (product.isVariation && product.name) {
                    const parts = product.name.split(' - ');
                    variationText = parts.length > 1 ? parts[1] : '';
                }
                
                // Añadir una imagen por cada unidad del producto
                for (let i = 0; i < product.quantity; i++) {
                    const $imageItem = $(`
                        <div class="wcbb-bundle-image">
                            <img src="${product.image}" alt="${product.name}">
                            ${variationText ? `<span class="wcbb-bundle-image-variation">${variationText}</span>` : ''}
                        </div>
                    `);
                    
                    $imagesGrid.append($imageItem);
                }
            }
        }

        removeProduct(productId) {
            if (!this.selectedProducts[productId]) {
                return;
            }

            const quantity = this.selectedProducts[productId].quantity;
            const price = this.selectedProducts[productId].price;

            // Actualizar totales
            this.totalCount -= quantity;
            this.totalPrice -= (price * quantity);

            // Eliminar del objeto
            delete this.selectedProducts[productId];

            // Actualizar UI
            this.updateProductQuantity(productId);
            this.updateSelectedProducts();
            this.updateUI();
        }

        updateUI() {
            // Actualizar contador
            this.$currentCount.text(this.totalCount);

            // Actualizar precio total
            this.$priceAmount.html(this.formatPrice(this.totalPrice));

            // Lógica del botón añadir al carrito - habilitar si cumple el mínimo
            if (this.totalCount >= this.minQuantity) {
                this.$addToCartBtn.prop('disabled', false).removeClass('disabled');
                this.$addToCartBtn.text('Add to Cart');
            } else {
                this.$addToCartBtn.prop('disabled', true).addClass('disabled');
                if (this.totalCount === 0) {
                    this.$addToCartBtn.text('Add to Cart');
                } else {
                    const remaining = this.minQuantity - this.totalCount;
                    this.$addToCartBtn.text(`Select ${remaining} more`);
                }
            }
        }

        formatPrice(price) {
            // Formatear precio con USD al final
            let formattedPrice = '';
            
            if (typeof accounting !== 'undefined') {
                formattedPrice = accounting.formatMoney(price, {
                    symbol: wcbbData.currency_symbol || '$',
                    decimal: wcbbData.currency_decimal || '.',
                    thousand: wcbbData.currency_thousand || ',',
                    precision: wcbbData.currency_decimals || 2,
                    format: wcbbData.currency_format || '%s%v'
                });
            } else {
                formattedPrice = '$' + price.toFixed(2);
            }
            
            // Añadir USD al final
            return formattedPrice + ' USD';
        }

        showMessage(message, type = 'info') {
            const $message = $(`<div class="wcbb-message wcbb-message-${type}">${message}</div>`);
            this.$messages.html($message);

            // Auto-ocultar después de 4 segundos
            setTimeout(() => {
                $message.fadeOut(() => $message.remove());
            }, 4000);
        }

        addToCart() {
            if (this.totalCount === 0) {
                this.showMessage(wcbbData.i18n.select_products || 'Please select at least one product.', 'error');
                return;
            }

            // Verificar que el bundle cumpla el mínimo
            if (this.totalCount < this.minQuantity) {
                const remaining = this.minQuantity - this.totalCount;
                this.showMessage(
                    wcbbData.i18n.bundle_incomplete.replace('%d', remaining) || 
                    `You need to select at least ${this.minQuantity} products. Please add ${remaining} more product(s).`,
                    'warning'
                );
                return;
            }

            // Deshabilitar botón durante la petición
            this.$addToCartBtn.prop('disabled', true).addClass('loading');

            // Preparar datos
            const products = {};
            for (const productId in this.selectedProducts) {
                products[productId] = this.selectedProducts[productId].quantity;
            }

            // Hacer petición AJAX
            $.ajax({
                url: wcbbData.ajax_url,
                type: 'POST',
                data: {
                    action: 'wcbb_add_bundle_to_cart',
                    nonce: wcbbData.nonce,
                    bundle_id: this.bundleId,
                    selected_products: JSON.stringify(products)
                },
                success: (response) => {
                    if (response.success) {
                        this.showMessage(wcbbData.i18n.added_to_cart || '✓ Bundle added to cart successfully', 'success');
                        
                        // Actualizar fragmentos del carrito de WooCommerce
                        $(document.body).trigger('wc_fragment_refresh');
                        
                        // Resetear el bundle
                        setTimeout(() => {
                            this.resetBundle();
                        }, 1000);
                        
                        // Opcional: Redirigir al carrito después de 2 segundos
                        if (response.data.cart_url) {
                            setTimeout(() => {
                                window.location.href = response.data.cart_url;
                            }, 2000);
                        }
                    } else {
                        this.showMessage(response.data.message || wcbbData.i18n.error || 'Error adding bundle to cart', 'error');
                        this.$addToCartBtn.prop('disabled', false).removeClass('loading');
                    }
                },
                error: () => {
                    this.showMessage(wcbbData.i18n.error || 'Connection error. Please try again.', 'error');
                    this.$addToCartBtn.prop('disabled', false).removeClass('loading');
                }
            });
        }

        resetBundle() {
            // Limpiar productos seleccionados
            this.selectedProducts = {};
            this.totalCount = 0;
            this.totalPrice = 0;

            // Resetear todos los inputs
            this.$quantityInputs.val(0);
            this.$decreaseButtons.prop('disabled', true).addClass('disabled');
            this.$increaseButtons.prop('disabled', false).removeClass('disabled');

            // Actualizar UI
            this.updateSelectedProducts();
            this.updateUI();
            this.$addToCartBtn.removeClass('loading');
        }
    }

    // Inicializar cuando el documento esté listo
    $(document).ready(function() {
        const bundleContainer = document.querySelector('.wcbb-bundle-builder-wrapper');
        if (bundleContainer) {
            // Para compatibilidad, pasar minQuantity en vez de maxQuantity
            // Si el atributo data-min-quantity no existe, usar data-max-quantity
            if (!bundleContainer.dataset.minQuantity && bundleContainer.dataset.maxQuantity) {
                bundleContainer.dataset.minQuantity = bundleContainer.dataset.maxQuantity;
            }
            new BundleBuilder(bundleContainer);
        }
    });

})(jQuery);

