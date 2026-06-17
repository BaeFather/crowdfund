<?

include_once('./_common.php');
auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v); }

$event_title = "럭키박스";	// 입금자명으로 사용
$event_id = "100BEVENT2";
$purpose_point = 3000000;

$where = " 1=1";
$where.= " AND A.event_id='".$event_id."'";
if($newbi=='1') $where.= " AND B.event_id='".$event_id."'";
if($invalid=='1') $where.= " AND A.invalid='1'";
if($paid) {
	$where.= ($paid=='Y') ? " AND A.paid='1'" : " AND A.paid=''";
}
if($member_type) {
	$where.= " AND B.member_type='".$member_type."'";
}

if($date_field) {
	if($sdate) $where.= " AND LEFT($date_field, 10)>='$sdate'";
	if($edate) $where.= " AND LEFT($date_field, 10)<='$edate'";
}
if($field && $keyword) {
	if( in_array($field, array('B.mb_hp','B.account_num')) ) {
		$where.= " AND $field='" . masterEncrypt($keyword, false) . "'";
	}
	else if($field == 'A.point') {
		$where.= " AND $field='" . $keyword . "'";
	}
	else {
		$where.= " AND $field LIKE BINARY '%$keyword%'";
	}
}


$sql = "
	SELECT
		A.idx, A.member_idx, A.event_id, A.entry_key, A.hp, A.point, A.regdate, A.invalid, A.invalid_date, A.paid, A.paid_date, A.bank_code, A.bank_acct, A.bank_private_name,
		B.member_type, B.mb_id, B.mb_name, B.mb_co_name, B.mb_hp, B.mb_co_reg_num, B.mb_email, B.event_id AS member_event_id
	FROM
		event_entry_log A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	WHERE
		$where
	ORDER BY
		A.idx DESC";
$res = sql_query($sql);
$rcount = $res->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = ($LIST[$i]['mb_hp']) ? masterDecrypt($LIST[$i]['mb_hp'],false) : '';

	if($LIST[$i]['member_type']=='2') {
		$LIST[$i]['mb_name'] = $LIST[$i]['mb_co_name'];
		$LIST[$i]['jumin'] = $LIST[$i]['mb_co_reg_num'];
	}
	else {
		$LIST[$i]['jumin'] = getJumin($LIST[$i]['member_idx']);
	}

}

$now_date  = date('Ymd');
$file_name = "럭키박스이벤트_지급요청내역_".$now_date.".xls";
$file_name = iconv("utf-8", "euc-kr", $file_name);

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-description: PHP5 Generated Data" );

?>


<table border='1' style="font-size:10pt">
	<tr bgcolor="#F8F8EF">
		<th class="text-center">가입구분</th>
		<th class="text-center">회원구분</th>
		<th class="text-center">아이디</th>
		<th class="text-center">성명.법인명</th>
		<th class="text-center">주민.법인번호</th>
		<th class="text-center">연락처</th>
		<th class="text-center">당첨금(원)</th>
		<th class="text-center">응모일시</th>
		<th class="text-center">지급여부</th>
		<th class="text-center">지급일시</th>
		<th class="text-center">무효</th>
		<th class="text-center">무효처리일시</th>

		<th class="text-center">은행명</th>
		<th class="text-center">계좌번호</th>
		<th class="text-center">금액</th>
		<th class="text-center">예금주</th>
		<th class="text-center"></th>
		<th class="text-center"></th>
		<th class="text-center">통장표시내용</th>
	</tr>

<?
for($i=0,$j=1; $i<count($LIST);$i++) {

	$print_mb_id = $LIST[$i]['mb_id'];

	$print_member_type = ($LIST[$i]['member_type']=='2') ? '법인' : '개인';
	$print_member_name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];


	$full_mb_hp = $LIST[$i]['mb_hp'];

	$print_newbi = ($LIST[$i]['member_event_id']==$event_id) ? "신규회원" : "기존회원";

	$print_paid      = ($LIST[$i]['paid']=='1') ? "<span style='color:#3366FF'>지급</span>" : "미지급";
	$print_paid_date = ($LIST[$i]['paid']=='1') ? "<span style='color:#3366FF'>".substr($LIST[$i]['paid_date'], 0, 16)."</span>" : "";

	$print_invalid      = ($LIST[$i]['invalid']!='') ? "<span style='color:brown'>무효</span>" : "";
	$print_invalid_date = ($LIST[$i]['invalid']!='') ? "<span style='color:brown'>".substr($LIST[$i]['invalid_date'], 0, 16)."</span>" : "";

	$print_bank = $print_acct = $print_private_name = "";

	if($LIST[$i]['bank_acct']) {
		$print_bank = $BANK[$LIST[$i]['bank_code']];
		$print_acct = $LIST[$i]['bank_acct'];
		$print_private_name = $LIST[$i]['bank_private_name'];
	}
	else {
		$row = sql_fetch("SELECT va_bank_code2, virtual_account2, va_private_name2 FROM g5_member WHERE mb_no='".$LIST[$i]['member_idx']."'");
		$print_bank = $BANK[$row['va_bank_code2']];
		$print_acct = $row['virtual_account2'];
		$print_private_name = $row['va_private_name2'];
	}
?>
	<tr align="center">
		<td><?=$print_newbi?></td>
		<td><?=$print_member_type?></td>
		<td><?=$print_mb_id?></td>
		<td><?=$print_member_name?></td>
		<td style="mso-number-format:'@';"><?=$LIST[$i]['jumin']?></td>
		<td style="mso-number-format:'@';"><?=$full_mb_hp?></td>
		<td align="right"><?=number_format($LIST[$i]['point'])?></td>
		<td><?=substr($LIST[$i]['regdate'], 0, 16)?></td>
		<td><?=$print_paid?></td>
		<td><?=$print_paid_date?></td>
		<td><?=$print_invalid?></td>
		<td><?=$print_invalid_date?></td>

		<td style="color:#2222FF;"><?=$print_bank?></td>
		<td style="color:#2222FF;mso-number-format:'@';"><?=$print_acct?></td>
		<td style="color:#2222FF;" align="right"><?=number_format($LIST[$i]['point'])?></td>
		<td style="color:#2222FF;"><?=$print_private_name?></td>
		<td style="color:#2222FF;"></td>
		<td style="color:#2222FF;"></td>
		<td style="color:#2222FF;"><?=$event_title?></td>
	</tr>
<?

	$num--;
}
?>
</table>