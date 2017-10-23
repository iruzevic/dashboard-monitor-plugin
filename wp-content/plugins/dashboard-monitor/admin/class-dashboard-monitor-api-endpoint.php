<?php
/**
 * Register Meta Endpoints for REST endpoint
 *
 * @package Dashboard_Monitor
 */

if ( ! function_exists( 'get_plugins' ) ) {
  require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

 /**
  * Register Endpoints Class
  */
class Dashborad_Monitor_Api_Endpoint {

  private $plugin_name;
  private $version;
  private $helpers;
  /**
   * Constructor function
   */
  public function __construct( $plugin_name, $version, $helpers ) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->helpers = $helpers;
  }

  /**
   * Register endpoint route
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
   */
  public function getCoreVersion() {

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
   * @return void
   */
  public function getTheme() {
    $themes_array = array();

    $get_updats_list = get_site_transient( 'update_themes' );

    foreach ( $get_updats_list->checked as $themes_key => $themes_value ) {

      $theme = wp_get_theme();

      $themes_array[ $themes_key ] = array(
        'name' => $theme->get( 'Name' ),
        'description' => $theme->get( 'Description' ),
        'version' => $themes_value,
        'update' => $themes_value
      );

      if( isset( $get_updats_list->response[ $themes_key ] ) ) {
        $themes_array[ $themes_key ][ 'update' ] = $get_updats_list->response[ $themes_key ][ 'new_version' ];
      }
    }

    return $themes_array;
  }

    /**
   * Get Full Array Of Plugins with Update Field
   *
   * @return void
   */
  public function getPlugins() {
    $plugins_array = array();

    $get_updats_list = get_site_transient( 'update_plugins' );
    $plugins_list = get_plugins();

    foreach ($plugins_list as $plugins_key => $plugins_value) {
      $plugins_array[ $plugins_key ] = array(
        'name' => $plugins_value['Name'],
        'description' => $plugins_value['Description'],
        'version' => $plugins_value['Version'],
        'update' => $plugins_value['Version']
      );

      if( isset( $get_updats_list->response[ $plugins_key ] ) ) {
        $plugins_array[ $plugins_key ][ 'update' ] = $get_updats_list->response[ $plugins_key ]->new_version;
      }
    }

    return $plugins_array;
  }

  /**
   * Display REST / API endpoint data
   *
   * @return json
   */
  public function endpoint_callback() {

    // Check if is valid key
    if ( $this->helpers->isValidAuth() === false ) {
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
    $callback['core'] = $this->getCoreVersion();
    $callback['themes'] = $this->getTheme();
    $callback['plugins'] = $this->getPlugins();

    return $callback;
  }
}
