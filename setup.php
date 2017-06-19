<?php
// www.tistory.com/guide/api/manage/list
$client_id = ""; // API 클라이언트 등록 후 발급
$client_secret = ""; // API 클라이언트 등록 후 발급
$redirect_uri = ""; // callback.php URL

$access_token = ""; // auth 이후 발급

$upload_dir = "./tmp";
if( !is_dir( $upload_dir ) ){
	mkdir( $upload_dir, 0777, true );
}
?>
