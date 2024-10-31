<?php
/**
 * Class PSWC_Yoast_SEO
 *
 * This PSWC Product Subtitle admin settings page
 *
 * @package PSWC_Yoast_SEO
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if( class_exists( 'WPSEO_Utils' ) && ! class_exists( 'PSWC_Yoast_SEO' ) ) :
	/**
	 * Class PSWC_Yoast_SEO
	 */
	class PSWC_Yoast_SEO {

		/**
		 * PSWC_Yoast_SEO constructor.
		 */
		public function __construct() {
			$this->event_handler();
		}

		/**
		 * Event handler for actions and filters.
		 */
		public function event_handler() {
			add_action( 'wpseo_register_extra_replacements', [ $this, 'register_subtitle_yoast_variables' ] );
			add_filter( 'wpseo_replacements', [ $this, 'filter_wpseo_replacements' ] );
		}

		/**
		 * Register the %%product_subtitle%% variable for Yoast SEO.
		 */
		public function register_subtitle_yoast_variables() {
			wpseo_register_var_replacement( 
				'%%product_subtitle%%', 
				[ $this, 'get_product_subtitle' ], 
				'advanced', 
				'Product Subtitle' 
			);
		}

		/**
		 * Get the product subtitle from post meta.
		 *
		 * @return string
		 */
		public function get_product_subtitle() {
			$post_id = get_the_ID();
			$product_subtitle = get_post_meta( $post_id, 'pswc_subtitle', true );
			return $product_subtitle;
		}

		/**
		 * Filter to update wpseo_replacements with actual product subtitle.
		 *
		 * @param array $replacements Current replacements array.
		 * @return array
		 */
		public function filter_wpseo_replacements( $replacements ) {
			if ( isset( $replacements['%%product_subtitle%%'] ) ) {
				$post_id = get_the_ID();
				$product_subtitle = get_post_meta( $post_id, 'pswc_subtitle', true );
				$replacements['%%product_subtitle%%'] = $product_subtitle;
			}
			return $replacements;
		}
	}

	new PSWC_Yoast_SEO();

endif;
