<?

include_once("../syndication_config.php");
include_once('./head.php');

if(!$is_admin && !$_SESSION['syndi_admin_login']) {
	msg_go('로그인 하십시요.', './');
}

$syndi_id = $_CONF['SYNDI_ID'];


$g5['title'] = '관리자 > 가입자 현황';

foreach($_REQUEST as $k=>$v) {
	$$_REQUEST[$k] = $v;
}

$sql_common = " FROM {$g5['member_table']} A";

$sql_search = " WHERE A.chosun_userid='chosun'";
//$sql_search = " WHERE syndi_id='$syndi_id'";
$sql_search.= " AND A.mb_leave_date=''";
//$sql_search.= " AND binary(A.mb_memo) NOT REGEXP '[0-9]+ 삭제함' ";
//$sql_search.= " AND A.mb_memo NOT REGEXP '[0-9]+ 삭제함' ";


if($member_group != '') {
	$sql_search .= " AND A.member_group='F' ";
}

if($member_type != '') {
	$sql_search .= " AND A.member_type='$member_type' ";
}

if($member_investor_type != '') {
	$sql_search .= " AND A.member_investor_type='$member_investor_type' ";
}

if($is_creditor == 'Y') {
	$sql_search .= " AND A.is_creditor='Y' ";
}

if($mb_mailling == '1') {
	$sql_search .= " AND A.mb_mailling='1' ";
}

if($mb_level) {
	if($mb_level=='null') {
		$sql_search .= " AND A.mb_level='0' ";
	}
	else if($mb_level='100') {
		$sql_search .= " AND A.mb_level='100' ";
	}
}
else {
	$sql_search .= " AND A.mb_level IN('1','2','3','4','5','100') ";
}

if($mb_sms == '1') {
	$sql_search .= " AND A.mb_sms='1' ";
}

if($start_date != '' && $end_date != '') {
	$sql_search .= " AND A.mb_datetime >= '$start_date 00:00:00' AND A.mb_datetime <= '$end_date 23:59:59' ";
}

if($start_point != '' && $end_point != '') {
	$sql_search .= " AND A.mb_point >= '$start_point' AND A.mb_point <= '$end_point' ";
}

if($mb_sms == '1') {
	$sql_search .= " AND A.mb_sms='1' ";
}

if($receive_method) {
	$sql_search .= ($receive_method=='unknown') ? " AND A.receive_method=''" : " AND A.receive_method='$receive_method' ";
}

if($key_search != '' && $keyword != '') {
	$sql_search .= " AND BINARY $key_search LIKE '%$keyword%' ";
}
/* 검색 필드 조합 E N D */


$sql_order = " ORDER BY ";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", ";
}
$sql_order.= " A.mb_datetime DESC ";

$sql = "
	SELECT
		COUNT(mb_no) AS cnt,
		(SELECT COUNT(mb_no) FROM g5_member WHERE va_bank_code!='' AND rec_mb_id IS NOT NULL) AS recommend_count,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE invest_state='Y') AS invest_count,
		(SELECT SUM(amount) FROM cf_product_invest WHERE invest_state='Y') AS invest_amount
	{$sql_common}
	{$sql_search}
	{$sql_order}";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 100;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함


$sql = "
	SELECT
		A.*,
		(SELECT COUNT(mb_no) FROM g5_member WHERE rec_mb_no=A.mb_no AND va_bank_code!='' AND rec_mb_id IS NOT NULL) AS recommend_count,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.mb_no AND invest_state='Y' AND syndi_id='chosun') AS invest_count,
		(SELECT SUM(amount) FROM cf_product_invest WHERE member_idx=A.mb_no AND invest_state='Y' AND syndi_id='chosun') AS invest_amount
	{$sql_common}
	{$sql_search}
	{$sql_order}
	LIMIT
		{$from_record}, {$rows}";
$result = sql_query($sql);
$rcount = sql_num_rows($result);
for($i=0; $i<$rcount; $i++) {
	$LIST[] = sql_fetch_array($result);
}

$num = $total_count - $from_record;

// 등록회원
$sql   = "SELECT COUNT(*) AS cnt FROM g5_member WHERE syndi_id='$syndi_id'";
$row   = sql_fetch($sql);
$count1 = $row['cnt'];

// 탈퇴회원
$sql   = "SELECT COUNT(*) AS cnt FROM g5_member_drop WHERE syndi_id='$syndi_id'";
$row   = sql_fetch($sql);
$count2 = $row['cnt'];

?>

		<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
		<style>
		.tblX { width:100%; border:1px solid #ccc }
		.tblX th,
		.tblX td { padding:8px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
		.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
		.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
		.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
		.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
		.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }
		span.left  { float:left; }
		span.right { float:right; }
		.new {padding:0 6px 2px 6px; font-size:8pt; color:#fff; border:0px; background-color:red; border-radius:10px; margin:0 4px;}
		</style>

		<div id="content" style="position:absolute;">
			<div class="content investment" style="width:98%;margin:-50px auto;">

				<ul class="tab_type03" style="margin:0">
					<li data-gubun="tab1" onClick="location.href='product_status.php'">상품 투자 현황</li>
					<li data-gubun="tab2" class="on">가입자 현황</li>
					<li data-gubun="tab3" onClick="location.href='invest_status.php'">가입.투자 통계</li>
					<li data-gubun="tab4" style="float:right;text-align:right;border:0;background:#FFF;"><button type="button" class="btn_gray" onClick="location.href='./'">로그아웃</button></li>
				</ul>

				<div class="tabArea" style="display:block;padding:30px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">

					<div style="margin-bottom:15px;">
						<strong>전체등록회원 : </strong><?=number_format($count1);?> 명 &nbsp;&nbsp;&nbsp;
						<strong>전체탈퇴회원 : </strong><?=number_format($count2);?> 명
					</div>

					<div style="margin-bottom:10px;">
						<form id="member_list_frm" method="get">

						<span style="margin-right:15px;">
							가입일 <input type="text" class="frm_input datepicker"  name="start_date" value="<?=$start_date;?>" readonly style="margin-left:10px;width:100px"> ~
							<input type="text" class="frm_input datepicker" name="end_date" value="<?=$end_date;?>" readonly style="width:100px;">

							<select name="key_search" class="frm_input" style="margin-left:10px;">
								<option value="">검색필드선택</option>
								<option value="A.mb_id" <? if($key_search == 'A.mb_id'){echo 'selected';} ?>>아이디</option>
								<option value="A.mb_name" <? if($key_search == 'A.mb_name'){echo 'selected';} ?>>성명</option>
								<option value="A.mb_hp" <? if($key_search == 'A.mb_hp'){echo 'selected';} ?>>휴대폰</option>
							</select>

							<input type="text" class="frm_input" name="keyword" size="30" value="<? echo $keyword;?>">
							<input type="submit" class="btn_blue" value="검 색" onClick="form_change();">
							<input type="button" class="btn_gray" value="초기화" onClick="location.href='<?=$_SERVER['PHP_SELF']?>'">
						</span>

						</form>
					</div>

					<div style="margin-bottom:15px;">
						<span style="margin-right:15px;">
							<select id="sort_field" class="frm_input">
								<option value="">정렬필드</option>
								<option value="A.mb_datetime" <? if($sort_field == 'A.mb_datetime'){echo 'selected';} ?>>가입순</option>
								<option value="A.mb_point" <? if($sort_field == 'A.mb_point'){echo 'selected';} ?>>예치금</option>
								<option value="invest_count" <? if($sort_field == 'invest_count'){echo 'selected';} ?>>투자상품수</option>
								<option value="invest_amount" <? if($sort_field == 'invest_amount'){echo 'selected';} ?>>투자금액</option>
								<option value="A.login_cnt" <? if($sort_field == 'A.login_cnt'){echo 'selected';} ?>>로그인 수</option>
							</select>
							<button type="button" onClick="sortList('DESC');" class="btn btn-sm btn-<?=($sort=='DESC')?'info':'default';?>">내림차순</button>
							<button type="button" onClick="sortList('ASC');" class="btn btn-sm btn-<?=($sort=='ASC')?'info':'default';?>">오름차순</button>
						</span>
					</div>

					<table class="tblX">
						<thead>
						<tr style="background-color:#EFEFEF">
							<th scope="col" style="text-align:center;width:60px">NO</th>
							<!--th scope="col" style="text-align:center;">아이디</th-->
							<th scope="col" style="text-align:center;">성명</th>
							<th scope="col" style="text-align:center;">휴대폰</th>
							<th scope="col" style="text-align:center;">이메일</th>
							<th scope="col" style="text-align:center;">주소</th>
							<!--<th scope="col" style="text-align:center;">회원구분</th>-->
							<!--<th scope="col" style="text-align:center;">투자자 유형</th>-->
							<!-- <th scope="col" style="text-align:center;">가입일</th> -->
							<!--<th scope="col" style="text-align:center;">로그인</th>-->
							<!--<th scope="col" style="text-align:center;">최종로그인</th>-->
							<!--<th scope="col" style="text-align:center;">예치금</th>-->
							<th scope="col" style="text-align:center;">투자상품수</th>
							<th scope="col" style="text-align:center;">투자금액</th>
						</tr>
						</thead>
						<tbody>
<?
if($num > 0) {
	for($i=0,$j=1; $i<$rcount; $i++,$j++) {

		switch($LIST[$i]['member_type']) {
			case '2' : $mType = "법인회원"; break;
			case '3' : $mType = "SNS회원";  break;
			default  : $mType = "개인회원"; break;
		}

		if($LIST[$i]['member_type']=='1') {
			switch($LIST[$i]['member_investor_type']) {
				case '1' : $investor_type_txt = "일반";      break;
				case '2' : $investor_type_txt = "소득적격";  break;
				case '3' : $investor_type_txt = "전문";      break;
			}
		}
		else {
			$investor_type_txt = "";
		}

		$new_mark = (time()-strtotime($LIST[$i]['mb_datetime'])<86400) ? '<span class="new">new</span>' : '';
		$tr_bgcolor = ($j%2==1) ? '' : '#FAFAFA';

		$mb_id = '';
		$mb_id.= substr($LIST[$i]['mb_id'], 0, 2);
		for($k=0;$k<strlen($LIST[$i]['mb_id'])-2;$k++) {
			$mb_id.='*';
		}
?>
						<tr bgcolor="<?=$tr_bgcolor?>">
							<td style="text-align:center;"><?=$num?></td>
							<!-- <td style="text-align:center;"><?=$LIST[$i]['mb_id']?><?//=$mb_id?><?//=$new_mark?></td> -->
							<td style="text-align:center;"><?=$LIST[$i]['mb_name']?></td>
							<td style="text-align:center;"><?=$LIST[$i]['mb_hp']?></td>
							<td style="text-align:center;"><?=$LIST[$i]['mb_email']?></td>
							<td style="text-align:center;"><?=$LIST[$i]['mb_addr1']?> <?=$LIST[$i]['mb_addr2']?></td>
							<!--<td style="text-align:center;"><?=$mType?></td>-->
							<!--<td style="text-align:center;"><?=$investor_type_txt?></td>-->
							<!--<td style="text-align:center;"><?=substr($LIST[$i]['mb_datetime'], 0, 16)?></td>-->
							<!--<td style="text-align:right;"><?=number_format($LIST[$i]['login_cnt'])?>회</td>-->
							<!--<td style="text-align:center;"><?=(substr($LIST[$i]['mb_today_login'],0,16)=='0000-00-00 00:00') ? '' : substr($LIST[$i]['mb_today_login'],0,16); ?></td>-->
							<!--<td style="text-align:right;"><?=number_format($LIST[$i]['mb_point'])?>원</td>-->
							<td style="text-align:right;"><?=number_format($LIST[$i]['invest_count'])?>건</td>
							<td style="text-align:right;"><?=number_format($LIST[$i]['invest_amount'])?>원</td>
						</tr>
<?
		$num--;
	}
}
else {
?>
						<tr>
							<td colspan="11" style="text-align:center">데이터가 없습니다.</td>
						</tr>
<?
}
?>
						</tbody>
					</table>

					<div style="padding-top:10px">
<?
$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page=');
?>
					</div>

				</div>

			</div>
		</div>

<?
include_once('./tail.php');
?>

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: "yy-mm-dd",
		monthNames: [ "1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월", "9월", "10월", "11월", "12월" ],
		dayNamesShort: [ "일", "월", "화", "수", "목", "금", "토" ]
	});
});

function sortList(param) {
	if(document.getElementById('sort_field').value!='') {
		url = 'member_status.php'
				+ '?token=<?=$token?>'
				+ '&member_group=<?=$member_group?>'
				+ '&member_type=<?=$member_type?>'
				+ '&member_investor_type=<?=$member_investor_type?>'
				+ '&mb_level=<?=$mb_level?>'
				+ '&mb_mailling=<?=$mb_mailling?>'
				+ '&start_date=<?=$start_date?>'
				+ '&end_date=<?=$end_date?>'
				+ '&start_point=<?=$start_date?>'
				+ '&end_point=<?=$end_date?>'
				+ '&receive_method=<?=$receive_method?>'
				+ '&key_search=<?=$key_search?>'
				+ '&keyword=<?=urlencode($keyword)?>'
				+ '&sort_field=' + document.getElementById('sort_field').value
				+ '&sort=' + param
		location.href= url;
	}
	else {
		alert('정렬필드를 선택하십시요.'); document.getElementById('sort_field').focus();
	}
}
</script>