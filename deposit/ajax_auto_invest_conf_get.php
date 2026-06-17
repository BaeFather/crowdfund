<?
include_once('_common.php');

if (!$member["mb_id"]){ $retn['err_msg']="로그인후 이용해 주세요.";echo json_encode($retn); exit; }

$sql = "select * from cf_auto_invest_config_user WHERE member_idx='".$member['mb_no']."' and ai_grp_idx<>6 and ai_grp_idx<>10 order by ai_grp_idx";

$res = sql_query($sql);
$cnt = $res->num_rows;

$retn['auto_conf'] = array();
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$retn['auto_conf'][$i] = $row;
}

//echo "<pre>";print_r($retn);echo "</pre>";
echo json_encode($retn);

sql_close();

?>