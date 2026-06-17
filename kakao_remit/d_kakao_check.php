<?
include "/home/crowdfund/public_html/common.php";
?>
<?
$this_date = date("Y-m-d");
$day_before = date( 'Y-m-d', strtotime( $this_date . ' -1 day' ) );


//$sql = "SELECT * FROM cf_kakao_remit WHERE insert_datetime LIKE '2019-08-21 %' order by idx limit 30";
//$sql = "SELECT * FROM cf_kakao_remit WHERE insert_datetime LIKE '2019-08-22 %' and idx>6123 order by idx limit 30";
$sql = "SELECT * FROM cf_kakao_remit WHERE tid<>'' and SUBSTRING(insert_datetime,1,10)>='$day_before' and confirm_tid='N' order by idx limit 30";

$res = sql_query($sql);
$cnt = sql_num_rows($res);

$tid_list = array();

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	echo "$i $row[idx] $row[mb_id] $row[tid]\n";
	$tid_list[$i] = $row['tid'];
}

$kakao_res = Call_KakaoPay_tid($tid_list);

for ($i=0 ; $i<$kakao_res['size'] ; $i++) {

	$chk_sql = "select * from cf_kakao_remit where tid='".$kakao_res['result'][$i]['tid']."'";
	$chk_res = sql_query($chk_sql);
	$chk_row = sql_fetch_array($chk_res);

	$up_sql="";
	$up_sql2="";

	if ($kakao_res['result'][$i]['send_status']<>$chk_row[send_result]) {
		echo $kakao_res['result'][$i]['tid']." ".$kakao_res['result'][$i]['send_status']."   ";
		echo "$chk_row[send_result]    ";
		echo "XXX";
		echo "\n";

		if ($kakao_res['result'][$i]['send_status']=="SUCCESS") {
			$up_sql = "update cf_kakao_remit set sent_amount='".$kakao_res['result'][$i]['sent_amount']."' where idx='$chk_row[idx]'";
			sql_query($up_sql);
		}

		$up_sql2 = "update cf_kakao_remit set send_result='".$kakao_res['result'][$i]['send_status']."', confirm_tid='Y', modify_datetime=now() where idx='$chk_row[idx]'";

	} else {
		$up_sql2 = "update cf_kakao_remit set confirm_tid='Y', modify_datetime=now() where idx='$chk_row[idx]'";
	}
	echo "$chk_row[idx] $up_sql2 $up_sql\n";
	sql_query($up_sql2);
}

//print_r($kakao_res);
?>

<?
function Call_KakaoPay_tid($tid_list) {

	//$kakao_pay_url = "https://biz-dapi.kakaopay.com/money/v2/transfer/partner/confirm/tid";       // live
	$kakao_pay_url = "https://moneyapi.dozn.co.kr/api/money/transfer/confirm/tid";
	//$CID     = "MC0000D7FBL5CL6";
	//$API_KEY = "aa2b94d273964fe9b452b1e48644257e";
	$CID     = "ID000002";
	$API_KEY = "0754dd3b-5af7-4b00-96c1-14c3600fd377";

	$kakao_params = array(
		'api_key' => $API_KEY,
		'cid' => $CID,
		'tid_list' => $tid_list
	);

	$strArrResult = request_curl2($kakao_pay_url, $kakao_params );

	return $strArrResult;
}
function request_curl2($url,  $data=array()) {

	$ch = curl_init();
echo json_encode($data)."\n";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json;charset=utf-8','Authorization: PARTNER_KEY 1B44B4809FC1AA49CC6A72718F5277A9623C8A14'));

	$result[0] = curl_exec($ch);
	$result[1] = curl_errno($ch);
	$result[2] = curl_error($ch);
	$result[3] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

	curl_close($ch);

	return json_decode($result[0], true);
	//return $result;

}
?>