<?php

namespace IfSo\PublicFace\Services\TriggersService\Filters;

require_once(__DIR__ . '/impl/auto-p-tag-filter.class.php');
require_once(__DIR__ . '/impl/rich-snippet-filter.class.php');
require_once(__DIR__ . '/impl/w3-total-cache-fragment-caching-filter.class.php');
require_once(__DIR__ . '/hooks/impl/recurrence-hook.class.php');
require_once(__DIR__ . '/hooks/impl/analytics-hook.class.php');
require_once(__DIR__ . '/hooks/impl/groups-hook.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/plugin-settings-service/plugin-settings-service.class.php');

use IfSo\PublicFace\Services\TriggersService\Filters\Hooks;
use IfSo\Services\PluginSettingsService;

class TriggerFilter {
	private $filters;
	private $hooks;
	
	private static $instance;
	
	private function __construct() {
		$this->filters = $this->load_filters();
		$this->hooks = $this->load_hooks();
	}
	
	private function load_filters() {
		$filters = array();
		
		$filters[] = new AutoPTagFilter();
		$filters[] = new RichSnippetFilter();
		//$filters[] = new W3TotalCacheFragmentCachingFilter(); remove caching filter for now as it breaks some pages sometimes

		return $filters;
	}

	private function load_hooks() {
		$hooks = array();

		$hooks[] = new Hooks\RecurrenceHook();
		$hooks[] = new Hooks\AnalyticsHook();
		$hooks[] = new Hooks\GroupHook();

		return $hooks;
	}
	
	public static function get_instance() {
		if ( NULL == self::$instance )
			self::$instance = new TriggerFilter();

		return self::$instance;
	}
	
	public function apply_filters_and_hooks($text, $rule) {
		foreach ($this->hooks as $hook) {
			$hook->apply($text, $rule);
		}
		
		return $this->apply_filters($text);
	}

	public function apply_filters($text , $trigger_data=false) {
	    if($trigger_data!==false)$this->apply_default_analytics_hook($trigger_data); //Since hooks are not applied to default, apply this (trigger analytics) hook here
		if ( has_filter('the_content', 'tve_clean_wp_editor_content') )
			return $this->mutate_text($text);

		$apply_the_content_filter = 
			PluginSettingsService\PluginSettingsService::get_instance()->applyTheContentFilterOption->get();

		if ( ! $apply_the_content_filter )
			return $this->mutate_text($text);

		$text = $this->apply_the_content_filter($text);
		return $this->mutate_text($text);
	}

	private function mutate_text($text) {
		foreach ($this->filters as $filter) {
			$text = $filter->change_text($text);
		}

        if(PluginSettingsService\PluginSettingsService::get_instance()->forceDoShortcode->get())
            $text = do_shortcode($text);

		return $text;
	}

	private function apply_the_content_filter($text) {
		foreach ($this->filters as $filter) {
			$filter->before_apply();
		}

		$text = apply_filters('the_content', $text);

		foreach ($this->filters as $filter) {
			$filter->after_apply();
		}

		return $text;
	}

	public function apply_default_analytics_hook($trigger_data){
	    //Counts every application of the trigger in the trigger(global) analytics
        foreach($this->hooks as $hook){
            if($hook instanceof Hooks\AnalyticsHook){
                $hook->apply_default($trigger_data);
            }
        }
    }
}