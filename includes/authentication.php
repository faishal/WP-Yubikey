<?php


add_action( 'init', 'wp_yubikey_auth_init' );
/**
 *
 */
function wp_yubikey_auth_init() {
	$setting = wp_yubikey_get_setting();
	if ( intval( $setting['client_id'] ) && strlen( trim( $setting['client_secret'] ) ) ) {
		add_action( 'login_form', 'wp_yubikey_login_render_field' );
		add_filter( 'wp_authenticate_user', 'wp_yubikey_login_validate' );
		// User registration functions
		add_action( 'user_register', 'wp_yubikey_register_save' );
		add_action( 'register_form', 'wp_yubikey_register_render_field' );
	}

}

/**
 *
 */
function wp_yubikey_login_render_field() {
	?>
	<p>
		<label>
			<?php esc_html_e( 'Yubikey', 'yubikey' ); ?>
			<input type="text" name="wp_yubikey_otp" class="input" size="20"/>
			<span class="description">
					<?php esc_html_e( 'Insert your yubikey and press the button', 'wp-yubikey' ); ?></span>
		</label>
	</p>
<?php }

/**
 * @param $user
 *
 * @return bool
 */
function wp_yubikey_login_validate( $user ) {
	// Get user specific settings
	$yubikey_user_setting = wp_yubikey_get_user_setting( $user->ID );
	if ( false === $yubikey_user_setting ['enable'] ) {
		return $user;
	}
	if ( empty( $yubikey_user_setting ['keys'] ) ) {
		return $user;
	}

	$otp         = filter_input( INPUT_POST, 'wp_yubikey_otp', FILTER_SANITIZE_STRING );
	$keyid       = substr( $otp, 0, 12 );
	$matched_key = false;
	foreach ( $yubikey_user_setting['keys'] as $key ) {
		if ( substr( $key, 0, 12 ) === $keyid ) {
			$matched_key = true;
			break;
		}
	}

	if ( false === $matched_key ) {
		return false;
	}

	$is_verified = wp_yubikey_verify_otp( $otp );
	if ( $is_verified ) {
		return $user;
	}

	return false;

}

/**
 * @param $response
 * @param $yubico_api_client_secret
 *
 * @return bool
 */
function wp_yubikey_verify_response( $response, $yubico_api_client_secret ) {
	//required trim response to remove unwated space
	$lines = explode( "\n", trim( $response ) );

	//convert it to array
	foreach ( $lines as $line ) {
		$lineparts               = explode( '=', $line, 2 );
		$result[ $lineparts[0] ] = trim( $lineparts[1] );
	}
	// Sort array Alphabetically based on keys
	ksort( $result );
	// Grab the signature sent by server and remove it to build data string
	$signature = $result['h'];
	unset( $result['h'] );

	// Build new string to calculate hmac signature
	$hmac_response_string = '';
	$sep = '';
	foreach ( $result as $key => $value ) {
		$hmac_response_string .= $sep . $key . '=' . $value;
		$sep = '&';
	}

	$hmac = base64_encode( hash_hmac( 'sha1', $hmac_response_string, base64_decode( $yubico_api_client_secret ), true ) );

	return $hmac === $signature;
}

/**
 * @param $otp
 * @param bool $yubico_client_id
 * @param bool $yubico_client_secret
 *
 * @return bool
 */
function wp_yubikey_verify_otp( $otp, $yubico_client_id = false, $yubico_client_secret = false ) {
	if ( false === $yubico_client_id || false === $yubico_client_secret ) {
		$setting = wp_yubikey_get_setting();
		if ( false === $yubico_client_id ) {
			$yubico_client_id = $setting['client_id'];
		}
		if ( false === $yubico_client_secret ) {
			$yubico_client_secret = $setting['client_secret'];
		}
	}

	$url             = add_query_arg( array(
		'id'  => $yubico_client_id,
		'otp' => $otp,
	), 'http://api.yubico.com/wsapi/verify' );
	$response_object = wp_remote_post( $url );

	if ( is_wp_error( $response_object ) ) {
		$GLOBALS['wp_yubikey_error'] = $response_object->get_error_message();

		return false;
	}

	$response = wp_remote_retrieve_body( $response_object );
	if ( wp_yubikey_verify_response( $response, $yubico_client_secret ) ) {
		if ( ! preg_match( '/status=([a-zA-Z0-9_]+)/', $response, $result ) ) {
			return false;
		}
		if ( 'OK' === $result[1] ) {
			return true;
		}
	}

	return false;
}

/**
 * @param $user_id
 */
function wp_yubikey_register_save( $user_id ) {

	$otp = filter_input( INPUT_POST, 'wp_yubikey_otp', FILTER_SANITIZE_STRING );
	// Only add Yubikey ID to profile if key is valid
	if ( yubikey_verify_otp( $otp ) ) {
		update_user_meta( $user_id, '_yubikey', array(
			'enable' => true,
			'keys'   => array( substr( $otp, 0, 12 ) ),
		) );
	}
}


/**
 * Add One-time Password field to register form.
 */
function wp_yubikey_register_render_field() {
	?>
	<p>
		<label><?php esc_html_e( 'Yubikey(Optional)', 'yubikey' ); ?><br/>
			<input type="text" name="wp_yubikey_otp" size="20" tabindex="99"/></label>
		<span class="description">
					<?php esc_html_e( 'Just press the Yubikey button in this field, it will handle rest', 'wp-yubikey' ); ?></span>
	</p>
	<?php
}
