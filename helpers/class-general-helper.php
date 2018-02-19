<?php
/**
 * The general helper specific functionality.
 *
 * @since   1.0.0
 * @package dashboard_monitor
 */

namespace Dashboard_Monitor\Helpers;

/**
 * Class General Helper
 */
class General_Helper {

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
   * REST / API endpoint Get pareameter name.
   *
   * @var string
   *
   * @since 1.0.0
   */
  protected $endpoint_key_name = 'api_key';

  /**
   * Database Field Name.
   *
   * @var string
   *
   * @since 1.0.0
   */
  public $db_options_name = 'dashboard_monitor_keys';

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
  }

  /**
   * Check if array has key and return its value if true.
   * Useful if you want to be sure that key exists and return empty if it doesn't.
   *
   * @param string $key   Array key to check.
   * @param array  $array Array in which the key should be checked.
   * @return string       Value of the key if it exists, empty string if not.
   *
   * @since 1.0.0
   */
  public function get_array_value( $key, $array ) {
    return ( gettype( $array ) === 'array' && array_key_exists( $key, $array ) ) ? $array[ $key ] : '';
  }

  /**
   * Return timestamp when file is changes.
   * This is used for cache busting assets.
   *
   * @param string $filename File name you want to get timestamp from.
   * @return init Timestamp.
   *
   * @since 1.0.0
   */
  public function get_assets_version( $filename = null ) {
    if ( ! $filename ) {
      return false;
    }

    $file_location = get_template_directory() . $filename;

    if ( ! file_exists( $file_location ) ) {
      return;
    }

    return filemtime( $file_location );
  }

  /**
   * Generate security key
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function generate_api_key() {
    return hash( 'sha256', openssl_random_pseudo_bytes( 16 ) . wp_salt( 'SECURE_AUTH_SALT' ) . time() );
  }

  /**
   * Update DB with provided value
   *
   * @param any $value Data provided to input to DB.
   *
   * @since 1.0.0
   */
  public function update_db_option( $value = null ) {
    if ( ! $value ) {
      return false;
    }

    update_option( $this->db_options_name, $value );
  }

  /**
   * Remove DB option.
   *
   * @since 1.0.0
   */
  public function remove_db_option() {
    delete_option( $this->db_options_name );
  }

  /**
   * Append data to keys list
   *
   * @param any $item Item to append.
   * @return array
   *
   * @since 1.0.0
   */
  public function set_key( $item = null ) {
    if ( ! $item ) {
      return false;
    }

    $array = array();
    $get_options_value = $this->get_keys();

    $array = json_decode( $get_options_value, true );
    $array[] = $item;
    return wp_json_encode( $array );
  }

  /**
   * Remove data from keys list
   *
   * @param int $id Arrey key to remove.
   * @return array
   *
   * @since 1.0.0
   */
  public function unset_key( $id = null ) {
    if ( ! $id ) {
      return false;
    }

    $get_keys = $this->get_keys_array();

    foreach ( $get_keys as $key => $value ) {
      if ( $value['id'] === $id ) {
        unset( $get_keys[ $key ] );
      }
    }

    return wp_json_encode( $get_keys );
  }

  /**
   * Get All Keys from DB
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_keys() {
    $option = get_option( $this->db_options_name );

    if ( empty( $option ) ) {
      return false;
    }

    return $option;
  }

  /**
   * Return Unseralized Keys from DB
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_keys_array() {
    $keys = $this->get_keys();

    if ( empty( $keys ) ) {
      return false;
    }

    return json_decode( $keys, true );
  }

  /**
   * Return only one Key By ID
   *
   * @param int $id Key ID.
   * @return boolean
   *
   * @since 1.0.0
   */
  public function get_key_by_id( $id = null ) {

    if ( ! $id ) {
      return false;
    }

    $all_keys = $this->get_keys_array();

    if ( $all_keys === false ) {
      return false;
    }

    $keys = array_column( $all_keys, 'key' );

    if ( empty( $keys ) ) {
      return false;
    }

    if ( in_array( $id, $keys, true ) === false ) {
      return false;
    }

    return true;
  }

  /**
   * Validate provided key with the one in DB
   *
   * @return boolean
   *
   * @since 1.0.0
   */
  public function is_valid_auth() {

    if ( ! isset( $_GET[ $this->endpoint_key_name ] ) ) {
      return false;
    }

    $provided_key = sanitize_key( wp_unslash( $_GET[ $this->endpoint_key_name ] ) );

    $key = $this->get_key_by_id( $provided_key );

    if ( $key === false ) {
      return false;
    }

    return true;
  }

  /**
   * Create unique ID for new key.
   *
   * @since 1.0.0
   */
  public function set_key_unique_id() {
    return (int) ( time() + random_int( 0, 100000 ) );
  }

  /**
   * Encode Message callback and set data.
   *
   * @param [string] $status Status of message.
   * @param [string] $msg Message content.
   * @param [array]  $data Data callback.
   *
   * @since 1.0.0
   */
  public function set_msg_array( $status = null, $msg = null, $data = null ) {
    return array(
        'status' => $status,
        'msg'    => $msg,
        'data'   => $data,
    );
  }

}
