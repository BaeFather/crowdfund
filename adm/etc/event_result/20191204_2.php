<?

/*
마케팅팀 요청자료
2. 첫투자자 누적 50만원 이상 투자자 리스트

* 10월 14일~11월 30일 까지 첫 투자자로 부동산, 주택담보 상품에 누적 50만원 이상인 투자자 리스트

- 리스트항목 :
- 조건 : 부동산, 주택담보 상품 투자만 적용
         핀크, 한경TV 등 제휴사를 통한 투자 제외
         중복제외
*/

include_once("_common.php");

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v);  }

$sdate = ($sdate) ? $sdate : date('Y-m-d', strtotime('-1 week'));
$edate = ($edate) ? $edate : date('Y-m-d');
$target_amount = ($target_amount) ? $target_amount : 1000000;
$category = ($category) ? $category : 'none';

$where3 = "";
$where3.= " AND invest_state='Y'";
$where3.= " AND insert_date BETWEEN '".$sdate."' AND '".$edate."'";

if($category) {
	if($category=='none') {
		//
	}
	else {
		if( in_array($category, array('1','2','3')) ) {
			$where.= " AND C.category='".$category."'";
		}
		else {
			if($category=='2A') $where.= " AND C.category='2' AND C.mortgage_guarantees=''";
			if($category=='2B') $where.= " AND C.category='2' AND C.mortgage_guarantees='1'";
			if($category=='3A') $where.= " AND C.category='3' AND C.title LIKE '%면세점%'";
			if($category=='3B') $where.= " AND C.category='3' AND C.title LIKE '%소상공인%'";
		}
	}
}
IF($member_type)
{
	$where .=  " AND member_type='".$member_type."' ";

	IF($member_investor_type)
	{
 		$where .= " AND member_investor_type ='".$member_investor_type."' ";
	}
}

if($pid) {
	if($pid=='none') {
		$where.= " AND B.pid=''";
	}
	else {
		$where.= ($pid=='all') ? " AND B.pid!=''" : " AND B.pid='".$pid."'";			// $pid=='all'  ::: pid가 기록된 모든 회원데이터
	}
}

if($syndi_id) {
	if($syndi_id=='none') {
		$where.= " AND A.syndi_id=''";
	}
	else {
		$where.= ($syndi_id=='all') ? " AND A.syndi_id!=''" : " AND A.syndi_id='".$syndi_id."'";		//	$syndi_id=='all'  ::: 신디케이션ID가 기록된 모든 투자데이터
	}
}

$sql = "
	SELECT
		A.member_idx, A.syndi_id,
		B.pid, B.mb_id,B.mb_name,B.mb_hp, B.mb_co_reg_num, B.va_bank_code2,B.member_type, B.virtual_account2, B.va_private_name2, IF(B.member_type='2','법인회원','개인회원') AS type_title,
		IF(B.member_type='2',mb_co_name,mb_name) AS mb_title,B.mb_ci,
		IFNULL(MIN(A.amount),0) AS amount_sum
	FROM
		(
			SELECT idx, member_idx, product_idx, amount,syndi_id,invest_state,insert_date FROM
			cf_product_invest_detail WHERE 1 $where3 group by member_idx

		) A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	LEFT JOIN
		cf_product C  ON A.product_idx=C.idx
	WHERE 1
		$where
	GROUP BY
		A.member_idx
	ORDER BY
		B.member_type DESC,
		amount_sum DESC,
		C.start_num DESC,
		A.idx DESC";
//print_rr($sql, 'font-size:12px');
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$LIST = array();
for($i=0; $i<$rows; $i++) {

	$ROW = sql_fetch_array($res);

	if($ROW['amount_sum'] >= $target_amount) {

		// 검색기간 이전 부동산 누적투자 데이터
		$where2 = " AND A.member_idx='".$ROW['member_idx']."' AND A.invest_state='Y' AND A.insert_date < '".$sdate."' ";

		IF($category2 && $category2 <> "none")
		{
			if( in_array($category2, array('1','2','3')) ) {
				$where.= " AND C.category='".$category2."'";
			}
			else {
				if($category2=='2A') $where2.= " AND C.category='2' AND C.mortgage_guarantees=''";
				if($category2=='2B') $where2.= " AND C.category='2' AND C.mortgage_guarantees='1'";
				if($category2=='3A') $where2.= " AND C.category='3' AND C.title LIKE '%면세점%'";
				if($category2=='3B') $where2.= " AND C.category='3' AND C.title LIKE '%소상공인%'";
			}

		}
		$sql2 = "
			SELECT
				COUNT(A.idx) AS cnt
			FROM
				cf_product_invest A
			LEFT JOIN
				cf_product C  ON A.product_idx=C.idx
			WHERE 1
				$where2";
		//print_rr($sql2, 'font-size:12px;color:red');
		$ROW2 = sql_fetch($sql2);

		UNSET($row);
		IF($ROW['member_type'] == "1")	// 개인회원 탈퇴이력 조회
		{
			$Query = "SELECT COUNT(*) as CNT FROM g5_member_drop WHERE (mb_name='".$ROW['mb_name']."' AND member_type='1' AND mb_hp='".$ROW['mb_hp']."') ";
			IF($ROW['mb_ci'])
			{
				$Query .= " OR mb_ci='".$ROW['mb_ci']."'";
			}

			$row = sql_fetch($Query);
		} ELSEIF($ROW['member_type'] == "2") {	// 법인회원 탈퇴이력 조회
			$Query = "SELECT COUNT(*) as CNT FROM g5_member_drop WHERE mb_co_reg_num='".$ROW['mb_co_reg_num']."' ";
			$row = sql_fetch($Query);
		}

		$strCntKind = false;
		IF($ROW2['cnt'] == 0)
		{
			IF($row["CNT"] == 0) {	// 기존이력이 존재하거나 탈퇴이력이 존재한다면
				$strCntKind = true;
			}

		}

		IF($strCntKind == true)
		{
			array_push($LIST, $ROW);
		}

	}

}

$list_count = count($LIST);

//print_rr($LIST);


$S_ARR_KEYS = array_keys($CONF['SYNDICATOR']);
$P_ARR_KEYS = array_keys($CONF['PARTNER']);

$page_title = "대상기간내 첫투자자 투자금액 ".price_cutting($target_amount)."원 이상 투자자 현황 (".preg_replace("/-/", ".", $sdate)."~".preg_replace("/-/", ".", $edate).")";

if($_REQUEST['download']=='1') {

	$file_name = date('Ymd') . "_".$page_title.".xls";
	$file_name = iconv("utf-8", "euc-kr", $file_name);

	header( "Content-type: application/vnd.ms-excel;" );
	header( "Content-Disposition: attachment; filename=$file_name" );
	header( "Content-description: PHP5 Generated Data" );

}
else {

	//print_rr($sql, 'font-size:11px');

	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/adm/css/admin.css\">\n";
	echo "<link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css\">\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/adm/css/bootstrap.min.css\">\n";
	echo "<style>th,td {padding:2px 4px;}</style>";
	echo "<script src=\"//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js\"></script>\n";
	echo "<script src=\"//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js\"></script>\n\n";

	echo "<div style='padding:10px 20px 30px'>\n";
	echo "	<p>".$page_title. "\n";

	echo "	<form id='form1' method='get' action='".$_SERVER['PHP_SELF']."'>\n";
	echo "		투자기간: <input type='text' id='sdate' name='sdate' value='".$sdate."' class='datepicker'> ~ <input type='text' id='edate' name='edate' value='".$edate."' class='datepicker'>\n";

	echo "		<select id='target_amount' name='target_amount'>\n";
	echo "			<option value=''>::투자금액::</option>\n";
	$selected[0] = ($target_amount==10000) ? 'selected' : '';
	echo "			<option value='10000' {$selected[0]}>1만원 이상</option>\n";
	$selected[2] = ($target_amount==100000) ? 'selected' : '';
	echo "			<option value='100000' {$selected[2]}>10만원 이상</option>\n";
	for($i=1; $i<=20; $i++) {
		$value = 1000000 * $i;
		$selected[1] = ($target_amount==$value) ? 'selected' : '';
		echo "			<option value='".$value."' {$selected[1]}>".price_cutting($value)."원 이상</option>\n";
	}
	echo "		</select><br/>\n";

	echo "		<select name='member_type'>";
   echo "		<option value=''>::회원구분 선택::</option> ";
  echo "		<option value='1' ";
	IF($member_type=='1') { ECHO " selected"; }
	ECHO ">개인회원</option>";
	echo "		<option value='2' ";
	IF($member_type=='2') { ECHO " selected"; }
	ECHO ">법인회원</option>";

	echo "		</select>\n";

	echo "		<select name='member_investor_type'>";
   echo "		<option value=''>::회원상세구분 선택::</option> ";
  echo "		<option value='1' ";
	IF($member_investor_type=='1') { ECHO " selected"; }
	ECHO ">일반개인</option>";
	echo "		<option value='2' ";
	IF($member_investor_type=='2') { ECHO " selected"; }
	ECHO ">소득적격</option>";
	echo "		<option value='3' ";
	IF($member_investor_type=='3') { ECHO " selected"; }
	ECHO ">전문투자</option>";

	echo "		</select><br/>\n";

	$selected0[0] = ($pid=='none') ? 'selected' : '';
	$selected0[1] = ($pid=='all') ? 'selected' : '';
	echo "		<select id='pid' name='pid'>\n";
	echo "			<option value=''>::마케팅제휴사::</option>\n";
	echo "			<option value='none' {$selected0[0]}>해당없음</option>\n";
	echo "			<option value='all' {$selected0[1]}>전체제휴사</option>\n";
	for($i=0; $i<count($CONF['PARTNER']); $i++) {
		$selected = ($P_ARR_KEYS[$i]==$pid) ? 'selected' : '';
		echo "			<option value='".$P_ARR_KEYS[$i]."' $selected> -".$CONF['PARTNER'][$P_ARR_KEYS[$i]]['name']."(".$P_ARR_KEYS[$i].")</option>\n";
	}
	echo "		</select>\n";

	$selected1[0] = ($syndi_id=='none') ? 'selected' : '';
	$selected1[1] = ($syndi_id=='all') ? 'selected' : '';
	echo "		<select id='syndi_id' name='syndi_id'>\n";
	echo "			<option value=''>::신디케이션사::</option>\n";
	echo "			<option value='none' {$selected1[0]}>해당없음</option>\n";
	echo "			<option value='all' {$selected1[1]}>전체신디케이션사</option>\n";
	for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
		$selected = ($S_ARR_KEYS[$i]==$syndi_id) ? 'selected' : '';
		echo "		<option value='".$S_ARR_KEYS[$i]."' $selected> -".$CONF['SYNDICATOR'][$S_ARR_KEYS[$i]]['name']."(".$S_ARR_KEYS[$i].")</option>\n";
	}
	echo "		</select>\n&nbsp;";

	$selected2[0] = ($category=='2')  ? 'selected' : '';
	$selected2[1] = ($category=='2A') ? 'selected' : '';
	$selected2[2] = ($category=='2B') ? 'selected' : '';
	$selected2[3] = ($category=='3')  ? 'selected' : '';
	$selected2[4] = ($category=='3A') ? 'selected' : '';
	$selected2[5] = ($category=='3B') ? 'selected' : '';
	$selected2[6] = ($category=='1')  ? 'selected' : '';

	echo "
		<select id='category' name='category'>
			<option value='none'>::상품분류::</option>
			<option value='2' {$selected2[0]}>부동산</option>
			<option value='2A' {$selected2[1]}>-부동산(PF)</option>
			<option value='2B' {$selected2[2]}>-부동산(주택담보)</option>
			<option value='3' {$selected2[3]}>헬로페이</option>
			<option value='3A' {$selected2[4]}>-헬로페이(면세점)</option>
			<option value='3B' {$selected2[5]}>-헬로페이(소상공인)</option>
			<option value='1' {$selected2[6]}>동산</option>
		</select>";

	$selected3[0] = ($category2=='2')  ? 'selected' : '';
	$selected3[1] = ($category2=='2A') ? 'selected' : '';
	$selected3[2] = ($category2=='2B') ? 'selected' : '';
	$selected3[3] = ($category2=='3')  ? 'selected' : '';
	$selected3[4] = ($category2=='3A') ? 'selected' : '';
	$selected3[5] = ($category2=='3B') ? 'selected' : '';
	$selected3[6] = ($category2=='1')  ? 'selected' : '';

	echo "
		<select id='category2' name='category2'>
			<option value='none'>::투자기간전 투자상품 대상::</option>
			<option value='2' {$selected3[0]}>부동산</option>
			<option value='2A' {$selected3[1]}>-부동산(PF)</option>
			<option value='2B' {$selected3[2]}>-부동산(주택담보)</option>
			<option value='3' {$selected3[3]}>헬로페이</option>
			<option value='3A' {$selected3[4]}>-헬로페이(면세점)</option>
			<option value='3B' {$selected3[5]}>-헬로페이(소상공인)</option>
			<option value='1' {$selected3[6]}>동산</option>
		</select>";

	ECHO "<button type='submit'>검색</button>
		<button type='button' onClick='location.href=\"".$_SERVER['REQUEST_URI']."&download=1\"'>엑셀 다운로드</button></p>
	</form>

	전체 ".number_format($list_count)."건<br/>\n";

}

echo "
	<table border=1 style='font-size:9pt;border-collapse:collapse;'>
		<tr>
			<td align=center bgcolor='#D9E1F2'>NO</td>
			<td align=center bgcolor='#D9E1F2'>아이디</td>
			<td align=center bgcolor='#D9E1F2'>회원구분</td>
			<td align=center bgcolor='#D9E1F2'>법인명.성명</td>
			<td align=center bgcolor='#D9E1F2'>사업자.주민번호</td>
			<td align=center bgcolor='#D9E1F2'>연락처</td>
			<td align=center bgcolor='#D9E1F2'>투자금액</td>
			<td align=center bgcolor='#D9E1F2'>".$sdate."이전 투자건수</td>
			<td align=center bgcolor='#D9E1F2'>입금은행</td>
			<td align=center bgcolor='#D9E1F2'>계좌번호</td>
			<td align=center bgcolor='#D9E1F2'>예금주</td>
			<td align=center bgcolor='#D9E1F2'>마케팅제휴사</td>
			<td align=center bgcolor='#D9E1F2'>신디케이션사</td>
		</tr>\n";

for($i=0,$j=$list_count; $i<$list_count; $i++,$j--) {

	$regist_num = ($LIST[$i]['type_title']=='개인회원') ? getJumin($LIST[$i]['member_idx']) : preg_replace("/(-| )/", "", $LIST[$i]['mb_co_reg_num']);
	$print_prev_invest_count = ($LIST[$i]['prev_invest_count']==0) ? '없음' : $LIST[$i]['prev_invest_count'];

		echo "
		<tr>
			<td align=center>".$j."</td>
			<td align=center>".$LIST[$i]['mb_id']."</td>
			<td align=center>".$LIST[$i]['type_title']."</td>
			<td align=center>".$LIST[$i]['mb_title']."</td>
			<td align=center style=\"mso-number-format:'@';\">".$regist_num."</td>
			<td align=center style=\"mso-number-format:'@';\">".masterDecrypt($LIST[$i]['mb_hp'],false)."</td>
			<td align=right>".number_format($LIST[$i]['amount_sum'])."</td>
			<td align=center>".$print_prev_invest_count."</td>
			<td align=center>".$BANK[$LIST[$i]['va_bank_code2']]."</td>
			<td align=center style=\"mso-number-format:'@';\">".$LIST[$i]['virtual_account2']."</td>
			<td align=center>".$LIST[$i]['va_private_name2']."</td>
			<td align=center>".$CONF['PARTNER'][$LIST[$i]['pid']]['name']."</td>
			<td align=center>".$CONF['SYNDICATOR'][$LIST[$i]['syndi_id']]['name']."</td>
		</tr>\n";

}

echo "	</table>";

if($_REQUEST['download']=='') {

	echo "</div>\n";

	echo "
<script>
$('.datepicker').datepicker({
	dateFormat: 'yy-mm-dd',
	changeYear: true,
	changeMonth: true,
	monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
	dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
});
</script>\n\n";

}

sql_close();
exit;


?>
