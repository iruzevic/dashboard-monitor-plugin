<?php
/**
 * Provide a admin area view for the plugin
 *
 * @since 1.0.0
 * @package dashboard_monitor
 */

?>

<div class="dashboard-monitor-content wrap">
  <div class="dashboard-monitor-content__item">
    <h1 class="dashboard-monitor-content__heading"><?php esc_html_e( 'Dashboard Monitor', 'dashboard_monitor' ); ?></h1>
    <p class="dashboard-monitor-content__desc description">
      <?php esc_html_e( 'This plugin will provide you to access REST/API endpoint with the key created here. You can use this data to create notifications about the status of your WordPress project.', 'dashboard_monitor' ); ?>
    </p>
  </div>

  <hr/>

  <div class="dashboard-monitor-content__item">
    <h2 class="dashboard-monitor-content__heading">
      <?php esc_html_e( 'Create API Key', 'dashboard_monitor' ); ?>
    </h2>
    <p class="dashboard-monitor-content__desc description">
      <strong><?php esc_html_e( 'Created key will be showned only once. Be sure to copy it!', 'dashboard_monitor' ); ?></strong>
    </p>
    
    <div class="dashboard-monitor-form">
      <input type="text" name="inf_dashboard_monitor_name" class="js-dashboard-monitor-generate-key-name dashboard-monitor-form__input" />
      <button class="button button-primary button-large js-dashboard-monitor-generate-key dashboard-monitor-form__btn"><?php esc_html_e( 'Generate Key', 'dashboard_monitor' ); ?></button>
      <?php wp_nonce_field( 'inf_dashboard_monitor_nonce_action', 'inf_dashboard_monitor_nonce' ); ?>
    </div>
  </div>

  <hr/>

  <div class="dashboard-monitor-content__item">
    <h2 class="dashboard-monitor-content__heading">
      <?php esc_html_e( 'Keys List', 'dashboard_monitor' ); ?>
    </h2>
    <p class="dashboard-monitor-content__desc description">
      <?php esc_html_e( 'List of all yout API KEYs for REST API endpoint.', 'dashboard_monitor' ); ?>
    </p>
    <div class="dashboard-monitor-msg js-msg" data-status=""></div>

    <ul class="dashboard-monitor-list js-dashboard-monitor-list">
      <?php if ( ! empty( $apy_keys ) ) { ?>
        <?php foreach ( $apy_keys as $key ) { ?>
          <?php
            $name = $general_helper->get_array_value( 'name', $key );
            $date = $general_helper->get_array_value( 'date', $key );
            $id = $general_helper->get_array_value( 'id', $key );
          ?>
          <li class="dashboard-monitor-list__item">

            <?php if ( ! empty( $name ) ) { ?>
              <?php echo esc_html( $name ); ?>
            <?php } ?>

            <div class="dashboard-monitor-list__right">
              <?php if ( ! empty( $date ) ) { ?>
                <span class="dashboard-monitor-list__date"><?php echo esc_html( $date ); ?></span>
              <?php } ?>

              <?php if ( ! empty( $id ) ) { ?>
                <a href="#" class="dashboard-monitor-list__remove js-dashboard-monitor-remove-key" data-key-id="<?php echo esc_html( $id ); ?>">
                  <?php esc_html_e( 'Remove', 'dashboard-monitor' ); ?>
                </a>
              <?php } ?>
            </div>
          </li>
        <?php } ?>
      <?php } ?>
    </ul>
  </div>
</div>
