<?php

/**
 * Installation related functions and actions.
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'PSWC_install' ) ) :

    /**
     * PSWC_install Class
     *
     * Handles installation processes like creating database tables,
     * setting up roles, and creating necessary pages on plugin activation.
     */
    class PSWC_install {

        /**
         * Hook into WordPress actions and filters.
         */
        public static function init() {
            add_filter( 'plugin_action_links_' . PSWC_FILE, array( __CLASS__, 'plugin_action_links' ) );
        }

        /**
         * Install the WPBookit plugin.
         *
         * Creates tables, roles, and necessary pages on plugin activation.
         */
        public static function install() {
            if ( ! is_blog_installed() ) :
                return;
            endif;
            
        }

        /**
         * Add plugin action links.
         *
         * @param array $links Array of action links.
         * @return array Modified array of action links.
         */
        public static function plugin_action_links( $links ) {
            $action_links = array(
                'settings' => sprintf(
                    '<a href="%s" aria-label="%s">%s</a>',
                    admin_url( 'admin.php?page=wc-settings&tab=pswc&section=pswc-general' ),
                    esc_attr__( 'Product Subtitle Settings', 'wpbookit-pro' ),
                    esc_html__( 'Settings', 'wpbookit-pro' )
                ),
            );
            return array_merge( $action_links, $links );
        }

    }

    // Initialize the installation process
    PSWC_install::init();

endif;
