<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @since   1.0.0
 * @package dashboard_monitor
 */

namespace Dashboard_Monitor\Includes;

use Dashboard_Monitor\Admin as Admin;

/**
 * The main start class.
 *
 * This is used to define admin-specific hooks
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 */
class Main {

  /**
   * Loader variable for hooks
   *
   * @var Loader    $loader    Maintains and registers all hooks for the plugin.
   *
   * @since 1.0.0
   */
  protected $loader;

  /**
   * Global plugin name
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $plugin_name;

  /**
   * Global plugin version
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $plugin_version;

  /**
   * Global assets version
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $assets_version;

  /**
   * Initialize class
   * Load hooks and define some global variables.
   *
   * @since 1.0.0
   */
  public function __construct() {
    if ( defined( 'DM_PLUGIN_VERSION' ) ) {
      $this->plugin_version = DM_PLUGIN_VERSION;
    } else {
      $this->plugin_version = '1.0.0';
    }

    if ( defined( 'DM_PLUGIN_NAME' ) ) {
      $this->plugin_name = DM_PLUGIN_NAME;
    } else {
      $this->plugin_name = 'dashboard-monitor';
    }

    $this->load_dependencies();
    $this->define_admin_hooks();
  }

  /**
   * Load the required dependencies.
   *
   * Create an instance of the loader which will be used to register the hooks
   * with WordPress.
   *
   * @since 1.0.0
   */
  private function load_dependencies() {
    $this->loader = new Loader();
  }

  /**
   * Register all of the hooks related to the admin area functionality
   * of the plugin.
   *
   * @since 1.0.0
   */
  private function define_admin_hooks() {

    $admin         = new Admin\Admin( $this->get_plugin_info() );
    $api_endpoint  = new Admin\Api_Endpoint( $this->get_plugin_info() );
    $settings_page = new Admin\Settings_Page( $this->get_plugin_info() );

    $this->loader->add_action( 'rest_api_init', $api_endpoint, 'endpoint_init' );
    $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_styles' );
    $this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );

    $this->loader->add_action( 'admin_menu', $settings_page, 'register_settings_page' );
    $this->loader->add_action( 'admin_init', $settings_page, 'register_db_options_field' );

    $this->loader->add_action( 'wp_ajax_add_api_key_ajax', $settings_page, 'add_api_key_ajax' );
    $this->loader->add_action( 'wp_ajax_nopriv_add_api_key_ajax', $settings_page, 'add_api_key_ajax' );
    $this->loader->add_action( 'wp_ajax_remove_api_key_ajax', $settings_page, 'remove_api_key_ajax' );
    $this->loader->add_action( 'wp_ajax_nopriv_remove_api_key_ajax', $settings_page, 'remove_api_key_ajax' );
  }

  /**
   * Run the loader to execute all of the hooks with WordPress.
   *
   * @since 1.0.0
   */
  public function run() {
    $this->loader->run();
  }

  /**
   * The reference to the class that orchestrates the hooks.
   *
   * @return Loader Orchestrates the hooks.
   *
   * @since 1.0.0
   */
  public function get_loader() {
    return $this->loader;
  }

  /**
   * The name used to uniquely identify it within the context of
   * WordPress and to define internationalization functionality.
   *
   * @return string Plugin name.
   *
   * @since 1.0.0
   */
  public function get_plugin_name() {
    return $this->plugin_name;
  }

  /**
   * Retrieve the version number.
   *
   * @return string Plugin version number.
   *
   * @since 1.0.0
   */
  public function get_plugin_version() {
    return $this->plugin_version;
  }

  /**
   * Retrieve the plugin info array.
   *
   * @return array Plugin info array.
   *
   * @since 1.0.0
   */
  public function get_plugin_info() {
    return array(
        'plugin_name' => $this->plugin_name,
        'plugin_version' => $this->plugin_version,
    );
  }

}
