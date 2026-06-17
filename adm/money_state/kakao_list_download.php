<?

$filename = "헬로펀딩 카카오 송금 내역_" . date('YmdHis');
$filename = iconv('UTF-8', 'EUC-KR', $filename);
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
header('Cache-Control: max-age=0');

$sub_menu = "600100";
include_once('./_common.php');

$sdate = $_GET['sdate'];
$edate = $_GET['edate'];
$keyword = $_GET['keyword'];
$num = $total_count - $from_record + 1;

$sql = "SELECT A.* , B.mb_name
		  FROM cf_kakao_remit A
  		  LEFT JOIN g5_member B ON(B.mb_id = A.mb_id)
		  WHERE 1=1 and send_result='SUCCESS'
					and send_result is not null 
					and substring(A.insert_datetime,1,10) >= '$sdate'
					and substring(A.insert_datetime,1,10) <= '$edate' 
					and (A.mb_id like '%$keyword%' or B.mb_name like '%$keyword%' or A.tid like '%$keyword%' ) 	 
		  ORDER BY A.insert_datetime ASC";



$result = sql_query($sql);


$EXCEL_FILE = "
<table style='border-collapse:collapse;font-size:10pt;'>
	<tr>
		<td colspan='7' rowspan='3' style='text-align:center;font-size:16pt;font-weight:bold;'>헬로펀딩 카카오 송금 내역</td>
	</tr>
</table>
<table border='1' style='border-collapse:collapse;font-size:10pt;'>
	<tr>
		<td style='text-align:center;background:#D8D8D8'>No.</td>
		<td style='text-align:center;background:#D8D8D8'>회원번호</td>
		<td style='text-align:center;background:#D8D8D8'>ID</td>
		<td style='text-align:center;background:#D8D8D8'>이름</td>
		<td style='text-align:center;background:#D8D8D8'>결과</td>
		<td style='text-align:center;background:#D8D8D8'>송금액</td>
		<td style='text-align:center;background:#D8D8D8'>처리시간</td>
		<td style='text-align:center;background:#D8D8D8'>TID</td>
	</tr>
";

while($row = sql_fetch_array($result)) {
	$EXCEL_FILE .= "
		<tr>
			<td>".$num++."</td>
			<td>".$row['idx']."</td>
			<td>".$row['mb_id']."</td>
			<td>".$row['mb_name']."</td>
			<td>".$row['send_result']."</td>
			<td>".$row['sent_amount']."</td>
			<td>".$row['insert_datetime']."</td>
			<td>".$row['tid']."</td>
		</tr>
	";
}

$EXCEL_FILE .= "</table>";

echo "<meta content=\"application/vnd.ms-excel; charset=UTF-8\" name=\"Content-type\"> ";
echo $EXCEL_FILE;

sql_close();
exit;

?>