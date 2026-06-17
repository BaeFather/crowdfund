<?
include_once('_common.php');
include_once('../lib/sms.lib.php');

if (!$member["mb_no"]){ $err['err_msg']="로그인후 이용해 주세요.";echo json_encode($err); exit; }

$del_sql = "delete from cf_auto_invest_config_user where member_idx='". $member["mb_no"] ."'";
$ret_data['sql'] = $del_sql;
$ret_data['res'] = sql_query($del_sql);

$ret_data['res_cnt'] = sql_affected_rows();

/*카카오톡 알림톡 추가*/
$tcode = "hello007";
$KaKao_Message_Send = new KaKao_Message_Send();
$KaKao_Message_Send->MEMBER = $member;	// common.lib member 환경변수
$KaKao_Message_Send->kakao_insert($tcode);
/*카카오톡 알림톡 추가*/

echo json_encode($ret_data);
?>
