<?

define('_INDEX_', true);
if(!defined('_GNUBOARD_')) exit;			// 개별 페이지 접근 불가


include_once(G5_LIB_PATH.'/function_prc.php');

/** 서버 부하 방지를 위해 캐시 사용 5분당 한번씩 **/
include_once(G5_LIB_PATH.'/nujuk_state.lib.php');
include_once(G5_LIB_PATH.'/product.lib.php');			// 인기상품, 최신상품 추출
include_once(G5_LIB_PATH.'/fundingNews.lib.php');		// 언론보도 리스트
include_once(G5_LIB_PATH.'/latest.lib.php');			// 최신글 추출
include_once(G5_LIB_PATH.'/product.lib.php');			// 인기상품, 최신상품 추출
include_once(G5_LIB_PATH.'/review.lib.php');			// 투자후기 최신글 추출	(필요한 페이지에서만 호출)
//include_once(G5_LIB_PATH.'/outlogin.lib.php');		// 외부로그인창 출력
//include_once(G5_LIB_PATH.'/poll.lib.php');			// 설문조사
//include_once(G5_LIB_PATH.'/popular.lib.php');			// 인기게시물 추출
//include_once(G5_LIB_PATH.'/visit.lib.php');			// 방문자수 출력
//include_once(G5_LIB_PATH.'/connect.lib.php');			// 현재 접속자수 출력

// 전체투자회원 이자수령방식 변경
if( date('Y-m-d H') == '2021-10-01 00' ) {
	sql_fetch("UPDATE g5_member SET receive_method='2' WHERE member_group='F' AND mb_level IN('1','2','3','4','5') AND receive_method='1'");
}


// 헬로펀딩 투자현황 (통계데이터)
$NUJUK_CACHE = getNujukState();

// 구 메인에서 필요한 배열
$NUJUK_STATUS['investAmount']       = price_cutting($NUJUK_CACHE["investAmount"]).'원';				// 누적대출액
$NUJUK_STATUS['repayPrincipal']     = price_cutting($NUJUK_CACHE["repayPrincipal"]).'원';			// 누적상환액
$NUJUK_STATUS['investIngAmount']    = price_cutting($NUJUK_CACHE["investIngAmount"]).'원';		// 대출잔액
$NUJUK_STATUS['averageReturn']      = floatRtrim($NUJUK_CACHE["averageReturn"]).'%';					// 평균수익률(연)
$NUJUK_STATUS['investSuccessCount'] = $NUJUK_CACHE["investSuccessCount"];											// 투자 성공건수
$NUJUK_STATUS['overduePerc']        = floatRtrim($NUJUK_CACHE["overduePerc"]).'%';						// 연체율
$NUJUK_STATUS['bankruptcy']         = floatRtrim($NUJUK_CACHE["bankruptcy"]).'%';							// 부실율


/**** 최신 등록 게시글 존재여부 ::: 메뉴테이블의 "me_code" 를 배열인자로 사용. NEW 마크 출력을 위한... ****/
// 모집중인 상품 : 모집시간 이후 경과 일수 추출
$latestDay['2010'] = sql_fetch("SELECT DATEDIFF(NOW(), start_datetime) AS diffDate FROM cf_product WHERE display='Y' AND state='' AND isTest='' AND only_vip='' AND end_date >= NOW() ORDER BY diffDate DESC LIMIT 1")['diffDate'];

// 공지사항 (게시 이후 경과 일수 추출)
$latestDay['5010'] = sql_fetch("SELECT DATEDIFF(NOW(), wr_datetime) AS diffDate FROM g5_write_notice WHERE 1 ORDER BY diffDate DESC LIMIT 1")['diffDate'];

// 언론보도 (게시 이후 경과 일수 추출)
$latestDay['1050'] = sql_fetch("SELECT DATEDIFF(NOW(), show_date) AS diffDate FROM funding_news_list WHERE 1 ORDER BY diffDate DESC LIMIT 1")['diffDate'];

// 투자후기 (게시 이후 경과 일수 추출)
$latestDay['5030'] = sql_fetch("SELECT DATEDIFF(NOW(), reg_date) AS diffDate FROM epilogue_list WHERE display_yn='Y' ORDER BY diffDate DESC LIMIT 1")['diffDate'];

// 펀딩디자이너 스토리 (게시 이후 경과 일수 추출)
$latestDay['5060'] = sql_fetch("SELECT DATEDIFF(NOW(), regdate) AS diffDate FROM funding_story_list WHERE display_yn='Y' ORDER BY diffDate DESC LIMIT 1")['diffDate'];
//print_rr($latestDay, 'font-size:12px');


// 최신 투자상품 조회 & 인기상품 조회
if(G5_IS_MOBILE) {
	$activeProductList  = getProductList(1, 4, 10);			// 진행상품목록
	$latestProductList  = getProductList(3, 4, 10);			// 최신상품목록
	$popularProductList = getProductList(2, 24, 3600);	// 인기상품목록
}
else {
	$activeProductList  = getProductList(1, 10, 10);		// 진행상품목록
	$latestProductList  = getProductList(3, 4, 10);			// 최신상품목록
	$popularProductList = getProductList(2, 24, 3600);	// 인기상품목록
}


// 언론보도 배열화
$res = sql_query("SELECT idx, subject, thumbnail, news_logo, news_link, press, show_date FROM funding_news_list ORDER BY show_date DESC LIMIT 3");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$FNEWS[] = sql_fetch_array($res);
}
//print_rr($FNEWS,'font-size:12px; text-align:left');

// 공지사항 배열화
$res = sql_query("SELECT wr_id, wr_subject, LEFT(wr_datetime,10) AS wr_datetime FROM g5_write_notice WHERE ca_name='이용안내' AND (wr_3 <='".DATE("Y-m-d")."' AND wr_4 >='".DATE("Y-m-d")."') ORDER BY wr_id DESC LIMIT 3");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$NOTI[] = sql_fetch_array($res);
}
//print_rr($NOTI,'font-size:12px; text-align:left');

// 헬로비디오
$res = sql_query("SELECT * FROM media_video_list WHERE 1 AND display_yn='Y' ORDER BY `sort` ASC, `regdate`");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$MLIST1[] = sql_fetch_array($res);
}
$mlist1_count = count($MLIST1);

// 헬로라이브TV
$res = sql_query("SELECT * FROM media_tv_list WHERE 1 AND display_yn='Y' ORDER BY `regdate` DESC");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$MLIST2[] = sql_fetch_array($res);
}
$mlist2_count = count($MLIST2);


/////////////////////////////////////////////////
// 이벤트 롤링 배너 배열화
/////////////////////////////////////////////////
$EVENT = array();
$num_per_page = 20;
$page = 1;
$strEventClass	=	new Event_Board();
$strColumn = ARRAY('idx', 'ifile', 'linkurl', 'target');
$orderDevice = (G5_IS_MOBILE) ? 'mobile' : 'pc';
$imageArrNo = (G5_IS_MOBILE) ? '2' : '1';					// 0:대표이미지 | 1:PC용 | 2:모바일용
$rowList = $strEventClass->FnMainFront($strColumn, $orderDevice);
IF($rowList[1] > 0) {
	FOR($i=0;$i<COUNT($rowList[2]);$i++) {
		$RowLink = $RowTarget = $RowImgUrl = $EROW = NULL;
		FOR($j=0;$j<COUNT($strColumn);$j++) {
			${$strColumn[$j]} = $rowList[2][$i][$j];
		}
		IF($linkurl) {
			$RowLinkUrl = $linkurl."?SE=".$idx;
			$RowTarget  = $target;
		}
		ELSE {
			$RowLinkUrl = "/hevent/?RD=2&SE=".$idx;
			$RowTarget  = "_self";
		}
		$RowImgUrl = $strEventClass->FnRepimg($ifile, $imageArrNo, "/data/fevent");

		$EROW = array(
			'linkurl' => $RowLinkUrl,
			'target'  => $RowTarget,
			'imgurl'  => $RowImgUrl
		);
		array_push($EVENT, $EROW);
	}

	IF($i < 3) {
		$EROW2 = array(
			'linkurl' => '/event/investor/investor2.php',
			'target' => '_self',
			'imgurl' => G5_THEME_IMG_URL.'/new/ev_03.png'
		);
		array_push($EVENT, $EROW2);
	}
}
/////////////////////////////////////////////////

//print_rr($EVENT, 'font-size:12px;text-align:left;');


// 접속플랫폼별 메인페이지(스킨용) 로드
if(G5_IS_MOBILE) {

	include_once(G5_THEME_MOBILE_PATH.'/head.php');
	include_once(G5_THEME_MOBILE_PATH.'/main.php');
	include_once(G5_THEME_MOBILE_PATH.'/tail.php');

}
else {

	include_once(G5_THEME_PATH.'/head.php');
	include_once(G5_THEME_PATH.'/main.php');
	include_once(G5_THEME_PATH.'/tail.php');

}

?>