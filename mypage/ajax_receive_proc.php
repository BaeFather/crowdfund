<?
###############################################################################
##   - 2019-01-31 최초 생성 : 원리금 수취방식만 변경하는 프로그램. 전승찬 작성
###############################################################################

include_once('../common.php');
include_once('../lib/common.lib.php');

if ($_SERVER["REQUEST_METHOD"]!="POST") { echo "fail"; }

while( list($k, $v) = each($_POST) ) {
	${$k} = @trim($v);
}


if($is_member) {

	$chk_sql = "select receive_method from g5_member where mb_no = '".$member['mb_no']."'";
	$chk_res = sql_query($chk_sql);
	$chk_row = sql_fetch_array($chk_res);

	if ($chk_row['receive_method'] <> $new_receive_method and ($new_receive_method=="1" or $new_receive_method=="2") ) {

		$up_sql = "update g5_member set receive_method = '$new_receive_method', edit_datetime=NOW() WHERE mb_no = '".$member['mb_no']."'";
		sql_query($up_sql);

		member_edit_log($member['mb_no']);	// 회원정보변경기록
		echo "ok";
	} else {
		echo "fail";
	}
} else {
	echo "fail";
}
?>