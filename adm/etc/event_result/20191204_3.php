<?
/*
마케팅팀 요청자료
1. 200만원이상 투자자 리스트

* 10월 14일~11월 30일 까지, 부동산과 주택담보 상품에 200만원 이상 투자한 투자자 리스트

- 리스트항목 : - 아이디, 이름, 상품명, 금액
- 조건 :  부동산, 주택담보 단일 상품에 200만원 이상 투자자(법인포함)
          상품 오픈 기준이 아닌, 해당 날짜안에 투자한 투자자 기준

*/

include_once("_common.php");
include_once("../../admin.loan.function.php");

FUNCTION fn_target_money($obj)
{
	IF($obj >= 1000000 && $obj < 2000000)
	{
		$retval = 5000;
	} ELSEIF($obj >= 2000000 && $obj < 3000000) {
		$retval = 10000;
	} ELSEIF($obj >= 3000000 && $obj < 4000000) {
		$retval = 15000;
	} ELSEIF($obj >= 4000000 && $obj < 5000000) {
		$retval = 20000;
	} ELSEIF($obj >= 5000000) {
		$retval = 50000;
	}

	return $retval;
}

while( list($k, $v) = each($_REQUEST) ) { ${$k} = trim($v);  }


$sdate = ($sdate) ? $sdate : DATE("Y-m-d", (time()-604800));
$edate = ($edate) ? $edate : DATE("Y-m-d");
$target_amount = ($target_amount) ? $target_amount : 5000000;

//투자
$where = " WHERE insert_date BETWEEN '".$sdate."' AND '".$edate."'";
$where.= " AND ( invest_state IN ('Y','R') OR (invest_state='N' AND cancel_by IN ('admin','system')) )"; // 투자

if($syndi_id) {
	if($syndi_id=='none') {
		$where.= " AND syndi_id=''";
	}
	else {
		$where.= ($syndi_id=='all') ? " AND syndi_id!=''" : " AND syndi_id='".$syndi_id."'";		//	$syndi_id=='all'  ::: 신디케이션ID가 기록된 모든 투자데이터
	}
}


//$where1 = " WHERE (start_date>='".$sdate."' AND start_date<='".$edate."')";	// product
$where1 = " WHERE (start_date>='".date("Y-m-d", strtotime("first day of ".$sdate." -1 month"))."' AND display='Y' AND isTest='')";	// product
//상품
if($category) {
	if($category=='all') {
		//
	}
	else {
		if( in_array($category, array('1','2','3')) ) {
			$where1.= " AND category='".$category."'";
		}
		else {
			if($category=='2A') $where1.= " AND category='2' AND mortgage_guarantees=''";
			if($category=='2B') $where1.= " AND category='2' AND mortgage_guarantees='1'";
			if($category=='3A') $where1.= " AND category='3' AND title LIKE '%면세점%'";
			if($category=='3B') $where1.= " AND category='3' AND title LIKE '%소상공인%'";
		}
	}
}
else {
	$category = '2';
}


//금액
$where2 = " WHERE A.amount >= '".$target_amount."' AND pid is not null"; // 금액

// 멤버

/*
if($pid) {
	if($pid=='none') {
		$where3 = " WHERE pid=''";
	}
	else {
		$where3 = ($pid=='all') ? " WHERE pid!=''" : " WHERE pid='".$pid."'";			// $pid=='all'  ::: pid가 기록된 모든 회원데이터
	}
}
*/
IF($member_type)
{
	$where3 =  " WHERE member_type='".$member_type."' ";

	IF($member_investor_type)
	{
 		$where3 .= " AND member_investor_type ='".$member_investor_type."' ";
	}
}

if($pid) {
	IF($where3) { $where3 = " AND "; } ELSE { $wher3 = " WHERE"; }
	if($pid=='none' || $pid == "all") {
		$where3 .= " pid=''";
	} else {
		$where3 .= " pid='".$pid."'";
	}
}

$sql= "
	SELECT
		A.member_idx,A.syndi_id,A.amount,A.cnt,
		IF(B.member_type='2','법인회원','개인회원') AS type_title,
		B.pid, B.mb_id, B.mb_hp, B.mb_co_reg_num, B.va_bank_code2, B.virtual_account2, B.va_private_name2,
		IF(B.member_type='2',mb_co_name,mb_name) AS mb_title
	FROM
		(
			SELECT
				t2.member_idx, t2.syndi_id,
				SUM(t2.amount) as amount,
				COUNT(*) as cnt
			FROM
				(SELECT idx, title FROM cf_product ".$where1.") t1
			JOIN
				(SELECT product_idx, member_idx, syndi_id, amount FROM cf_product_invest ".$where.") t2  ON t1.idx = t2.product_idx
			GROUP BY
				member_idx,
				syndi_id
		) A
	LEFT JOIN
		(SELECT mb_no, mb_name, mb_co_name, member_type, pid, mb_id, mb_hp, mb_co_reg_num, va_bank_code2, virtual_account2, va_private_name2 FROM g5_member ".$where3.") B ON A.member_idx = B.mb_no
	$where2
	ORDER BY
		binary(B.mb_id)";
//print_rr($sql);

$res  = sql_query($sql);
$rows = sql_num_rows($res);

$S_ARR_KEYS = array_keys($CONF['SYNDICATOR']);
$P_ARR_KEYS = array_keys($CONF['PARTNER']);

$page_title = "대상기간내 " . price_cutting($target_amount) . "원 이상 투자자 현황 (".preg_replace("/-/", ".", $sdate)."~".preg_replace("/-/", ".", $edate).")";

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

	echo "<div style='padding:10px 20px 30px;'>\n";
	echo "<div style='float:left;width:55%'>";
	echo "	<p>".$page_title. "\n";

	echo "	<form id='form1' method='get' action='".$_SERVER['PHP_SELF']."'>\n";
	echo "		투자기간: <input type='text' id='sdate' name='sdate' value='".$sdate."' class='datepicker'> ~ <input type='text' id='edate' name='edate' value='".$edate."' class='datepicker'>\n";

	echo "		<select id='target_amount' name='target_amount'>\n";
	echo "			<option value=''>::투자금액::</option>\n";
	if ($target_amount=="10000") $ab="selected";
	else $ab="";
	echo "			<option value='10000' $ab >1만원 이상</option>\n";
	for($i=1; $i<=20; $i++) {
		$value = 1000000 * $i;
		$selected = ($target_amount==$value) ? 'selected' : '';
		echo "			<option value='".$value."' {$selected}>".price_cutting($value)."원 이상</option>\n";
	}
	echo "		</select>";

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
	echo "			<option value='none' {$selected0[0]}>해당없음</option>\n";
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
			<option value='2A' {$selected2[1]}> -부동산(PF)</option>
			<option value='2B' {$selected2[2]}> -부동산(주택담보)</option>
			<option value='3' {$selected2[3]}>헬로페이</option>
			<option value='3A' {$selected2[4]}> -헬로페이(면세점)</option>
			<option value='3B' {$selected2[5]}> -헬로페이(소상공인)</option>
			<option value='1' {$selected2[6]}>동산</option>
		</select>

		<button type='submit'>검색</button>
		<button type='button' onClick='location.href=\"".$_SERVER['REQUEST_URI']."&download=1\"'>엑셀 다운로드</button></p>
	</form>
	</div>

	<form name='regfm' id='regfm'>
	<div style='float:left;width:45%;'>
		<table style='width:600px;'>
		<tr>
			<td rowspan='2' style='width:200px;'><textarea name='cphone' style='width:100%;height:110px;' placeholder='수신번호'></textarea></td>
			<td rowspan='2' style='width:300px;'><textarea name='content' style='width:100%;height:110px;' placeholder='내용'></textarea></td>
			<td style='width:100px;'><input type='text' name='sphone' style='width:100%;' placeholder='발송번호' value='15886760'></td>
		</tr>
		<tr>
			<td style='width:100px;'><input type='button' name='sbtn' value='SMS발송하기' OnClick=\"fn_send_sms('regfm',event);\"></td>
		<tr>
		</table>
	</div>

	<div style='clear:both;width:100%;'>


	<a href=\"#none\" OnClick=\"fn_all_check();\">전체</a> ".number_format($rows)."건<br/></div>\n";


}

echo "
	<table border=1 style='font-size:9pt'>
		<tr>
			<td align=center bgcolor='#D9E1F2'>NO</td>
			<td align=center bgcolor='#D9E1F2'>아이디</td>
			<td align=center bgcolor='#D9E1F2'>회원구분</td>
			<td align=center bgcolor='#D9E1F2'>법인명.성명</td>
			<td align=center bgcolor='#D9E1F2'>사업자.주민번호</td>
			<td align=center bgcolor='#D9E1F2'>연락처</td>
			<td align=center bgcolor='#D9E1F2'>투자건수</td>
			<td align=center bgcolor='#D9E1F2'>투자금액</td>
			<td align=center bgcolor='#D9E1F2'>리워드금액</td>
			<td align=center bgcolor='#D9E1F2'>입금은행</td>
			<td align=center bgcolor='#D9E1F2'>계좌번호</td>
			<td align=center bgcolor='#D9E1F2'>예금주</td>
			<td align=center bgcolor='#D9E1F2'>마케팅제휴사</td>
			<td align=center bgcolor='#D9E1F2'>신디케이션사</td>
		</tr>\n";

for($i=0,$j=$rows; $i<$rows; $i++,$j--) {
	$row = sql_fetch_array($res);

	$regist_num = ($row['type_title']=='개인회원') ? getJumin($row['member_idx']) : preg_replace("/(-| )/", "", $row['mb_co_reg_num']);

	echo "
		<tr>
			<td align=center>".$j."<input type='checkbox' name='idx[]' value='".masterDecrypt($row['mb_hp'],false)."'></td>
			<td align=center>".$row['mb_id']."</td>
			<td align=center>".$row['type_title']."</td>
			<td align=center>".$row['mb_title']."</td>
			<td align=center style=\"mso-number-format:'@';\">".$regist_num."</td>
			<td align=center style=\"mso-number-format:'@';\">".masterDecrypt($row['mb_hp'],false)."</td>
			<td align=right>".f_number($row['cnt'])."</td>
			<td align=right>".f_number($row['amount'])."</td>
			<td align=right>".f_number(fn_target_money($row['amount']))."</td>
			<td align=center>".$BANK[$row['va_bank_code2']]."</td>
			<td align=center style=\"mso-number-format:'@';\">".$row['virtual_account2']."</td>
			<td align=center>".$row['va_private_name2']."</td>
			<td align=center>".$CONF['PARTNER'][$row['pid']]['name']."</td>
			<td align=center>".$CONF['SYNDICATOR'][$row['syndi_id']]['name']."</td>
		</tr>\n";

}

echo "	</table></form>";

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


?>
	<script type="text/javascript">
	<!--
		var strProcUrl = "20191204_1.proc.php";

		function fn_send_sms(fmname,event)
		{
			if(!event)
			{
			   event =window.event;
			}
			if(event.stopPropagation)
			{
				event.preventDefault();
				event.stopPropagation();
			} else {
				event.cancelBubble = true;
			}
			var sphone = $("input[name='sphone']").val();
			var content = $("textarea[name='content']").val();

			/*
			if($("input:checkbox[name='idx[]']").is(":checked") == false)
			{
				alert("체크박스를 하나 이상 선택하셔야 합니다");
			}
			*/
			if(!sphone) {
				alert('발신번호를 입력하셔야 합니다');
				return false;
			}
			if(!content) {
				alert('내용을 입력하셔야 합니다.');
				return false;
			}

			var frm = $('#'+fmname);
			var str = frm.serialize();

			$.ajax({
				type : 'POST',
				url : strProcUrl,
				data : str,
				dataType: 'json',
				success : function(data){
					if(data.retcode == "OK"){
						var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));
					} else if(data.retcode == "X") {
						var stralert = decodeURIComponent(data.retalert);
						alert(stralert.replace("+"," "));
					}
				},
				error : function(XMLHttpRequest, textStatus, errorThrown){
					alert("처리중 오류가 발생하였습니다. 다시 시도하여주십시오");
					return false;
				}
			});
		}

		function fn_all_check()
		{
			var targetele = $("input:checkbox[name='idx[]']");
			if($("input:checkbox[name='idx[]']").is(":checked") == false)
			{
				$("input:checkbox[name='idx[]']").prop("checked", true);
			} else {
				$("input:checkbox[name='idx[]']").prop("checked", false);
			}
		}
	//-->
	</script>
<?php
sql_close();
exit;


?>
