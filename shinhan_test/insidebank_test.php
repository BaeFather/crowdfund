<?
//exit;

################################################################################
## /usr/local/php/bin/php -q /home/crowdfund/public_html/shinhan_test/insidebank_test.php [000|128|256] [debug]
################################################################################

set_time_limit(0);

include_once("_common.php");

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/lib/common.lib.php");
include_once($base_path."/lib/insidebank.lib.php");


$SHISDBK['target_host']       = "10.10.11.11";			// 실서버
//$SHISDBK['target_host']       = "222.231.31.34";		// 테스트서버
$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";


while( list($k, $v)=@each($_REQUEST) ) { ${$k} = @trim($v); }


$enc_bit = ($_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : $enc_bit;
$enc_bit = ( in_array($enc_bit, array('000','128','256')) ) ? $enc_bit : '256';
$mode    = ($_SERVER['argv']['2']) ? $_SERVER['argv']['2'] :	$mode;

/*
// 결번요청(8400)  -> 전문 실행 결과값 재전송받기
$ORI[] = array('TRAN_DATE' => '20220901', 'FB_SEQ' => 'HEL1481743');

for($i=0,$j=1; $i<count($ORI); $i++,$j++) {
	$ARR['SUBMIT_GBN'] = "04";							//전문번호
	$ARR['TRAN_DATE']  = $ORI[$i]['TRAN_DATE'];				//원전문발송일자
	$ARR['ORI_FB_SEQ'] = $ORI[$i]['FB_SEQ'];		//원전문번호

	$insidebank_result = insidebank_request("000", $ARR, $mode);

	$fcolor = ($insidebank_result['ORI_FB_REQCODE']!='00000000') ? '#FF2222' : '';

	echo "<div style='font-size:12px;color:{$fcolor}'>{$j} " . $ARR['ORI_FB_SEQ'] . " = ";
	print_r($insidebank_result);
	echo "</div>\n";
}
exit;
*/


/*
// 고객정보등록(1100, ※ 한번 사용된 가상계좌는 재배정 불가함!!)
$ARR['REQ_NUM']     = "010";							//전문번호
$ARR['SUBMIT_GBN']  = "01";								//거래구분 (02:변경)
$ARR['CUST_ID']     = "99999999";					//고객ID (20자 제한이 있어 '0채운 10자리 회원번호'로 발송)
$ARR['CUST_NM']     = "배재수";						//고객명
$ARR['CUST_SUB_NM'] = "";									//고객부기명
$ARR['REP_NM']      = "";									//대표자고객명
$ARR['BIRTH_DATE']  = "19750903";					//생년월일자 YYYYMMDD
$ARR['SUP_REG_NB']  = "";									//사업자번호
$ARR['PRI_SUP_GBN'] = "1";								//개인사업자구분
$ARR['HP_NO1']      = "010";							//휴대폰지역번호
$ARR['HP_NO2']      = "6406";							//휴대폰국번호
$ARR['HP_NO3']      = "3972";							//휴대폰일련번호
$ARR['BANK_CD']     = "004";							//은행코드
$ARR['ACCT_NB']     = "59440204031532";		//은행계좌
$ARR['CMS_NB']      = "56212670605010 ";	//가상계좌번호
*/


//REQ_NUM=010&SUBMIT_GBN=02&CUST_ID=228&CUST_NM=(주)에이코리아대부&CUST_SUB_NM=(주)에이코리아대부&REP_NM=박진용&BIRTH_DATE=&SUP_REG_NB=6178182380&PRI_SUP_GBN=2&HP_NO1=010&HP_NO2=4454&HP_NO3=1367&BANK_CD=004&ACCT_NB=44400104045305&CMS_NB=56214495304093
/*
// 고객정보변경(1200)
$ARR['REQ_NUM']     = "010";							//전문번호
$ARR['SUBMIT_GBN']  = "02";								//거래구분 (02:변경)
$ARR['CUST_ID']     = "228";							//고객ID
$ARR['CUST_NM']     = "(주)에이코리아대부";						//고객명
$ARR['CUST_SUB_NM'] = "(주)에이코리아대부";									//고객부기명
$ARR['REP_NM']      = "박진용";									//대표자고객명
$ARR['BIRTH_DATE']  = "";					//생년월일자 YYYYMMDD		730518 1273513
$ARR['SUP_REG_NB']  = "6178182380";									//사업자번호
$ARR['PRI_SUP_GBN'] = "2";								//개인사업자구분 (1:개인, 2:개인사업자,법인)
$ARR['HP_NO1']      = "010";							//휴대폰지역번호
$ARR['HP_NO2']      = "4454";							//휴대폰국번호
$ARR['HP_NO3']      = "1367";							//휴대폰일련번호
$ARR['BANK_CD']     = "004";							//은행코드
$ARR['ACCT_NB']     = "44400104045305";		//은행계좌(환급계좌)
$ARR['CMS_NB']      = "56212670605045";	  //가상계좌번호
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/

/*
// 고객정보변경(1200)
$ARR['REQ_NUM']     = "010";							//전문번호
$ARR['SUBMIT_GBN']  = "02";								//거래구분 (02:변경)
$ARR['CUST_ID']     = "14680";						//고객ID
$ARR['CUST_NM']     = "이미정";						//고객명
$ARR['CUST_SUB_NM'] = "";									//고객부기명
$ARR['REP_NM']      = "";									//대표자고객명
$ARR['BIRTH_DATE']  = "19861013";					//생년월일자 YYYYMMDD
$ARR['SUP_REG_NB']  = "";									//사업자번호
$ARR['PRI_SUP_GBN'] = "1";								//개인사업자구분
$ARR['HP_NO1']      = "010";							//휴대폰지역번호
$ARR['HP_NO2']      = "7227";							//휴대폰국번호
$ARR['HP_NO3']      = "0295";							//휴대폰일련번호
$ARR['BANK_CD']     = "032";							//은행코드
$ARR['ACCT_NB']     = "107120616961";			//은행계좌(환급계좌)
$ARR['CMS_NB']      = "56213699980058";	  //가상계좌번호
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
// 고객정보조회(1400)
$ARR['REQ_NUM']     = "010";
$ARR['SUBMIT_GBN']  = "04";
$ARR['CUST_ID']     = "817";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
unset($ARR);
*/


//660509-2067313		smk6659		49677
//660509-1261529


// 대출등록(2100)
/*
$ARR['REQ_NUM']           = "020";							// 전문번호
$ARR['SUBMIT_GBN']        = "01";								// 거래구분
$ARR['LOAN_SEQ']          = "152";							// 대출식별번호
$ARR['LOAN_AMT']          = "100000000";				// 총대출금
$ARR['LOAN_FEE']          = "0";								// 취급수수료
$ARR['LOAN_EXEC_DATE']    = "20170926";					// 대출실행일자	YYYYMMDD
$ARR['LOAN_EXP_DATE']     = "20180226";					// 대출만기일자	YYYYMMDD
$ARR['LOAN_CUST_ID']      = "2935";							// 대출자고객ID
$ARR['LOAN_CUST_NM']      = "테스트대출자2";		// 대출자고객명
$ARR['CMS_NB']            = "56212670575072";		// 가상계좌번호
$ARR['LOAN_DEP_CNT']      = "2";								// 대출입금계좌건수
$ARR['INV_CNT']           = "1";								// 투자자건수
$ARR['LOAN_DEP_BANK_CD1'] = "004";							// 대출금입금은행코드1
$ARR['LOAN_DEP_ACCT_NB1'] = "57860101265445";		// 대출금입금계좌번호1
$ARR['LOAN_DEP_AMT1']     = "50000000";					// 대출금입금금액1
$ARR['LOAN_DEP_BANK_CD2'] = "004";							// 대출금입금은행코드2
$ARR['LOAN_DEP_ACCT_NB2'] = "10270104006435";		// 대출금입금계좌번호2
$ARR['LOAN_DEP_AMT2']     = "50000000";					// 대출금입금금액2
$ARR['LOAN_DEP_BANK_CD3'] = "";									// 대출금입금은행코드3
$ARR['LOAN_DEP_ACCT_NB3'] = "";									// 대출금입금계좌번호3
$ARR['LOAN_DEP_AMT3']     = "";									// 대출금입금금액3
$ARR['LOAN_DEP_BANK_CD4'] = "";									// 대출금입금은행코드4
$ARR['LOAN_DEP_ACCT_NB4'] = "";									// 대출금입금계좌번호4
$ARR['LOAN_DEP_AMT4']     = "";									// 대출금입금금액4
$ARR['LOAN_DEP_BANK_CD5'] = "";									// 대출금입금은행코드5
$ARR['LOAN_DEP_ACCT_NB5'] = "";									// 대출금입금계좌번호5
$ARR['LOAN_DEP_AMT5']     = "";									// 대출금입금금액5
*/

/*
$ARR['FB_SEQ']="HEL0034523";
$ARR['REQ_NUM']="020";
$ARR['SUBMIT_GBN']="01";
$ARR['LOAN_SEQ']="245";
$ARR['LOAN_AMT']="157570000";
$ARR['LOAN_FEE']="0";
$ARR['LOAN_EXEC_DATE']="20180613";
$ARR['LOAN_EXP_DATE']="20180616";
$ARR['LOAN_CUST_ID']="5818";
$ARR['LOAN_CUST_NM']="주식회사 렛츠플레이컴퍼니";
$ARR['CMS_NB']="56212670575620";
$ARR['LOAN_DEP_CNT']="1";
$ARR['INV_CNT']="1";
$ARR['LOAN_DEP_BANK_CD1']="020";
$ARR['LOAN_DEP_ACCT_NB1']="1005103456541";
$ARR['LOAN_DEP_AMT1']="157570000";
$ARR['LOAN_DEP_BANK_CD2']="";
$ARR['LOAN_DEP_ACCT_NB2']="";
$ARR['LOAN_DEP_AMT2']="";
$ARR['LOAN_DEP_BANK_CD3']="";
$ARR['LOAN_DEP_ACCT_NB3']="";
$ARR['LOAN_DEP_AMT3']="";
$ARR['LOAN_DEP_BANK_CD4']="";
$ARR['LOAN_DEP_ACCT_NB4']="";
$ARR['LOAN_DEP_AMT4']="";
$ARR['LOAN_DEP_BANK_CD5']="";
$ARR['LOAN_DEP_ACCT_NB5']="";
$ARR['LOAN_DEP_AMT5']="";
$ARR['INV_CUST_ID']="234";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/

/*
// 투자자등록(2200)
$VAL = array(
	array('product_idx'=>'397', 'invest_idx'=>'18361', 'member_idx'=>'4330', 'prin_rcv_no'=>'M4330P397I18361', 'amount'=>'10000')
);

$ARR['REQ_NUM']     = "020";
$ARR['SUBMIT_GBN']  = "02";								// 거래구분(등록:02)
$ARR['LOAN_SEQ']    = "148";							// 대출식별번호
$ARR['INV_SEQ']     = "3441";							// 투자자건수일련번호(변경불가항목)
$ARR['INV_CUST_ID'] = "817";							// 투자자고객ID
$ARR['PRIN_RCV_NO'] = "M817P148I3441";		// M회원번호P상품번호I투자번호
$ARR['INV_AMT']     = "5000000";
*/

/*
// 대출실행(2300)
$ARR['REQ_NUM']    = "020";
$ARR['SUBMIT_GBN'] = "03";
$ARR['LOAN_SEQ']   = "";		// 대출식별번호
*/


// 대출취소(2400)
// 164번 상품 대출취소 : 2017-10-31 14:22
// 433번 상품 대출취소 : 2018-10-25 14:22  435번으로 대체
// 8449번 상품 대출취소 : 2022-04-19
/*
$ARR['REQ_NUM']    = "020";
$ARR['SUBMIT_GBN'] = "04";
$ARR['LOAN_SEQ']   = "8449";		// 대출식별번호
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
// 대출정보변경(2500)
$ARR['REQ_NUM']           = "020";							// 전문번호
$ARR['SUBMIT_GBN']        = "05";								// 거래구분
$ARR['LOAN_SEQ']          = "8214";							// 대출식별번호
$ARR['LOAN_AMT']          = "5140000";					// 총대출금
$ARR['LOAN_FEE']          = "0";								// 취급수수료
$ARR['LOAN_EXEC_DATE']    = "20220218";					// 대출실행일자	YYYYMMDD
$ARR['LOAN_EXP_DATE']     = "20220222";					// 대출만기일자	YYYYMMDD
$ARR['LOAN_CUST_ID']      = "9008";								// 대출자고객ID
$ARR['LOAN_CUST_NM']      = "주식회사 엘피엔지";	// 대출자고객명
$ARR['CMS_NB']            = "56212670576836";		// 가상계좌번호
$ARR['LOAN_DEP_CNT']      = "1";								// 대출입금계좌건수
$ARR['INV_CNT']           = "10";								// 투자자건수
$ARR['LOAN_DEP_BANK_CD1'] = "023";							// 대출금입금은행코드1
$ARR['LOAN_DEP_ACCT_NB1'] = "35016217562769";		// 대출금입금계좌번호1
$ARR['LOAN_DEP_AMT1']     = "5140000";					// 대출금입금금액1
$ARR['LOAN_DEP_BANK_CD2'] = "";									// 대출금입금은행코드2
$ARR['LOAN_DEP_ACCT_NB2'] = "";									// 대출금입금계좌번호2
$ARR['LOAN_DEP_AMT2']     = "";									// 대출금입금금액2
$ARR['LOAN_DEP_BANK_CD3'] = "";									// 대출금입금은행코드3
$ARR['LOAN_DEP_ACCT_NB3'] = "";									// 대출금입금계좌번호3
$ARR['LOAN_DEP_AMT3']     = "";									// 대출금입금금액3
$ARR['LOAN_DEP_BANK_CD4'] = "";									// 대출금입금은행코드4
$ARR['LOAN_DEP_ACCT_NB4'] = "";									// 대출금입금계좌번호4
$ARR['LOAN_DEP_AMT4']     = "";									// 대출금입금금액4
$ARR['LOAN_DEP_BANK_CD5'] = "";									// 대출금입금은행코드5
$ARR['LOAN_DEP_ACCT_NB5'] = "";									// 대출금입금계좌번호5
$ARR['LOAN_DEP_AMT5']     = "";									// 대출금입금금액5
$ARR['INV_CUST_ID']       = "527";							// SELECT idx FROM cf_product WHERE idx!='상품번호' AND repay_acct_no='상환계좌번호' AND ib_product_regist='Y' ORDER BY idx ASC LIMIT 1;
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
FB_SEQ=HEL0216250
REQ_NUM=020
SUBMIT_GBN=02
LOAN_SEQ=1554
INV_SEQ=64444
INV_CUST_ID=13658
PRIN_RCV_NO=M13658P1554I64444
INV_AMT=400000
*/

// 투자자변경/취소(2600)
/*
$VAL = array(
//	array('product_idx'=>'6871', 'invest_idx'=>'327114', 'member_idx'=>'49903', 'prin_rcv_no'=>'M49903P6871I327114', 'amount'=>'20000000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327126', 'member_idx'=>'19541', 'prin_rcv_no'=>'M19541P6871I327126', 'amount'=>'30000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327170', 'member_idx'=>'10527', 'prin_rcv_no'=>'M10527P6871I327170', 'amount'=>'4000000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327176', 'member_idx'=>'49903', 'prin_rcv_no'=>'M49903P6871I327176', 'amount'=>'7200000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327178', 'member_idx'=>'49903', 'prin_rcv_no'=>'M49903P6871I327178', 'amount'=>'6200000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327128', 'member_idx'=>'9282',  'prin_rcv_no'=>'M9282P6871I327128',  'amount'=>'2100000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327142', 'member_idx'=>'14715', 'prin_rcv_no'=>'M14715P6871I327142', 'amount'=>'610000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327151', 'member_idx'=>'4312',  'prin_rcv_no'=>'M4312P6871I327151',  'amount'=>'2500000'),
//	array('product_idx'=>'6871', 'invest_idx'=>'327168', 'member_idx'=>'49880', 'prin_rcv_no'=>'M49880P6871I327168', 'amount'=>'2000000')
);

for($i=0; $i<count($VAL); $i++) {
	$ARR['REQ_NUM']     = "020";
	$ARR['SUBMIT_GBN']  = "06";												// 거래구분	(변경:06, 취소:07)
	$ARR['LOAN_SEQ']    = $VAL[$i]['product_idx'];		// 대출식별번호
	$ARR['INV_SEQ']     = $VAL[$i]['invest_idx'];			// 투자자건수일련번호(변경불가항목)
	$ARR['INV_CUST_ID'] = $VAL[$i]['member_idx'];			// 투자자고객ID
	$ARR['PRIN_RCV_NO'] = $VAL[$i]['prin_rcv_no'];		// 원리금수취권번호: M회원번호P상품번호I투자번호
	$ARR['INV_AMT']     = $VAL[$i]['amount'];			    // 투자금액
	$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
	print_rr($insidebank_result);
}
exit;
*/


/*
// 대출상환완료 중도상환 대출취소(2700)
$ARR['REQ_NUM']				= "020";
$ARR['SUBMIT_GBN']		= "08";						// 거래구분	(대출상환완료:08)
$ARR['LOAN_SEQ']			= "162";					// 대출식별번호
$ARR['LOAN_AMT']			= "0";						// 대출상환금액
$ARR['LOAN_EXP_DATE'] = "20171023";			// 대출상환일자
*/


/*
기대출건 취소용 : 대출상환금액을 0으로 셋팅.
                  대출상환일자는 대출실행일자로 셋팅

중도상환용 : 대출상환금액을 중도상환전 납부금액으로 셋팅.
             대출상환일자는 중도상환일자를 셋팅

정산상환용 : 대출상환금은 총대출금을 셋팅
             대출상환일자는 대출상환일자를 셋팅
*/

/*
// 출금(3200) - 예치금
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "6412";
$ARR['TRAN_BANK_CD']    = "090";
$ARR['TRAN_ACCT_NB']    = "3333134167303";
$ARR['TRAN_REMITEE_NM'] = "헬로(1549)";		// $ARR['TRAN_REMITEE_NM'] = "헬로펀딩(".rand(0,99).")";
$ARR['TRAN_AMT']        = "1";
$ARR['TRAN_MEMO']       = "헬로펀딩";
$ARR['GUAR_MEMO']       = "출금(1549)";
$ARR['FUND_KIND']       = "10";										// 예치금:10
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/

/*
$VAL = array(
	array('20679','103'),	array('3124','56'),	array('16528','166'),	array('1675','16'),	array('14715','16'),	array('5801','34917'),	array('11034','16'),	array('20220','16'),	array('17053','263'),	array('16929','166'),
	array('11386','16'),	array('11707','16'),	array('21081','16'),	array('7121','16'),	array('16535','527'),	array('21590','1748'),	array('12862','870'),	array('8104','31'),	array('3738','1748'),	array('16828','832'),
	array('4913','17463'),	array('14511','16'),	array('7134','16'),	array('21488','166'),	array('15696','166'),	array('2584','1748'),	array('19849','166'),	array('13288','166'),	array('19677','166'),	array('11589','16'),
	array('1354','870'),	array('4938','693'),	array('19527','31'),	array('21749','16'),	array('14756','6111'),	array('13229','166'),	array('15038','870'),	array('16165','166'),	array('19541','56'),	array('20895','1748'),
	array('13440','166'),	array('9674','56'),	array('21782','8727'),	array('6082','16'),	array('21040','8727'),	array('12819','1219'),	array('10861','16'),	array('5302','16'),	array('11289','88'),	array('5124','56'),
	array('7882','31'),	array('5795','1748'),	array('16995','341'),	array('5103','16'),	array('10482','166'),	array('13619','56'),	array('19098','1748'),	array('7985','16'),	array('15633','1748'),	array('19567','870'),
	array('7928','166'),	array('16049','341'),	array('19192','3495'),	array('5611','870'),	array('5662','166'),	array('8630','1748'),	array('1166','1748'),	array('6492','870'),	array('18178','166'),	array('5004','326'),
	array('21731','1748'),	array('21735','1748'),	array('16650','1748')
);

for($i=0,$j=1; $i<count($VAL); $i++,$j++) {
	$ARR['REQ_NUM']         = "032";
	$ARR['CUST_ID']         = $VAL[$i][0];
	$ARR['TRAN_BANK_CD']    = "088";
	$ARR['TRAN_ACCT_NB']    = "100032239810";
	$ARR['TRAN_REMITEE_NM'] = "보정2469(".rand(0,99).")";
	$ARR['TRAN_AMT']        = $VAL[$i][1];
	$ARR['TRAN_MEMO']       = "보정2469";
	$ARR['GUAR_MEMO']       = "보정(2469)";
	$ARR['FUND_KIND']       = "10";										// 예치금:10

	echo $j . " : \n";
	print_rr($ARR);

	$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
	print_rr($insidebank_result);
}
*/

/*
// 고객정보조회(1400)
$ARR['REQ_NUM']     = "010";
$ARR['SUBMIT_GBN']  = "04";
$ARR['CUST_ID']     = "53483";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
unset($ARR);
*/

/*
// 2022-01-25일 처리 내용: 김세현 회원 사망으로 인하여 자녀(김영연) 계좌로 이체 (사망증빙자료 및 상속관련자료 전달받음: 정현빈)
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "18276";
$ARR['TRAN_BANK_CD']    = "011";
$ARR['TRAN_ACCT_NB']    = "3010024700261";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩(" . sprintf("%02d", rand(0,99)) .")";
$ARR['TRAN_AMT']        = "5082814";
$ARR['TRAN_MEMO']       = "헬로펀딩반환금";
$ARR['GUAR_MEMO']       = "헬로펀딩반환금";
$ARR['FUND_KIND']       = "10";										// 예치금:10
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "10220";
$ARR['TRAN_BANK_CD']    = "081";
$ARR['TRAN_ACCT_NB']    = "38189021967307";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩(" . sprintf("%02d", rand(0,99)) .")";
$ARR['TRAN_AMT']        = "5000000";
$ARR['TRAN_MEMO']       = "오입금환불";
$ARR['GUAR_MEMO']       = "오입금환불";
$ARR['FUND_KIND']       = "10";										// 예치금:10
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "10220";
$ARR['TRAN_BANK_CD']    = "090";
$ARR['TRAN_ACCT_NB']    = "3333111491499";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩(" . sprintf("%02d", rand(0,99)) .")";
$ARR['TRAN_AMT']        = "1";
$ARR['TRAN_MEMO']       = "오입금환불";
$ARR['GUAR_MEMO']       = "오입금환불";
$ARR['FUND_KIND']       = "10";										// 예치금:10
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "290";
$ARR['TRAN_BANK_CD']    = "088";
$ARR['TRAN_ACCT_NB']    = "100032239834";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩";
$ARR['TRAN_AMT']        = "4470";
$ARR['TRAN_MEMO']       = "임봉기보정";
$ARR['GUAR_MEMO']       = "임봉기보정";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
// 78109 : 이체번호 40002
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "7020";
$ARR['TRAN_BANK_CD']    = "020";
$ARR['TRAN_ACCT_NB']    = "41000006313101";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩216(01)";
$ARR['TRAN_AMT']        = "1000000000";
$ARR['TRAN_MEMO']       = "헬로펀딩216";
$ARR['GUAR_MEMO']       = "원리금(7020)";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

// 78110 10억:40008 / 2억:40009
/*
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "7227";
$ARR['TRAN_BANK_CD']    = "020";
$ARR['TRAN_ACCT_NB']    = "41000006313101";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩231(05)";
$ARR['TRAN_AMT']        = "1000000000";
$ARR['TRAN_MEMO']       = "헬로펀딩231";
$ARR['GUAR_MEMO']       = "원리금(7227)";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);

$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "7227";
$ARR['TRAN_BANK_CD']    = "020";
$ARR['TRAN_ACCT_NB']    = "41000006313101";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩231(06)";
$ARR['TRAN_AMT']        = "200000000";
$ARR['TRAN_MEMO']       = "헬로펀딩231";
$ARR['GUAR_MEMO']       = "원리금(7227)";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
// 78111 : 40010
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "7227";
$ARR['TRAN_BANK_CD']    = "020";
$ARR['TRAN_ACCT_NB']    = "41000006313101";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩232(03)";
$ARR['TRAN_AMT']        = "500000000";
$ARR['TRAN_MEMO']       = "헬로펀딩232";
$ARR['GUAR_MEMO']       = "원리금(7227)";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/

/*
// 78112 : 40011
$ARR['REQ_NUM']         = "032";
$ARR['CUST_ID']         = "7227";
$ARR['TRAN_BANK_CD']    = "020";
$ARR['TRAN_ACCT_NB']    = "41000006313101";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩247(04)";
$ARR['TRAN_AMT']        = "700000000";
$ARR['TRAN_MEMO']       = "헬로펀딩247";
$ARR['GUAR_MEMO']       = "원리금(7227)";
$ARR['FUND_KIND']       = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/


/*
$ARR['REQ_NUM'] = '032';
$ARR['CUST_ID'] = '4144';
$ARR['TRAN_BANK_CD'] = '081';
$ARR['TRAN_ACCT_NB'] = '36591001624004';
$ARR['TRAN_REMITEE_NM'] = '헬로펀딩52(14)';
$ARR['TRAN_AMT'] = '400000000';
$ARR['TRAN_MEMO'] = '헬로펀딩52';
$ARR['GUAR_MEMO'] = '원금지급(4144)';
$ARR['FUND_KIND'] = '10';
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
// GUAR_SEQ(입금처리번호) : 27740
*/

/*
$ARR['REQ_NUM'] = '032';
$ARR['CUST_ID'] = '4026';
$ARR['TRAN_BANK_CD'] = '081';
$ARR['TRAN_ACCT_NB'] = '36591001610704';
$ARR['TRAN_REMITEE_NM'] = '헬로펀딩52(15)';
$ARR['TRAN_AMT']  = '100000000';
$ARR['TRAN_MEMO'] = '헬로펀딩52';
$ARR['GUAR_MEMO'] = '원금지급(4026)';
$ARR['FUND_KIND'] = '10';
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
// GUAR_SEQ(입금처리번호) : 27741
*/

/*
$ARR['REQ_NUM'] = "032";
$ARR['CUST_ID'] = "1203";
$ARR['TRAN_BANK_CD'] = "088";
$ARR['TRAN_ACCT_NB'] = "140011701455";
$ARR['TRAN_REMITEE_NM'] = "헬로펀딩57(99)";
$ARR['TRAN_AMT'] = "5734326";
$ARR['TRAN_MEMO'] = "헬로펀딩57";
$ARR['GUAR_MEMO'] = "연체금(1203)";
$ARR['FUND_KIND'] = "10";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
*/


/*
// 출금(3200) - 상환금
$ARR['REQ_NUM']   = "032";
$ARR['CUST_ID']   = "1";
$ARR['TRAN_AMT']  = "10000000";
$ARR['TRAN_MEMO'] = "";
$ARR['GUAR_MEMO'] = "";		// 상환금모계좌(기관)
$ARR['FUND_KIND'] = "20"; // 상환금:20
*/

/*
// 수취인조회(4000, 예금주명 리턴)
$ARR['REQ_NUM'] = "040";
$ARR['BANK_CD'] = "032";
$ARR['ACCT_NB'] = "56213699980058";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/

//FB_SEQ=HEL0932911&REQ_NUM=020&SUBMIT_GBN=02&LOAN_SEQ=6935&INV_SEQ=328947&INV_CUST_ID=16383&PRIN_RCV_NO=M16383P6935I328947&INV_AMT=1000000


// 고객 투자정보조회(4100)
$ARR['REQ_NUM'] = "041";
$ARR['CUST_ID'] = "10794";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);


/*
$ARR['REQ_NUM'] = "041";
$ARR['CUST_ID'] = "817";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/


/*
$ARR['REQ_NUM'] = "041";
$ARR['CUST_ID'] = "4026";
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
exit;
*/


/*
// 고객 투자정보조회
$MEM = array(
'3351','3352','3537','3641','78','3878','3714','3077','3920','3298',
'2120','666','3723','3839','3665','2289','3085','3589','1939','3932',
'3505','3689','3464','605','1115','2767','3269','3421','3268','1894',
'3948','3344','3360','1682','464','976','3952','3899','3895','1529',
'3959','3957','3840','2170','3731','3518','1513','3342','3855','2279',
'3639','3011','2112','3552','3943','2610','3970','3881','3671','3972',
'3727','3759','2392','3079','685','3544','3953','2657','3886','2742',
'1459'
);

for($i=0,$j=1; $i<count($MEM); $i++,$j++) {
	$ARR['REQ_NUM'] = "041";
	$ARR['CUST_ID'] = $MEM[$i];

	$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
	print_rr($insidebank_result);
	usleep(200000);
}
*/

/*
// 집계조회(4400) - 예치금 신탁계좌 당일 조회만 가능
$ARR['REQ_NUM']     = "044";
$ARR['STAND_DATE']  = "20220731";
$ARR['TOT_GBN_CD']  = "";
print_rr($ARR);
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result);
unset($ARR);
exit;
*/

/*
// 집계조회(4400) - 집계조회 전문 활용해 TOT_GBN_CD(FILLER1) 구분값 세팅 후 회수금신탁계좌 단순잔액만 조회
$ARR['REQ_NUM']     = "044";
$ARR['STAND_DATE']  = "20220520";
$ARR['TOT_GBN_CD']  = "2";				// 잔액조회:2 (FILLER1)
$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
print_rr($insidebank_result, 'color:red');
echo date('Y-m-d H:i:s');
unset($ARR);
exit;
*/

/*
// 원리금 지급요청
$req_seq = '02';		// 회차
$insidebank_result = insidebank_request('001', $req_seq);
print_rr($insidebank_result);
exit;
*/

/*
// 원리금 지급요청 (직접실행)
$req_seq = '01';
$fp = fopen("http://222.231.31.120/IFX5010?REQ_SEQ=".$req_seq, r);
while(!feof($fp)) {
	echo iconv("euc-kr", "utf-8", fgets($fp));
}
fclose($fp);
exit;
*/

/*
// 고객해지(1300, 취급주의)
$ARR['REQ_NUM']     = "010";
$ARR['SUBMIT_GBN']  = "03";
$ARR['CUST_ID']     = "";
*/


/*
(3865)	whdgh2494		채종호
*/

?>