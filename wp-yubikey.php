<?php
/**
 * Plugin Name: Yubikey Authentication for WordPress
 * Plugin URI: http://faish.al/
 * Description: Yubikey Multi-Factor Authentication with One-time Passwords for Wordpress.
 * Version: 0.96
 * Author: Faishal
 * Author URI: http://faish.al/
 * Compatibility : WordPress 4.4.2
 * Text Domain: wp-yubikey
 * Domain Path: /languages/
 * License: GPL2+
 **/

define( 'WP_YUBIKEY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_YUBIKEY_PLUGIN_INC_DIR', WP_YUBIKEY_PLUGIN_DIR . 'includes/' );
define( 'WP_YUBIKEY_PLUGIN_FILE', __FILE__ );

require_once( WP_YUBIKEY_PLUGIN_INC_DIR . 'api.php' );
require_once( WP_YUBIKEY_PLUGIN_INC_DIR . 'authentication.php' );

if ( is_admin() ) {
	require_once( WP_YUBIKEY_PLUGIN_INC_DIR . 'setting.php' );
	require_once( WP_YUBIKEY_PLUGIN_INC_DIR . 'profile.php' );
}

load_plugin_textdomain( 'wp-yubikey',false, WP_YUBIKEY_PLUGIN_DIR . 'languages' );
