<?php
/**
 * Plugin Name:     My WordPress Custom Form
 * Plugin URI:      https://solusipress.com/
 * Description:     Form untuk berbagai keperluan di website pribadi
 * Author:          Yerie Piscesa
 * Author URI:      https://solusipress.com/
 * Text Domain:     sp-custom-form
 * Domain Path:     /languages
 * Version:         0.9.0
 *
 * @package         Sp_Custom_Form
 */

// Your code starts here.

if ( ! defined( 'WPINC' ) ) {
	die;
}
@session_start();
include_once( 'vendor/autoload.php' );
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

define( 'RAJAONGKIR_API_KEY', getenv( 'RAJAONGKIR_KEY' ) );
define( 'RAJAONGKIR_BASE_URI', 'https://api.rajaongkir.com/starter' );

include_once( 'sp-class-administrative.php' );
include_once( 'sp-class-calculate-ongkir.php' );

add_shortcode( 'sp_form_ongkir', 'solusi_press_custom_form_ongkir' );
function solusi_press_custom_form_ongkir( $atts ) {
	
	wp_enqueue_style( 'sp-css-grid' );
	$html = '';
	ob_start();
	include_once( plugin_dir_path( __FILE__ ) . 'views/form-ongkir.php' );
	$html = ob_get_contents();
	ob_end_clean();
	    
	return $html;
	
}