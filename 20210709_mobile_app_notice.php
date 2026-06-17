<?

include_once("_common.php");

$mobile_notice_start = false;
//if( $CONF['flatform'] == 'app' && preg_match("/175\.223\.20\.211/", $_SERVER['REMOTE_ADDR']) ) {
if( preg_match("/175\.223\.20\.211/", $_SERVER['REMOTE_ADDR']) ) {
	$mobile_notice_start = true;
}

if($mobile_notice_start) {
?>
<!DOCTYPE html>
<html lang="ko">
<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10,user-scalable=yes" />
	<title>헬로펀딩, 대한민국 P2P 금융의 표준, 헬로핀테크, P2P투자, P2P대출, 소액투자의 시작 헬로펀딩</title>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js" integrity="sha384-rY/jv8mMhqDabXSo+UCggqKtdmBfd3qC2/KvyTDNQ6PcUJXaxK1tMepoQda4g5vB" crossorigin="anonymous"></script>
</head>
<body>
<div style="margin:100px 5px;">
헬로펀딩 앱 서비스 일시중단 안내<br/><br/>
안녕하세요 헬로펀딩입니다.<br/><br/>
현재 이용중이신 헬로펀딩 앱 서비스가 7월 9일자로 일시 중단됨을 안내드립니다.<br/>
기존 앱 서비스는 리뉴얼을 통해 투자 전용 앱으로 재출시 예정입니다.<br/><br/>
빠른 시일내에 앱 서비스를 제공할 수 있도록 최선을 다하겠습니다.<br/><br/>
감사합니다.<br/><br/><br/>

<button type="button" onClick="navigator.app.exitApp();" style="width:100%">닫기</button>
</div>
</body>
</html>
<?
}
?>