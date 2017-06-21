<?php
namespace Setting;
// www.tistory.com/guide/api/manage/list
const CLIENT_ID = "c2ec4c8f28775f2f1204b0f1fdf205e1"; // API 클라이언트 등록 후 발급
const CLIENT_SECRET = "c2ec4c8f28775f2f1204b0f1fdf205e1cd06ddf0e94e00b14ac2ec245ed55f2271684a97"; // API 클라이언트 등록 후 발급
const REDIRECT_URI = "https://tistory-api-dbjobs.c9users.io/callback.php"; // callback.php URL

const ACCESS_TOKEN = "6346a761c4260be861839f1af86125fb_db6d04cd86ef428ac309ba043942ce0a"; // auth 이후 발급
/***********************************************************************
일단 유효한 access_token이 발급되면 이후엔 다른 uri에서도 사용 가능.
redirect_uri만 올바르게 적어주면 됨.
 ************************************************************************/


const HEADERS = [
    'Connection: keep-alive',
    'Cache-Control: max-age=0',
    'Upgrade-Insecure-Requests: 1',
    'User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.110 Safari/537.36',
    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Encoding: deflate, sdch, br',
    'Accept-Language: ko,en;q=0.8,en-US;q=0.6',
    //'Referer: '. $_POST['referer']
];