<?
///////////////////////
// 이벤트 등록
///////////////////////

include_once("_common.php");

while( list($k, $v) = each($_POST) ) { ${$k} = trim($v); }


if($event_no) {

	$ROW = sql_fetch("SELECT * FROM cf_partner_event_config WHERE event_no='".$event_no."'");

	if($ROW['idx']) {
		$mode = 'edit';
		$EDATA['event_no'] = $ROW['event_no'];
	}
	else {
		$mode = 'new';
	}

}

$EDATA['event_title'] = sql_real_escape_string($event_title);
$EDATA['is_real'] = $is_real;
$EDATA['event_summary'] = sql_real_escape_string($event_summary);
$EDATA['sdate'] = $sdate;
$EDATA['edate'] = $edate;
$EDATA['pid'] = $pid;
$EDATA['provider_name'] = sql_real_escape_string($provider_name);
$EDATA['coupon_name'] = sql_real_escape_string($coupon_name);
$EDATA['coupon_point'] = $coupon_point;
$EDATA['invest_rwd_give'] = $invest_rwd_give;
$EDATA['invest_rwd_cond'] = $invest_rwd_cond;
$EDATA['invest_prdt_ca'] = $invest_prdt_ca;
$EDATA['invest_use_amt'] = $invest_use_amt;
$EDATA['invest_rwd_amt'] = $invest_rwd_amt;
$EDATA['invest_rwd_point'] = $invest_rwd_point;
$EDATA['message'] = sql_real_escape_string($message);

$array_count = count($EDATA);

$EDATAKEY = array_keys($EDATA);


if($mode=='edit') {

	$edit = false;

	for($i=0,$j=1; $i<$array_count; $i++,$j++) {
		if($EDATA[$EDATAKEY[$i]] != $ROW[$EDATAKEY[$i]]) {
			$edit = true;
			break;
		}
	}

	if($edit) {

		$sql = "UPDATE cf_partner_event_config SET\n";
		for($i=0,$j=1; $i<$array_count; $i++,$j++) {
			$sql.= $EDATAKEY[$i]." = '".$EDATA[$EDATAKEY[$i]]."'";
			$sql.= ($j < $array_count) ? ",\n" : "\n";
		}
		$sql.= "WHERE idx = '".$ROW['idx']."'";

	}
	else {
		$ARR = array('result'=>'fail', 'message'=>'변동사항이 없습니다.'); echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); sql_close(); exit;
	}

}
else {

	$event_no = sql_fetch("SELECT (MAX(event_no)+1) AS next_event_no FROM cf_partner_event_config")['next_event_no'];

	$sql = "INSERT INTO cf_partner_event_config SET\n";
	$sql.= "event_no = '".$event_no."',";
	$sql.= "rdt = NOW(),";
	for($i=0,$j=1; $i<$array_count; $i++,$j++) {
		$sql.= $EDATAKEY[$i]." = '".$EDATA[$EDATAKEY[$i]]."'";
		$sql.= ($j < $array_count) ? ",\n" : "";
	}

}

if( $res = sql_query($sql) ) {

	if(!sql_affected_rows()) {
		$ARR = array('result'=>'fail', 'message'=>'변동사항이 없습니다.'); echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); sql_close(); exit;
	}

	$ARR = array('result'=>'success', 'message'=>'');
	echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
}
else {
	$ARR = array('result'=>'fail', 'message'=>'DB처리중 오류발생!!'); echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);
}


@sql_close();
exit;

?>