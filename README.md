# README #
# TistoryAPI
php가 필요함.
tistory의 아이디가 필요함.

git clone https://github.com/adiy21c/Tistory.git

수정, 개량을 한 경우 저도 좀....-_-

### What is this repository for? ###

Tistory API

### How do I get set up? ###

setup.php 를 수정.

1. www.tistory.com/guide/api/manage/list 에서 api client id를 발급받는다.
2. (setup.php) client_id, client_secret을 발급받아 입력하고, redirect_uri를 수정한다.
3. 브라우저로 get_authcode.php 파일을 엑세스하면 티스토리 access_token 발급 페이지로 이동.
4. 허가 버튼을 누르면 위의 redirect_uri로 이동하게되고 access_token을 출력한다.
5. (setup.php) 해당 access_token 문자열을 복사하여 setup.php 에 access_token 변수에 입력한다.

### Contribution guidelines ###

- simpledom.php 은 html파싱을 위한것으로, 사용하지 않으려면 지워도 좋다.
- sample.php 을 참조하면 주요 사용법을 알 수 있다.
- http://www.tistory.com/guide/api/index 페이지를 참조하면 return message 를 볼 수 있다.

- backup.php 를 이용하면 블로그를 백업 받을 수 있다. 
  csv 형식으로 다운로드 되고, 워드프레스에 임포트 할 수 있다. 적당히 소스 보고 수정해서 쓰기 바람.
- 참고로 API를 등록해두면 티스토리 접근제한이 걸려있을 때에도 백업이 가능하다.

### Who do I talk to? ###

* adiy21c@nate.com
* www.hobbiez.ml
