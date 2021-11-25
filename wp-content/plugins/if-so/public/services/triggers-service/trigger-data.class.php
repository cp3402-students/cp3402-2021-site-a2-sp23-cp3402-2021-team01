<?php

namespace IfSo\PublicFace\Services\TriggersService;

class TriggerData {
	protected $trigger_id;
	protected $rule;
	protected $version_index;
	protected $data_rules;
	protected $content;
	protected $general_data;
    protected $http_request;
	
	private function __construct($trigger_id,
								 $rule, 
								 $version_index, 
								 &$data_rules,
								 $content,
                                    $http_request) {
		$this->trigger_id = $trigger_id;
		$this->rule = $rule;
		$this->version_index = $version_index;
		$this->data_rules = &$data_rules;
		$this->content = $content;
		$this->general_data = array();
        $this->http_request = $http_request;
	}

	public static function create($trigger_id,
								  $rule, 
								  $version_index,
								  &$data_rules,
								  $content,
                                    $http_request) {
		return new TriggerData($trigger_id,
							   $rule, 
							   $version_index, 
							   $data_rules,
							   $content,
                                $http_request);
	}

	public function get_trigger_id() {
		return $this->trigger_id;
	}

	public function get_rule() {
		return $this->rule;
	}

	public function get_version_index() {
		return $this->version_index;
	}

	public function &get_data_rules() {
		return $this->data_rules;
	}

	public function get_content() {
		return $this->content;
	}

	public function get_general_data($key) {
		if ( !array_key_exists($key, $this->general_data) )
			return false;

		return $this->general_data[$key];
	}

	public function get_http_request(){
	    return $this->http_request;
    }

	public function set_general_data($key, $value) {
		$this->general_data[$key] = $value;
	}
}