<?php
include_once('tistory.php');

$tistory = new Tistory\Api("", REDIRECT_URI, "", CLIENT_ID, CLIENT_SECRET);
echo $tistory->get_authcode();

/**********************************************************************************
이 파일에 브라우저로 엑세스 하면 access_token 발급 페이지로 이동.
허가 버튼을 누르면 access_token이 발급된다.
해당 access_token을 setup.php 에 입력 후 저장.

access_token을 발급받기 전까지는 access_token과 blogName은 빈 문자열을 넣어줘도 됨.
 ***********************************************************************************/
