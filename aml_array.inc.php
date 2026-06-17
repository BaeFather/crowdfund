<?

// 고객구분2 ----------------------------------------------------------------------
$ARR_TMS_CUSTOMER_DIV = array(
	array('CD'=>'02', 'NM'=>'법인'),
	array('CD'=>'01', 'NM'=>'개인'),
	array('CD'=>'03', 'NM'=>'개인사업자')
);
$ATCD_KEY = array_keys($ARR_TMS_CUSTOMER_DIV);


// 접근경로 -----------------------------------------------------------------------
$ARR_AML_RA_CHANNEL_CD = array(
	array('CD'=>'01', 'NM'=>'대면'),
	array('CD'=>'02', 'NM'=>'전화'),
	array('CD'=>'03', 'NM'=>'모바일/인터넷')
);
$AARCC_KEY = array_keys($ARR_AML_RA_CHANNEL_CD);


// 고객유형코드 ---------------------------------------------------------------------
$ARR_CUSTOMER_TP_CD = array(
	array('CD'=>'01', 'NM'=>'비영리단체'),
	array('CD'=>'02', 'NM'=>'고액자산가'),
	array('CD'=>'03', 'NM'=>'신용불량자'),
	array('CD'=>'04', 'NM'=>'금융기관'),
	array('CD'=>'05', 'NM'=>'국가.지방자치단체'),
	array('CD'=>'06', 'NM'=>'UN산하 국제자선기구'),
	array('CD'=>'07', 'NM'=>'상장회사'),
	array('CD'=>'08', 'NM'=>'기타')
);
$ACTC_KEY = array_keys($ARR_CUSTOMER_TP_CD);


// 실명번호구분 ---------------------------------------------------------------------
$AML_RNM_NO_DIV = array(
	array('CD' => '03', 'NM' => '사업자등록번호'),
	array('CD' => '05', 'NM' => '법인등록번호'),
	array('CD' => '01', 'NM' => '주민등록번호(개인)'),
	array('CD' => '02', 'NM' => '주민등록번호(기타단체)'),
	array('CD' => '04', 'NM' => '여권번호'),
	array('CD' => '06', 'NM' => '외국인등록번호'),
	array('CD' => '07', 'NM' => '재외국민거소신고번호'),
	array('CD' => '08', 'NM' => '투자자등록번호'),
	array('CD' => '09', 'NM' => '고유번호/납세번호'),
	array('CD' => '11', 'NM' => 'BIC코드(SWIFT)'),
	array('CD' => '12', 'NM' => '해당국가법인번호'),
	array('CD' => '13', 'NM' => '재정경제부문서번호'),
	array('CD' => '99', 'NM' => '기타')
);
$ARND_KEY = array_keys($AML_RNM_NO_DIV);


// 상장구분 -----------------------------------------------------------------------
$AML_LSTNG_DIV = array(
	array('CD' => '01', 'NM' => '유가증권시장'),
	array('CD' => '02', 'NM' => '코스닥시장'),
	array('CD' => '03', 'NM' => '뉴욕증권거래소'),
	array('CD' => '04', 'NM' => 'NASDAQ'),
	array('CD' => '05', 'NM' => '런던증권거래소'),
	array('CD' => '06', 'NM' => '홍콩증권거래소'),
	array('CD' => '99', 'NM' => '기타')
);
$ALD_KEY = array_keys($AML_LSTNG_DIV);


///////////////////////////////////////////////////////////
// 거래자금출처구분
///////////////////////////////////////////////////////////
// 법인용
$CORP_TRAN_FUND_SOURCE_DIV = array(
	array('CD' => 'B01', 'NM' => '사업소득'),
	array('CD' => 'B02', 'NM' => '부동산임대소득'),
	array('CD' => 'B03', 'NM' => '부동산양도소득'),
	array('CD' => 'B04', 'NM' => '금융소득(이자 및 배당)'),
	array('CD' => 'B99', 'NM' => '기타')
);
$CTFSD_KEY = array_keys($CORP_TRAN_FUND_SOURCE_DIV);

// 개인용
$INDI_TRAN_FUND_SOURCE_DIV = array(
	array('CD' => 'A01', 'NM' => '근로및연금소득'),
	array('CD' => 'A02', 'NM' => '퇴직소득'),
	array('CD' => 'A03', 'NM' => '사업소득'),
	array('CD' => 'A04', 'NM' => '부동산임대소득'),
	array('CD' => 'A05', 'NM' => '부동산양도소득'),
	array('CD' => 'A06', 'NM' => '금융소득(이자 및 배당)'),
	array('CD' => 'A07', 'NM' => '상속/증여'),
	array('CD' => 'A08', 'NM' => '일시 재산양도로 인한 소득'),
	array('CD' => 'A99', 'NM' => '기타')
);
$ITFSD_KEY = array_keys($INDI_TRAN_FUND_SOURCE_DIV);


///////////////////////////////////////////////////////////
// 거래목적코드
///////////////////////////////////////////////////////////
//법인용
$CORP_ACCOUNT_NEW_PURPOSE_CD = array(
	array('CD' => 'B01', 'NM' => '물품대금결제'),
	array('CD' => 'B02', 'NM' => '상속 및 증여성 거래'),
	array('CD' => 'B03', 'NM' => '부채상환'),
	array('CD' => 'B11', 'NM' => '유휴운영자금투자'),
	array('CD' => 'B12', 'NM' => '수탁자금운용'),
	array('CD' => 'B99', 'NM' => '기타')
);
$CANPC_KEY = array_keys($CORP_ACCOUNT_NEW_PURPOSE_CD);

//개인용
$INDI_ACCOUNT_NEW_PURPOSE_CD = array(
	array('CD' => 'A05', 'NM' => '저축'),
	array('CD' => 'A01', 'NM' => '가족보장'),
	array('CD' => 'A02', 'NM' => '노후준비'),
	array('CD' => 'A04', 'NM' => '자녀양육'),
	array('CD' => 'A06', 'NM' => '부채면제'),
	array('CD' => 'A03', 'NM' => '상속준비'),
	array('CD' => 'A07', 'NM' => '기타')
);
$IANPC_KEY = array_keys($INDI_ACCOUNT_NEW_PURPOSE_CD);




// 실제소유자구분 --------------------------------------------------------------------
$AML_REAL_OWNR_CHK_CD = array(
	array('CD' => '40', 'NM' => '법인의 대표자'),
	array('CD' => '10', 'NM' => '법률159조의 사업보고서제출대상법인'),
	array('CD' => '20', 'NM' => '100분의25이상의 지분증권을 소유한 사람'),
	array('CD' => '31', 'NM' => '최대 지분증권을 소유한 사람'),
	array('CD' => '32', 'NM' => '대표자 또는 임원, 업무집행사원의 과반수를 선임한 주주(자연인)')
);
$AROCC_KEY = array_keys($AML_REAL_OWNR_CHK_CD);


// 국선 지역번호 --------------------------------------------------------------------
$PHONE_AREA_NO = array(
	array('NO'=>'02',  'AREA'=>'서울'),
	array('NO'=>'031', 'AREA'=>'경기'),
	array('NO'=>'032', 'AREA'=>'인천'),
	array('NO'=>'033', 'AREA'=>'강원'),
	array('NO'=>'041', 'AREA'=>'충남'),
	array('NO'=>'042', 'AREA'=>'대전'),
	array('NO'=>'043', 'AREA'=>'충북'),
	array('NO'=>'044', 'AREA'=>'세종'),
	array('NO'=>'051', 'AREA'=>'부산'),
	array('NO'=>'052', 'AREA'=>'울산'),
	array('NO'=>'053', 'AREA'=>'대구'),
	array('NO'=>'054', 'AREA'=>'경북'),
	array('NO'=>'055', 'AREA'=>'경남'),
	array('NO'=>'061', 'AREA'=>'전남'),
	array('NO'=>'062', 'AREA'=>'광주'),
	array('NO'=>'063', 'AREA'=>'전북'),
	array('NO'=>'064', 'AREA'=>'제주'),
	array('NO'=>'070', 'AREA'=>'070'),
	array('NO'=>'010', 'AREA'=>'010'),
	array('NO'=>'011', 'AREA'=>'011'),
	array('NO'=>'016', 'AREA'=>'016'),
	array('NO'=>'017', 'AREA'=>'017'),
	array('NO'=>'018', 'AREA'=>'018'),
	array('NO'=>'019', 'AREA'=>'019')
);
$PAN_KEY = array_keys($PHONE_AREA_NO);


// 휴대폰 통신사번호 ------------------------------------------------------------------
$HP_AREA_NO = array(
	array('NO'=>'010', 'AREA'=>'010'),
	array('NO'=>'011', 'AREA'=>'011'),
	array('NO'=>'016', 'AREA'=>'016'),
	array('NO'=>'017', 'AREA'=>'017'),
	array('NO'=>'018', 'AREA'=>'018'),
	array('NO'=>'019', 'AREA'=>'019')
);
$HAN_KEY = array_keys($HP_AREA_NO);

?>