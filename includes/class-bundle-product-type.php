<?php
/**
 * Clase para registrar el tipo de producto Bundle Builder
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCBB_Bundle_Product_Type {

    /**
     * Instancia única de la clase
     */
    private static $instance = null;

    /**
     * Obtener instancia única
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
        add_filter( 'product_type_selector', array( $this, 'add_bundle_product_type' ) );
        add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_bundle_product_tab' ) );
        add_action( 'woocommerce_product_data_panels', array( $this, 'add_bundle_product_panel' ) );
        add_action( 'woocommerce_process_product_meta_bundle_builder', array( $this, 'save_bundle_product_options' ) );
        add_action( 'woocommerce_bundle_builder_add_to_cart', array( $this, 'bundle_add_to_cart_template' ) );
        
        // Registrar el tipo de producto
        add_action( 'init', array( $this, 'register_bundle_product_class' ) );
    }

    /**
     * Añadir tipo de producto al selector
     */
    public function add_bundle_product_type( $types ) {
        $types['bundle_builder'] = __( 'Bundle Builder', 'wp-custom-bundle-builder' );
        return $types;
    }

    /**
     * Registrar la clase del producto
     */
    public function register_bundle_product_class() {
        require_once WCBB_PLUGIN_DIR . 'includes/class-wc-product-bundle-builder.php';
    }

    /**
     * Añadir tab personalizado en el panel de producto
     */
    public function add_bundle_product_tab( $tabs ) {
        $tabs['bundle_builder'] = array(
            'label'    => __( 'Bundle Builder', 'wp-custom-bundle-builder' ),
            'target'   => 'bundle_builder_product_data',
            'class'    => array( 'show_if_bundle_builder' ),
            'priority' => 21,
        );
        return $tabs;
    }

    /**
     * Añadir panel de configuración del bundle
     */
    public function add_bundle_product_panel() {
        global $post;
        ?>
        <div id="bundle_builder_product_data" class="panel woocommerce_options_panel">
            
            <div class="options_group">
                <h4 style="padding: 10px 12px; margin: 0; border-bottom: 1px solid #eee;">
                    <?php esc_html_e( '⚙️ Configuración General', 'wp-custom-bundle-builder' ); ?>
                </h4>
                <?php
                woocommerce_wp_text_input(
                    array(
                        'id'                => '_bundle_max_quantity',
                        'label'             => __( 'Cantidad máxima de productos', 'wp-custom-bundle-builder' ),
                        'description'       => __( 'Número máximo de productos que el cliente puede seleccionar en el bundle', 'wp-custom-bundle-builder' ),
                        'type'              => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min'  => '1',
                        ),
                        'value'             => get_post_meta( $post->ID, '_bundle_max_quantity', true ) ?: '4',
                    )
                );

                woocommerce_wp_checkbox(
                    array(
                        'id'          => '_bundle_allow_incomplete',
                        'label'       => __( 'Permitir bundles incompletos', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Permitir añadir al carrito bundles que no estén completos', 'wp-custom-bundle-builder' ),
                        'value'       => get_post_meta( $post->ID, '_bundle_allow_incomplete', true ) ?: 'yes',
                    )
                );

                woocommerce_wp_checkbox(
                    array(
                        'id'          => '_bundle_hide_price',
                        'label'       => __( 'Ocultar precio hasta selección', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Ocultar el precio total hasta que el usuario seleccione productos', 'wp-custom-bundle-builder' ),
                    )
                );
                ?>
            </div>

            <div class="options_group">
                <h4 style="padding: 10px 12px; margin: 0; border-bottom: 1px solid #eee;">
                    <?php esc_html_e( '🎨 Personalización de Diseño', 'wp-custom-bundle-builder' ); ?>
                </h4>
                <?php
                woocommerce_wp_text_input(
                    array(
                        'id'          => '_bundle_container_color',
                        'label'       => __( 'Color del contenedor', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Color de fondo del contenedor del bundle', 'wp-custom-bundle-builder' ),
                        'type'        => 'text',
                        'value'       => get_post_meta( $post->ID, '_bundle_container_color', true ) ?: '#2c3e50',
                        'class'       => 'wcbb-color-picker',
                    )
                );

                woocommerce_wp_text_input(
                    array(
                        'id'          => '_bundle_button_color',
                        'label'       => __( 'Color del botón añadir', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Color del botón de añadir al carrito', 'wp-custom-bundle-builder' ),
                        'type'        => 'text',
                        'value'       => get_post_meta( $post->ID, '_bundle_button_color', true ) ?: '#27ae60',
                        'class'       => 'wcbb-color-picker',
                    )
                );

                woocommerce_wp_text_input(
                    array(
                        'id'          => '_bundle_accent_color',
                        'label'       => __( 'Color de acento', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Color de los botones y elementos interactivos', 'wp-custom-bundle-builder' ),
                        'type'        => 'text',
                        'value'       => get_post_meta( $post->ID, '_bundle_accent_color', true ) ?: '#3498db',
                        'class'       => 'wcbb-color-picker',
                    )
                );

                woocommerce_wp_text_input(
                    array(
                        'id'                => '_bundle_border_radius',
                        'label'             => __( 'Border radius (px)', 'wp-custom-bundle-builder' ),
                        'description'       => __( 'Redondeo de esquinas de los elementos (0 = cuadrado, 20 = muy redondeado)', 'wp-custom-bundle-builder' ),
                        'type'              => 'number',
                        'custom_attributes' => array(
                            'step' => '1',
                            'min'  => '0',
                            'max'  => '50',
                        ),
                        'value'             => get_post_meta( $post->ID, '_bundle_border_radius', true ) ?: '4',
                    )
                );

                woocommerce_wp_select(
                    array(
                        'id'          => '_bundle_design_style',
                        'label'       => __( 'Estilo de diseño', 'wp-custom-bundle-builder' ),
                        'description' => __( 'Elige el estilo visual del bundle builder', 'wp-custom-bundle-builder' ),
                        'options'     => array(
                            'minimal' => __( 'Minimalista (Flat)', 'wp-custom-bundle-builder' ),
                            'shadow'  => __( 'Con sombras suaves', 'wp-custom-bundle-builder' ),
                            'border'  => __( 'Con bordes', 'wp-custom-bundle-builder' ),
                        ),
                        'value'       => get_post_meta( $post->ID, '_bundle_design_style', true ) ?: 'minimal',
                    )
                );
                ?>
            </div>

            <div class="options_group">
                <h4 style="padding: 10px 12px; margin: 0; border-bottom: 1px solid #eee;">
                    <?php esc_html_e( '📦 Productos del Bundle', 'wp-custom-bundle-builder' ); ?>
                </h4>
                <p class="form-field">
                    <label><?php esc_html_e( 'Productos permitidos en el bundle', 'wp-custom-bundle-builder' ); ?></label>
                    <select 
                        id="_bundle_allowed_products" 
                        name="_bundle_allowed_products[]" 
                        class="wc-product-search" 
                        multiple="multiple" 
                        style="width: 50%;" 
                        data-placeholder="<?php esc_attr_e( 'Buscar productos y variaciones&hellip;', 'wp-custom-bundle-builder' ); ?>" 
                        data-action="woocommerce_json_search_products_and_variations"
                        data-exclude="<?php echo intval( $post->ID ); ?>"
                    >
                        <?php
                        $product_ids = get_post_meta( $post->ID, '_bundle_allowed_products', true );
                        if ( ! empty( $product_ids ) && is_array( $product_ids ) ) {
                            foreach ( $product_ids as $product_id ) {
                                $product = wc_get_product( $product_id );
                                if ( $product ) {
                                    echo '<option value="' . esc_attr( $product_id ) . '" selected="selected">' . esc_html( wp_strip_all_tags( $product->get_formatted_name() ) ) . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                    <span class="description"><?php esc_html_e( 'Selecciona los productos y variaciones que estarán disponibles para este bundle', 'wp-custom-bundle-builder' ); ?></span>
                </p>
            </div>

            <style>
                .show_if_bundle_builder { display: none; }
                .product-type-bundle_builder .show_if_bundle_builder { display: block !important; }
                .product-type-bundle_builder .hide_if_bundle_builder { display: none !important; }
                .product-type-bundle_builder .general_tab { display: block !important; }
            </style>
        </div>
        <?php
    }

    /**
     * Guardar opciones del producto bundle
     */
    public function save_bundle_product_options( $post_id ) {
        // Cantidad máxima
        $max_quantity = isset( $_POST['_bundle_max_quantity'] ) ? absint( $_POST['_bundle_max_quantity'] ) : 4;
        update_post_meta( $post_id, '_bundle_max_quantity', $max_quantity );

        // Permitir bundles incompletos
        $allow_incomplete = isset( $_POST['_bundle_allow_incomplete'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_bundle_allow_incomplete', $allow_incomplete );

        // Color del contenedor
        $container_color = isset( $_POST['_bundle_container_color'] ) ? sanitize_hex_color( $_POST['_bundle_container_color'] ) : '#2c3e50';
        update_post_meta( $post_id, '_bundle_container_color', $container_color );

        // Color del botón
        $button_color = isset( $_POST['_bundle_button_color'] ) ? sanitize_hex_color( $_POST['_bundle_button_color'] ) : '#27ae60';
        update_post_meta( $post_id, '_bundle_button_color', $button_color );

        // Color de acento
        $accent_color = isset( $_POST['_bundle_accent_color'] ) ? sanitize_hex_color( $_POST['_bundle_accent_color'] ) : '#3498db';
        update_post_meta( $post_id, '_bundle_accent_color', $accent_color );

        // Border radius
        $border_radius = isset( $_POST['_bundle_border_radius'] ) ? absint( $_POST['_bundle_border_radius'] ) : 4;
        update_post_meta( $post_id, '_bundle_border_radius', $border_radius );

        // Estilo de diseño
        $design_style = isset( $_POST['_bundle_design_style'] ) ? sanitize_text_field( $_POST['_bundle_design_style'] ) : 'minimal';
        update_post_meta( $post_id, '_bundle_design_style', $design_style );

        // Productos permitidos
        $allowed_products = isset( $_POST['_bundle_allowed_products'] ) ? array_map( 'absint', (array) $_POST['_bundle_allowed_products'] ) : array();
        update_post_meta( $post_id, '_bundle_allowed_products', $allowed_products );

        // Ocultar precio
        $hide_price = isset( $_POST['_bundle_hide_price'] ) ? 'yes' : 'no';
        update_post_meta( $post_id, '_bundle_hide_price', $hide_price );

        // Marcar como virtual y no requiere envío por defecto
        update_post_meta( $post_id, '_virtual', 'no' );
        update_post_meta( $post_id, '_sold_individually', 'yes' );
    }

    /**
     * Template del botón añadir al carrito
     */
    public function bundle_add_to_cart_template() {
        include WCBB_PLUGIN_DIR . 'templates/bundle-builder-template.php';
    }
}
