<?php
/**
 * Create Admin Setting page
 *
 * @package Dashboard_Monitor
 */

  /**
   * Register Endpoints Class
   */
class Dashborad_Monitor_Settings_Page {

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
   * Register Setting page to sidebar navigation
   *
   * @return void
   */
  public function registerSettingsPage() {
    add_submenu_page(
      'tools.php',
      esc_html__( 'Dashboard Monitor', 'dashboard-monitor' ),
      esc_html__( 'Dashboard Monitor', 'dashboard-monitor' ),
      'manage_options',
      'dashboard-monitor',
      array( $this, 'getSettingsPage' )
    );
  }



  /**
   * Populate page with HTML
   */
  public function getSettingsPage() {
    $apy_keys = $this->displayAllKeysName();
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings-page-display.php';
    unset( $apy_keys );
  }

  /**
   * Register database field
   */
  public function registerOptionsFieldDB() {
    register_setting( 'dashboard-monitor-settings', $this->helpers->getDbFieldName() );
  }

  /**
   * Return All inputs from DB without Keys
   *
   * @return array
   */
  public function displayAllKeysName() {
    $get_options_value = $this->helpers->getKeysUnserialized();

    if ( empty( $get_options_value ) ) {
      return false;
    }

    // Remove keys from array
    foreach ( $get_options_value as $key => $value ) {
      unset( $get_options_value[ $key ]['key'] );
    }

    return array_reverse( $get_options_value );
  }

  /**
   * Ajax Callback to Add Key to DB
   */
  public function add_api_key_ajax() {

    if ( ! isset( $_POST['syncNonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['syncNonce'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_die( $this->helpers->setMsg( 'error', 'Check your nonce!' ) );
    }

    if ( ! isset( $_POST['name'] ) || sanitize_key( empty( $_POST['name'] ) ) ) {
      wp_die( $this->helpers->setMsg( 'error', 'Name not provided!' ) );
    }

    if ( $this->helpers->getKeys() === false ) {
      $this->helpers->addDbOption();
    }

    $key = array(
      'id' => time() + uniqid(),
      'name' => $_POST['name'],
      'date' => date("Y-m-d H:i:s P"),
      'key' => $this->helpers->generateApiKey(),
    );

    $new_value = $this->helpers->addItemToSerializedArray( $key );

    $this->helpers->updateDbOption( $new_value );

    wp_die( $this->helpers->setMsg( 'success', 'Success in creating key!', $key ) );

  }

  /**
   * Ajax Callback to Remove Key to DB
   */
  public function remove_api_key_ajax() {

    if ( ! isset( $_POST['syncNonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['syncNonce'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_die( $this->helpers->setMsg( 'error', 'Check your nonce!' ) );
    }

    if ( ! isset( $_POST['key'] ) && ! wp_verify_nonce( sanitize_key( $_POST['key'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_die( $this->helpers->setMsg( 'error', 'Key ID not provided!' ) );
    }

    $keyId = $_POST['key'];

    $new_value = $this->helpers->removeItemFromSerializedArray( (int) $keyId );

    if ( $new_value === false ) {
      wp_die( $this->helpers->setMsg( 'error', 'Key not removed. ID not valid!' ) );
    }

    $this->helpers->updateDbOption( $new_value );

    wp_die( $this->helpers->setMsg( 'success', 'Success in removing key!', $key ) );
  }
}
