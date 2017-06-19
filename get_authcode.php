<?php
require_once "setup.php";
require_once "tistory.php";

$tistory = new Tistory();
$tistory->client_id = $client_id;
$tistory->client_secret = $client_secret;
$tistory->redirect_url = $redirect_uri;

echo $tistory->get_authcode();

// 이 파일에 브라우저로 엑세스 하면 access_token 발급 페이지로 이동.
// 허가 버튼을 누르면 access_token이 발급된다. 
// 해당 access_token을 setup.php 에 입력 후 저장.
?>
