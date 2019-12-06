<?php

class Mo_OpenidConnect_Handler {
	
	function getToken($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url){
		$ch = curl_init($tokenendpoint);
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt( $ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Basic '.base64_encode($clientid.":".$clientsecret),
			'Accept: application/json'
		));
		
		curl_setopt( $ch, CURLOPT_POSTFIELDS, 'redirect_uri='.urlencode($redirect_url).'&grant_type='.$grant_type.'&client_id='.$clientid.'&client_secret='.$clientsecret.'&code='.$code);

		$response = curl_exec($ch);	
		
		if(curl_error($ch)){
			print_r($response);
			exit( curl_error($ch) );
		}
		
		

		if(!is_array(json_decode($response, true))){
			print_r($response);
			exit("Invalid response received getting access_token from url ".$tokenendpoint);
		}
		$content = json_decode($response,true);
		if(isset($content["error_description"])){
			print_r($response);
			exit($content["error_description"]);
		} else if(isset($content["error"])){
			print_r($response);
			exit($content["error"]);
		} 
		
		return $response;
	}
	
	function getIdToken($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url){
		$response = $this->getToken ($tokenendpoint, $grant_type, $clientid, $clientsecret, $code, $redirect_url);
		$content = json_decode($response,true);
		if(isset($content["id_token"])) {
			return $content;
		} else {
			echo 'Invalid response received from OpenId Provider. Contact your administrator for more details.<br><br><b>Response : </b><br>'.$response;
			exit;
		}
	}
	
	
	function getResourceOwnerFromIdToken($id_token){
		$id_array = explode(".", $id_token);
		if(isset($id_array[1])) {
			$id_body = base64_decode($id_array[1]);
			if(is_array(json_decode($id_body, true))){
				return json_decode($id_body,true);
			}
		}
		echo 'Invalid response received.<br><b>Id_token : </b>'.$id_token;
		exit;
	}
		
}

?>