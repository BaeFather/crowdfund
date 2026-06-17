<?php
/********************
    경로 상수
********************/

/*
보안서버 도메인
회원가입, 글쓰기에 사용되는 https 로 시작되는 주소를 말합니다.
포트가 있다면 도메인 뒤에 :443 과 같이 입력하세요.
보안서버주소가 없다면 공란으로 두시면 되며 보안서버주소 뒤에 / 는 붙이지 않습니다.
입력예) https://www.domain.com:443/gnuboard5
*/
define('HF_DOMAIN', '');
define('HF_HTTPS_DOMAIN', '');

define('HF_COOKIE_DOMAIN',  'itembay.hellofunding.co.kr');

// URL 은 브라우저상에서의 경로 (도메인으로 부터의)
if (HF_DOMAIN) {
	//define('G5_URL', HF_DOMAIN);
    //define('HF_URL', HF_DOMAIN);
} else {
	if (1>2) {
        define('HF_URL', $hf_path['url']);
    } else {
        define('HF_URL', '');
	}
}


define('G5_URL',           'https://itembay.hellofunding.co.kr');
define('G5_BBS_URL',       'https://www.hellofunding.co.kr/bbs');
define('G5_THEME_IMG_URL', 'https://hellofunding.co.kr/theme/2018/img/');
define('BSC_URL',          HF_URL.'/syndicate/itembay');  //   /syndicate/itembay
define('HF_CSS_URL',       HF_URL.'/css');
define('HF_DATA_URL',      HF_URL.'/data');
define('HF_IMG_URL',       HF_URL.'/img');
define('G5_IMG_URL',       '/img');
define('HF_IMAGES_URL',    HF_URL.'/images');

if (isset($hf_path['path'])) {
	define('HF_PATH', $hf_path['path']);
} else {
	define('HF_PATH', '');
}

define('BSC_PATH', $_SERVER['DOCUMENT_ROOT']);
define('HF_ORI_PATH', $ORI_ROOT);

define('HF_DATA_PATH', HF_ORI_PATH.'/data');
define('HF_IS_MOBILE', false);
define('HF_LIB_PATH',    HF_PATH.'/lib');
define('HF_PLUGIN_PATH', BSC_PATH.'/plugin');
define('HF_CSS_PATH',    HF_PATH.'/css');
define('HF_IMG_PATH',    HF_PATH.'/img');
define('HF_JS_PATH',     HF_PATH.'/js');
define('HF_JS_URL',      HF_URL.'/js');

// (!주의)세션저장경로
define('HF_SESSION_PATH', HF_DATA_PATH.'/session_itembay');
//define('HF_SESSION_PATH', HF_DATA_PATH.'/data/session');  // /home/crowdfund/public_html/data


/*
//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
//define('HF_SESSION_PATH', $_SERVER['DOCUMENT_ROOT'].'/data/session');  // /home/crowdfund/public_html/data
define('HF_SESSION_PATH', HF_DATA_PATH.'/data/session');  // /home/crowdfund/public_html/data
//}
//else {
//	define('HF_SESSION_PATH', HF_DATA_PATH.'/session2');  // /home/crowdfund_dev2/public_html/data
//}
*/

/********************
    시간 상수
********************/
// 서버의 시간과 실제 사용하는 시간이 틀린 경우 수정하세요.
// 하루는 86400 초입니다. 1시간은 3600초
// 6시간이 빠른 경우 time() + (3600 * 6);
// 6시간이 느린 경우 time() - (3600 * 6);
define('HF_SERVER_TIME',    time());
define('HF_TIME_YMDHIS',    date('Y-m-d H:i:s', HF_SERVER_TIME));
define('HF_TIME_YMD',       substr(HF_TIME_YMDHIS, 0, 10));
define('HF_TIME_HIS',       substr(HF_TIME_YMDHIS, 11, 8));

// MySQLi 사용여부를 설정합니다.
define('HF_MYSQLI_USE', true);

define('HF_USE_CACHE',  false); // 최신글등에 cache 기능 사용 여부


// 모바일 인지 결정 $_SERVER['HTTP_USER_AGENT']
define('G5_MOBILE_AGENT',   'phone|samsung|lgtel|mobile|[^A]skt|nokia|blackberry|android|sony');


/********************
    기타 상수
********************/

// 암호화 함수 지정
// 사이트 운영 중 설정을 변경하면 로그인이 안되는 등의 문제가 발생합니다.
define('HF_STRING_ENCRYPT_FUNCTION', 'sql_password');

// SQL 에러를 표시할 것인지 지정
// 에러를 표시하려면 TRUE 로 변경
define('G5_DISPLAY_SQL_ERROR', FALSE);


$BANK = array(
	'004' => '국민은행',
	'081' => 'KEB하나은행',
	'088' => '신한은행',
	'071' => '우체국',
	'011' => '농협은행',
	'020' => '우리은행',
	'089' => '케이뱅크',
	'090' => '카카오뱅크',
	'007' => '수협중앙회',
	'023' => 'SC은행',
	'002' => '산업은행',
	'003' => '기업은행',
	'027' => '한국씨티은행',
	'031' => '대구은행',
	'032' => '부산은행',
	'034' => '광주은행',
	'035' => '제주은행',
	'037' => '전북은행',
	'039' => '경남은행',
	'045' => '새마을금고중앙회',
	'048' => '신협중앙회',
	'050' => '상호저축은행',
	'054' => 'HSBC은행',
	'055' => '도이치은행',

	'001' => '한국은행',
	'008' => '수출입은행',
	'012' => '지역농․축협',
	'052' => '모건스탠리은행',
	'056' => '알비에스피엘씨은행',
	'057' => '제이피모간체이스은행',
	'058' => '미즈호은행',
	'059' => '미쓰비시도쿄UFJ은행',
	'060' => 'BOA은행',
	'061' => '비엔피파리바은행',
	'062' => '중국공상은행',
	'063' => '중국은행',
	'064' => '산림조합중앙회',
	'065' => '대화은행',
	'066' => '교통은행',
	'076' => '신용보증기금',
	'077' => '기술보증기금',
	'093' => '한국주택금융공사',
	'094' => '서울보증보험',
	'095' => '경찰청',
	'096' => '한국전자금융(주)',
	'099' => '금융결제원',

	'209' => '유안타증권',
	'218' => '현대증권',
	'221' => '골든브릿지투자증권',
	'222' => '한양증권',
	'223' => '리딩투자증권',
	'224' => 'BNK투자증권',
	'225' => 'IBK투자증권',
	'226' => 'KB투자증권',
	'227' => 'KTB투자증권',
	'230' => '미래에셋증권',
	'238' => '대우증권',
	'240' => '삼성증권',
	'243' => '한국투자증권',
	'247' => 'NH투자증권',
	'261' => '교보증권',
	'262' => '하이투자증권',
	'263' => 'HMC투자증권',
	'264' => '키움증권',
	'265' => '이베스트투자증권',
	'266' => 'SK증권',
	'267' => '대신증권',
	'269' => '한화투자증권',
	'270' => '하나대투증권',
	'278' => '신한금융투자',
	'279' => '동부증권',
	'280' => '유진투자증권',
	'287' => '메리츠종합금융증권',
	'290' => '부국증권',
	'291' => '신영증권',
	'292' => '엘아이지투자증권',
	'293' => '한국증권금융',
	'294' => '펀드온라인코리아',
	'295' => '우리종합금융',
	'296' => '삼성선물',
	'297' => '외환선물',
	'298' => '현대선물',

	'041' => '우리카드',
	'044' => '외환카드',
	'361' => 'BC카드',
	'367' => '현대카드',
	'368' => '롯데카드',
	'366' => '신한카드',
	'369' => '수협카드',
	'370' => '씨티카드',
	'371' => 'NH카드',
	'374' => '하나SK카드',
	'381' => 'KB국민카드',
	'364' => '광주카드',
	'365' => '삼성카드',
	'372' => '전북카드',
	'373' => '제주카드',

	'431' => '미래에셋생명',
	'452' => '삼성생명',
	'453' => '흥국생명'
);

/*
// (구)은행코드 : 2016-11-10 신규코드로 대체되었슴.
$BANK = array(
	"02" => '산업',
	"03" => '기업',
	"04" => '국민',
	"05" => '외환',
	"07" => '수협',
	"08" => '수출입',
	"10" => '농협',
	"20" => '우리',
	"21" => '신한',
	"23" => 'SC제일',
	"25" => '하나',
	"27" => '한국씨티',
	"31" => '대구',
	"32" => '부산',
	"34" => '광주',
	"35" => '제주',
	"37" => '전북',
	"39" => '경남',
	"45" => '새마을금고',
	"48" => '신협',
	"50" => '상호저축은행',
	"54" => 'HSBC',
	"71" => '우체국'
);
*/

// 심사자 리스트
$JUDGE = array(
	'A01' => '최수석',
	'B01' => '남기중',
	'C01' => '채영민'
);

// 가상계좌 코드 및 번호
$VBANK = array(
	'003' => 'IBK기업은행',
	'023' => 'SC제일은행',
	'031' => '대구은행'
);

// 개인회원 투자자유형별 금액 제한
// 1=>일반       : 2000만원 (부동산은 1000만원까지만, 동일차주 500만원까지)
// 2=>소득적격   : 4000만원 (동일차주 2000만원)
// 3=>전문투자자 : 무제한
$INDI_INVESTOR = array(
  '1' => array(
           'title'                => '일반투자자',
           'site_limit'           => 20000000,
           'single_product_limit' => 5000000,
           'group_product_limit'  => 5000000,
           'prpt_limit'           => 10000000
				 ),
  '2' => array(
           'title'                => '소득적격투자자',
           'site_limit'           => 40000000,
           'single_product_limit' => 20000000,
           'group_product_limit'  => 20000000,
				 ),
  '3' => array(
           'title'                => '전문투자자',
           'site_limit'           => 999999999999,
           'single_product_limit' => 999999999999,
           'group_product_limit'  => 999999999999
         )
);


$CONF['loan_guideline_date0']   = "2017-05-28";		// 가이드라인 최초 적용일
$CONF['loan_guideline_date1']   = "2018-02-27";		// 가이드라인 2차 적용일
$CONF['old_type_end_date']     = $CONF['loan_guideline_date1'];
$CONF['old_type_end_prdt_idx'] = "135";


// 투자금 설정
$CONF['min_invest_limit'] = 10000;			// 최소투자금액단위
$CONF['max_invest_limit'] = '';


$CONF['customer_mail']    = 'hellofunding@gmail.com';
$CONF['admin_sms_number'] = '15886760';
$_admin_sms_number = $CONF['admin_sms_number'];

// 아이디/비번 입력형식 정의
$ID_LIMIT = array(
	'easy'=>array('min_length'=>6, 'max_length'=>15, 'str_type'=>'', 'describe'=>'영문 또는 영문/숫자 조합, 6-15자리 등록 가능합니다.'),
	'hard'=>array('min_length'=>6, 'max_length'=>15, 'str_type'=>'alpha_num', 'describe'=>'영문 또는 영문/숫자 조합, 6~15자리 등록 가능합니다.')
);
$PW_LIMIT = array(
	'easy'=>array('min_length'=>4, 'max_length'=>15, 'str_type'=>'alpha_num', 'describe'=>'영문/숫자 조합, 4-15자리 등록 가능합니다.'),
	'hard'=>array('min_length'=>8, 'max_length'=>15, 'str_type'=>'alpha_num_special', 'describe'=>'영문/숫자/특수문자 조합, 8-15자리 등록 가능합니다.')
);
$idpw_type = 'hard';		// 현재사용할 아이디/비번 조합 난이도
?>