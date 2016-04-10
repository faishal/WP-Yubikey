<?php

add_action( 'admin_menu', 'wp_yubikey_add_admin_menu' );
add_action( 'admin_init', 'wp_yubikey_settings_init' );

/**
 *
 */
function wp_yubikey_add_admin_menu() {
	add_options_page( 'Yubikey', 'Yubikey', 'manage_options', 'wp-yubikey', 'wp_yubikey_setting_page' );
}

/**
 *
 */
function wp_yubikey_settings_init() {
	register_setting( 'wp_yubikey', 'wp_yubikey_settings' );
	add_settings_section(
		'wp_yubikey_setting_section',
		__( 'API Key', 'wp-yubikey' ),
		'wp_yubikey_settings_section_callback',
		'wp_yubikey'
	);
	add_settings_field(
		'wp_yubikey_text_field_0',
		__( 'Client ID', 'wp-yubikey' ),
		'wp_yubikey_setting_field_client_id',
		'wp_yubikey',
		'wp_yubikey_setting_section'
	);
	add_settings_field(
		'wp_yubikey_text_field_1',
		__( 'Client Secret', 'wp-yubikey' ),
		'wp_yubikey_setting_field_client_secret',
		'wp_yubikey',
		'wp_yubikey_setting_section'
	);
}

/**
 *
 */
function wp_yubikey_setting_field_client_id() {
	$options = get_option( 'wp_yubikey_settings' );
	?>
	<input type='text' name='wp_yubikey_settings[client_id]'
	       value='<?php echo esc_attr( $options['client_id'] ); ?>'>
	<?php
}

/**
 *
 */
function wp_yubikey_setting_field_client_secret() {
	$options = get_option( 'wp_yubikey_settings' );
	?>
	<input type='text' class='widefat' name='wp_yubikey_settings[client_secret]'
	       value='<?php echo esc_attr( $options['client_secret'] ); ?>'>
	<?php
}

/**
 *
 */
function wp_yubikey_settings_section_callback() {
	printf( esc_html__( 'Set yubico API key, Get api key for your Yubikey from %s', 'wp-yubikey' ), '<a href="https://upgrade.yubico.com/getapikey/">' . esc_html__( 'Yubico.com', 'wp-yubikey' ) . '</a>' );
}

/**
 *
 */
function wp_yubikey_setting_page() {
	?>
	<form action='options.php' method='post'>
		<h1><?php esc_html_e( 'Yubikey Setting', 'wp-yubikey' ); ?></h1>
		<?php
		settings_fields( 'wp_yubikey' );
		do_settings_sections( 'wp_yubikey' );
		submit_button();
		?>
	</form>
	<?php
}
