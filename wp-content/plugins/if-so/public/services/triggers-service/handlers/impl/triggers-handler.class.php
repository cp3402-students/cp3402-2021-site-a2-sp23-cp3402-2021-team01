<?php

namespace IfSo\PublicFace\Services\TriggersService\Handlers;

require_once( plugin_dir_path ( __DIR__ ) . 'chain-handler-base.class.php');
require_once( plugin_dir_path ( dirname(__DIR__) ) . 'filters/trigger-filter.class.php');
require_once( plugin_dir_path ( dirname(__DIR__) ) . 'trigger-data.class.php');

use IfSo\PublicFace\Services\TriggersService\Filters;
use IfSo\PublicFace\Services\TriggersService;

class TriggersHandler extends ChainHandlerBase {
	protected $triggers;

	public function __construct($triggers) {
		$this->triggers = $triggers;
	}

	public function handle($context) {
		$trigger_id = $context->get_trigger_id();
		$data_versions = $context->get_data_versions();
		$data_rules = $context->get_data_rules();
        $http_request = $context->get_HTTP_request();

		foreach ($data_rules as $index => $rule) {
			$trigger_data = TriggersService\TriggerData::create($trigger_id,
									    $rule, 
									    $index, 
									    $data_rules,
									    $data_versions[$index],
                                        $http_request);

			$content = $this->run_triggers($trigger_data);

			if ($content !== false)
				return Filters\TriggerFilter::get_instance()->apply_filters_and_hooks($content, $trigger_data);
		}

		return Filters\TriggerFilter::get_instance()->apply_filters( $context->get_default_content() , $trigger_data );
	}

	private function run_triggers($trigger_data) {
		foreach ($this->triggers as $trigger) {
			if ($trigger->can_handle($trigger_data)) {
				return $trigger->handle($trigger_data);
			}
		}

		return false;
	}
}