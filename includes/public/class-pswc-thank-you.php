<?php
/**
 * Class PSWC_Thank_You
 *
 * This class manages the display of product subtitles on the thank you page.
 *
 * @package PSWC_Thank_You
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * PSWC_Thank_You
 */
class PSWC_Thank_You {

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
	 * This method registers WooCommerce hooks for adding product subtitles on the thank you page.
	 */
	public function event_handler() {
		// Hook into WooCommerce before the thank you page is displayed.
		add_action( 'woocommerce_before_thankyou', array( $this, 'thank_you_page_hooks' ), 10, 2 );
	}

	/**
	 * View order hook method.
	 *
	 * Adds the subtitle display action to WooCommerce order item names.
	 */
	public function thank_you_page_hooks() {
		// Hook to display the subtitle within the order item name.
		add_action( 'woocommerce_order_item_name', array( $this, 'order_item_subtitle' ), 10, 2 );
	}

	/**
	 * Order item subtitle method.
	 *
	 * Displays the product subtitle on the thank you page.
	 *
	 * @param string   $item_name The item name.
	 * @param WC_Order_Item_Product $item      The WooCommerce order item object.
	 *
	 * @return string  Modified item name with subtitle appended or prepended.
	 */
	public function order_item_subtitle( $item_name, $item ) {

		// Get the setting for subtitle position.
		$subtitle_position = get_option( 'pswc_thank_you_position', 'disable' );

		// Ensure item is a product line item and subtitle position is enabled.
		if ( $item->is_type( 'line_item' ) && 'disable' !== $subtitle_position ) {

			// Get the product ID and the subtitle from post meta.
			$product_id = $item->get_product_id();
			$subtitle   = get_post_meta( $product_id, 'pswc_subtitle', true );

			// Only proceed if subtitle exists.
			if ( ! empty( $subtitle ) ) {

				// Get the HTML tag to wrap the subtitle, defaults to 'strong'.
				$subtitle_tag = get_option( 'pswc_thank_you_tag', 'strong' );

				// Apply filter to allow customization of the subtitle HTML.
				$subtitle_html = apply_filters(
					'pswc_thank_you_subtitle_html',
					sprintf(
						'<%1$s class="product-subtitle product-subtitle-%2$d">%3$s</%1$s>',
						esc_attr( $subtitle_tag ), // Sanitize the HTML tag.
						esc_attr( $product_id ), // Ensure the product ID is properly escaped.
						esc_html( $subtitle ) // Escape the subtitle text.
					),
					$item // Pass the item object to the filter.
				);

				// Append or prepend the subtitle to the item name based on the position setting.
				$item_name = ( 'after_title' === $subtitle_position ) ? $item_name . $subtitle_html : $subtitle_html . $item_name;
			}
		}

		// Return the modified item name.
		return $item_name;
	}
}

// Instantiate the PSWC_Thank_You class to ensure the hooks are registered.
new PSWC_Thank_You();
