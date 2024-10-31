<?php
/**
 * Class PSWC_Single
 *
 * This class manages the display of product subtitles on single product pages and order item names.
 *
 * @package PSWC_Single
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * PSWC_Single
 */
class PSWC_Single {


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->event_handler(); // Call the event handler method.
	}

	/**
	 * Event handler for actions and filters
	 */
	public function event_handler() {
		$single_position = get_option( 'pswc_single_position', 'disable-0' );
		$hook            = ! empty( $single_position ) ? explode( '-', $single_position ) : array();

		if ( is_array( $hook ) ) :
			add_action( $hook[0], array( $this, 'display_product_subtitle' ), $hook[1] );
		endif;
		add_shortcode( 'PSWC_Subtitle', array( $this, 'get_product_subtitle' ) );
	}

	/**
	 * Add product subtitle to order item name
	 */
	public function display_product_subtitle() {
		global $product;
		$product_id   = $product->get_id(); // Get the product ID.
		$subtitle_tag = get_option( 'pswc_single_tag', 'strong' ); // Get the single subtitle tag option.
		$subtitle     = get_post_meta( $product_id, 'pswc_subtitle', true ); // Get the product subtitle.

		// Display the subtitle if it exists.
		if ( ! empty( $subtitle ) ) :
			$subtitle_html = sprintf(
				'<%1$s class="product-subtitle product-subtitle-%2$d">%3$s</%1$s>',
				esc_html( $subtitle_tag ), // Ensure the subtitle tag is safe.
				esc_attr( $product_id ),
				wp_kses_post( $subtitle )  // Allow safe HTML in the subtitle.
			);

			echo wp_kses_post( apply_filters( 'pswc_single_page_product_subtitle_html', $subtitle_html, $subtitle_tag, $subtitle, $product ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		endif;
	}

	/**
	 * Product Subtitle Shortcode
	 *
	 * @param array $atts Attributes.
	 * @return string
	 */
	public function get_product_subtitle( $atts ) {
		global $post;

		// Define shortcode attributes.
		$atts = shortcode_atts(
			array(
				'product_id' 	=> 0,
				'tagname'		=> 'p'
			),
			$atts,
			'PSWC'
		);

		// Get product ID from attributes or use the current post ID.
		$product_id = isset( $atts['product_id'] ) ? intval( $atts['product_id'] ) : 0;
		$tagname 	= isset( $atts['tagname'] ) ? esc_html( $atts['tagname'] ) : 0;
		if ( ! $product_id && isset( $post->ID ) ) :
			$product_id = $post->ID;
		endif;
		
		// If no valid product ID, return a message.
		if ( ! $product_id ) :
			return false;
		endif;

		// Get the single subtitle tag option.
		$subtitle_tag = $tagname ? $tagname : get_option( 'pswc_single_tag', 'h2' ); // Default to 'h2' if not set.
		$subtitle     = get_post_meta( $product_id, 'pswc_subtitle', true ); // Get  product subtitle.

		// Display the subtitle if it exists.
		if ( ! empty( $subtitle ) ) :
			$subtitle_html = sprintf(
				'<%1$s class="product-subtitle product-subtitle-%3$d">%2$s</%1$s>',
				esc_html( $subtitle_tag ), // Ensure the subtitle tag is safe.
				wp_kses_post( $subtitle ),  // Allow safe HTML in the subtitle.
				esc_html( $product_id ),
			);

			// Apply filters and return the subtitle HTML.
			return apply_filters( 'pswc_shortcode_product_subtitle_html', $subtitle_html, $subtitle_tag, $subtitle, $product_id );
		endif;
		return false; // Return empty if no subtitle found.
	}
}

new PSWC_Single(); // Instantiate the PSWC_Single class.
