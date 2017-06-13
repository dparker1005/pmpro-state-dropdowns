<?php
/**
 * Plugin Name: Paid Memberships Pro - State Dropdowns Add On
 * Description: Creates an autopopulated field for countries and states/provinces for billing fields.
 * Author: Stranger Studios
 * Author URI: https://strangerstuidos.com
 * Version: 0.1
 * License: GPL2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: pmpro-state-dropdown
 * Domain Path: Domain Path
 * Network: false
 */

defined( 'ABSPATH' ) or exit;

class PMPro_State_Dropdowns {

	private static $_instance = null;

	public static function instance() {
		if ( ! isset( self::$_instance ) ) {
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	function init(){
		//add all hooks & filters here.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );
	}

	public static function enqueue_styles_scripts(){
		global $current_user, $user_id;

		$the_user_id = $user_id;

		//fallback to current user on pages that don't support $user_id variable.
		if( empty( $the_user_id ) ){
			$the_user_id = $current_user->ID;
		}

		//we only want to enqueue this on certain pages
		$script_name = basename($_SERVER['SCRIPT_NAME']);
		if(is_admin() &&  $script_name !== 'user-edit.php' && 
						  $script_name !== 'profile.php' && 
						  (empty($_REQUEST['page']) || $_REQUEST['page'] != 'pmpro-addmember') )
			return;
		if(!is_admin() && empty($_REQUEST['level']) && !is_page('your-profile'))
			return;
		
		wp_register_script( 'pmpro-countries', plugins_url( '/js/crs.js', __FILE__ ), array('jquery') );
		wp_register_script( 'pmpro-countries-main', plugins_url( '/js/countries-main.js', __FILE__ ), array('jquery', 'pmpro-countries') );		
		wp_enqueue_script( 'pmpro-countries' );
		wp_enqueue_script( 'pmpro-countries-main' );

		/**
		 * Data for localize script, get user meta from the user and load it into fields using jquery from countries-main.js
		 * @internal: Add in a nonce for security reasons.
		 */

		$user_saved_countries = array();

		if( isset( $_REQUEST['bcountry'] ) ){
			$user_saved_countries['bcountry'] = $_REQUEST['bcountry'];
		}else{
			$user_saved_countries['bcountry'] = get_user_meta( $the_user_id, 'pmpro_bcountry', true );
		}

		if( isset( $_REQUEST['bstate'] ) ){
			$user_saved_countries['bstate'] = $_REQUEST['bstate'];
		}else{
			$user_saved_countries['bstate'] = get_user_meta( $the_user_id, 'pmpro_bstate', true );
		}

		if( isset( $_REQUEST['scountry'] ) ){
			$user_saved_countries['scountry'] = $_REQUEST['scountry'];
		}else{
			$user_saved_countries['scountry'] = get_user_meta( $the_user_id, 'pmpro_scountry', true );
		}

		if( isset( $_REQUEST['sstate'] ) ){
			$user_saved_countries['sstate'] = $_REQUEST['sstate'];
		}else{
			$user_saved_countries['sstate'] = get_user_meta( $the_user_id, 'pmpro_sstate', true );
		}

		wp_localize_script( 'pmpro-countries-main', 'pmpro_state_dropdowns', $user_saved_countries );
	}




}

PMPro_State_Dropdowns::instance();
