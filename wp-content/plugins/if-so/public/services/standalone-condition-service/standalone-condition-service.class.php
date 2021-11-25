<?php

namespace IfSo\PublicFace\Services\StandaloneConditionService;

require_once IFSO_PLUGIN_SERVICES_BASE_DIR . 'triggers-service/triggers-service.class.php';

use IfSo\PublicFace\Services\TriggersService;


class StandaloneConditionService{
    private static $instance;

    private function __construct(){
    }

    public static function get_instance(){
        if(NULL===self::$instance){
            self::$instance = new StandaloneConditionService();
        }
        return self::$instance;
    }

    public function render($atts){
        $content = $atts['content'];
        $default_content = htmlspecialchars_decode($atts['default']);
        $rule = $this->fill_missing_data_rules($atts['rule']);


        return TriggersService\TriggersService::get_instance()->handle_form_data([$rule],[$content],$default_content);
    }


    private function fill_missing_data_rules($rule){
        $rules_model = new \IfSo\PublicFace\Models\DataRulesModel\DataRulesModel;
        $condition_type = (isset($rule['trigger_type'])) ? $rule['trigger_type'] : false;
        $fields = $rules_model->get_condition_fields($condition_type);

        if($condition_type && $fields){
            foreach($fields as $field){
                if(!isset($rule[$field])){
                    $rule[$field] = '';
                }
            }
        }

        if (!isset($rule['freeze-mode'])) $rule['freeze-mode'] = false;     //Validation function in the base trigger class checks this

        return $rule;
    }

    public function render_ifso_condition_shortcode($atts,$content){
        $params = [];
        $params['content'] = $content;
        $params['default'] = !empty($atts['default']) ? $atts['default'] : '';

        if(isset($atts['rule']) && null !== json_decode($atts['rule'])){
            $params['rule'] = json_decode(stripslashes($atts['rule']), true);

            return $this->render($params);
        }

        return $params['default'];
    }
}