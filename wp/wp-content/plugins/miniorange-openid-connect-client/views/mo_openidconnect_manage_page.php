<?php 

function update_app($appname){
	
	$appslist = get_option('mo_openidconnect_apps_list');
	foreach($appslist as $key => $app){
		if($appname == $key){
			$currentappname = $appname;
			$currentapp = $app;
			break;
		}
	}
	
	if(!$currentapp)
		return;
	
	
		
	?>
		
		<div id="toggle2" class="panel_toggle">
			<h3>Update Application : <?php echo $currentappname;?></h3>
		</div>
		<form id="form-common" name="form-common" method="post" action="admin.php?page=mo_openidconnect_settings">
		<input type="hidden" name="option" value="mo_openidconnect_add_app" /> 
		<table class="mo_settings_table">
			<tr>
			<td><strong><font color="#FF0000">*</font>Application:</strong></td>
			<td>
				<input class="mo_table_textbox" required="" type="hidden" name="mo_openidconnect_app_name" value="<?php echo $currentappname;?>">
				<input class="mo_table_textbox" required="" type="hidden" name="mo_openidconnect_custom_app_name" value="<?php echo $currentappname;?>">
				<?php echo $currentappname;?><br><br>
			</td>
			</tr>
			<tr>
				<td><strong>Display App Name:</strong></td>
				<td><input class="mo_table_textbox" type="text" name="mo_openidconnect_display_app_name" value="<?php echo isset($currentapp['displayappname']) ? $currentapp['displayappname'] : '';?>"></td>
			</tr>
			<tr><td><strong>Redirect / Callback URL</strong></td>
			<td><input class="mo_table_textbox"  type="text" readonly="true" value='<?php echo $currentapp['redirecturi']; ?>'></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client ID:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" name="mo_openidconnect_client_id" value="<?php echo $currentapp['clientid'];?>"></td>
			</tr>
			<tr>
				<td><strong><font color="#FF0000">*</font>Client Secret:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" name="mo_openidconnect_client_secret" value="<?php echo $currentapp['clientsecret'];?>"></td>
			</tr>
			<tr>
				<td><strong>Scope:</strong></td>
				<td><input class="mo_table_textbox" type="text" name="mo_openidconnect_scope" value="<?php echo $currentapp['scope'];?>"></td>
			</tr>
			<tr  id="mo_openidconnect_authorizeurl_div">
				<td><strong><font color="#FF0000">*</font>Authorize Endpoint:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_authorizeurl" name="mo_openidconnect_authorizeurl" value="<?php echo $currentapp['authorizeurl'];?>"></td>
			</tr>
			<tr id="mo_openidconnect_idtokenurl_div">
				<td><strong><font color="#FF0000">*</font>ID Token Endpoint:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_idtokenurl" name="mo_openidconnect_idtokenurl" value="<?php echo $currentapp['idtokenurl'];?>"></td>
			</tr>
			<tr id="mo_openidconnect_resourceownerdetailsurl_div">
				<td><strong>Get User Info Endpoint <small>(Optional)</small>:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_resourceownerdetailsurl" name="mo_openidconnect_resourceownerdetailsurl" value="<?php if(isset($currentapp['resourceownerdetailsurl'])) echo $currentapp['resourceownerdetailsurl'];?>"></td>
			</tr>
			<tr>
				<td><strong>Group Info Endpoint <small>(Optional)</small>:</strong></td>
				<td><input class="mo_table_textbox"  type="text" name="mo_openidconnect_groupdetailsurl" value="<?php if(isset($currentapp['groupdetailsurl'])) echo $currentapp['groupdetailsurl'];?>" ></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>
					<input type="submit" name="submit" value="Save settings" class="button button-primary button-large" />
					<?php if($currentappname != " "){?><input type="button" name="button" value="Test Configuration" class="button button-primary button-large" onclick="testConfiguration()" /><?php } ?>
				</td>
			</tr>
		</table>
		</form>
		</div>
		
		<?php 
		
			mo_openidconnect_attribute_mapping($currentapp, $currentappname);
			$current_appname = get_option("mo_openidconnect_app_name_".$currentappname);
		
			
			mo_openidconnect_client_rolemapping($currentappname);
			
		
		
		mo_openidconnect_client_instructions($current_appname, true);
		
} 


function mo_openidconnect_attribute_mapping($currentapp, $currentappname) {
	?>
	<div class="mo_table_layout" id="attrmapping">
		<form name="form-common" method="post" action="admin.php?page=mo_openidconnect_settings">
		<h3>Attribute Mapping</h3>
		<p style="font-size:10px">Do <b>Test Configuration</b> above to configure attribute mapping.<br></p>
		<input type="hidden" name="option" value="mo_openidconnect_attribute_mapping" />
		<input class="mo_table_textbox" required="" type="hidden" id="mo_openidconnect_app_name" name="mo_openidconnect_app_name" value="<?php echo $currentappname;?>">
		<input class="mo_table_textbox" required="" type="hidden" name="mo_openidconnect_custom_app_name" value="<?php echo $currentappname;?>">
		<table class="mo_settings_table" id="attributemappingtable">		
			<!-- <tr id="mo_openidconnect_email_attr_div">
				<td><strong><font color="#FF0000">*</font>Username Attribute:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_username_attr" name="mo_openidconnect_username_attr" placeholder="Username Attributes Name" value="<?php //if(isset( $currentapp['username_attr']))echo $currentapp['username_attr'];?>"></td>
			</tr> -->
			<tr id="mo_openidconnect_email_attr_div">
				<td><strong><font color="#FF0000">*</font>Email Attribute:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_email_attr" name="mo_openidconnect_email_attr" placeholder="Email Attributes Name" value="<?php if(isset( $currentapp['email_attr']))echo $currentapp['email_attr'];?>"></td>
			</tr>
			<tr id="mo_openidconnect_name_attr_div">
				<td><strong><font color="#FF0000">*</font>First Name Attribute:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_firstname_attr" name="mo_openidconnect_firstname_attr" placeholder="FirstName Attributes Name" value="<?php if(isset( $currentapp['firstname_attr'])) echo $currentapp['firstname_attr'];?>"></td>
			</tr>
			<?php

			echo '<tr id="mo_openidconnect_name_attr_div">
				<td><strong><font color="#FF0000"></font>Last Name Attribute:</strong></td>
				<td><input class="mo_table_textbox" type="text" id="mo_openidconnect_lastname_attr" name="mo_openidconnect_lastname_attr" placeholder="LastName Attributes Name" value="" readonly /></td>
			</tr>

			<tr id="mo_openidconnect_email_attr_div">
				<td><strong><font color="#FF0000"></font>Username Attribute:</strong></td>
				<td><input class="mo_table_textbox" required="" type="text" id="mo_openidconnect_username_attr" name="mo_openidconnect_username_attr" placeholder="Username Attributes Name" value="" readonly /></td>
			</tr>
			
			<tr id="mo_openidconnect_name_attr_div">
				<td><strong><font color="#FF0000"></font>Group Attributes Name:</strong></td>				
				<td>
					<input type="text"  class="mo_table_textbox"  name="mapping_groupname_attribute" placeholder="Group Attributes Name"  value="" readonly />
				</td>
			</tr>
		
			  <tr>
				<td><strong>Display Name:</strong></td>
				<td>
					<select name="oauth_client_am_display_name" id="oauth_client_am_display_name" disabled >
						<option value="USERNAME"';
						echo '>Username</option>
						<option value="FNAME"';
						echo '>FirstName</option>
						<option value="LNAME"';
						echo '>LastName</option>
						<option value="FNAME_LNAME"';
						echo '>FirstName LastName</option>
						<option value="LNAME_FNAME"';
						echo '>LastName FirstName</option>
					</select>
				</td>
			  </tr>'; ?>
			
			<!-- if(get_option('mo_openidconnect_client_custom_attrs_mapping')){
				$custom_attributes = get_option('mo_openidconnect_client_custom_attrs_mapping');
				$i = 0;
				foreach($custom_attributes as $key=>$value){ $i++;
				 echo '<tr class="rows"><td><input type="text" name="mo_openidconnect_client_custom_attribute_key_'.$i.'" placeholder="Enter field meta name"  value="'.$key.'" /></td>
				 <td><input type="text" name="mo_openidconnect_client_custom_attribute_value_'.$i.'" placeholder="Enter attribute name from OAuth Provider" style="width:74%;" value="'.$value.'" /></td>
				 </tr>';
				}
			}else {
				echo '<tr class="rows"><td><input type="text" name="mo_openidconnect_client_custom_attribute_key_1" placeholder="Enter field meta name"   /></td>
				 <td><input type="text" name="mo_openidconnect_client_custom_attribute_value_1" placeholder="Enter attribute name from OAuth Provider" style="width:74%;"  /></td>
				 </tr>';

			} -->
		
			<tr id="save_config_element">
				<td>&nbsp;</td>
				<td><input type="submit" name="submit" value="Save settings"
					class="button button-primary button-large" /></td>
			</tr>
			</table>
		</form>
		<?php
		
		echo '<script>
			var countAttributes = jQuery("#attributemappingtable tr.rows").length;
			function add_custom_attribute(){
				countAttributes += 1;
				rows = "<tr id=\"row_" +countAttributes + "\"><td><input type=\"text\" name=\"mo_openidconnect_client_custom_attribute_key_" + countAttributes + "\" id=\"mo_openidconnect_client_custom_attribute_key_" +countAttributes + "\"  placeholder=\"Enter field meta name\"  ></td><td><input type=\"text\" name=\"mo_openidconnect_client_custom_attribute_value_" +countAttributes + "\" id=\"mo_openidconnect_client_custom_attribute_value_" +countAttributes + "\" placeholder=\"Enter Attribute Name from OAuth Provider\" style=\"width:74%;\" /></td></tr>";

				jQuery(rows).insertBefore(jQuery("#save_config_element"));
			}

			function remove_custom_attribute(){
				jQuery("#row_" + countAttributes).remove();
				countAttributes -= 1;
				if(countAttributes == 0)
					countAttributes = 1;
			}
			</script>';
		
		?>
		<script>
		function testConfiguration(){
			var mo_openidconnect_app_name = jQuery("#mo_openidconnect_app_name").val();
			var myWindow = window.open('<?php echo site_url(); ?>' + '/?option=testattrmappingconfig&app='+mo_openidconnect_app_name, "Test Attribute Configuration", "width=600, height=600");	
		}
		</script>
	</div>
		<?php
		if( get_option('mo_openidconnect_admin_customer_key') > 17991) { ?>
		<div class="mo_table_layout" id="user-creation">
		<form id="form-common" name="form-common" method="post" action="admin.php?page=mo_openidconnect_settings">
		<h3>User Creation</h3>
		<p style="font-size:13px;color:#dc2424">You must create user to enable SSO Login.<br></p>
		<input type="hidden" name="option" value="mo_openidconnect_user_creation" />
		<table class="mo_settings_table">
			<tr id="mo_openidconnect_email_attr_div">
				<p><input disabled type="checkbox" /><strong> Enable Auto Create User</strong><small class="premium_feature"> [STANDARD]</small><br><small>Create user automatically when user will login to the WordPress for the first time.</small></p>
			</tr>
			<tr id="mo_openidconnect_name_attr_div">
				<a onclick="show_user_creation()" href="javascript:void(0)">Click here</a> to create user manually.

			</tr>

			</table>
		</form>
		<script>
			function show_user_creation() {
			   	<?php
			   		echo "var myWindow = window.open('".site_url()."' + '/wp-admin/user-new.php', '', 'width=900,height=900');";
			   	?>
			}
		</script>
		</div>
		

<?php
}
}
	

/* Test Default Configuration*/
function mo_openidconnect_client_rolemapping($currentappname) {
?>
	
	<div class="mo_openidconnect_premium_option_text"><span style="color:red;">*</span>This is a premium feature. 
		<a href="admin.php?page=mo_openidconnect_settings&tab=licensing">Click Here</a> to see our full list of Premium Features.</div>
	<div class="mo_table_layout mo_openidconnect_premium_option" id="rolemapping">
	<div class="mo_openidconnect_client_small_layout" style="margin-top:0px;">
	<br><h3>Role Mapping (Optional)</h3>
	
	<b>NOTE: </b>Role will be assigned only to non-admin users (user that do NOT have Administrator privileges). You will have to manually change the role of Administrator users.<br>
	<form disabled id="role_mapping_form" name="f" method="post" action="" >
		<input disabled class="mo_table_textbox" required="" type="hidden"  name="mo_openidconnect_app_name" value="<?php echo $currentappname;?>">
		<input disabled type="hidden" name="option" value="mo_openidconnect_client_save_role_mapping" />
		
		<p><input disabled type="checkbox" name="keep_existing_user_roles" value="" /><strong> Keep existing user roles</strong><br><small>Role mapping won't apply to existing wordpress users.</small></p>
		<p><input disabled type="checkbox" name="restrict_login_for_mapped_roles" value="" > <strong> Do Not allow login if roles are not mapped here </strong></p><small>We won't allow users to login if we don't find users role/group mapped below.</small></p>
		<p><input disabled type="checkbox" name="restrict_login_for_mapped_roles" value="" > <strong> Do Not allow login if roles are not mapped here </strong></p><small>We won't allow users to login if we don't find users role/group mapped below.</small></p>

	</form>
</div>
</div>		
</div>

</script>
<?php
}

?>