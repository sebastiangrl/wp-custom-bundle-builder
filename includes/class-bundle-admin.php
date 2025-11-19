<?php
/**
 * Configuración del panel admin
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCBB_Bundle_Admin {

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
        // Añadir columna en la lista de productos
        add_filter( 'manage_edit-product_columns', array( $this, 'product_columns' ) );
        add_action( 'manage_product_posts_custom_column', array( $this, 'product_column_content' ), 10, 2 );
        
        // Añadir filtro por tipo de producto
        add_action( 'restrict_manage_posts', array( $this, 'product_filters' ) );
        
        // Mensajes de ayuda
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    /**
     * Añadir columna de tipo bundle
     */
    public function product_columns( $columns ) {
        $new_columns = array();
        
        foreach ( $columns as $key => $column ) {
            $new_columns[ $key ] = $column;
            
            if ( $key === 'product_type' ) {
                $new_columns['bundle_info'] = __( 'Info Bundle', 'wp-custom-bundle-builder' );
            }
        }
        
        return $new_columns;
    }

    /**
     * Contenido de la columna bundle
     */
    public function product_column_content( $column, $post_id ) {
        if ( $column === 'bundle_info' ) {
            $product = wc_get_product( $post_id );
            
            if ( $product && $product->get_type() === 'bundle_builder' ) {
                $max_qty = get_post_meta( $post_id, '_bundle_max_quantity', true ) ?: 4;
                $allowed_products = get_post_meta( $post_id, '_bundle_allowed_products', true );
                $products_count = is_array( $allowed_products ) ? count( $allowed_products ) : 0;
                
                echo '<strong>' . esc_html__( 'Máx:', 'wp-custom-bundle-builder' ) . '</strong> ' . esc_html( $max_qty ) . '<br>';
                echo '<strong>' . esc_html__( 'Productos:', 'wp-custom-bundle-builder' ) . '</strong> ' . esc_html( $products_count );
            } else {
                echo '—';
            }
        }
    }

    /**
     * Añadir filtros en la lista de productos
     */
    public function product_filters() {
        global $typenow;
        
        if ( $typenow === 'product' ) {
            $current_type = isset( $_GET['product_type'] ) ? sanitize_text_field( $_GET['product_type'] ) : '';
            ?>
            <select name="product_type">
                <option value=""><?php esc_html_e( 'Todos los tipos', 'wp-custom-bundle-builder' ); ?></option>
                <option value="bundle_builder" <?php selected( $current_type, 'bundle_builder' ); ?>>
                    <?php esc_html_e( 'Bundle Builder', 'wp-custom-bundle-builder' ); ?>
                </option>
            </select>
            <?php
        }
    }

    /**
     * Mostrar avisos en el admin
     */
    public function admin_notices() {
        $screen = get_current_screen();
        
        if ( $screen && $screen->id === 'product' && isset( $_GET['post'] ) ) {
            $product = wc_get_product( $_GET['post'] );
            
            if ( $product && $product->get_type() === 'bundle_builder' ) {
                $allowed_products = get_post_meta( $product->get_id(), '_bundle_allowed_products', true );
                
                if ( empty( $allowed_products ) || ! is_array( $allowed_products ) ) {
                    ?>
                    <div class="notice notice-warning">
                        <p>
                            <strong><?php esc_html_e( 'Bundle Builder:', 'wp-custom-bundle-builder' ); ?></strong>
                            <?php esc_html_e( 'No has seleccionado productos para este bundle. Por favor, selecciona al menos un producto en la pestaña "Bundle Builder".', 'wp-custom-bundle-builder' ); ?>
                        </p>
                    </div>
                    <?php
                }
            }
        }
    }
}
