<?php
/**
 * Class PSWC_Minicart
 *
 * This class manages the display of product subtitles in the mini cart.
 *
 * @package PSWC_Minicart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *  PSWC_Minicart
 */
class PSWC_Minicart {

	/**
	 * The action to perform with the subtitle.
	 *
	 * @var string
	 */
	protected $subtitle_action;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->subtitle_action = get_option( 'pswc_minicart_position', 'disable' ); // Get the mini cart subtitle action from options.

		$this->event_handler(); // Call the event handler method.
	}

	/**
	 * Event handler for actions and filters
	 */
	public function event_handler() {
		if ( 'disable' !== $this->subtitle_action ) : // Check if mini cart subtitle action is not disabled.
			add_filter( 'woocommerce_cart_item_name', array( $this, 'product_subtitle_for_minicart' ), 0, 2 ); // Add filter to modify product name in mini cart.
		endif;
	}

	/**
	 * Display product subtitle in the mini cart
	 *
	 * @param string $product_name The name of the product.
	 * @param array  $cart_item The cart item.
	 * @return string The modified product name.
	 */
	public function product_subtitle_for_minicart( $product_name, $cart_item ) {

		$product = $cart_item['data']; // Get the product data from the cart item.
		if ( is_a( $product, 'WC_Product' ) && ! is_checkout() && ! is_cart() ) { // Check if the product is a WooCommerce product and if it's not in checkout or cart.
			$subtitle = $product->get_meta( 'pswc_subtitle' ); // Get the product subtitle.
			$subtitle_tag = get_option( 'pswc_minicart_tag', 'strong' ); // Get the mini cart subtitle tag option.
	
			if ( ! empty( $subtitle ) ) {
				$subtitle_html = sprintf(
					'<%1$s class="product-subtitle product-subtitle-%2$d">%3$s</%1$s>',
					esc_html( $subtitle_tag ),
					esc_attr( $product->get_id() ),
					wp_kses_post( $subtitle )
				); // Generate the subtitle HTML.
	
				$product_name = ( 'after_title' === $this->subtitle_action ) 
					? $product_name . $subtitle_html // Append the subtitle after the product name.
					: $subtitle_html . wp_kses_post( $product_name ); // Prepend the product name with the subtitle.
			}
		}
	
		return apply_filters( 'pswc_minicart_product_subtitle_html', $product_name, $cart_item ); // Return the modified product name.
	}
	
}

new PSWC_Minicart(); // Instantiate the PSWC_Minicart class.
