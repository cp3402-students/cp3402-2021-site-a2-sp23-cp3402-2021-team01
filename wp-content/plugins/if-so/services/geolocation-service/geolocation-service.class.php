<?php

namespace IfSo\Services\GeolocationService;

require_once(IFSO_PLUGIN_BASE_DIR . 'services/plugin-settings-service/plugin-settings-service.class.php');

use IfSo\Services\PluginSettingsService;

class GeolocationService {
	
	private static $instance;

    private static $bad_request = false;

    private $possible_notification_threshholds = [60,75,90,95,100];
    
	private function __construct() {
		$web_service_domain = 'http://www.if-so.com/api/';
		$this->web_service_url = $web_service_domain.IFSO_API_VERSION.'/geolocation-service/geolocation-api.php';
	}
	
	public static function get_instance() {
		if ( NULL == self::$instance )
		self::$instance = new GeolocationService();
		
		return self::$instance;
	}
	
	private function cache_geo_data($geoData) {
        if(PluginSettingsService\PluginSettingsService::get_instance()->disableSessions->get())
            return false;

        session_start();
		$encodedGeoData = json_encode($geoData, JSON_UNESCAPED_UNICODE);
		// setcookie('ifso_geo_data', $encodedGeoData, time() + (60 * 15), "/");
		$_SESSION['ifso_geo_data'] = $encodedGeoData;
		session_write_close();
	}
	
	private function get_cached_geo_data() {
		if ( isset($_SESSION['ifso_geo_data']) )
		return json_decode(stripslashes($_SESSION['ifso_geo_data']), true);
		
		return NULL;
	}
	
	private function get_geo_data($license, $user_ip, $action) {
		$url = $this->web_service_url . 
		"?license=" . $license . "&ip=" . $user_ip . "&action=" . $action;
		$response = wp_remote_get( $url ,array('timeout' => 10) );
		
		if( is_array($response) ) {
			return json_decode( $response['body'], true );
		} else {
			return json_encode(array('success' => false));
		}
	}
	
	private function send_session_to_localdb($license) {
		global $wpdb;
		    $daily_sessions_table_name = $wpdb->prefix . 'ifso_daily_sessions';
		    $local_user_table_name = $wpdb->prefix . 'ifso_local_user';
			$sql = "INSERT INTO {$daily_sessions_table_name} (sessions_date, num_of_sessions) VALUES (%s,%d) ON DUPLICATE KEY UPDATE sessions_date = sessions_date, num_of_sessions = num_of_sessions + 1";
			$sql = $wpdb->prepare($sql, date(get_option('date_format')) ,1);
			$wpdb->query($sql);

            $local_status= $wpdb->get_row("SELECT * FROM {$local_user_table_name}");
            if($local_status->user_bank < $local_status->user_sessions || $local_status->user_sessions%50!=0){//Only request geo status from the server every 50 sessions
                $user_bank_status = $local_status->user_bank;
                $user_sessions_status = $local_status->user_sessions+1;
                $used_geo_sessions = $local_status->used_geo_sessions;
                $used_pro_sessions = $local_status->used_pro_sessions;
                if($local_status->geo_bank - $used_geo_sessions>0) ++$used_geo_sessions;
                elseif($local_status->pro_bank - $used_pro_sessions>0) ++$used_pro_sessions;

                $wpdb->query("UPDATE {$local_user_table_name} SET 
                    user_sessions = '$user_sessions_status',
                    user_bank = '$user_bank_status',
                    used_pro_sessions = '$used_pro_sessions',
                    used_geo_sessions  = '$used_geo_sessions'
				");

            }
            else{
                $status = $this->get_status($license);
                $user_bank_status = $status["bank"];
                $user_sessions_status = $status["realizations"];
                $pro_key_renewal = (isset($status['pro_renewal_date'])) ?  date_format(new \DateTime($status['pro_renewal_date']),'Y-m-d') : NULL;
                $used_pro_sessions = (isset($status['pro_realizations'])) ? $status['pro_realizations']   : 0;
                $geo_key_renewal = ($status['has_plusgeo_key']) ? date_format(new \DateTime($status['plusgeo_renewal_date']),'Y-m-d') : NULL;
                $used_geo_sessions = (isset($status['geo_realizations'])) ? $status['geo_realizations']   : 0;
                $pro_bank = (isset($status['product_bank'])) ? $status['product_bank']   : 0;;
                $geo_bank = (isset($status['geo_bank'])) ? $status['geo_bank']   : 0;;

                $sql = "UPDATE {$local_user_table_name} SET 
                        user_sessions = '$user_sessions_status',
                        user_bank = '$user_bank_status',
                        pro_renewal_date = '$pro_key_renewal',
                        geo_renewal_date = '$geo_key_renewal',
                        used_pro_sessions = '$used_pro_sessions',
                        used_geo_sessions = '$used_geo_sessions',
                        pro_bank = '$pro_bank',
                        geo_bank = '$geo_bank'";
                $wpdb->query($sql);

                if($pro_key_renewal != $local_status->pro_renewal_date || $geo_key_renewal != $local_status->geo_renewal_date){//One of the licenses has been refreshed
                    if($pro_key_renewal != $local_status->pro_renewal_date)
                        $this->reset_email_triggers('pro');
                    elseif($geo_key_renewal != $local_status->geo_renewal_date)
                        $this->reset_email_triggers('geo');
                }
            }
	}

	private function get_localdb_notification_data() {
		global $wpdb;
        $local_user_table_name = $wpdb->prefix . 'ifso_local_user';
		$user_notification_data = $wpdb->get_results( "SELECT * FROM {$local_user_table_name}");
		foreach ($user_notification_data as $data) {
			if (count($user_notification_data) > 0) {
                $db_user_email = $data->user_email;
                $get_alert_values = $data->alert_values;
			}
			return $user_notification_data[0];
	}
}
	public function reset_email_triggers($type = false) {
        foreach($this->possible_notification_threshholds as $percentage){
            if($type===false) update_option($percentage,'');
            else update_option($percentage,str_replace(substr($type,0,1),'',get_option($percentage)));
        }
	}

	private function notifications_email(){
        $local_user_data = $this->get_localdb_notification_data();
        $notifications_arr = array_filter(explode(" ",$local_user_data->alert_values));
        $user_domain = get_option('home');
        $user_domain_name = parse_url(get_option('home'))['host'];

        $to = (isset($local_user_data->user_email) && !empty($local_user_data->user_email)) ? $local_user_data->user_email : get_option('admin_email');
        $headers = 'Content-Type: text/html; charset=ISO-8859-1';

        $geo_percent_used = ($local_user_data->geo_bank !== 0) ? $local_user_data->used_geo_sessions/($local_user_data->geo_bank-1) * 100 : 0;
        $pro_percent_used = ($local_user_data->pro_bank !== 0) ? $local_user_data->used_pro_sessions/($local_user_data->pro_bank-1) * 100 : 0;


        if (in_array('100', $notifications_arr) && ($geo_percent_used >= 100 && $this->can_send_notification('100','geo')) || ($pro_percent_used >= 100 && $this->can_send_notification('100','pro'))) {
            $type = ($geo_percent_used >= 100) ? 'geolocation' : 'pro';
            $letter = substr($type,0,1);
            $type = ($type=='pro' && $local_user_data->user_bank <= 250) ? 'free' : 'pro';  //Free licenses have 250 sessions or less. TODO: change to a better method for detecting a lack of license
            $subject = "If-So - $type license quota at 100%";
            $body = "Dear {$user_domain_name } Admin, <br /> <br />
            We would like to notify you that your {$type} license has reached 100% of its' monthly Geolocation sessions quota.<br /><br />
            A report showing your daily usage is available on your <a href='$user_domain/wp-admin/admin.php?page=wpcdd_admin_geo_license' target='_blank'>website's admin panel</a>. <br />
            You can upgrade the amount of Geolocation Sessions anytime.<a href='https://www.if-so.com/plans/geolocation-plans/' target='_blank'>Upgrade</a>. <br /><br />
            Please feel free to contact our team for any assistance.<br /><br />
            The If-So Team <br /><br />";

            wp_mail( $to, $subject, $body, $headers );
            $this->add_value_to_option('100',$letter);
            return;
        }

        foreach ($notifications_arr as $data) {
            $data = (int) $data;
            if(($geo_percent_used >= $data  && $this->can_send_notification($data,'geo')) || ($pro_percent_used >= $data  && $this->can_send_notification($data,'pro'))) {
                $type = ($geo_percent_used >= $data  && $this->can_send_notification($data,'geo')) ? 'geolocation' : 'pro';
                $letter = substr($type,0,1);
                $type = ($type=='pro' && $local_user_data->user_bank <= 250) ? 'free' : 'pro';  //Free licenses have 250 sessions or less. TODO: change to a better method for detecting a lack of license
                $subject = "If-So - $type license quota at $data%";
                $body = "Dear {$user_domain_name } Admin, <br /> <br />
				We would like to notify you that your {$type} license has reached $data% of its' monthly Geolocation sessions quota.<br /><br />
				A report showing your daily usage is available on your <a href='$user_domain/wp-admin/admin.php?page=wpcdd_admin_geo_license' target='_blank'>website's admin panel</a>. <br />
				You can upgrade the amount of Geolocation Sessions anytime. <a href='https://www.if-so.com/plans/geolocation-plans/' target='_blank'>Upgrade</a>. <br /><br />
				Please feel free to contact our team for any assistance.<br /><br />
				The If-So Team <br /><br />";


                $this->add_value_to_option($data,$letter);

                foreach ($notifications_arr as $vals) {
                    if ($data > $vals) {
                        $this->add_value_to_option($vals,$letter);
                    }
                }
                wp_mail( $to, $subject, $body, $headers );
                return;
            }
        }
    }


	public function get_location_by_ip($license, $user_ip) {
		// try get cached geo data
		$cachedGeoData = $this->get_cached_geo_data();
		
		if ($cachedGeoData !== NULL && isset($cachedGeoData['ipAddress']) && $cachedGeoData['ipAddress']== $user_ip) {      //Invalidate geo cache if user IP has changed
			return $cachedGeoData;
		}

		if(self::$bad_request){
            //The first api request during this pageload was bad - don't try to do any more until the next page load
            return;
        }

		
		$geoData = $this->get_geo_data($license, $user_ip, 'get_ip_info');
		
		// cache and send sessions to db if success
		if ( isset($geoData['success']) && $geoData['success'] === true ) {
			$this->cache_geo_data($geoData);
			$this->send_session_to_localdb($license); //Locally tracking geo sessions
			$this->notifications_email();//Check whether a quota notification needs to be sent and send one if yes

		}
		else{
            $this->cache_geo_data(array());
            self::$bad_request = true;
        }

		return $geoData;
		}

        public function get_user_ip() {
            $ip = null;
            if (!empty($_SERVER['HTTP_CF_CONNECTING_IP']))      //Cloudflare
                $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
            elseif (!empty($_SERVER['HTTP_CLIENT_IP']))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
                $ip = explode(',',$_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            else
                $ip = $_SERVER['REMOTE_ADDR'];

            if(!empty($ip)){
                $ip = explode(':',$ip)[0];
            }

            return $ip;
        }
		
		public function get_status($license) {
			$url = $this->web_service_url . "?action=get_status&license=".$license;
			$response = wp_remote_get($url, array('timeout' => 20) );
			
			if( is_array($response) ) {
				$data = json_decode( $response['body'], true );
				
				return $data;
			} else {
				return json_encode(array('success' => false));
			}
		}

        private function str_contains($haystack,$needle){//Move to a helper class?
            if(strpos($haystack,$needle)!==false){
                return true;
            }
            return false;
        }

        private function can_send_notification($option,$type){
            $dat = get_option($option);
            if($dat){
                $letter = substr($type,0,1);     //First letter.Used to indicate which mail has already been sent(in the option)
                if($this->str_contains($dat,$letter))
                    return false;
                else
                    return true;
            }
            return true;
        }

        private function add_value_to_option($option,$value){
            $v = '';
            if(get_option($option)) $v = get_option($option);
            update_option($option, $v.$value);

        }

	}