<?php
include_once('./_common.php');

$ym = array();

$idx = 0 ;
for ($y = 2016 ; $y < date("Y") ; $y++) {
	$ym[$idx] = $y;
	$idx++;
}
?>
<h1>연도별 대출 잔액</h1>
<table border=1 >
	<tr>
		<th style="width:60px;">년도</th>
		<th style="width:140px;">동산</th>
		<th style="width:140px;">부동산 PF</th>
		<th style="width:140px;">부동산 담보</th>
		<th style="width:140px;">기타</th>
		<th style="width:140px;">합계</th>
	</tr>
<?
for ($i=0 ; $i<count($ym); $i++) {

	$sql = "SELECT
	LEFT(loan_end_date, 4) AS year,
	SUM(recruit_amount) recruit_amount
	FROM
	cf_product
	WHERE 1
	AND display='Y'
	AND state NOT IN('','3','6','7')
	AND LEFT(loan_start_date, 4) <= '$ym[$i]' AND LEFT(loan_end_date, 4) > '$ym[$i]';";
	$result = sql_query($sql);
	$row=sql_fetch_array($result);
	//echo "<pre>";print_r($row);echo "</pre>";
	//echo $ym[$i] . " " .number_format($row["recruit_amount"])."<br/>";

	// 동산 category=1
	$sql1 = "SELECT
	LEFT(loan_end_date, 4) AS year,
	SUM(recruit_amount) recruit_amount
	FROM
	cf_product
	WHERE 1
	AND display='Y'
	AND state NOT IN('','3','6','7')
	AND LEFT(loan_start_date, 4) <= '$ym[$i]' AND LEFT(loan_end_date, 4) > '$ym[$i]'
	AND category=1";
	$result1 = sql_query($sql1);
	$row1=sql_fetch_array($result1);

	// 부동산 PF category=2 mortgage_guarantees=''
	$sql2 = "SELECT
	LEFT(loan_end_date, 4) AS year,
	SUM(recruit_amount) recruit_amount
	FROM
	cf_product
	WHERE 1
	AND display='Y'
	AND state NOT IN('','3','6','7')
	AND LEFT(loan_start_date, 4) <= '$ym[$i]' AND LEFT(loan_end_date, 4) > '$ym[$i]'
	AND category=2 AND mortgage_guarantees=''";
	$result2 = sql_query($sql2);
	$row2=sql_fetch_array($result2);

	// 부동산 담보 category=2 mortgage_guarantees='1'
	$sql3 = "SELECT
	LEFT(loan_end_date, 4) AS year,
	SUM(recruit_amount) recruit_amount
	FROM
	cf_product
	WHERE 1
	AND display='Y'
	AND state NOT IN('','3','6','7')
	AND LEFT(loan_start_date, 4) <= '$ym[$i]' AND LEFT(loan_end_date, 4) > '$ym[$i]'
	AND category=2 AND mortgage_guarantees='1'";
	$result3 = sql_query($sql3);
	$row3=sql_fetch_array($result3);


	// 확정매출채권 category=3 
	$sql4 = "SELECT
	LEFT(loan_end_date, 4) AS year,
	SUM(recruit_amount) recruit_amount
	FROM
	cf_product
	WHERE 1
	AND display='Y'
	AND state NOT IN('','3','6','7')
	AND LEFT(loan_start_date, 4) <= '$ym[$i]' AND LEFT(loan_end_date, 4) > '$ym[$i]'
	AND category=3";
	$result4 = sql_query($sql4);
	$row4=sql_fetch_array($result4);

	$chk = "Y";
	if ($row["recruit_amount"] <> $row1["recruit_amount"] + $row2["recruit_amount"] + $row3["recruit_amount"] + $row4["recruit_amount"]) $chk="N"
	?>
	<tr>
		<td style="text-align:center;"><?=$ym[$i]?> <?=$chk=="N"?"ERROR":""?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row1["recruit_amount"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row2["recruit_amount"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row3["recruit_amount"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row4["recruit_amount"])?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row["recruit_amount"])?></td>
	</tr>
	<?
}
?>
</table>

<br/><br/>

<h1>이자 소득세 (개인)</h1>
<table border=1 >
	<tr>
		<th style="width:60px;">년도</th>
		<th style="width:140px;">동산</th>
		<th style="width:140px;">부동산 PF</th>
		<th style="width:140px;">부동산 담보</th>
		<th style="width:140px;">기타</th>
		<th style="width:140px;">합계</th>
	</tr>
<?

for ($i=0 ; $i<count($ym); $i++) {

	// 개인 이자소득세 합계
	/*
	$sql = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='1'
			";
	*/
	$sql = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='1'
			";
	$result = sql_query($sql);
	$row=sql_fetch_array($result);
	$p_total[$i] = $row["only_tax"];
	// 개인 이자소득세 동산
	/*
	$sql1 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='1'
					AND C.category=1
			";
	*/
	$sql1 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='1'
					AND C.category=1
			";
	$result1 = sql_query($sql1);
	$row1 = sql_fetch_array($result1);

	// 개인 이자소득세 부동산 PF category=2 mortgage_guarantees=''
	/*
	$sql2 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='1'
					AND C.category=2
					AND C.mortgage_guarantees=''
			";
	*/
	$sql2 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='1'
					AND C.category=2
					AND C.mortgage_guarantees=''
			";
	$result2 = sql_query($sql2);
	$row2 = sql_fetch_array($result2);

	// 개인 이자소세 부동산 담보 category=2 mortgage_guarantees='1'
	/*
	$sql3 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='1'
					AND C.category=2
					AND C.mortgage_guarantees='1'
			";
	*/
	$sql3 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='1'
					AND C.category=2
					AND C.mortgage_guarantees='1'
			";
	$result3 = sql_query($sql3);
	$row3 = sql_fetch_array($result3);

	// 개인 이자도슥세 확정매출채권 category=3 
	/*
	$sql4 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='1'
					AND C.category=3
			";
	*/
	$sql4 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='1'
					AND C.category=3
			";
	$result4 = sql_query($sql4);
	$row4 = sql_fetch_array($result4);

	$real_tot = $row1["only_tax"] + $row2["only_tax"] + $row3["only_tax"] + $row4["only_tax"];
	$chk = "";
	if ($real_tot<>$row["only_tax"]) $chk=" error";
	?>
	<tr>
		<td style="text-align:center;"><?=$ym[$i]?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row1["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row2["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row3["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row4["only_tax"])?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row["only_tax"])?><?=$chk?></td>
	</tr>
	<?
}
?>
</table>

<br/>

<h1>이자 소득세 (법인)</h1>
<table border=1 >
	<tr>
		<th style="width:60px;">년도</th>
		<th style="width:140px;">동산</th>
		<th style="width:140px;">부동산 PF</th>
		<th style="width:140px;">부동산 담보</th>
		<th style="width:140px;">기타</th>
		<th style="width:140px;">합계</th>
	</tr>
<?

for ($i=0 ; $i<count($ym); $i++) {

	// 법인 이자소득세 합계
	/*
	$sql = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='2'
			";
	*/
	$sql = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='2'
			";
	//echo "$sql<br/>";
	$result = sql_query($sql);
	$row=sql_fetch_array($result);
	$c_total[$i] = $row["only_tax"];

	// 법인 이자소득세 동산
	/*
	$sql1 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='2'
					AND C.category=1
			";
	*/
	$sql1 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='2'
					AND C.category=1
			";
	$result1 = sql_query($sql1);
	$row1 = sql_fetch_array($result1);

	// 법인 이자소득세 부동산 PF category=2 mortgage_guarantees=''
	/*
	$sql2 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='2'
					AND C.category=2
					AND C.mortgage_guarantees=''
			";
	*/
	$sql2 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='2'
					AND C.category=2
					AND C.mortgage_guarantees=''
			";
	$result2 = sql_query($sql2);
	$row2 = sql_fetch_array($result2);

	// 법인 이자도슥세 부동산 담보 category=2 mortgage_guarantees='1'
	/*
	$sql3 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='2'
					AND C.category=2
					AND C.mortgage_guarantees='1'
			";
	*/
	$sql3 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='2'
					AND C.category=2
					AND C.mortgage_guarantees='1'
			";
	$result3 = sql_query($sql3);
	$row3 = sql_fetch_array($result3);

	// 법인 이자도슥세 확정매출채권 category=3 
	/*
	$sql4 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax + fee) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(C.loan_start_date, 4) <= '$ym[$i]' AND LEFT(C.loan_end_date, 4) > '$ym[$i]'
					AND B.member_type='2'
					AND C.category=3
			";
	*/
	$sql4 = "SELECT SUM(A.interest + A.interest_tax + A.local_tax ) only_tax, B.member_type
				FROM cf_product_give A
				LEFT JOIN g5_member B ON(A.member_idx = B.mb_no)
				LEFT JOIN cf_product C ON(A.product_idx = C.idx)
				WHERE 1
					AND C.display='Y'
					AND C.state NOT IN('','3','6','7')
					AND LEFT(A.banking_date, 4) = '$ym[$i]'
					AND B.member_type='2'
					AND C.category=3
			";
	$result4 = sql_query($sql4);
	$row4 = sql_fetch_array($result4);

	$real_tot = $row1["only_tax"] + $row2["only_tax"] + $row3["only_tax"] + $row4["only_tax"];
	$chk = "";
	if ($real_tot<>$row["only_tax"]) $chk=" error";
	?>
	<tr>
		<td style="text-align:center;"><?=$ym[$i]?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row1["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row2["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row3["only_tax"])?></td>
		<td style="text-align:right;padding-right:15px;"><?=number_format($row4["only_tax"])?></td>

		<td style="text-align:right;padding-right:15px;"><?=number_format($row["only_tax"])?><?=$chk?></td>
	</tr>
	<?
}
?>
</table>

<br/><br/>

<h1>이자 소득세 (개인+법인)</h1>
<table border=1 >
	<tr>
		<th><?=$ym[0]?></th>
		<th><?=$ym[1]?></th>
		<th><?=$ym[2]?></th>
		<th><?=$ym[3]?></th>
	</tr>
	<tr>
		<td style="text-align:right;width:140px;padding-right:10px;"><?=number_format($p_total[0]+$c_total[0])?></td>
		<td style="text-align:right;width:140px;padding-right:10px;"><?=number_format($p_total[1]+$c_total[1])?></td>
		<td style="text-align:right;width:140px;padding-right:10px;"><?=number_format($p_total[2]+$c_total[2])?></td>
		<td style="text-align:right;width:140px;padding-right:10px;"><?=number_format($p_total[3]+$c_total[3])?></td>
	</tr>
</table>

<script>alert("조회완료");</script>