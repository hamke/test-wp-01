<?php
/**
 * Plugin Name: Kandan Custom Global Variables
 * Plugin URI: https://www.wp-talk.com/
 * Description: Easily create custom variables that can be accessed globally in Wordpress and PHP. Retrieval of information is extremely fast, with no database calls.
 * Version: 0.0.1
 * Author: WP-Talk
 * Author URI: https://www.wp-talk.com/
 */

class Kandan_Custom_Global_Variables {

  private $file_path = '';

  // Constructor
  function __construct() {

      $this->file_path = WP_CONTENT_DIR . '/custom-global-variables/' . md5( AUTH_KEY ) . '.json';

      /* Retrieve locally the current definitions. */

      if ( file_exists( $this->file_path ) ) {

          $vars = file_get_contents( $this->file_path );

          if ( ! empty( $vars ) ) {

              $GLOBALS['kcgv'] = json_decode( $vars, true );
          }
          else {

              $GLOBALS['kcgv'] = array();
          }
      }
      // Create the directory and file when it doesn't exist.
      else {

          if ( wp_mkdir_p( WP_CONTENT_DIR . '/custom-global-variables' ) ) {

              file_put_contents( $this->file_path, '' );
          }

          $GLOBALS['kcgv'] = array();
      }

      // Setup the shortcode.
      add_shortcode( 'kcgv', array( &$this, 'shortcode' ) );

      // Add settings page
      add_action( 'admin_menu', array( &$this, 'add_settings_page' ) );

      // Add settings link
      add_filter( 'plugin_action_links_kandan-custom-global-variables/kandan-custom-global-variables.php', array( &$this, 'add_settings_link' ) );

    } // function __construct()

    //////////////////////////////////////////////////////////////////////////

    // Shortcode for displaying values
    function shortcode( $params ) {

      if ( ! empty( $GLOBALS['kcgv'][ $params[0] ] ) ) {

        return $GLOBALS['kcgv'][ $params[0] ];
      }

      return false;
    }

    //////////////////////////////////////////////////////////////////////////

    /**
     * Add Settings Page
     */
    function add_settings_page() {
      add_submenu_page(
        null,
        'Kandan Custom Global Variables',
        'Kandan Custom Global Variables',
        'manage_options',
        'kandan-custom-global-variables',
        array( &$this, 'settings_page' )
      );
    }

    /**
     * Settings page
     */
    function settings_page() {
      include( 'admin/settings-page.php' );
    }

    /**
     * Add Settings Link
     */
    function add_settings_link( $links ) {
    	$url = admin_url( '?page=kandan-custom-global-variables' );
    	$settings_link = "<a href='$url'>" . __( 'Settings Page' ) . '</a>';
    	array_push( $links, $settings_link );
    	return $links;
    }

}

$kandan_custom_global_variables = new Kandan_Custom_Global_Variables;
