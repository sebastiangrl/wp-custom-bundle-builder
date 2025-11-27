<?php
/**
 * Plugin Name: Custom Bundle Builder for WooCommerce
 * Plugin URI: https://example.com/wp-custom-bundle-builder
 * Description: Crea bundles personalizados con diseño ultra minimalista blanco. Grid de imágenes circulares, sin fondos, completamente limpio y moderno.
 * Version: 1.4.2
 * Author: Sebastián Gonzalez
 * Author URI: https://example.com
 * Text Domain: wp-custom-bundle-builder
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * WC requires at least: 8.0
 * WC tested up to: 10.3.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Si se accede directamente, salir
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Definir constantes del plugin
define( 'WCBB_VERSION', '1.4.2' );
define( 'WCBB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WCBB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WCBB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Verificar si WooCommerce está activo
 */
function wcbb_check_woocommerce() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'wcbb_woocommerce_missing_notice' );
        return false;
    }
    return true;
}

/**
 * Declarar compatibilidad con HPOS (High-Performance Order Storage)
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

/**
 * Mostrar aviso si WooCommerce no está activo
 */
function wcbb_woocommerce_missing_notice() {
    ?>
    <div class="error">
        <p><?php esc_html_e( 'Custom Bundle Builder requiere que WooCommerce esté instalado y activo.', 'wp-custom-bundle-builder' ); ?></p>
    </div>
    <?php
}

/**
 * Inicializar el plugin
 */
function wcbb_init() {
    if ( ! wcbb_check_woocommerce() ) {
        return;
    }

    // Cargar archivos de clases
    require_once WCBB_PLUGIN_DIR . 'includes/class-bundle-product-type.php';
    require_once WCBB_PLUGIN_DIR . 'includes/class-bundle-admin.php';
    require_once WCBB_PLUGIN_DIR . 'includes/class-bundle-ajax-handler.php';
    require_once WCBB_PLUGIN_DIR . 'includes/class-bundle-cart-handler.php';

    // Inicializar clases
    WCBB_Bundle_Product_Type::instance();
    WCBB_Bundle_Admin::instance();
    WCBB_Bundle_Ajax_Handler::instance();
    WCBB_Bundle_Cart_Handler::instance();
}
add_action( 'plugins_loaded', 'wcbb_init' );

/**
 * Cargar estilos y scripts del frontend
 */
function wcbb_enqueue_scripts() {
    if ( is_product() ) {
        global $post;
        $product = wc_get_product( $post->ID );
        
        if ( $product && $product->get_type() === 'bundle_builder' ) {
            // CSS - Forzar recarga con timestamp
            wp_enqueue_style( 
                'wcbb-bundle-builder', 
                WCBB_PLUGIN_URL . 'assets/css/bundle-builder.css', 
                array(), 
                WCBB_VERSION . '-' . time()
            );

            // JavaScript - Forzar recarga con timestamp
            wp_enqueue_script( 
                'wcbb-bundle-builder', 
                WCBB_PLUGIN_URL . 'assets/js/bundle-builder.js', 
                array( 'jquery' ), 
                WCBB_VERSION . '-' . time(), 
                true 
            );

            // Localizar script con datos del producto
            $bundle_data = array(
                'ajax_url'          => admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce( 'wcbb_nonce' ),
                'product_id'        => $post->ID,
                'max_quantity'      => get_post_meta( $post->ID, '_bundle_max_quantity', true ),
                'container_color'   => get_post_meta( $post->ID, '_bundle_container_color', true ),
                'currency_symbol'   => get_woocommerce_currency_symbol(),
                'currency_format'   => get_woocommerce_price_format(),
                'currency_decimals' => wc_get_price_decimals(),
                'currency_decimal'  => wc_get_price_decimal_separator(),
                'currency_thousand' => wc_get_price_thousand_separator(),
                'i18n'              => array(
                    'spaces_remaining'   => __( 'Espacios restantes:', 'wp-custom-bundle-builder' ),
                    'bundle_complete'    => __( '¡Bundle completo! No puedes añadir más productos.', 'wp-custom-bundle-builder' ),
                    'bundle_incomplete'  => __( 'Te faltan %d producto(s) para completar el bundle.', 'wp-custom-bundle-builder' ),
                    'add_to_cart'        => __( 'Añadir al Carrito', 'wp-custom-bundle-builder' ),
                    'add_complete'       => __( '✨ Añadir Bundle Completo', 'wp-custom-bundle-builder' ),
                    'add_incomplete'     => __( 'Añadir Bundle Incompleto', 'wp-custom-bundle-builder' ),
                    'select_products'    => __( 'Por favor, selecciona al menos un producto.', 'wp-custom-bundle-builder' ),
                    'select_products_btn'=> __( 'Selecciona Productos', 'wp-custom-bundle-builder' ),
                    'added_to_cart'      => __( '✓ Bundle added to cart successfully', 'wp-custom-bundle-builder' ),
                    'adding_incomplete'  => __( 'Añadiendo bundle con %d productos...', 'wp-custom-bundle-builder' ),
                    'error'              => __( 'Error al añadir el bundle al carrito', 'wp-custom-bundle-builder' ),
                )
            );

            wp_localize_script( 'wcbb-bundle-builder', 'wcbbData', $bundle_data );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'wcbb_enqueue_scripts' );

/**
 * Cargar estilos y scripts del admin
 */
function wcbb_admin_enqueue_scripts( $hook ) {
    global $post;

    if ( ( $hook === 'post.php' || $hook === 'post-new.php' ) && $post && $post->post_type === 'product' ) {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        
        wp_enqueue_style( 
            'wcbb-admin-bundle', 
            WCBB_PLUGIN_URL . 'assets/css/admin-bundle.css', 
            array(), 
            WCBB_VERSION 
        );

        wp_enqueue_script( 
            'wcbb-admin-bundle', 
            WCBB_PLUGIN_URL . 'assets/js/admin-bundle.js', 
            array( 'jquery', 'wp-color-picker' ), 
            WCBB_VERSION, 
            true 
        );
    }
}
add_action( 'admin_enqueue_scripts', 'wcbb_admin_enqueue_scripts' );

/**
 * Ocultar productos tipo bundle_builder de la página de shop y archivos
 */
function wcbb_hide_bundle_products_from_shop( $query ) {
    // Solo aplicar en el front-end y en queries principales de productos
    if ( ! is_admin() && $query->is_main_query() && ( is_shop() || is_product_category() || is_product_tag() ) ) {
        $tax_query = $query->get( 'tax_query' ) ? $query->get( 'tax_query' ) : array();
        
        $tax_query[] = array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'bundle_builder',
            'operator' => 'NOT IN',
        );
        
        $query->set( 'tax_query', $tax_query );
    }
}
add_action( 'pre_get_posts', 'wcbb_hide_bundle_products_from_shop' );

/**
 * Ocultar productos tipo bundle de búsquedas de productos
 */
function wcbb_hide_bundle_from_search( $query ) {
    if ( ! is_admin() && $query->is_search() && $query->get( 'post_type' ) === 'product' ) {
        $tax_query = $query->get( 'tax_query' ) ? $query->get( 'tax_query' ) : array();
        
        $tax_query[] = array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'bundle_builder',
            'operator' => 'NOT IN',
        );
        
        $query->set( 'tax_query', $tax_query );
    }
}
add_action( 'pre_get_posts', 'wcbb_hide_bundle_from_search', 20 );

/**
 * Hook de activación del plugin
 */
function wcbb_activate() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( 
            esc_html__( 'Este plugin requiere WooCommerce. Por favor, instala y activa WooCommerce primero.', 'wp-custom-bundle-builder' ),
            'Plugin dependency check',
            array( 'back_link' => true )
        );
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'wcbb_activate' );

/**
 * Hook de desactivación del plugin
 */
function wcbb_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'wcbb_deactivate' );
