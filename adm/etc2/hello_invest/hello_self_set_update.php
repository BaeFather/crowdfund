<?
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }


if($mode == 'save') {

	if(!$reg_date){
		$date_text = "now()";
	} else if($reg_date && !$mod_date) {
		$date_text = "DATE_FORMAT('$mod_date','%Y-%m-%d')";
	}

	$sql = "
		INSERT INTO
			hello_self_invest
		SET
			start_date='".$start_date."',
			end_date='".$end_date."',
			price ='".$price."',
			reg_date=NOW()
	";

	sql_query($sql);

	alert("등록되었습니다.","./hello_self_set_list.php");

} else if($mode == 'del') {
	$sql = "
		DELETE
		FROM
			hello_self_invest 
		WHERE 
			idx='$idx'
	";

	sql_query($sql);

	alert("삭제되었습니다.","./hello_self_set_list.php");

}  else if($mode == 'mod') {

	$sql = "
		UPDATE
			hello_self_invest
		SET
			start_date='".$start_date."',
			end_date='".$end_date."',
			price ='".$price."',
			mod_date=NOW()
		WHERE
			idx='$idx'
	";
	
	sql_query($sql);

	alert("수정되었습니다.","./hello_self_set_list.php");
} 

?>