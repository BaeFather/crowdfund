<?
include_once('./_common.php');
echo G5_DATA_PATH;
echo G5_DATA_URL;
echo "<br/>".G5_LIB_PATH;
echo "<br/>".sys_get_temp_dir();
echo "<br/>"; echo exec('whoami');
die();
include_once($_SERVER["DOCUMENT_ROOT"].'/lib/p2pctr_svc.lib.php');

$mb_id = $_REQUEST["mb_id"];

//$lmt = get_p2pctr_limit_test($mb_no);

//echo "총 투자한도 ---------------- <br/>";
//echo "전체 투자한도 ".number_format($lmt["ALL_LIMIT"])."<br/>";
//echo "부동산 투자한도 ".number_format($lmt["IMV_LIMIT"])."<br/>";

$product_idx = $_REQUEST["product_idx"];
?>
<br/><br/>
<form method="post">
	아이디 <input type="text" name="mb_id" value="<?=$mb_id?>" />
	품번 <input type="text" name="product_idx" value="<?=$product_idx?>" />
	<input type="submit" value="조회" />
<form>
<?
if ($mb_id) {


	$lmt2 = get_p2pctr_limit_test($mb_id, $product_idx);
	?>
	<br/><br/><br/>
	<table border=1 style="width:400px;">
		<tr>
			<td>전체 투자한도</td>
			<td style="text-align:right;"><?=number_format($lmt2["ALL_LIMIT"]);?></td>
		</tr>
		<tr>
			<td>부동산 투자한도</td>
			<td style="text-align:right;"><?=number_format($lmt2["IMV_LIMIT"]);?></td>
		</tr>
	<? if ($product_idx) { ?>
		<tr>
			<td>품번 <?=$product_idx?> 동일 차주 한도</td>
			<td style="text-align:right;"><?=number_format($lmt2["BRW_LIMIT"]);?></td>
		</tr>
	<? } ?>
	<?
}
?>
<?
function get_p2pctr_limit_test($mb_id, $product_idx="") {	

	if (!$mb_id) return;

	global $p2p_host;

	$apiNo = "4.4.1";
	$apiTitle = "투자잔액 조회";

	$url  = $p2p_host . "investments/inquiry";
	$method = "POST";

	if ($product_idx) {
		$psql = "SELECT recruit_amount FROM cf_product WHERE idx='$product_idx'";
		$pres = sql_query($psql);
		$prow = sql_fetch_array($pres);
	}

	$sql = "SELECT mb_no, member_investor_type FROM g5_member WHERE mb_leave_date='' AND mb_id='".$mb_id."'";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);


	if (!$cnt) return;

	$row = sql_fetch_array($res);
	$mno = $row["mb_no"];

	if ($row["member_investor_type"]=="1") {          // 일반투자자
		$LIMIT_ALL =  30000000;  // 전체
		$LIMIT_IMV =  10000000;  // 부동산
		$LIMIT_BRW =   5000000;  // 동일차주

	} else if ($row["member_investor_type"]=="2") {   // 소득적격 투자자
		$LIMIT_ALL = 100000000;
		$LIMIT_IMV = 100000000;
		$LIMIT_BRW =  20000000;

	} else if ($row["member_investor_type"]=="3") {   // 전문투자자
		$LIMIT_ALL = 999999999;
		$LIMIT_IMV = 999999999;
		$LIMIT_BRW =		40;
	}

	$inv_info = get_inv_info($mno);
	if ($product_idx) {
		$sqlp = "SELECT loan_mb_no FROM cf_product WHERE idx='$product_idx'";
		$resp = sql_query($sqlp);
		$rowp = sql_fetch_array($resp);

		$brw_info = get_brw_info($rowp["loan_mb_no"]);
	}

	$data = array();
	$data["investor_identity_no"] = $inv_info["inv_idno"];
	if ($brw_info["brw_idno"]) $data["borrower_identity_no"] = $brw_info["brw_idno"];

	//echo "<pre>"; print_r($data); echo "</pre>";
	
	$curl_res = curl_p2pctr2($apiNo, $apiTitle, $url , $method , $data, $product_idx, $mno);
	$resj = json_decode($curl_res["body"] , true);

	$ret = array();
	$bds = 0 ; //부톧산+PF 투자합계

	if ($resj["rsp_code"] == "A0000") {

		//$bd = json_decode($curl_res["body"] , true);
		//echo "<pre>"; print_r($resj); echo "</pre>";

		for ($i=0 ; $i<count($resj["goods_balance_list"]) ; $i++) {

			if ($resj["goods_balance_list"][$i]["goods_type"]=="P000" ) {
				$ret["ALL_LIMIT"] = $LIMIT_ALL - $resj["goods_balance_list"][$i]["balance"];

			} else if ($resj["goods_balance_list"][$i]["goods_type"]=="P110" OR $resj["goods_balance_list"][$i]["goods_type"]=="P120") {
				 $bds = $bds + $resj["goods_balance_list"][$i]["balance"];

			}

		}

		$ret["IMV_LIMIT"] = $LIMIT_IMV - $bds;

		if ($product_idx) {
			if ($LIMIT_BRW<100) {
				$ret["BRW_LIMIT"] = ceil($prow["recruit_amount"]*$LIMIT_BRW/100/10000)*10000 - $resj["balance_per_borrower"];
			} else {
				$ret["BRW_LIMIT"] = $LIMIT_BRW - $resj["balance_per_borrower"];
			}
		}

	}

	
	//echo "<pre>"; print_r($ret); echo "</pre>";
	return $ret;
	
}
?>
<?
//echo "<pre>"; print_r($member); echo "</pre>";