<?
include_once("_common.php");
?>
<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=0,maximum-scale=10">
<meta name="HandheldFriendly" content="true">
<meta name="format-detection" content="telephone=no">
<title>AJAX TEST</title>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">

<!--[if lte IE 8]><script src="/js/html5.js"></script><![endif]-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script type="text/javascript" src="/js/modernizr.custom.70111.js"></script>
<script type="text/javascript" src="/adm/js/jquery.form.js"></script>
<script src="https://spi.maps.daum.net/imap/map_js_init/postcode.v2.js?autoload=false"></script>
</head>
<body>

<button type="button" id="testBtn">AJAX 호출</button>

</body>
</html>

<script>
$('#testBtn').click(function() {
	$.ajax({
		url: "ajax.test_ajax.php",
		type: "post",
		data: {'dataType':'text'},
		dataType: 'text',
		success: function(data) {
			if(data=='OK') {
				alert(data);
			}
			else {
				console.log(data);
			}
		},
		error: function () {
			alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
		}
	})

});
</script>