<?
set_time_limit(0);

include_once("_common.php");

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/lib/common.lib.php");
include_once($base_path."/lib/insidebank.lib.php");

while( list($k, $v)=@each($_REQUEST) ) { ${$k} = @trim($v); }
?>
<h2>결번요청</h2>
<form name="f" method="post">
<input type="hidden" name="mode">
원거래번호 <input type="text" name="ORI_FB_SEQ" value="<?=$ORI_FB_SEQ?>">
<br/>
거래일자 <input type="text" name="TRAN_DATE" placeholder="<?=date('Ymd')?>" value="<?=$TRAN_DATE?$TRAN_DATE:date("Ymd");?>">
<br/>
<input type=button value="조회" onclick="go_src();">
</form>

<?
if ($mode=="search") {
	echo "조회시작";

	$mode = "";

	$ARR = array();
	//$ARR['SUBMIT_GBN'] = "02";						
	$ARR['SUBMIT_GBN'] = "04";           // 거래구분 04=>결번요청
	$ARR['TRAN_DATE']  = $TRAN_DATE;     //원전문발송일자
	$ARR['ORI_FB_SEQ'] = $ORI_FB_SEQ;    //원전문번호

	$insidebank_result = insidebank_request_test("000", $ARR, $mode);

	echo "<pre>"; print_r($insidebank_result); echo "</pre>";
}
?>

<script>
function go_src() {
	var f = document.f;
	f.mode.value="search";
	f.submit();
}
</script>

<?
if(!is_array($SHISDBK)) {
	$SHISDBK['target_host']       = "222.231.31.120";		// 실서버
//$SHISDBK['target_host']       = "222.231.31.34";		//테스트서버
	$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
	$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
	$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
	$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
	$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";

	// 인사이드뱅크 서버로 URL호출을 하여 데이터 송신을 할 때 사용 URL
	$SHISDBK['001']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5010";
}

/////////////////////////////////////////////
// 전송 및 리턴함수 (결과는 배열로...)
/////////////////////////////////////////////
function insidebank_request_test($enc_bit='000', $data_arr='', $mode='') {
	global $g5;
	global $link;
	global $_SESSION;
	global $SHISDBK;
	global $_REQUEST;

echo "<br/><br/>insidebank_request_test<br/><br/>";
echo "<pre>"; print_r($SHISDBK); echo "</pre>";
echo "<pre>"; print_r($data_arr); echo "</pre>";


	//if($mode=='debug') print_r($data_arr);
/*
	//거래고유번호 생성 (IB_make_fbseq 테이블을 이용)
	sql_query("INSERT INTO IB_make_fbseq SET rdate=NOW()");
	$seq = sql_insert_id($link);
	$fb_seq = 'HEL' . sprintf("%07d", $seq);
	//sql_query("DELETE FROM IB_make_fbseq WHERE LEFT(rdate,10)='".date('Y-m-d', strtotime('-1 day'))."'");		// 오늘것만 저장
*/

	if($enc_bit=='001') {
		// 지급 요청시 $data_arr은 지급회차(01, 02, 03)가 들어온다.
		$reg_seq = $data_arr;
		$data = "REQ_SEQ=" . $reg_seq;

	} else {
		$data = "FB_SEQ=".$fb_seq;
		while( list($key, $value) = @each($data_arr) ) {
			$data.= '&'.$key.'='.@trim($value);

			if($key=='REQ_NUM')    $tmp_req_num    = trim($value);
			if($key=='SUBMIT_GBN') $tmp_submit_gbn = trim($value);
		
		}
	}
echo $data;
return;

	//요청 자료중 로그등록용 데이터 추출
	if($tmp_req_num=='010') {
		switch($tmp_submit_gbn) {
			case '01' :	$REQ = array('request_code'=>'1100', 'request_summary'=>'고객정보등록');    break;
			case '02' :	$REQ = array('request_code'=>'1200', 'request_summary'=>'고객정보변경');    break;
			case '03' :	$REQ = array('request_code'=>'1300', 'request_summary'=>'고객정보해지');    break;
			case '04' :	$REQ = array('request_code'=>'1400', 'request_summary'=>'고객정보조회');    break;
		}
	}
	else if($tmp_req_num=='020') {
		switch($tmp_submit_gbn) {
			case '01' :	$REQ = array('request_code'=>'2100', 'request_summary'=>'대출등록');         break;
			case '02' :	$REQ = array('request_code'=>'2200', 'request_summary'=>'대출투자자등록');   break;
			case '03' :	$REQ = array('request_code'=>'2300', 'request_summary'=>'대출실행');         break;
			case '04' :	$REQ = array('request_code'=>'2400', 'request_summary'=>'대출취소');         break;
			case '05' :	$REQ = array('request_code'=>'2500', 'request_summary'=>'대출정보변경');     break;
			case '06' :	$REQ = array('request_code'=>'2600', 'request_summary'=>'투자자정보변경');   break;
			case '07' :	$REQ = array('request_code'=>'2600', 'request_summary'=>'투자자취소');       break;
			case '08' :	$REQ = array('request_code'=>'2700', 'request_summary'=>'대출상환완료');     break;
		}
	}
	else if($tmp_req_num=='032') $REQ = array('request_code'=>'3200', 'request_summary'=>'예치금출금');
	else if($tmp_req_num=='040') $REQ = array('request_code'=>'4000', 'request_summary'=>'수취인조회');
	else if($tmp_req_num=='041') $REQ = array('request_code'=>'4100', 'request_summary'=>'고객정보조회');
	else if($tmp_req_num=='044') $REQ = array('request_code'=>'4400', 'request_summary'=>'집계조회');



	$host = $SHISDBK['target_host'];
	if( trim($data) ) {
		if($enc_bit=='128') {
			$path = '/IFX5001';
			$enc_key = $SHISDBK[$enc_bit]['enc_key'];
			$encode_str = aes128Encrypt($enc_key, $data);
			$decode_str = aes128Decrypt($enc_key, $encode_str);
		}
		else if($enc_bit=='256') {
			$path = '/IFX5002';
			$enc_key = $SHISDBK[$enc_bit]['enc_key'];
			$encode_str = aes256Encrypt($enc_key, $data);
			$decode_str = aes256Decrypt($enc_key, $encode_str);
		}
		else {
			if($enc_bit=='001') {
				$path = '/IFX5010';
				$REQ = array('request_code'=>'B2500', 'request_summary'=>'원리금지급요청');
			}
			else {
				$path = '/IFX5000';
				if($tmp_submit_gbn=='04') {
					$REQ = array('request_code'=>'8400', 'request_summary'=>'결번조회요청');
				}
				else {
					$REQ = array('request_code'=>'8900', 'request_summary'=>'TESTCALL');
				}
			}
			$encode_str = $data;
			$decode_str = $encode_str;
		}
	}


	if($mode=='debug') {
		echo "원본: ". $data . "\n";
		echo "인코딩: ". $encode_str . "\n";
		echo "디코딩: ". $decode_str . "\n\n";
	}

	if($_SESSION['ss_mb_id']) {
		$mb_id = $_SESSION['ss_mb_id'];
	}
	else {
		if(@$data_arr['CUST_ID']) {
			$TMP = sql_fetch("SELECT mb_id FROM g5_member WHERE mb_no='".$data_arr['CUST_ID']."'");
			$mb_id = $TMP['mb_id'];
		}
	}

	// 실행로그 :: 기록 시작
	$log_sql = "
		INSERT INTO
			IB_request_log
		SET
			request_arr     = '".sql_real_escape_string($data)."',
			request_code    = '".$REQ['request_code']."',
			request_summary = '".sql_real_escape_string($REQ['request_summary'])."',
			mb_id           = '".$mb_id."',
			exec_path       = '".sql_real_escape_string($_SERVER['PHP_SELF'])."',
			referer         = '".sql_real_escape_string($_SERVER['HTTP_REFERER'])."',
			regdate         = NOW()";
	$log_res = sql_query($log_sql);
	$log_idx = sql_insert_id();


	$fp = @fsockopen($host, 80, $errno, $errstr, 30);		    // open a socket connection on port 80 - timeout: 15sec
	if($fp) {
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($encode_str) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $encode_str);
		$result = '';
		while(!feof($fp)) {
			$result.= fgets($fp);
		}
	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR: ' . $errstr . " (" . $errno . ")";		// connection error
	}
	@fclose($fp);

	if($result) {
		$result  = explode("\r\n\r\n", $result, 2);
		$header  = isset($result[0]) ? $result[0] : '';
		$content = isset($result[1]) ? $result[1] : '';

		if($mode=='debug') {
			print_r( array('status' => 'ok', 'header' => $header, 'content' => $content) );
		}

		if(preg_match("/HTTP\/1\.1 200 OK/", $header)) {

			$obj = @simplexml_load_string($content);

			if($obj === false) {
				$RETURN_ARR['ERRMSG'] = "ERROR: FAILED LOADING XML";
			}
			else {
				$ARRAY = XML2Array($obj);		// 오브젝트로 받은 데이터를 배열로 전환
				foreach($ARRAY as $k=>$v) {
					$RETURN_ARR[$k] = $v['@attributes']['value'];
				}


				$RETURN_ARR['FB_SEQ'] = $fb_seq;

				if((string)$RETURN_ARR['RCODE']=='00000000') {
					//
				}
				else {
					// 에러메세지가 전달되지 않았을 경우 코드에 따른 대체
					if(!$RETURN_ARR['ERRMSG']) {
						switch($RETURN_ARR['RCODE']) {
							case "AGE00010" :	$RETURN_ARR['ERRMSG'] = "ERROR:전문오류 (FORMAT ERROR)"; break;
							case "AGE00011" :	$RETURN_ARR['ERRMSG'] = "ERROR:서비스 불가"; break;
							case "AGE00012" :	$RETURN_ARR['ERRMSG'] = "ERROR:해당 이용기관 정보 없음"; break;
							case "AGE00013" :	$RETURN_ARR['ERRMSG'] = "ERROR:DB처리오류"; break;
							case "AGE00014" :	$RETURN_ARR['ERRMSG'] = "ERROR:통신장애"; break;
							case "AGE00015" :	$RETURN_ARR['ERRMSG'] = "ERROR:응답코드오류"; break;
							case "AGE00016" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류"; break;
							case "AGE00017" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류(공통모듈)"; break;
							case "AGE00018" :	$RETURN_ARR['ERRMSG'] = "ERROR:시스템오류(날짜,시간)"; break;
							case "AGE00100" :	$RETURN_ARR['ERRMSG'] = "ERROR:계좌오류"; break;
							case "AGE00101" :	$RETURN_ARR['ERRMSG'] = "ERROR:잔액부족"; break;
							case "AGE00102" :	$RETURN_ARR['ERRMSG'] = "ERROR:원거래없음"; break;
							case "AGE00103" :	$RETURN_ARR['ERRMSG'] = "ERROR:기 처리 오류(이미 처리완료)"; break;
							case "AGE00104" :	$RETURN_ARR['ERRMSG'] = "ERROR:거래 금액오류"; break;
							case "AGE00105" :	$RETURN_ARR['ERRMSG'] = "ERROR:원장/거래내역 미존재"; break;
							case "AGE00107" :	$RETURN_ARR['ERRMSG'] = "ERROR:거래정보오류"; break;
							case "AGE00200" :	$RETURN_ARR['ERRMSG'] = "ERROR:입력값오류(내부)"; break;
							case "AGE00201" :	$RETURN_ARR['ERRMSG'] = "ERROR:입력값오류(외부)"; break;
							case "AGE09999" :	$RETURN_ARR['ERRMSG'] = "ERROR:기타오류"; break;
							default : "ERROR:UNKNOWN"; break;
						}
					}
					else {
						// 메세지가 애매모호 할 경우 메세지 추가
						if($RETURN_ARR['RCODE'] == "AGE00301") $RETURN_ARR['ERRMSG'] = "ERROR:출금지연제한 (" . $RETURN_ARR['ERRMSG'] . ")";
					}

				}

			}

		}
		else {
			$RETURN_ARR['ERRMSG'] = 'ERROR:RESULT_HEADER';
		}

	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR:EMPTY_DATA';
	}


	// 실행로그 :: 결과 기록
	$log_sql2 = "
		UPDATE
			IB_request_log
		SET
			rcode = '".$RETURN_ARR['RCODE']."',
			msg   = '".$RETURN_ARR['ERRMSG']."',
			edate = NOW()
		WHERE
			idx = '$log_idx'";
	sql_query($log_sql2);


	//$limit_date = date("Y-m-d H:i:s", strtotime("-10day"));
	//sql_query("DELETE FROM IB_request_log where regdate < '$limit_date'");


	if($RETURN_ARR)	{
		return $RETURN_ARR;
	}
	else {
		$RETURN_ARR['ERRMSG'] = 'ERROR:EMPTY_RESULT';
		return $RETURN_ARR;
	}

}
?>