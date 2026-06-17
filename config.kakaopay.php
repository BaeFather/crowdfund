<?

// 카카오페이 도메인 설정

define('MODE_FLAG', 'real');

if(MODE_FLAG=='real') {
	define('KAKAOPAY_API_DOMAIN', 'https://kakaopay.hellofunding.co.kr');
}
else {
	define('KAKAOPAY_API_DOMAIN', 'https://mirror.hellofunding.co.kr');
}


?>