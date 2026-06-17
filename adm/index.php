<?
include_once('./_common.php');

if($member['mb_id']=='seintax') {
	header("Location: invitaion_event.php");
}


$g5['title'] = '관리자메인';
include_once('./admin.head.php');

$tab = ($tab) ? $tab : 0;

//echo "<span class='blinkEle'>점검중입니다.</span>"; exit;

?>

<style>
table {border-collapse:collapse; font-size:13px}
.tabmenu .tabX { height:42px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.tabmenu .tabX li { float:left; width:150px; margin-right:3px; line-height:40px; text-align:center; font-size:14px; color:#202020; background-color:#f7f7f7; border:1px solid #e5e5e5; border-bottom:0; cursor:pointer; }
.tabmenu .tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; }
.tabmenu .tabX li:last-child { margin:0; display:inline-block; }
.tabmenu .tabXarea { display:block;margin:0; padding:20px; border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
</style>

<div style="width:100%">
	<h2 style="margin-left:10px;">정산관리</h2>
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<div class="tabmenu" style="margin:0 auto">
				<ul class="tabX" style="width:100%;list-style:none;padding-left:0px;margin:0;">
					<li data-gubun="all" <?=($tab==0)?'class="on"':''?>>전체</li>
					<li data-gubun="2A" <?=($tab==1)?'class="on"':''?>>부동산(PF,기타)</li>
					<li data-gubun="2B" <?=($tab==2)?'class="on"':''?>>부동산(주택담보)</li>
					<li data-gubun="3A" <?=($tab==3)?'class="on"':''?>>헬로페이(면세점)</li>
					<li data-gubun="3B" <?=($tab==4)?'class="on"':''?>>헬로페이(소상공인)</li>
					<li data-gubun="1" <?=($tab==5)?'class="on"':''?>>동산</li>
				</ul>
				<div class="tabXarea">
				</div>
			</div>

			<script>
			// 탭 기능
			$(document).ready(function(){
				$(this).addClass('on').siblings().removeClass('on');
				getList('');
			});
			$('.tabmenu li').click(function() {
				$(this).addClass('on').siblings().removeClass('on');
				//var cur = $(this).index();
				//$('.tabXarea:eq('+cur+')').show();
				getList($(this).data('gubun'));
			});

			getList = function(category) {
				$.ajax({
					url: './ajax_open_product_list.php',
					type: 'get',
					data:{ ca:category },
					success: function(result) {
						$('.tabXarea').html(result);
					},
					beforeSend: function() { loading('on'); },
					complete: function() { loading('off'); },
					error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
			</script>

		</div>
	</div>
</div>

<div style="width:100%">
	<h2 style="margin-left:10px;">최근 상품 등록현황</h2>
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th style="background:#F8F8EF">품번</th>
					<th style="background:#F8F8EF">호번</th>
					<th style="background:#F8F8EF">상품명</th>
					<th style="background:#F8F8EF">진행상태</th>
					<th style="background:#F8F8EF">등록일</th>
					<th style="background:#F8F8EF">모집기간</th>
					<th style="background:#F8F8EF">투자자수</th>
					<th style="background:#F8F8EF">모집금액</th>
					<th style="background:#F8F8EF">모집율</th>
				</tr>
<?
$sql = "
	SELECT
		idx, start_num, state, category, category2, mortgage_guarantees, title, recruit_amount, invest_return, open_datetime, start_datetime, recruit_period_start, recruit_period_end, insert_date
	FROM
		cf_product
	WHERE 1
		AND state NOT IN('3','6','7')
		AND display='Y' AND isTest=''
	ORDER BY
		start_num DESC
	LIMIT 5";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$row = sql_fetch_array($res);

	// 상품별 투자건수,금액
	$row2 = sql_fetch("
		SELECT
			COUNT(idx) AS invest_count,
			IFNULL(SUM(amount),0) AS invest_amount
		FROM
			cf_product_invest
		WHERE 1
			AND product_idx='".$row['idx']."' AND invest_state='Y'");

	$new_mark = (time()-strtotime($row['open_datetime']) < 86400) ? '<span class="new_mark">new</span>' : '';

	$state = '';

	if($row['state']=='') {
		if($row['open_datetime'] > G5_TIME_YMDHIS)   $state = '오픈대기중';
		if($row['open_datetime'] <= G5_TIME_YMDHIS && $row['start_datetime'] > G5_TIME_YMDHIS) $state = '투자대기중';
		if($row['start_datetime'] <= G5_TIME_YMDHIS && $row['invest_end_date'] == '')          $state = '투자모집중';
		if($row['start_datetime'] <= G5_TIME_YMDHIS && $row['invest_end_date'])                $state = '투자모집완료';
	}
	else {
		switch($row['state']) {
			case '1' : $state = '이자상환중';   break;
			case '2' : $state = '상품마감';	    break;
			case '4' : $state = '부실';         break;
			case '5' : $state = '중도일시상환'; break;
			case '6' : $state = '대출계약취소'; break;
		}
	}

	$invest_perc = sprintf("%.2f", ( $row2['invest_amount'] / $row['recruit_amount'] ) * 100);

?>
				<tr class="odd">
					<td align="center"><?=$row['idx']?></td>
					<td align="center"><?=$row['start_num']?></td>
					<td align="left">
						<?=$new_mark?>
						<?=$row['title']?>
					</td>
					<td align="center"><?=$state?></td>
					<td align="center"><?=substr($row['insert_date'], 0, 10)?></td>
					<td align="center"><?=$row['recruit_period_start']?> ~ <?=$row['recruit_period_end']?></td>
					<td align="center"><?=number_format($row2['invest_count'])?></td>
					<td align="right"><?=number_format($row2['invest_amount'])?> / <?=number_format($row['recruit_amount'])?></td>
					<td align="right"><?=floatRtrim($invest_perc)?>%</td>
				</tr>
<?
}
sql_free_result($res);
?>
			</table>
			<!--
			<div style="width: 100%; text-align: center;">
				<ul class="pagination"><?=$pagination?></ul>
			</div>
			//-->

		</div>
	</div>
</div>

<div id="loan_request" style="width:100%">
	<h2 style="margin-left:10px;">담보대출 신청현황</h2>
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th style="width:60px;background:#F8F8EF">NO.</th>
					<th style="background:#F8F8EF">구분</th>
					<th style="background:#F8F8EF">성명.법인명</th>
					<th style="background:#F8F8EF">연락처</th>
					<th style="background:#F8F8EF">이메일</th>
					<th style="background:#F8F8EF">물건소재지</th>
					<th style="background:#F8F8EF">희망대출금</th>
					<th style="background:#F8F8EF">대출기간</th>
					<th style="background:#F8F8EF">대출목적</th>
					<th style="background:#F8F8EF">등록일시</th>
					<th style="background:#F8F8EF">내용</th>
				</tr>
<?
$TYPE     = array('1'=>'아파트담보대출신청', '2'=>'취급법인유동화신청');
$RELATION = array('1'=>'본인', '2'=>'가족', '3'=>'중개인');
$PERIOD   = array('6'=>'6개월', '9'=>'9개월', '12'=>'12개월', '12+'=>'12개월 초과');
$PURPOSE  = array('1'=>'기대출상환', '2'=>'기대출상환 및 추가대출', '3'=>'선순위대출', '4'=>'사업자금', '5'=>'전세퇴거자금', '6'=>'기타');

$sql = "
	SELECT
		*
	FROM
		cf_apat_loan_request
	WHERE 1=1
		AND blind=''
	ORDER BY
		idx DESC
	LIMIT 5";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

for($i=0,$num=$rows; $i<$rows; $i++,$num--) {
	$row = sql_fetch_array($res);

	$new_mark = (time()-strtotime($row['regdate']) < 86400) ? '<span class="new_mark">new</span>' : '';

	$print_type = $TYPE[$row['type']];
	$print_hp  = masterDecrypt($row['hp'], false);
	$print_hp  = substr($print_hp, 0, strlen($print_hp)-4) . "****";

	$print_loc = $print_wamt = $print_purpose = $print_period = $print_wtime = '';

	if($row['type']=='1') {
		$print_name    = $row['name'];
		$print_loc     = $row['loc'];
		$print_wamt    = price_cutting($row['wamt']).'원';
		$print_purpose = $PURPOSE[$row['purpose']];
		$print_period  = $PERIOD[$row['period']];
		$print_wtime   = $row['wtime'];
	}
	else {
		$print_name = $row['co_name'];
	}

?>
				<tr>
					<td align="center"><?=$num?></td>
					<td>
						<?=$new_mark?>
						<?=$print_type?>
					</td>
					<td align="center"><?=$print_name?></td>
					<td align="center"><?=$print_hp?></td>
					<td align="center"><?=$row['email']?></td>
					<td align="center"><?=$print_loc?></td>
					<td align="center"><?=$print_wamt?></td>
					<td align="center"><?=$print_period?></td>
					<td align="center"><?=$print_purpose?></td>
					<td align="center"><?=substr($row['regdate'],0,16)?></td>
					<td align="center"><button type="button" onClick="location.href='/adm/loan_request/request.php?idx=<?=$row['idx']?>'" class="btn btn-sm btn-default">상세보기</button></td>
				</tr>
<?
}
sql_free_result($res);
?>

			</table>

		</div>
	</div>
</div>

<div style="width:100%">
	<h2 style="margin-left:10px;">가상계좌 현황</h2>
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<table class="table table-bordered table-hover">
				<colgroup>
					<col style="width:25%">
					<col style="width:25%">
					<col style="width:25%">
					<col style="width:25%">
				</colgroup>
				<tr>
					<th style="background:#F8F8EF">용도구분</th>
					<th style="background:#F8F8EF">발급은행</th>
					<th style="background:#F8F8EF">전체보유분</th>
					<th style="background:#F8F8EF">미사용분</th>
				</tr>
<?
	$gubun = "예치금 입금용";
	$ACCT  = sql_fetch("
		SELECT
			(SELECT COUNT(org_cd) AS cnt FROM IB_vact WHERE acct_no!='') AS used_cnt,
			(SELECT COUNT(org_cd) AS cnt FROM IB_vact WHERE acct_no!='' AND acct_st='0') AS unused_cnt");

	$fcolor = ($ACCT['unused_cnt'] <= 100) ? '#FF2222' : '';
	if($ACCT['unused_cnt'] <= 300) $acct_arlim_start1 = true;
?>
				<tr class="odd">
					<td align="center" style="color:<?=$fcolor?>"><?=$gubun?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=$BANK['088']?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT['used_cnt'])?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT['unused_cnt'])?></td>
				</tr>
<?
	if($x > false) {
		$VBANK_KEYS = array_keys($VBANK);
		for($i=0; $i<count($VBANK); $i++) {

			$ACCT = sql_fetch("
				SELECT
					(SELECT COUNT(org_cd) AS cnt FROM vacs_vact WHERE bank_cd='".$VBANK_KEYS[$i]."' AND acct_no!='') AS used_cnt,
					(SELECT COUNT(org_cd) AS cnt FROM vacs_vact WHERE bank_cd='".$VBANK_KEYS[$i]."' AND acct_no!='' AND acct_st='0') AS unused_cnt");

			$fcolor = '#ccc';
?>
				<tr class="odd">
					<td align="center" style="color:<?=$fcolor?>"><?=$gubun?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=$VBANK[$VBANK_KEYS[$i]]?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT['used_cnt'])?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT['unused_cnt'])?></td>
				</tr>
<?
		}
	}

	$gubun = "대출금 상환용";
	$ACCT2  = sql_fetch("
		SELECT
			(SELECT COUNT(org_cd) AS cnt FROM IB_vact_hellocrowd WHERE acct_no!='') AS used_cnt,
			(SELECT COUNT(org_cd) AS cnt FROM IB_vact_hellocrowd WHERE acct_no!='' AND acct_st='0') AS unused_cnt");

	$fcolor = ($ACCT['unused_cnt'] <= 100) ? '#FF2222' : '';
	if($ACCT2['unused_cnt'] <= 300) $acct_arlim_start2 = true;
?>
				<tr class="odd">
					<td align="center" style="color:<?=$fcolor?>"><?=$gubun?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=$BANK['088']?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT2['used_cnt'])?></td>
					<td align="center" style="color:<?=$fcolor?>"><?=number_format($ACCT2['unused_cnt'])?></td>
				</tr>
			</table>

		</div>
	</div>
</div>

<div style="width:100%">
	<h2 style="margin-left:10px;">금일 신규회원 현황</h2>
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<table class="table table-striped table-bordered table-hover">
				<tr>
					<th style="background:#F8F8EF">번호</th>
					<th style="background:#F8F8EF">아이디</th>
					<th style="background:#F8F8EF">회원구분</th>
					<th style="background:#F8F8EF">투자유형구분</th>
					<th style="background:#F8F8EF">성명/법인명</th>
					<th style="background:#F8F8EF">가입일시</th>
					<th style="background:#F8F8EF">신디케이션</th>
					<th style="background:#F8F8EF">관리</th>
				</tr>
<?
$datetime_s = date('Y-m-d') . ' 00:00:00';
$datetime_e = date('Y-m-d') . ' 23:59:59';

$sql = "
	SELECT
		mb_no, mb_id, mb_level, member_type, member_investor_type, is_creditor, mb_name, mb_co_name, mb_datetime, syndi_id
	FROM
		g5_member
	WHERE 1
		AND member_group='F' AND mb_level='1'
		AND mb_datetime BETWEEN '$datetime_s' AND '$datetime_e'
	ORDER BY
		mb_datetime DESC";
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$member_type   = array('1' => '개인회원', '2' => '법인회원', '3' => 'SNS회원');
$investor_type = array('1' => '일반투자자', '2' => '소득적격투자자', '3' => '전문투자자');
$syndi = array(
	'wowstar'     => array('name'=>'와우스타'),
	'finnq'       => array('name'=>'핀크'),
	'chosun'      => array('name'=>'땅집고'),
	'r114'        => array('name'=>'부동산114'),
	'oligo'       => array('name'=>'올리고'),
	'itembay'     => array('name'=>'아이템베이'),
	'kakaopay'    => array('name'=>'카카오페이')
);

for($i=0,$num=$rows; $i<$rows; $i++,$num--) {
	$row = sql_fetch_array($res);

	$new_mark = (time()-strtotime($row['mb_datetime']) < 86400) ? '<span class="new_mark">new</span>' : '';
	$print_name = ($row['member_type']=='2') ? $row['mb_co_name'] : $row['mb_name'];

?>
				<tr class="odd">
					<td align="center"><?=$num?></td>
					<td>
						<?=$new_mark?>
						<?=$row['mb_id']?>
					</td>
					<td align="center"><?=$member_type[$row['member_type']]?></td>
					<td align="center"><?=$investor_type[$row['member_investor_type']]?></td>
					<td align="center"><?=$print_name?></td>
					<td align="center"><?=substr($row['mb_datetime'], 0, 16)?></td>
					<td align="center"><?=$syndi[$row['syndi_id']]['name']?></td>
					<td align="center"><a href="./member/member_view.php?mb_id=<?=$row['mb_id']?>" class="btn btn-sm btn-default">상세보기</a></td>
				</tr>
<?
}
sql_free_result($res);
?>
			</table>
			<!--
			<div style="width: 100%; text-align: center;">
				<ul class="pagination"><?=$pagination?></ul>
			</div>
			//-->

		</div>
	</div>
</div>



<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});

$(document).ready(function() {
<? if($acct_arlim_start1) { ?>
	alert('예치금 입금용 가상계좌 여유분이 부족합니다. 추가 발급 요청하십시요.');
	location.href = "#virtual_account";
<? } ?>
<? if($acct_arlim_start2) { ?>
	alert('대출금 상환용 가상계좌 여유분이 부족합니다. 추가 발급 요청하십시요.');
	location.href = "#virtual_account";
<? } ?>
});
</script>

<?
include_once ('./admin.tail.php');
?>