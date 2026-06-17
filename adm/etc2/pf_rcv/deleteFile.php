<?
include_once('./_common.php');

// get으로 넘어온 필드 항목을 변수로 지정
$idx   = $_GET['idx'];
$oname = $_GET['oname'];
$tname = $_GET['tname'];

// 해당 hcseq의 모든 필드를 select (기존 저장된 값) 
$sql = "SELECT * FROM cf_pf_accounts_rcv WHERE idx='$idx'";
$res = sql_query($sql);
$old_data = sql_fetch_array($res);
$del_file_res = unlink("./uploads/".$tname);
 
// 원본 파일, 임시 파일
$ori_file_names = $old_data["origin_file"];  
$tmp_file_names = $old_data["temp_file"];

// 해당 파일 삭제될 때 sql update
$new_ori_file_name = str_replace($oname.";", "", $ori_file_names); 
$new_tmp_file_name = str_replace($tname.";", "", $tmp_file_names);

$sql = "UPDATE 
			cf_pf_accounts_rcv
		SET
			origin_file = '$new_ori_file_name',
			temp_file = '$new_tmp_file_name'
		WHERE
			idx = '$idx'
";

// 삭제된 파일은 지워지고 update가 되어야 함
$result = sql_query($sql); 


goto_url('./pf_rcv_form.php?idx='.$idx, false);

?>