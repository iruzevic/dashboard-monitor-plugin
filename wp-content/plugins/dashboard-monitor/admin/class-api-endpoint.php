<?php
/**
 * Register Meta Endpoints for REST endpoint
 *
 * @since   1.0.0
 * @package dashboard_monitor
 */

namespace Dashboard_Monitor\Admin;

use Dashboard_Monitor\Helpers as General_Helpers;

if ( ! function_exists( 'get_plugins' ) ) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

 /**
  * Class Api_Endpoint
  */
class Api_Endpoint {

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
   * General Helper class
   *
   * @var object General_Helper
   *
   * @since 1.0.0
   */
  public $general_helper;

  /**
   * Initialize class
   *
   * @param array $plugin_info Load global theme info.
   *
   * @since 1.0.0
   */
  public function __construct( $plugin_info = null ) {
    $this->plugin_name     = $plugin_info['plugin_name'];
    $this->plugin_version  = $plugin_info['plugin_version'];

    $this->general_helper = new General_Helpers\General_Helper();
  }


  /**
   * Register endpoint route
   *
   * @since 1.0.0
   */
  public function endpoint_init() {

    /**
     * /wp-json/wp/v2/dashboard-monitor
     */
    register_rest_route(
      'wp/v2', '/dashboard-monitor', array(
          'methods'  => 'GET',
          'callback' => [ $this, 'endpoint_callback' ],
      )
    );
  }

  /**
   * Return current and update version of WP core
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_core_version() {

    $update_array = get_site_transient( 'update_core' );

    if ( empty( $update_array ) ) {
      return false;
    }

    if ( ! isset( $update_array->updates ) ) {
      return false;
    }

    if ( ! array_key_exists( 0, $update_array->updates ) ) {
      return false;
    }

    if ( ! isset( $update_array->updates[0]->current ) ) {
      return false;
    }

    $update_array = $update_array->updates[0]->current;

    return array(
        'version' => get_bloginfo( 'version' ),
        'update' => $update_array,
    );
  }

   /**
    * Get Full Array Of Themes with Update Field
    *
    * @return array
    *
    * @since 1.0.0
    */
  public function get_theme() {
    $themes_array = array();

    $get_updats_list = get_site_transient( 'update_themes' );

    foreach ( $get_updats_list->checked as $themes_key => $themes_value ) {

      $theme = wp_get_theme();

      $themes_array[ $themes_key ] = array(
          'name' => $theme->get( 'Name' ),
          'description' => $theme->get( 'Description' ),
          'version' => $themes_value,
          'update' => $themes_value,
      );

      if ( isset( $get_updats_list->response[ $themes_key ] ) ) {
        $themes_array[ $themes_key ]['update'] = $get_updats_list->response[ $themes_key ]['new_version'];
      }
    }

    return $themes_array;
  }

    /**
     * Get Full Array Of Plugins with Update Field
     *
     * @return array
     *
     * @since 1.0.0
     */
  public function get_plugins() {
    $plugins_array = array();

    $get_updats_list = get_site_transient( 'update_plugins' );
    $plugins_list = get_plugins();

    foreach ( $plugins_list as $plugins_key => $plugins_value ) {
      $plugins_array[ $plugins_key ] = array(
          'name' => $plugins_value['Name'],
          'description' => $plugins_value['Description'],
          'version' => $plugins_value['Version'],
          'update' => $plugins_value['Version'],
      );

      if ( isset( $get_updats_list->response[ $plugins_key ] ) ) {
        $plugins_array[ $plugins_key ]['update'] = $get_updats_list->response[ $plugins_key ]->new_version;
      }
    }

    return $plugins_array;
  }

  /**
   * Display REST / API endpoint data
   *
   * @return json
   *
   * @since 1.0.0
   */
  public function endpoint_callback() {

    // Check if is valid key.
    if ( $this->general_helper->is_valid_auth() === false ) {
      return array(
          'error' => esc_html__( 'Missing API Key', 'dashboard-monitor' ),
      );
    }

    $callback = array();

    $callback['q'] = get_num_queries();
    $callback['project_name'] = get_bloginfo( 'name' );
    $callback['project_description'] = get_bloginfo( 'description' );
    $callback['url'] = get_bloginfo( 'url' );
    $callback['admin_email'] = get_bloginfo( 'admin_email' );
    $callback['core'] = $this->get_core_version();
    $callback['themes'] = $this->get_theme();
    $callback['plugins'] = $this->get_plugins();

    return $callback;
  }
}
