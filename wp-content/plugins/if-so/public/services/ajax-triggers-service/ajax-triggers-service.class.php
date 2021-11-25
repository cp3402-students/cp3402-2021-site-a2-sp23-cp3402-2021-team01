<?php
namespace IfSo\PublicFace\Services\AjaxTriggersService;

require_once IFSO_PLUGIN_SERVICES_BASE_DIR . 'triggers-service/triggers-service.class.php';
require_once(IFSO_PLUGIN_BASE_DIR . 'public/helpers/ifso-request/If-So-Http-Get-Request.php');
require_once(IFSO_PLUGIN_BASE_DIR . 'public/services/analytics-service/analytics-service.class.php');

use IfSo\PublicFace\Services\TriggersService;
use IfSo\PublicFace\Helpers\IfSoHttpGetRequest as IfsoRequest;
use IfSo\Services\PluginSettingsService\PluginSettingsService;

class AjaxTriggersService{
    private static $instance;
    private $loader_animation_type;

    private function __construct(){
        $this->loader_animation_type = PluginSettingsService::get_instance()->ajaxLoaderAnimationType->get();
    }

    public static function get_instance(){
        if(NULL === self::$instance)
            self::$instance = new AjaxTriggersService();

        return self::$instance;
    }

    public function create_ifso_ajax_tag($atts){
        $attString = '';
        $loader_classes = ['', 'ifso-logo-loader', 'lds-dual-ring'];
        $loader_type = $this->loader_animation_type;
        foreach($atts as $attName=>$attVal){
            if($attName!== 'id' && $attName!=='ajax'){
                $attString .= " {$attName}='{$attVal}'";
            }
            if($attName === 'loader'){
               if(is_numeric($attVal))
                   $loader_type = $attVal;
                if($attVal === 'no')
                    $loader_type = 0;
            }
        }
        $html = "<IfSoTrigger tid='{$atts['id']}' class='{$loader_classes[$loader_type]}' {$attString} style='display:block;'></IfSoTrigger>";
        return $html;
    }

    public function handle($atts){
        if(!empty($atts['id'])){
            return $this->create_ifso_ajax_tag($atts);
        }
        return '';
    }

    public function handle_ajax(){
        if(wp_doing_ajax() && !empty($_REQUEST['triggers'])){
            $triggers = $_REQUEST['triggers'];
            $page_url = $_REQUEST['page_url'];
            $pageload_referrer = !empty($_REQUEST['pageload_referrer']) ? $_REQUEST['pageload_referrer'] : '';
            $triggers_service = TriggersService\TriggersService::get_instance();
            $http_request = IfsoRequest\IfSoHttpGetRequest::create($page_url,$pageload_referrer);
            \IfSo\PublicFace\Services\AnalyticsService\AnalyticsService::get_instance()->useAjax=false;
            if($triggers && is_array($triggers)){
                $res = new \stdClass();

                foreach($triggers as $id){
                    if(is_numeric($id))
                        $res->$id = $triggers_service->handle(['id'=>$id],$http_request);
                }

                if(!empty($res)){
                    echo json_encode($res);
                }
            }
        }
        wp_die();
    }
}