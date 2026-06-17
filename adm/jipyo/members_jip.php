<?php
include_once('./_common.php');
auth_check($auth[$sub_menu], "w");
?>
<?
$ym = $_REQUEST['ym'];
//$ym = "9019-10";
?>
<?
//echo "$ym 집계<br/><br/>";
$del_sql = "delete from cf_jipyo_first_invest where ym='$ym'";
sql_query($del_sql);

get_first_inv2($ym);
get_first_inv_finnq($ym);
get_first_inv_r114($ym);
get_first_inv_tvtalk($ym);
get_first_inv_cashcow($ym);
get_first_inv_toomics($ym);
get_evntout_inv_donga($ym);
get_evntout_inv_seoul($ym);
get_evntin_inv_f1000($ym);
get_evntin_inv_f10002($ym);

//if (!chk_data($ym, "P")) {
//}
$ajax_res = array();
$ajax_res["result"] = "ok";
$ajax_res["ym"] = $ym;
echo json_encode($ajax_res);
?>
<?
function get_first_inv2($ym) {
	$sql = "SELECT COUNT(*) cnt FROM
				(SELECT A.mb_no, A.member_type,
					(SELECT B.insert_date  FROM cf_product_invest_detail B WHERE B.member_idx=A.mb_no  ORDER BY B.idx LIMIT 1) first_inv
					FROM g5_member A
				) t1
			WHERE member_type='1' AND  SUBSTRING(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['P'] = $row['cnt'];

	if ($retval['P']) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='P', cnt='$retval[P]'";
		sql_query($ins_sql);
	}

	//echo "개인회원 $retval[P] 건<br/>";

	$sql = "SELECT COUNT(*) cnt FROM
				(SELECT A.mb_no, A.member_type,
					(SELECT B.insert_date  FROM cf_product_invest_detail B WHERE B.member_idx=A.mb_no  ORDER BY B.idx LIMIT 1) first_inv
					FROM g5_member A
				) t1
			WHERE member_type='2' AND  SUBSTRING(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['C'] = $row['cnt'];

	if ($retval['C']) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='C', cnt='$retval[C]'";
		sql_query($ins_sql);
	}

	//echo "기업회원 $retval[C] 건<br/>";

	//return $retval;
}
function get_first_inv_finnq($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.finnq_userid<>''
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='finnq', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "핀크 $cnt 건<br/>";

	//return $cnt;
}
function get_first_inv_r114($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.r114_userid<>''
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='r114', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "부동산114 $cnt 건<br/>";

	//return $cnt;
}

function get_first_inv_tvtalk($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='TvTalk'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='tvtalk', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "티비톡 $cnt 건<br/>";

	return $cnt;
}

function get_first_inv_cashcow($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='cashcow'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='cashcow', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "캐시카우 $cnt 건<br/>";

	return $cnt;
}

function get_first_inv_toomics($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='toomics'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='toomics', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "투믹스 $cnt 건<br/>";

	return $cnt;
}

function get_evntout_inv_donga($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.rec_mb_id='donga_expo'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='donga', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "한경TV $cnt 건<br/>";

	return $cnt;
}

function get_evntout_inv_seoul($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.rec_mb_id='seoul_money_show'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='seoul_money_show', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "서울머니쇼 $cnt 건<br/>";

	return $cnt;
}

function get_evntin_inv_f1000($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.event_id='100B'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='100b', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "천억돌파 $cnt 건<br/>";

	return $cnt;
}

function get_evntin_inv_f10002($ym) {
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.event_id='100BEVENT2'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	if ($cnt) {
		$ins_sql = "insert into cf_jipyo_first_invest set ym='$ym' , gubun='luckybox', cnt='$cnt'";
		sql_query($ins_sql);
	}

	//echo "럭키박스 $cnt 건<br/>";

	return $cnt;
}
?>




<?
function chk_data($ym, $gb) {
	$sql = "SELECT count(*) chkcnt FROM cf_jipyo_first_invest WHERE ym='$ym' AND gubun='$gb'";
	echo "$sql";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['chkcnt'];

	if ($cnt) $retval=true;
	else $retval=false;

	return $retval;
}

?>