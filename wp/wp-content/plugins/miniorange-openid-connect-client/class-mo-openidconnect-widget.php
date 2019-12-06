<?php

class Mo_OpenIDConnect_Widget extends WP_Widget {

	public function __construct() {
		update_option( 'host_name', 'https://auth.miniorange.com' );
		add_action( 'init', array( $this, 'mo_openidconnect_start_session' ) );
		add_action( 'wp_logout', array( $this, 'mo_openidconnect_end_session' ) );
		parent::__construct( 'mo_openidconnect_widget', 'miniOrange OpenID Connect Login', array( 'description' => __( 'Login to Apps with OpenIDConnect', 'flw' ), ) );
	 }

	function mo_openidconnect_start_session() {
		if( ! session_id() ) {
			session_start();
		}
		if(isset($_REQUEST['option']) and $_REQUEST['option'] == 'testattrmappingconfig'){
			$mo_openidconnect_app_name = $_REQUEST['app'];
			wp_redirect(site_url().'?option=oauthredirect&app_name='. urlencode($mo_openidconnect_app_name)."&test=true");
			exit();
		}

	}

	function mo_openidconnect_end_session() {
		if( ! session_id() )
		{ 	session_start();
        }
		session_destroy();
	}

	public function widget( $args, $instance ) {
		extract( $args );

		echo $args['before_widget'];
		if ( ! empty( $wid_title ) ) {
			echo $args['before_title'] . $wid_title . $args['after_title'];
		}
		$this->mo_openidconnect_login_form();
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		return $instance;
	}

	public function mo_openidconnect_login_form( $check_if_shortcode=false ) {

		global $post;
		$temp = '';
		$this->error_message();
		
		$spacebetweenicons = get_option('mo_openidconnect_login_icon_space');
		$customWidth = get_option('mo_openidconnect_login_icon_custom_width');
		$customHeight = get_option('mo_openidconnect_login_icon_custom_height');
		$customSize = get_option('mo_openidconnect_login_icon_custom_size');
		$customBackground = get_option('mo_openidconnect_login_icon_custom_color');
		$customBoundary = get_option('mo_openidconnect_login_icon_custom_boundary');

		$appslist = get_option('mo_openidconnect_apps_list');
		if( $appslist && sizeof( $appslist ) > 0 )
			$appsConfigured = true;

		if( ! is_user_logged_in() ) {
			if( $appsConfigured ) {
				$this->mo_openidconnect_load_login_script();

				if( is_array( $appslist ) ) {
					foreach($appslist as $key=>$app) {
						$appgroup = get_option("mo_openidconnect_app_name_".$key);
						$imageurl = "";
						$bcolor = 'btn-primary';
						$logo_class = 'fa fa-lock';

						//set display name on login button
						$appclass = "openidconnect_app_".str_replace(" ","-",$key);

						if (array_key_exists('displayappname', $app) && !empty($app['displayappname']) ) {
							$disp_name = $app['displayappname'];
						} else {
							$disp_name = ucwords($key);
						}
				
						if( empty( $custom_css ) ) { 
								$temp.='<a href="javascript:void(0)" onclick="moOpenIDConnectLoginNew(\''.$key.'\');" style="color:white; width:'.$customWidth.'px !important;padding-top:'.$customHeight.'px !important;padding-bottom:'.$customHeight.'px !important;margin-bottom:'.$spacebetweenicons.'px !important;border-radius:'.$customBoundary.'px !important;" class="openidconnectloginbutton btn btn-social '.$bcolor.'"> <i style="padding-top:'.$customHeight.'-6 px !important; width:15%" class="'.$logo_class.'"></i> '.$disp_name.' </a>';
								
						} else {
								$temp.='<a href="javascript:void(0)" onclick="moOpenIDConnectLoginNew(\''.$key.'\');" class="openidconnectloginbutton btn btn-social"> <i class="'.$logo_class.' custom_logo"></i> '.$disp_name.' </a>';
								$temp.='<style>'.$custom_css.'</style>';
							
						}
						$appgroup = " ";
					}
	            }

			} else {
				$temp.='No apps configured.';
			}
		} else {
			$current_user = wp_get_current_user();
			$custom_name = 'Howdy , ##user##';
			if(get_option('mo_openidconnect_custom_logout_text'))
			$custom_name = get_option('mo_openidconnect_custom_logout_text');
			$custom_name=str_replace("##user##", $current_user->display_name,$custom_name);
			$link_with_username = __($custom_name, 'flw');
			 
			$temp.=$link_with_username.'|<a href="'.wp_logout_url( site_url() ).';" title="'._e('','flw').';">Logout</a>';
			
		}
		if( ! $check_if_shortcode )
			echo $temp;
		else
			return $temp;
	}

	private function mo_openidconnect_load_login_script() {
	?>
	<script type="text/javascript">

		function HandlePopupResult(result) {
			window.location.href = result;
		}

		function moOpenIDConnectLoginNew(app_name) {
		    var myWindow = window.open('<?php echo site_url() ?>' + '/?option=oauthredirect&app_name=' + app_name, '', 'width=500,height=500');

		}
	</script>
	<?php
	}



	public function error_message() {
		if( isset( $_SESSION['msg'] ) and $_SESSION['msg'] ) {
			echo '<div class="' . $_SESSION['msg_class'] . '">' . $_SESSION['msg'] . '</div>';
			unset( $_SESSION['msg'] );
			unset( $_SESSION['msg_class'] );
		}
	}


}
	function mo_openidconnect_login_validate(){

		/* Handle Eve Online old flow */
		if( isset( $_REQUEST['option'] ) and strpos( $_REQUEST['option'], 'oauthredirect' ) !== false ) {
			$appname = $_REQUEST['app_name'];
			$appslist = get_option('mo_openidconnect_apps_list');

			if(isset($_REQUEST['test']))
				setcookie("mo_openidconnect_test", true);
			else
				setcookie("mo_openidconnect_test", false);

			foreach($appslist as $key => $app){
				if($appname==$key){
					$state = base64_encode($appname);
					$authorizationUrl = $app['authorizeurl'];
					if(strpos($authorizationUrl, '?' ) !== false)
						$authorizationUrl = $authorizationUrl."&client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code&state=".$state;
					else 
						$authorizationUrl = $authorizationUrl."?client_id=".$app['clientid']."&scope=".$app['scope']."&redirect_uri=".$app['redirecturi']."&response_type=code&state=".$state;

					if(session_id() == '' || !isset($_SESSION))
						session_start();
					$_SESSION['oauth2state'] = $state;
					$_SESSION['appname'] = $appname;
					header('Location: ' . $authorizationUrl);
					exit;
				}
			}
		}

		// else if(strpos($_SERVER['REQUEST_URI'], "/oauthcallback") !== false) {
		else if( isset( $_REQUEST['code']) && isset($_REQUEST['state'] ) ) {

			if(session_id() == '' || !isset($_SESSION))
				session_start();

			if (!isset($_GET['code'])){
				if(isset($_GET['error_description']))
					exit($_GET['error_description']);
				else if(isset($_GET['error']))
					exit($_GET['error']);
				exit('Invalid response');
			} else {

				try {

					$currentappname = "";

						if (isset($_SESSION['appname']) && !empty($_SESSION['appname']))
							$currentappname = $_SESSION['appname'];
						else if (isset($_GET['state']) && !empty($_GET['state'])){
							// Handling issues for wpengine
							$currentappname = base64_decode($_GET['state']);
						}

						if (empty($currentappname)) {
							exit('No request found for this application.');
						}

						$appslist = get_option('mo_openidconnect_apps_list');
						$username_attr = $email_attr = $firstname_attr = $lastname_attr = $groupname_attr = $display_attr_key = "";
						$currentapp = false;
						foreach($appslist as $key => $app){
							if($key == $currentappname){
								$currentapp = $app;
								if(isset($app['username_attr'])){
									$username_attr = $app['username_attr'];
								}
								if(isset($app['email_attr'])){
									$email_attr = $app['email_attr'];
								}
								if(isset($app['firstname_attr'])){
									$firstname_attr = $app['firstname_attr'];
								}
								if(isset($app['lastname_attr'])){
									$lastname_attr = $app['lastname_attr'];
								}
								
								if(isset($app['display_attr'])){
									$display_attr_key = $app['display_attr'];
								}
								
								if(get_option('mo_openidconnect_client_'.$currentappname.'_mapping_groupname_attribute')){
									$groupname_attr_key = get_option('mo_openidconnect_client_'.$currentappname.'_mapping_groupname_attribute');
								}
							}
						}

						if (!$currentapp)
							exit('Application not configured.');

						$groupResourceDetails = false;
						$mo_openidconnect_handler = new Mo_OpenidConnect_Handler();
						if(isset($currentapp['apptype']) && $currentapp['apptype'] != "" ) {
							// OpenId connect
							$tokenResponse = $mo_openidconnect_handler->getIdToken($currentapp['idtokenurl'], 'authorization_code',
								$currentapp['clientid'], $currentapp['clientsecret'], $_GET['code'], $currentapp['redirecturi']);

							$idToken = $tokenResponse["id_token"];
								
							if(!$idToken)
								exit('Invalid token received.');
							$resourceOwner = $mo_openidconnect_handler->getResourceOwnerFromIdToken($idToken);
						 } 
					
						$email = "";
						if($currentappname != ""){ //20-02-2018

							//TEST Configuration
							if(isset($_COOKIE['mo_openidconnect_test']) && $_COOKIE['mo_openidconnect_test']){
								echo '<style>table{border-collapse: collapse;}table, td, th {border: 1px solid black;padding:4px}</style>';
								echo "<h2>Test Configuration</h2><table><tr><th>Attribute Name</th><th>Attribute Value</th></tr>";
								test_attr_mapping_config("", $resourceOwner, $groupResourceDetails);
								echo "</table>";
								exit();
							}

							$display_attr = "";
							if(!empty($username_attr))
								$username = getnestedattribute($resourceOwner, $username_attr);
							if(!empty($email_attr))
								$email = getnestedattribute($resourceOwner, $email_attr); //$resourceOwner[$email_attr];
							if(!empty($firstname_attr))
								$firstname_attr = getnestedattribute($resourceOwner, $firstname_attr); //$resourceOwner[$name_attr];
							if(!empty($lastname_attr))
								$lastname_attr = getnestedattribute($resourceOwner, $lastname_attr);
							if(!empty($groupname_attr_key)) {
								if (isset($groupdetailsurl) && !empty($groupdetailsurl) && isset($groupResourceDetails)  && $groupResourceDetails) {
									$groupname_attr = getnestedattribute($groupResourceDetails, $groupname_attr_key);
								} else
									$groupname_attr = getnestedattribute($resourceOwner, $groupname_attr_key);
							}
							
						}

						if(empty($email)){
							exit('Email address not received. Check your <b>Attribute Mapping</b> configuration.');
						}

						$user = get_user_by("login",$email);
						if(!$user)
							$user = get_user_by( 'email', $email);

						if($user){
							$user_id = $user->ID;
						} else {
							if( get_option('mo_openid_connect_flag') != true ) {
								mo_openid_connect_jhuyn_jgsukaj($email, $name);
							} else {
									wp_die( base64_decode( 'PGRpdiBzdHlsZT0ndGV4dC1hbGlnbjpjZW50ZXI7Jz48Yj5Vc2VyIEFjY291bnQgZG9lcyBub3QgZXhpc3QuPC9iPjwvZGl2Pjxicj48c21hbGw+VGhpcyB2ZXJzaW9uIHN1cHBvcnRzIEF1dG8gQ3JlYXRlIFVzZXIgZmVhdHVyZSB1cHRvIDEwIFVzZXJzLiBQbGVhc2UgdXBncmFkZSB0byB0aGUgaGlnaGVyIHZlcnNpb24gb2YgdGhlIHBsdWdpbiB0byBlbmFibGUgYXV0byBjcmVhdGUgdXNlciBmb3IgdW5saW1pdGVkIHVzZXJzIG9yIGFkZCB1c2VyIG1hbnVhbGx5Ljwvc21hbGw+' ) );
							} 														
						}

						if($user_id){

							wp_set_current_user($user_id);
							wp_set_auth_cookie($user_id);
							$user  = get_user_by( 'ID',$user_id );
							do_action( 'wp_login', $user->user_login, $user );
							$relaystate = home_url();
                            echo '<script>window.opener.HandlePopupResult("'.$relaystate.'");window.close();</script>';
							exit;
						}


				} catch (Exception $e) {

				exit($e->getMessage());

				}

			}//end else

		} 
		
		/* End of old flow */
	}

	function mo_openid_connect_hjsguh_kiishuyauh878gs($email, $name)
	{
		$random_password = wp_generate_password( 10, false );
		if(is_email($email))
			$user_id = wp_create_user( $email, $random_password, $email );
		else
			$user_id = wp_create_user( $email, $random_password);					
		$user = get_user_by( 'login', $email);						
		wp_update_user( array( 'ID' => $user_id, 'first_name' => $name ) );
	}

	function test_attr_mapping_config($nestedprefix, $resourceOwnerDetails, $groupResourceDetails){
		test_attr_mapping_config_for_resource($nestedprefix, $resourceOwnerDetails);
		
		if($groupResourceDetails) {
			echo "</table>";
			echo '<h4>Group Info Result</h4><table><tr><th>Attribute Name</th><th>Attribute Value</th></tr>';
			test_attr_mapping_config_for_resource($nestedprefix, $groupResourceDetails);
		}
	}
	
	function test_attr_mapping_config_for_resource($nestedprefix, $resourceOwnerDetails){
		foreach($resourceOwnerDetails as $key => $resource){
			if(is_array($resource) || is_object($resource)){
				if(!empty($nestedprefix))
					$nestedprefix .= ".";
				test_attr_mapping_config_for_resource($nestedprefix.$key,$resource);
			} else {
				echo "<tr><td>";
				if(!empty($nestedprefix))
					echo $nestedprefix.".";
				echo $key."</td><td>".$resource."</td></tr>";
			}
		}
	}

	function getnestedattribute($resource, $key){
		if(empty($key))
			return "";

		$keys = explode(".",$key);
		if(sizeof($keys)>1){
			$current_key = $keys[0];
			if(isset($resource[$current_key]))
				return getnestedattribute($resource[$current_key], str_replace($current_key.".","",$key));
		} else {
			$current_key = $keys[0];
			if(isset($resource[$current_key])) {
				if(is_array($resource[$current_key]))
					return $resource[$current_key][0];
				else
					return $resource[$current_key];
			}
		}
		return "";
	}

	function register_mo_openidconnect_widget() {
		register_widget('mo_openidconnect_widget');
	}

	add_action('widgets_init', 'register_mo_openidconnect_widget');
	add_action( 'init', 'mo_openidconnect_login_validate' );
?>