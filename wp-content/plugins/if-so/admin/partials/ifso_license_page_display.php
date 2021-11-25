<?php
	if ( ! defined( 'ABSPATH' ) ) exit;

	$license = get_option( 'edd_ifso_license_key' );
	$status  = get_option( 'edd_ifso_license_status' );
	$expires = get_option( 'edd_ifso_license_expires' );
	$item_id = get_option( 'edd_ifso_license_item_id' );

	function is_license_valid($status) {
		return ( $status !== false && $status == 'valid' );
	}

?>
<div class="wrap">

	<h2>
	
		<?php 
			_e('License','if-so');
		?>
		
	</h2>

	<div class="ifso-license-wrapper">
			<div class="ifso-license-tabs-wrapper">
				<div class="license-tab-wrapper">

				<?php if (!is_license_valid( $status )): ?>

					<?php echo '<div id="nolicense_message_target"></div>'; ?>

				<?php endif; ?>

				<form method="post" action="options.php" class="license-form">

					<?php settings_fields('edd_ifso_license'); ?>

			<?php
			$dummy_license = $license;
			$dummy_license = substr($dummy_license ,18);
			$dummy_license = "❊❊❊❊-❊❊❊❊-❊❊❊❊-❊❊❊".$dummy_license;
			?>
					<table class="form-table license-tbl">
						<tbody>
							<tr valign="top">
								<th class="licenseTable" scope="row" valign="top">
									<?php _e('License Key','if-so'); ?>
								</th>
								<td>
									<input id="edd_ifso_license_key" <?php echo ( is_license_valid( $status ) ) ? "readonly":""; ?> 
									name="edd_ifso_license_key" type="text" class="regular-text" placeholder=<?php echo ($license) ? $dummy_license : '&nbsp;';?>
									value=<?php echo ($license) ? $dummy_license:"";?>>

									<?php
										
										if ( $this->edd_ifso_is_in_activations_process() ) {
											// in activations process
											
											$error_message = $this->edd_ifso_get_error_message();

											if ( $error_message ) {
												?>

												<span class="description license-error-message">
													<?php echo $error_message; ?>
												</span>

												<?php
											}

										} else {
									?>

										<?php if(!$status) : ?>
												<label class="description" for="edd_ifso_license_key"><?php _e('Enter your license key','if-so'); ?></label>
										<?php else: ?>
											<label class="description active"><?php _e('Active', 'if-so'); ?></label>
										<?php endif;?>



									<?php
										}
									?>

								</td>
							</tr>
							<tr valign="top">
								<th class="licenseTable" scope="row" valign="top">
									<!--<?php _e('Activate License'); ?>-->
								</th>
								<td>
									<?php if( $status !== false && $status == 'valid' ) { ?>
										<?php wp_nonce_field( 'edd_ifso_nonce', 'edd_ifso_nonce' ); ?>
										<input type="submit" class="button-secondary" name="edd_ifso_license_deactivate" value="<?php _e('Deactivate License','if-so'); ?>"/>
									<?php } else {
										wp_nonce_field( 'edd_ifso_nonce', 'edd_ifso_nonce' ); ?>
										<input type="submit" class="button-secondary" name="edd_ifso_license_activate" value="<?php _e('Activate License','if-so'); ?>"/>
									<?php } ?>
								</td>
							</tr>
						</tbody>
					</table>
					<?php $lifetime_license_message = __('Your license is Lifetime.','if-so');
						  $expires_license_message = __('Your license key expires on','if-so');
					?>

					<!-- License key expiratiaton date -->
					<?php if ($status == 'valid' && $expires == 'lifetime') { ?>
					<div class="license_expires_message"></span><?php echo $lifetime_license_message;?></div>
					<?php } else if ( $status == 'valid' && $expires !== false ) { ?>
					<div class="license_expires_message"><?php echo $expires_license_message;?> <span class="expire_date"><?php echo date_i18n( 'F j, Y', strtotime( $expires, current_time( 'timestamp' ) ) ); ?>.</span></div>
					<?php } ?>

					<?php //submit_button(); ?>

				</form>

				<?php if ($status !== false && $status == 'valid' ): ?>
					<div class="approved_license_message">
						<?php _e('<strong>Thank you for using If-So Dynamic Content!</strong> Please feel free to contact our team with any questions or concerns you may have.','if-so') ?>
					</div>
				<?php endif; ?>

            <a class="" style="margin-top:10px;float: right;" title="Click here if you are having trouble deactivating your license or if you want to purge the license data from the system completely." href="<?php echo $_SERVER['REQUEST_URI']; ?>&edd_ifso_license_clear=true"><?php _e('Clear License','if-so'); ?></a>
			</div> <!-- end of license-tab-wrapper -->
		</div> <!-- end of ifso-settings-tabs-wrapper -->

	</div>

</div>


<script>
	document.querySelector('.license-form').addEventListener('submit',function(e){
		var dael = document.createElement('input');
		dael.setAttribute('name','for_deactivation');
		dael.setAttribute('type','hidden');
		dael.setAttribute('value',"<?php echo (strpos($license, 'GE') === 0) ? '' : $license; ?>");
		e.target.append(dael);
	});
</script>