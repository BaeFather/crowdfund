<?
###############################################################################
##	차명입금자 목록
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = "500600";
include_once('./_common.php');


$g5['title'] = $menu['menu500'][6][1];
include_once (G5_ADMIN_PATH.'/admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$sql_search = " 1=1";
$sql_search.= " AND REPLACE(TRIM(A.REMITTER_NM),' ','') != REPLACE(TRIM(B.mb_name), ' ','')";
$sql_search.= " AND A.REMITTER_NM NOT IN('(주)헬로핀테크','8월후기이벤트','보정입금')";
$sql_search.= " AND A.TR_AMT_GBN='10'";
$sql_search.= " AND B.mb_level IN('1','200') AND B.member_type='1'";
//$sql_search.= " AND AND B.is_creditor='N'";
$sql_search.= " AND B.finnq_userid=''";

if($date_field=='A.SR_DATE') {
	$_sdate = preg_replace("/-/", "", $sdate);
	$_edate = preg_replace("/-/", "", $edate);
	if($sdate && $edate) {
		$sql_search.= " AND $date_field BETWEEN $_sdate AND $_edate ";
	}
	else {
		if($sdate)       $sql_search.= " AND $date_field >= $_sdate ";
		else if($edate)  $sql_search.= " AND $date_field <= $_edate ";
	}
}
else if($date_field=='A.auth_date') {
	if($sdate && $edate) {
		$sql_search.= " AND LEFT($date_field, 10) BETWEEN $sdate AND $edate ";
	}
	else {
		if($sdate)       $sql_search.= " AND LEFT($date_field, 10) >= $sdate ";
		else if($edate)  $sql_search.= " AND LEFT($date_field, 10) <= $edate ";
	}
}

if ($key_search == "A.trans_to_point") $sql_search.=" AND A.trans_to_point<>'OK' ";

if($key_search && $keyword) {
	$sql_search.= " AND $key_search LIKE '%$keyword%'";
}


$sql = "
	SELECT
		COUNT(A.SR_DATE) AS cnt,
		IFNULL(SUM(A.TR_AMT), 0) AS amount
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID=B.mb_no
	WHERE
		$sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];
$total_trans_count  = $row['cnt'];
$total_trans_amount = $row['amount'];

$page_rows = 50;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql_order = "";
if($sort_field) {
	if($sort_field=='A.ERP_TRANS_DT') {
		$sql_order.= $sort_field." ".$sort." ";
	}
	else {
		$sql_order.= $sort_field." ".$sort.", A.ERP_TRANS_DT DESC ";
	}
}
else {
	$sql_order.= " CASE WHEN A.trans_to_point='OK' THEN 2 ELSE 1 END, ";
	$sql_order.= " A.ERP_TRANS_DT DESC ";
}

$sql = "
	SELECT
		A.SR_DATE, A.FB_SEQ, A.CUST_ID, A.ACCT_NB, A.TR_AMT, A.ERP_TRANS_DT, A.REMITTER_NM, A.trans_to_point, A.manual_auth, A.auth_admin, A.auth_date,
		B.mb_no, B.mb_level, B.mb_id, B.member_type, B.mb_name, B.mb_hp, B.finnq_userid,
		A.sms_to_admin,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.CUST_ID AND invest_state='Y') AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=A.CUST_ID AND invest_state='Y') AS invest_amount
	FROM
		IB_FB_P2P_IP A
	LEFT JOIN
		g5_member B  ON A.CUST_ID=B.mb_no
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
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	// 입금이 발생한 시점의 포인트 기록 가져오기
	$compare_date = date("Y-m-d H:i:s", strtotime($LIST[$i]['ERP_TRANS_DT']));
	$POINTLOG =	sql_fetch("SELECT IFNULL(po_mb_point, 0) AS po_mb_point FROM g5_point WHERE mb_no='".$LIST[$i]['mb_no']."' AND po_datetime <= '".$compare_date."' ORDER BY po_id DESC LIMIT 1");
	$LIST[$i]['realtime_point'] = (int)$POINTLOG['po_mb_point'];

	$res2 = sql_query("SELECT allow_remitter_name FROM IB_auth_deposit_to_amount WHERE mb_no='".$LIST[$i]['mb_no']."' ORDER BY rdate DESC");
	$LIST[$i]['ALLOW_REMITTERS'] = array();
	while($row = sql_fetch_array($res2)) {
		array_push($LIST[$i]['ALLOW_REMITTERS'], $row['allow_remitter_name']);
	}

	$page_total_trans_count+=1;
	$page_total_trans_amount+=$LIST[$i]['TR_AMT'];
}
sql_free_result($result);
$list_count = count($LIST);
$num = $total_count - $from_record;

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
				<select name="date_field" class="form-control input-sm">
					<option value="">::데이트 필드선택::</option>
					<option value="A.SR_DATE" <?=($date_field=='A.SR_DATE')?'selected':'';?>>입금일</option>
					<option value="A.auth_date" <?=($date_field=='A.auth_date')?'selected':'';?>>전환처리일</option>
				</select>
			</li>
			<li><input type="text" id="sdate" name="sdate" value="<?=$sdate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(시작)"></li>
			<li>~</li>
			<li><input type="text" id="edate" name="edate" value="<?=$edate?>" readonly class="form-control input-sm datepicker" placeholder="대상일자(종료)"></li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select name="key_search" class="form-control input-sm">
					<option value="">::필드선택::</option>
					<option value="A.REMITTER_NM" <? if($key_search == 'A.REMITTER_NM'){echo 'selected';} ?>>입금자명</option>
					<option value="A.ACCT_NB" <? if($key_search == 'A.ACCT_NB'){echo 'selected';} ?>>입금계좌번호</option>
					<option value="B.mb_no" <? if($key_search == 'B.mb_no'){echo 'selected';} ?>>회원번호</option>
					<option value="B.mb_id" <? if($key_search == 'B.mb_id'){echo 'selected';} ?>>아이디</option>
					<option value="B.mb_name" <? if($key_search == 'B.mb_name'){echo 'selected';} ?>>회원성명</option>
					<option value="A.trans_to_point" <? if($key_search == 'A.trans_to_point'){echo 'selected';} ?>>미전환</option>
				</select>
			</li>
			<li><input type="text" class="form-control input-sm" name="keyword" size="30" value="<?=$keyword;?>"></li>
			<li><button type="submit" class="btn btn-sm btn-warning">검색</button></li>
			<li></li>
			<li>
				<select id="sort_field" class="form-control input-sm" style="width:150px;">
					<option value="">::정렬필드::</option>
					<option value="A.ERP_TRANS_DT" <?=($sort_field=='A.ERP_TRANS_DT')?'selected':'';?>>입금일시</option>
					<option value="B.mb_no" <?=($sort_field=='B.mb_no')?'selected':'';?>>회원번호</option>
					<option value="B.mb_id" <?=($sort_field=='A.mb_id')?'selected':'';?>>등록일시</option>
					<option value="B.mb_name" <?=($sort_field=='A.mb_name')?'selected':'';?>>회원명</option>
					<option value="invest_count" <?=($sort_field=='invest_count')?'selected':'';?>>투자건수</option>
					<option value="invest_amount" <?=($sort_field=='invest_amount')?'selected':'';?>>투자금액</option>
				</select>
			</li>
			<li>
				<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
				<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
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

	<div style="text-align:right;font-size:11px">차명입금 승인과정 적용일 : 2018년 10월 19일 18:00</div>
	<table id="dataList" class="table-striped table-bordered table-hover" style="font-size:12px;">
		<form id="frmTrans" name="frmTrans">
		<colgroup>
			<col style="width:2%">
			<col style="width:6%">
			<col style="width:7.5%">
			<col style="width:7.5%">
			<col style="width:%">
			<col style="width:7.5%">
			<col style="width:6%">
			<col style="width:7.5%">
			<col style="width:7.5%">
			<col style="width:7.5%">
			<col style="width:7.5%">
			<col style="width:6%">
			<col style="width:7.5%">
		</colgroup>
		<thead style="font-size:13px">
		<tr>
			<th style="background:#F8F8EF"><input type="checkbox" id="chkall"></th>
			<th style="background:#F8F8EF">NO</th>
			<th style="background:#F8F8EF">입금일시</th>
			<th style="background:#F8F8EF">입금액</th>
			<th style="background:#F8F8EF">입금계좌</th>
			<th style="background:#F8F8EF">입금자명</th>
			<th style="background:#F8F8EF">예치금전환</th>
			<th style="background:#F8F8EF">회원번호</th>
			<th style="background:#F8F8EF">아이디</th>
			<th style="background:#F8F8EF">회원성명</th>
			<th style="background:#F8F8EF">연락처</th>
			<th style="background:#F8F8EF">예치금(시점)</th>
			<th style="background:#F8F8EF">투자건수</th>
			<th style="background:#F8F8EF">투자금액</th>
			<th style="background:#F8F8EF">전환일시</th>
		</tr>
		</thead>
		<!-- 합계 -->
		<tr style="background:#EEEEFF;color:red">
			<td colspan="2" align="center">합계</td>
			<td colspan="2" align="right" style="font-size:12px;">
				전체. <?=number_format($total_trans_count)?>건 / <?=number_format($total_trans_amount)?>원<br>
				<span style="color:#FF6633">페이지. <?=number_format($page_total_trans_count)?>건 / <?=number_format($page_total_trans_amount)?>원</span>
			</td>
			<td colspan="11"></td>
		</tr>
		<!-- 합계 -->
<?
if($list_count) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		if($LIST[$i]['member_type']=='2') {
			$print_mb_name = $LIST[$i]['mb_co_name'];
			$print_mb_hp   = $LIST[$i]['mb_hp'];
			$print_account_num = $LIST[$i]['account_num'];
		}
		else {
			$print_mb_name = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_name'] : hanStrMasking($LIST[$i]['mb_name']);
			$print_mb_hp   = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['mb_hp'] : substr($LIST[$i]['mb_hp'],0,strlen($LIST[$i]['mb_hp'])-4)."****";;
			$print_account_num = ($_SESSION['ss_accounting_admin']) ? $LIST[$i]['account_num'] : substr($LIST[$i]['account_num'],0,strlen($LIST[$i]['account_num'])-4)."****";;
		}


		$print_auth_flag = $print_setup_button = "";

		if($LIST[$i]['ERP_TRANS_DT'] < '20181019130000') {
			$print_auth_flag = ($LIST[$i]['trans_to_point']=='OK') ? '<font color="gray">승인(구)</font>' : '';
		}
		else {
			if($LIST[$i]['manual_auth']=='1') {
				if($LIST[$i]['trans_to_point']=='OK') {
					$print_auth_flag = '<font color="blue">승인</font>';
				}
				else {
					$print_auth_flag = '';
				}

				if( in_array($LIST[$i]['REMITTER_NM'], $LIST[$i]['ALLOW_REMITTERS']) ) {
					$print_setup_button = "<button type=\"button\" DISABLED style=\"margin-top:4px;padding:2px 8px;border:1px solid;opacity:0.35;\">자동승인</button>";
				}
				else {
					$print_setup_button = "<button type=\"button\" onClick=\"allowRemitter('".$LIST[$i]['mb_no']."','".$LIST[$i]['REMITTER_NM']."');\" style=\"margin-top:4px;padding:2px 8px;border:1px solid;\">자동승인</button>";
				}
			}
			else {
				if($LIST[$i]['trans_to_point']=='OK') {
					$print_auth_flag = '<font color="blue">자동승인</font>';
				}
				else {
					$print_auth_flag = '';
				}
			}
		}

		$svalue = $LIST[$i]['FB_SEQ']."^".$LIST[$i]['ERP_TRANS_DT'];

		$print_bgColor   = ($LIST[$i]['trans_to_point']=='OK') ? '' : '#FFDDDD';
		$print_checkbox  = ($LIST[$i]['trans_to_point']=='OK') ? '' : '<input type="checkbox" name="chk[]" value="'.$svalue.'" onClick="activeSubmit();">';

		$print_fColor1   = ($LIST[$i]['realtime_point']) ? '' : '#AAA';
		$print_fColor2   = ($LIST[$i]['invest_count']) ? '' : '#AAA';
		$print_fColor3   = ($LIST[$i]['invest_amount']) ? '' : '#AAA';

		$backDoor = ( in_array($member['mb_id'], array('admin_hellosiesta','admin_yr4msp','admin_sori9th','admin_romrom')) ) ? "<a href=\"javascript:;\" onClick=\"if(confirm('".$LIST[$i]['mb_name']." 회원에게 비상경계경보를 발령합니다.\\n중대한 사안이므로 신중히 결정하십시요.\\n\\n진행하시겠습니까?')){ location.replace('/adm/simple_login.php?mb_no=".$LIST[$i]['mb_no']."'); }\">.</a>" : "";


		$tr_style = (in_array($LIST[$i]['mb_id'], $CONF['BLOCKOUT_ID'])) ? 'border:2px solid red;background:#FFDDDD' : '';

?>
		<tr align="center" style="background:<?=$print_bgColor?>;<?=$tr_style?>">
			<td><?=$print_checkbox?></td>
			<td><?=$num?></td>
			<td><?=date("Y-m-d H:i", strtotime($LIST[$i]['ERP_TRANS_DT']))?></td>
			<td align="right"><?=number_format((int)$LIST[$i]['TR_AMT'])?>원</td>
			<td><a href="?key_search=A.ACCT_NB&keyword=<?=$LIST[$i]['ACCT_NB']?>"><?=$LIST[$i]['ACCT_NB']?></td>
			<td>
				<div><?=$LIST[$i]['REMITTER_NM']?></div>
				<?=$print_setup_button?>
			</td>
			<td><?=$print_auth_flag?></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$LIST[$i]['mb_no']?><?if($LIST[$i]['mb_level']=='200'){?>&nbsp;<span style="color:#FF2222">(탈퇴)</span><?}?></a></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$LIST[$i]['mb_id']?></a></td>
			<td><a href="/adm/member/member_list.php?key_search=A.mb_no&keyword=<?=$LIST[$i]['mb_no']?>" style="color:#000"><?=$print_mb_name?></a> <?=$backDoor?></td>
			<td><?=$print_mb_hp?></td>
			<td align="right" style="color:<?=$print_fColor1?>"><?=number_format($LIST[$i]['realtime_point'])?>원</td>
			<td align="right" style="color:<?=$print_fColor2?>"><?=number_format($LIST[$i]['invest_count'])?>건</td>
			<td align="right" style="color:<?=$print_fColor3?>"><?=number_format($LIST[$i]['invest_amount'])?>원</td>
			<td style="color:blue;"><?=substr($LIST[$i]['auth_date'],0,16)?></td>
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
		<button type="button" id="frmTransSubmit" class="btn btn-danger" style="width:600px;">선택항목 예치금 전환승인</button>
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
		if(confirm('선택된 차명입금 내역 ' + checked_count + '건에 대하여 예치금 전환 승인하시겠습니까?')) {
			params = $('#frmTrans').serialize();

			$.ajax({
				url : "./others_deposit_auth_proc.php",
				type: 'POST',
				data: {action:'deposit_trans', data:params},
				contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
				success:function(data, textStatus, jqXHR) {
					alert(data); location.reload();
					//$('#ajax_return_txt_zone').css('display','block');
					//$('#ajax_return_txt').val(data);
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

function allowRemitter(no, name) {
	if( confirm(no+'번 회원의 가상계좌로 입금되는 입금자명 "'+name+'"의 입금액을 헬로펀딩 예치금으로 자동전환 되도록 설정 하시겠습니까?') ) {
		$.ajax({
			url : "./others_deposit_auth_proc.php",
			type: 'POST',
			data: {
				action:'allow_remitter',
				member_idx:no,
				allow_remitter_name:name
			},
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			success:function(data, textStatus, jqXHR) {
				//$('#ajax_return_txt_zone').css('display','block');
				//$('#ajax_return_txt').val(data);
				if(data=='OK') { alert('자동승인 설정이 완료되었습니다.');location.reload(); }
				else if(data=='DUPLICATE ORDER') { alert('이미 자동승인 설정된 입금자명 입니다.'); }
				else if(data=='SYSTEM ERROR') { alert('DB 시스템 오류 입니다. 기술관리자에게 문의하십시요.'); }
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function () { alert("통신 오류 입니다. 기술관리자에게 문의하십시요."); }
		})
	}
}

$(document).ready(function() {
	$('#dataList').floatThead();
});
</script>

<?

include_once ('../admin.tail.php');

?>