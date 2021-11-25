<?php
namespace IfSo\Extensions\Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class IFSO_Elementor_Widgets {

	protected static $instance = null;

    public $isOn = false;

	public static function get_instance() {
		if ( ! isset( static::$instance ) ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	protected function __construct() {}

	public function init_elementor_widget(){
        if(did_action( 'elementor/loaded' )){
            $this->isOn = true;
        }

        if($this->isOn){
            require_once( 'widgets/ifso_dynamic_widget.php' );
            add_action( 'elementor/widgets/widgets_registered', [ $this, 'ifso_register_widgets' ] );

            // scripts and styles
            add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'ifso_enqueue_scripts' ] );
            add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'ifso_enqueue_styles' ] );
            add_action( 'elementor/preview/enqueue_styles', [ $this, 'ifso_enqueue_preview_styles' ] );
        }
    }


	public function ifso_register_widgets() {
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new \Elementor\IFSO_Dynamic_Widget() );
	}

	public function ifso_enqueue_preview_styles(){
		wp_enqueue_style( 'ifso-preview', plugin_dir_url(__FILE__)  . 'assets/css/ifso-preview.css' );
	}

	public function ifso_enqueue_scripts() {
	    global $wp_version;
	    global $wp_scripts;
		wp_enqueue_script( 'datetime', IFSO_PLUGIN_DIR_URL . 'admin/js/jquery.ifsodatetimepicker.full.min.js',  [ 'jquery' ]);
		wp_enqueue_script( 'WeeklyScheduleMinJs', IFSO_PLUGIN_DIR_URL . 'admin/js/jquery.weekly-schedule-plugin.min.js',  [ 'jquery' ] );
        if(version_compare($wp_version,'5.6')!== -1 || version_compare($wp_scripts->registered['jquery']->ver,'3.5.1')!==-1)    //wp 5.6 intrduced a new version of jquery
            wp_enqueue_script( 'ifso-jquery-ui', IFSO_PLUGIN_DIR_URL . 'admin/js/jquery-ui.min.js', array( 'jquery' ), IFSO_WP_VERSION, false );
        else
            wp_enqueue_script( 'ifso-jquery-ui', IFSO_PLUGIN_DIR_URL . 'admin/js/jquery-ui-old.min.js', array( 'jquery' ), IFSO_WP_VERSION, false );

		wp_enqueue_script( 'ifso-editor-js', plugin_dir_url(__FILE__)  . 'assets/js/ifso.js', [
			'jquery',
			'ifso-jquery-ui',
			'WeeklyScheduleMinJs',
			'datetime'
		] );

	}

	public function ifso_enqueue_styles() {
		wp_enqueue_style( 'ifso-font', plugin_dir_url(__FILE__)  . 'assets/css/ifso-font.css' );
		wp_enqueue_style( 'ifso-editor-css', plugin_dir_url(__FILE__)  . 'assets/css/ifso-editor.css' );
	}

    public function register_elementor_category() {
            \Elementor\Plugin::instance()->elements_manager->add_category(
                'IFSO',
                [
                    'title' => 'IF-SO',
                ]
            );
    }

}





