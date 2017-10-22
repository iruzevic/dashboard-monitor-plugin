<?php
/**
 * Provide a admin area view for the plugin
 *
 * @package    Dashboard_Monitor
 */

?>

<div class="wrap">
  <h1 class="wp-heading-inline"><?php esc_html_e( 'Dashboard Monitor', 'dashboard_monitor' ); ?></h1>
  <p class="description"><?php esc_html_e( 'Import API KEY to enable REST API endpoint.', 'dashboard_monitor' ); ?></p>
  <p class="description">
    <strong><?php esc_html_e( 'Created key will be showned only once. Be sure to copy it!', 'dashboard_monitor' ); ?></strong>
  </p>
  
  <input type="text" name="inf_dashboard_monitor_name" class="js-dashboard-monitor-generate-key-name" />
  <?php wp_nonce_field( 'inf_dashboard_monitor_nonce_action', 'inf_dashboard_monitor_nonce' ); ?>
  <button class="button button-primary button-large js-dashboard-monitor-generate-key"><?php esc_html_e( 'Generate Key', 'dashboard_monitor' ); ?></button>
  
  <hr/>

  <p class="description">
    <?php esc_html_e( 'List of all yout API KEYs for REST API endpoint.', 'dashboard_monitor' ); ?>
  </p>
  <div class="js-msg"></div>

  <ul class="dashboard-monitor-list js-dashboard-monitor-list">
    <?php if( ! empty( $apyKeys ) ) { ?>
      <?php foreach( $apyKeys as $key ) { ?>
        <li class="dashboard-monitor-list__item">
          <?php echo esc_html( $key['name'] ); ?> : <?php echo esc_html( $key['id'] ); ?>
          <a href="#" class="dashboard-monitor-list__remove js-dashboard-monitor-remove-key" data-key-id="<?php echo esc_html( $key['id'] ); ?>">
            <?php esc_html_e( 'Remove', 'dashboard-monitor' ); ?>
          </a>
        </li>
      <?php } ?>
    <?php } ?>
  </ul>

</div>
