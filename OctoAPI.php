<?php
date_default_timezone_set('Africa/Johannesburg');
include('resources/functions.php');
$apiurl = 'http://192.168.0.99';

//GET Json REST request
$get_data = callAPI('GET', $apiurl.'/api/files', false);
$response = json_decode($get_data, true);
$errors = $response['response']['errors'];
$data = $response['response']['data'][0];
echo "<pre>";
print_r($response);
echo "</pre>";

echo getHostName();


//POST Json REST request
$data_array['command'] = "M18";  //Motor OFF

$make_call = callAPI('POST', $apiurl.'/api/printer/command', json_encode($data_array));
$response = json_decode($make_call, true);
$errors   = $response['response']['errors'];
$data     = $response['response']['data'][0];

echo "<pre>";
print_r($response);
echo "</pre>";
?>