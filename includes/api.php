<?php
/**
 * @param $user_id
 *
 * @return array
 */
function wp_yubikey_get_user_setting( $user_id ) {
	$default              = array(
		'enable' => false,
		'keys'   => array(),
	);
	$yubikey_user_setting = get_user_meta( $user_id, '_yubikey', true );
	if ( is_array( $yubikey_user_setting ) ) {
		return wp_parse_args( $yubikey_user_setting, $default );
	}

	return $default;
}

/**
 * @return array
 */
function wp_yubikey_get_setting() {
	$default = array(
		'client_id'     => '',
		'client_secret' => '',
	);

	return get_option( 'wp_yubikey_settings', $default );
}
