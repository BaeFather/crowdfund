<?

include_once("_common.php");

///////////////////////////////////////////////////////////////////////////////
// CURL 전송 : $url 파라미터에 도메인주소를 직접 입력
///////////////////////////////////////////////////////////////////////////////
function curlWFL($data=array(), $headers='', $returnType='')
{
	global $g5;
	global $_CONF;
	global $member;

	$method     = 'POST';
	$tmpLog     = false;
	$url        = "http://10.22.162.37:8080";
	$detail_url = "/view/AML/common/include/HELLOPP/interface/wlf.jsp";

	$ret = array();

	if(!$headers) {
		$headers = [
			'Content-Type: application/json; charset=UTF-8'
		];
	}

	$url = $url . '/' . $detail_url;

	//////////////////////
	// 로그 기록 시작
	//////////////////////
	$json_data = json_encode($data, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

	$title = '나이스본인인증';

	if($tmpLog) {
		$logSql = "
			INSERT INTO
				curl_wlf_request_log
			SET
				ip      = '".$_SERVER['REMOTE_ADDR']."',
				title   = '".$title."',
				path    = '".$url."',
				referer = '".$_SERVER['HTTP_REFERER']."',
				input   = '".$json_data."',
				mb_id   = '".$member['mb_id']."',
				rdate   = NOW()";
		sql_query($logSql);
		$log_id = sql_insert_id();		// 로그ID
	}


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	if($method=="POST") { // POST default
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
	//curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}
	else {
		$get_data = http_build_query($data,'','&');
		$url = $url."?".$get_data;
	}

	curl_setopt($ch, CURLOPT_URL, $url);
	$result = curl_exec($ch);

	$http_code   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$header      = substr($result, 0, $header_size);
	$body        = substr($result, $header_size);

	curl_close($ch);


	$ret['http_code'] = $http_code;
	$ret['head']      = $header;
	$ret['body']      = $body;  //json_decode($body, true);
	$ret['req_url']   = $url;

	//////////////////////
	// 로그 기록 마무리
	//////////////////////
	if($tmpLog) {
		$result_json = json_encode($ret['body'],JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

		$logSql = "UPDATE curl_wlf_request_log SET output = '".addSlashes($result_json)."', edate = NOW() WHERE idx = '".$log_id."'";
		sql_query($logSql);

		$logDelSql = "DELETE FROM curl_wlf_request_log WHERE rdate <= '".date('Y-m-d H:i:s', strtotime('-2 hour'))."'";
		sql_query($logDelSql);
	}

	if($returnType=='json') {
		$value = json_encode($ret['body'], JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);		// 결과값을 json 으로 받기 원할 경우
	}
	else {
		$value = $ret['body'];
	}

	return $value;

}


?>