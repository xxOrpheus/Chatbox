<?php
function makeRequest(array $request, $server = 'http://localhost/chatbox-api/request.php') {	
	$ch = curl_init($server);
	$request = json_encode($request);
	$opts = array(
		CURLOPT_POSTFIELDS => array('request' => $request),
		CURLOPT_RETURNTRANSFER => true
	);
	curl_setopt_array($ch, $opts);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data;
}

$sendMessage = array('command' => 'sendMessage', 'options' => array('room' => '1', 'message' => 'boats and hoes'), 'APP_ID' => 'test');
$getMessages = array('command' => 'getMessages', 'options' => array('room' => '1', 'limit' => '10'), 'APP_ID' => 'test');

$r = makeRequest($sendMessage);
$r = makeRequest($getMessages);
echo $r;
