<?php
/*
Plugin Name: RSG Retrieve Google Drive Spreadsheet
Plugin URI: https://wordpress.org/plugins/
Description: Retrieve contents from google drive spreadsheet and display it as a table.
Version: 0.0.3
Author: Ryner S. Galaus
Author URI: https://profiles.wordpress.org/rynergalaus
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Copyright: Ryner S. Galaus
Text Domain: rsggds
Domain Path: /lang
*/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( ! class_exists('RSGGDS') ) :

	class RSGGDS{

		function __construct() { /* Do nothing here */ }

	    function rsggds_initialize(){
	    	if(!defined('rsggds_LINK')){
				define('rsggds_LINK',plugin_dir_url(__FILE__));
				define('rsggds_ETC',rsggds_LINK.'rsggds-global/');
			}

			if (!defined('RSGGDS_VERSION')){
				define('RSGGDS_VERSION', '0.0.3');
			}

    		register_activation_hook(__FILE__, 'rsggds_plugin_activation');
			add_action( 'plugins_loaded',  'rsggds_version_check') ;
			register_deactivation_hook(__FILE__, 'rsggds_plugin_deactivation');

			add_action( 'wp_enqueue_scripts', array($this,'initialize_script_styles') );
			add_action( 'admin_enqueue_scripts', array($this,'initialize_script_styles') );	

	        if ( empty ( $GLOBALS['admin_page_hooks']['rsg_addons_page'] ) ){
	        	add_action( 'admin_menu',array($this,'rsg_add_page') );
	        	add_action( 'admin_menu',array($this,'GDS_PAGE') );
	        }else{
	        	add_action( 'admin_menu',array($this,'GDS_PAGE') );
	        }

	        if( get_option('rsggds_tbl_w') == false ){ update_option('rsggds_tbl_w','1200'); }
	        if( get_option('rsggds_tbl_h') == false ){ update_option('rsggds_tbl_h','500'); }
	        if( get_option('rsggds_link') == false || get_option('rsggds_link') == '' ){
	        	update_option('rsggds_link','1AmhOJ_pYHGc4q89tY5U8HtIpwB7klSAOdDR1b6M_x7o');
	        }
	        if( get_option('rsggds_title') == false || get_option('rsggds_title') == '' ){
	        	update_option('rsggds_title','Sample Table');
	        }
	        if( get_option('rsggds_cols') == false || get_option('rsggds_cols') == '' ){
	        	update_option('rsggds_cols','A,B,C,D');
	        }
	        if( get_option('rsggds_tbl_color') == false || get_option('rsggds_tbl_color') == '' ){
	        	update_option('rsggds_tbl_color','#2d2d2d');
	        }
	        add_shortcode( 'RSGGDS_TABLE','rsggds_return_table_sc');

	    }

	    /**
		 ****************************************************************
		 * MAIN PAGE = Title|Menu Title|Capability|Slug|Function|Icon - Position
		 * @since   0.0.1
		 ****************************************************************/

		function rsg_add_page(){
			add_menu_page(
				__('RSG Addons'), __('RSG Addons'), 'manage_options', 'rsg_addons_page', 'rsg_addons_page_callback', 'dashicons-editor-code', 2
			);
		}

		/**
		 ****************************************************************
		 * SUBPAGE = Slug|Title|Menu Title|Capability|Slug|Function
		 * @since   0.0.1
		 ****************************************************************/
	    function GDS_PAGE(){ add_submenu_page( 'rsg_addons_page', __('Spreadsheet'), __('Spreadsheet'), 'manage_options', 'rsggds', 'rsggds_sheet' ); }

	    /**
		 ****************************************************************
		 * SCRIPTS AND STYLES
		 * @since   0.0.3
		 ****************************************************************/
	    function initialize_script_styles(){
			wp_register_script( 'rsggds_scripts', rsggds_ETC.'rsggds.min.js', array( 'jquery' ), 1.0, true );
		    wp_enqueue_script( 'rsggds_scripts' );
		    wp_localize_script( 'rsggds_scripts', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));

		    wp_register_style( 'rsggds_styles', rsggds_ETC.'rsggds.min.css', null, '0.0.1', 'screen' );
		    wp_enqueue_style( 'rsggds_styles' );

			if( (! wp_style_is('rsg_tablesorter', 'registered ') ) || (! wp_style_is('rsg_tablesorter', 'enqueued ') ) ){
			    wp_register_style( 'rsg_tablesorter', rsggds_LINK.'lib/tablesorter/theme.default.css' );
			    wp_enqueue_style( 'rsg_tablesorter' );
			}
			if( (! wp_script_is('rsg_tablesorter_js', 'registered ') ) || (! wp_script_is('rsg_tablesorter_js', 'enqueued ') ) ){
				wp_register_script( 'rsg_tablesorter_js', rsggds_LINK.'lib/tablesorter/jquery.tablesorter.js', array( 'jquery' ), '2.31.1', true );
		    	wp_enqueue_script( 'rsg_tablesorter_js' );
			}
			if( (! wp_script_is('rsg_tblsorter_jsw', 'registered ') ) || (! wp_script_is('rsg_tblsorter_jsw', 'enqueued ') ) ){
				wp_register_script( 'rsg_tablesorter_jsw', rsggds_LINK.'lib/tablesorter/jquery.tablesorter.widgets.js', array( 'jquery' ), '2.31.1', true );
		    	wp_enqueue_script( 'rsg_tablesorter_jsw' );
			}
		}
	    
	}

	/**
	 ****************************************************************
	 * VERSION CONTROL
	 * @since   0.0.1
	 ****************************************************************/
	if(!defined('rsggds_LINK')){
		define('rsggds_LINK',plugin_dir_url(__FILE__));
		define('rsggds_ETC',rsggds_LINK.'rsggds-global/');
	}
	function rsggds_plugin_activation() { update_option('RSGGDS_VERSION', RSGGDS_VERSION); }

	function rsggds_version_check(){ if (RSGGDS_VERSION !== get_option('RSGGDS_VERSION')){ rsggds_plugin_activation(); } }

	function rsggds_plugin_deactivation() {
		delete_option('RSGGDS_VERSION');
		delete_option('rsggds_tbl_color');
		delete_option('rsggds_tbl_w');
		delete_option('rsggds_tbl_h');
		delete_option('rsggds_link');
		delete_option('rsggds_title');
	    delete_option('rsggds_row');
	    delete_option('rsggds_cols');
	}

	/**
	 ****************************************************************
	 * CALL NECESSARY FUNCTIONS
	 * @since   0.0.1
	 ****************************************************************/
		require('rsggds-global/func.php');
		require('lib/google/vendor/autoload.php');

		function rsggds_start(){
		    $rsggds = new RSGGDS();
		    $rsggds->rsggds_initialize();
		    return $rsggds;
		}rsggds_start();
endif;


/**
 ****************************************************************
 * CALL MAIN PAGE
 * @since   0.0.2
 ****************************************************************/
if( !function_exists('rsg_addons_page_callback') ){
	function rsg_addons_page_callback(){
		echo '<i class="rsg-basis" dataurl="'.get_home_url().'/wp-admin/"></i>';
	}	
}