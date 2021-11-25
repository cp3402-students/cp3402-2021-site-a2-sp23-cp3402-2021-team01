<?php
	if ( ! defined( 'ABSPATH' ) ) exit;

	require_once(IFSO_PLUGIN_BASE_DIR . 'services/geolocation-service/geolocation-service.class.php');

	use IfSo\Services\GeolocationService;

	$license = get_option( 'edd_ifso_geo_license_key' );
	$status  = get_option( 'edd_ifso_geo_license_status' );
	$expires = get_option( 'edd_ifso_geo_license_expires' );
	// $item_name = get_option( 'edd_ifso_license_item_name' );

	function is_license_valid($status) {
		return ( $status !== false && $status == 'valid' );
	}

	function is_plusgeo_license_exist($geoData) {
		return ( isset($geoData['has_plusgeo_key']) && $geoData['has_plusgeo_key'] == true );
	}

	function is_pro_license_exist($geoData) {
		return ( isset($geoData['has_pro_key']) && $geoData['has_pro_key'] == true );
	}

	function get_subscription($geoData) {
		$subscription = '';

		if ( is_pro_license_exist($geoData) )
			$subscription = "Pro";
		else
			$subscription = "Free";
		

		if ( is_plusgeo_license_exist($geoData) )
			$subscription .= " +Geolocation";
		

		return $subscription;
	}

	function is_geo_data_valid($geoData) {
		return ( isset($geoData['success']) && $geoData['success'] == true );
	}

	function get_queries_left($geoData) {	//This actually shows the USED queries(not the ones left)
		if ( is_geo_data_valid($geoData) ) {
			return intval($geoData['realizations']);
		}

		return 0;
	}

	function get_monthly_queries($geoData) {
		if ( is_geo_data_valid($geoData) ) {
			return $geoData['bank'];
		}

		return 0;
	}

	function get_key($geoData, $key) {
		if ( isset( $geoData[$key] ) )
			return $geoData[$key];
		else
			return false;
	}

	function get_date_i18n($date) {
		return date_i18n( 'F j, Y', strtotime( $date, current_time( 'timestamp' ) ) );
	}

	function get_pro_purchase_date($geoData) {
		return get_key($geoData, 'pro_purchase_date');
	}

	function get_pro_renewal_date($geoData) {
		return get_key($geoData, 'pro_renewal_date');
	}

	function get_plusgeo_purchase_date($geoData) {
		return get_key($geoData, 'plusgeo_purchase_date');
	}

	function get_plusgeo_renewal_date($geoData) {
		return get_key($geoData, 'plusgeo_renewal_date');
	}

	function get_pro_and_geo_realizations($geoData){
		$ret = [
			'pro' => 0,
			'geo' => 0,
		];

		if(isset($geoData['geo_realizations'])){
			$ret['geo'] = number_format(intval($geoData['geo_realizations']));
		}

		if(isset($geoData['pro_realizations'])){
			$ret['pro'] = number_format(intval($geoData['pro_realizations']));
		}

		return $ret;
	}

	// General
	// $ifso_version = IFSO_WP_VERSION;
	// $ifso_product = get_product_name($license, $status);

	// Geolocation
	$geoData = GeolocationService\GeolocationService::get_instance()->get_status($license);

	$geo_subscription = get_subscription($geoData, $license, $status);
	$geo_monthly_queries = number_format(get_monthly_queries($geoData));
	$geo_int_monthly_queries = get_monthly_queries($geoData);
	$geo_queries_left = number_format(get_queries_left($geoData));
	$geo_queries_left_send = get_queries_left($geoData);
	$geo_pro_purchase_date = get_pro_purchase_date($geoData);
	$geo_pro_renewal_date = get_pro_renewal_date($geoData);
	$separateRealizations = get_pro_and_geo_realizations($geoData);
	$pro_license_type = (isset($geoData['pro_license_type']) && !empty($geoData['pro_license_type'])) ? $geoData['pro_license_type'] : false;
	$geo_license_type = (isset($geoData['geo_license_type']) && !empty($geoData['geo_license_type'])) ? $geoData['geo_license_type'] : false;
	$pro_license_bank = (isset($geoData['product_bank']) && !empty($geoData['product_bank'])) ? number_format($geoData['product_bank']) : 0;
	$geo_license_bank = (isset($geoData['geo_bank']) && !empty($geoData['geo_bank'])) ? number_format($geoData['geo_bank']) : 0;

	global $wpdb;

	$sent_user_email = (isset($_POST["user-email-box"]) && !empty($_POST["user-email-box"]) &&  $_POST["user-email-box"] != get_option('admin_email')) ? $_POST["user-email-box"] : get_option('admin_email');
	//if(isset($_POST["alert-checkbox-value"]) && isset($_POST["alert-checkbox-value-2"])&& isset($_POST["alert-checkbox-value-3"]))$set_alert_values = $_POST["alert-checkbox-value"] . " " . $_POST["alert-checkbox-value-3"] . " " . $_POST["alert-checkbox-value-2"] . " " . $_POST["alert-checkbox-value-1"];
	$set_alert_values = (isset($_POST["alert-checkbox-value"]) ? $_POST["alert-checkbox-value"]  : '') . " " . (isset($_POST["alert-checkbox-value-3"]) ? $_POST["alert-checkbox-value-3"] : '') . " " . (isset($_POST["alert-checkbox-value-2"]) ? $_POST["alert-checkbox-value-2"] : '') . " " . (isset($_POST["alert-checkbox-value-1"]) ? $_POST["alert-checkbox-value-1"] : '');
	// $checkbox_value = isset($_POST['alert-checkbox-value']);

function get_notification_data() {
	global $wpdb, $sent_user_email;
    $table = $wpdb->prefix . 'ifso_local_user';
	$user_notification_data = $wpdb->get_results( "SELECT * FROM {$table}"); //add another on reload
	if (count($user_notification_data) > 0) {
	$notifications_data_arr["user_email"] = (isset($user_notification_data[0]->user_email) && !empty($user_notification_data[0]->user_email)) ? $user_notification_data[0]->user_email : $sent_user_email;
	$notifications_data_arr["alert_values"] = isset($user_notification_data[0]->alert_values) ? $user_notification_data[0]->alert_values : '';
	$notifications_data_arr["record_id"] = isset($user_notification_data[0]->id) ? $user_notification_data[0]->id : 0;
	return $notifications_data_arr;
	}
}	

	$data = get_notification_data();
	$form_alert_values = explode(" ",$data["alert_values"]);

	$table = $wpdb->prefix . 'ifso_local_user';
    $daily_sessions_table_name = $wpdb->prefix . 'ifso_daily_sessions';

	if(isset($_POST['update_notifications'])) {
	$wpdb->query("REPLACE INTO $table SET 
    id = '{$data['record_id']}',
    user_email = '$sent_user_email',
	user_sessions = '$geo_queries_left_send',
	user_bank = '$geo_int_monthly_queries',
	alert_values = '$set_alert_values'
	");
	$form_alert_values = explode(" ",get_notification_data()["alert_values"]);
}

	$selectedTab = 'license';
	$anotherSelectedTab = 
	$licenseTabHeaderExtraClasses = ( $selectedTab == 'license' ) ?
									'selected-tab' : '';
	$licenseTabExtraStyles = ( $selectedTab == 'license' ) ?
						  '' : 'display:none;';

	$infoTabHeaderExtraClasses = ( $selectedTab != 'license' ) ?
									'selected-tab' : '';
	$infoTabExtraStyles = ( $selectedTab != 'license' ) ?
						  '' : 'display:none;';

   $noLicenseMessageBox = '<div class="no_license_message">'. __("Enter a Geolocation License to gain extra sessions. ", 'if-so') . '<a href="https://www.if-so.com/plans/geolocation-plans/?ifso=geocredits" target="_blank">'. __("Click here to get a Geolocation license key", 'if-so') . '</a>.</div>';
?>
<html> 
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

<!-- TEMPORARY inner css! -->
<style>
#customers {
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    border-collapse: collapse;
    width: 100%;
    text-align: center;
    font-size: 14px;
  }
  
  #outer-div {
      width: 50%;
      /* box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); */
  }

  .outer-table {
    border: 1px solid #cccccc;
    border-bottom: 0;
}


  #upper-table {
	width: 100%;
    text-align: center;
    background-color: #e5e5e5;
    font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    font-size: 14px;
    color: white;
  }
  .outer-table th{
      width: 50%;
      color: #333;
	  padding: 10px 0;
  }

	.outer-table th:first-child{
		border-right: 1px dashed #ccc;
	}
  
  #customers {
      /* border-style: hidden; */
  
  }
  #customers td, #customers th {
    padding: 8px;
    width: 50%;
    border: 1px solid #ddd;
  
  }
	#customers p:nth-child(1){
		padding: 10px 0;
		color: #5787f9;
		font-size: 16px;
		font-weight:bold;
	}

	#customers p:nth-child(2){
		color: #a9a8a8;
		padding: 0 60px;
	}

  .inner-table {
      width:100%;
      /* overflow-y:scroll; */
  }
  #customers tr {
      background-color: white;
      }
  
  .percentage { 
      font-weight:700;
  }
	 </style>
</head>

<body>
<div class="wrap">
	<h2>
		<?php
		    _e('If-So Dynamic Content | Geolocation');
		?>
	</h2>
	<div class="ifso-license-wrapper">
			<ul class="ifso-license-tabs-header">
				<li class="ifso-tab <?php echo $licenseTabHeaderExtraClasses; ?>" 					data-tab="ifso-info-tab-geo">		<?php _e('Info', 'if-so');?>							</li>

				<li class="ifso-tab <?php echo $infoTabHeaderExtraClasses ?>"
					data-tab="license-tab-wrapper">
					 <?php _e('Usage History', 'if-so'); ?>
				</li>
				<li class="ifso-tab <?php echo $infoTabHeaderExtraClasses; ?>" 					data-tab="ifso-info-tab-wrapper">			 <?php _e('Notifications', 'if-so'); ?>
							</li>

			</ul>

			<div class="ifso-license-tabs-wrapper">

	<!-- Notifications tab contents: -->

				<div class="ifso-info-tab-wrapper"
					 style="<?php echo $infoTabExtraStyles; ?>">

					<div class="geolocation-info-wrapper">	

<form method="post" action="" class="license-form">
<?php settings_fields('edd_ifso_license'); ?>
<table class="form-table license-tbl">
	<tbody>
		<tr valign="top">
			<th class="licenseTable" scope="row"  valign="top">
				<?php _e('Send Email Alerts'); ?>
			</th>
			<td >
			<input type="checkbox" class="box" name="alert-checkbox-value" id="dimming-checkbox" value="100"  <?php echo in_array('100', $form_alert_values) ? 'checked' : '';?>>
			<span class="notification-line">  <i><?php _e('Check this box if you would like to be notified regarding your quota threshold', 'if-so'); ?></i> </span>
<!-- <i>    Check this box if you would like to be notified regarding your quota threshold</i> -->
			</td>
		</tr>
		<tr valign="top">
			<th class="licenseTable" scope="row" valign="top">
				<?php _e('Quota Threshold', 'if-so'); ?>
			</th>
			<td>
			<input type="checkbox" class="group1" id="1st-box" name="alert-checkbox-value-1" value="75" <?php echo in_array('75', $form_alert_values) ? 'checked' : '';?> <?php echo !in_array('100', $form_alert_values) ? 'disabled' : '';?> > <span class="notification-line">  <span class="percentage">75%</span> &nbsp 	   <i><?php _e('Receive an email alert when quota reaches 75%', 'if-so'); ?></i> </span><br><br>
			<input type="checkbox" class="group1" id="2nd-box" name="alert-checkbox-value-2" value="95" <?php echo in_array('95', $form_alert_values) ? 'checked' : '';?> <?php echo !in_array('100', $form_alert_values) ? 'disabled' : '';?>>  <span class="notification-line">  <span class="percentage">95%</span>&nbsp&nbsp  <i><?php _e('Receive an email alert when quota reaches 90%', 'if-so'); ?></i> </span><br><br>
			<input type="checkbox" class="group1" id="3rd-box" name="alert-checkbox-value-3" value="100" <?php echo in_array('100', $form_alert_values) ? 'checked' : '';?> <?php echo !in_array('100', $form_alert_values) ? 'disabled' : '';?>>  <span class="notification-line">  <span class="percentage">100%</span>&nbsp&nbsp  <i><?php _e('Receive an email alert when quota reaches 100%', 'if-so'); ?></i></span><br>
			</td>
		</tr>
		<?php if(in_array('100', $form_alert_values) != 1 ) 
					echo "<script>$('.notification-line').css('color', '#DCDCDC	') </script>";
				else
					echo "<script> $('.notification-line').css('color', 'black') </script>";
				?>

		<tr valign="top">
			<th class="licenseTable" scope="row" valign="top">
				<?php _e('Email'); ?>
			</th>
			<td>
			<input id name="user-email-box" type="email" class="emailBox" value = "<?php echo get_notification_data()['user_email'];?>" >
			</td>
		</tr>

		<tr valign="top">
			<th></th>
			<td style="padding-top:0;"><?php _e('Make sure emails don’t go to spam.','if-so') ?> <a id="ifso_send_test_email" href="#"><?php _e('Send a test email.','if-so') ?> </a></td>
		</tr>
	</tbody>
</table>
<br>
<input type="submit" class="button-primary" name="update_notifications" value=<?php _e('Save', 'if-so')?>>

<!-- License key expiratiaton date -->
<!-- <?php if ($status == 'valid' && $expires == 'lifetime') { ?>
<div class="license_expires_message">Your license is Lifetime.</span></div>				
<?php } else if ( $status == 'valid' && $expires !== false ) { ?>
<div class="license_expires_message">Your license key expires on <span class="expire_date"><?php echo date_i18n( 'F j, Y', strtotime( $expires, current_time( 'timestamp' ) ) ); ?>.</span></div>
<?php } ?> -->
</form>

<!-- <?php if ($status !== false && $status == 'valid' ): ?>

<div class="approved_license_message">
	<strong>Thank you for using If>So Dynamic Content!</strong> Please feel free to contact our team with any issues you may have.
</div>
<?php endif; ?> -->
				</div>
</div>

<!--end of Notifications div  -->


			
	<!-- Usage History tab contents: -->
				<div class="license-tab-wrapper" 
				style="<?php echo $infoTabExtraStyles; ?>">

				<h1></h1>
				<div id="outer-div">
				<div class="outer-table">
				<table id="upper-table">
				<tr>
 				 <th><?php _e('Date','if-so');?></th>
 				 <th><?php _e('Geolocation sessions','if-so');?></th>
				</tr>
				</table>
				</div>
				<div class="inner-table">
				<table id="customers">
					<?php
						$results = $wpdb->get_results( "SELECT * FROM {$daily_sessions_table_name} ORDER BY id DESC");
										echo "<tr>";
										foreach ($results as $res) {
											if (count($results) > 0) {
											echo "</tr>";
											echo "<td>". $res->sessions_date . "</td>";
											echo "<td>". $res->num_of_sessions . "</td>";
											}
										}
										if (count($results) < 1) {
											_e('<td><p>You haven`t used the geolocation condition</p><p>Usage statistics will be automatically recorded here as soon as a trigger with a geolocation condition is realized for the first time.</p></td></tr>','if-so');
										}
										?>
				</table>
			</div>
				</div>
				</div>
			 <!-- end of license-tab-wrapper -->

			<div class="ifso-info-tab-geo" style="<?php echo $licenseTabExtraStyles; ?>">
			<?php if (!is_license_valid( $status )): ?>


<?php endif; ?>

<form method="post" action="options.php" class="license-form">

<?php settings_fields('edd_ifso_license'); ?>
<?php
	$dummy_license = $license;
	$dummy_license = substr($dummy_license ,18);
	$dummy_license = "❊❊❊❊-❊❊❊❊-❊❊❊❊-❊❊❊".$dummy_license;


	_e('<p class="ifso-settings_paragraph">Geolocation is limited to 250 monthly <span title="A session is defined as beginning when a visitor first visits a page with a geolocation trigger and ends when a visitor closes the browser, the user ip is changed, or after 25 minutes of inactivity." class="tm-tip ifso_tooltip line-tooltip">sessions</span>  with the free version and 1,000 monthly sessions for the duration of one year with the pro version.  <a class="buy-more-credits-link" href=" https://www.if-so.com/plans/geolocation-plans/?utm_source=Plugin&utm_medium=message&utm_campaign=geolocation&utm_term=info-top&utm_content=a" target="_blank">Click here</a> for additional options if your website handles a larger amount.</p>','if-so');
	?>
<div><?php _e('Sessions used this month:', 'if-so'); ?> <span style="font-weight:bold;"><?php echo $geo_queries_left . '/' . number_format($geo_int_monthly_queries); ?></span> <a href="https://www.if-so.com/plans/geolocation-plans/?utm_source=Plugin&utm_medium=message&utm_campaign=geolocation&utm_term=info-tab&utm_content=a" target="_blank">Upgrade</a></div>
<?php if (is_pro_license_exist($geoData) && is_plusgeo_license_exist($geoData)): ?>
	<div>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Pro:', 'if-so'); ?> <span style="font-weight:bold;"><?php echo '(' . $separateRealizations['pro'] . '/' . $pro_license_bank . ')'; ?></span></div>
	<div>&nbsp;&nbsp;&nbsp;&nbsp;<?php _e('Geo:', 'if-so'); ?> <span style="font-weight:bold;"><?php echo '(' . $separateRealizations['geo'] . '/' . $geo_license_bank . ')'; ?></span></div>
<?php endif; ?>

<?php //submit_button(); ?>

				<!-- <?php if ($status !== false && $status == 'valid' ): ?>
<div class="approved_license_message">
	<strong>Thank you for using If>So Dynamic Content!</strong> Please feel free to contact our team with any issues you may have.
</div>
<?php endif; ?> -->
				<div class="geo-license-section">
					<h1 class="ifso-info-title" style=""><?php _e('Your subscription', 'if-so'); ?></h1>
					<div class="ifso-info-content" style="font-size: 13px; line-height: 1.7;max-width: 1020px;">

						<div class="ifso-info-content" >
							<span class="ifso-content-head"><?php _e('Subscription:', 'if-so'); ?></span>
							<span class="ifso-content-body"> <?php echo (($pro_license_type && $pro_license_type!='free' || $pro_license_type=='free' && !$geo_license_type ) ? ucwords($pro_license_type) . " ({$pro_license_bank} sessions)" : '') . (($geo_license_type && $pro_license_type && $pro_license_type!='free') ? ' + ' : '') . (($geo_license_type) ? ucwords($geo_license_type) . " ({$geo_license_bank} sessions)" : '');?> <a href="https://www.if-so.com/plans/geolocation-plans/?utm_source=Plugin&utm_medium=message&utm_campaign=geolocation&utm_term=info-tab&utm_content=a" target="_blank"><?php _e('Upgrade','if-so'); ?></a></span>
						</div>

						<div class="ifso-info-content">
							<span class="ifso-content-head"><?php _e('Remaining Queries this month:', 'if-so'); ?></span>
							<span class="ifso-content-body"><?php echo number_format($geo_int_monthly_queries - $geo_queries_left_send); ?>
</span>
						</div>
						<!-- <?php echo $geo_queries_left;?> -->

						<!-- if a product license exists-->
						<?php if ( is_pro_license_exist($geoData) ): ?>

						<!--	<div class="ifso-info-content">
								<span class="ifso-content-head">Product license purchase date:</span>
								<span class="ifso-content-body"><?php// echo get_date_i18n($geo_pro_purchase_date); ?>
</span>
							</div>-->

						<?php endif; ?>

						<!-- if a geo license exists-->
						<?php if ( is_plusgeo_license_exist($geoData) ): ?>

			<!--				<div class="ifso-info-content">
								<span class="ifso-content-head">Geolocation license purchase date:</span>
								<span class="ifso-content-body">
		<?php// echo get_date_i18n(get_plusgeo_purchase_date($geoData)); ?>
									<span>
							</div>-->

							<div class="ifso-info-content">
								<span class="ifso-content-head"><?php _e('Geolocation license renewal date:', 'if-so'); ?></span>
								<span class="ifso-content-body">
		<?php echo get_date_i18n(get_plusgeo_renewal_date($geoData)); ?>
									<span>
							</div>

						<!-- if aproduct license exists license -->
						<?php elseif (is_pro_license_exist($geoData)): ?>

							<div class="ifso-info-content">
								<span class="ifso-content-head"><?php _e('Pro license renewal date:', 'if-so'); ?></span>
								<span class="ifso-content-body"><?php echo get_date_i18n($geo_pro_renewal_date); ?>
</span>
							</div>

						<?php endif; ?>



<h1 style="margin-top:20px;"><?php _e('Geolocation License', 'if-so'); ?></h1>
<p><?php _e('Enter your geolocation license key to upgrade the monthly session limit.', 'if-so'); ?> <a href="https://www.if-so.com/plans/geolocation-plans?ifso=geocredits" target="_blank"><?php _e('Get a license.', 'if-so'); ?></a></p>
<table class="form-table license-tbl">
	<tbody>
		<tr valign="top">
			<th class="licenseTable" scope="row" valign="top">
				<?php _e('License Key'); ?>
			</th>
			<td>
				<input id="edd_ifso_geo_license_key" <?php echo ( is_license_valid( $status ) ) ? "readonly":""; ?>
				name="edd_ifso_geo_license_key" type="text" class="regular-text" placeholder=<?php echo ($license) ? $dummy_license : '&nbsp;';?>>
				<input type="hidden" name="for_geo_deactivation" value=<?php echo (strpos($license, 'PR') !== false || strpos($license, 'FE') !== false) ? "" : $license; ?>>

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
					<?php if(!$status || !$geo_license_type) : ?>
					<label class="description" for="edd_ifso_geo_license_key"><?php _e('Enter your license key', 'if-so'); ?></label>
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
					<input type="submit" class="button-secondary" name="edd_ifso_geo_license_deactivate" value="<?php _e('Deactivate License', 'if-so'); ?>"/>
				<?php } else {
					wp_nonce_field( 'edd_ifso_nonce', 'edd_ifso_nonce' ); ?>
					<input type="submit" class="button-secondary" name="edd_ifso_geo_license_activate" value="<?php _e('Activate License', 'if-so'); ?>"/>
				<?php } ?>
			</td>
		</tr>
	</tbody>
	</form>
</table>

<!-- License key expiratiaton date -->
<?php if ($status == 'valid' && $expires == 'lifetime') { ?>
	<div class="license_expires_message"><?php _e('Your Geolocation License is Activated.', 'if-so'); ?></span></div>
<?php } else if ( $status == 'valid' && $expires !== false ) { ?>
	<div class="license_expires_message"><?php _e('Your license key expires on ', 'if-so'); ?><span class="expire_date"><?php echo date_i18n( 'F j, Y', strtotime( $expires, current_time( 'timestamp' ) ) ); ?>.</span></div>
<?php } ?>


</div>
		</div> <!-- end of ifso-settings-tabs-wrapper -->
	</div>

</div>
</div>

</body>
</html> 
