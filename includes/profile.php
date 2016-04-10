<?php
add_action( 'show_user_profile', 'wp_yubikey_profile_render' );
add_action( 'edit_user_profile', 'wp_yubikey_profile_render' );
add_action( 'personal_options_update', 'wp_yubikey_profile_save' );
add_action( 'edit_user_profile_update', 'wp_yubikey_profile_save' );


/**
 * Extend personal profile page with Yubikey settings.
 */
function wp_yubikey_profile_render( $user ) {
	$yubikey_user_setting = wp_yubikey_get_user_setting( $user->ID );
	$is_enable            = filter_var( $yubikey_user_setting['enable'], FILTER_VALIDATE_BOOLEAN );
	?>
	<h3> <?php esc_html_e( 'Yubikey', 'wp-yubikey' ); ?></h3>
	<table class="form-table">
		<tr>
			<th scope="row"><label
					for="wp_yubkey_enable"><?php esc_html_e( 'Enable Yubikey authentication', 'wp-yubikey' ); ?></label>
			</th>
			<td>
				<div><input name="wp_yubkey_enable" id="wp_yubkey_enable" value="true" type="checkbox"
						<?php checked( $is_enable ); ?> />
				</div>
			</td>
		</tr>
		<tr>
			<th><label for="wp_yubikey_keys"><?php esc_html_e( 'Keys', 'wp-yubikey' ); ?></label></th>

			<td>
				<?php
				if ( count( $yubikey_user_setting['keys'] ) > 0 ) {
					foreach ( $yubikey_user_setting['keys'] as $key ) {
						?> <input style='width:350px' name="wp_yubikey_keys[]" autocomplete="off"
						          value="<?php echo esc_attr( $key ); ?>"
						          type="text"/><?php
					}
				} else { ?>
					<input name="wp_yubikey_keys[]" value="" autocomplete="off" type="text"/>
					<?php
				}
				?>
				<p class="description">
					<?php esc_html_e( 'Just press the Yubikey button in this field, it will handle rest', 'wp-yubikey' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}


/**
 * Form handling of Yubikey options added to personal profile page (user editing own profile)
 */
function wp_yubikey_profile_save( $user_id ) {
	check_admin_referer( 'update-user_' . $user_id );

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$is_enable = filter_input( INPUT_POST, 'wp_yubkey_enable', FILTER_VALIDATE_BOOLEAN, array(
		'options' => array(
			'default' => false,
		),
	) );

	$post_keys = filter_input( INPUT_POST, 'wp_yubikey_keys', FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY );

	$keys = array();
	foreach ( $post_keys as $key ) {
		if ( empty( $key ) ) {
			continue;
		}
		if ( strlen( $key ) < 12 ) {
			continue;
		}
		$keys[] = substr( $key, 0, 12 );
	}
	update_user_meta( $user_id, '_yubikey', array(
		'enable' => $is_enable,
		'keys'   => $keys,
	) );
}

/**
 * @param $key
 *
 * @return bool
 */
function wp_yubikey_is_valid_key( $key ) {
	if ( empty( $key ) ) {
		return false;
	}
}
