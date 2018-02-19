<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since   1.0.0
 * @package dashboard_monitor
 */

namespace Dashboard_Monitor;

use Dashboard_Monitor\Helpers as General_Helpers;

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
  exit;
}

$general_helper = new General_Helpers\General_Helper();
$general_helper->remove_db_option();
