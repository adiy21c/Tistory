<?php
/*
특정 사이트의 제목, 본문을 파싱 하는 샘플
*/

error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once '../tistory.php';
require_once "simpledom.php";


define('DOGDRIP_URL', 'http://www.dogdrip.net/');

$blogid = "script-dev"; // 본인 소유 블로그 아이디
$tistory = new Tistory\Api(ACCESS_TOKEN, REDIRECT_URI, $blogid, CLIENT_ID, CLIENT_SECRET);

$upload_dir = "./tmp";
if( !is_dir( $upload_dir ) ){
	mkdir( $upload_dir, 0777, true );
}

getdogdrip($_GET['srl']);

function getdogdrip($id)
{
	global $tistory;
	$url = DOGDRIP_URL . "index.php?document_srl=" . $id;
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
			$url = str_replace("./", DOGDRIP_URL, $url);
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

		// 만약 파일사이즈가 0이거나 ( 다운로드 오류 ), 10메가 이상인 경우엔 티스토리에 업로드 하지 않고 그냥 원본 소스 그대로 사용.
		if( $ifs > 0 && $ifs < 10485760 ) {
			$result = $tistory->attach("./tmp/{$filename}");
			$string = str_replace($img, $result->tistory->replacer, $string);
		}
	}

	// 버튼 제거
	$string = str_replace($doc->find(".xe_content",0)->find(".document_popup_menu",0), "", $string);

	$title = $doc->find("h1", 2)->plaintext;
	removeTempfile();

	if( $string != "" ){
		$result = $tistory->post_write( $title, $string, $title, ""); // 제목, 본문, 태그, 카테고리id
		var_dump(json_decode($result));
		echo "<span>posting complete.</span>";
	} else {
		echo "<span>posting error.</span>";
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
