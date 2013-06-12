<?php
 /**
 * @package sermonconnect shortcode
 */
/*
Plugin Name: SermonFlow ShortCode
Plugin URI: http://sermonconnect.com/?return=true
Description: Shortcode to embed SermonFlow into content area
Author: Ignite360 Inc.
Author URI: http://ignite360.com/
License: GPLv2 or later
*/
class My_Shortcode {
	static $add_script;

	static function init() {
		add_shortcode('sermonflow', array(__CLASS__, 'handle_shortcode'));

		add_action('init', array(__CLASS__, 'register_script'));
		add_action('wp_footer', array(__CLASS__, 'print_script'));
	}

	static function handle_shortcode($atts,$content=null) {
		self::$add_script = true;
		
		
		extract( shortcode_atts( array(
			'scid' => '',
			'display'=>'coverflow',
			'showitems'=>'0',
			'series'=>'',
			'item'=>'',
			'color'=>'000',
			'boxcolor'=>'000'
			
		), $atts ) );
		
		?>
		
		<style>
		
		#sermonflow {
			min-width:800px;
		}
		#sermonflow td, .sermonflow td{
			border-top: 0px solid #DDDDDD;
    		padding: 0px;
    		font-size:12px !important;
		}
		#sermonflow table,.sermonflow table{
			border-bottom: 0px solid #DDDDDD !important;
    	}
		.sermonflow{
			border-bottom: 0px solid #DDDDDD !important;
			margin:0px !important;
    	}
		</style>

		<div id="sermonflow_<?=$display?>"></div>
		<script>
		jQuery(document).ready(function() {
			
		   jQuery.sermonconnect(jQuery('#sermonflow_<?=$display?>'),{scid:'<?=$scid?>',display:'<?=$display?>',color:'<?=$color?>',boxcolor:'<?=$boxcolor?>'});
		});
		</script>		
	<?
	}

	static function register_script() {
		wp_enqueue_script("jquery"); 
		wp_register_script('my-script', 'http://sc.fhview.com/scripts/sermonconnect.js', '1.0', true);
	}

	static function print_script() {
		if ( ! self::$add_script )
			return;

		wp_print_scripts('my-script');
	}
}

My_Shortcode::init();
?>
