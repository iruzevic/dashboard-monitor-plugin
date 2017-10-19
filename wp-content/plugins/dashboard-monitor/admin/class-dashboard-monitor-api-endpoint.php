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
  /**
   * Constructor function
   */
  public function __construct() {
    add_action( 'rest_api_init', array( $this, 'endpoint_init' ) );
  }

  /**
   * Meta_Endpoints function
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
    $get_updats_list = get_site_transient("update_plugins");

    if( empty( $get_updats_list ) ) {
      return false;
    }

    foreach ($get_updats_list->response as $plugin_key => $plugin_value) {
      $plugins_to_update_array[$plugin_key] = $plugin_value->new_version;
    }

    return $plugins_to_update_array;
  }

  /**
   * Get Plugins Full Array
   *
   * @return void
   */
  public function getPluginsFullArray() {
    $plugins_array = array();

    $plugins = get_plugins();

    $plugins_to_update_array = $this->getPluginsToUpdateArray();

    foreach($plugins as $plugin_key => $plugin_value) {

      if( array_key_exists( $plugin_key, $plugins_to_update_array) ) {
        $plugin_value['Update'] = $plugins_to_update_array[ $plugin_key ];
      } else {
        $plugin_value['Update'] = false;
      }

      $plugins_array[] = $plugin_value;
    };

    return $plugins_array;
  }

  /**
   * Callback for endpoint
   *
   * @return void
   */
  public function endpoint_callback() {
    $callback = array();

    $callback['project_name'] = get_bloginfo( 'name' );
    $callback['project_description'] = get_bloginfo( 'description' );
    $callback['version'] = get_bloginfo( 'version' );
    $callback['plugins'] = $this->getPluginsFullArray();

    return $callback;
  }
}
