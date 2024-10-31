<?php
/**
 * Class PSWC_Cart
 *
 * This class manages the display of product subtitles in the cart.
 *
 * @package PSWC_Cart
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PSWC_Cart
 */
class PSWC_Cart {

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
		$this->subtitle_action = get_option( 'pswc_cart_position', 'disable' ); // Get the subtitle action from options.

		$this->event_handler(); // Call the event handler method.
	}

	/**
	 * Event handler for actions and filters
	 */
	public function event_handler() {
		if ( 'disable' !== $this->subtitle_action ) : // Check if subtitle action is not disabled.
			add_filter( 'woocommerce_cart_item_name', array( $this, 'product_subtitle_for_cart' ), 0, 2 ); // Add filter to modify product name in cart.
		endif;
	}

	/**
	 * Display product subtitle in the cart
	 *
	 * @param string $product_name The name of the product.
	 * @param array  $cart_item The cart item.
	 * @return string The modified product name.
	 */
	public function product_subtitle_for_cart( $product_name, $cart_item ) {

		$product = $cart_item['data']; // Get the product data from the cart item.
		if ( is_a( $product, 'WC_Product' ) && is_cart() ) { // Check if the product is a WooCommerce product and if it's in the cart.
			$subtitle     = $product->get_meta( 'pswc_subtitle' ); // Get the product subtitle.
			$subtitle_tag = get_option( 'pswc_cart_tag', 'strong' ); // Get the subtitle tag option.
	
			if ( ! empty( $subtitle ) ) { // Check if the subtitle is not empty.
				$subtitle_html = sprintf(
					'<%1$s class="product-subtitle product-subtitle-%2$d">%3$s</%1$s>',
					esc_html( $subtitle_tag ),
					esc_attr( $product->get_id() ),
					wp_kses_post( $subtitle )
				); // Create the subtitle HTML only once.
	
				// Conditionally append or prepend the subtitle to the product name.
				$product_name = ( 'after_title' === $this->subtitle_action )
					? $product_name . $subtitle_html // Append after the product name.
					: $subtitle_html . $product_name; // Prepend before the product name.
			}
		}
	
		return apply_filters( 'pswc_cart_page_product_subtitle_html', $product_name, $cart_item ); // Return the modified product name.
	}
	
}

new PSWC_Cart(); // Instantiate the PSWC_Cart class.
