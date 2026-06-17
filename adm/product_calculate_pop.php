<?php

set_time_limit(0);
include_once('./_common.php');
include_once('./admin.head.nomenu.php');
include_once('./admin.loan.function.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

include_once(G5_LIB_PATH.'/repay_calculation.php');		// 월별 정산내역 추출함수 호출

$idx =	$_REQUEST["idx"];

IF(!$idx) { ECHO "no_result"; }

//경로 /adm/admin.loan.function.php
$strProduct	=	new Product_Calculate();

$strVal = $strProduct->Product_view($idx);
?>
<style>
	.g_table {width:100%;border-collapse:collapse;}
	.g_table th {background-color:#EEE;border:1px solid #CCC;font-size:14px;padding:7px 5px;width:25%;}
	.g_table td {background-color:#fff;text-align:center;font-size:14px;padding:7px 5px;width:75%;}
	.g_table .thfull {width:100%;}
</style>
<table class="g_table">
	<tr>
		<th colspan="2" class="thfull">대출 정보</th>
	</tr>
	<tr>
		<th>상품명</th>
		<td><?php ECHO $strVal["title"];?></td>
	</tr>
	<tr>
		<th>대출자명</th>
		<td><?php ECHO $strVal["loaner"];?></td>
	</tr>
	<tr>
		<th>금리</th>
		<td><?php ECHO $strVal["loan_interest_rate"];?> %</td>
	</tr>
	<tr>
		<th>대출금액</th>
		<td><?php ECHO NUMBER_FORMAT($strVal["recruit_amount"]);?> 원</td>
	</tr>
	<tr>
		<th>대출기간</th>
		<td><?php ECHO $strVal["loan_date_range"];?></td>
	</tr>
	<tr>
		<th>대출금 입금계좌</th>
		<td><?php ECHO $strVal["loan_dep"];?></td>
	</tr>
	<tr>
		<th>대출 실행일</th>
		<td><?php ECHO $strVal["loan_date"];?></td>
	</tr>
	<tr>
		<th>플랫폼 수수료</th>
		<td><?php ECHO NUMBER_FORMAT($strVal["invest_usefee"]);?> 원  <div style="width:100%;height:10px;"></div><?php ECHO $strVal["invest_bank"];?></td>
	</tr>
	<tr>
		<th>대출금 예치 이자</th>
		<td><?php ECHO NUMBER_FORMAT($strVal["deposit_interest"]);?> 원 &nbsp; (<?php ECHO $strVal["intday"];?> 일치)<div style="width:100%;height:10px;"></div>신한은행 | <?php ECHO $strVal["repay_acct_no"];?></td>
	</tr>
</table>

<?

include_once ('./admin.tail.nomenu.php');
sql_close();

?>