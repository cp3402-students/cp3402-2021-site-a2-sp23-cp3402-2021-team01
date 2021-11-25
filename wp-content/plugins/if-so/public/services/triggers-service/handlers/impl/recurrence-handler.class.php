<?php

namespace IfSo\PublicFace\Services\TriggersService\Handlers;

require_once( plugin_dir_path ( __DIR__ ) . 'chain-handler-base.class.php');
require_once( plugin_dir_path ( dirname(__DIR__) ) . 'filters/trigger-filter.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/license-service/license-service.class.php');
require_once( IFSO_PLUGIN_SERVICES_BASE_DIR . 'recurrence-service/recurrence-service.class.php' );

use IfSo\PublicFace\Services\TriggersService\Filters;
use IfSo\Services\LicenseService;
use IfSo\PublicFace\Services\RecurrenceService;

class RecurrenceHandler extends ChainHandlerBase {
	public function handle($context) {
		$trigger_id = $context->get_trigger_id();
		$data_versions = $context->get_data_versions();
		$data_rules = $context->get_data_rules();

		$is_license_valid = LicenseService\LicenseService::get_instance()->is_license_valid();

        $overriding_versions = [];
        foreach ($data_rules as $key=>$value){
            if(isset($value['recurrence-override']) && $value['recurrence-override']==true){
                $overriding_versions[] = $key;
            }
        }


        // check if there is a cookie for the current trigger (a session cookie)
		if (isset($_COOKIE["ifso_recurrence_session_" . $trigger_id])) {
			
			$session_cookie_for_trigger_id_json = 
				stripslashes($_COOKIE["ifso_recurrence_session_" . $trigger_id]);
			
			$session_cookie_for_trigger_id = 
				json_decode($session_cookie_for_trigger_id_json, true);

			// There is a session cookie!
			$recurrence_version_index = $session_cookie_for_trigger_id['version_index'];
			$recurrence_version_type = $session_cookie_for_trigger_id['trigger_type'];

			$recurrence_type = 'all-session';
			$recurrence_expiration_date = '';

			// TODO Create the following service under IfsoServices namespace (Already exists as recurrence_handler)
			if (RecurrenceService\RecurrenceService::get_instance()->is_recurrence_valid($data_rules, 
				$recurrence_version_index, $recurrence_version_type, $recurrence_type, $is_license_valid, $recurrence_expiration_date)) {
				// the recurrence is valid!

				// One last test is the recurrence type

				$rule = $data_rules[$recurrence_version_index];
				$index = $recurrence_version_index;

				if ( !isset( $rule['freeze-mode'] ) || $rule['freeze-mode'] != "true" ){
                    //return Filters\TriggerFilter::get_instance()->apply_filters( $data_versions[$recurrence_version_index] );
                    $recurr_version = $data_versions[$recurrence_version_index];
                }
			}

		}

		if ( isset($_COOKIE['ifso_recurrence_data']) ) {
			$recurrence_data_json = stripslashes($_COOKIE['ifso_recurrence_data']);
			$recurrence_data = json_decode($recurrence_data_json, true);

			// pull out the data for the current trigger (if exists)
			if (array_key_exists($trigger_id, $recurrence_data)) {
				$trigger_recurrence_data = $recurrence_data[$trigger_id];

				/* recurrence structure:
				 * {
				 * 		expiration_date: <timestamp>,
			 	 * 		version_index: <version_index>,
				 * 		trigger_type: <trigger_type>,		
				 * }
				 */

				$recurrence_expiration_date = '';
				if (array_key_exists('expiration_date', $trigger_recurrence_data))
					$recurrence_expiration_date = $trigger_recurrence_data['expiration_date'];
				
				$recurrence_version_index = '';
				if (array_key_exists('version_index', $trigger_recurrence_data))
					$recurrence_version_index = $trigger_recurrence_data['version_index'];
				
				$recurrence_version_type = '';
				if (array_key_exists('trigger_type', $trigger_recurrence_data))
					$recurrence_version_type = $trigger_recurrence_data['trigger_type'];

				$recurrence_type = '';
				if (array_key_exists('recurrence_type', $trigger_recurrence_data))
					$recurrence_type = $trigger_recurrence_data['recurrence_type'];

				if (RecurrenceService\RecurrenceService::get_instance()->is_recurrence_valid($data_rules, $recurrence_version_index, $recurrence_version_type, $recurrence_type, $is_license_valid, $recurrence_expiration_date)) {
					// the recurrence is valid!

					// we need to check the expiration_date later (when we add the 'custom' option)

					$rule = $data_rules[$recurrence_version_index];
					$index = $recurrence_version_index;

					if ( !isset( $rule['freeze-mode'] ) || $rule['freeze-mode'] != "true" )
						//return Filters\TriggerFilter::get_instance()->apply_filters($data_versions[$index]);
                        $recurr_version = $data_versions[$recurrence_version_index];
				}
			}
		}



		if(isset($recurr_version) && count($overriding_versions)>0){
            $context->set_new_default($recurr_version);
            $context->clear_context(array_merge($overriding_versions,[$recurr_version]));
            return $this->handle_next($context);
        }
        elseif(isset($recurr_version)){
            return Filters\TriggerFilter::get_instance()->apply_filters($recurr_version);
        }

		return $this->handle_next($context);
	}
}