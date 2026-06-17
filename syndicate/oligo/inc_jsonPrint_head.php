<?

header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$inputJSON = file_get_contents('php://input');
$inputJSON = (iconv("utf-8", "utf-8", $inputJSON)==$inputJSON) ? $inputJSON : iconv("euc-kr", "utf-8", $inputJSON);
//echo "inputJSON: " . $inputJSON; echo "\n\n";

$request_uri = $_CONF['domain_for_syndi'] . $_SERVER['REQUEST_URI'];
$input = "curl -X POST -k -s -H 'Expect:' -H 'Content-Type:application/json' -A 'Mozilla/5.0' -d '".$inputJSON."' " . $request_uri;

// 로그기록 시작 --------------------------------------
$logRecord = false;
if($logRecord) {
	$sql = "
		INSERT INTO
			{$LOG['table']}
		SET
			ip    = '".$_SERVER['REMOTE_ADDR']."',
			title = '".$LOG['title']."',
			path  = '".$_SERVER['REQUEST_URI']."',
			input = '".addSlashes($input)."',
			rdate = SYSDATE()";
	//echo $sql."\n";
	$res = sql_query($sql);
	$LOG['idx'] = sql_insert_id();
}
// 로그기록 시작 --------------------------------------

$OBJ = json_decode($inputJSON, true);
//print_r($OBJ); echo "\n\n";
if( !@is_array($OBJ) ) { $ARR = array("code"=>"9999", "msg"=>"전송데이터 없슴"); echo printJson($ARR); exit; }

while(list($k, $v) = each($OBJ)) {

	$REQUEST[$k] = $v;

	if(is_array($REQUEST[$k])) {
		foreach($REQUEST[$k] as $k2=>$v2) {
			if(is_array($REQUEST[$k][$k2])) {
				foreach($REQUEST[$k][$k2] as $k3=>$v3) {
					$REQUEST[$k][$k2][$k3] = trim($v3);
				}
			}
			else {
				$REQUEST[$k][$k2] = trim($v2);
			}
		}
	}
	else {
		$REQUEST[$k] = trim($REQUEST[$k]);
	}

}

//echo "REQUEST: "; print_r($REQUEST); echo "\n\n"; exit;

if(isset($REQUEST['ci']) && $REQUEST['ci']!='') {
	if(strlen(urldecode($REQUEST['ci'])) < 88) { $ARR = array("code"=>"9999", "msg"=>"CI오류"); echo printJson($ARR); exit; }		// ci길이 체크. 실제 ci길이는 88글자임
}

$ARR = array();

/*
prompt: curl -X POST -H "Content-Type:application/json" -d "{\"user_nm\":\"배재수\",\"jumin_no\":\"7509031114220\"}" https://www.hellofunding.co.kr/external/api/checkJuminNo.do
Shell : curl -X POST -H "Content-Type:application/json" -d '{"user_nm":"배재수","jumin_no":"7509031114220"}' https://www.hellofunding.co.kr/external/api/checkJuminNo.do

prompt: curl -X POST -H "Content-Type:application/json" -d "{\"comp_cd\":\"xxxxxxxxxxxx\",\"call_cd\":\"I\",\"prod_list\":[{\"prod_cd\":\"xxxxxxx\",\"prod_nm\":\"상품명 정보 n호\",\"status\":\"01\",\"open_dt\":\"20180701\",\"close_dt\":\"20180701\",\"tot_amt\":\"1000000000\",\"rate\":\"10.0\",\"inve_term_kn\":\"M\",\"inve_term\":\"8\",\"inve_num\":\"80\",\"inve_amt\":\"0\",\"inve_rate\":\"10\",\"img_url\":\"https://cdn.oligo.kr/img/aa.png\",\"prod_url\":\"https://oligo.kr/product/10\",\"view_yn\":\"Y\"},{\"prod_cd\":\"xxxxxxx\",\"prod_nm\":\"상품명 정보, n호\",\"status\":\"01\",\"open_dt\":\"20180701\",\"close_dt\":\"20180701\",\"tot_amt\":\"1000000000\",\"rate\":\"10.0\",\"inve_term_kn\":\"M\",\"inve_term\":\"8\",\"inve_num\":\"80\",\"inve_amt\":\"0\",\"inve_rate\":\"10\",\"img_url\":\"https://cdn.oligo.kr/img/aa.png\",\"prod_url\":\"https://oligo.kr/product/10\",\"view_yn\":\"Y\"}]}" https://www.hellofunding.co.kr/external/api/checkJuminNo.do
Shell : curl -X POST -H "Content-Type:application/json" -d '{"comp_cd":"xxxxxxxxxxxx","call_cd":"I","prod_list":[{"prod_cd":"xxxxxxx","prod_nm":"상품명 정보 n호","status":"01","open_dt":"20180701","close_dt":"20180701","tot_amt":"1000000000","rate":"10.0","inve_term_kn":"M","inve_term":"8","inve_num":"80","inve_amt":"0","inve_rate":"10","img_url":"https://cdn.oligo.kr/img/aa.png","prod_url":"https://oligo.kr/product/10","view_yn":"Y"},{"prod_cd":"xxxxxxx","prod_nm":"상품명 정보, n호","status":"01","open_dt":"20180701","close_dt":"20180701","tot_amt":"1000000000","rate":"10.0","inve_term_kn":"M","inve_term":"8","inve_num":"80","inve_amt":"0","inve_rate":"10","img_url":"https://cdn.oligo.kr/img/aa.png","prod_url":"https://oligo.kr/product/10","view_yn":"Y"}]}' https://www.hellofunding.co.kr/external/api/checkJuminNo.do
*/
?>