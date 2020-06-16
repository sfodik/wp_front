<?php
/*
Plugin Name: Coding Ninjas Tasks Child
Description: Plugin for extends parent functionality.
Author: CodingNinjas inc.
Author URI: http://codingninjas.co/
Plugin URI: http://codingninjas.co/
Version: 1.0
Text Domain: cn
*/


use codingninjaschild\AppChild;

/**
 *  Check active Coding Ninjas Tasks plugin before activate child plugin
 */
if ( ! in_array( 'coding-ninjas-tasks/coding-ninjas.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'admin_init', 'child_plugin_off' );
	add_action( 'admin_notices', 'child_plugin_notice' );

	function child_plugin_off() {
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}

	function child_plugin_notice() {
		echo '<div class="updated"><p><strong>' . __( 'Coding Ninjas Tasks Child ', 'cn' ) . '</strong>' . __( 'has not been activated. Please activate the Coding Ninjas Tasks plugin before activating this plugin', 'cn' ) . '</p></div>';

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}

} else {
	require_once "app/AppChild.php";
	AppChild::run( __FILE__ );
}