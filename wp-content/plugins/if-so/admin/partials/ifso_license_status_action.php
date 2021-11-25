<?php
function return_license_data($license, $item_id) {
	$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => $item_id , // the name of our product in EDD
			'url'        => home_url()
		);
		$response = wp_remote_post( EDD_IFSO_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.' );
			}
		} 
		else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
			$message = false;
			if ( false === $license_data->success ) {
				//die("Im dead");
				if ( $license_data->error == 'expired' ) {
						return $message = sprintf(
							__( 'Your license key expired on %s. ' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
				}
			}
		}
	}