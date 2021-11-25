<?php

require_once('ifso/libs/ifso-license-hooks.php');

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );






add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts' );
	function theme_enqueue_scripts() {
    	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
    	wp_enqueue_style( 'IfSoJqueryUiMinCss', get_stylesheet_directory_uri() . '/css/jquery-ui.min.css', array() );

		wp_enqueue_script('IfSoJqueryUiMinJs', get_stylesheet_directory_uri() . '/js/jquery-ui.min.js', array('jquery'));
	    
		wp_enqueue_script('general_js', get_stylesheet_directory_uri() . '/js/general.js', array('jquery', 'IfSoJqueryUiMinJs'));

	    if (!is_page(1551) && !is_page(1821)) {
		    wp_enqueue_script('vticker_min_js', get_stylesheet_directory_uri() . '/js/jquery.vticker.min.js', array('jquery'));
		}

		/* download page load js file */
		if (is_page(9594)) {
			wp_enqueue_script('ifso_download', get_stylesheet_directory_uri() . '/js/ifso_download.js', array('jquery'));
		}
				
	}




function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    $args['quicktags'] = false;
    return $args;
}

add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );

function displaydate(){
    return date_i18n('F');
}
add_shortcode( 'date', 'displaydate' );

function displaytime(){
    return date_i18n('H:i');
}
add_shortcode( 'time', 'displaytime' );

add_action('after_setup_theme', 'remove_admin_bar');

function remove_admin_bar() {
if (!current_user_can('administrator') && !is_admin()) {
  show_admin_bar(false);
}
}

/* Hooks for EDD License */

function one_license_per_domain_restriction($result, $license) {
	$result = IfSo_License_Hooks::apply_one_license_per_domain_restriction($result, $license);

	return $result;

}

function update_license_for_domain($status, $license) {
	$status = IfSo_License_Hooks::update_license_for_domain($status, $license);

	return $status;
}

function deactivated_license_hook($license_id, $download_id) {
	IfSo_License_Hooks::deactivated_license_hook($license_id, $download_id);
}

function activated_license_hook($license_id, $download_id) {
	IfSo_License_Hooks::activated_license_hook($license_id, $download_id);
}

add_filter( 'edd_sl_activate_license_response', 'one_license_per_domain_restriction', 10, 2 );
add_filter( 'edd_sl_check_license_status', 'update_license_for_domain', 10, 2 );
add_action( 'edd_sl_deactivate_license', 'deactivated_license_hook', 10, 2);
add_action( 'edd_sl_activate_license', 'activated_license_hook', 10, 2);


// Start of shortcode finder

function wpb_find_shortcode($atts, $content=null) { 
ob_start();
extract( shortcode_atts( array(
        'find' => '',
    ), $atts ) );
 
$string = $atts['find'];
 
$args = array(
    's' => $string,
    );
 
$the_query = new WP_Query( $args );
 
if ( $the_query->have_posts() ) {
        echo '<ul>';
    while ( $the_query->have_posts() ) {
    $the_query->the_post(); ?>
    <li><a href="<?php  the_permalink() ?>"><?php the_title(); ?></a></li>
    <?php
    }
        echo '</ul>';
} else {
        echo "Sorry no posts found"; 
}
 
wp_reset_postdata();
return ob_get_clean();
}
add_shortcode('shortcodefinder', 'wpb_find_shortcode'); 

// End of shortcode finder

?>