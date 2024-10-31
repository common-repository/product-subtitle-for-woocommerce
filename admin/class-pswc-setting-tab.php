<?php
/**
 * Class PSWC_Admin
 *
 * This class manages the PSWC Product Subtitle admin settings page.
 *
 * @package PSWC_Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class PSWC_Setting_Tab
 */
class PSWC_Setting_Tab {


	/**
	 * The ID of the settings tab.
	 *
	 * @var string
	 */
	private $id;

	/**
	 * The label of the settings tab.
	 *
	 * @var string
	 */
	private $label;

	/**
	 * The section ID of the settings tab.
	 *
	 * @var string
	 */
	private $section_id;

	/**
	 * Flag to determine if subtitles are enabled.
	 *
	 * @var string
	 */
	private $enable_subtitle;

	/**
	 * Flag to determine if the subtitle column is enabled.
	 *
	 * @var string
	 */
	private $pswc_enable_subtitle_col;

	/**
	 * Flag to determine if the WYSIWYG editor is enabled.
	 *
	 * @var string
	 */
	private $pswc_enable_wysiwyg;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id                 = 'pswc'; // Set the ID of the settings tab.
		$this->label              = esc_html__( 'Product Subtitle', 'product-subtitle-for-woocommerce' ); // Set the label of the settings tab.
		$this->section_id         = 'pswc-general'; // Set the section ID of the settings tab.
		$this->enable_subtitle    = get_option( 'pswc_disable_subtitle', 'no' ); // Get the option for enabling subtitles.
		$this->pswc_enable_wysiwyg  = get_option( 'pswc_enable_wysiwyg' ); // Get the option for enabling the WYSIWYG editor.

		$this->event_handler(); // Call the event handler method.
	}

	/**
	 * Event handler for actions and filters
	 */
	public function event_handler() {
		add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_tab' ), 25, 1 ); // Add a filter to include the settings tab.

		add_action( 'woocommerce_settings_' . $this->id, array( $this, 'settings_tab' ) ); // Add action to display the settings tab.
		add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save_pswc_woocommerce_settings' ) ); // Add action to save settings.
		add_filter( 'plugin_action_links_product-subtitle-for-woocommerce/product-subtitle-for-woocommerce.php', array( $this, 'settings_link' ), 20 ); // Add setting tab.

		if ( 'no' === $this->enable_subtitle ) :
			add_action( 'edit_form_after_title', array( $this, 'subtitle_field' ) ); // Add action to display custom subtitle field.
			add_action( 'save_post', array( $this, 'save_pswc_subtitle_field' ) ); // Add action to save subtitle field.
		endif;
	}

	/**
	 * Add settings tab
	 *
	 * @param  array $settings_tabs Settings tabs array.
	 * @return array
	 */
	public function add_settings_tab( $settings_tabs ) {
		$settings_tabs[ $this->id ] = $this->label; // Add the settings tab to the tabs array.
		return $settings_tabs; // Return the modified settings tabs array.
	}

	/**
	 * Settings tab content
	 */
	public function settings_tab() {
		woocommerce_admin_fields( $this->get_settings() ); // Display the settings fields.
		require_once 'views/ask-help.html';
	}

	/**
	 * Settings product subtitle subtitle html tags
	 */
	public function settings_spported_html_tags_options() {
		return apply_filters( 'pswc_supported_html_tags',		
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
		);
	}

	/**
	 * Get settings fields
	 *
	 * @return array
	 */
	public function get_settings() {
		$options  = $this->settings_spported_html_tags_options();
		$settings = array(
			'pswc_title'                   => array(
				'name' => esc_html__( 'Product Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_title',
			),
			'pswc_disable_subtitle'         => array(
				'name' 		=> esc_html__( 'Disable Product Subtitle', 'product-subtitle-for-woocommerce' ),
				'type' 		=> 'checkbox',
				'desc' 		=> esc_html__( 'Check this box to disable subtitles for all products sitewide.', 'product-subtitle-for-woocommerce' ),
				'id'   		=> 'pswc_disable_subtitle',
				'default' 	=> 'no',
			),
			'pswc_enable_subtitle_col'     => array(
				'name' => esc_html__( 'Enable Subtitle Column', 'product-subtitle-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'If enabled, a subtitle column will be added to the product listing table.', 'product-subtitle-for-woocommerce' ),
				'id'   => 'pswc_enable_subtitle_col',
			),
			'pswc_enable_admin_order_view' => array(
				'name' => esc_html__( 'Enable Subtitle in Admin Order View', 'product-subtitle-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'If enabled, a subtitle column will appear in the single order product listing table in the admin panel.', 'product-subtitle-for-woocommerce' ),
				'id'   => 'pswc_enable_admin_order_view',
			),
			'pswc_enable_wysiwyg'           => array(
				'name' => esc_html__( 'Enable HTML Editor for Subtitle?', 'product-subtitle-for-woocommerce' ),
				'type' => 'checkbox',
				'desc' => esc_html__( 'Enable to use a WYSIWYG editor (TinyMCE) for the subtitle field. If disabled, a simple text box will be displayed for editing the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'   => 'pswc_enable_wysiwyg',
			),
			'section_end'                  => array(
				'type' => 'sectionend',
				'id'   => 'pswc_title',
			),
			'pswc_cart_start'              => array(
				'name' => esc_html__( 'Cart Page Subtitle Configuration', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_cart_start',
			),
			'pswc_cart_position'           => array(
				'name'    => esc_html__( 'Subtitle Placement', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Choose the placement for the subtitle in the cart.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_cart_position',
				'default' => 'disable',
			),
			'pswc_cart_tag'                => array(
				'name'    => esc_html__( 'Subtitle HTML Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the cart subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_cart_tag',
				'default' => 'strong',
			),
			'cart_section_end'             => array(
				'type' => 'sectionend',
				'id'   => 'cart_section_end',
			),
			'pswc_minicart_start'          => array(
				'name' => esc_html__( 'Minicart Subtitle Configuration', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_minicart_start',
			),
			'pswc_minicart_position'       => array(
				'name'    => esc_html__( 'Subtitle Placement', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Choose the placement for the subtitle in the minicart.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_minicart_position',
				'default' => 'disable',
			),
			'pswc_minicart_tag'            => array(
				'name'    => esc_html__( 'Subtitle HTML Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the minicart subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_minicart_tag',
				'default' => 'strong',
			),
			'pswc_minicart_end'            => array(
				'type' => 'sectionend',
				'id'   => 'pswc_minicart_end',
			),
			'pswc_checkout_title'          => array(
				'name' => esc_html__( 'Checkout Subtitle Configuration', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_checkout_title',
			),
			'pswc_checkout_position'       => array(
				'name'    => esc_html__( 'Subtitle Placement', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Choose the placement for the subtitle in the checkout.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_checkout_position',
				'default' => 'disable',
			),
			'pswc_checkout_tag'            => array(
				'name'    => esc_html__( 'Subtitle HTML Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the checkout subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_checkout_tag',
				'default' => 'strong',
			),
			'checkout_section_end'         => array(
				'type' => 'sectionend',
				'id'   => 'checkout_section_end',
			),
			'pswc_order_email_title'          => array(
				'name' => esc_html__( 'Order Email Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_order_email_title',
			),
			'pswc_order_email_position'       => array(
				'name'    => esc_html__( 'Subtitle Position', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      						=> esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' 	=> esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  	=> esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Choose the position for the order email subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_order_email_position',
				'default' => 'disable'
			),
			'pswc_order_email_tag'            => array(
				'name'    => esc_html__( 'Subtitle Element Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_order_email_tag',
				'default' => 'strong'
			),
			'order_email_section_end'         => array(
				'type' => 'sectionend',
				'id'   => 'order_email_section_end',
			),
			'pswc_my_account_title'          => array(
				'name' => esc_html__( 'My Account Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_my_account_title',
			),
			'pswc_my_account_position'       => array(
				'name'    => esc_html__( 'Subtitle Position', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Select the subtitle position for the My Account page.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_my_account_position',
				'default' => 'disable'
			),
			'pswc_my_account_tag'            => array(
				'name'    => esc_html__( 'Subtitle Element Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_my_account_tag',
				'default' => 'strong'
			),
			'my_account_section_end'         => array(
				'type' => 'sectionend',
				'id'   => 'my_account_section_end',
			),
			'pswc_thank_you_title'          => array(
				'name' => esc_html__( 'Thank You Page Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'pswc_thank_you_title',
			),
			'pswc_thank_you_position'       => array(
				'name'    => esc_html__( 'Subtitle Position', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'      => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'before_title' => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'after_title'  => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Choose the subtitle position on the Thank You page.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_thank_you_position',
				'default' => 'disable'
			),
			'pswc_thank_you_tag'            => array(
				'name'    => esc_html__( 'Subtitle Element Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_thank_you_tag',
				'default' => 'strong'
			),
			'thank_you_section_end'         => array(
				'type' => 'sectionend',
				'id'   => 'thank_you_section_end',
			),
			'single_tag_start'             => array(
				'name' => esc_html__( 'Single Product Page Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'single_tag_start',
			),
			'pswc_single_position'         => array(
				'name'    => esc_html__( 'Subtitle Position', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable-0'                            		 => esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'woocommerce_single_product_summary-0' 		 => esc_html__( 'Before Title', 'product-subtitle-for-woocommerce' ),
					'woocommerce_single_product_summary-6' 		 => esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
					'woocommerce_before_add_to_cart_form-10' 	 => esc_html__( 'Before Add to Cart', 'product-subtitle-for-woocommerce' ),
					'woocommerce_before_add_to_cart_quantity-10' => esc_html__( 'After Price', 'product-subtitle-for-woocommerce' ),
					'woocommerce_single_variation-10'      		 => esc_html__( 'Before Price', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Select the subtitle position on the single product page.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_single_position',
				'default' => 'disable-0'
			),
			'pswc_single_tag'              => array(
				'name'    => esc_html__( 'Subtitle Element Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_single_tag',
				'default' => 'strong'
			),
			'single_tag_end'               => array(
				'type' => 'sectionend',
				'id'   => 'single_tag_end',
			),
			'shop_start'                   => array(
				'name' => esc_html__( 'Shop Page Subtitle Settings', 'product-subtitle-for-woocommerce' ),
				'type' => 'title',
				'desc' => '',
				'id'   => 'shop_start',
			),
			'pswc_shop_position'           => array(
				'name'    => esc_html__( 'Subtitle Position', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => array(
					'disable'                           		=> esc_html__( 'Disable', 'product-subtitle-for-woocommerce' ),
					'woocommerce_before_shop_loop_item' 		=> esc_html__( 'Before Featured Image', 'product-subtitle-for-woocommerce' ),
					'woocommerce_before_shop_loop_item_title' 	=> esc_html__( 'After Featured Image', 'product-subtitle-for-woocommerce' ),
					'woocommerce_shop_loop_item_title'  		=> esc_html__( 'After Title', 'product-subtitle-for-woocommerce' ),
					'woocommerce_after_shop_loop_item_title' 	=> esc_html__( 'Before Add to Cart', 'product-subtitle-for-woocommerce' ),
					'woocommerce_after_shop_loop_item'  		=> esc_html__( 'After Add to Cart', 'product-subtitle-for-woocommerce' ),
				),
				'desc'    => esc_html__( 'Select the subtitle position on the Shop page.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_shop_position',
				'default' => 'disable'
			),
			'pswc_shop_tag'                => array(
				'name'    => esc_html__( 'Subtitle Element Tag', 'product-subtitle-for-woocommerce' ),
				'type'    => 'select',
				'options' => $options,
				'desc'    => esc_html__( 'Select the HTML tag for the subtitle.', 'product-subtitle-for-woocommerce' ),
				'id'      => 'pswc_shop_tag',
				'default' => 'strong'
			),
			'shop_end'                     => array(
				'type' => 'sectionend',
				'id'   => 'shop_end',
			),
					);
		return apply_filters( 'pswc_setting_fields_args', $settings );
	}

	/**
	 * Subtitle field on edit product page
	 */
	public function subtitle_field() {
		global $post;

		if ( 'product' === $post->post_type ) :
			$pswc_subtitle = get_post_meta( $post->ID, 'pswc_subtitle', true ); ?>
			<div class="pswc-pub-section">
				<h2 for="pswc_subtitle_editor" style="padding: 10px 0;"><?php esc_html_e( 'Product Sub Title', 'product-subtitle-for-woocommerce' ); ?></h2>

				<?php
				if ( 'yes' === $this->pswc_enable_wysiwyg ) :
					$editor_id = 'pswc_subtitle_editor'; // Set the ID of the WYSIWYG editor.
					$settings  = array(
						'textarea_name' => 'pswc_subtitle', // Set the name of the textarea.
						'textarea_rows' => 5, // Set the number of rows for the textarea.
						'teeny'         => true, // Enable the "teeny" mode for the editor.
					);
					wp_editor( $pswc_subtitle, $editor_id, $settings ); // Display the WYSIWYG editor.
				else :
					?>
					<input type="text" id="pswc_subtitle" name="pswc_subtitle" value="<?php echo esc_attr( $pswc_subtitle ); ?>" style="width:100%" placeholder="Enter Your Product Subtitle" />
				<?php endif; ?>
			</div>
			<?php
		endif;
	}

	/**
	 * Save fields
	 */
	public function get_fields() {
		
		return apply_filters(
			'pswc_save_fields',
			array(
				'pswc_cart_position',
				'pswc_cart_tag',
				'pswc_minicart_position',
				'pswc_minicart_tag',
				'pswc_checkout_position',
				'pswc_checkout_tag',
				'pswc_single_position',
				'pswc_single_tag',
				'pswc_shop_position',
				'pswc_shop_tag',
				'pswc_order_email_position',
				'pswc_order_email_tag',
				'pswc_my_account_position',
				'pswc_my_account_tag',
				'pswc_thank_you_position',
				'pswc_thank_you_tag'
			)
		);
	}

	/**
	 * Save settings
	 */
	public function save_pswc_woocommerce_settings() {
		check_admin_referer( 'woocommerce-settings' );
		$settings_to_save = $this->get_fields();

		foreach ( $settings_to_save as $setting ) :
			$setting_field = isset( $_POST[ $setting ] ) ? sanitize_text_field( wp_unslash( $_POST[ $setting ] ) ) : '';
			if ( ! empty( $setting_field ) ) :
				update_option( $setting, $setting_field );
			endif;
		endforeach;

		$enable_subtitle = isset( $_POST['pswc_disable_subtitle'] ) ? ( sanitize_text_field( wp_unslash( $_POST['pswc_disable_subtitle'] ) ) ? 'yes' : 'no' ) : 'no';
		update_option( 'pswc_disable_subtitle', $enable_subtitle );

		$enable_subtitle_col = isset( $_POST['pswc_enable_subtitle_col'] ) ? ( sanitize_text_field( wp_unslash( $_POST['pswc_enable_subtitle_col'] ) ) ? 'yes' : 'no' ) : 'no';
		update_option( 'pswc_enable_subtitle_col', $enable_subtitle_col );

		$enable_subtitle_admin = isset( $_POST['pswc_enable_admin_order_view'] ) ? ( sanitize_text_field( wp_unslash( $_POST['pswc_enable_admin_order_view'] ) ) ? 'yes' : 'no' ) : 'no';
		update_option( 'pswc_enable_admin_order_view', $enable_subtitle_admin );

		$enable_wyswing = isset( $_POST['pswc_enable_wysiwyg'] ) ? ( sanitize_text_field( wp_unslash( $_POST['pswc_enable_wysiwyg'] ) ) ? 'yes' : 'no' ) : 'no';
		update_option( 'pswc_enable_wysiwyg', $enable_wyswing );
	}

	/**
	 * Save subtitle field value
	 *
	 * @param int $product_id Product ID.
	 */
	public function save_pswc_subtitle_field( $product_id ) {
		// Check the nonce.
		$security = isset( $_POST['woocommerce_meta_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ) : '';
		if ( empty( $security ) || ! wp_verify_nonce( $security, 'woocommerce_save_data' ) ) : // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			return;
		endif;

		if ( isset( $_POST['post_type'] ) && 'product' === $_POST['post_type'] ) :
			if ( isset( $_POST['pswc_subtitle'] ) ) :
				// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$pswc_subtitle = sanitize_text_field( wp_unslash( $_POST['pswc_subtitle'] ) );
				update_post_meta( $product_id, 'pswc_subtitle', $pswc_subtitle );
			endif;
		endif;
	}

	/**
	 * Plugin setting links
	 *
	 * @param int $links plugin options links.
	 */
	public function settings_link( $links ) {
		// Build and escape the URL.
		$url = esc_url(
			add_query_arg(
				array(
					'page' => 'wc-settings',
					'tab'  => 'pswc',
				),
				admin_url( 'admin.php' )
			)
		);
		// Create the link.
		$settings_link = sprintf( "<a href='%s'>%s</a>", $url, esc_html__( 'Settings', 'product-subtitle-for-woocommerce' ) );
		// Adds the link to the end of the array.
		array_unshift( $links, $settings_link );

		return $links;
	}
}

new PSWC_Setting_Tab(); // Instantiate the PSWC_Setting_Tab class.
