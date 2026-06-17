<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = "500700";
include_once('./_common.php');


$g5['title'] = $menu['menu500'][7][1];
include_once (G5_ADMIN_PATH.'/admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$sql_search = " 1=1";
$sql_search.= " AND B.mb_level='1'";

if($member_type) {
	if($member_type=='1_1')      $sql_search.= " AND B.member_type='1' AND member_investor_type='1'";
	else if($member_type=='1_2') $sql_search.= " AND B.member_type='1' AND member_investor_type='2'";
	else if($member_type=='1_3') $sql_search.= " AND B.member_type='1' AND member_investor_type='3'";
	else                         $sql_search.= " AND B.member_type='$member_type'";
}

if($date_field) {
	if($sdate && $edate) {
		$sql_search.= " AND LEFT($date_field, 10) BETWEEN '".$sdate."' AND '".$edate."' ";
	}
	else {
		if($sdate)       $sql_search.= " AND LEFT($date_field, 10) >= '".$sdate."' ";
		else if($edate)  $sql_search.= " AND LEFT($date_field, 10) <= '".$edate."' ";
	}
}

if($key_search && $keyword) {
	$sql_search.= " AND $key_search LIKE '%$keyword%'";
}


$sql = "
	SELECT
		COUNT(A.mb_no) AS cnt
	FROM
		IB_auth_withdrawal A
	INNER JOIN
		g5_member B  ON A.mb_no=B.mb_no
	WHERE
		$sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page_rows = 50;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", A.rdate DESC ";
}
else {
	$sql_order.= " A.rdate DESC ";
}

$sql = "
	SELECT
		A.mb_no, A.account_num, A.auth_admin, A.rdate,
		B.mb_id, B.member_type, B.member_investor_type, B.is_creditor, B.is_owner_operator, B.mb_name, B.mb_co_name, B.mb_hp, B.mb_point,
		B.account_num AS now_account_num,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.mb_no AND invest_state='Y') AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=A.mb_no AND invest_state='Y') AS invest_amount
	FROM
		IB_auth_withdrawal A
	INNER JOIN
		g5_member B  ON A.mb_no=B.mb_no
	WHERE
		$sql_search
	ORDER BY
		$sql_order
	LIMIT
		$from_record, $page_rows";

//print_rr($sql,'font-size:9pt');
$result = sql_query($sql);
$rcount = $result->num_rows;

$page_total_trans_count = 0;
$page_total_trans_amount = 0;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);
	$LIST[$i]['mb_hp']           = masterDecrypt($LIST[$i]['mb_hp'], false);
	$LIST[$i]['now_account_num'] = masterDecrypt($LIST[$i]['now_account_num'], false);
	$LIST[$i]['account_num']     = preg_replace('/(-| )/', '', $LIST[$i]['now_account_num']);

	$res2 = sql_query("SELECT allow_remitter_name FROM IB_auth_deposit_to_amount WHERE mb_no='".$LIST[$i]['mb_no']."' ORDER BY rdate DESC");
	$LIST[$i]['ALLOW_REMITTERS'] = array();
	while($row = sql_fetch_array($res2)) {
		array_push($LIST[$i]['ALLOW_REMITTERS'], $row['allow_remitter_name']);
	}

	$page_total_trans_count+=1;
	$page_total_trans_amount+=$LIST[$i]['TR_AMT'];
}
$list_count = count($LIST);
$num = $total_count - $from_record;

$resx = sql_query("SELECT mb_id, mb_name FROM g5_member WHERE mb_level between '2' AND '10' ORDER BY mb_no");
while($RX = sql_fetch_array($resx)) {
	$ANAME[$RX['mb_id']] = $RX['mb_name'];
}

//print_rr($LIST,'font-size:9pt');

?>

<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

	<!-- 검색영역 START -->
	<div style="line-height:28px;">
		<form id="frmSearch" name= "frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select name="date_field" class="form-control">
					<option value="">::데이트 필드선택::</option>
					<option value="A.rdate" <?=($date_field=='A.rdate')?'selected':'';?>>설정일</option>
					<option value="B.mb_datetime" <?=($date_field=='B.mb_datetime')?'selected':'';?>>회원가입일</option>
				</select>
			</li>
			<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control datepicker" placeholder="대상일자(시작)"></li>
			<li>~</li>
			<li><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control datepicker" placeholder="대상일자(종료)"></li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select name="member_type" class="form-control">
					<option value="">::회원구분::</option>
					<option value="1" <? if($member_type == '1'){echo 'selected';} ?>>개인회원</option>
					<option value="1_1" <? if($member_type == '1_1'){echo 'selected';} ?>>개인회원(일반투자자)</option>
					<option value="1_2" <? if($member_type == '1_2'){echo 'selected';} ?>>개인회원(소득적격자)</option>
					<option value="1_3" <? if($member_type == '1_3'){echo 'selected';} ?>>개인회원(전문투자자)</option>
					<option value="2" <? if($member_type == '2'){echo 'selected';} ?>>법인회원</option>
				</select>
			</li>
			<li>
				<select name="key_search" class="form-control">
					<option value="">::필드선택::</option>
					<option value="A.account_num" <? if($key_search == 'A.account_num'){echo 'selected';} ?>>승인계좌번호</option>
					<option value="B.mb_no" <? if($key_search == 'B.mb_no'){echo 'selected';} ?>>회원번호</option>
					<option value="B.mb_id" <? if($key_search == 'B.mb_id'){echo 'selected';} ?>>아이디</option>
					<option value="B.mb_name" <? if($key_search == 'B.mb_name'){echo 'selected';} ?>>회원성명</option>
					<option value="B.account_num" <? if($key_search == 'B.account_num'){echo 'selected';} ?>>현재계좌번호</option>
				</select>
			</li>
			<li><input type="text" class="form-control" name="keyword" size="30" value="<?=$keyword;?>"></li>
			<li><button type="submit" class="btn btn-warning">검색</button></li>
			<li></li>
			<li>
				<select id="sort_field" class="form-control" style="width:150px;">
					<option value="">::정렬필드::</option>
					<option value="A.rdate" <?=($sort_field=='A.rdate')?'selected':'';?>>설정일시</option>
					<option value="B.mb_no" <?=($sort_field=='B.mb_no')?'selected':'';?>>회원번호</option>
					<option value="B.mb_name" <?=($sort_field=='B.mb_name')?'selected':'';?>>회원명</option>
					<option value="B.mb_point" <?=($sort_field=='B.mb_point')?'selected':'';?>>예치금</option>
					<option value="invest_count" <?=($sort_field=='invest_count')?'selected':'';?>>투자건수</option>
					<option value="invest_amount" <?=($sort_field=='invest_amount')?'selected':'';?>>투자금액</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('ASC');" class="btn btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
				<button type="button" onClick="sortList('DESC');" class="btn btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
			</li>
		</ul>
		</form>
	</div>
	<script>
	function sortList(param) {
		if(document.getElementById('sort_field').value!='') {
			url = '<?=$_SERVER['PHP_SELF']?>'
					+ '?date_field=<?=$date_field?>'
					+ '&sdate=<?=$sdate?>'
					+ '&edate=<?=$edate?>'
					+ '&member_type=<?=$member_type?>'
					+ '&key_search=<?=$key_search?>'
					+ '&keyword=<?=$keyword?>'
					+ '&sort_field=' + document.getElementById('sort_field').value
					+ '&sort=' + param

			location.href= url;
		}
		else {
			alert('정렬필드를 선택하십시요.'); document.getElementById('sort_field').focus();
		}
	}
	</script>
	<!-- 검색영역 E N D -->

	<div style="text-align:right;font-size:11px">차명입금 승인과정 적용일 : 2018년 10월 29일 22:00</div>
	<table id="dataList" class="table-striped table-bordered table-hover" style="font-size:12px;">
		<form id="frmTrans" name="frmTrans">
		<colgroup>
			<col style="width:2%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
			<col style="width:%">
		</colgroup>
		<thead style="font-size:13px">
		<tr>
			<th style="background:#F8F8EF"><input type="checkbox" id="chkall"></th>
			<th style="background:#F8F8EF">NO</th>
			<th style="background:#F8F8EF">회원번호</th>
			<th style="background:#F8F8EF">아이디</th>
			<th style="background:#F8F8EF">회원구분</th>
			<th style="background:#F8F8EF">성명 . 업체명</th>
			<th style="background:#F8F8EF">연락처</th>
			<th style="background:#F8F8EF">예치금</th>
			<th style="background:#F8F8EF">투자건수</th>
			<th style="background:#F8F8EF">투자금액</th>
			<th style="background:#F8F8EF">설정시 계좌번호</th>
			<th style="background:#F8F8EF">설정자</th>
			<th style="background:#F8F8EF">설정일시</th>
		</tr>
		</thead>
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {


		if($LIST[$i]['member_type']=='2') {
			$print_gubun = '법인';
			$print_name  = $LIST[$i]['mb_co_name'];
		}
		else {
			$print_gubun = '개인';
			$print_name  = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);

			if($LIST[$i]['member_investor_type']=='1') $print_gubun.= '일반';
			if($LIST[$i]['member_investor_type']=='2') $print_gubun.= '<b>소득적격</b>';
			if($LIST[$i]['member_investor_type']=='3') $print_gubun.= '<b>전문투자</b>';
		}
		$print_gubun.= '회원';

		if($LIST[$i]['member_type']=='2') {
			$print_mb_hp   = $LIST[$i]['mb_hp'];
			$print_account_num = $LIST[$i]['account_num'];
		}
		else {
			$print_mb_hp   = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_hp'] : substr($LIST[$i]['mb_hp'],0,strlen($LIST[$i]['mb_hp'])-4)."****";;
			$print_account_num = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['account_num'] : substr($LIST[$i]['account_num'],0,strlen($LIST[$i]['account_num'])-4)."****";;
		}

		$print_auth_flag = $print_setup_button = "";

		$svalue = $LIST[$i]['mb_no']."^".$LIST[$i]['account_num'];

		//$print_bgColor   = ($LIST[$i]['MEDIA_GBN']=='OK') ? '' : '#FFDDDD';
		$print_checkbox  = '<input type="checkbox" name="chk[]" value="'.$svalue.'" onClick="activeSubmit();">';

		$print_fColor1   = ($LIST[$i]['mb_point']) ? '' : '#AAA';
		$print_fColor2   = ($LIST[$i]['invest_count']) ? '' : '#AAA';
		$print_fColor3   = ($LIST[$i]['invest_amount']) ? '' : '#AAA';
		$print_fColor4   = ($LIST[$i]['account_num']!=$LIST[$i]['now_account_num']) ? '#FF2222' : '';

		$backDoor = ( in_array($member['mb_id'], array('admin_hellosiesta','admin_yr4msp','admin_sori9th','admin_romrom')) ) ? "<a href=\"javascript:;\" onClick=\"if(confirm('".$LIST[$i]['mb_name']." 회원에게 비상경계경보를 발령합니다.\\n중대한 사안이므로 신중히 결정하십시요.\\n\\n진행하시겠습니까?')){ location.replace('/adm/simple_login.php?mb_no=".$LIST[$i]['mb_no']."'); }\">.</a>" : "";


		if(in_array($LIST[$i]['mb_id'], $CONF['BLOCKOUT_ID'])) {
			$tr_style = 'border:2px solid red;background:#FFDDDD';
			$print_name.= "<font color='red'>(차단회원)</font>";
		}
		else {
			$tr_style = '';
			$print_name.= "";
		}

?>
		<tr align="center" style="background:<?=$print_bgColor?>;<?=$tr_style?>">
			<td><?=$print_checkbox?></td>
			<td><?=$num?></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$LIST[$i]['mb_no']?></a></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$LIST[$i]['mb_id']?></a></td>
			<td><?=$print_gubun?></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$print_name?></a> <?=$backDoor?></td>
			<td><?=$print_mb_hp?></td>
			<td align="right" style="color:<?=$print_fColor1?>"><?=number_format($LIST[$i]['mb_point'])?>원</td>
			<td align="right" style="color:<?=$print_fColor2?>"><?=number_format($LIST[$i]['invest_count'])?>건</td>
			<td align="right" style="color:<?=$print_fColor3?>"><?=number_format($LIST[$i]['invest_amount'])?>원</td>
			<td style="color:<?=$print_fColor4?>"><?=$print_account_num?></td>
			<td><?=$ANAME[$LIST[$i]['auth_admin']]?></td>
			<td style="color:blue;"><?=substr($LIST[$i]['rdate'],0,16)?></td>
		</tr>
<?
		$num--;
	}
}
else {
	echo '
		<tr>
			<td colspan="15" align="center">데이터가 없습니다.</th>
		</tr>' . PHP_EOL;
}
?>
		</form>
	</table>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

</div>

<div id="btnDiv" style="display:none;">
	<p id="button_area" class="text-center" style="width: 100%;padding: 10px 0 !important; margin: 0 !important;">
		<button type="button" id="frmTransSubmit" class="btn btn-danger" style="width:600px;">선택항목 설정내역 삭제</button>
	</p>
</div>

<? $qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']); ?>

<script>
$(function() {
	$("#chkall").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
		var checked_count = $("input:checkbox[name='chk[]']:checked").length;
		if(checked_count > 0) {
			$('#btnDiv').css('display','block');
		}
		else {
			$('#btnDiv').css('display','none');
		}
	});
});


$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>' + '?<?=$qstr?>&page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});

function activeSubmit() {
	var checked_count = $("input:checkbox[name='chk[]']:checked").length;
	if(checked_count > 0) {
		$('#btnDiv').css('display','block');
	}
	else {
		$('#btnDiv').css('display','none');
	}
}

$(document).ready(function(){
	var m_height = 54;
	top_position = $(document).height() - $(window).height() - m_height - $('#ft').height();
	$('#button_area').addClass('text-center button_area_scroll');
	$(window).scroll(function() {
		top_position = $(document).height() - $(window).height() - m_height - $('#ft').height();
		scroll_top = $(window).scrollTop();
		$('#top_position').val(top_position);
		$('#scroll_top').val(scroll_top);
		if(scroll_top <= top_position) {
			$('#button_area').addClass('text-center button_area_scroll');
		}
		else {
			$('#button_area').removeClass('button_area_scroll');
		}
	});
});

$('#frmTransSubmit').click(function() {
	var checked_count = $("input:checkbox[name='chk[]']:checked").length;
	if(checked_count > 0) {
		if(confirm('선택된 출금허용 설정내역 ' + checked_count + '건을 삭제 하시겠습니까?')) {
			params = $('#frmTrans').serialize();

			$.ajax({
				url : "./ajax.auth_withdrawal_proc.php",
				type: 'POST',
				data: {action:'delete', data:params},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				success:function(data, textStatus, jqXHR) {
					//$('#ajax_return_txt_zone').css('display','block'); $('#ajax_return_txt').val(data);
					alert(data); location.reload();
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			})

		}
	}
	else {
		alert('선택된 내역이 없습니다.');
	}
});

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<?

include_once ('../admin.tail.php');

?>