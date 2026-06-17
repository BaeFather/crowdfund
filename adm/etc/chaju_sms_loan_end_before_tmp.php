<?
include_once('./_common.php');

// 만료 30일전 안내 문자 메시지 정의 ----------------------------------------------------------------
$count_sms = "[헬로펀딩] 만기 안내
안녕하세요 {USER_NAME}님 헬로펀딩입니다.
대출 만기일 안내드립니다.

- 대출금 : {REMAIN_AMT}원
- 대출금리 : {eyul}%
- 만기일 : {YYYYMMDD} 

만기 이후 신규대출(기간 연장)이 필요하신 경우
주택금융사업팀으로 연락 부탁드립니다.

※ 신용상태, 담보시세등 심사기준에 따라
대출이 불가능하거나 대출 금리가 인상될 수있습니다. ※

주택금융사업팀 : 1588-5210";
// -------------------------------------------------------------------------------

$sql = "SELECT * FROM cf_product 
		 WHERE state='1' 
		   AND category='2' AND mortgage_guarantees='1' 
	  ORDER BY loan_end_date";
$res = sql_query($sql);
$cnt = $res->num_rows;
?>
<table border=1 style="border-spacing:0 ; border-collapse: collapse; border: 2px solid black;">
<?
for ($i=0 ; $i<$cnt ; $i++) {

	$row = sql_fetch_array($res);

	$count_end_ym = date('Y-m-d', strtotime( $row["loan_end_date"] . '-1 month') );  // 만료 1달전

	$chk = "Y";
	if ($count_end_ym<date("Y-m-d")) $chk="";
	
	if ($chk=="Y") {

		$bg="white";
		$msg = $count_sms;
		$msg = str_replace("{USER_NAME}", $chaju[$l]['mb_name'], $msg);

	} else {
		$bg="#E9EBEC";
	}


	?>
	<tr style="background-color: <?=$bg?>;">
		<td><?=$i+1?></td>
		<td><?=$count_end_ym?></td>
		<td><?=$row["loan_end_date"]?></td>
		<td><?=$row["title"]?></td>
		<td><pre><?=$msg?></pre></td>
	</tr>
	<?
}

?>
</table>