<?php

//FM variables,change here
putenv("DATABASE=database");
putenv("SERVER=server");
putenv("USER=user");
putenv("PASSWORD=password");

// Use the below three lines to show errors on the page
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Function to get an authorization token which will be used to get data from the database - note, environment variables are set just before function is called
function get_token() {
    $username = getenv("USER");
    $userpass = getenv("PASSWORD");
    $server = getenv("SERVER");
    $database = getenv("DATABASE");

	$additionalHeaders = '';
    $url = 'https://' . $server . '/fmi/data/v1/databases/' . $database .'/sessions';
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $userpass);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, '');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$result = curl_exec($ch); // Execute the cURL statement
    // var_dump($result);
	curl_close($ch); // Close the cURL connection
	// Decode the resulting JSON
	$json_token = json_decode($result, true);
//  $info = curl_getinfo($ch);
//  echo curl_errno($ch);
//  echo curl_error($ch);
//  var_dump($info);
//  var_dump($json_token);
    $code = $json_token['messages'][0]['code'];
    if($code == 0){
        $token_received = $json_token['response']['token'];
	    return($token_received);
    } else {
        $message = $json_token['messages'][0]['message'];
        return ("Err: " . $code .  "Message: " . $message);
    }
};


function delete_token($token){
    $server = getenv("SERVER");
    $database = getenv("DATABASE");

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://' . $server . '/fmi/data/v1/databases/' . $database . '/sessions' .$token,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYHOST => false,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_CUSTOMREQUEST => 'DELETE',
      CURLOPT_POSTFIELDS =>'{}',
      CURLOPT_HTTPHEADER => array('Content-Type: application/json','Authorization: Basic '.$token),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
}

function api_call($token, $method, $body, $path){
    $server = getenv("SERVER");
    $database = getenv("DATABASE");

    $additionalHeaders = 'Authorization: Bearer '.$token;
    $url = 'https://' . $server . '/fmi/data/v1/databases/' . $database . $path;
	$ch = curl_init($url);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $additionalHeaders));
	curl_setopt($ch, CURLOPT_HEADER, 0);
	// curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $userpass);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch,CURLOPT_CUSTOMREQUEST, $method);


	$result = curl_exec($ch); // Execute the cURL statement
    // var_dump($result);
	curl_close($ch); // Close the cURL connection
	// Decode the resulting JSON
	$json_response = json_decode($result, true);
    // $code = $json_response['messages'][0]['code'];
    return $json_response;
}

?>
