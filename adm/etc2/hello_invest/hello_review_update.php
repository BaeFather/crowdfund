<?
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }


if($type == '주택담보대출') {
	$type_no = '1';
} else if($type == '매출채권') {
	$type_no = '2';
} else if($type == 'PF') {
	$type_no = '3';
} else if($type == '동산') {
	$type_no = '4';
}


if($res1=='Y' && $res2=='Y' && $res3=='Y' && $res4=='Y' && $res5=='Y') { 
	$resYN = 'Y';
} else { 
	$resYN = 'N';
} 

$title_name = sql_fetch("SELECT title FROM cf_product WHERE	idx='$title'")['title'];

if($mode == 'save') {

// 등록
$sql = "
	INSERT INTO
		hello_self_review
	SET
		h1_invest_price='".$invest_price."',
		h1_remain='".$remain."',
		h1_perc='".$perc."',
		h1_res1='".$res1."',
		h1_res2='".$res2."',
		h1_overdue_perc='".$overdue_perc."',
		h2_prdidx='".$title."',
		h2_title='".$title_name."',
		h2_type='".$type_no."',
		h2_recruit_amount='".$recruit_amount."',	
		h2_live_amount='".$live_amount."',	
		h2_tot_perc='".$tot_perc."',
		h2_request_price='".$request_price."',
		h2_hello_perc='".$hello_perc."',
		h2_res3='".$res3."',
		h2_ltv='".$ltv."',
		h2_res4='".$res4."',
		h2_loan_mb_name='".$loan_mb_name."',
		h2_loan_remain='".$loan_remain."',
		h2_loan_perc='".$loan_perc."',
		h2_res5='".$res5."',
		resYN='".$resYN."',
		reg_date=NOW()
";

//echo $sql; exit;

$res = sql_query($sql);
$row = sql_fetch_array($res);

alert("저장되었습니다.","./hello_review_list.php");

}


// 수정
if($mode == 'modify') {
	$sql = "
	UPDATE
		hello_self_review
	SET
		h1_invest_price='".$invest_price."',
		h1_remain='".$remain."',
		h1_perc='".$perc."',
		h1_res1='".$res1."',
		h1_res2='".$res2."',
		h1_overdue_perc='".$overdue_perc."',
		h2_prdidx='".$title."',
		h2_title='".$title_name."',
		h2_type='".$type_no."',
		h2_recruit_amount='".$recruit_amount."',	
		h2_live_amount='".$live_amount."',	
		h2_tot_perc='".$tot_perc."',
		h2_request_price='".$request_price."',
		h2_hello_perc='".$hello_perc."',
		h2_res3='".$res3."',
		h2_ltv='".$ltv."',
		h2_res4='".$res4."',
		h2_loan_mb_name='".$loan_mb_name."',
		h2_loan_remain='$loan_remain',
		h2_loan_perc='".$loan_perc."',
		h2_res5='".$res5."',
		resYN='".$resYN."',
		mod_date=NOW()
	WHERE
		idx='$idx'
";

//echo $sql; exit;

sql_query($sql);

alert("수정되었습니다.","./hello_review_list.php");

}

if($mode ==  'delete') {
$sql = "
	DELETE 
	FROM 
		hello_self_review
	WHERE
		idx='$idx'
";
//echo $sql; exit;
sql_query($sql);

alert("삭제되었습니다.","./hello_review_list.php");
}


?>