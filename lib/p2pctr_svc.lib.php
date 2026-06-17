<?
///////////////////////////////////////////////////////////
// 중앙기록관리 라이브러리 (스테이징)
///////////////////////////////////////////////////////////

// [테스트용]
//$p2p_host      = "https://testapi.p2pcenter.or.kr/v1.0/";
//$hello_code    = "M202112431";
//$client_id     = "90b279ba-48ff-42bb-b2e0-6363626c7941";
//$client_secret = "e86f44f4-8f04-4aa6-b2ab-d15aa7c02192";

// [실서비스용]
$p2p_host      = "https://openapi.p2pcenter.or.kr/v1.0/";
$hello_code    = "K210500031";														// 기관코드
$client_id     = "2420bb6b-ce33-4756-ba9f-8bf6fc71fa9e";
$client_secret = "3900b895-df4c-47df-8df4-1d6700392f3d";

$p2p_host_egan = "https://testapi.p2pcenter.or.kr/data/";


$scope = "p2pbiz";
$grant_type = "client_credentials";

$access_token = get_access_token();

?>
<?
$chk_tkn_cnt = 0;  // 토큰 만료로 인한 재전송
$chk_ret_cnt = 0;  // 재전송 횟수

function curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx="", $member_idx="") {

	global $chk_tkn_cnt;
	global $chk_ret_cnt;

	$intStime = time();

	$access_token = get_access_token();

	$strApiTrxNo  = get_p2pord_no(); // API거래고유번호
	if ($strApiTrxNo=="K2105000310000000000") $strApiTrxNo  = get_p2pord_no();
	//if ($strApiTrxNo=="K2105000310000000000") $strApiTrxNo  = "K21050003122". rand(10000000,99999999);
	$strApiTrxDtm = get_dtm_no();    // 거래일시 (밀리세컨드)

	$headers = array();
	ARRAY_PUSH($headers,"Content-Type: application/json; charset=UTF-8");
	ARRAY_PUSH($headers,"Authorization: Bearer ".$access_token);
	ARRAY_PUSH($headers,"api_trx_no: ".$strApiTrxNo);
	ARRAY_PUSH($headers,"api_trx_dtm: ".$strApiTrxDtm);


	$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRESERVE_ZERO_FRACTION);

	if ($apiNo<>"4.4.1") {
		$log_idx = fn_log($apiNo, $apiTitle, $mb_no, $url, $json_data , "", "", "", $strApiTrxNo, $product_idx, $member_idx);
	}

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);


	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	IF($method=="PUT") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	} ELSEIF($method=="DELETE") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	} ELSEIF($method=="GET") {
		$get_data = http_build_query($json_data,'','&');
		$url = $url."?".$get_data;

	} ELSEIF($method=="REST_GET") {


	} ELSEIF($method=="FILE") {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	} ELSE { // POST default
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	}

	curl_setopt($ch, CURLOPT_URL, $url);

	curl_setopt($ch, CURLOPT_VERBOSE, true);
	$streamVerboseHandle = fopen('/home/crowdfund/public_html/data/log/curl_'.gethostname().'_'.date("Ymd").'.log', 'a+');
	curl_setopt($ch, CURLOPT_STDERR, $streamVerboseHandle);

	$result = curl_exec($ch);

	rewind($streamVerboseHandle);
	//echo stream_get_contents($streamVerboseHandle);
	fclose($streamVerboseHandle);

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = SUBSTR($result, 0, $header_size);
	$body = SUBSTR($result, $header_size);

	curl_close($ch);

	$ret = array();
	$ret['http_code'] = $http_code;
	$ret['head'] = $header;
	$ret['body'] = $body;
	$ret['req_body'] = $json_data;


	$resj = json_decode($ret['body'] , true);

	if ($resj['rsp_code'] == "O0002" and $chk_tkn_cnt==0) {  // 토큰 만료일경우
		//echo "토큰 불일치로 재발행됨 <br/>";
		$chk_tkn_cnt = $chk_tkn_cnt + 1;
		get_new_token();
		$ret = curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx="", $member_idx="");
		return $ret;

	} else if ($resj['rsp_code'] == "A5001" and $chk_ret_cnt==0) {  // 사전조건 검증 오류 (strApiTrxNo 중복)

		$chk_ret_cnt = $chk_ret_cnt + 1;
		$ret = curl_p2pctr2($apiNo, $apiTitle , $url , $method , $data,  $product_idx="", $member_idx="");
		return $ret;

	}

	$intEtime = time();
	$thrSec = $intStime - $intEtime;

	if ($apiNo=="4.4.1") {
		fn_log($apiNo, $apiTitle, $mb_no, $url, $ret['req_body'] , $ret['body'], $ret['http_code'], $thrSec, $strApiTrxNo, $product_idx, $member_idx);	// 주석풀면 에러나는 경우 있음....
	} else {
		//fn_log($apiNo, $apiTitle, $mb_no, $url, $json_data , "", "", "", $strApiTrxNo, $product_idx, $member_idx);
		fn_log_up($log_idx, $ret['body'], $ret['http_code'], $thrSec);
	}

	//print_r($ret);

	return $ret;
}

function curl_p2pctr($url , $method , $data , $headers) {

	$json_data = json_encode($data,JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRESERVE_ZERO_FRACTION);


	$ch = curl_init();

	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
	curl_setopt($ch, CURLOPT_TIMEOUT, 5);


	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	IF($method=="PUT") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	} ELSEIF($method=="DELETE") {
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

	} ELSEIF($method=="GET") {
		$get_data = http_build_query($json_data,'','&');
		$url = $url."?".$get_data;

	} ELSEIF($method=="REST_GET") {


	} ELSEIF($method=="FILE") {
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

	} ELSE { // POST default
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	}

	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);

	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header = SUBSTR($result, 0, $header_size);
	$body = SUBSTR($result, $header_size);

	curl_close($ch);

	$ret = array();
	$ret['http_code'] = $http_code;
	$ret['head']      = $header;
	$ret['body']      = $body;
	$ret['req_body']  = $json_data;

	return $ret;
}

function get_status_text($status) {

	if ($status=="T100") $txt="신청중";
	else if ($status=="S150") $txt="상청취소";
	else if ($status=="S200") $txt="모집중";
	else if ($status=="S210") $txt="모집완료";

	else if ($status=="S100") $txt="상환중";
	else if ($status=="S150") $txt="연체중";
	else if ($status=="S301") $txt="상환완료(정상)";
	else if ($status=="S302") $txt="상환완료(양도)";
	else if ($status=="S303") $txt="상환완료(기타)";
	else if ($status=="S304") $txt="부실처리";
	else if ($status=="S311") $txt="계약해제";
	else if ($status=="S311") $txt="계약해지";
	else $txt = $status;

	return $txt;
}

function get_repay_type_txt($type) {

	if ($type=="RP00") $txt="일반상환";
	else if ($type=="RP10") $txt="조기상환";
	else if ($type=="RP90") $txt="연체상환";
	else $txt = $type;

	return $txt;

}

function get_inv_type_txt($type) {

	$typeText="";
	if ($type=="I110") $typeText = "일반개인투자자";
	else if ($type=="I120") $typeText = "소득적격투자자";
	else if ($type=="I130") $typeText = "개인전문투자자";
	else if ($type=="I310") $typeText = "법인투자자";
	else if ($type=="I320") $typeText = "여신금융기관";
	else if ($type=="I330") $typeText = "P2P온투업자";

	return $typeText;

}

function get_access_token_back() {
	$sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1";
	$row = sql_fetch($sql);
	return $row['access_token'];
}

function get_new_token() {

	global $p2p_host;
	global $client_id , $client_secret , $scope , $grant_type;
	global $mb_no;

	$intStime = time();

	$url = $p2p_host."oauth/2.0/token";
	$headers = ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'];
	$method = "POST";

	$data = array();
	$data['client_id']     = $client_id;
	$data['client_secret'] = $client_secret;
	$data['scope']         = $scope;
	$data['grant_type']    = $grant_type;

	$sdata = http_build_query($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $sdata);
	$result = curl_exec($ch);

	$http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);


	$res_arr = json_decode($result, true);


	$apiNo = "4.1.1";
	$apiTitle = "토큰발급 API";
	$intEtime = time();
	$thrSec = $intStime - $intEtime;
	fn_log($apiNo, $apiTitle, $mb_no, $url, $sdata , $result, $http_code, $thrSec);

	$access_token = $res_arr['access_token'];
	$today = time();
	$exday = $today+$res_arr['expires_in'];
	$exday2 = date("Y-m-d H:i:s",$exday);


	$ins_sql = "INSERT INTO p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$res_arr['access_token']."', token_limit_time='".$exday2."'";
	sql_query($ins_sql);
	// 수정 2022-05-16
	/*
	$ch_sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
	$ch_res = sql_query($ch_sql);
	$ch_cnt = sql_num_rows($ch_res);

	if ($ch_cnt) {
		$ch_row = sql_fetch_array($ch_res);
		$up_sql = "UPDATE p2pctr_order_no SET access_token='".$res_arr['access_token']."' , token_limit_time='".$exday2."' WHERE idx='".$ch_row['idx']."'";
		sql_query($up_sql);
	} else {
		$ins_sql = "INSERT INTO p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$res_arr['access_token']."', token_limit_time='".$exday2."'";
		sql_query($ins_sql);
	}
	*/
}

function get_access_token() {

	global $p2p_host;
	global $client_id , $client_secret , $scope , $grant_type;
	global $mb_no;

//$sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1"; // 수정 2022-05-16
	$sql = "SELECT * FROM p2pctr_order_no ORDER BY token_limit_time DESC LIMIT 1";
	$row = sql_fetch($sql);

	$access_token = $row['access_token'];


	if ($row['token_limit_time'] <= date('Y-m-d H:i:s')) {

		$intStime = time();

		$url = $p2p_host."oauth/2.0/token";
		$headers = ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'];
		$method = "POST";

		$data = array();
		$data['client_id'] = $client_id;
		$data['client_secret'] = $client_secret;
		$data['scope'] = $scope;
		$data['grant_type'] = $grant_type;

		$sdata = http_build_query($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $sdata);
		$result = curl_exec($ch);

		$http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);


		$res_arr = json_decode($result, true);


		$apiNo = "4.1.1";
		$apiTitle = "토큰발급 API";
		$intEtime = time();
		$thrSec = $intStime - $intEtime;
		fn_log($apiNo, $apiTitle, $mb_no, $url, $sdata , $result, $http_code, $thrSec);

		$access_token = $res_arr['access_token'];
		$today = time();
		$exday = $today+$res_arr['expires_in'];
		$exday2 = date("Y-m-d H:i:s",$exday);

		// 수정 2022-05-16
		$ins_sql = "INSERT INTO p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$res_arr['access_token']."', token_limit_time='".$exday2."'";
		sql_query($ins_sql);
/*
		$ch_sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
		$ch_res = sql_query($ch_sql);
		$ch_cnt = sql_num_rows($ch_res);

		if ($ch_cnt) {
			$ch_row = sql_fetch_array($ch_res);
			$up_sql = "UPDATE p2pctr_order_no SET access_token='".$res_arr['access_token']."' , token_limit_time='".$exday2."' WHERE idx='".$ch_row['idx']."'";
			sql_query($up_sql);
		} else {
			$ins_sql = "INSERT INTO p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$res_arr['access_token']."', token_limit_time='".$exday2."'";
			sql_query($ins_sql);
		}
*/
	}

	return $access_token;

}

function reset_odno_tbl() {
	sql_query("DELETE FROM p2pctr_order_no2 WHERE idx>0");
	sql_query("ALTER TABLE p2pctr_order_no2 AUTO_INCREMENT=1");


	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='TXNO', odno=1");  // 거래번호
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='LNRG', odno=1");  // 대출 신청
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='GDRG', odno=1");  // 상품 모집
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='IVRG', odno=1");  // 투자 신청
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='LNCT', odno=1");  // 대출 계약
	sql_query("INSERT INTO p2pctr_order_no2 SET ymd='".date("Ymd")."', gubun='IVCT', odno=1");  // 투자 계약
}

function get_p2pord_no() {

	//$hello_code = "M202112431";
	global $hello_code;

	$gubun = "TXNO";

	$sql = "
		INSERT INTO
			p2pctr_order_no2
		SET
			ymd   = '".date("Ymd")."',
			gubun = '$gubun'
	";

	$res = sql_query($sql);
	$idx = sql_insert_id();
	$no  = $idx;

	if (!$no) {
		sleep(1);
		$sql = "
			INSERT INTO p2pctr_order_no2
				(ymd, gubun, odno)
				SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
		$res = sql_query($sql);
		$idx = sql_insert_id();

		$sql2 = "SELECT * FROM p2pctr_order_no2 WHERE idx='$idx'";
		$row2 = sql_fetch($sql2);
		$no  = $row2['odno'];
	}

	$odno = $hello_code. str_pad($no , 10, "0", STR_PAD_LEFT);

	return $odno;

}


function get_p2pord_no_back() {

	//$hello_code = "M202112431";
	global $hello_code;

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {

		$row = sql_fetch_array($res);

		$no = $row['ordno']+1;

		$up_sql = "UPDATE p2pctr_order_no SET ordno='$no' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	} else {

		$no = 1;

		$old_sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1";
		$old_row = sql_fetch($old_sql);

		$ins_sql = "INSERT p2pctr_order_no SET ymd='".date("Ymd")."' , ordno='$no' , access_token='".$old_row['access_token']."' ,token_limit_time='".$old_row['token_limit_time']."'";
		sql_query($ins_sql);

	}

	$odno = $hello_code. substr(date("Ymd"), 2). str_pad($no , 4, "0", STR_PAD_LEFT);

	return $odno;

}

function get_dtm_no() {
	$now = DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
	$local = $now->setTimeZone(new DateTimeZone('Asia/Seoul'));
	$api_trx_dtm = $now->format("YmdHisu");  // 거래일시 (밀리세컨드)
	$api_trx_dtm = substr($api_trx_dtm,0,-3);

	return $api_trx_dtm;
}

function get_new_id($gbn) {

	global $hello_code;

	if ($gbn=="loan_register_id")            $GB = "LR";	 // 대출신청 기록 ID
	else if ($gbn=="goods_id")               $GB = "GR";	 // 대출신청 기록 ID
	else if ($gbn=="investment_register_id") $GB = "IR";	 // 대출신청 기록 ID
	else if ($gbn=="loan_contract_id")       $GB = "LC";	 // 대출신청 기록 ID
	else if ($gbn=="contract_id")            $GB = "IC";

	//$day_serial = get_date_serial($gbn);
	if ($gbn=="investment_register_id") {
		$day_serial = get_date_serial2($gbn);
	} else {
		$day_serial = get_date_serial($gbn);
	}

	$new_id = $hello_code."_".date("ymd")."_".$GB."_".str_pad($day_serial , 10, "0", STR_PAD_LEFT);

	return $new_id;
}

function get_date_serial2($gbn) {

	if ($gbn=="loan_register_id") $gubun="LNRG";
	else if ($gbn=="goods_id") $gubun="GDRG";
	else if ($gbn=="investment_register_id") $gubun="IVRG";
	else if ($gbn=="loan_contract_id") $gubun="LNCT";
	else if ($gbn=="contract_id") $gubun="IVCT";

	$sql = "INSERT INTO p2pctr_order_no2
					SET ymd   = '".date("Ymd")."',
						gubun = '$gubun'
			";

	$res = sql_query($sql);
	$idx = sql_insert_id();
	$no  = $idx;

	return $no;
}

function get_date_serial($gbn) {

	chk_odno_row();

	if ($gbn=="loan_register_id") $gubun="LNRG";
	else if ($gbn=="goods_id") $gubun="GDRG";
	else if ($gbn=="investment_register_id") $gubun="IVRG";
	else if ($gbn=="loan_contract_id") $gubun="LNCT";
	else if ($gbn=="contract_id") $gubun="IVCT";

	$chk_sql = "SELECT count(*) chk_cnt FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$chk_row = sql_fetch($chk_sql);

	if (!$chk_row['chk_cnt']) {
		reset_odno_tbl();
	}

	$sql = "INSERT INTO p2pctr_order_no2(ymd, gubun, odno)
			SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$res = sql_query($sql);
	$idx = sql_insert_id();


	$sql2 = "SELECT * FROM p2pctr_order_no2 WHERE idx='$idx'";
	$row2 = sql_fetch($sql2);
	$ret = $row2['odno'];

	if (!$ret) {
		sleep(1);

		$sql = "INSERT INTO p2pctr_order_no2(ymd, gubun, odno)
				SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2 WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
		$res = sql_query($sql);
		$idx = sql_insert_id();


		$sql2 = "SELECT * FROM p2pctr_order_no2 WHERE idx='$idx'";
		$row2 = sql_fetch($sql2);
		$ret = $row2['odno'];

	}

	return $ret;
}

function get_date_serial_back($gbn) {

	chk_odno_row();

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";

	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$row = sql_fetch_array($res);


	if ($gbn=="loan_register_id") {
		$ret = $row['loan_register_no'] + 1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_register_no='$ret' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	} else if ($gbn=="goods_id") {
		$ret = $row['goods_no'] + 1;
		$up_sql = "UPDATE p2pctr_order_no SET goods_no='$ret' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	} else if ($gbn=="investment_register_id") {
		$ret = $row['investment_register_no'] + 1;
		$up_sql = "UPDATE p2pctr_order_no SET investment_register_no='$ret' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	} else if ($gbn=="loan_contract_id") {  // 대출 계약 번호
		$ret = $row['loan_contract_no'] + 1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_contract_no='$ret' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	} else if ($gbn=="contract_id") {  // 투자 계약 번호
		$ret = $row['contract_no'] + 1;
		$up_sql = "UPDATE p2pctr_order_no SET contract_no='$ret' WHERE idx='".$row['idx']."'";
		sql_query($up_sql);

	}

	return $ret;
}

function chk_odno_row() {
return; // 필요 없어짐 2022-05-16 p2pctr_order_no 는 90마다 한번 무조건 insert 로 바뀜
	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	if (!$cnt) {
		$old_sql = "SELECT * FROM p2pctr_order_no WHERE ymd<'".date("Ymd")."' ORDER BY ymd DESC LIMIT 1";
		$old_res = sql_query($old_sql);
		$old_row = sql_fetch_array($old_res);


		$ins_sql = "INSERT p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$old_row['access_token']."' ,token_limit_time='".$old_row['token_limit_time']."'";
		sql_query($ins_sql);
	}

}

function get_goods_type($product_idx) {

    /*
    category 1동산 2부동산 3 매출채권
    mortgage_guarantees 주택담보
        상품 유형 코드
    (부동산)P110 부동산 프로젝트파이낸싱 연계대출 상품
    (부동산)P120 부동산 담보 연계대출 상품

    (매출채권)P210 어음·매출채권 담보 연계대출 상품
    (주택담보)P220 기타 담보 연계대출 상품(어음·매출채권 제외)
    (신용)P230 개인 신용 연계대출 상품
    (신용)P240 법인 신용 연계대출 상품
    (전체)P000 전체 상품 ALL
    */

	$sql = "SELECT category , mortgage_guarantees FROM cf_product WHERE idx='$product_idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	if ($row['category']=="1") {  // 동산
		$ret = "P220";
	} else if ($row['category']=="2") {  // 부동산
		if ($row['mortgage_guarantees']=="1") $ret = "P120";
		else $ret = "P110";
	} else if ($row['category']=="3") {  // 매출채권
		$ret = "P210";
	} else {
		$ret = "";
	}

	return $ret;
}

function get_status($product_idx) {

    // T200(모집중), T210(모집완료)
    // N건의 연계투자상품 전체가 모집완료(T210)이거나,- 상환 중/연체 중 혹은 상환완료 상태임 (S100, S150, S300등)

	$sql = "SELECT status FROM cf_product WHERE idx='$product_idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

    if($row['status'] == "2") $ret = "T210";
    else $ret = "T200";

    return $ret;

}

?>
<?

FUNCTION check_int($obj)
{
  $ret = preg_replace('/[^0-9]/','', $obj);
  return $ret;
}

Class Product_Class
{
  private $dbConn;

  Public Function __construct($link)
  {
    $this->dbConn = $link;
  }
  Function __destruct()
  {
  }

  FUNCTION register_id()
  {
    global $hello_code;
    $intRand = DATE("His").RAND(1000,9999);
    $ret = $hello_code."_".DATE("ymd")."_GR_".$intRand;

    return $ret;
  }

  FUNCTION fn_register_save($product_idx)
  {
    $Query = "SELECT idx, goods_id FROM p2pctr_product WHERE product_idx='".add_str2($product_idx)."'";

    $Result = sql_query($Query);

    $goods_id = "";
    $idx = 0;
    IF($Row=sql_fetch_array($Result))
    {
        $idx       =  $Row['idx'];
        $goods_id  =  $Row['goods_id'];
        sql_free_result($Result);
    }
    IF(!$goods_id)
    {
      $goods_id = $this->register_id();

      IF($idx > 0)
      {
        $Query = "UPDATE p2pctr_product SET goods_id='".add_str2($goods_id)."', reg_date=now(),logidx=0 WHERE product_idx='".add_str2($product_idx)."'";
      } ELSE {
        $Query = "INSERT INTO p2pctr_product
                 (request_idx, loan_register_id, loan_contract_id, goods_id, reg_date, logidx, product_idx,loan_contract_amount, status, loan_term_days)
                 VALUES
                 (0,'','','".add_str2($goods_id)."',now(),0,'".$product_idx."',0,'',0)";
      }
      sql_query($Query);
    }
    return $goods_id;
  }

  FUNCTION product_id($idx)
  {
	/*
    $strTable = "(SELECT t1.category, t1.mortgage_guarantees, t1.recruit_amount, t1.state, t1.start_date,
                         t1.end_date,t1.invest_end_date,t1.loan_start_date, IFNULL(t2.goods_id,'') as goods_id ,
                         t1.title, t1.loan_interest_rate, t1.invest_return, IFNULL(t2.loan_register_id,'') as loan_register_id ,IFNULL(t2.loan_contract_id,'') as loan_contract_id
                  FROM cf_product t1 LEFT JOIN p2pctr_product t2 ON t1.idx=t2.product_idx WHERE t1.idx='".add_str2($idx)."') t1";
	*/
    $strTable = "(SELECT t1.category, t1.mortgage_guarantees, t1.recruit_amount, t1.state, t1.start_date,
                         t1.end_date,t1.invest_end_date,t1.loan_end_date,t1.loan_start_date, IFNULL(t2.goods_id,'') as goods_id ,
                         t1.title, t1.loan_interest_rate, t1.invest_return, IFNULL(t2.loan_register_id,'') as loan_register_id ,IFNULL(t2.loan_contract_id,'') as loan_contract_id
                  FROM cf_product t1 LEFT JOIN p2pctr_product t2 ON t1.idx=t2.product_idx WHERE t1.idx='".add_str2($idx)."') t1";
    $strTable = "(SELECT t1.category, t1.mortgage_guarantees, t1.recruit_amount, t1.state, t1.start_date,
                         t1.end_date,t1.invest_end_date,t1.loan_end_date,t1.loan_start_date, t1.goods_id as goods_id ,
                         t1.title, t1.loan_interest_rate, t1.invest_return, t1.loan_register_id as loan_register_id ,t1.loan_contract_id as loan_contract_id
                  FROM cf_product t1 WHERE t1.idx='".add_str2($idx)."') t1";

    $strColumn = ARRAY("category","mortgage_guarantees","recruit_amount","state","start_date","end_date","invest_end_date","loan_end_date","loan_start_date","goods_id","title","loan_interest_rate","invest_return","loan_register_id","loan_contract_id");
    $Query = "SELECT ";
    FOR($i=0;$i<COUNT($strColumn);$i++)
    {
      IF($i > 0) { $Query .= ","; }
      $Query .= $strColumn[$i];
    }
    $Query .= " FROM ".$strTable;

    $Result = sql_query($Query);

    $Ret = ARRAY();

    IF($Row=sql_fetch_array($Result))
    {
      FOR($i=0;$i<COUNT($strColumn);$i++)
      {
        $Ret[$strColumn[$i]] = $Row[$strColumn[$i]];
      }
    }
    return $Ret;
  }

  FUNCTION product_loan_request($product_idx)
  {
    $strTable = "p2pctr_loan_request";
    $strColumn = ARRAY("brw_identity_no","brw_name","brw_type","brw_business_register_no");
    $Query = "SELECT ";
    FOR($i=0;$i<COUNT($strColumn);$i++)
    {
      IF($i > 0) { $Query .= ","; }
      $Query .= $strColumn[$i];
    }
    $Query .= " FROM ".$strTable." WHERE product_idx='".add_str2($product_idx)."'";

    $Result = sql_query($Query);

    $Ret = ARRAY();
    FOR($i=0;$i<COUNT($strColumn);$i++)
    {
      $Ret[$strColumn[$i]] = $Row[$strColumn[$i]];
    }

    IF($Row=sql_fetch_array($Result))
    {
      FOR($i=0;$i<COUNT($strColumn);$i++)
      {
        $Ret[$strColumn[$i]] = $Row[$strColumn[$i]];
      }
    }
    return $Ret;
  }

  FUNCTION product_invest_code($state)
  {
    //진행현황(1:이자상환중|2:상환완료(투자종료)|3:투자금모집실패|4:부실|5:중도상환|6:대출취소(기표전)|7:대출취소(기표후)|8:연체|9:매각
    SWITCH($state)
    {
      CASE "1" : $ret = "S100"; BREAK; //계약-상환중
      CASE "8" : $ret = "S150"; BREAK; // 계약-연체중
      CASE "2" : $ret = "S301"; BREAK; // 계약-상환완료(정상)
      CASE "9" : $ret = "S302"; BREAK; // 계약 - 상환완료(양도)
      CASE "5" : $ret = "S303"; BREAK; // 계약 상환완료 (기타)
      CASE "4" : $ret = "S304"; BREAK; // 계약 상환완료 (부실처리)
      CASE "7" : $ret = "S311"; BREAK; // 계약- 계약의 해제
      CASE "3" : CASE "6" : $ret = "S312"; BREAK; // 계약 계약의 해지
    }
    return $ret;
  }

  FUNCTION product_status_code($status)
  {
    // T200(모집중), T210(모집완료)
    // N건의 연계투자상품 전체가 모집완료(T210)이거나,- 상환 중/연체 중 혹은 상환완료 상태임 (S100, S150, S300등)
    IF($status == "2")
    {
      $ret = "T210";
    } ELSE {
      $ret = "T200";
    }
    return $ret;
  }

  FUNCTION product_code($category, $mortgage_guarantees)
  {
    /*
    category 1동산 2부동산 3 매출채권
    mortgage_guarantees 주택담보
        상품 유형 코드
    (부동산)P110 부동산 프로젝트파이낸싱 연계대출 상품
    (부동산)P120 부동산 담보 연계대출 상품

    (매출채권)P210 어음·매출채권 담보 연계대출 상품
    (주택담보)P220 기타 담보 연계대출 상품(어음·매출채권 제외)
    (신용)P230 개인 신용 연계대출 상품
    (신용)P240 법인 신용 연계대출 상품
    (전체)P000 전체 상품 ALL
    */
	SWITCH($category)
    {
      CASE "1" : //동산
        $ret = "P220";
      BREAK;
      CASE "2" : //부동산
        $ret = "P110";
      BREAK;
      CASE "3" : //매출채권
        $ret = "P210";
      BREAK;
    }
    IF($mortgage_guarantees)
    {
      $ret = "P120";
    }
    return $ret;
  }
}



FUNCTION add_str2($strVal)
{
	$strVal = @addslashes(@trim($strVal));

	return $strVal;
}



function get_term_days($start_date, $month, $days) {

	$datetime1 = new DateTime($start_date);
	if ($days) $end_date = date("Y-m-d" , strtotime( $days.' day', strtotime($start_date)));
	else $end_date = date("Y-m-d" , strtotime( $month.' month', strtotime($start_date)));
	$datetime2 = new DateTime($end_date);
	$diff = $datetime1->diff($datetime2);
	return $diff->days;

}

function fn_log_up($log_idx, $rcvJson, $httpcode, $thrSec) {

	if (!$log_idx) return;
	if ($apiNo=="4.4.1") {
		$up_sql = "UPDATE p2pctr_inquiry_log
					  SET api_trx_no    = '".$api_trx_no."',
						  rcvJson       = '".add_str2($rcvJson)."',
						  thrSec        = '".$thrSec."',
						  rcv_http_code = '".$httpcode."'
					WHERE idx='$log_idx'
		";
	} else {
		$up_sql = "UPDATE p2pctr_request_log
					  SET rcvJson       = '".add_str2($rcvJson)."',
						  thrSec        = '".$thrSec."',
						  rcv_http_code = '".$httpcode."',
						  mod_datetime  = NOW()
					WHERE idx='$log_idx'
		";
	}
	sql_query($up_sql);
}

FUNCTION fn_log($apiNo, $apiTitle, $mb_no, $toUrl, $reqJson, $rcvJson, $httpcode, $thrSec, $api_trx_no="" , $product_idx=0, $member_idx=0)
{
    global $link;
    // rdate 등록일, rtime 등록시간 apiNo api번호  apiTitle api타이틀 mb_no  발송회원id,
    // toUrl 목적지상세주소  reqJson 발송시 데이터 , revJson 수신결과데이터 thrSec 소유시간

	if ($apiNo=="4.4.1") {
		//if ($httpcode<>"200") {

	    $Query = "
				INSERT INTO
					p2pctr_inquiry_log
				SET
					member_idx    = '".$member_idx."',
					product_idx   = '".$product_idx."',
					api_trx_no    = '".$api_trx_no."',
					rdate         = '".date('Y-m-d')."',
					rtime         = '".date('H:i:s')."',
					apiNo         = '".$apiNo."',
					apiTitle      = '".add_str2($apiTitle)."',
					mb_no         = '".$mb_no."',
					toUrl         = '".add_str2($toUrl)."',
					reqJson       = '".add_str2($reqJson)."',
					rcvJson       = '".add_str2($rcvJson)."',
					thrSec        = '".$thrSec."',
					ip            = '".$_SERVER['REMOTE_ADDR']."',
					rcv_http_code = '".$httpcode."'";

		//}
	}
	else {

			$Query = "
				INSERT INTO
					p2pctr_request_log
				SET
					api_trx_no    = '".$api_trx_no."',
					product_idx   = '".$product_idx."',
					member_idx    = '".$member_idx."',
					rdate         = '".date('Y-m-d')."',
					rtime         = '".date('H:i:s')."',
					apiNo         = '".$apiNo."',
					apiTitle      = '".add_str2($apiTitle)."',
					mb_no         = '".$mb_no."',
					toUrl         = '".add_str2($toUrl)."',
					reqJson       = '".add_str2($reqJson)."',
					rcvJson       = '".add_str2($rcvJson)."',
					thrSec        = '".$thrSec."',
					ip            = '".$_SERVER['REMOTE_ADDR']."',
					admin_id      = '".add_str2($_SESSION['ss_mb_id'])."',
					rcv_http_code = '".$httpcode."',
					insert_datetime = NOW()";

	}

	sql_query($Query);

	return sql_insert_id();
}


function get_brw_info($member_idx) {
	$sql = "
		SELECT member_type, mb_name, corp_num, mb_co_name, mb_co_reg_num
		FROM g5_member
		WHERE mb_no='$member_idx' AND mb_level BETWEEN 1 AND 5";
	$row = sql_fetch($sql);

	$ret = array();

	if ($row['member_type']=="2") { // 법인
		$ret['brw_idno'] = $row['corp_num'];
		$ret['brw_name'] = $row['mb_co_name'];
		$ret['business_register_no'] = $row['mb_co_reg_num'];
		$ret['brw_type'] = "B300";
	} else {

		//$csql = "SELECT * FROM cf_chaju WHERE mb_no = '$member_idx'";
		//$crow = sql_fetch($csql);
		//$ret['brw_idno'] = $crow['psnl_num1'].$crow['psnl_num2'];

		$ret['brw_idno'] = getJumin($member_idx);
		$ret['brw_name'] = $row['mb_name'];
		$ret['brw_type'] = "B100";
	}

	return $ret;
}

function get_inv_info($member_idx) {

	// I110 일반개인투자자
	// I120 소득적격투자자 개인
	// I130 개인전문투자자
	// I310 법인투자자
	// I320 여신금융기관 법인
	// I330 P2P온투업

	$sql = "
		SELECT
			member_type, member_investor_type, mb_name, corp_num, mb_co_name, mb_co_reg_num
		FROM
			g5_member
		WHERE	1
			AND mb_no = '".$member_idx."' AND mb_level BETWEEN 1 AND 5";
	$row = sql_fetch($sql);

	$ret = array();

	if ($row['member_type']=='2') {			// 법인
		$ret['inv_idno'] = $row['corp_num'];
		$ret['inv_name'] = $row['mb_co_name'];
		$ret['business_register_no'] = $row['mb_co_reg_num'];
		$ret['inv_type'] = 'I310';
	}
	else {			// 개인
		if ($row['member_investor_type']=='1') {					// 일반개인투자자
			$ret['inv_idno'] = getJumin($member_idx);
			$ret['inv_name'] = $row['mb_name'];
			$ret['inv_type'] = 'I110';
		}
		else if ($row['member_investor_type']=='2') {			// 소득적격투자자 개인
			$ret['inv_idno'] = getJumin($member_idx);
			$ret['inv_name'] = $row['mb_name'];
			$ret['inv_type'] = 'I120';
		}
		else if ($row['member_investor_type']=='3') {			// 개인전문투자자
			$ret['inv_idno'] = getJumin($member_idx);
			$ret['inv_name'] = $row['mb_name'];
			$ret['inv_type'] = 'I130';
		}
	}

	return $ret;
}

function get_repay_info($product_idx) {

	$tbl_bill = getBillTable($product_idx);

	$sql = "SELECT turn, repay_date FROM $tbl_bill WHERE product_idx='$product_idx' GROUP BY turn ORDER BY turn";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$ret = array();

	for ($i=0 ; $i<$cnt ; $i++) {

		$row = sql_fetch_array($res);

		$sum_interest = 0;
		$sql2 = "SELECT * FROM $tbl_bill WHERE product_idx='$product_idx' AND turn='".$row['turn']."'";
		$res2 = sql_query($sql2);
		$cnt2 = sql_num_rows($res2);
		for($j=0 ; $j<$cnt2 ; $j++) {
			$row2 = sql_fetch_array($res2);
			$day_inter = floor(customRoundOff($row2['day_interest']));
			$sum_interest = $sum_interest + $day_inter;
		}

		$ret[$i]['repay_num'] = $row['turn'];
		$ret[$i]['repay_date'] = preg_replace('/[^0-9]/','', $row['repay_date']);
		$ret[$i]['schd_p_amount'] = 0;
		$ret[$i]['schd_interest'] = $sum_interest;  // 이자
		$ret[$i]['schd_fee_etc'] = $row['sum_fee'];
	}

	return $ret;

}

function get_repay_info11($product_idx) {
	$tbl_bill = getBillTable($product_idx);

	$sql = "SELECT turn, repay_date, SUM(day_interest) sum_interest, SUM(fee) sum_fee FROM $tbl_bill WHERE product_idx='$product_idx' GROUP BY turn ORDER BY turn";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$ret = array();
	//$ret['repay_count'] = $cnt;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		$ret[$i]['repay_num'] = $row['turn'];
		$ret[$i]['repay_date'] = preg_replace('/[^0-9]/','', $row['repay_date']);
		$ret[$i]['schd_p_amount'] = 0;
		$ret[$i]['schd_interest'] = $row['sum_interest'];  // 이자
		$ret[$i]['schd_fee_etc'] = $row['sum_fee'];
	}

	return $ret;
}


///////////////////////////////////////////////////
// 잔액조회를 이용한 잔여한도 추출
///////////////////////////////////////////////////
function get_p2pctr_limit($mb_id, $product_idx="") {

	if (!$mb_id) return;

	global $p2p_host;

	$apiNo = "4.4.1";
	$apiTitle = "투자잔액 조회";

	$url  = $p2p_host . "investments/inquiry";
	$method = "POST";

	if ($product_idx) {
		$psql = "SELECT idx, recruit_amount, loan_mb_no FROM cf_product WHERE idx = '".$product_idx."'";
		$prow = sql_fetch($psql);
		$product_idx = ($prow['idx']) ? $prow['idx'] : '';
	}

	$sql = "SELECT mb_no, member_type, member_investor_type FROM g5_member WHERE mb_id='".$mb_id."' AND mb_level BETWEEN 1 AND 5";
	$row = sql_fetch($sql);
	if (!$row['mb_no']) return;
	$mno = $row['mb_no'];

	if($row['member_type']=='1') {
		if($row['member_investor_type']=='1') {          // 일반투자자
			$LIMIT_ALL = 30000000;  // 전체
			$LIMIT_IMV = 10000000;  // 부동산
			$LIMIT_BRW =  5000000;  // 동일차주
		}
		else if($row['member_investor_type']=='2') {   // 소득적격 투자자
			$LIMIT_ALL = 100000000;
			$LIMIT_IMV = 100000000;
			$LIMIT_BRW =  20000000;

		}
		else if($row['member_investor_type']=='3') {   // 전문투자자
			$LIMIT_ALL = 999999999999;
			$LIMIT_IMV = 999999999999;
			$LIMIT_BRW = 40 * 0.01;
		}
	}
	else {
		$LIMIT_ALL = 999999999999;
		$LIMIT_IMV = 999999999999;
		$LIMIT_BRW = 40 * 0.01;
	}

	$inv_info = get_inv_info($mno);
	if($product_idx) $brw_info = get_brw_info($prow['loan_mb_no']);


	$data = array();
	$data['investor_identity_no'] = $inv_info['inv_idno'];
	if ($brw_info['brw_idno']) $data['borrower_identity_no'] = $brw_info['brw_idno'];

	//echo "<pre>"; print_r($data); echo "</pre>";

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $mno);
	//if($mb_id=='sori9th') print_rr($curl_res);
	$resj = json_decode($curl_res['body'] , true);

	$ret = array();

	$LIVE_INVEST_AMT['ALL'] = 0;		// 전체 투자잔액
	$LIVE_INVEST_AMT['BDS'] = 0;		// 부동산 PF + 담보 잔액
	$LIVE_INVEST_AMT['DS']  = 0;		// 동산 + 매출채권
	$LIVE_INVEST_AMT['CL']  = 0;		// 신용대출

	if ($resj['rsp_code'] == "A0000") {

		//$bd = json_decode($curl_res['body'] , true);
		//echo "<pre>"; print_r($resj); echo "</pre>";

		for($i=0 ; $i<count($resj['goods_balance_list']); $i++) {

			if( $resj['goods_balance_list'][$i]['goods_type']=='P000' ) {
				$LIVE_INVEST_AMT['ALL'] = $resj['goods_balance_list'][$i]['balance'];
			}
			else {
				if( in_array($resj['goods_balance_list'][$i]['goods_type'], array('P110', 'P120')) ) {
					 $LIVE_INVEST_AMT['BDS'] += $resj['goods_balance_list'][$i]['balance'];
				}
				else if( in_array($resj['goods_balance_list'][$i]['goods_type'], array('P210', 'P220')) ) {
					 $LIVE_INVEST_AMT['DS'] += $resj['goods_balance_list'][$i]['balance'];
				}
				else if( in_array($resj['goods_balance_list'][$i]['goods_type'], array('P230', 'P240')) ) {
					$LIVE_INVEST_AMT['CL'] += $resj['goods_balance_list'][$i]['balance'];
				}
			}

		}

		//echo number_format($LIMIT_ALL) . "\n";

		$ret['ALL_LIMIT'] =	$LIMIT_ALL - $LIVE_INVEST_AMT['ALL'];															// 전체 잔여한도 (전체투자한도 - 전체투자잔액)
		$ret['IMV_LIMIT'] = $LIMIT_IMV - $LIVE_INVEST_AMT['BDS'];															// 부동산 잔여한도 (부동산투자한도 - 부동산투자잔액)
		$ret['MV_LIMIT']  = $ret['ALL_LIMIT'];		// 동산/매출채권/신용대출 잔여한도

/*
		$ret['ALL_LIMIT'] =	$LIVE_INVEST_AMT['ALL'];															// 전체 잔여한도
		$ret['IMV_LIMIT'] = $LIVE_INVEST_AMT['BDS'];															// 부동산 잔여한도
		$ret['MV_LIMIT']  = ($LIVE_INVEST_AMT['DS'] + $LIVE_INVEST_AMT['CL']);		// 동산/매출채권/신용대출 잔여한도
*/

		if($product_idx) {
			if($LIMIT_BRW < 100) {
				$ret['BRW_LIMIT'] = (ceil($prow['recruit_amount'] * $LIMIT_BRW / 10000) * 10000) - $resj['balance_per_borrower'];
			}
			else {
				$ret['BRW_LIMIT'] = $LIMIT_BRW - $resj['balance_per_borrower'];
			}
		}

	}


	//echo "<pre>"; print_r($ret); echo "</pre>";
	return $ret;

}

function p2pctr_invest_register($member_idx, $product_idx) {

	if (!$member_idx || !$product_idx) return false;

	global $p2p_host;

	$psql = "SELECT goods_id FROM cf_product WHERE idx='$product_idx'";
	$prow = sql_fetch($psql);

	if (!$prow['goods_id']) return;

	$goods_id   = $prow['goods_id'];
	$goods_type = get_goods_type($product_idx);


	$sqli = "SELECT * FROM cf_product_invest WHERE invest_state='Y' AND product_idx='$product_idx' AND member_idx='".$member_idx."'";
	$rowi = sql_fetch($sqli);

	if (!$rowi['idx']) return;

	// 기등록 투자 데이터 취소처리 -------------------------------------------------------
	if ($rowi['investment_register_id']) {
		$canc_res = p2pctr_invest_register_canc($member_idx, $product_idx, $rowi['idx']);

		// canc_res 로 인해 다시 읽어옮
		$sqli = "SELECT * FROM cf_product_invest WHERE invest_state='Y' AND product_idx='$product_idx' AND member_idx='".$member_idx."'";
		$rowi = sql_fetch($sqli);
	}
	// 기등록 투자 데이터 취소처리 -------------------------------------------------------


	$apiNo = "4.4.2";
	$apiTitle = "투자신청 기록";
	$url = $p2p_host . "investments/register";
	$method = "POST";

	$data['investment_register_info'] = array();
	$data['investor_info'] = array();
	$data['goods_info'] = array();

	$mode = "send";
	$investment_register_id = $rowi['investment_register_id'];
	if (!$investment_register_id AND $mode=="send") $investment_register_id = get_new_id("investment_register_id");

	$data['investment_register_info']['investment_register_id'] = $investment_register_id;
	$data['investment_register_info']['bank_inquiry_id'] = $rowi['prin_rcv_no']; 								// 대입변수 수정 : 2022-03-18 배부장   기존: $data['investment_register_info']['bank_inquiry_id'] = $investment_register_id;
	$data['investment_register_info']['investment_amount'] = (int)$rowi['amount'];
	$data['investment_register_info']['investment_register_dtm'] = preg_replace('/[^0-9]/','', $rowi['insert_date']).preg_replace('/[^0-9]/','', $rowi['insert_time']);
	$data['investment_register_info']['status'] = "T100"; // T100 투자신청중
	$data['investment_register_info']['investments_document_info']['document_confirm_date'] = preg_replace('/[^0-9]/','', $rowi['insert_date']);
	$data['investment_register_info']['investments_document_info']['document_type'] = "DP99"; // DP01  (전자문서 형식의 파일)  DP99  (전자문서 이외의 파일)

	$inv_info = get_inv_info($member_idx);
	$data['investor_info']['identity_no'] = $inv_info['inv_idno'];
	$data['investor_info']['name'] = $inv_info['inv_name'];
	$data['investor_info']['type'] = $inv_info['inv_type'];

	if (substr($data['investor_info']['type'] , 0 , 2) == "I3") $data['investor_info']['business_register_no'] = $inv_info['business_register_no'];
	$data['goods_info']['goods_id'] = $goods_id;
	$data['goods_info']['goods_type'] = $goods_type;


	if ($mode=="send" AND !$rowi['investment_register_id']) {

		$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data , $product_idx, $member_idx);
		$resj = json_decode($curl_res['body'] , true);

		// 전송 성공시
		if($resj['rsp_code'] == "A0000") {

			$up_sql = "
				UPDATE
					cf_product_invest
				SET
					investment_register_id = '$investment_register_id',
					bank_inquiry_id = '".$data['investment_register_info']['bank_inquiry_id']."'
				WHERE
					idx = '".$rowi['idx']."' AND investment_register_id='' ";

			sql_query($up_sql);
			return true;

		}
		else {

			return false;

		}

	}

}

function p2pctr_invest_register_canc($member_idx, $product_idx, $invest_idx) {

	if (!$member_idx || !$product_idx || !$invest_idx) return false;

	global $p2p_host;

	$sqli = "SELECT * FROM cf_product_invest WHERE product_idx = '$product_idx' AND member_idx = '$member_idx' AND idx = '$invest_idx'";
	$rowi = sql_fetch($sqli);

	if (!$rowi['idx']) return;
	if (!$rowi['investment_register_id']) return;

	$apiNo = "4.4.3";
	$apiTitle = "투자신청 갱신";
	$url = $p2p_host . "investments/register/".$rowi['investment_register_id'];
	$method = "PUT";

	$cdata = array();
	$cdata['status'] = "T150";

	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url, $method, $cdata, $product_idx, $member_idx);
	$resj = json_decode($curl_res['body'] , true);


	$return_value = false;

	if($resj['rsp_code'] == "A0000") {

		$return_value = true;

		// 중앙기록 투자등록ID 플래그 초기화
		$up_sql = "
			UPDATE
				cf_product_invest
			SET
				investment_register_id = '',
				bank_inquiry_id = ''
			WHERE
				idx = '".$rowi['idx']."'";
		sql_query($up_sql);

	}

	return $return_value;

}
?>