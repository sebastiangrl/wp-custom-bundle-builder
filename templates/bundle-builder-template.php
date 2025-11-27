<?php
/**
 * Template del Bundle Builder Frontend
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( ! $product || $product->get_type() !== 'bundle_builder' ) {
    return;
}

// Obtener configuración del bundle
$max_quantity = get_post_meta( $product->get_id(), '_bundle_max_quantity', true ) ?: 4;
$allow_incomplete = get_post_meta( $product->get_id(), '_bundle_allow_incomplete', true ) ?: 'yes';
$container_color = get_post_meta( $product->get_id(), '_bundle_container_color', true ) ?: '#2c3e50';
$button_color = get_post_meta( $product->get_id(), '_bundle_button_color', true ) ?: '#27ae60';
$accent_color = get_post_meta( $product->get_id(), '_bundle_accent_color', true ) ?: '#3498db';
$border_radius = get_post_meta( $product->get_id(), '_bundle_border_radius', true ) ?: 4;
$design_style = get_post_meta( $product->get_id(), '_bundle_design_style', true ) ?: 'minimal';
$allowed_product_ids = get_post_meta( $product->get_id(), '_bundle_allowed_products', true );

if ( empty( $allowed_product_ids ) || ! is_array( $allowed_product_ids ) ) {
    echo '<p class="woocommerce-info">' . esc_html__( 'This bundle has no products configured.', 'wp-custom-bundle-builder' ) . '</p>';
    return;
}

/**
 * Helper function: Obtener imagen de galería (NO la principal)
 * Usa la primera imagen de la galería que NO sea la imagen principal
 */
function wcbb_get_product_image( $product, $size = 'woocommerce_thumbnail' ) {
    $gallery_image_ids = $product->get_gallery_image_ids();
    $main_image_id = $product->get_image_id();
    
    // Si hay imágenes en la galería
    if ( ! empty( $gallery_image_ids ) ) {
        // Buscar la primera imagen que NO sea la principal
        foreach ( $gallery_image_ids as $gallery_id ) {
            if ( $gallery_id != $main_image_id ) {
                $gallery_image = wp_get_attachment_image_url( $gallery_id, $size );
                if ( $gallery_image ) {
                    return $gallery_image;
                }
            }
        }
        
        // Si todas las imágenes de galería son la principal, usar la primera de todas formas
        $first_gallery_image = wp_get_attachment_image_url( $gallery_image_ids[0], $size );
        if ( $first_gallery_image ) {
            return $first_gallery_image;
        }
    }
    
    // Fallback: usar imagen principal
    return wp_get_attachment_image_url( $main_image_id, $size );
}

// Obtener productos permitidos
$allowed_products = array();
foreach ( $allowed_product_ids as $product_id ) {
    $allowed_product = wc_get_product( $product_id );
    
    if ( ! $allowed_product ) {
        continue;
    }
    
    // Si es un producto variable, añadir con sus variaciones
    if ( $allowed_product->is_type( 'variable' ) ) {
        if ( $allowed_product->is_in_stock() ) {
            $variations = $allowed_product->get_available_variations();
            $variations_data = array();
            $attribute_label = '';
            
            foreach ( $variations as $variation_data ) {
                $variation = wc_get_product( $variation_data['variation_id'] );
                
                if ( $variation && $variation->is_in_stock() ) {
                    // Obtener solo los valores de los atributos (sin el label)
                    $attributes = array();
                    foreach ( $variation_data['attributes'] as $attr_name => $attr_value ) {
                        // Obtener el label del atributo para la primera variación
                        if ( empty( $attribute_label ) ) {
                            $attribute_label = wc_attribute_label( str_replace( 'attribute_', '', $attr_name ) );
                        }
                        $attributes[] = $attr_value;
                    }
                    
                    // Obtener imagen de la variación: primero intentar imagen propia, luego galería, luego padre
                    $variation_image_id = $variation->get_image_id();
                    $variation_image = '';
                    
                    if ( $variation_image_id ) {
                        // Si la variación tiene imagen propia, usarla
                        $variation_image = wp_get_attachment_image_url( $variation_image_id, 'woocommerce_thumbnail' );
                    }
                    
                    if ( ! $variation_image ) {
                        // Si no, intentar con la función de galería
                        $variation_image = wcbb_get_product_image( $variation, 'woocommerce_thumbnail' );
                    }
                    
                    if ( ! $variation_image ) {
                        // Último recurso: imagen del producto padre
                        $variation_image = wcbb_get_product_image( $allowed_product, 'woocommerce_thumbnail' );
                    }
                    
                    $variations_data[] = array(
                        'id'         => $variation->get_id(),
                        'name'       => implode( ', ', $attributes ),
                        'price'      => $variation->get_price(),
                        'price_html' => $variation->get_price_html(),
                        'image'      => $variation_image,
                    );
                }
            }
            
            if ( ! empty( $variations_data ) ) {
                $allowed_products[] = array(
                    'id'          => $allowed_product->get_id(),
                    'parent_id'   => 0,
                    'name'        => $allowed_product->get_name(),
                    'price'       => $allowed_product->get_price(),
                    'price_html'  => $allowed_product->get_price_html(),
                    'image'       => wcbb_get_product_image( $allowed_product, 'woocommerce_thumbnail' ),
                    'description' => $allowed_product->get_short_description(),
                    'stock_qty'   => $allowed_product->get_stock_quantity(),
                    'is_variable' => true,
                    'variations'  => $variations_data,
                    'attribute_label' => $attribute_label,
                );
            }
        }
    } else {
        // Producto simple
        if ( $allowed_product->is_in_stock() ) {
            $allowed_products[] = array(
                'id'          => $allowed_product->get_id(),
                'parent_id'   => 0,
                'name'        => $allowed_product->get_name(),
                'price'       => $allowed_product->get_price(),
                'price_html'  => $allowed_product->get_price_html(),
                'image'       => wcbb_get_product_image( $allowed_product, 'woocommerce_thumbnail' ),
                'description' => $allowed_product->get_short_description(),
                'stock_qty'   => $allowed_product->get_stock_quantity(),
                'is_variable' => false,
                'variations'  => array(),
            );
        }
    }
}

if ( empty( $allowed_products ) ) {
    echo '<p class="woocommerce-info">' . esc_html__( 'No products available for this bundle.', 'wp-custom-bundle-builder' ) . '</p>';
    return;
}
?>

<div class="wcbb-bundle-builder-wrapper" 
    data-bundle-id="<?php echo esc_attr( $product->get_id() ); ?>" 
    data-min-quantity="<?php echo esc_attr( $max_quantity ); ?>"
    data-allow-incomplete="<?php echo esc_attr( $allow_incomplete ); ?>"
    data-design-style="<?php echo esc_attr( $design_style ); ?>">
    
    <style>
        /* Ocultar galería de imágenes y precio de WooCommerce */
        .woocommerce-product-gallery {
            display: none !important;
        }
        .product_meta,
        .product .price,
        .woocommerce-product-details__short-description + .price,
        div[itemprop="offers"] .price,
        .summary .price {
            display: none !important;
        }
        
        /* Heredar tipografía del tema para el precio */
        .wcbb-price-amount {
            font-family: var(--heading-font-family, inherit) !important;
        }
    </style>
    
    <div class="wcbb-bundle-container">
        
        <!-- Columna Izquierda: Productos Disponibles -->
        <div class="wcbb-products-list">
            <h3>
                <?php echo esc_html( sprintf( 'Select at least %d Flavors', $max_quantity ) ); ?>
                <span style="font-size: 14px; font-weight: 400; color: #666; margin-left: 8px;">
                    <?php esc_html_e( '· Available Products', 'wp-custom-bundle-builder' ); ?>
                </span>
            </h3>
            
            <div class="wcbb-products-grid">
                <?php foreach ( $allowed_products as $product_data ) : ?>
                    <div class="wcbb-product-item" 
                         data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>" 
                         data-product-price="<?php echo esc_attr( $product_data['price'] ); ?>" 
                         data-product-name="<?php echo esc_attr( $product_data['name'] ); ?>" 
                         data-product-image="<?php echo esc_attr( $product_data['image'] ); ?>"
                         data-is-variable="<?php echo esc_attr( $product_data['is_variable'] ? 'yes' : 'no' ); ?>">
                        
                        <div class="wcbb-product-image">
                            <?php if ( $product_data['image'] ) : ?>
                                <img src="<?php echo esc_url( $product_data['image'] ); ?>" alt="<?php echo esc_attr( $product_data['name'] ); ?>">
                            <?php else : ?>
                                <img src="<?php echo esc_url( wc_placeholder_img_src() ); ?>" alt="<?php echo esc_attr( $product_data['name'] ); ?>">
                            <?php endif; ?>
                        </div>

                        <div class="wcbb-product-info">
                            <h4 class="wcbb-product-name">
                                <a href="<?php echo esc_url( get_permalink( $product_data['id'] ) ); ?>" target="_blank">
                                    <?php echo esc_html( $product_data['name'] ); ?>
                                </a>
                            </h4>
                            
                            <?php if ( $product_data['is_variable'] && ! empty( $product_data['variations'] ) ) : ?>
                                <!-- Selector oculto (necesario para el JS) -->
                                <select class="wcbb-variation-select" data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>">
                                    <option value=""><?php esc_html_e( 'Select an option', 'wp-custom-bundle-builder' ); ?></option>
                                    <?php foreach ( $product_data['variations'] as $variation ) : ?>
                                        <option 
                                            value="<?php echo esc_attr( $variation['id'] ); ?>"
                                            data-price="<?php echo esc_attr( $variation['price'] ); ?>"
                                            data-image="<?php echo esc_attr( $variation['image'] ); ?>"
                                            data-name="<?php echo esc_attr( $product_data['name'] . ' - ' . $variation['name'] ); ?>">
                                            <?php echo esc_html( $variation['name'] . ' - ' ); ?>
                                            <?php echo wp_kses_post( $variation['price_html'] ); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <!-- Botones personalizados de variación -->
                                <div class="wcbb-custom-variation-swatches">
                                    <label class="wcbb-oz-attribute-label"><?php echo esc_html( $product_data['attribute_label'] ?: __( 'Options', 'wp-custom-bundle-builder' ) ); ?></label>
                                    <div class="wcbb-swatches-container">
                                        <?php foreach ( $product_data['variations'] as $variation ) : 
                                            // Dividir el nombre de la variación para extraer número y unidad
                                            $variation_name = $variation['name'];
                                            $number = '';
                                            $unit = '';
                                            
                                            // Buscar patrón "número-unidad" (ej: "6-oz", "12-oz")
                                            if ( preg_match('/^(\d+)-(.+)$/', $variation_name, $matches) ) {
                                                $number = $matches[1];
                                                $unit = $matches[2];
                                            } else {
                                                // Si no coincide el patrón, usar el nombre completo
                                                $number = $variation_name;
                                            }
                                        ?>
                                            <input 
                                                type="radio" 
                                                id="wcbb-var-<?php echo esc_attr( $variation['id'] ); ?>" 
                                                name="wcbb-variation-<?php echo esc_attr( $product_data['id'] ); ?>" 
                                                value="<?php echo esc_attr( $variation['id'] ); ?>"
                                                data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>"
                                                class="wcbb-variation-radio"
                                            />
                                            <label for="wcbb-var-<?php echo esc_attr( $variation['id'] ); ?>" class="wcbb-swatch-label">
                                                <span class="wcbb-oz-icon wcbb-default-icon"></span>
                                                <span class="wcbb-oz-icon wcbb-selected-icon"></span>
                                                <span class="wcbb-oz-text" data-number="<?php echo esc_attr( $number ); ?>" data-unit="<?php echo esc_attr( $unit ); ?>"><?php echo esc_html( $variation['name'] ); ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <!-- Precio para productos simples -->
                                <div class="wcbb-product-price">
                                    <?php echo wp_kses_post( $product_data['price_html'] ); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="wcbb-product-controls">
                            <button type="button" class="wcbb-decrease-qty" data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>" disabled>
                                <span>−</span>
                            </button>
                            
                            <input 
                                type="number" 
                                class="wcbb-quantity-input" 
                                value="0" 
                                min="0" 
                                readonly
                                data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>"
                            >
                            
                            <button type="button" class="wcbb-increase-qty" data-product-id="<?php echo esc_attr( $product_data['id'] ); ?>">
                                <span>+</span>
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Columna Derecha: Bundle Visual con Grid de Imágenes -->
        <div class="wcbb-bundle-visual" data-min-quantity="<?php echo esc_attr( $max_quantity ); ?>">
            <div class="wcbb-bundle-header">
                <h3>
                    <?php esc_html_e( 'Your Bundle', 'wp-custom-bundle-builder' ); ?>
                    <span class="wcbb-counter">
                        <span class="wcbb-current-count">0</span> (min <?php echo esc_html( $max_quantity ); ?>)
                    </span>
                </h3>
            </div>

            <!-- Grid de imágenes de productos seleccionados -->
            <div class="wcbb-images-grid"></div>

            <!-- Resumen y total -->
            <div class="wcbb-bundle-summary">
                <div class="wcbb-summary-row wcbb-total">
                    <span class="wcbb-label"><?php esc_html_e( 'Total:', 'wp-custom-bundle-builder' ); ?></span>
                    <span class="wcbb-price-amount"><?php echo wc_price( 0 ); ?></span>
                </div>

                <button type="button" class="wcbb-add-to-cart-btn button alt disabled" disabled>
                    <?php esc_html_e( 'Add to Cart', 'wp-custom-bundle-builder' ); ?>
                </button>

                <div class="wcbb-messages"></div>
            </div>
        </div>

    </div>

</div>
