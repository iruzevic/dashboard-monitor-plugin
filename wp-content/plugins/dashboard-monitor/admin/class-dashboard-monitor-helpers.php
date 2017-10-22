<?php
/**
 * Create Admin Setting page
 *
 * @package Dashboard_Monitor
 */

/**
 * Register Endpoints Class
 */
class Dashborad_Monitor_Helpers {

  private $plugin_name;
  private $version;
  /**
   * Constructor function
   */
  public function __construct($plugin_name, $version) {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * REST / API endpoint Get pareameter name.
   *
   * @return string
   */
  public function getEndpointKeyName() {
    return 'api_key';
  }

  /**
   * Database Field Name
   */
  public function getDbFieldName() {
    return 'dashboard_monitor_key';
  }

  /**
   * Update DB with provided value
   *
   * @param [any] $value Data provided to input to DB.
   */
  public function updateDbOption($value) {
    if( empty( $value ) ) {
      return false;
    }

    update_option( $this->getDbFieldName(), $value );
  }

  /**
   * Add initial empty state to DB
   */
  public function addDbOption() {
    add_option( $this->getDbFieldName(), '' );
  }

  /**
   * Append data to serialized array
   *
   * @param [any] $item Item to append.
   * @return void
   */
  public function addItemToSerializedArray($item) {

    $get_options_value = $this->getKeys();

    $array = unserialize($get_options_value);
    $array[] = $item;
    return serialize($array);
 }

  /**
   * Append data to serialized array
   *
   * @param [any] $item Item to append.
   * @return void
   */
  public function removeItemFromSerializedArray($id) {
    
    $get_options_value = $this->getKeys();
  
    $array = unserialize($get_options_value);

    foreach ( $array as $array_key => $array_value ) {
      $key = in_array( $id, $array_value, true);
      if( $key === true ) {
        unset( $array[$array_key] );
      }
    }

      return serialize($array);
    }

  /**
   * Get All Keys from DB
   *
   * @return void
   */
  public function getKeys() {
    $option = get_option( $this->getDbFieldName() );

    if( empty( $option ) ) {
      return false;
    }

    return $option;
  }

  /**
   * Return Unseralized Keys from DB
   *
   * @return array
   */
  public function getKeysUnserialized() {
    $keys = $this->getKeys();

    if( empty( $keys ) ) {
      return false;
    }

    return unserialize( $keys );
  }

  /**
   * Return only one Key By ID
   *
   * @param [init] $id Key ID
   * @return boolean
   */
  public function getKeyById( $id ) {
    
    if( empty( $id ) ) {
      return false;
    }

    $all_keys = $this->getKeysUnserialized();

    if( $all_keys === false ) {
      return false;
    }

    $keys = array_column($all_keys, 'key');

    if( empty( $keys ) ) {
      return false;
    }

    if( in_array( $id, $keys, true ) === false ) {
      return false;
    }

    return true;
  }

  /**
   * Validate provided key with the one in DB
   *
   * @return boolean
   */
  public function isValidAuth() {
    
    if ( ! isset( $_GET[ $this->getEndpointKeyName() ] ) ) {
      return false;
    }

    $provided_key = sanitize_key( $_GET[ $this->getEndpointKeyName() ] );

    $key = $this->getKeyById( $provided_key );

    if( $key === false ) {
      return false;
    }

    return true;
  }

  /**
   * Encode Message callback and set data.
   *
   * @param [string] $status Status of message.
   * @param [string] $msg Message content.
   * @param [array] $data Data callback.
   */
  public function setMsg($status, $msg, $data = false) {
    return wp_json_encode(
      array(
        'status' => esc_html__( $status, 'dashboard-monitor' ),
        'msg' => esc_html__( $msg, 'dashboard-monitor' ),
        'data' => $data
       )
    );
  }

}