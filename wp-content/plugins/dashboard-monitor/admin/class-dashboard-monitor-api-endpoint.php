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
  public function __construct($plugin_name, $version, $helpers) {
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
   * Get Plugins To Update List From DB
   */
  public function getPluginsToUpdateArray() {
    $plugins_to_update_array = array();
    $get_updats_list = get_site_transient( "update_plugins" );

    if( empty( $get_updats_list ) ) {
      return false;
    }

    foreach ($get_updats_list->response as $plugin_key => $plugin_value) {
      $plugins_to_update_array[$plugin_key] = $plugin_value->new_version;
    }

    return $plugins_to_update_array;
  }

  /**
   * Get Full Array Of Plugins with Update Field
   *
   * @return void
   */
  public function getPluginsFullArray() {
    $plugins_array = array();

    $plugins = get_plugins();

    $plugins_to_update_array = $this->getPluginsToUpdateArray();

    foreach($plugins as $plugin_key => $plugin_value) {

      $plugin_value['Update'] = $plugin_value['Version'];

      // If there is update
      if( array_key_exists( $plugin_key, $plugins_to_update_array) ) {
        $plugin_value['Update'] = $plugins_to_update_array[ $plugin_key ];
      }

      $plugins_array[] = $plugin_value;
    };

    return $plugins_array;
  }

  /**
   * Display REST / API endpoint data
   *
   * @return json
   */
  public function endpoint_callback() {
    
    // Check if is valid key
    if( $this->helpers->isValidAuth() === false ) {
      return array(
        'error' => esc_html__( 'Missing API Key', 'dashboard-monitor' )
      );
    }

    $callback = array();

    $callback['project_name'] = get_bloginfo( 'name' );
    $callback['project_description'] = get_bloginfo( 'description' );
    $callback['version'] = get_bloginfo( 'version' );
    $callback['plugins'] = $this->getPluginsFullArray();

    return $callback;
  }
}

$this->loader = new Dashboard_Monitor_Loader();