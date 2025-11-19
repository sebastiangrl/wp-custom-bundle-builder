<?php
/**
 * Handler del carrito para Bundle Builder
 *
 * @package WP_Custom_Bundle_Builder
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCBB_Bundle_Cart_Handler {

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
        // Modificar el precio del bundle en el carrito
        add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 2 );
        
        // Mostrar los productos seleccionados en el carrito
        add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
        
        // Guardar datos del bundle en la orden y aplicar precio (prioridad alta)
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 50, 4 );
        
        // Modificar el nombre del producto en el carrito
        add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 10, 3 );
        
        // Prevenir que se modifique la cantidad del bundle
        add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cart_item_quantity' ), 10, 3 );
        
        // Cargar datos del carrito desde la sesión
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 10, 2 );
        
        // Forzar el precio en el carrito antes de calcular totales
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 99, 1 );
        
        // Modificar el precio mostrado en el carrito
        add_filter( 'woocommerce_cart_item_price', array( $this, 'cart_item_price' ), 10, 3 );
        
        // Modificar el subtotal mostrado en el carrito
        add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'cart_item_subtotal' ), 10, 3 );
        
        // Filtros adicionales para forzar el precio en el checkout
        add_filter( 'woocommerce_get_cart_contents', array( $this, 'get_cart_contents' ), 10, 1 );
        add_action( 'woocommerce_cart_loaded_from_session', array( $this, 'cart_loaded_from_session' ), 99, 1 );
    }

    /**
     * Modificar item al añadir al carrito
     */
    public function add_cart_item( $cart_item, $cart_item_key ) {
        if ( isset( $cart_item['wcbb_bundle_id'] ) && isset( $cart_item['wcbb_bundle_price'] ) ) {
            // Asegurar que el precio sea numérico y mayor a 0
            $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
            
            // Si el precio es 0, recalcular desde los productos
            if ( $bundle_price <= 0 && isset( $cart_item['wcbb_selected_products'] ) ) {
                foreach ( $cart_item['wcbb_selected_products'] as $product_id => $quantity ) {
                    $product = wc_get_product( $product_id );
                    if ( $product ) {
                        $bundle_price += floatval( $product->get_price() ) * intval( $quantity );
                    }
                }
                $cart_item['wcbb_bundle_price'] = $bundle_price;
            }
            
            if ( $bundle_price > 0 ) {
                $cart_item['data']->set_price( $bundle_price );
            }
        }
        return $cart_item;
    }

    /**
     * Mostrar productos seleccionados en el carrito
     */
    public function get_item_data( $item_data, $cart_item ) {
        if ( isset( $cart_item['wcbb_selected_products'] ) && ! empty( $cart_item['wcbb_selected_products'] ) ) {
            $item_data[] = array(
                'name'  => __( 'Productos en el bundle:', 'wp-custom-bundle-builder' ),
                'value' => '',
            );

            foreach ( $cart_item['wcbb_selected_products'] as $product_id => $quantity ) {
                $product = wc_get_product( $product_id );
                if ( $product ) {
                    $item_data[] = array(
                        'name'  => '',
                        'value' => sprintf( 
                            '%s × %d - %s', 
                            $product->get_name(),
                            $quantity,
                            wc_price( $product->get_price() * $quantity )
                        ),
                    );
                }
            }
        }
        return $item_data;
    }

    /**
     * Guardar datos en la orden y establecer precio correcto
     */
    public function order_line_item( $item, $cart_item_key, $values, $order ) {
        if ( isset( $values['wcbb_bundle_id'] ) ) {
            // Guardar metadatos
            $item->add_meta_data( '_wcbb_bundle_id', $values['wcbb_bundle_id'], true );
            
            if ( isset( $values['wcbb_selected_products'] ) ) {
                $item->add_meta_data( '_wcbb_selected_products', $values['wcbb_selected_products'], true );
                
                // Añadir lista legible de productos
                $products_list = array();
                foreach ( $values['wcbb_selected_products'] as $product_id => $quantity ) {
                    $product = wc_get_product( $product_id );
                    if ( $product ) {
                        $products_list[] = sprintf( 
                            '%s × %d', 
                            $product->get_name(),
                            $quantity
                        );
                    }
                }
                
                if ( ! empty( $products_list ) ) {
                    $item->add_meta_data( __( 'Productos del bundle', 'wp-custom-bundle-builder' ), implode( ', ', $products_list ), true );
                }
            }
            
            // CRÍTICO: Establecer el precio correcto en la orden
            if ( isset( $values['wcbb_bundle_price'] ) ) {
                $bundle_price = floatval( $values['wcbb_bundle_price'] );
                
                // Si el precio es 0, recalcular desde los productos
                if ( $bundle_price <= 0 && isset( $values['wcbb_selected_products'] ) ) {
                    foreach ( $values['wcbb_selected_products'] as $product_id => $quantity ) {
                        $product = wc_get_product( $product_id );
                        if ( $product ) {
                            $bundle_price += floatval( $product->get_price() ) * intval( $quantity );
                        }
                    }
                }
                
                if ( $bundle_price > 0 ) {
                    $item->set_subtotal( $bundle_price );
                    $item->set_total( $bundle_price );
                }
            }
        }
    }

    /**
     * Modificar nombre del producto en el carrito
     */
    public function cart_item_name( $name, $cart_item, $cart_item_key ) {
        if ( isset( $cart_item['wcbb_bundle_id'] ) ) {
            $name .= ' <span class="wcbb-bundle-badge">' . __( '(Bundle Personalizado)', 'wp-custom-bundle-builder' ) . '</span>';
        }
        return $name;
    }

    /**
     * Mostrar cantidad fija para bundles (no editable)
     */
    public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
        if ( isset( $cart_item['wcbb_bundle_id'] ) ) {
            $product_quantity = sprintf( '%d', $cart_item['quantity'] );
        }
        return $product_quantity;
    }

    /**
     * Cargar datos del bundle desde la sesión
     */
    public function get_cart_item_from_session( $cart_item, $values ) {
        if ( isset( $values['wcbb_bundle_id'] ) ) {
            $cart_item['wcbb_bundle_id'] = $values['wcbb_bundle_id'];
            $cart_item['wcbb_selected_products'] = $values['wcbb_selected_products'];
            $cart_item['wcbb_bundle_price'] = isset( $values['wcbb_bundle_price'] ) ? $values['wcbb_bundle_price'] : 0;
            
            // Recalcular el precio desde los productos seleccionados si no existe o es 0
            if ( empty( $cart_item['wcbb_bundle_price'] ) && isset( $values['wcbb_selected_products'] ) ) {
                $total_price = 0;
                foreach ( $values['wcbb_selected_products'] as $product_id => $quantity ) {
                    $product = wc_get_product( $product_id );
                    if ( $product ) {
                        $total_price += floatval( $product->get_price() ) * intval( $quantity );
                    }
                }
                $cart_item['wcbb_bundle_price'] = $total_price;
            }
            
            // Establecer el precio
            $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
            if ( $bundle_price > 0 ) {
                $cart_item['data']->set_price( $bundle_price );
            }
        }
        return $cart_item;
    }

    /**
     * Forzar el precio del bundle antes de calcular totales
     */
    public function before_calculate_totals( $cart ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        // Evitar loops infinitos
        if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) {
            return;
        }

        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( isset( $cart_item['wcbb_bundle_id'] ) ) {
                $bundle_price = isset( $cart_item['wcbb_bundle_price'] ) ? floatval( $cart_item['wcbb_bundle_price'] ) : 0;
                
                // Recalcular desde productos si el precio es 0 o no existe
                if ( $bundle_price <= 0 && isset( $cart_item['wcbb_selected_products'] ) ) {
                    $bundle_price = 0;
                    foreach ( $cart_item['wcbb_selected_products'] as $product_id => $quantity ) {
                        $product = wc_get_product( $product_id );
                        if ( $product ) {
                            $bundle_price += floatval( $product->get_price() ) * intval( $quantity );
                        }
                    }
                    // Actualizar el precio en el carrito
                    $cart->cart_contents[ $cart_item_key ]['wcbb_bundle_price'] = $bundle_price;
                }
                
                // Establecer precio en el objeto del producto
                if ( $bundle_price > 0 && isset( $cart_item['data'] ) ) {
                    $cart_item['data']->set_price( $bundle_price );
                    // También actualizar en el array del carrito directamente
                    WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $bundle_price );
                }
            }
        }
    }

    /**
     * Modificar el precio mostrado en el carrito
     */
    public function cart_item_price( $price, $cart_item, $cart_item_key ) {
        if ( isset( $cart_item['wcbb_bundle_price'] ) ) {
            $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
            if ( $bundle_price > 0 ) {
                return wc_price( $bundle_price );
            }
        }
        return $price;
    }

    /**
     * Modificar el subtotal mostrado en el carrito
     */
    public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
        if ( isset( $cart_item['wcbb_bundle_price'] ) ) {
            $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
            if ( $bundle_price > 0 ) {
                $quantity = isset( $cart_item['quantity'] ) ? intval( $cart_item['quantity'] ) : 1;
                return wc_price( $bundle_price * $quantity );
            }
        }
        return $subtotal;
    }
    
    /**
     * Modificar el contenido del carrito cuando se obtiene
     */
    public function get_cart_contents( $cart_contents ) {
        foreach ( $cart_contents as $cart_item_key => $cart_item ) {
            if ( isset( $cart_item['wcbb_bundle_id'] ) && isset( $cart_item['wcbb_bundle_price'] ) ) {
                $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
                if ( $bundle_price > 0 && isset( $cart_item['data'] ) ) {
                    $cart_item['data']->set_price( $bundle_price );
                    $cart_contents[ $cart_item_key ] = $cart_item;
                }
            }
        }
        return $cart_contents;
    }
    
    /**
     * Forzar precio cuando el carrito se carga desde la sesión
     */
    public function cart_loaded_from_session( $cart ) {
        foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( isset( $cart_item['wcbb_bundle_id'] ) && isset( $cart_item['wcbb_bundle_price'] ) ) {
                $bundle_price = floatval( $cart_item['wcbb_bundle_price'] );
                if ( $bundle_price > 0 ) {
                    WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $bundle_price );
                }
            }
        }
    }
}
