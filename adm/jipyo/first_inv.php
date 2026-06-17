<?
include_once('./_common.php');
?>
<?
$sql = "SELECT A.idx, A.member_idx, A.insert_datetime,
			  B.idx AS prd_idx, B.state
		 FROM cf_product_invest A
	LEFT JOIN cf_product B ON(B.idx = A.product_idx)
		WHERE A.first_inv=''
		  AND A.invest_state='Y'
		  AND B.state IN(1,2,5)
	 ORDER BY insert_datetime ASC LIMIT 1000";
$res = sql_query($sql);
$cnt = $res->num_rows;

echo "총 $cnt 건 <br/><br/>";

for ($i=0 ; $i<$cnt ; $i++) {
	if ($i>10000) die("safe die");
	$row = sql_fetch_array($res);

	// 아래의 update 문으로 변경됐을수도 있으니 다시 한번 처리하여 이미 처리된 회원건이면 스캡한다.
	$rechk_sql = "SELECT first_inv FROM cf_product_invest WHERE idx='$row[idx]'";
	$rechk_res = sql_query($rechk_sql);
	$rechk_row = sql_fetch_array($rechk_res);
	if ($rechk_row["first_inv"]<>"") {
		echo "<br/>$row[idx] 이미 처리됨<br/>";
		continue;
	}

	$chk_sql = "SELECT count(A.idx) chk_cnt
				 FROM cf_product_invest A
			LEFT JOIN cf_product B ON(B.idx = A.product_idx)
				WHERE A.member_idx='$row[member_idx]'
				  AND A.insert_datetime<'$row[insert_datetime]'
				  AND A.invest_state='Y'
				  AND B.state IN(1,2,5)
				  ";
	$chk_res = sql_query($chk_sql);
	$chk_row = sql_fetch_array($chk_res);
	$chk_cnt=$chk_row["chk_cnt"];

	if ($chk_cnt==0) {
		$up_sql1 = "update cf_product_invest set first_inv='Y' where idx='$row[idx]'";
		sql_query($up_sql1);

		$up_sql2 = "update cf_product_invest set first_inv='N' where member_idx='$row[member_idx]' and idx<>'$row[idx]'";
		sql_query($up_sql2);

		echo "$i 최초 투자<br/>";
		echo "$up_sql1<br/>";
		echo "$up_sql2<br/>";

	} else {
		$up_sql3 = "update cf_product_invest set first_inv='N' where idx='$row[idx]'";
		sql_query($up_sql3);
		echo "$i $up_sql3 중복<br/>";
	}
	echo "<br/>";
}

$sql = "SELECT A.* , B.title, B.loan_start_date
		  FROM cf_product_invest A
	 LEFT JOIN cf_product B ON(B.idx=A.product_idx)
		 WHERE A.first_inv=''
	  ORDER BY A.idx";
$res = sql_query($sql);
$cnt = $res->num_rows;

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	if ($row["invest_state"]=="N" or $row["invest_state"]=="R") {
		$up_sql4 = "UPDATE cf_product_invest set first_inv='N' where idx='$row[idx]'";
		sql_query($up_sql4);
		echo "투자취소 $up_sql4<br/>";
	} else if ($row[loan_start_date]=="0000-00-00") {
		echo "대출실행전 $row[idx] $row[title] ($row[loan_start_date] ~)<br/>";
	} else {
		echo "값없음 $row[idx] ($row[loan_start_date] ~)<br/>";
	}
	echo "<br/>";
}
?>

