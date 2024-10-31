<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor Widget for Product Subtitle with Custom HTML Tag and Styling
 */
if ( ! class_exists( 'PSWC_Product_Subtitle_Widget' ) ) {

    class PSWC_Product_Subtitle_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'pswc_product_subtitle_widget';
        }

        public function get_title() {
            return __( 'Product Subtitle', 'plugin-name' );
        }

        public function get_icon() {
            return 'eicon-product-title'; // Elementor icon for the widget.
        }

        public function get_categories() {
            return [ 'basic' ]; // You can change the category based on your need.
        }

        public function get_keywords() {
            return [ 'html', 'tag', 'style', 'subtitle', 'custom' ];
        }

        protected function register_controls() {  // Changed to register_controls

            // Start Section for Content
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __( 'Content', 'plugin-name' ),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            // Add control for HTML tag selection from filtered list.
            $this->add_control(
                'html_tag',
                [
                    'label' => __( 'HTML Tag', 'plugin-name' ),
                    'type' => \Elementor\Controls_Manager::SELECT,
                    'options' => apply_filters( 'pswc_supported_html_tags',		
                        array(
                            'i'      => '<i>',
                            'p'      => '<p>',
                            'mark'   => '<mark>',
                            'span'   => '<span>',
                            'small'  => '<small>',
                            'strong' => '<strong>',
                            'div'    => '<div>',
                            'h1'     => '<h1>',
                            'h2'     => '<h2>',
                            'h3'     => '<h3>',
                            'h4'     => '<h4>',
                            'h5'     => '<h5>',
                            'h6'     => '<h6>',
                        )
                    ),
                    'default' => 'div',
                ]
            );

            // Add a fallback text input in case the product subtitle isn't available.
            $this->add_control(
                'fallback_text',
                [
                    'label' => __( 'Fallback Subtitle Text', 'plugin-name' ),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __( 'Default Subtitle', 'plugin-name' ),
                    'placeholder' => __( 'Enter your fallback subtitle', 'plugin-name' ),
                ]
            );

            $this->end_controls_section();

            // Start Style Section
            $this->start_controls_section(
                'style_section',
                [
                    'label' => __( 'Style', 'plugin-name' ),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            // Add typography control
            $this->add_group_control(
                \Elementor\Group_Control_Typography::get_type(),
                [
                    'name' => 'typography',
                    'label' => __( 'Typography', 'plugin-name' ),
                    'selector' => '{{WRAPPER}} {{CURRENT_TAG}}',
                ]
            );

            // Add color control
            $this->add_control(
                'text_color',
                [
                    'label' => __( 'Text Color', 'plugin-name' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_TAG}}' => 'color: {{VALUE}}',
                    ],
                ]
            );

            // Add background color control
            $this->add_control(
                'background_color',
                [
                    'label' => __( 'Background Color', 'plugin-name' ),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} {{CURRENT_TAG}}' => 'background-color: {{VALUE}}',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();

            // Get the selected HTML tag
            $tag = isset($settings['html_tag']) ? $settings['html_tag'] : 'div';

            // Fetch the current product's subtitle
            $product_subtitle = $this->pswc_get_product_subtitle();

            // Use fallback text if product subtitle is not available
            $text = ! empty( $product_subtitle ) ? $product_subtitle : $settings['fallback_text'];
            // Get color settings with default fallback to avoid issues
            $text_color = isset($settings['text_color']) ? $settings['text_color'] : 'inherit';

            $background_color = isset($settings['background_color']) ? $settings['background_color'] : 'transparent';

            // Render the HTML
            echo sprintf(
                '<%1$s lass="product-subtitle product-subtitle-%4$d" style="color: %2$s; background-color: %3$s;">%4$s</%1$s>',
                esc_html( $tag ),
                esc_attr( $text_color ),
                esc_attr( $background_color ),
                esc_html( $text )
            );
        }

        /**
         * Retrieve the product subtitle from the current product's metadata
         *
         * @return string|null
         */
        private function pswc_get_product_subtitle() {

            // Ensure WooCommerce is active and get the global product object
            if ( class_exists( 'WooCommerce' ) && is_product() ) {
                global $product;
                if ( $product instanceof WC_Product ) {
                    // Assuming the subtitle is stored in the custom field 'product_subtitle'
                    return get_post_meta( $product->get_id(), 'pswc_subtitle', true );
                }
            }
            return null;
        }

        protected function _content_template() {
            ?>
            <#
            var tag = settings.html_tag;
            var fallbackText = settings.fallback_text;
            var text = fallbackText; // Placeholder, dynamic rendering will handle product subtitle.
            var textColor = settings.text_color;
            var backgroundColor = settings.background_color;
            #>
            <{{{ tag }}} style="color: {{{ textColor }}}; background-color: {{{ backgroundColor }}};">
                {{{ text }}}
            </{{{ tag }}}>
            <?php
        }
    }

}