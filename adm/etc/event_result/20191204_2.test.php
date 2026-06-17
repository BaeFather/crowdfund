<?

set_time_limit(0);

/*
마케팅팀 요청자료
2. 첫투자자 누적 50만원 이상 투자자 리스트

* 10월 14일~11월 30일 까지 첫 투자자로 부동산, 주택담보 상품에 누적 50만원 이상인 투자자 리스트

- 리스트항목 :
- 조건 : 부동산, 주택담보 상품 투자만 적용
         핀크, 한경TV 등 제휴사를 통한 투자 제외
         중복제외

2021-03-17 전체 리뉴얼 배재수

*/

include_once("_common.php");

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v);  }

$sdate = ($sdate) ? $sdate : date('Y-m-d', strtotime('-1 week'));
$edate = ($edate) ? $edate : date('Y-m-d');

$target_amount = ($target_amount) ? $target_amount : 1000000;


$where = "";
$where.= " AND A.invest_state='Y'";
$where.= " AND A.insert_date BETWEEN '".$sdate."' AND '".$edate."'";
$where.= " AND A.amount >= $target_amount";

if($category) {
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

if($member_type) {

	$where.=  " AND B.member_type='".$member_type."' ";

	if($member_investor_type) {
		$where.= " AND B.member_investor_type ='".$member_investor_type."' ";
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
//echo $where."<br/>\n";


$whereX = " AND (SELECT COUNT(AA.idx) FROM cf_product_invest AA LEFT JOIN cf_product BB  ON AA.product_idx=BB.idx WHERE AA.insert_date<'".$sdate."' AND AA.member_idx=A.member_idx AND AA.invest_state='Y'";
if($category2) {
	if( in_array($category2, array('1','2','3')) ) {
		$whereX.= " AND BB.category='".$category2."'";
	}
	else {
		if($category2=='2A') $whereX.= " AND BB.category='2' AND BB.mortgage_guarantees=''";
		if($category2=='2B') $whereX.= " AND BB.category='2' AND BB.mortgage_guarantees='1'";
		if($category2=='3A') $whereX.= " AND BB.category='3' AND BB.title LIKE '%면세점%'";
		if($category2=='3B') $whereX.= " AND BB.category='3' AND BB.title LIKE '%소상공인%'";
	}
}
$whereX.= ") = 0";

// 대상기간내 대상금액이상 투자한 모든 회원idx 추출
$sql0 = "
	SELECT
		distinct(A.member_idx),
		B.mb_level AS now_mb_level
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B  ON A.member_idx=B.mb_no
	LEFT JOIN
		cf_product C  ON A.product_idx=C.idx
	WHERE 1
		$where
		AND B.mb_level IN(1,200)
		$whereX
	ORDER BY
		now_mb_level ASC,
		B.member_type DESC,
		A.amount DESC,
		C.start_num DESC,
		A.idx DESC";

if($_SERVER['REMOTE_ADDR']=='220.117.134.164') print_rr($sql0,'font-size:12px');
exit;

$res0  = sql_query($sql0);
$rows0 = sql_num_rows($res0);

$LIST = array();

for($i=0; $i<$rows0; $i++) {

	$BASE[$i] = sql_fetch_array($res0);

	$member_table = ($BASE[$i]['now_mb_level']=='200') ? 'g5_member_drop' : 'g5_member';

	$sql = "
		SELECT
			A.idx, A.member_idx, B.mb_id, B.member_type, B.member_investor_type,
			(IF(B.member_type='2', B.mb_co_name, B.mb_name)) AS mb_title,
			B.mb_hp, B.mb_co_reg_num,
			A.amount, B.va_bank_code2, B.virtual_account2, B.va_private_name2, B.pid,
			A.syndi_id
		FROM
			cf_product_invest A
		LEFT JOIN
			{$member_table} B  ON A.member_idx=B.mb_no
		LEFT JOIN
			cf_product C  ON A.product_idx=C.idx
		WHERE 1
			AND A.member_idx = '".$BASE[$i]['member_idx']."'
			$where
			$whereX
		ORDER BY
			A.idx ASC LIMIT 1";

	if( $R = sql_fetch($sql) ) {

		$sql1 = "
			SELECT COUNT(AA.idx) AS prev_invest_count
			FROM cf_product_invest AA
			LEFT JOIN cf_product BB  ON AA.product_idx=BB.idx
			WHERE AA.insert_date<'".$sdate."' AND AA.member_idx='".$R['member_idx']."' AND AA.invest_state='Y'";
		//echo $sql1 . ";<br/>\n";
		$R['prev_invest_count'] = sql_fetch($sql1)['prev_invest_count'];

		if($BASE[$i]['now_mb_level']=='200') {
			$R['mb_hp'] = '';
			$R['is_drop_member'] = 1;
		}
		else {
			$R['mb_hp'] = masterDecrypt($R['mb_hp'], false);
			$R['is_drop_member'] = 0;
		}

		array_push($LIST, $R);

	}

	if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
		if($i==0)  print_rr($sql,'font-size:11px');
	}

}

//print_rr($LIST,'font-size:11px');


$list_count = count($LIST);

//print_rr($LIST);


$page_title = "대상기간내 첫투자자 투자금액 ".price_cutting($target_amount)."원 이상 투자자 현황 (".preg_replace("/-/", ".", $sdate)."~".preg_replace("/-/", ".", $edate).")";

if($_REQUEST['download']=='1') {

	$file_name = date('Ymd') . "_".$page_title.".xls";
	$file_name = iconv("utf-8", "euc-kr", $file_name);

	header( "Content-type: application/vnd.ms-excel;" );
	header( "Content-Disposition: attachment; filename=$file_name" );
	header( "Content-description: PHP5 Generated Data" );

}
else {
?>

	<link rel="stylesheet" type="text/css" href="/adm/css/admin.css">
	<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
	<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">
	<style>th,td {padding:2px 4px;}</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

	<div style='padding:10px 20px 30px'>
		<p><?=$page_title?>

		<form id='form1' method='get' action='<?=$_SERVER['PHP_SELF']?>'>
			투자기간: <input type='text' id='sdate' name='sdate' value='<?=$sdate?>' class='datepicker'> ~ <input type='text' id='edate' name='edate' value='<?=$edate?>' class='datepicker'>

			<select id='target_amount' name='target_amount'>
				<option value=''>::투자금액::</option>
				<option value='10000' <?=($target_amount==10000)?'selected':''?>>1만원 이상</option>
				<option value='100000' <?=($target_amount==100000)?'selected':''?>>10만원 이상</option>
<?
	for($i=1; $i<=20; $i++) {
		$value = 1000000 * $i;
		$selected = ($target_amount==$value) ? 'selected' : '';
			echo "				<option value='".$value."' {$selected}>".price_cutting($value)."원 이상</option>\n";
	}
?>
			</select>

			<div style='height:4px'></div>

			<select id='category' name='category'>
				<option value=''>::상품분류::</option>
				<option value='2' <?=($category=='2')?'selected':'';?>>부동산 전체</option>
				<option value='2A' <?=($category=='2A')?'selected':'';?>> - PF</option>
				<option value='2B' <?=($category=='2B')?'selected':'';?>> - 주택담보</option>
				<option value='3' <?=($category=='3')?'selected':'';?>>헬로페이 전체</option>
				<option value='3A' <?=($category=='3A')?'selected':'';?>> - 면세점</option>
				<option value='3B' <?=($category=='3B')?'selected':'';?>> - 소상공인</option>
				<option value='1' <?=($category=='1')?'selected':'';?>>동산</option>
			</select>

			<select id='category2' name='category2'>
				<option value=''>::투자기간 이전 투자내역 필터링::</option>
				<option value='2' <?=($category2=='2')?'selected' : '';?>>부동산 전체상품 투자내역 없는 회원</option>
				<option value='2A' <?=($category2=='2A')?'selected' : '';?>>- PF상품 투자내역 없는 회원</option>
				<option value='2B' <?=($category2=='2B')?'selected' : '';?>>- 주택담보상품 투자내역 없는 회원</option>
				<option value='3' <?=($category2=='3')?'selected' : '';?>>헬로페이 전체상품 투자내역 없는 회원</option>
				<option value='3A' <?=($category2=='3A')?'selected' : '';?>>- 면세점상품 투자내역 없는 회원</option>
				<option value='3B' <?=($category2=='3B')?'selected' : '';?>>- 소상공인상품 투자내역 없는 회원</option>
				<option value='1' <?=($category2=='1')?'selected' : '';?>>동산상품 투자내역 없는 회원</option>
			</select>

			<div style='height:4px'></div>

			<select name='member_type'>
				<option value=''>::회원구분 선택::</option> ";
				<option value='1' <?=($member_type=='1')?'selected':'';?>>개인회원</option>
				<option value='2' <?=($member_type=='2')?'selected':'';?>>법인회원</option>
			</select>

			<select name='member_investor_type'>
   			<option value=''>::회원상세구분 선택 (현재기준)::</option> ";
  			<option value='1' <?=($member_investor_type=='1')?'selected':'';?>>일반개인</option>
				<option value='2' <?=($member_investor_type=='2')?'selected':'';?>>소득적격</option>
				<option value='3' <?=($member_investor_type=='3')?'selected':'';?>>전문투자</option>
			</select>

			<select id='pid' name='pid'>
				<option value=''>::마케팅제휴사::</option>
				<option value='none' <?=($pid=='none')?'selected':'';?>>해당없음</option>
				<option value='all' <?=($pid=='all')?'selected':'';?>>전체제휴사</option>
<?
		$P_ARR_KEYS = array_keys($CONF['PARTNER']);
		for($i=0; $i<count($CONF['PARTNER']); $i++) {
		$selected = ($P_ARR_KEYS[$i]==$pid) ? 'selected' : '';
			echo "				<option value='".$P_ARR_KEYS[$i]."' $selected> -".$CONF['PARTNER'][$P_ARR_KEYS[$i]]['name']."(".$P_ARR_KEYS[$i].")</option>\n";
	}
?>
			</select>

			<select id='syndi_id' name='syndi_id'>
				<option value=''>::외부투자플랫폼::</option>
				<option value='none' <?=($syndi_id=='none')?'selected':'';?>>해당없음</option>
				<option value='all' <?=($syndi_id=='all')?'selected':'';?>>전체신디케이션사</option>
<?
		$S_ARR_KEYS = array_keys($CONF['SYNDICATOR']);
		for($i=0; $i<count($CONF['SYNDICATOR']); $i++) {
		$selected = ($S_ARR_KEYS[$i]==$syndi_id) ? 'selected' : '';
			echo "				<option value='".$S_ARR_KEYS[$i]."' $selected> -".$CONF['SYNDICATOR'][$S_ARR_KEYS[$i]]['name']."(".$S_ARR_KEYS[$i].")</option>\n";
	}
?>
			</select>

			<button type='submit'>검색</button>
				<button type='button' onClick="location.href='<?=$_SERVER['REQUEST_URI']?>&download=1';">엑셀 다운로드</button></p>
			</form>

			전체 <?=number_format($list_count)?>건<br/>

<?
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
				<td align=center bgcolor='#D9E1F2'>".$sdate." 이전 투자건수</td>
				<td align=center bgcolor='#D9E1F2'>입금은행</td>
				<td align=center bgcolor='#D9E1F2'>계좌번호</td>
				<td align=center bgcolor='#D9E1F2'>예금주</td>
				<td align=center bgcolor='#D9E1F2'>마케팅제휴사</td>
				<td align=center bgcolor='#D9E1F2'>신디케이션사</td>
			</tr>\n";

if($list_count) {

	for($i=0,$j=$list_count; $i<$list_count; $i++,$j--) {

		$print_member_type = '';
		if($LIST[$i]['member_type']=='2') {
			$print_member_type.= '법인';
			$print_reg_num = preg_replace("/-/", "", $LIST[$i]['mb_co_reg_num']);
		}
		else {
			$print_member_type.= "개인";
			if($LIST[$i]['member_investor_type']=='3') $print_member_type.= '-전문';
			else if($LIST[$i]['member_investor_type']=='2') $print_member_type.= '-소득적격';
			else $print_member_type.= '-일반';

			$print_reg_num = getJumin($LIST[$i]['member_idx']);

		}


		if($LIST[$i]['is_drop_member']) {
			$fcolor = '#AAA';
			$bgcolor = '#EEE';
		}
		else {
			$fcolor = '';
			$bgcolor = '';
		}


		echo "
			<tr style='color:$fcolor;background:$bgcolor'>
				<td align=center>".$j."</td>
				<td align=center>".$LIST[$i]['mb_id']."</td>
				<td align=center>".$print_member_type."</td>
				<td align=center>".$LIST[$i]['mb_title']."</td>
				<td align=center style=\"mso-number-format:'@';\">".$print_reg_num."</td>
				<td align=center style=\"mso-number-format:'@';\">".$LIST[$i]['mb_hp']."</td>
				<td align=right>".number_format($LIST[$i]['amount'])."</td>
				<td align=center>".$LIST[$i]['prev_invest_count']."</td>
				<td align=center>".$BANK[$LIST[$i]['va_bank_code2']]."</td>
				<td align=center style=\"mso-number-format:'@';\">".$LIST[$i]['virtual_account2']."</td>
				<td align=center>".$LIST[$i]['va_private_name2']."</td>
				<td align=center>".$CONF['PARTNER'][$LIST[$i]['pid']]['name']."</td>
				<td align=center>".$CONF['SYNDICATOR'][$LIST[$i]['syndi_id']]['name']."</td>
			</tr>\n";

	}

}
else {

	echo "			<tr><td colspan='13' align='center'>데이터가 없습니다.</td></tr>\n";

}

echo "		</table>\n";


if($_REQUEST['download']=='') {
?>
	</div>
	<script>
	$('.datepicker').datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesShort: ['일' ,'월', '화', '수', '목', '금', '토']
	});
	</script>
<?
}


sql_close();
exit;

?>
