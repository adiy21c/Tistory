<?php
class Tistory {
	public $access_token = "";
	public $client_id = "";
	public $client_secret = "";
	public $redirect_url = "";
	public $response_type = "code";

	private function request_post($url, $param){
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1000);
		curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $param);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		$headers = [
		'Host: '.$url,
		'Connection: keep-alive',
		'Cache-Control: max-age=0',
		'Upgrade-Insecure-Requests: 1',
		'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		'Accept-Encoding: deflate, sdch, br',
		'Accept-Language: ko,en;q=0.8,en-US;q=0.6',
		];
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($curl_handle);
		curl_close($curl_handle);
		$result = $this->unicode_decode($result);
		
		return $result;
	}

	private function request_get($url, $param){
		$curl_handle = curl_init();
		if( $param != "" ){
			$fullurl = $url . $param;
		} else {
			$fullurl = $url;
		}

		curl_setopt($curl_handle, CURLOPT_URL, $fullurl);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 1000);
		$headers = [
		'Host: '.$url,
		'Connection: keep-alive',
		'Cache-Control: max-age=0',
		'Upgrade-Insecure-Requests: 1',
		'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
		'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
		'Accept-Encoding: deflate, sdch, br',
		'Accept-Language: ko,en;q=0.8,en-US;q=0.6',
		];
		curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($curl_handle);
		curl_close($curl_handle);
		
		$result = $this->unicode_decode($result);
		return $result;
	}

	private function unicode_decode($str) {
		if( strstr($str, "\\\\u") === false )
			return $str;	
		else
			return preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str);
	}

	private function replace_unicode_escape_sequence($match) {
		return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
	}

	public function print_r2($var) {
		ob_start();
		print_r($var);
		$str = ob_get_contents();
		ob_end_clean();
		$str = str_replace(" ", "&nbsp;", $str);
		echo nl2br("<span style='font-family:Tahoma, 굴림; font-size:9pt;'>$str</span>");
	}

	public function get_authcode(){
		//echo "<a href='https://www.tistory.com/oauth/authorize/?client_id=".$this->client_id."&redirect_uri=".$this->redirect_url."&response_type=".$this->response_type."'>get_authcode page</a>";
		echo "<script>location.href='https://www.tistory.com/oauth/authorize/?client_id=".$this->client_id."&redirect_uri=".$this->redirect_url."&response_type=".$this->response_type."';</script>";
	}

	public function post_write($blogName, $title, $content, $tag, $category, $output="json"){
		if( $this->access_token == "" ) return false;
		$url = "https://www.tistory.com/apis/post/write";
		$param = "access_token=".$this->access_token;
		$param .= "&blogName=".$blogName;
		$param .= "&title=".$title;
		$param .= "&content=".urlencode($content);
		$param .= "&category=".$category;
		$param .= "&output=".$output;
		$param .= "&visibility=3&tag=".$tag;

		return $this->request_post($url, $param);
	}

	public function post_modify($blogName, $postId, $category){
		if( $this->access_token == "" ) return false;
		$article = json_decode($this->post_get($blogName, $postId));

		//$this->print_r2($article);
		$url = "https://www.tistory.com/apis/post/modify";
		$param = "access_token=".$this->access_token;
		$param .= "&blogName=".$blogName;
		$param .= "&title=".$article->tistory->item->title;
		$param .= "&postId=".$postId;
		$param .= "&content=".$article->tistory->item->content;
		$param .= "&category=" .$category;
		if( count($article->tags->tag) == 0 )
			$param .= "&tag=".$article->tistory->item->title;

		//echo $param;
		return $this->request_post($url, $param);
	}

	public function post_get($blogName, $postId){
		$url = "https://www.tistory.com/apis/post/read";
		$param = "access_token=".$this->access_token;
		$param .= "&blogName=".$blogName;
		$param .= "&postId=".$postId;
		$param .= "&output=json";

		return $this->request_post($url, $param);
	}

	public function getCategory($blogName){
		if( $this->access_token == "" ) return false;
		$url = "https://www.tistory.com/apis/category/list";
		$param = "access_token=".$this->access_token;
		$param .= "&blogName=$blogName&output=json";

		return $this->request_post($url, $param);
	}

	public function getbloginfo($blogurl){
		if( $this->access_token == "" ) return false;
		$url = "https://www.tistory.com/apis/blog/info";
		$param = "access_token=".$this->access_token;
		$param .= "&url=$blogurl&output=json";

		return $this->request_post($url, $param);
	}

	public function attach($blogName, $filepath){
		$fileName = $filepath;
		$fileSize = filesize($fileName);

		if(!file_exists($fileName)) {
		    $out['status'] = 'error';
		    $out['message'] = 'File not found.';
		    exit(json_encode($out));
		}

		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$finfo = finfo_file($finfo, $fileName);

		$cFile = new CURLFile($fileName, $finfo, basename($fileName));
		$data = array( "uploadedfile" => $cFile, "access_token" => $this->access_token, "blogName" => $blogName, "output" => "json");

		$cURL = curl_init("https://www.tistory.com/apis/post/attach");
		curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

		// This is not mandatory, but is a good practice.
		curl_setopt($cURL, CURLOPT_HTTPHEADER,
		    array(
		        'Content-Type: multipart/form-data'
		    )
		);
		curl_setopt($cURL, CURLOPT_POST, true);
		curl_setopt($cURL, CURLOPT_POSTFIELDS, $data);
		curl_setopt($cURL, CURLOPT_INFILESIZE, $fileSize);

		$response = curl_exec($cURL);
		$result = json_decode($this->unicode_decode($response));
		curl_close($cURL);

		return $result;
	}
}

?>
