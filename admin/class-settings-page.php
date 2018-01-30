<?php
/**
 * Create Admin Setting page
 *
 * @since   1.0.0
 * @package dashboard_monitor
 */

namespace Dashboard_Monitor\Admin;

use Dashboard_Monitor\Helpers as General_Helpers;

/**
 * Class Settings_Page
 */
class Settings_Page {

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
   * Register Setting page to sidebar navigation
   *
   * @return void
   *
   * @since 1.0.0
   */
  public function register_settings_page() {
    add_submenu_page(
      'tools.php',
      esc_html__( 'Dashboard Monitor', 'dashboard-monitor' ),
      esc_html__( 'Dashboard Monitor', 'dashboard-monitor' ),
      'manage_options',
      'dashboard-monitor',
      array( $this, 'get_settings_page' )
    );
  }



  /**
   * Populate page with HTML
   *
   * @since 1.0.0
   */
  public function get_settings_page() {
    $apy_keys = $this->display_all_keys_name();
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/templates/settings-page-display.php';
    unset( $apy_keys );
  }

  /**
   * Register database field
   *
   * @since 1.0.0
   */
  public function register_options_field_db() {
    register_setting( 'dashboard-monitor-settings', $this->general_helper->get_db_field_name() );
  }

  /**
   * Return All inputs from DB without Keys
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function display_all_keys_name() {
    $get_options_value = $this->general_helper->get_keys_unserialized();

    if ( empty( $get_options_value ) ) {
      return false;
    }

    // Remove keys from array.
    foreach ( $get_options_value as $key => $value ) {
      unset( $get_options_value[ $key ]['key'] );
    }

    return array_reverse( $get_options_value );
  }

  /**
   * Ajax Callback to Add Key to DB
   *
   * @since 1.0.0
   */
  public function add_api_key_ajax() {

    if ( ! isset( $_POST['syncNonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['syncNonce'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_send_json( $this->general_helper->set_msg_array( esc_html( 'error' ), esc_html__( 'Check your nonce!' ) ) );
    }

    if ( ! isset( $_POST['name'] ) || sanitize_key( empty( $_POST['name'] ) ) ) {
      wp_send_json( $this->general_helper->set_msg_array( esc_html( 'error' ), esc_html__( 'Name not provided!' ) ) );
    }

    if ( $this->general_helper->get_keys() === false ) {
      $this->general_helper->add_db_option();
    }

    $key = array(
        'id' => time() + uniqid(),
        'name' => sanitize_key( $_POST['name'] ),
        'date' => date( 'Y-m-d H:i:s P' ),
        'key' => $this->general_helper->generate_api_key(),
    );

    $new_value = $this->general_helper->add_item_to_serialized_array( $key );

    $this->general_helper->update_db_option( $new_value );

    wp_send_json( $this->general_helper->set_msg_array( esc_html( 'success' ), esc_html__( 'Success in creating key!' ), $key ) );

  }

  /**
   * Ajax Callback to Remove Key to DB
   *
   * @since 1.0.0
   */
  public function remove_api_key_ajax() {

    if ( ! isset( $_POST['syncNonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['syncNonce'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_send_json( $this->general_helper->set_msg_array( esc_html__( 'error' ), esc_html__( 'Check your nonce!' ) ) );
    }

    if ( ! isset( $_POST['key'] ) && ! wp_verify_nonce( sanitize_key( $_POST['key'] ), 'inf_dashboard_monitor_nonce_action' ) ) {
      wp_send_json( $this->general_helper->set_msg_array( esc_html__( 'error' ), esc_html__( 'Key ID not provided!' ) ) );
    }

    $key_id = sanitize_key( $_POST['key'] );

    $new_value = $this->general_helper->remove_item_from_serialized_array( (int) $key_id );

    if ( $new_value === false ) {
      wp_send_json( $this->general_helper->set_msg_array( esc_html__( 'error' ), esc_html__( 'Key not removed. ID not valid!' ) ) );
    }

    $this->general_helper->update_db_option( $new_value );

    wp_send_json( $this->general_helper->set_msg_array( esc_html( 'success' ), esc_html__( 'Success in removing key!' ), $key ) );
  }
}
