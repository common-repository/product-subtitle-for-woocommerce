<?php
/**
 * Class PSWC_Order_Email
 *
 * This class manages the display of product subtitles in orders and order emails.
 *
 * @package PSWC_Order_Email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * PSWC_Order_Email
 */
class PSWC_Order_Email {

	/**
	 * Constructor
	 *
	 * Initializes the event handler.
	 */
	public function __construct() {
		$this->event_handler(); // Call the event handler method.
	}

	/**
	 * Event handler for actions and filters.
	 *
	 * This method registers WooCommerce hooks for adding product subtitles in emails.
	 */
	public function event_handler() {
		add_action( 'woocommerce_email_before_order_table', array( $this, 'email_hooks' ), 10, 2 );
	}

	/**
	 * Email hooks method.
	 *
	 * Adds the subtitle display action to the appropriate position based on user settings.
	 */
	public function email_hooks() {
		add_action( 'woocommerce_order_item_name', array( $this, 'order_item_subtitle' ), 10, 2 );
	}

	/**
	 * Order item subtitle method.
	 *
	 * Displays the product subtitle in the order emails if applicable.
	 *
	 * @param int      $item_id  The item ID.
	 * @param WC_Order_Item_Product $item  The item object.
	 * @param WC_Order $order    The order object.
	 * @param bool     $flag     Optional flag for conditional handling.
	 */
	public function order_item_subtitle( $item_name, $item ) {

		$subtitle_position  = get_option( 'pswc_order_email_position', 'disable' );
		if ( $item->is_type( 'line_item' ) && 'disable' !== $subtitle_position ) :
			$product_id 		= $item->get_product_id();	
			$subtitle 			= get_post_meta( $product_id, 'pswc_subtitle', true );

			if ( ! empty( $subtitle )  ) :

				$subtitle_tag 	= get_option( 'pswc_order_email_tag', 'strong' );
				$subtitle_html = apply_filters(
					'pswc_order_email_subtitle_html',
					sprintf(
						'<%1$s class="product-subtitle product-subtitle-%2$d">%3$s</%1$s>',
						esc_attr( $subtitle_tag ), // Sanitize the HTML tag.
						esc_attr( $product_id ), // Ensure the product ID is properly escaped.
						esc_html( $subtitle ) // Escape the subtitle text.
					),
					$item, // Pass the item object.
				);

				$item_name = ( 'after_title' == $subtitle_position ) ? $item_name . $subtitle_html : $subtitle_html . $item_name;
			endif;
		endif;
		return $item_name;
	}
}

// Instantiate the PSWC_Order_Email class to ensure the hooks are registered.
new PSWC_Order_Email();
