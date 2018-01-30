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
    return hash( 'sha256', bin2hex( openssl_random_pseudo_bytes( 16 ) . wp_salt( 'SECURE_AUTH_SALT' ) ) );
  }

  /**
   * REST / API endpoint Get pareameter name.
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_endpoint_key_name() {
    return 'api_key';
  }

  /**
   * Database Field Name
   *
   * @return string
   *
   * @since 1.0.0
   */
  public function get_db_field_name() {
    return 'dashboard_monitor_key';
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

    update_option( $this->get_db_field_name(), $value );
  }

  /**
   * Add initial empty state to DB
   *
   * @since 1.0.0
   */
  public function add_db_option() {
    add_option( $this->get_db_field_name(), '' );
  }

  /**
   * Append data to serialized array
   *
   * @param any $item Item to append.
   * @return array
   *
   * @since 1.0.0
   */
  public function add_item_to_serialized_array( $item = null ) {
    if ( ! $item ) {
      return false;
    }

    $get_options_value = $this->get_keys();

    $array = unserialize( $get_options_value );
    $array[] = $item;
    return serialize( $array );
  }

  /**
   * Remove data from serialized array
   *
   * @param int $id Arrey key to remove.
   * @return array
   *
   * @since 1.0.0
   */
  public function remove_item_from_serialized_array( $id = null ) {
    if ( ! $id ) {
      return false;
    }

    $get_options_value = $this->get_keys();

    $array = unserialize( $get_options_value );

    foreach ( $array as $array_key => $array_value ) {
      $key = in_array( $id, $array_value, true );
      if ( $key === true ) {
        unset( $array[ $array_key ] );
      }
    }

      return serialize( $array );
  }

  /**
   * Get All Keys from DB
   *
   * @return array
   *
   * @since 1.0.0
   */
  public function get_keys() {
    $option = get_option( $this->get_db_field_name() );

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
  public function get_keys_unserialized() {
    $keys = $this->get_keys();

    if ( empty( $keys ) ) {
      return false;
    }

    return unserialize( $keys );
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

    $all_keys = $this->get_keys_unserialized();

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

    if ( ! isset( $_GET[ $this->get_endpoint_key_name() ] ) ) {
      return false;
    }

    $provided_key = sanitize_key( $_GET[ $this->get_endpoint_key_name() ] );

    $key = $this->get_key_by_id( $provided_key );

    if ( $key === false ) {
      return false;
    }

    return true;
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
