<?php
/**
 * Class PSWC_Admin
 *
 * This PSWC Product Subtitle admin settings page
 *
 * @package PSWC_Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PSWC_Admin
 */
class PSWC_Admin {

	/**
	 * PSWC_Admin constructor.
	 */
	public function __construct() {
		$this->event_handler();
	}

	/**
	 * Event handler for actions and filters.
	 */
	public function event_handler() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_styles' ] );

		$pswc_enable_subtitle_col = get_option( 'pswc_enable_subtitle_col' );
		if ( 'yes' === $pswc_enable_subtitle_col ) :
			add_filter( 'manage_product_posts_columns', array( $this, 'add_product_subcolumn' ) );
			add_action( 'manage_product_posts_custom_column', array( $this, 'pswc_product_subcolumn' ), 10, 2 );
		endif;
		$pswc_enable_admin_order_view = get_option( 'pswc_enable_admin_order_view' );
		if ( 'yes' === $pswc_enable_admin_order_view ) :
			add_action( 'woocommerce_before_order_itemmeta', array( $this, 'subtitle_on_order' ), 10, 3 );
		endif;
	}

	/**
	 * Add product subtitle column.
	 *
	 * @param  array $columns Columns array.
	 * @return array
	 */
	public function add_product_subcolumn( $columns ) {
		$columns['product_subtitle'] = esc_html__( 'Product Subtitle', 'product-subtitle-for-woocommerce' );
		return $columns;
	}

	/**
	 * Output product subtitle in column.
	 *
	 * @param string $column  Column name.
	 * @param int    $post_id Post ID.
	 */
	public function pswc_product_subcolumn( $column, $post_id ) {
		if ( 'product_subtitle' === $column ) {
			$product_subtitle = get_post_meta( $post_id, 'pswc_subtitle', true );
			echo wp_kses_post( $product_subtitle );
		}
	}

	/**
	 * Display product subtitle on order item.
	 *
	 * @param int    $item_id Order item ID.
	 * @param object $item    Order item object.
	 * @param object $product Product object.
	 */
	public function subtitle_on_order( $item_id, $item, $product ) {
		if ( $product ) :
			$subtitle = $product->get_meta( 'pswc_subtitle' );
			if ( $subtitle ) :
				printf(
					'<div class="product-subtitle">%s</div>',
					wp_kses_post( $subtitle )
				);
			endif;
		endif;
	}

	/**
	 * Enqueue the custom CSS file in the admin area
	 */
	public function enqueue_admin_styles() {
		// Enqueue the CSS file.
		wp_enqueue_style( 'admin-style', PSWC_URL . 'assets/css/admin-style.css', array(), PSWC_VERSION, 'all' );
	}
}

new PSWC_Admin();
