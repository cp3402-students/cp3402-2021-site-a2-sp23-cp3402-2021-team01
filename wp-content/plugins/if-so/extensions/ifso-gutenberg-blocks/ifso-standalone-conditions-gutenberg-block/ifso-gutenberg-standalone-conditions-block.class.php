<?php
/**
 * This block provides the functionality of a standalone condition filter for all gutenberg blocks
 *
 * @since      1.5.1
 * @package    IfSo
 * @subpackage IfSo/extensions/IfSoGutenbergBlock
 * @author Nick Martianov
 */
namespace IfSo\Extensions\IfSoGutenbergBlock;

require_once IFSO_PLUGIN_BASE_DIR . 'extensions/ifso-gutenberg-blocks/ifso-gutenberg-block-base.class.php';
require_once IFSO_PLUGIN_SERVICES_BASE_DIR . 'standalone-condition-service/standalone-condition-service.class.php';
require_once(IFSO_PLUGIN_BASE_DIR. 'public/models/data-rules/ifso-data-rules-ui-model.class.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'services/license-service/license-service.class.php');

use IfSo\PublicFace\Services\StandaloneConditionService\StandaloneConditionService;
use IfSo\Services\LicenseService\LicenseService;

class IfsoGutenbergStandaloneConditionBlock extends IfSoGutenbergBlockBase{
    public function enqueue_block_assets(){
        if($this->gutenberg_exists){
            wp_enqueue_script( 'if-soGooglePlacesJS', IFSO_PLUGIN_DIR_URL . 'admin/js/if-so-google-places.js', array( 'jquery' ));

            wp_register_script(
                'ifso-standalone-conditions-block',
                plugin_dir_url( __FILE__ ) . './ifso-standalone-conditions-gutenberg-block.js',
                array( 'wp-blocks', 'wp-element', 'wp-data','wp-hooks','wp-editor','wp-edit-post', 'if-soGooglePlacesJS'),
                IFSO_WP_VERSION
            );

            $this->pass_data_to_js('ifso-standalone-conditions-block');

            wp_register_style(
                'ifso-standalone-conditions-block',
                plugin_dir_url( __FILE__ ) . './ifso-standalone-conditions-gutenberg-block.css',
                array(),
                IFSO_WP_VERSION
            );

            wp_enqueue_script('ifso-standalone-conditions-block');
            wp_enqueue_style('ifso-standalone-conditions-block');

        }
    }

    public function enqueue_block_styles(){
        if($this->gutenberg_exists){
            wp_enqueue_style(
                'ifso-standalone-conditions-block',
                plugin_dir_url( __FILE__ ) . './ifso-gutenberg-block.css',
                array()
            );
        }
    }

    public function filter_gutenberg_block_through_condition($block_content,$block){
        $standalone_cond_service_instance = StandaloneConditionService::get_instance();
        $attrs = $block['attrs'];
        $inside_gutenberg = (defined('REST_REQUEST') && REST_REQUEST );     //To avoid Server-Side rendered blocks from not showing in editor when condition is not met

        if(!$inside_gutenberg && !empty($attrs['ifso_condition_type']) && !empty($attrs['ifso_condition_rules'])){
            $rule = $attrs['ifso_condition_rules'];
            $rule['trigger_type'] = $attrs['ifso_condition_type'];
            $default_content = isset($attrs['ifso_default_content']) ? $attrs['ifso_default_content'] : '';
            $params =[
                'content'=>$block_content,
                'default'=>$default_content,
                'rule'=>$rule
            ];

            if(!empty($attrs['ifso_aud_addrm'])){
                $params['rule']['add_to_group'] = (array) $attrs['ifso_aud_addrm']['add'];
                $params['rule']['remove_from_group'] = (array) $attrs['ifso_aud_addrm']['rm'];
            }

            return $standalone_cond_service_instance->render($params);
        }

        return $block_content;
    }

    public function add_ifso_standalone_attributes_to_all_block_types(){     //Adding them only on in the js breaks the blocks that are rendered server-side
        $registered_blocks = \WP_Block_Type_Registry::get_instance()->get_all_registered();
        foreach( $registered_blocks as $name => $block ) {
            $block->attributes['ifso_condition_type'] = array(
                'type'    => 'string',
                'default' => '',
            );
            $block->attributes['ifso_condition_rules'] = array(
                'type'    => 'object',
            );
            $block->attributes['ifso_default_exists'] = array(
                'type'    => 'boolean',
                'default' => false,
            );
            $block->attributes['ifso_default_content'] = array(
                'type'    => 'string',
                'default' => '',
            );
            $block->attributes['ifso_aud_addrm'] = array(
                'type'    => 'object',
            );
        }
    }

    private function get_license_status_object(){
        $is_license_valid = LicenseService::get_instance()->is_license_valid();
        $free_condition  = array("Device", "User-Behavior", "Geolocation", "UserIp", "Time-Date");      //Move this to one of the models
        $license_status = (object) [
            "free_conditions" => $free_condition,
            "is_license_valid" => $is_license_valid
        ];

        return $license_status;
    }

    private function pass_data_to_js($scriptName){
        $data_rules_model  = new \IfSo\PublicFace\Models\DataRulesModel\DataRulesUiModel();
        $ui_model_json = json_encode($data_rules_model->get_ui_model());
        $ui_model_links_json = json_encode($data_rules_model->get_links());
        $ui_model_license_obj_json = json_encode($this->get_license_status_object());
        if(function_exists('wp_add_inline_script')){
            $data_scr = <<<SCR
                var data_rules_model_json = {$ui_model_json};
                var ifso_pages_links = {$ui_model_links_json};
                var license_status = {$ui_model_license_obj_json};
SCR;
            wp_add_inline_script($scriptName,$data_scr,'before');
        }
        else{
            wp_localize_script($scriptName,'data_rules_model_json',$ui_model_json);
            wp_localize_script($scriptName,'ifso_pages_links',$ui_model_links_json);
            wp_localize_script($scriptName,'license_status',$ui_model_license_obj_json);
        }
    }
}