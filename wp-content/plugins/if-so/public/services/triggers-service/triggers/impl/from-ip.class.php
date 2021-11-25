<?php
/**
 * 
 * @author Muli Cohen <mulikohn@gmail.com>
 * 
 * 
 */
namespace IfSo\PublicFace\Services\TriggersService\Triggers;
require_once( plugin_dir_path ( __DIR__ ) . 'trigger-base.class.php');
class UserIpAddress extends TriggerBase {
	public function __construct() {
		parent::__construct('UserIp');
	}
	
	public function handle($trigger_data) { 
		$rule = $trigger_data->get_rule();
		$content = $trigger_data->get_content();
		$ip_values = trim($rule['ip-values']);
		$ip_input =  trim($rule['ip-input']);
		switch ($ip_values) {
			case "contains":
				if($this->contains_or_not($ip_input, true, false))
					return $content;
				return false;
			case "not-contains":
				if($this->contains_or_not($ip_input, false, true))
					return $content;
				return false;
			case "is":
				if($this->user_ip($ip_input))
					return $content;
				return false;
			case "is-not":
				if($this->user_ip($ip_input))
					return false;
				return $content;
		}
	}
	private function user_ip($ip_input) {
		if($_SERVER['REMOTE_ADDR'] == $ip_input) 
			return true;	
		return false;
	}
	private function contains_or_not($arg, $f, $t) {
			if(strpos($_SERVER['REMOTE_ADDR'], $arg) !== false)
				return $f;
		return $t; 
	}
}