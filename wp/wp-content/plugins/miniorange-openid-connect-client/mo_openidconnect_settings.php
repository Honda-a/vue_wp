<?php
/**
* Plugin Name: OpenID Connect Login ( OpenID Connect Client)
* Plugin URI: http://miniorange.com
* Description: OpenID Connect Client plugin allows login (Single Sign On) with any OpenID provider that conforms to the OpenID Connect 1.0 standard.
* Version: 1.5.1
* Author: miniOrange
* Author URI: http://miniorange.com
* License: GPL2
*/

require('handler/openidconnect_handler.php');
include_once dirname( __FILE__ ) . '/class-mo-openidconnect-widget.php';
require('class-customer.php');
require('mo_openidconnect_settings_page.php');
require('views/mo_openidconnect_feedback_form.php');

class mo_openidconnect {

	function __construct() {
		add_action( 'admin_menu', array( $this, 'miniorange_menu' ) );
		add_action( 'admin_init',  array( $this, 'miniorange_openidconnect_save_settings' ) );
		add_action( 'plugins_loaded',  array( $this, 'mo_login_widget_text_domain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_style' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'plugin_settings_script' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'plugin_settings_style' ) ,5);
		register_activation_hook(__FILE__, array( $this, 'mo_openidconnect_activate'));
		register_deactivation_hook(__FILE__, array( $this, 'mo_openidconnect_deactivate'));
		remove_action( 'admin_notices', array( $this, 'mo_openidconnect_success_message') );
		remove_action( 'admin_notices', array( $this, 'mo_openidconnect_error_message') );
		add_action( 'admin_footer', array( $this, 'mo_openidconnect_client_feedback_request' ) );
		//set default values
		update_option( 'mo_openidconnect_login_icon_space','5' );
		update_option( 'mo_openidconnect_login_icon_custom_width','325.43' );
		update_option( 'mo_openidconnect_login_icon_custom_height','9.63' );
        add_option( 'mo_openidconnect_login_icon_custom_size','35' );
		add_option( 'mo_openidconnect_login_icon_custom_color', '2B41FF' );	
		add_option( 'mo_openidconnect_login_icon_custom_boundary','4');
		add_shortcode('mo_openidconnect_login', array( $this,'mo_openidconnect_shortcode_login'));
	}

	function mo_openidconnect_success_message() {
		$class = "error";
		$message = get_option('message');
		echo "<div class='" . $class . "'> <p>" . $message . "</p></div>";
	}

	function mo_openidconnect_error_message() {
		$class = "updated";
		$message = get_option('message');
		echo "<div class='" . $class . "'><p>" . $message . "</p></div>";
	}

	public function mo_openidconnect_activate() {
		update_option( 'mo_openidconnect_client_auto_register', 1);
	}

	function mo_openidconnect_client_feedback_request() {
		mo_openidconnect_client_display_feedback_form();
	}
	
	public function mo_openidconnect_deactivate() {
		//delete all stored key-value pairs
		//do_action('clear_os_cache'); 
		delete_option('host_name');
		delete_option('new_registration');
		delete_option('mo_openidconnect_admin_phone');
		delete_option('verify_customer');
		delete_option('mo_openidconnect_admin_customer_key');
		delete_option('mo_openidconnect_admin_api_key');
		delete_option('mo_openidconnect_new_customer');
		delete_option('customer_token');
		delete_option('message');
		delete_option('mo_openidconnect_registration_status');
		delete_option('mo_oauth_show_mo_openidconnect_message');
	}


	function miniorange_menu() {
		//Add miniOrange plugin to the menu
		$page = add_menu_page( 'MO OpenIDConnect Settings ' . __( 'Configure OpenIDConnect', 'mo_openidconnect_settings' ), 'miniOrange OpenID Connect', 'administrator', 'mo_openidconnect_settings', array( $this, 'mo_openidconnect_login_options' ) ,plugin_dir_url(__FILE__) . 'images/miniorange.png');

		global $submenu;
		if ( is_array( $submenu ) AND isset( $submenu['mo_openidconnect_settings'] ) )
		{
			$submenu['mo_openidconnect_settings'][0][0] = __( 'Configure OpenIDConnect', 'mo_openidconnect_login' );
		}
	}

	function  mo_openidconnect_login_options () {
		global $wpdb;
		update_option( 'host_name', 'https://auth.miniorange.com' );
		$customerRegistered = mo_openidconnect_is_customer_registered();
		if( $customerRegistered ) { 
			mo_register();
		} else {
			mo_register();
		}
	}

	function plugin_settings_style() {
		wp_enqueue_style( 'mo_openidconnect_admin_settings_style', plugins_url( 'css/style_settings.css', __FILE__ ) );
		wp_enqueue_style( 'mo_openidconnect_admin_settings_phone_style', plugins_url( 'css/phone.css', __FILE__ ) );
		wp_enqueue_style( 'mo_wpns_admin_settings_datatable_style', plugins_url('css/jquery.dataTables.min.css', __FILE__));
		wp_enqueue_style( 'mo-wp-bootstrap-social',plugins_url('css/bootstrap-social.css', __FILE__), false );
		wp_enqueue_style( 'mo-wp-bootstrap-main',plugins_url('css/bootstrap.min-preview.css', __FILE__), false );
		wp_enqueue_style( 'mo-wp-font-awesome',plugins_url('css/font-awesome.min.css?version=4.8', __FILE__), false );
		wp_enqueue_style( 'mo-wp-font-awesome',plugins_url('css/font-awesome.css?version=4.8', __FILE__), false );
	}

	function plugin_settings_script() {
		wp_enqueue_script( 'mo_wpns_admin_datatable_script', plugins_url('js/jquery.dataTables.min.js', __FILE__ ) );
		wp_enqueue_script( 'mo_openidconnect_admin_settings_script', plugins_url( 'js/settings.js', __FILE__ ) );
		wp_enqueue_script( 'mo_openidconnect_admin_settings_phone_script', plugins_url('js/phone.js', __FILE__ ) );
		wp_enqueue_script( 'mo_openidconnect_admin_settings_script', plugins_url('js/bootstrap.min.js', __FILE__ ));
	}

	function mo_login_widget_text_domain(){
		load_plugin_textdomain( 'flw', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}

	private function mo_openidconnect_show_success_message() {
		remove_action( 'admin_notices', array( $this, 'mo_openidconnect_success_message') );
		add_action( 'admin_notices', array( $this, 'mo_openidconnect_error_message') );
	}

	private function mo_openidconnect_show_error_message() {
		remove_action( 'admin_notices', array( $this, 'mo_openidconnect_error_message') );
		add_action( 'admin_notices', array( $this, 'mo_openidconnect_success_message') );
	}

	public function mo_openidconnect_check_empty_or_null( $value ) {
		if( ! isset( $value ) || empty( $value ) ) {
			return true;
		}
		return false;
	}

	
	function miniorange_openidconnect_save_settings(){
		if ( isset( $_POST['option'] ) and $_POST['option'] == "mo_oauth_mo_openidconnect_message" ) {
			update_option( 'mo_oauth_show_mo_openidconnect_message', 1 );

			return;
		}
		if( current_user_can( 'manage_options' ) && isset( $_POST['option'] )) {
			if( $_POST['option'] == "mo_openidconnect_register_customer" ) {	//register the admin to miniOrange
				//validation and sanitization
				$email = '';
				$phone = '';
				$password = '';
				$confirmPassword = '';
				$fname = '';
				$lname = '';
				$company = '';
				if( $this->mo_openidconnect_check_empty_or_null( $_POST['email'] ) || $this->mo_openidconnect_check_empty_or_null( $_POST['phone'] ) || $this->mo_openidconnect_check_empty_or_null( $_POST['password'] ) || $this->mo_openidconnect_check_empty_or_null( $_POST['confirmPassword'] ) ) {
					update_option( 'message', 'All the fields are required. Please enter valid entries.');
					$this->mo_openidconnect_show_error_message();
					return;
				} else if( strlen( $_POST['password'] ) < 8 || strlen( $_POST['confirmPassword'] ) < 8){
					update_option( 'message', 'Choose a password with minimum length 8.');
					$this->mo_openidconnect_show_error_message();
					return;
				} else{
					$email = sanitize_email( $_POST['email'] );
					$phone = sanitize_text_field( $_POST['phone'] );
					$password = sanitize_text_field( $_POST['password'] );
					$confirmPassword = sanitize_text_field( $_POST['confirmPassword'] );
					$fname = sanitize_text_field( $_POST['fname'] );
					$lname = sanitize_text_field( $_POST['lname'] );
					$company = sanitize_text_field( $_POST['company'] );
				}

				update_option( 'mo_openidconnect_admin_email', $email );
				update_option( 'mo_openidconnect_admin_phone', $phone );
				update_option( 'mo_openidconnect_admin_fname', $fname );
				update_option( 'mo_openidconnect_admin_lname', $lname );
				update_option( 'mo_openidconnect_admin_company', $company );

				if( mo_openidconnect_is_curl_installed() == 0 ) {
					return $this->mo_openidconnect_show_curl_error();
				}

				if( strcmp( $password, $confirmPassword) == 0 ) {
					update_option( 'password', $password );
					$customer = new Customer();
					$content = json_decode($customer->check_customer(), true);
					if( strcasecmp( $content['status'], 'CUSTOMER_NOT_FOUND') == 0 ){
						$content = json_decode($customer->send_otp_token(), true);
						if(strcasecmp($content['status'], 'SUCCESS') == 0) {
							update_option( 'message', ' A one time passcode is sent to ' . get_option('mo_openidconnect_admin_email') . '. Please enter the OTP here to verify your email.');
							$_SESSION['mo_openidconnect_transactionId'] = $content['txId'];
							update_option('mo_openidconnect_registration_status','MO_OTP_DELIVERED_SUCCESS');

							$this->mo_openidconnect_show_success_message();
						}else{
							update_option('message','There was an error in sending email. Please click on Resend OTP to try again.');
							update_option('mo_openidconnect_registration_status','MO_OTP_DELIVERED_FAILURE');
							$this->mo_openidconnect_show_error_message();
						}
					} else {
						$this->mo_openidconnect_get_current_customer();
					}
				} else {
					update_option( 'message', 'Passwords do not match.');
					delete_option('verify_customer');
					$this->mo_openidconnect_show_error_message();
				}
			} else if(isset($_POST['option']) and $_POST['option'] == "mo_openidconnect_validate_otp"){
				if( mo_openidconnect_is_curl_installed() == 0 ) {
					return $this->mo_openidconnect_show_curl_error();
				}
				//validation and sanitization
				$otp_token = '';
				if( $this->mo_openidconnect_check_empty_or_null( $_POST['mo_openidconnect_otp_token'] ) ) {
					update_option( 'message', 'Please enter a value in OTP field.');
					update_option('mo_openidconnect_registration_status','MO_OTP_VALIDATION_FAILURE');
					$this->mo_openidconnect_show_error_message();
					return;
				} else{
					$otp_token = sanitize_text_field( $_POST['mo_openidconnect_otp_token'] );
				}

				$customer = new Customer();
				$content = json_decode($customer->validate_otp_token($_SESSION['mo_openidconnect_transactionId'], $otp_token ),true);
				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
					$this->create_customer();
				}else{
					update_option( 'message','Invalid one time passcode. Please enter a valid OTP.');
					update_option('mo_openidconnect_registration_status','MO_OTP_VALIDATION_FAILURE');
					$this->mo_openidconnect_show_error_message();
				}
			} else if( $_POST['option'] == "mo_openidconnect_verify_customer" ) {
				if( mo_openidconnect_is_curl_installed() == 0 ) {
					return $this->mo_openidconnect_show_curl_error();
				}
				//validation and sanitization
				$email = '';
				$password = '';
				if( $this->mo_openidconnect_check_empty_or_null( $_POST['email'] ) || $this->mo_openidconnect_check_empty_or_null( $_POST['password'] ) ) {
					update_option( 'message', 'All the fields are required. Please enter valid entries.');
					$this->mo_openidconnect_show_error_message();
					return;
				} else{
					$email = sanitize_email( $_POST['email'] );
					$password = sanitize_text_field( $_POST['password'] );
				}

				update_option( 'mo_openidconnect_admin_email', $email );
				update_option( 'password', $password );
				$customer = new Customer();
				$content = $customer->get_customer_key();
				$customerKey = json_decode( $content, true );
				if( json_last_error() == JSON_ERROR_NONE ) {
					update_option( 'mo_openidconnect_admin_customer_key', $customerKey['id'] );
					update_option( 'mo_openidconnect_admin_api_key', $customerKey['apiKey'] );
					update_option( 'customer_token', $customerKey['token'] );
					update_option( 'mo_openidconnect_admin_phone', $customerKey['phone'] );
					delete_option('password');
					update_option( 'message', 'Customer retrieved successfully');
					delete_option('verify_customer');
					$this->mo_openidconnect_show_success_message();
				} else {
					update_option( 'message', 'Invalid username or password. Please try again.');
					$this->mo_openidconnect_show_error_message();
				}
			}
			else if( $_POST['option'] == "mo_openidconnect_add_app" ) {
				$scope = '';
				$clientid = '';
				$clientsecret = '';
				if($this->mo_openidconnect_check_empty_or_null($_POST['mo_openidconnect_client_id']) || $this->mo_openidconnect_check_empty_or_null($_POST['mo_openidconnect_client_secret'])) {
					update_option( 'message', 'Please enter valid Client ID and Client Secret.');
					$this->mo_openidconnect_show_error_message();
					return;
				} else{
					$scope = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_scope'] ));
					$clientid = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_client_id'] ));
					$clientsecret = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_client_secret'] ));
					$custom_appname = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_custom_app_name'] ));
					$appname = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_app_name'] ));
					add_option( "mo_openidconnect_app_name_".$custom_appname, $appname);

					if(get_option('mo_openidconnect_apps_list'))
						$appslist = get_option('mo_openidconnect_apps_list');
					else
						$appslist = array();

					$email_attr = "";
					$name_attr = "";
					$displayappname = "";
					$newapp = array();

					foreach($appslist as $key => $currentapp){
						if($appname == $key){
							$newapp = $currentapp;
							break;
						}
					}

					$newapp['clientid'] = $clientid;
					$newapp['clientsecret'] = $clientsecret;
					$newapp['scope'] = $scope;
					if( ! isset($newapp['redirecturi'] ) )
					{
						$newapp['redirecturi'] = site_url();
					}
					
					if(!isset($newapp['apptype'])) {
						if($appname=="openidconnect")
							$newapp['apptype'] = "openidconnect";
						else

							$newapp['apptype'] = "oauth";
					}
					
					$authorizeurl = stripslashes(sanitize_text_field($_POST['mo_openidconnect_authorizeurl']));
					$idtokenurl = stripslashes(sanitize_text_field($_POST['mo_openidconnect_idtokenurl']));
					$resourceownerdetailsurl = stripslashes(sanitize_text_field($_POST['mo_openidconnect_resourceownerdetailsurl']));
					$groupdetailsurl = stripslashes(sanitize_text_field($_POST['mo_openidconnect_groupdetailsurl']));

					if ( isset($_POST['mo_openidconnect_display_app_name']) && $_POST['mo_openidconnect_display_app_name'] != "") {
						$displayappname = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_display_app_name']));
					}
					if($appname != " ")
						$appname=stripcslashes(sanitize_text_field( $_POST['mo_openidconnect_custom_app_name'])); //set custom app name
					else
						$appname = stripcslashes(sanitize_text_field( $_POST['mo_openidconnect_app_name']));

					$newapp['authorizeurl'] = $authorizeurl;
					$newapp['idtokenurl'] = $idtokenurl;
					$newapp['resourceownerdetailsurl'] = $resourceownerdetailsurl;
					$newapp['groupdetailsurl'] = $groupdetailsurl;
					if ( $displayappname != "")
						$newapp['displayappname'] = $displayappname;
					$appslist[$appname] = $newapp;
					update_option('mo_openidconnect_apps_list', $appslist);
					$statusMessage='';
				update_option( 'message', 'Advanced Setting has been updated.'.$statusMessage);
				
					wp_redirect('admin.php?page=mo_openidconnect_settings&action=update&app='.urlencode($appname));
					mo_openidconnect_show_success_message();
				}
			}
			
			if($_POST['option'] == "mo_wpns_manual_clear") {
				global $wpdb;
				$wpdb->query("DELETE FROM ".$wpdb->prefix.Mo_OpenidConnect_Constants::USER_TRANSCATIONS_TABLE."");
			}
			else if( $_POST['option'] == "mo_openidconnect_attribute_mapping" ) {

				$appname = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_app_name'] ));
				$username_attr = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_username_attr'] ));
				$email_attr = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_email_attr'] ));
				$firstname_attr = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_firstname_attr'] ));
				$lastname_attr = stripslashes(sanitize_text_field( $_POST['mo_openidconnect_lastname_attr'] ));
				$display_attr = stripslashes(sanitize_text_field( $_POST['oauth_client_am_display_name'] ));

				$appslist = get_option('mo_openidconnect_apps_list');
				foreach($appslist as $appkey => $currentapp){
					if($appname == $appkey){
						$currentapp['username_attr'] = $username_attr;
						$currentapp['email_attr'] = $email_attr;
						$currentapp['firstname_attr'] = $firstname_attr;
						$currentapp['lastname_attr'] = $lastname_attr;
						$currentapp['display_attr'] = $display_attr;

						if(isset($_POST['mapping_groupname_attribute']))
							update_option('mo_openidconnect_client_'.$appname.'_mapping_groupname_attribute', $_POST['mapping_groupname_attribute']);
				
						$custom_attributes = array();
						$i = 0;
						foreach($_POST as $key=>$value){
							if(strpos($key, "mo_openidconnect_client_custom_attribute_key") !== false && !empty($_POST[$key]) ) {
								$i++;
								$attr_value = 'mo_openidconnect_client_custom_attribute_value_'.$i;
								$custom_attributes[$value] = $_POST[$attr_value];
							}
						}
						update_option('mo_openidconnect_client_custom_attrs_mapping',$custom_attributes);

						$appslist[$appkey] = $currentapp;
						break;
					}
				}
				update_option('mo_openidconnect_apps_list', $appslist);
				update_option( 'message', 'Your settings are saved successfully.' );
				$this->mo_openidconnect_show_success_message();
				wp_redirect('admin.php?page=mo_openidconnect_settings&action=update&app='.urlencode($appname));
			} 
			
		    else if( $_POST['option'] == "mo_openidconnect_contact_us_query_option" ) {
				if( mo_openidconnect_is_curl_installed() == 0 ) {
					return $this->mo_openidconnect_show_curl_error();
				}
				// Contact Us query
				$email = $_POST['mo_openidconnect_contact_us_email'];
				$phone = $_POST['mo_openidconnect_contact_us_phone'];
				$query = $_POST['mo_openidconnect_contact_us_query'];
				$customer = new Customer();
				if ( $this->mo_openidconnect_check_empty_or_null( $email ) || $this->mo_openidconnect_check_empty_or_null( $query ) ) {
					update_option('message', 'Please fill up Email and Query fields to submit your query.');
					$this->mo_openidconnect_show_error_message();
				} else {
					$submited = $customer->submit_contact_us( $email, $phone, $query );
					if ( $submited == false ) {
						update_option('message', 'Your query could not be submitted. Please try again.');
						$this->mo_openidconnect_show_error_message();
					} else {
						update_option('message', 'Thanks for getting in touch! We shall get back to you shortly.');
						$this->mo_openidconnect_show_success_message();
					}
				}
			}
			else if( $_POST['option'] == "mo_openidconnect_resend_otp" ) {
				if( mo_openidconnect_is_curl_installed() == 0 ) {
					return $this->mo_openidconnect_show_curl_error();
				}
				$customer = new Customer();
				$content = json_decode($customer->send_otp_token(), true);
				if(strcasecmp($content['status'], 'SUCCESS') == 0) {
						update_option( 'message', ' A one time passcode is sent to ' . get_option('mo_openidconnect_admin_email') . ' again. Please check if you got the otp and enter it here.');
						$_SESSION['mo_openidconnect_transactionId'] = $content['txId'];
						update_option('mo_openidconnect_registration_status','MO_OTP_DELIVERED_SUCCESS');
						$this->mo_openidconnect_show_success_message();
				}else{
						update_option('message','There was an error in sending email. Please click on Resend OTP to try again.');
						update_option('mo_openidconnect_registration_status','MO_OTP_DELIVERED_FAILURE');
						$this->mo_openidconnect_show_error_message();
				}
			}
			else if( $_POST['option'] == "mo_openidconnect_change_email" ) {
				update_option('mo_openidconnect_registration_status','');
			}
		}

		if( current_user_can( 'manage_options' )) {
			if(isset( $_POST['option'] ) and $_POST['option'] == 'mo_openidconnect_client_skip_feedback' ) {
				deactivate_plugins( __FILE__ );
				update_option( 'message', 'Plugin deactivated successfully' );
				$this->mo_openidconnect_show_success_message();
			} else if( isset( $_POST['mo_openidconnect_client_feedback'] ) and $_POST['mo_openidconnect_client_feedback'] == 'true' ) {
				$user = wp_get_current_user();
				$message = 'Plugin Deactivated:';
				$deactivate_reason         = array_key_exists( 'deactivate_reason_radio', $_POST ) ? $_POST['deactivate_reason_radio'] : false;
				$deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? $_POST['query_feedback'] : false;
				if ( $deactivate_reason ) {
					$message .= $deactivate_reason;
					if ( isset( $deactivate_reason_message ) ) {
						$message .= ':' . $deactivate_reason_message;
					}
					$email = get_option( "mo_openidconnect_admin_email" );
					if ( $email == '' ) {
						$email = $user->user_email;
					}
					$phone = get_option( 'mo_openidconnect_admin_phone' );
					//only reason
					$feedback_reasons = new Customer();
					$submited = json_decode( $feedback_reasons->mo_openidconnect_send_email_alert( $email, $phone, $message ), true );
					deactivate_plugins( __FILE__ );
					update_option( 'message', 'Thank you for the feedback.' );
					$this->mo_openidconnect_show_success_message();
				} else {
					update_option( 'message', 'Please Select one of the reasons ,if your reason is not mentioned please select Other Reasons' );
					$this->mo_openidconnect_error_message();
				}
			}
		}
	}

	function mo_openidconnect_get_current_customer(){
		$customer = new Customer();
		$content = $customer->get_customer_key();
		$customerKey = json_decode( $content, true );
		if( json_last_error() == JSON_ERROR_NONE ) {
			update_option( 'mo_openidconnect_admin_customer_key', $customerKey['id'] );
			update_option( 'mo_openidconnect_admin_api_key', $customerKey['apiKey'] );
			update_option( 'customer_token', $customerKey['token'] );
			update_option('password', '' );
			update_option( 'message', 'Customer retrieved successfully' );
			delete_option('verify_customer');
			delete_option('new_registration');
			$this->mo_openidconnect_show_success_message();
		} else {
			update_option( 'message', 'You already have an account with miniOrange. Please enter a valid password.');
			update_option('verify_customer', 'true');
			delete_option('new_registration');
			$this->mo_openidconnect_show_error_message();

		}
	}

	function create_customer(){
		$customer = new Customer();
		$customerKey = json_decode( $customer->create_customer(), true );
		if( strcasecmp( $customerKey['status'], 'CUSTOMER_USERNAME_ALREADY_EXISTS') == 0 ) {
			$this->mo_openidconnect_get_current_customer();
			delete_option('mo_openidconnect_new_customer');
		} else if( strcasecmp( $customerKey['status'], 'SUCCESS' ) == 0 ) {
			update_option( 'mo_openidconnect_admin_customer_key', $customerKey['id'] );
			update_option( 'mo_openidconnect_admin_api_key', $customerKey['apiKey'] );
			update_option( 'customer_token', $customerKey['token'] );
			update_option( 'password', '');
			update_option( 'message', 'Registered successfully.');
			update_option('mo_openidconnect_registration_status','MO_OAUTH_REGISTRATION_COMPLETE');
			update_option('mo_openidconnect_new_customer',1);
			delete_option('verify_customer');
			delete_option('new_registration');
			$this->mo_openidconnect_show_success_message();
		}
	}

	function mo_openidconnect_show_curl_error() {
		if( mo_openidconnect_is_curl_installed() == 0 ) {
			update_option( 'message', '<a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please enable it to continue.');
			$this->mo_openidconnect_show_error_message();
			return;
		}
	}

	function mo_openidconnect_shortcode_login(){
		$mowidget = new Mo_OpenIDConnect_Widget();
		return $mowidget->mo_openidconnect_login_form( true );
	}

}


	function mo_openidconnect_is_customer_registered() {
		$email 			= get_option('mo_openidconnect_admin_email');
		$phone 			= get_option('mo_openidconnect_admin_phone');
		$customerKey 	= get_option('mo_openidconnect_admin_customer_key');
		if( ! $email || ! $phone || ! $customerKey || ! is_numeric( trim( $customerKey ) ) ) {
			return 0;
		} else {
			return 1;
		}
	}


	function mooauthencrypt($str){
	   $pass = get_option("customer_token");
	   $pass = str_split(str_pad('', strlen($str), $pass, STR_PAD_RIGHT));
	   $stra = str_split($str);
	   foreach($stra as $k=>$v){
		 $tmp = ord($v)+ord($pass[$k]);
		 $stra[$k] = chr( $tmp > 255 ?($tmp-256):$tmp);
	   }
	   return base64_encode(join('', $stra));
	}
	function mooauthdecrypt($str){
	   $str = base64_decode($str);
	   $pass = get_option("customer_token");
	   $pass = str_split(str_pad('', strlen($str), $pass, STR_PAD_RIGHT));
	   $stra = str_split($str);
	   foreach($stra as $k=>$v){
		 $tmp = ord($v)-ord($pass[$k]);
		 $stra[$k] = chr( $tmp < 0 ?($tmp+256):$tmp);
	   }
	   return join('', $stra);
	}


	function mo_openidconnect_is_curl_installed() {
		if  (in_array  ('curl', get_loaded_extensions())) {
			return 1;
		} else {
			return 0;
		}
	}

new mo_openidconnect;