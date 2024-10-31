<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if( ! class_exists( 'PSWC_Guternuburg_Block' ) ) :

    class PSWC_Guternuburg_Block {

        public function __construct() {
            add_action( 'init', [ $this, 'register_block' ] );
        }

        /**
         * Registers the block and enqueues scripts.
         */
        public function register_block() {

            // Register the block editor script
            wp_register_script(
                'pswc-product-subtitle-block-editor',
                PSWC_URL . '/build/block.js',
                array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components' ),
                PSWC_VERSION
            );

            // Register the block type with render callback
            register_block_type( 'pswc/product-subtitle-block', array(
                'editor_script'   => 'pswc-product-subtitle-block-editor',
                'render_callback' => [ $this, 'render_block' ],
                'attributes'      => $this->get_block_attributes(),
            ) );
        }

        /**
         * Defines block attributes.
         *
         * @return array Attributes for the block.
         */
        private function get_block_attributes() {
            return array(
                'htmlTag' => array(
                    'type'    => 'string',
                    'default' => 'div',
                ),
                'fallbackText' => array(
                    'type'    => 'string',
                    'default' => 'Default Subtitle',
                ),
                'textColor' => array(
                    'type'    => 'string',
                    'default' => 'inherit',
                ),
                'backgroundColor' => array(
                    'type'    => 'string',
                    'default' => 'inherit',
                ),
            );
        }

        /**
         * Renders the block output on the frontend.
         *
         * @param array $attributes Block attributes.
         * @return string Block HTML output.
         */
        public function render_block( $attributes ) {

            // Sanitize attributes
            $tag = isset( $attributes['htmlTag'] ) ? esc_html( $attributes['htmlTag'] ) : 'div';
            $fallback_text = isset( $attributes['fallbackText'] ) ? esc_html( $attributes['fallbackText'] ) : 'Default Subtitle';
            $text_color = isset( $attributes['textColor'] ) ? esc_attr( $attributes['textColor'] ) : 'inherit';
            $background_color = isset( $attributes['backgroundColor'] ) ? esc_attr( $attributes['backgroundColor'] ) : 'transparent';

            // Fetch the current product's subtitle
            $product_subtitle = $this->get_product_subtitle();

            // Use fallback text if subtitle isn't available
            $text = ! empty( $product_subtitle ) ? $product_subtitle : $fallback_text;

            // Return the HTML output
            return sprintf(
                '<%1$s class="pswc-product-subtitle" style="color: %2$s; background-color: %3$s;">%4$s</%1$s>',
                $tag,
                $text_color,
                $background_color,
                esc_html( $text )
            );
        }

        /**
         * Retrieves the product subtitle from WooCommerce product metadata.
         *
         * @return string|null The product subtitle if available, or null.
         */
        private function get_product_subtitle() {

            // Check if WooCommerce is active and get the global product
            if ( class_exists( 'WooCommerce' ) && is_product() ) {
                global $product;
                if ( $product instanceof WC_Product ) {
                    return get_post_meta( $product->get_id(), 'pswc_subtitle', true );
                }
            }
            return null;
        }
    }

    // Instantiate the block class
    new PSWC_Guternuburg_Block();
endif;
