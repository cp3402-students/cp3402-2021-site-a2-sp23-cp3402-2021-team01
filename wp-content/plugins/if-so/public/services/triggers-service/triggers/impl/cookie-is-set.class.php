<?php
/**
 * 
 * @author Muli Cohen <mulikohn@gmail.com>
 * @contributor Nick Martianov
 *
 */

namespace IfSo\PublicFace\Services\TriggersService\Triggers;

require_once( plugin_dir_path ( __DIR__ ) . 'trigger-base.class.php');

class CookieIsSet extends TriggerBase {
	public function __construct() {
		parent::__construct('Cookie');
	}
	
	public function handle($trigger_data) { 
		$rule = $trigger_data->get_rule();
		$content = $trigger_data->get_content();

        $cookie_name = isset($rule['cookie-input']) ? $rule['cookie-input'] : '' ;
        $cookie_value = isset($rule['cookie-value-input']) ? $rule['cookie-value-input'] : '';


		if(!empty($cookie_name) || !empty($cookie_value)){
            if(!empty($cookie_name) && empty($cookie_value)){
                if($this->cookie_exists($cookie_name)) return $content;
            }

            if(!empty($cookie_value) && empty($cookie_name)){
                if($this->cookie_value_exists($cookie_value)) return $content;
            }

            if(!empty($cookie_name) && !empty($cookie_value)){
                if($this->cookie_exists($cookie_name) && $_COOKIE[$cookie_name] == $cookie_value) return $content;
            }
        }
        return false;

	}
	
	private function cookie_exists($cookie_name) {
		if(isset($_COOKIE[$cookie_name])) 
			return true;	
		return false;
	}

	private function cookie_value_exists($cookie_val){
	    if(in_array($cookie_val,$_COOKIE))
            return true;
	    return false;
    }

	private function contains_or_not($arg, $f, $t) {
		foreach ($_COOKIE as $key=>$val) {
			if(strpos($key, $arg) !== false)
				return $f;
		}
		return $t; 
	}
}

