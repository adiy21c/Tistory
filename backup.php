<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once "setup.php";
require_once "tistory.php";
require_once "simpledom.php";

$tistory = new Tistory();
$tistory->access_token = $access_token;
$tistory->client_id = $client_id;
$tistory->client_secret = $clinet_secret;
$tistory->redirect_url = $redirect_uri;

$blogid = "";

$categoryr = json_decode( $tistory->getCategory($blogid) );
$category = array();
foreach($categoryr->tistory->item->categories as $key=>$val){
	$category[$val->id] = $val->name;
}
$list = array();
$count = 1000; // 백업할 블로그의 가장 큰 id값.
for( $i=1; $i <= $count; $i++ ){
	$article = json_decode($tistory->post_get($blogid, $i));
	if( $article == NULL ) continue;
	if( $article->tistory->status != "200" ) continue;
	$temp = array();
	$temp['title'] = (string)$article->tistory->item->title;
	$temp['content'] = (string)$article->tistory->item->content;
	$temp['id'] = (string)$i;
	$temp['date'] = (string)$article->tistory->item->date;
	if( $article->tistory->item->tags != null ){
		foreach( $article->tistory->item->tags->tag as $key => $val ){
			if( $key == 0 ) $temp['tag'] = $val;
			else $temp['tag'] .= "|" . $val;
		}
	} else {
		$temp['tag'] = "";
	}
	$temp['category'] = $category[(string)$article->tistory->item->categoryId];

	// filedownload
	$datetemp = explode("-", $temp['date']);
	$year = $datetemp[0];
	$month = $datetemp[1];

	// 디렉토리 있는지 확인
	$upload_dir = './files/'.$year."/".$month."/";
	if( !is_dir( $upload_dir ) ){
		mkdir( $upload_dir, 0777, true );
	}
	
	
	$doc = str_get_html($temp['content']);

    	$imageTags = $doc->find('img');
	$temp['content'] = "";

	foreach($imageTags as $element){
		$back = $element;
		//echo $element->style;
		$element->removeAttribute("height");
		$element->removeAttribute("width");
		$element->removeAttribute("style");
		$element->setAttribute("style", "width:100%; height:auto;");
		if( strpos($element->src, "tistory_admin/blogs/image") ) continue;
		if( strpos($element->src, "icon.daum-img.net/editor/p_etc_s.gif") ) continue;
		$temp['content'] .= $element;
	}
	/*

	$files = $doc->find('a');

	foreach($files as $element){
		if( !strpos($element->href, "attachment") ) continue;
		$rname = download("http://{$blogid}.tistory.com".$element->href, $upload_dir, $i);
		$temp['content'] = str_replace($element->href, "/wp-content/uploads/{$year}/{$month}/{$rname}", $temp['content']);
	}
	*/
	//$temp['tag'] = (string)$article->tistory->item->tags;
	//var_dump($temp);
	$list[] = $temp;
	//print_r($temp);
	echo " $i lines \n";
}

$fp = fopen('file.csv', 'w');

foreach ($list as $fields) {
    fputcsv($fp, $fields);
}

fclose($fp);

//echo count($list);
//var_dump(json_decode($tistory->post_get($blogid, 1)));
//var_dump(json_decode($tistory->post_get($blogid, 2)));
// for( $i=2; $i<=1204; $i++){
// 	$tistory->post_modify($blogid, $i);	
// }
function download($url, $target, $i){
	$targetPath = "$target";
	$filename = $targetPath . 'tmpfile';
	$headerBuff = fopen('/tmp/headers', 'w+');
	$fileTarget = fopen($filename, 'w');
	$origname = "";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_WRITEHEADER, $headerBuff);
	curl_setopt($ch, CURLOPT_FILE, $fileTarget);
	curl_exec($ch);

	if(!curl_errno($ch)) {
	  rewind($headerBuff);
	  $headers = stream_get_contents($headerBuff);
	  if(preg_match('/.*filename=[\'\"]?([^\"]+)/', $headers, $matches)) {
	  	$origname = $matches[1];
	  } else {
	  	$temp = explode("/", $url);
	  	$origname = $temp[count($temp)-1];		
	  }
	  rename($filename, $targetPath . $i . "-" .$origname);
	}
	curl_close($ch);
	return $i.'-'.$origname;
}
?>
