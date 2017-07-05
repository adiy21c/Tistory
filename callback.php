<?php
include_once "setup.php";

$authorization_code = $_REQUEST['code'];
$grant_type = 'authorization_code';
$url = 'https://www.tistory.com/oauth/access_token/';
$param = 'code=' . $authorization_code
	. '&client_id=' . CLIENT_ID
	. '&client_secret=' . CLIENT_SECRET
	. '&redirect_uri=' . urlencode(REDIRECT_URI)
	. '&grant_type=' . $grant_type;
$curl_handle = curl_init();
//echo $url . "?" . $param;
curl_setopt($curl_handle, CURLOPT_URL, $url . "?" . $param);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1000);
ob_start();
curl_exec($curl_handle);
$result = ob_get_clean();
curl_close($curl_handle);
$access_token_t = explode('=', $result);
$access_token = $access_token_t[1];

echo $access_token;