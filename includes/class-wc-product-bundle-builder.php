<?php
/**
 * Clase personalizada del producto Bundle Builder
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Product_Bundle_Builder extends WC_Product {

    /**
     * Constructor
     */
    public function __construct( $product ) {
        $this->product_type = 'bundle_builder';
        parent::__construct( $product );
    }

    /**
     * Obtener tipo de producto
     */
    public function get_type() {
        return 'bundle_builder';
    }

    /**
     * El bundle es siempre comprable si tiene productos configurados
     */
    public function is_purchasable() {
        $allowed_products = $this->get_allowed_products();
        return ! empty( $allowed_products );
    }
    
    /**
     * El bundle no tiene precio propio (se calcula dinámicamente)
     */
    public function get_price( $context = 'view' ) {
        // Intentar obtener el precio desde el carrito si existe
        if ( ! is_null( WC()->cart ) ) {
            foreach ( WC()->cart->get_cart() as $cart_item ) {
                if ( isset( $cart_item['wcbb_bundle_id'] ) && 
                     $cart_item['wcbb_bundle_id'] == $this->get_id() && 
                     isset( $cart_item['wcbb_bundle_price'] ) ) {
                    return floatval( $cart_item['wcbb_bundle_price'] );
                }
            }
        }
        
        // Si no está en el carrito, devolver el precio regular si existe
        $regular_price = parent::get_price( $context );
        return $regular_price ? $regular_price : 0;
    }
    
    /**
     * Establecer que el bundle no requiere un precio definido
     */
    public function has_options() {
        return true;
    }

    /**
     * Obtener productos permitidos en el bundle
     */
    public function get_allowed_products() {
        return get_post_meta( $this->get_id(), '_bundle_allowed_products', true );
    }

    /**
     * Obtener cantidad máxima del bundle
     */
    public function get_max_quantity() {
        return get_post_meta( $this->get_id(), '_bundle_max_quantity', true ) ?: 4;
    }

    /**
     * Obtener color del contenedor
     */
    public function get_container_color() {
        return get_post_meta( $this->get_id(), '_bundle_container_color', true ) ?: '#3498db';
    }

    /**
     * Si debe ocultar el precio
     */
    public function hide_price() {
        return get_post_meta( $this->get_id(), '_bundle_hide_price', true ) === 'yes';
    }

    /**
     * El bundle siempre está en stock (depende de los productos internos)
     */
    public function is_in_stock() {
        return true;
    }

    /**
     * Añadir al carrito URL
     */
    public function add_to_cart_url() {
        return get_permalink( $this->get_id() );
    }

    /**
     * Texto del botón añadir al carrito
     */
    public function add_to_cart_text() {
        return __( 'Seleccionar productos', 'wp-custom-bundle-builder' );
    }

    /**
     * Descripción del botón añadir al carrito
     */
    public function add_to_cart_description() {
        return __( 'Crea tu bundle personalizado', 'wp-custom-bundle-builder' );
    }
}
