<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Product_Subtitle' ) ) :

    /**
     * Main Product_Subtitle Class
     *
     * @class Product_Subtitle
     * @version 1.0.0
     */
    final class Product_Subtitle {

        /**
         * The single instance of the class.
         *
         * @var Product_Subtitle
         */
        protected static $instance = null;

        /**
         * Constructor for the class.
         */
        public function __construct() {
            $this->init_hooks();
            $this->includes();
        }

        
        /**
         * Initialize hooks and filters.
         */
        private function init_hooks() {
            // Load plugin textdomain
            add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
            add_action( 'elementor/widgets/register', [ $this, 'register_elementor_widget' ] );

            // Register plugin activation hook
            register_activation_hook( PSWC_FILE, array( 'PSWC_install', 'install' ) );

            // Hook to install the plugin after plugins are loaded
            add_action( 'plugins_loaded', array( $this, 'pswc_install' ), 11 );
            add_action( 'pswc_init', array( $this, 'includes' ), 11 );
        }

        /**
         * Function to display admin notice if WooCommerce is not active.
         */
        public function pswc_woocommerce_admin_notice() {
            ?>
            <div class="error">
                <p><?php esc_html_e( 'Product Subtitle for WooCommerce is enabled but not effective. It requires WooCommerce to work.', 'product-subtitle-for-woocommerce' ); ?></p>
            </div>
            <?php
        }

        /**
         * Function to initialize the plugin after WooCommerce is loaded.
         */
        public function pswc_install() {
            if ( ! function_exists( 'WC' )  ) : // Check if WooCommerce is active.
                add_action( 'admin_notices', array( $this, 'pswc_woocommerce_admin_notice' ) ); // Display admin notice if WooCommerce is not active.
            else :
                do_action( 'pswc_init' ); // Initialize the plugin.
            endif;
        }

        /**
         * Main Product_Subtitle Instance.
         *
         * Ensures only one instance of Product_Subtitle is loaded or can be loaded.
         *
         * @static
         * @return Product_Subtitle - Main instance.
         */
        public static function instance() {
            if ( is_null( self::$instance ) ) :
                self::$instance         = new self();

                /**
                 * Fire a custom action to allow dependencies
                 * after the successful plugin setup
                 */
                do_action( 'PSWC_plugin_loaded' );
            endif;
            return self::$instance;
        }

        /**
         * Load the plugin text domain for translation.
         */
        public static function load_textdomain() {
            load_plugin_textdomain( 'product-subtitle-for-woocommerce', false, PSWC_PATH . '/languages/' );
        }

        /**
         * Include required files.
         *
         * @access private
         */
        public function includes() {
            $pswc_disable_subtitle = get_option( 'pswc_disable_subtitle', 'no' );
            /**
             * Core
             */
            require_once PSWC_PATH . 'includes/class-pswc-install.php'; // include order class.
            require_once PSWC_PATH . 'includes/public/class-pswc-order-email.php'; // include order class.
            require_once PSWC_PATH . 'includes/plugins/class-pswc-guternburg-block.php';

            if( is_admin() ) :
                $this->includes_admin( $pswc_disable_subtitle );
            elseif(  'no' === $pswc_disable_subtitle ) :
                $this->includes_public();
            endif;
        }

        /**
         * Include Admin required files.
         *
         * @access private
         */
        public function includes_admin( $pswc_disable_subtitle ) {

            require_once PSWC_PATH . 'includes/admin/class-pswc-setting-tab.php'; // Include admin settings class.
            if ( 'no' === $pswc_disable_subtitle ) :
                require_once PSWC_PATH . 'includes/admin/class-pswc-admin.php'; // Include admin class if subtitles are enabled.
            endif;
        }

        /**
         * Include Public required files.
         *
         * @access private
         */
        public function includes_public(){
            require_once PSWC_PATH . 'includes/public/class-pswc-cart.php';         // Include cart class.
			require_once PSWC_PATH . 'includes/public/class-pswc-checkout.php';     // Include checkout class.
			require_once PSWC_PATH . 'includes/public/class-pswc-thank-you.php';    // Include thank you class.
			require_once PSWC_PATH . 'includes/public/class-pswc-my-account.php';   // Include my account class.
			require_once PSWC_PATH . 'includes/public/class-pswc-minicart.php';     // Include mini cart class.
			require_once PSWC_PATH . 'includes/public/class-pswc-shop.php';         // Include shop class.
			require_once PSWC_PATH . 'includes/public/class-pswc-single.php';       // Include single class.
        }


        public function register_elementor_widget( $widgets_manager ) {
            require_once PSWC_PATH . 'includes/plugins/class-pswc-elementor-widget.php';
            $widgets_manager->register( new PSWC_Product_Subtitle_Widget() );
        }

    }

endif;
