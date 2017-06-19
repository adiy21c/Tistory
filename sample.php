<?php
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

require_once "setup.php";
require_once "tistory.php";
require_once "simpledom.php";

$tistory = new Tistory();
$tistory->access_token = $access_token;
$tistory->client_id = $client_id;
$tistory->client_secret = $client_secret;
$tistory->redirect_url = $redirect_uri;

$blogid = ""; // 본인 소유 블로그 아이디

getdogdrip($_GET['srl']);

function getdogdrip($id){
	global $tistory;
	$url = "http://www.dogdrip.net/index.php?document_srl=".$id;
	$html = file_get_contents($url);
	$doc = str_get_html($html);

	$content = $doc->find(".xe_content",0);
	$string = $content->innertext;
	foreach($doc->find(".xe_content",0)->find("img") as $img) {
		// 여기서 이제 업로드 하고 문자열 대체 시켜주고.
		$url = $img->src;

		if( is_null($img->attr['class']) ){
			$url = $img->src;
		} else if( $img->attr['lazy']) {
			$url = $img->attr['data-original'];
		}

		if( strstr($url, "./") !== false ){
			$url = str_replace("./", "http://www.dogdrip.net/", $url);
		}

		$ch = curl_init($url);
		$tmparray = explode('/', $img->src);
		$filename = $tmparray[ count($tmparray) - 1];
		$fp = fopen("./tmp/{$filename}", 'wb');
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$headers = [
		'Connection: keep-alive',
		'Cache-Control: max-age=0',
		'Upgrade-Insecure-Requests: 1',
		'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		'Accept-Encoding: deflate, sdch, br',
		'Accept-Language: ko,en;q=0.8,en-US;q=0.6',
		];
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);

		$ifs = filesize("./tmp/{$filename}");

		if( $ifs > 0 && $ifs < 10485760 ) {
			$result = $tistory->attach($blogid, "./tmp/{$filename}");
			$string = str_replace($img, $result->tistory->replacer, $string);
		}
	}
	
	// 버튼 제거
	$string = str_replace($doc->find(".xe_content",0)->find(".document_popup_menu",0), "", $string);

	$title = $doc->find("h1", 2)->plaintext;
	removeTempfile();
	
	if( $string != "" ){
		$tistory->post_write( $blogid, $title, $string, $title, ""); // 블로그명, 제목, 본문, 태그, 카테고리id
		echo "<span>posting complete.</span>";
	} else {
		echo "<span>posting error.</span>";
	}
	
}

function removeTempfile(){
	$files = glob('./tmp/*'); // get all file names
	foreach($files as $file){ // iterate files
		if(is_file($file))
			unlink($file); // delete file
	}
}
?>
