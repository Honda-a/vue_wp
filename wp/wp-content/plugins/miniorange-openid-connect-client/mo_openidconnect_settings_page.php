<?php

include("views/mo_openidconnect_manage_page.php");
include("views/instructions_page.php");

function mo_register() {

	$currenttab = "";
	if(isset($_GET['tab']))
		$currenttab = $_GET['tab'];
	?>
	<?php
		if(mo_openidconnect_is_curl_installed()==0){ ?>
			<p style="color:red;">(Warning: <a href="http://php.net/manual/en/curl.installation.php" target="_blank">PHP CURL extension</a> is not installed or disabled. Please install/enable it before you proceed.)</p>
		<?php
		}
	?>
<div id="mo_oauth_settings">
	<?php
        if ( $currenttab == 'licensing' || ! get_option( 'mo_oauth_show_mo_openidconnect_message' ) ) {
            ?>
            <form name="f" method="post" action="" id="mo_oauth_mo_openidconnect_form">
                <input type="hidden" name="option" value="mo_oauth_mo_openidconnect_message"/>
                <div class="notice notice-info" style="padding-right: 38px;position: relative;">
					<h4>Looking for SSO using OAuth/OpenID Connect? We are providing both protocols in our miniOrange WordPress OAuth Client plugin. <a href="<?php 
						$plug = 'miniorange-login-with-eve-online-google-facebook';
						$action = 'install-plugin';
						$uri = wp_nonce_url(
							add_query_arg(
								array(
									'action' => $action,
									'plugin' => $plug
								),
								admin_url('update.php')
							),
							$action.'_'.$plug
						);
						echo $uri;
						?>" target="_blank">Click here</a> to download the plugin.</h4>
                    <button type="button" class="notice-dismiss" id="mo_oauth_mo_openidconnect"><span class="screen-reader-text">Dismiss this notice.</span>
                    </button>
                </div>
            </form>
            <script>
                jQuery("#mo_oauth_mo_openidconnect").click(function () {
                    jQuery("#mo_oauth_mo_openidconnect_form").submit();
                });
            </script>
            <?php
        }
        ?>
<div id="tab">
	<h2 class="nav-tab-wrapper">
		<a class="nav-tab <?php if($currenttab == '') echo 'nav-tab-active';?>" href="admin.php?page=mo_openidconnect_settings">Configure Apps</a>
		<?php if(get_option('mo_openidconnect_new_customer')!=1 && get_option('mo_openidconnect_eveonline_enable') == 1 ) ?>
		<a class="nav-tab <?php if($currenttab == 'customization') echo 'nav-tab-active';?>" href="admin.php?page=mo_openidconnect_settings&tab=customization">Customizations</a>
		<a class="nav-tab <?php if($currenttab == 'signinsettings') echo 'nav-tab-active';?>" href="admin.php?page=mo_openidconnect_settings&tab=signinsettings">Sign In Settings</a>
		<a class="nav-tab <?php if($currenttab == 'licensing') echo 'nav-tab-active';?>" href="admin.php?page=mo_openidconnect_settings&tab=licensing">Licensing Plans</a>
		<a class="nav-tab <?php if($currenttab == 'faq') echo 'nav-tab-active';?>" href="admin.php?page=mo_openidconnect_settings&tab=faq">FAQ</a>

	</h2>
</div>
<div id="mo_openidconnect_settings">

	<div class="miniorange_container">
		<table style="width:100%;">
		<tr>
		<td style="vertical-align:top;width:65%;" class="mo_openidconnect_content">
		<?php
	if (get_option ( 'verify_customer' ) == 'true') {
		mo_openidconnect_show_verify_password_page();
	} else if (trim ( get_option ( 'mo_openidconnect_admin_email' ) ) != '' && trim ( get_option ( 'mo_openidconnect_admin_api_key' ) ) == '' && get_option ( 'new_registration' ) != 'true') {
		mo_openidconnect_show_verify_password_page();
	} else if(get_option('mo_openidconnect_registration_status') == 'MO_OTP_DELIVERED_SUCCESS' || get_option('mo_openidconnect_registration_status') == 'MO_OTP_VALIDATION_FAILURE' ){
		mo_openidconnect_show_otp_verification();
	} else if (! mo_openidconnect_is_customer_registered()) {
		delete_option ( 'password_mismatch' );
		mo_openidconnect_show_new_registration_page();
	} else {

		if($currenttab == 'customization')
			mo_openidconnect_app_customization();
		else if($currenttab == 'signinsettings')
			mo_openidconnect_sign_in_settings();
		else if($currenttab == 'useranalytics')
			mo_openidconnect_user_analytics();
		else if($currenttab == 'licensing')
			mo_openidconnect_licensing();
		else if($currenttab == 'faq') 
			mo_openidconnect_faq();
		else
			mo_openidconnect_apps_config();
	}
	?>
			</td>
			<?php if($currenttab != 'licensing') { ?>
				<td style="vertical-align:top;padding-left:1%;" class="mo_openidconnect_sidebar">
					<?php echo miniorange_support(); ?>
				</td>
			<?php } ?>
			</tr>
			</table>
		</div>
		<?php
}

function mo_openidconnect_faq()
{?>
<div class="mo_table_layout">
    <object type="text/html" data="https://faq.miniorange.com/kb/oauth-openid-connect/" width="100%" height="600px" > 
    </object>
</div>
<?php
}

function mo_openidconnect_show_new_registration_page() {
	update_option ( 'new_registration', 'true' );
	$current_user = wp_get_current_user();
	?>
			<!--Register with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_openidconnect_register_customer" />
			<div class="mo_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Register with miniOrange</h3>
				</div>
				<div id="panel1">
					<!--<p><b>Register with miniOrange</b></p>-->
					<p>Please enter a valid Email ID that you have access to. You will be able to move forward after verifying an OTP that we will be sending to this email.
					</p>
					<table class="mo_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_openidconnect_admin_email');?>" />
							</td>
						</tr>
						<tr class="hidden">
							<td><b><font color="#FF0000">*</font>Website/Company Name:</b></td>
							<td><input class="mo_table_textbox" type="text" name="company"
							required placeholder="Enter website or company name"
							value="<?php echo $_SERVER['SERVER_NAME']; ?>"/></td>
						</tr>
						<tr  class="hidden">
							<td><b>&nbsp;&nbsp;First Name:</b></td>
							<td><input class="mo_openid_table_textbox" type="text" name="fname"
							placeholder="Enter first name" value="<?php echo $current_user->user_firstname;?>" /></td>
						</tr>
						<tr class="hidden">
							<td><b>&nbsp;&nbsp;Last Name:</b></td>
							<td><input class="mo_openid_table_textbox" type="text" name="lname"
							placeholder="Enter last name" value="<?php echo $current_user->user_lastname;?>" /></td>
						</tr>

						<tr  class="hidden">
							<td><b>&nbsp;&nbsp;Phone number :</b></td>
							 <td><input class="mo_table_textbox" type="text" name="phone" pattern="[\+]?([0-9]{1,4})?\s?([0-9]{7,12})?" id="phone" title="Phone with country code eg. +1xxxxxxxxxx" placeholder="Phone with country code eg. +1xxxxxxxxxx" value="<?php echo get_option('mo_openidconnect_admin_phone');?>" />
							 This is an optional field. We will contact you only if you need support.</td>
							</tr>
						</tr>
						<tr  class="hidden">
							<td></td>
							<td>We will call only if you need support.</td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Password:</b></td>
							<td><input class="mo_table_textbox" required type="password"
								name="password" placeholder="Choose your password (Min. length 8)" /></td>
						</tr>
						<tr>
							<td><b><font color="#FF0000">*</font>Confirm Password:</b></td>
							<td><input class="mo_table_textbox" required type="password"
								name="confirmPassword" placeholder="Confirm your password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><br /><input type="submit" name="submit" value="Save" style="width:100px;"
								class="button button-primary button-large" /></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<script>
			jQuery("#phone").intlTelInput();
		</script>
		<?php
}


function mo_openidconnect_show_verify_password_page() {
	?>
			<!--Verify password with miniOrange-->
		<form name="f" method="post" action="">
			<input type="hidden" name="option" value="mo_openidconnect_verify_customer" />
			<div class="mo_table_layout">
				<div id="toggle1" class="panel_toggle">
					<h3>Login with miniOrange</h3>
				</div>
				<div id="panel1">
					</p>
					<table class="mo_settings_table">
						<tr>
							<td><b><font color="#FF0000">*</font>Email:</b></td>
							<td><input class="mo_table_textbox" type="email" name="email"
								required placeholder="person@example.com"
								value="<?php echo get_option('mo_openidconnect_admin_email');?>" /></td>
						</tr>
						<td><b><font color="#FF0000">*</font>Password:</b></td>
						<td><input class="mo_table_textbox" required type="password"
							name="password" placeholder="Choose your password" /></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td><input type="submit" name="submit"
								class="button button-primary button-large" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a
								target="_blank"
								href="<?php echo get_option('host_name') . "/moas/idp/userforgotpassword"; ?>">Forgot
									your password?</a></td>
						</tr>
					</table>
				</div>
			</div>
		</form>
		<?php
}

function mo_openidconnect_sign_in_settings(){
	?>
	<div class="mo_table_layout">
		<h2>Sign in options</h2>

		<h4>Option 1: Use a Widget<small class="premium_feature"> [STANDARD]</small></h4>
		<ol>
			<li>Go to Appearances > Widgets.</li>
			<li>Select <b>"miniOrange OpenID Connect"</b>. Drag and drop to your favourite location and save.</li>
		</ol>

		<h4>Option 2: Use a Shortcode<small class="premium_feature"> [STANDARD]</small></h4>
		<ul>
			<li>Place shortcode <b>[mo_openidconnect_login]</b> in wordpress pages or posts.</li>
		</ul>
	</div>
	
	<div class="mo_openidconnect_premium_option_text"><span style="color:red;">*</span>This is a premium feature. 
		<a href="admin.php?page=mo_openidconnect_settings&tab=licensing">Click Here</a> to see our full list of Premium Features.</div>
	<div class="mo_table_layout mo_openidconnect_premium_option" id="advanced">

	<h3>Advanced Settings</h3>

	<form id="role_mapping_form" name="f" method="post" action="">

		<input disabled="true" type="hidden" name="option" value="mo_openidconnect_client_advanced_settings">

		<input disabled="true" type="checkbox" name="restrict_to_logged_in_users" value="1" <?php checked( get_option('mo_openidconnect_client_restrict_to_logged_in_users') == 1 );?> ><strong> Restrict site to logged in users</strong> ( Users will be auto redirected to OpenID Connect login if not logged in )

		<p><input disabled="true" type="checkbox" name="popup_login" value=""><strong> Open login window in Popup</strong></p>
		
		<p><input disabled="true" type="checkbox" name="auto_register" id="auto_register" value="" > <strong> Auto register Users </strong>(If unchecked, only existing users will be able to log-in)</p>

		<table class="mo_openidconnect_client_mapping_table" id="mo_openidconnect_client_role_mapping_table" style="width:90%">
			<tr>
				<td><font style="font-size:13px;font-weight:bold;">Custom redirect URL after login </font><br>(Keep blank in case you want users to redirect to page from where SSO originated)
				</td>
				<td><input disabled="true" type="text" name="custom_after_login_url"  placeholder="" style="width:100%;" value=""></td>
			</tr>
			<tr>
				<td><font style="font-size:13px;font-weight:bold;">Custom redirect URL after logout </font>
				</td>
				<td><input disabled="true" type="text" name="custom_after_logout_url"  placeholder="" style="width:100%;" value=""></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td><input disabled="true" type="submit" class="button button-primary button-large" value="Save Settings" /></td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</form>
	<?php
}

function mo_openidconnect_licensing(){

?>
		<div class="mo_openidconnect_table_layout">

		<span style="float:right;margin-top:5px"><input type="button" name="ok_btn" id="ok_btn" class="button button-primary button-large" value="OK, Got It" onclick="window.location.href='admin.php?page=mo_openidconnect_settings'" /></span>
		<h2>Licensing Plans</h2>
		<hr>
		<table class="table mo_table-bordered mo_table-striped">

            <thead>
            <tr style="background-color:#0085ba">
                <th width="20%"><br><br><br>
                    <h3><font color="#FFFFFF">Features \ Plans</font></h3></th>
                <th class="text-center" width="10%"><br><br><br></h3><p class="mo_plan-desc"></p><h3><font color="#FFFFFF">FREE</font><b class="tooltip"><span class="tooltiptext"></span></b><br><span>
                </span></h3></th>
                <th class="text-center" width="10%"><h3><font color="#FFFFFF">Standard<br></font></h3><p class="mo_plan-desc"></p><h3><b class="tooltip"><font color="#FFFFFF">$149</font><span class="tooltiptext">Cost applicable for one instance only.</span></b><br><br><span>

                <input type="button" name="upgrade_btn" class="button button-default button-large" value="Upgrade Now"
                       onclick="getupgradelicensesform('wp_oauth_client_standard_plan')"/>


                </span></h3></th>

                <th class="text-center" width="10%"><h3><font color="#FFFFFF">Premium</font></h3><p></p><p class="mo_plan-desc"></p><h3><b class="tooltip"><font color="#FFFFFF">$349</font><span class="tooltiptext">Cost applicable for one instance only.</span></b><br><br><span>
      <input type="button" name="upgrade_btn" class="button button-default button-large" value="Upgrade Now"
                       onclick="getupgradelicensesform('wp_oauth_client_premium_plan')"/>

            </th>
			<th class="text-center" width="10%"><h3><font color="#FFFFFF">Enterprise</font></h3><p></p><p class="mo_plan-desc"></p><h3><b class="tooltip"><font color="#FFFFFF">$449</font><span class="tooltiptext">Cost applicable for one instance only.</span></b><br><br><span>
      <input type="button" name="upgrade_btn" class="button button-default button-large" value="Upgrade Now"
                       onclick="getupgradelicensesform('wp_oauth_client_enterprise_plan')"/>

            </th></tr>
            </thead>
            <tbody class="mo_align-center mo-fa-icon">
			<tr>
                <td>OpenIDConnect Provider Support</td>
                <td>1</td>
                <td>1</td>
                <td>1</td>
				<td>Unlimited</td>
            </tr>
            <tr>
                <td>Auto Create Users</td>
                <td>Upto 10 Users</td>
                <td>Unlimited</td>
                <td>Unlimited</td>
				<td>Unlimited</td>
            </tr>
			<tr>
            <td>Auto fill OpenID Connect servers configuration</td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
				<td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
            <td>Basic Attribute Mapping (Email, FirstName)</td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
				<td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Login Widget</td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
				<td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Advanced Attribute Mapping (Username, FirstName, LastName, Email, Group Name)</td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Login using the link / shortcode</td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Custom login buttons and CSS</td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
                <td>Custom Redirect URL after login and logout</td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			 <tr>
                <td>Basic Role Mapping (Support for default role for new users)</td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Advanced Role Mapping</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Custom Attribute Mapping</td>
				<td></td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Force authentication / Protect complete site</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>OAuth Support (Login using OAuth Server)</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Multiple Userinfo endpoints support</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Domain specific registration </td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Multi-site Support</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Reverse Proxy Support</td>
                <td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Account Linking </td>
                <td></td>
				<td></td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
                <td>BuddyPress Attribute Mapping</td>
                <td></td>
				<td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
            <tr>
                <td>Dynamic Callback URL</td>
                <td></td>
				<td></td>
				<td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
                <td>Page Restriction</td>
                <td></td>
				<td></td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
                <td>WP hooks for different events</td>
                <td></td>
				<td></td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
			<tr>
                <td>Login Reports / Analytics</td>
                <td></td>
				<td></td>
                <td></td>
                <td><img style="width:10px;height:10px;" src="<?php echo plugin_dir_url( __FILE__ );?>/images/tick.png"></i></td>
            </tr>
        </table>
		<form style="display:none;" id="loginform" action="<?php echo get_option( 'host_name').'/moas/login'; ?>"
		target="_blank" method="post">
		<input type="email" name="username" value="<?php echo get_option('mo_openidconnect_admin_email'); ?>" />
		<input type="text" name="redirectUrl" value="<?php echo get_option( 'host_name').'/moas/viewlicensekeys'; ?>" />
		<input type="text" name="requestOrigin" id="requestOrigin1"  />
		</form>
		<form style="display:none;" id="licenseform" action="<?php echo get_option( 'host_name').'/moas/login'; ?>"
		target="_blank" method="post">
		<input type="email" name="username" value="<?php echo get_option('mo_openidconnect_admin_email'); ?>" />
		<input type="text" name="redirectUrl" value="<?php echo get_option( 'host_name').'/moas/initializepayment'; ?>" />
		<input type="text" name="requestOrigin" id="requestOrigin2"  />
		</form>
		<script>


			function getupgradelicensesform(planType){
				jQuery('#requestOrigin2').val(planType);
				jQuery('#licenseform').submit();
			}
			jQuery('.mo_openidconnect_content').css("width","100%");
		</script>
		<br>
		<h3>* Steps to upgrade to premium plugin -</h3>
		<p>1. You will be redirected to miniOrange Login Console. Enter your password with which you created an account with us. After that you will be redirected to payment page.</p>
		<p>2. Enter you card details and complete the payment. On successful payment completion, you will see the link to download the premium plugin.</p>
		</div>

	<?php
}
function mo_openidconnect_app_customization(){
	$custom_css = get_option('mo_openidconnect_icon_configure_css');
	function format_custom_css_value( $textarea ){ 
		$lines = explode(";", $textarea);
		for($i=0;$i<count($lines);$i++)
		{if($i<count($lines)-1)
			echo $lines[$i].";\r\n";
		
		else if($i==count($lines)-1)
			echo $lines[$i]."\r\n";
		}
	}

	?>
	<div class="mo_openidconnect_premium_option_text"><span style="color:red;">*</span>This is a standard feature. 
		<a href="admin.php?page=mo_openidconnect_settings&tab=licensing">Click Here</a> to see our full list of Standard Features.</div>
	<div class="mo_table_layout mo_openidconnect_premium_option">
	<form disabled id="form-common" name="form-common" method="post" action="admin.php?page=mo_openidconnect_settings&tab=customization">
		<input type="hidden" name="option" value="mo_openidconnect_app_customization" />
		<h2>Customize Icons</h2>
		<table class="mo_settings_table">
			<tr>
				<td><strong>Icon Width:</strong></td>
				<td><input disabled type="text" value=""> px</td>
			</tr>
			<tr>
				<td><strong>Icon Height:</strong></td>
				<td><input disabled type="text" value=""> px</td>
			</tr>
			<tr>
				<td><strong>Icon Margins:</strong></td>
				<td><input disabled type="text" value=""> px</td>
			</tr>
			<tr>
				<td><strong>Custom CSS:</strong></td>
				<td><textarea disabled type="text" style="resize: vertical; width:400px; height:180px;  margin:5% auto;" rows="6" ></textarea><br/><b>Example CSS:</b> 
<pre>.openidconnectloginbutton { 
	 width:100%;
	 height:50px;
	 padding-top:15px;
	 padding-bottom:15px;
	 margin-bottom:-1px;
	 border-radius:4px;
	 background: #7272dc;
	 text-align:center;
	 font-size:16px;
	 color:#fff;
	 margin-bottom:5px;
 } 
 .custom_logo { 
	 padding-top:-1px;
	 padding-right:15px;
	 padding-left:15px;
	 padding-top:15px;
	 background: #7272dc;
	 color:#fff;
 }</pre>
</td>
			</tr>
			<tr>
				<td><strong>Custom Logout button text:</strong></td>
				<td><input disabled type="text" style="resize: vertical; width:200px; height:30px;  margin:5% auto;"  placeholder ="Howdy ,##user##"value=""> <b>##user##</b> is replaced by Username</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><br><input disabled type="submit" name="" value="Save Settings"
					class="button button-primary button-large" /></td>
			</tr>
		</table>
	</form>
	</div>
	<?php
	}


function mo_openidconnect_apps_config() {
	?>
	
	<div class="mo_table_layout">
	<?php

		if(isset($_GET['action']) && $_GET['action']=='delete'){
			if(isset($_GET['app']) && check_admin_referer('mo_openidconnect_delete_'.$_GET['app']) )
				mo_openidconnect_client_delete_app($_GET['app']);
		}

		if(isset($_GET['action']) && $_GET['action']=='add'){
			add_app();
		}
		else if(isset($_GET['action']) && $_GET['action']=='update'){
			if(isset($_GET['app']))
				update_app($_GET['app']);
		}
		else if(get_option('mo_openidconnect_apps_list'))
		{
			$appslist = get_option('mo_openidconnect_apps_list');
			if(sizeof($appslist)>0)
				echo "<br><a href='#'><button disabled style='float:right'>Add Application</button></a>";
			else
				echo "<br><a href='admin.php?page=mo_openidconnect_settings&action=add'><button style='float:right'>Add Application</button></a>";
			echo "<h3>Applications List</h3>";
			if(is_array($appslist) && sizeof($appslist)>0)
				echo "<p style='color:#a94442;background-color:#f2dede;border-color:#ebccd1;border-radius:5px;padding:12px'>You can only add 1 application with free version. Upgrade to <a href='admin.php?page=mo_openidconnect_settings&tab=licensing'><b>enterprise</b></a> to add more.</p>";
			echo "<table class='tableborder'>";
			echo "<tr><th><b>Name</b></th><th>Action</th></tr>";
			foreach($appslist as $key => $app){
				echo "<tr><td>".$key;
				// if(isset($app['apptype']) && $app['apptype']=='openidconnect') echo " <small>(OpenId Connect)<small>";
				
				$delete_url = wp_nonce_url( "admin.php?page=mo_openidconnect_settings&action=delete&app=".$key , 'mo_openidconnect_delete_'.$key );
				echo "</td><td><a href='admin.php?page=mo_openidconnect_settings&action=update&app=".$key."'>Update</a> | <a href='admin.php?page=mo_openidconnect_settings&action=update&app=".$key."#attrmapping'>Attribute Mapping</a> | <a href='admin.php?page=mo_openidconnect_settings&action=update&app=".$key."#rolemapping'>Role Mapping</a> | <a href='admin.php?page=mo_openidconnect_settings&tab=signinsettings&app=".$key."#advanced'>Advanced</a> | <a href='admin.php?page=mo_openidconnect_settings&action=update&app=".$key."#howtoconfigure'>How to Configure?</a> | <a href='".$delete_url."' onclick =\"return confirm('Are you sure you want to delete this item?');\">Delete</a></td></tr>";
			}
			echo "</table>";
			echo "<br><br>";

		} else {
			add_app();
		 } ?>
		</div>

<?php
}

function add_app(){
	?>

		<script>
			function selectapp() {
				var appname = document.getElementById("mo_openidconnect_app").value;
				document.getElementById("instructions").innerHTML  = "";
				if(appname=="amazon"){
				document.getElementById("instructions").innerHTML  = '<?php mo_openidconnect_client_instructions("amazon", false);?>';
				} else if(appname=="salesforce"){
					document.getElementById("instructions").innerHTML  = '<?php mo_openidconnect_client_instructions("salesforce", false);?>';
				} else if(appname=="microsoft"){
					document.getElementById("instructions").innerHTML  = '<?php mo_openidconnect_client_instructions("microsoft", false);?>';
				} else if(appname=="google"){
					document.getElementById("instructions").innerHTML  = '<?php mo_openidconnect_client_instructions("google", false);?>';
				} else {
				document.getElementById("instructions").innerHTML  = '<?php mo_openidconnect_client_instructions("other", false);?>';
				}
                
                if( appname ) {
					jQuery("#mo_openidconnect_display_app_name_div").show();
					jQuery("#mo_openidconnect_custom_app_name_div").show();
					jQuery("#mo_openidconnect_authorizeurl_div").show();
					jQuery("#mo_openidconnect_idtokenurl_div").show();
					jQuery("#mo_openidconnect_email_attr_div").show();
					jQuery("#mo_openidconnect_name_attr_div").show();
					jQuery("#mo_openidconnect_custom_app_name").attr('required','true');
					jQuery("#mo_openidconnect_email_attr").attr('required','true');
					jQuery("#mo_openidconnect_name_attr").attr('required','true');
					jQuery("#callbackurl").val("<?php echo site_url();?>");

					//---------------------------------------------
					if( appname !="openidconnect" ) {
						if(appname=="amazon"){
							var authorizeurl = 'https://www.amazon.com/ap/oa';
							var accesstokenurl = 'https://api.amazon.com/auth/o2/token';
						}else if(appname=="salesforce"){
							var authorizeurl = "https://login.salesforce.com/services/oauth2/authorize";
							var accesstokenurl = "https://login.salesforce.com/services/oauth2/token";
						}else if(appname=="paypal"){
							var authorizeurl = "https://www.paypal.com/signin/authorize";
							var accesstokenurl = "https://api.paypal.com/v1/oauth2/token";
						}else if(appname=="microsoft"){
							var authorizeurl = "https://login.microsoftonline.com/<Tenant-ID>/oauth2/v2.0/authorize";
							var accesstokenurl = "https://login.microsoftonline.com/<Tenant-ID>/oauth2/v2.0/token";
						}else if(appname=="yahoo"){
							var authorizeurl = "https://api.login.yahoo.com/oauth2/request_auth";
							var accesstokenurl = "https://api.login.yahoo.com/oauth2/get_token";
						} else if(appname=="google"){
							var authorizeurl = "https://accounts.google.com/o/oauth2/auth";
							var accesstokenurl = "https://www.googleapis.com/oauth2/v3/token";
						} else if(appname=="onelogin"){
							var authorizeurl = "https://<site-url>.onelogin.com/oidc/auth";
							var accesstokenurl = "https://<site-url>.onelogin.com/oidc/token";
						} else if(appname=="okta"){
							var authorizeurl = "https://{yourOktaDomain}.com/oauth2/default/v1/authorize";
							var accesstokenurl = "https://{yourOktaDomain}.com/oauth2/default/v1/token";
						} else if(appname=="adfs"){
							var authorizeurl = "https://fs.fabidentity.com/adfs/oauth2/authorize/";
							var accesstokenurl = "https://fs.fabidentity.com/adfs/oauth2/token/";
						} else if(appname=="gigya"){
							var authorizeurl = "https://fidm.[Data_Center_ID].gigya.com/oidc/op/v1.0/[The-OP-API-Key]/authorize";
							var accesstokenurl = "https://fidm.[Data_Center_ID].gigya.com/oidc/op/v1.0/[The-OP-API-Key]/token";
						}  
						document.getElementById('mo_openidconnect_scope').value="openid";
						if(appname=="amazon")
							document.getElementById('mo_openidconnect_scope').value="profile";
						document.getElementById('mo_openidconnect_authorizeurl').value=authorizeurl;
						document.getElementById('mo_openidconnect_idtokenurl').value=accesstokenurl;
						//document.getElementById('mo_openidconnect_idtokenurl').value=resourceownerdetailsurl;
					} else {
						document.getElementById('mo_openidconnect_scope').value="openid";
						document.getElementById('mo_openidconnect_authorizeurl').value="";
						document.getElementById('mo_openidconnect_idtokenurl').value="";	
					}
					jQuery("#mo_openidconnect_authorizeurl").attr('required','true');
					jQuery("#mo_openidconnect_idtokenurl").attr('required','true');
				}

			}

		</script>
		<div id="toggle2" class="panel_toggle">
			<h3>Add Application</h3>
		</div>
		<form id="form-common" name="form-common" method="post" action="admin.php?page=mo_openidconnect_settings">
		<input type="hidden" name="option" value="mo_openidconnect_add_app" />
		<table class="mo_settings_table">
			<tr>
			<td><strong><font color="#FF0000">*</font>Select Application:</strong></td>
			<td>
				<select class="mo_table_textbox" required="true" name="mo_openidconnect_app_name" id="mo_openidconnect_app" onchange="selectapp()">
				  <option value="">Select Application</option>
				  <option value="amazon">Amazon</option>
				  <option value="salesforce">SalesForce</option>
				  <option value="paypal">PayPal</option>
				  <option value="microsoft">Microsoft Azure</option>
				  <option value="yahoo">Yahoo</option>
				  <option value="google">Google</option>
				  <option value="onelogin">OneLogin</option>
				  <option value="okta">Okta</option>
				  <option value="adfs">ADFS</option>
				  <option value="gigya">Gigya</option>
				  <option value="openidconnect">Custom OpenId Connect</option>
				</select>
			</td>
			</tr>
			<tr><td><strong>Redirect / Callback URL</strong></td>
			<td><input class="mo_table_textbox" id="callbackurl"  type="text" readonly="true" value='<?php echo site_url();?>'></td>
			</tr>
			<tr>
			<tr  style="display:none" id="mo_openidconnect_custom_app_name_div">
				<td><strong><font color="#FF0000">*</font>Custom App Name:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_custom_app_name" name="mo_openidconnect_custom_app_name" value=""></td>
			</tr>
			<tr style="display:none" id="mo_openidconnect_display_app_name_div">
				<td><strong>Display App Name:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_display_app_name" name="mo_openidconnect_display_app_name" value=""></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client ID:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" name="mo_openidconnect_client_id" value=""></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client Secret:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text"  name="mo_openidconnect_client_secret" value=""></td>
			</tr>
			<tr>
				<td><strong>Scope:</strong></td>
				<td><input class="mo_table_textbox" type="text" name="mo_openidconnect_scope" id="mo_openidconnect_scope" value="openid"></td>
			</tr>
			<tr style="display:none" id="mo_openidconnect_authorizeurl_div">
				<td><strong><font color="#FF0000">*</font>Authorize Endpoint:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_authorizeurl" name="mo_openidconnect_authorizeurl" value=""></td>
			</tr>
			<tr style="display:none" id="mo_openidconnect_idtokenurl_div">
				<td><strong><font color="#FF0000">*</font>ID Token Endpoint:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_idtokenurl" name="mo_openidconnect_idtokenurl" value=""></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" name="submit" value="Save settings"
					class="button button-primary button-large" /></td>
			</tr>
			</table>
		</form>

		<div id="instructions">

		</div>

		<?php
}


function mo_openidconnect_client_delete_app($appname){
	$appslist = get_option('mo_openidconnect_apps_list');
	foreach($appslist as $key => $app){
		if($appname == $key){
			unset($appslist[$key]);
		}
	}
	update_option('mo_openidconnect_apps_list', $appslist);
}

	function miniorange_support(){
?>
	<div class="mo_support_layout">
		<div>
			<h3>Contact Us</h3>
			<p>Need any help? Couldn't find an answer in <a href="<?php echo add_query_arg( array('tab' => 'faq'), $_SERVER['REQUEST_URI'] ); ?>">FAQ</a>?<br>Just send us a query so we can help you.</p>
			<form method="post" action="">
				<input type="hidden" name="option" value="mo_openidconnect_contact_us_query_option" />
				<table class="mo_settings_table">
					<tr>
						<td><b><font color="#FF0000">*</font>Email:</b></td>
						<td><input type="email" class="mo_table_textbox" required name="mo_openidconnect_contact_us_email" value="<?php echo get_option("mo_openidconnect_admin_email"); ?>"></td>
					</tr>
					<tr>
						<td><b>Phone:</b></td>
						<td><input type="tel" id="contact_us_phone" pattern="[\+]\d{11,14}|[\+]\d{1,4}[\s]\d{9,10}" class="mo_table_textbox" name="mo_openidconnect_contact_us_phone" value="<?php if( get_option( 'mo_openidconnect_admin_phone' ) != 'false' ) echo get_option( 'mo_openidconnect_admin_phone' ); ?>"></td>
					</tr>
					<tr>
						<td><b><font color="#FF0000">*</font>Query:</b></td>
						<td><textarea class="mo_table_textbox" onkeypress="mo_openidconnect_valid_query(this)" onkeyup="mo_openidconnect_valid_query(this)" onblur="mo_openidconnect_valid_query(this)" required name="mo_openidconnect_contact_us_query" rows="4" style="resize: vertical;"></textarea></td>
					</tr>
				</table>
				<div style="text-align:center;">
					<input type="submit" name="submit" style="margin:15px; width:100px;" class="button button-primary button-large" />
				</div>
				<p>If you want custom features in the plugin, just drop an email at <a href="mailto:info@miniorange.com">info@miniorange.com</a>.</p>
			</form>
		</div>
	</div>
	<script>
		jQuery("#contact_us_phone").intlTelInput();
		function mo_openidconnect_valid_query(f) {
			!(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
					/[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
		}
	</script>
<?php
}

function mo_openid_connect_jkhuiysuayhbw($ejhi, $nabnbj)
{
	$option = 0; $flag = false;	
	if(!empty(get_option( 'mo_openid_connect_authorizations' )))
	   	$option = get_option( 'mo_openid_connect_authorizations' ); 
	if(mo_openid_connect_hjsguh_kiishuyauh878gs($ejhi, $nabnbj));								
		++$option;							
	update_option( 'mo_openid_connect_authorizations', $option);
	if(base64_decode('JG9wdGlvbiA+PSAy'))
	{
		$mo_openid_connect_set_val = base64_decode('JG9wdGlvbiA+PSAxMA==');
	    update_option($mo_openid_connect_set_val, true);
	}
}

function mo_openid_connect_jhuyn_jgsukaj($temp_var, $ntemp)
{
	mo_openid_connect_jkhuiysuayhbw($temp_var, $ntemp);
}

function mo_openidconnect_show_otp_verification(){
	?>
		<!-- Enter otp -->
		<form name="f" method="post" id="otp_form" action="">
			<input type="hidden" name="option" value="mo_openidconnect_validate_otp" />
				<div class="mo_table_layout">
					<div id="panel5">
						<table class="mo_settings_table">
							<h3>Verify Your Email</h3>
							<tr>
								<td><b><font color="#FF0000">*</font>Enter OTP:</b></td>
								<td><input class="mo_table_textbox" autofocus="true" type="text" name="mo_openidconnect_otp_token" required placeholder="Enter OTP" style="width:61%;" pattern="[0-9]{6,8}"/>
								 &nbsp;&nbsp;<a style="cursor:pointer;" onclick="document.getElementById('mo_openidconnect_resend_otp_form').submit();">Resend OTP</a></td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><br /><input type="submit" name="submit" value="Validate OTP" class="button button-primary button-large" />
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<input type="button" name="back-button" id="mo_openidconnect_back_button" onclick="document.getElementById('mo_openidconnect_change_email_form').submit();" value="Back" class="button button-primary button-large" />
								</td>
							</tr>
						</table>
					</div>
				</div>
		</form>
		<form name="f" id="mo_openidconnect_resend_otp_form" method="post" action="">
			<input type="hidden" name="option" value="mo_openidconnect_resend_otp"/>
		</form>
		<form id="mo_openidconnect_change_email_form" method="post" action="">
			<input type="hidden" name="option" value="mo_openidconnect_change_email" />
		</form>
<?php
}
?>