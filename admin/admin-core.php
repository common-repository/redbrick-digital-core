<?php

#error_reporting(E_ALL);
#ini_set('display_errors', 1 );

add_action( 'admin_menu', 'rbd_core_settings_menu_item' );
function rbd_core_settings_menu_item() {
	add_menu_page( 'RBD Core Settings', 'RBD Core', 'manage_options', __FILE__, 'rbd_core_settings_page', 'dashicons-star-filled' );

	// Register Settings Function on Init
	add_action( 'admin_init', 'rbd_core_settings_on_init' );
}

function rbd_core_settings_on_init() {
	$rbd_settings_group = 'rbd-core-settings-group';
	$rbd_settings_array = array(
		'rbd_core_hipaa_compliance',
		'rbd_core_disable_cutestrap',
		'rbd_core_review_engine_url',
		'rbd_core_review_engine_write_a_review_url',
		'wlio_api_url',
	);

	foreach( $rbd_settings_array as $setting ){
		register_setting( 'rbd-core-settings-group', $setting );
	}
}

function rbd_core_settings_page() {
	$rbd_settings_group = 'rbd-core-settings-group'; ?>
	<div class="wrap">
		<h1>RBD Core Settings</h1>
		<form method="post" action="options.php">
			<?php settings_fields( $rbd_settings_group ); ?>
			<?php do_settings_sections( $rbd_settings_group ); ?>
			<table class="form-table">
				<h3>General Options</h3>
				<p>This plugin is now <strong>deprecated</strong>. A new "WhirLocal.io" plugin will be available in the repository soon. In the meantime, please paste your WhirLocal.io API URL in the new field below:</p>
				<tr valign="top" style="background: #0095ee; padding: 0 10px; color: #fff; margin-bottom: 20px;">
					<th scope="row" style="padding-left: 20px; color: #fff;">WHIRLOCAL.IO API URL:</th>
					<td>
						<input class="regular-text" type="url" name="wlio_api_url" value="<?php echo esc_attr( get_option('wlio_api_url') ); ?>" />
						<p cass="description" style="color: #fff;">This URL should be the BASE api URL. e.g. "https://whirlocal.io/wp-json/wp/v2/company/123456"</p>
					</td>
				</tr>
				<tr valign="top" style="opacity: .6;">
					<th scope="row">Verifiable Review Engine URL:</th>
					<td>
						<?php
							if( update_option( 'rbd_core_review_engine_url_updated', get_option( 'rbd_core_review_engine_url' ) ) ){
								delete_transient( 'rbd_core_api_call' );
							}

							$verification_data	= RBD_Core::rbd_core_api_call();
							$verify				= $verification_data->data[0]->message;

							if( $verify == 'token:valid::accepted' ){ // All is good! Don't need to do anything else.
								update_option( 'RBD_CORE_VALID', true );
								//rbd_core_et_phone_home( true, site_url(), get_option( 'rbd_core_review_engine_url' ), get_option( 'admin_email' ) );
							} else { // Whoops, not a valid Review Engine.
								update_option( 'RBD_CORE_VALID', false );
								//rbd_core_et_phone_home( false, site_url(), get_option( 'rbd_core_review_engine_url' ), get_option( 'admin_email' ) );
							}
						?>
						<input class="regular-text" type="text" readonly name="rbd_core_review_engine_url" value="<?php echo esc_attr( get_option('rbd_core_review_engine_url') ); ?>" />
						<?php
							if( get_option( 'rbd_core_review_engine_url' ) != '' ){
								//if( rbd_core_verify() == false ){
								//	echo '<br /><br /><span style="background: #fcc; border: 1px solid #f36; padding: 3px 8px;">Invalid Review Engine URL. Please make sure you typed your URL properly, or check with your account representative.</span><br /><br />';
								//} else if( rbd_core_verify() == true ){
									echo '<span style="background: #cfc; border: 1px solid #0c3; padding: 3px 8px;">Valid Review Engine URL!</span><br />';
								//}
							}
						?>
						<p class="description">Put a Review Engine URL in here. This field verifies access to Review Engines. You can change the URL in Widgets and Shortcodes as needed. <br />If you don't have a review engine, please contact your account representative.</p>
					</td>
				</tr>
				<tr valign="top" style="opacity: .6;">
					<th scope="row">Enable HIPAA Compliance:</th>
					<td>
						<?php $hipaa = get_option('rbd_core_hipaa_compliance'); ?>
						<input type="checkbox" name="rbd_core_hipaa_compliance" <?php if( $hipaa == true ){ echo 'checked="checked"'; } ?> />
						<p class="description">This field forces Shortcodes and Widgets to comply with HIPAA's guidelines and helps prevent sharing sensitive information, including reviewer names and "Gravatars".</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
<?php } ?>
