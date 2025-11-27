<?php
/**
 * Handler AJAX para el Bundle Builder
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCBB_Bundle_Ajax_Handler {

    /**
     * Instancia única
     */
    private static $instance = null;

    /**
     * Obtener instancia
     */
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'wp_ajax_wcbb_add_bundle_to_cart', array( $this, 'add_bundle_to_cart' ) );
        add_action( 'wp_ajax_nopriv_wcbb_add_bundle_to_cart', array( $this, 'add_bundle_to_cart' ) );
        add_action( 'wp_ajax_wcbb_get_product_data', array( $this, 'get_product_data' ) );
        add_action( 'wp_ajax_nopriv_wcbb_get_product_data', array( $this, 'get_product_data' ) );
    }

    /**
     * Añadir bundle al carrito vía AJAX
     */
    public function add_bundle_to_cart() {
        check_ajax_referer( 'wcbb_nonce', 'nonce' );

        $bundle_id = isset( $_POST['bundle_id'] ) ? absint( $_POST['bundle_id'] ) : 0;
        $selected_products = isset( $_POST['selected_products'] ) ? json_decode( stripslashes( $_POST['selected_products'] ), true ) : array();

        // Validaciones
        if ( empty( $bundle_id ) || empty( $selected_products ) ) {
            wp_send_json_error( array(
                'message' => __( 'Datos inválidos', 'wp-custom-bundle-builder' ),
            ) );
        }

        $product = wc_get_product( $bundle_id );

        if ( ! $product || $product->get_type() !== 'bundle_builder' ) {
            wp_send_json_error( array(
                'message' => __( 'Producto no válido', 'wp-custom-bundle-builder' ),
            ) );
        }

        // Validar cantidad mínima
        $min_quantity = get_post_meta( $bundle_id, '_bundle_max_quantity', true ) ?: 4;
        $total_quantity = 0;
        foreach ( $selected_products as $product_id => $quantity ) {
            $total_quantity += absint( $quantity );
        }

        if ( $total_quantity < $min_quantity ) {
            wp_send_json_error( array(
                'message' => sprintf(
                    __( 'You must select at least %d products', 'wp-custom-bundle-builder' ),
                    $min_quantity
                ),
            ) );
        }

        if ( $total_quantity === 0 ) {
            wp_send_json_error( array(
                'message' => __( 'Please select at least one product', 'wp-custom-bundle-builder' ),
            ) );
        }

        // Validar que los productos seleccionados estén permitidos
        $allowed_products = get_post_meta( $bundle_id, '_bundle_allowed_products', true );
        
        if ( ! is_array( $allowed_products ) ) {
            $allowed_products = array();
        }

        foreach ( $selected_products as $product_id => $quantity ) {
            $selected_product = wc_get_product( $product_id );
            
            // Obtener el ID del producto padre si es una variación
            $parent_id = $product_id;
            if ( $selected_product && $selected_product->is_type( 'variation' ) ) {
                $parent_id = $selected_product->get_parent_id();
            }
            
            // Validar que el producto (o su padre si es variación) esté permitido
            if ( ! in_array( $parent_id, $allowed_products, true ) ) {
                wp_send_json_error( array(
                    'message' => __( 'Uno o más productos seleccionados no están permitidos', 'wp-custom-bundle-builder' ),
                ) );
            }

            // Validar stock del producto
            if ( ! $selected_product || ! $selected_product->is_in_stock() ) {
                wp_send_json_error( array(
                    'message' => sprintf(
                        __( 'El producto "%s" no está disponible', 'wp-custom-bundle-builder' ),
                        $selected_product ? $selected_product->get_name() : ''
                    ),
                ) );
            }

            // Validar cantidad disponible
            if ( $selected_product->managing_stock() ) {
                $stock_quantity = $selected_product->get_stock_quantity();
                if ( $quantity > $stock_quantity ) {
                    wp_send_json_error( array(
                        'message' => sprintf(
                            __( 'Solo hay %d unidades disponibles de "%s"', 'wp-custom-bundle-builder' ),
                            $stock_quantity,
                            $selected_product->get_name()
                        ),
                    ) );
                }
            }
        }

        // Calcular precio total
        $total_price = 0;
        foreach ( $selected_products as $product_id => $quantity ) {
            $selected_product = wc_get_product( $product_id );
            if ( $selected_product ) {
                $product_price = floatval( $selected_product->get_price() );
                $product_qty = intval( $quantity );
                $total_price += $product_price * $product_qty;
            }
        }

        // Añadir al carrito
        $cart_item_data = array(
            'wcbb_bundle_id' => $bundle_id,
            'wcbb_selected_products' => $selected_products,
            'wcbb_bundle_price' => $total_price,
            'unique_key' => md5( microtime() . rand() ),
        );

        $cart_item_key = WC()->cart->add_to_cart( 
            $bundle_id, 
            1, 
            0, 
            array(), 
            $cart_item_data 
        );

        if ( $cart_item_key ) {
            wp_send_json_success( array(
                'message' => __( 'Bundle added to cart successfully', 'wp-custom-bundle-builder' ),
                'cart_url' => wc_get_cart_url(),
                'cart_hash' => WC()->cart->get_cart_hash(),
            ) );
        } else {
            wp_send_json_error( array(
                'message' => __( 'Error al añadir el bundle al carrito', 'wp-custom-bundle-builder' ),
            ) );
        }
    }

    /**
     * Obtener datos de un producto vía AJAX
     */
    public function get_product_data() {
        check_ajax_referer( 'wcbb_nonce', 'nonce' );

        $product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;

        if ( empty( $product_id ) ) {
            wp_send_json_error( array(
                'message' => __( 'ID de producto inválido', 'wp-custom-bundle-builder' ),
            ) );
        }

        $product = wc_get_product( $product_id );

        if ( ! $product ) {
            wp_send_json_error( array(
                'message' => __( 'Producto no encontrado', 'wp-custom-bundle-builder' ),
            ) );
        }

        wp_send_json_success( array(
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'price_html' => $product->get_price_html(),
            'image' => wp_get_attachment_image_url( $product->get_image_id(), 'thumbnail' ),
            'stock_status' => $product->is_in_stock(),
            'stock_quantity' => $product->get_stock_quantity(),
        ) );
    }
}
