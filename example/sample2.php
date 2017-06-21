<?php
/*
post로 넘겨받은 제목과 본문을 블로그에 작성
*/
//error_reporting(E_ALL);
//ini_set("display_errors", 1);
require_once "simpledom.php";

$blogid = "script-dev"; // 본인 소유 블로그 아이디
$tistory = new \Tistory\Api(\Setting\ACCESS_TOKEN, \Setting\REDIRECT_URI, $blogid, \Setting\CLIENT_ID, \Setting\CLIENT_SECRET);

$upload_dir = "./tmp";
if( !is_dir( $upload_dir ) ){
        mkdir( $upload_dir, 0777, true );
}

function write()
{
    global $tistory;
    $doc = str_get_html($_POST['content']);
    $content = $_POST['content'];
    
    $hash = md5( $_POST['referer'] );
    
    foreach($doc->find("img") as $element){
    	$url = $element->getAttribute('src');
    	
    	if( strstr($url, "http") == "" ){
    		$url = "http:".$url;
    	}
    
    	$ch = curl_init($url);
    	$tmparray = explode('/', $element->getAttribute('src'));
    	$filename = $tmparray[ count($tmparray) - 1];
    	if(!is_dir("./tmp/{$hash}")) mkdir("./tmp/{$hash}");
    	$fp = fopen("./tmp/{$hash}/{$filename}", 'wb');
    	curl_setopt($ch, CURLOPT_FILE, $fp);
    	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    	curl_setopt($ch, CURLOPT_POSTREDIR, 3);
    	curl_setopt($ch, CURLOPT_HEADER, 0);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, \Setting\HEADERS);
    	curl_exec($ch);
    	curl_close($ch);
    	fclose($fp);
    
    	if( filesize("./tmp/{$hash}/{$filename}") < 10485760 ) {
    		echo $url;
    		$result = json_decode($tistory->attach("evemarket", "./tmp/{$hash}/{$filename}"));
    		var_dump($result);
    		$content = str_replace($element, "<img style='width: 100%; height: auto;'' src='".$result->tistory->url."' />", $content);
    	} else {
    		$content = str_replace($element, "<img style='width: 100%; height: auto;'' src='".$url."' />", $content);
    	}
    }
    
    $content = str_replace($doc->find(".realssp_dv"), "", $content);
    $content = str_replace($doc->find("script"), "", $content);
    
    $title = $_POST['title'];
    removeTempfile($hash);
    
    if( $content != "" ){
        $result = $tistory->post_write( $title, $content, $title, ""); // 제목, 본문, 태그, 카테고리id
    	echo json_decode($result);
    } else {
    	echo "<span>파일 업로드에 문제가 있습니다..</span>";
    }
}

function removeTempfile()
{
	$files = glob('./tmp/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file))
			unlink($file); // delete file
	}
}