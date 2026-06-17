<?
$p2p_host_test = "https://testapi.p2pcenter.or.kr/v1.0/";
$p2p_host_egan = "https://testapi.p2pcenter.or.kr/data/";


//$p2p_host      = "https://testapi.p2pcenter.or.kr/v1.0/";
//$hello_code    = "M202112431";
//$client_id     = "90b279ba-48ff-42bb-b2e0-6363626c7941";
//$client_secret = "e86f44f4-8f04-4aa6-b2ab-d15aa7c02192";

$p2p_host      = "https://openapi.p2pcenter.or.kr/v1.0/";
$hello_code    = "K210500031";
$client_id     = "2420bb6b-ce33-4756-ba9f-8bf6fc71fa9e";
$client_secret = "3900b895-df4c-47df-8df4-1d6700392f3d";

$p2p_host = $p2p_host."data/";


$scope = "p2pbiz";
$grant_type = "client_credentials";

$access_token = get_access_token();
?>
<?
function curl_p2pctr($url , $method , $data , $headers) {
	return;
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
	$ret["http_code"] = $http_code;
	$ret["head"] = $header;
	$ret["body"] = $body;
	$ret["req_body"] = $json_data;

	return $ret;
}

function get_access_token_back() {
	$sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);


	$row = sql_fetch_array($res);

	return $row["access_token"];
}

function get_access_token() {

	global $p2p_host;
	global $client_id , $client_secret , $scope , $grant_type;
	global $mb_no;

	$sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$row = sql_fetch_array($res);
	$access_token = $row["access_token"];


	if ($row["token_limit_time"]<=date("Y-m-d H:i:s")) {

		$intStime = time();

		$url = $p2p_host."oauth/2.0/token";
		$headers = ['Content-Type: application/x-www-form-urlencoded; charset=UTF-8'];
		$method = "POST";

		$data = array();
		$data["client_id"] = $client_id;
		$data["client_secret"] = $client_secret;
		$data["scope"] = $scope;
		$data["grant_type"] = $grant_type;

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

		$access_token = $res_arr["access_token"];
		$today = time();
		$exday = $today+$res_arr["expires_in"];
		$exday2 = date("Y-m-d H:i:s",$exday);

		$ch_sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
		$ch_res = sql_query($ch_sql);
		$ch_cnt = sql_num_rows($ch_res);

		if ($ch_cnt) {
			$ch_row = sql_fetch_array($ch_res);
			$up_sql = "UPDATE p2pctr_order_no SET access_token='".$res_arr["access_token"]."' , token_limit_time='".$exday2."' WHERE idx='$ch_row[idx]'";
			sql_query($up_sql);
		} else {
			$ins_sql = "INSERT INTO p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$res_arr["access_token"]."', token_limit_time='".$exday2."'";
			sql_query($ins_sql);
		}

	}

	return $access_token;

}

function get_p2pord_no() {

	//$hello_code = "M202112431";
	global $hello_code;

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	if ($cnt) {

		$row = sql_fetch_array($res);

		$no = $row["ordno"]+1;

		$up_sql = "UPDATE p2pctr_order_no SET ordno='$no' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else {

		$no = 1;

		$old_sql = "SELECT * FROM p2pctr_order_no ORDER BY ymd DESC LIMIT 1";
		$old_res = sql_query($old_sql);
		$old_row = sql_fetch_array($old_res);

		$ins_sql = "INSERT p2pctr_order_no SET ymd='".date("Ymd")."' , ordno='$no' , access_token='".$old_row["access_token"]."' ,token_limit_time='".$old_row["token_limit_time"]."'";
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

	if ($gbn=="loan_register_id") $GB="LR";	 // 대출신청 기록 ID
	else if ($gbn=="goods_id") $GB="GR";	 // 대출신청 기록 ID
	else if ($gbn=="investment_register_id") $GB="IR";	 // 대출신청 기록 ID
	else if ($gbn=="loan_contract_id") $GB="LC";	 // 대출신청 기록 ID
	else if ($gbn=="contract_id") $GB = "IC";

	$day_serial = get_date_serial($gbn);

	$new_id = $hello_code."_".date("ymd")."_".$GB."_".str_pad($day_serial , 10, "0", STR_PAD_LEFT);

	return $new_id;
}

function get_date_serial($gbn) {

	chk_odno_row();

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";

	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$row = sql_fetch_array($res);


	if ($gbn=="loan_register_id") {
		$ret = $row["loan_register_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_register_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="goods_id") {
		$ret = $row["goods_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET goods_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="investment_register_id") {
		$ret = $row["investment_register_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET investment_register_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="loan_contract_id") {  // 대출 계약 번호
		$ret = $row["loan_contract_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET loan_contract_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	} else if ($gbn=="contract_id") {  // 투자 계약 번호
		$ret = $row["contract_no"]+1;
		$up_sql = "UPDATE p2pctr_order_no SET contract_no='$ret' WHERE idx='$row[idx]'";
		sql_query($up_sql);

	}

	return $ret;
}

function chk_odno_row() {

	$sql = "SELECT * FROM p2pctr_order_no WHERE ymd='".date("Ymd")."'";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	if (!$cnt) {
		$old_sql = "SELECT * FROM p2pctr_order_no WHERE ymd<'".date("Ymd")."' ORDER BY ymd DESC LIMIT 1";
		$old_res = sql_query($old_sql);
		$old_row = sql_fetch_array($old_res);


		$ins_sql = "INSERT p2pctr_order_no SET ymd='".date("Ymd")."' , access_token='".$old_row["access_token"]."' ,token_limit_time='".$old_row["token_limit_time"]."'";
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

	if ($row["category"]=="1") {  // 동산
		$ret = "P220";
	} else if ($row["category"]=="2") {  // 부동산
		if ($row["mortgage_guarantees"]=="1") $ret = "P120";
		else $ret = "P110";
	} else if ($row["category"]=="3") {  // 매출채권
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

    if($row["status"] == "2") $ret = "T210";
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
    $Query = "SELECT idx, goods_id FROM p2pctr_product WHERE product_idx='".add_str($product_idx)."'";

    $Result = sql_query($Query);

    $goods_id = "";
    $idx = 0;
    IF($Row=sql_fetch_array($Result))
    {
        $idx       =  $Row["idx"];
        $goods_id  =  $Row["goods_id"];
        sql_free_result($Result);
    }
    IF(!$goods_id)
    {
      $goods_id = $this->register_id();

      IF($idx > 0)
      {
        $Query = "UPDATE p2pctr_product SET goods_id='".add_str($goods_id)."', reg_date=now(),logidx=0 WHERE product_idx='".add_str($product_idx)."'";
      } ELSE {
        $Query = "INSERT INTO p2pctr_product
                 (request_idx, loan_register_id, loan_contract_id, goods_id, reg_date, logidx, product_idx,loan_contract_amount, status, loan_term_days)
                 VALUES
                 (0,'','','".add_str($goods_id)."',now(),0,'".$product_idx."',0,'',0)";
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
                  FROM cf_product t1 LEFT JOIN p2pctr_product t2 ON t1.idx=t2.product_idx WHERE t1.idx='".add_str($idx)."') t1";
	*/
    $strTable = "(SELECT t1.category, t1.mortgage_guarantees, t1.recruit_amount, t1.state, t1.start_date,
                         t1.end_date,t1.invest_end_date,t1.loan_end_date,t1.loan_start_date, IFNULL(t2.goods_id,'') as goods_id ,
                         t1.title, t1.loan_interest_rate, t1.invest_return, IFNULL(t2.loan_register_id,'') as loan_register_id ,IFNULL(t2.loan_contract_id,'') as loan_contract_id
                  FROM cf_product t1 LEFT JOIN p2pctr_product t2 ON t1.idx=t2.product_idx WHERE t1.idx='".add_str($idx)."') t1";
    $strTable = "(SELECT t1.category, t1.mortgage_guarantees, t1.recruit_amount, t1.state, t1.start_date,
                         t1.end_date,t1.invest_end_date,t1.loan_end_date,t1.loan_start_date, t1.goods_id as goods_id ,
                         t1.title, t1.loan_interest_rate, t1.invest_return, t1.loan_register_id as loan_register_id ,t1.loan_contract_id as loan_contract_id
                  FROM cf_product t1 WHERE t1.idx='".add_str($idx)."') t1";

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
    $Query .= " FROM ".$strTable." WHERE product_idx='".add_str($product_idx)."'";

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

FUNCTION add_str($strVal)
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

FUNCTION fn_log($apiNo, $apiTitle, $mb_no, $toUrl, $reqJson, $rcvJson, $httpcode, $thrSec)
{
    global $link;
    // rdate 등록일, rtime 등록시간 apiNo api번호  apiTitle api타이틀 mb_no  발송회원id,
    // toUrl 목적지상세주소  reqJson 발송시 데이터 , revJson 수신결과데이터 thrSec 소유시간
    $Query = "INSERT INTO p2pctr_request_log
                          (rdate,rtime,apiNo,apiTitle,mb_no, toUrl,
                           reqJson, rcvJson, thrSec, ip, rcv_http_code)
                          values
                          ('".DATE("Y-m-d")."','".DATE("H:i:s")."','".add_str($apiNo)."','".add_str($apiTitle)."','".add_str($mb_no)."','".add_str($toUrl)."',
                           '".add_str($reqJson)."','".add_str($rcvJson)."','".add_str($thrSec)."','".$_SERVER["REMOTE_ADDR"]."','".add_str($httpcode)."'
                          )";

    sql_query($Query);

    return SQL_INSERT_ID();
}

function get_brw_info($member_idx) {
	$sql = "SELECT member_type,
				   mb_name,
				   corp_num, mb_co_name, mb_co_reg_num
			  FROM g5_member
			 WHERE mb_no='$member_idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret = array();

	if ($row["member_type"]=="2") { // 법인
		$ret["brw_idno"] = $row["corp_num"];
		$ret["brw_name"] = $row["mb_co_name"];
		$ret["business_register_no"] = $row["mb_co_reg_num"];
		$ret["brw_type"] = "B300";
	} else {

		//$csql = "SELECT * FROM cf_chaju WHERE mb_no = '$member_idx'";
		//$crow = sql_fetch($csql);
		//$ret["brw_idno"] = $crow["psnl_num1"].$crow["psnl_num2"];

		$ret["brw_idno"] = getJumin($member_idx);
		$ret["brw_name"] = $row["mb_name"];
		$ret["brw_type"] = "B100";
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

	$sql = "SELECT member_type, member_investor_type,
				   mb_name,
				   corp_num, mb_co_name, mb_co_reg_num
			  FROM g5_member
			 WHERE mb_no='$member_idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);

	$ret = array();

	if ($row["member_type"]=="2") { // 법인

		$ret["inv_idno"] = $row["corp_num"];
		$ret["inv_name"] = $row["mb_co_name"];
		$ret["business_register_no"] = $row["mb_co_reg_num"];
		$ret["inv_type"] = "I310";

	} else {  // 개인

		if ($row["member_investor_type"]=="1") {  // 일반개인투자자
			$ret["inv_idno"] = getJumin($member_idx);
			$ret["inv_name"] = $row["mb_name"];
			$ret["inv_type"] = "I110";

		} else if ($row["member_investor_type"]=="2") {  // 소귿적격투자자 개인
			$ret["inv_idno"] = getJumin($member_idx);
			$ret["inv_name"] = $row["mb_name"];
			$ret["inv_type"] = "I120";

		} else if ($row["member_investor_type"]=="3") {  // 개인전문투자자
			$ret["inv_idno"] = getJumin($member_idx);
			$ret["inv_name"] = $row["mb_name"];
			$ret["inv_type"] = "I130";
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
		$sql2 = "SELECT * FROM $tbl_bill WHERE product_idx='$product_idx' AND turn='$row[turn]'";
		$res2 = sql_query($sql2);
		$cnt2 = sql_num_rows($res2);
		for($j=0 ; $j<$cnt2 ; $j++) {
			$row2 = sql_fetch_array($res2);
			$day_inter = floor(customRoundOff($row2['day_interest']));
			$sum_interest = $sum_interest + $day_inter;
		}

		$ret[$i]["repay_num"] = $row["turn"];
		$ret[$i]["repay_date"] = preg_replace('/[^0-9]/','', $row["repay_date"]);
		$ret[$i]["schd_p_amount"] = 0;
		$ret[$i]["schd_interest"] = $sum_interest;  // 이자
		$ret[$i]["schd_fee_etc"] = $row["sum_fee"];
	}

	return $ret;

}

function get_repay_info11($product_idx) {
	$tbl_bill = getBillTable($product_idx);

	$sql = "SELECT turn, repay_date, SUM(day_interest) sum_interest, SUM(fee) sum_fee FROM $tbl_bill WHERE product_idx='$product_idx' GROUP BY turn ORDER BY turn";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$ret = array();
	//$ret["repay_count"] = $cnt;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		$ret[$i]["repay_num"] = $row["turn"];
		$ret[$i]["repay_date"] = preg_replace('/[^0-9]/','', $row["repay_date"]);
		$ret[$i]["schd_p_amount"] = 0;
		$ret[$i]["schd_interest"] = $row["sum_interest"];  // 이자
		$ret[$i]["schd_fee_etc"] = $row["sum_fee"];
	}

	return $ret;
}
?>