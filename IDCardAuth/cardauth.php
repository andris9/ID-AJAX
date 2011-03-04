<?php

error_reporting(E_ALL & ~(E_STRICT|E_NOTICE|E_WARNING));

session_start();
require_once("../lib/api.php");

Header("Content-type: text/javascript; Charset=utf-8");
$response = array();

Auth::CardAuthRequest();
if(!Auth::$error){
	$response["status"] = "AUTHENTICATED";
	$response["data"] = array(
		"UserIDCode"    => Auth::$data["UserIDCode"],
		"UserGivenname" => Auth::$data["UserGivenname"],
		"UserSurname"   => Auth::$data["UserSurname"],
		"UserCountry"   => Auth::$data["UserCountry"]
	);
}else{
	$response["status"] = "ERROR";
    $response["message"] = Auth::$error;
    $response["code"] = Auth::$error_code;
}

echo json_encode($response);