<?
$sub_menu = "601100";
include_once('./_common.php');


$g5['title'] = $menu['menu601'][1][1];
include_once (G5_ADMIN_PATH.'/admin.head.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$where							   = "1=1";
if($category)               $where.= " AND category='$category'";
if($display)								$where.= " AND display='$display'";
if($key_search && $keyword) $where.= " AND $key_search LIKE '%$keyword%'";
if($auto_inv_unlimited)     $where.= " AND auto_inv_unlimited='$auto_inv_unlimited'";
if($mb2_unlimited)          $where.= " AND mb2_unlimited='$mb2_unlimited'";
if($mb11_unlimited)         $where.= " AND mb11_unlimited='$mb11_unlimited'";
if($mb12_unlimited)         $where.= " AND mb12_unlimited='$mb12_unlimited'";
if($mb13_unlimited)         $where.= " AND mb13_unlimited='$mb13_unlimited'";
if($inv_order)              $where.= " AND inv_order='$inv_order'";

$sql = "SELECT COUNT(idx) AS cnt FROM cf_auto_invest_config WHERE $where";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$page_rows = 100;
$total_page = ceil($total_count / $page_rows);		// 전체 페이지 계산
$page = ($page) ? $page : 1;											// 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows;					// 시작 열을 구함

$sql = "
	SELECT
		A.*,
		(SELECT COUNT(idx) FROM cf_product WHERE ai_grp_idx=A.idx) AS product_count,
		(SELECT COUNT(idx) FROM cf_auto_invest_config_user t1 LEFT JOIN g5_member t2 ON t1.member_idx=t2.mb_no WHERE t2.mb_level='1' AND t1.ai_grp_idx=A.idx) AS user_count,
		(SELECT SUM(setup_amount) FROM cf_auto_invest_config_user t1 LEFT JOIN g5_member t2 ON t1.member_idx=t2.mb_no WHERE t2.mb_level='1' AND t1.ai_grp_idx=A.idx) AS setup_amount,
		(SELECT SUM(setup_amount2) FROM cf_auto_invest_config_user  t1 LEFT JOIN g5_member t2 ON t1.member_idx=t2.mb_no WHERE t2.mb_level='1' AND t1.ai_grp_idx=A.idx) AS setup_amount2
	FROM
		cf_auto_invest_config A
	WHERE
		$where
	ORDER BY
		idx DESC
	LIMIT
		$from_record, $page_rows";

$res = sql_query($sql);
$rcount = $res->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[] = sql_fetch_array($res);
}
sql_free_result($res);

$list_count = count($LIST);
$num = $total_count - $from_record;

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
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0">
			<li><label class="checkbox-inline"><input type="checkbox" name="auto_inv_unlimited" value="1" <?=($auto_inv_unlimited=='1')?'checked':'';?>> 자동투자 무제한</label></li>
			<li><label class="checkbox-inline"><input type="checkbox" name="mb2_unlimited" value="1" <?=($mb2_unlimited=='1')?'checked':'';?>> 법인투자자 무제한</label></li>
			<li><label class="checkbox-inline"><input type="checkbox" name="mb11_unlimited" value="1" <?=($mb11_unlimited=='1')?'checked':'';?>> 일반투자자 무제한</label></li>
			<li><label class="checkbox-inline"><input type="checkbox" name="mb12_unlimited" value="1" <?=($mb12_unlimited=='1')?'checked':'';?>> 소득적격자 무제한</label></li>
			<li><label class="checkbox-inline"><input type="checkbox" name="mb13_unlimited" value="1" <?=($mb13_unlimited=='1')?'checked':'';?>> 전문투자자 무제한</label></li>
		</ul>
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0">
			<li>
				<select id="category" name="category" class="form-control">
					<option value="">::담보형태::</option>
					<option value="1" <? if($category=='1'){echo 'selected';} ?>>동산</option>
					<option value="2" <? if($category=='2'){echo 'selected';} ?>>부동산</option>
					<option value="3" <? if($category=='3'){echo 'selected';} ?>>확정매출채권</option>
				</select>
			</li>
			<li>
				<select name="inv_order" id="inv_order" class="form-control">
					<option value="">::우선순위::</option>
					<option value="0" <? if($inv_order=='0'){echo 'selected';} ?>>선착순</option>
					<option value="1" <? if($inv_order=='1'){echo 'selected';} ?>>법인우선</option>
					<option value="2" <? if($inv_order=='2'){echo 'selected';} ?>>개인우선</option>
					<option value="3" <? if($inv_order=='3'){echo 'selected';} ?>>고액우선</option>
				</select>
			</li>
			<li>
				<select name="display" id="display" class="form-control">
					<option value="">::노출설정::</option>
					<option value="Y" <? if($display=='Y'){echo 'selected';} ?>>노출</option>
					<option value="N" <? if($display=='N'){echo 'selected';} ?>>비노출</option>
				</select>
			</li>
			<li>
				<select name="key_search" class="form-control">
					<option value="">::필드선택::</option>
					<option value="grp_title" <? if($key_search == 'grp_title'){echo 'selected';} ?>>자동투자그룹명</option>
				</select>
			</li>
			<li><input type="text" class="form-control" name="keyword" size="30" value="<?=$keyword;?>"></li>
			<li><input type="submit" class="btn btn-warning" value="검색" onclick="form_change();"></li>
			<li><a href="./auto_invest_group_form.php?<?=$_SERVER['QUERY_STRING']?>" class="btn btn-danger">신규 자동투자그룹 등록</a></li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->

	<table class="table-striped table-bordered table-hover" style="font-size:13px;">
		<tr>
			<th rowspan="3" style="background:#F8F8EF">NO</th>
			<th rowspan="3" style="background:#F8F8EF">고유번호</th>
			<th rowspan="3" style="background:#F8F8EF">자동투자그룹</th>
			<th rowspan="3" style="background:#F8F8EF">담보형태</th>
			<th rowspan="3" style="background:#F8F8EF">투자기간</th>
			<th rowspan="3" style="background:#F8F8EF">수익률(연)</th>
			<th rowspan="3" style="background:#F8F8EF">자동투자 가능금액<br>(전체모집금액대비)</th>
			<th colspan="4" style="background:#F8F8EF">투자등급별<br>자동투자 제한금액</th>
			<th rowspan="3" style="background:#F8F8EF">우선순위</th>
			<th rowspan="3" style="background:#F8F8EF">노출설정</th>
			<th rowspan="3" style="background:#F8F8EF">소속상품수</th>
			<th colspan="5" style="background:#F8F8EF">사용자</th>
			<th rowspan="3" style="background:#F8F8EF">PROC</th>
		</tr>
		<tr>
			<th rowspan="2" style="background:#F8F8EF">법인투자자</th>
			<th rowspan="2" style="background:#F8F8EF">일반투자자</th>
			<th rowspan="2" style="background:#F8F8EF">소득적격투자자</th>
			<th rowspan="2" style="background:#F8F8EF">전문투자자</th>
			<th rowspan="2" style="background:#F8F8EF">설정자수</th>
			<th colspan="2" style="background:#F8F8EF">설정금액</th>
			<th colspan="2" style="background:#F8F8EF">실제금액</th>
		</tr>
		<tr>
			<th style="background:#F8F8EF">최소 설정</th>
			<th style="background:#F8F8EF">최대 설정</th>
			<th style="background:#F8F8EF">카테고리 기준</th>
			<th style="background:#F8F8EF">상품 기준</th>
		</tr>

<?

if($num) {
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {

		IF($i > 0)
		{
			$strIdx = "'".$LIST[$i]["idx"]."',".$strIdx;
		} ELSE {
			$strIdx	= "'".$LIST[$i]["idx"]."'";
		}
		$auto_inv_limit_per = ($LIST[$i]['auto_inv_unlimited']=='1') ? '무제한' : $LIST[$i]['auto_inv_limit_per'].'%';
		$mb2_limit_amt  = ($LIST[$i]['mb2_unlimited']=='1') ? '무제한' : number_format($LIST[$i]['mb2_limit_amt'])."원";
		$mb11_limit_amt = ($LIST[$i]['mb11_unlimited']=='1') ? '무제한' : number_format($LIST[$i]['mb11_limit_amt'])."원";
		$mb12_limit_amt = ($LIST[$i]['mb12_unlimited']=='1') ? '무제한' : number_format($LIST[$i]['mb12_limit_amt'])."원";
		$mb13_limit_amt = ($LIST[$i]['mb13_unlimited']=='1') ? '무제한' : number_format($LIST[$i]['mb13_limit_amt'])."원";
		switch($LIST[$i]['inv_order']) {
			case '1': $inv_order = '개인우선'; break;
			case '2': $inv_order = '법인우선'; break;
			case '3': $inv_order = '고액우선'; break;
			default : $inv_order = '선착순';
		}

		switch($LIST[$i]['category']) {
			case '1': $print_category = '동산'; break;
			case '2': $print_category = '부동산'; break;
			case '3': $print_category = '확정매출채권'; break;
			default : $print_category = '미지정';
		}

		switch($LIST[$i]['display']) {
			case 'Y': $print_display = '노출'; break;
			case 'N':
			default : $print_display = '비노출'; break;
		}

		echo '
		<tr align="center">
			<td>'.$num.'</td>
			<td>'.$LIST[$i]['idx'].'</td>
			<td>'.$LIST[$i]['grp_title'].'</td>
			<td>'.$print_category.'</td>
			<td>'.$LIST[$i]['min_period_days'].' ~ '.$LIST[$i]['max_period_days'].'일</td>
			<td>'.$LIST[$i]['min_profit'].' ~ '.$LIST[$i]['max_profit'].'%</td>
			<td>'.$auto_inv_limit_per.'</td>
			<td style="color:#CCC">'.$mb2_limit_amt.'</td>
			<td style="color:#CCC">'.$mb11_limit_amt.'</td>
			<td style="color:#CCC">'.$mb12_limit_amt.'</td>
			<td style="color:#CCC">'.$mb13_limit_amt.'</td>
			<td>'.$inv_order.'</td>
			<td>'.$print_display.'</td>
			<td><a href="/adm/product/product_list.php?ai_grp_idx='.$LIST[$i]['idx'].'">'.number_format($LIST[$i]['product_count']).'</a></td>
			<td><a href="/adm/auto_invest/auto_invest_users_setup.php?ai_grp_idx='.$LIST[$i]['idx'].'">'.number_format($LIST[$i]['user_count']).'</a></td>
			<td align="right"><a href="/adm/auto_invest/auto_invest_users_setup.php?ai_grp_idx='.$LIST[$i]['idx'].'">'.number_format($LIST[$i]['setup_amount']).'원</a></td>
			<td align="right"><a href="/adm/auto_invest/auto_invest_users_setup.php?ai_grp_idx='.$LIST[$i]['idx'].'">'.number_format($LIST[$i]['setup_amount2']).'원</a></td>
			<td align="right"><a onclick="view_auto_list('.$LIST[$i]['idx'].')"><span id="category_money_'.$LIST[$i]['idx'].'" style="cursor:pointer;"></span></a></td>
			<td align="right"><a onclick="view_auto_list('.$LIST[$i]['idx'].')"><span id="real_money_'.$LIST[$i]['idx'].'" style="cursor:pointer;"></span></a></td>
			<td><a href="auto_invest_group_form.php?idx='.$LIST[$i]['idx'].'&'.$_SERVER['QUERY_STRING'].'" class="btn btn-primary" style="width:60px">수정</a></td>
		</tr>' . PHP_EOL;

		$num--;
	}
}
else {
	echo '
		<tr>
			<td colspan="16" align="center">데이터가 없습니다.</th>
		</tr>' . PHP_EOL;
}
?>
	</table>

	<form name="regfm" id="regfm">
<?php
	FOR($i=0;$i<COUNT($LIST);$i++)
	{
		ECHO "<input type='hidden' name='SE[]' value='".$LIST[$i]["idx"]."'>";
	}
?>
	</form>

	<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div>

</div>

<script>
$(document).on('click', '#paging_span span.btn_paging', function() {
	var url = '<?=$_SERVER['PHP_SELF']?>'
	        + '?page=' + $(this).attr('data-page');
	$(location).attr('href', url);
});
</script>

<span id="hellopay_amt"></span>
<script>
function view_auto_list(s_type) {
	window.open("./auto_inv_real_money_list_new.html?s_type="+s_type, "_blank", "left=50,top=30,width=1200,height=600,scrollbars=yes");
}
function get_auto_passble_money() {
	$.ajax({
		url: 'ajax_auto_inv_money_new.php',
		type: 'POST',
		dataType: "json",
		success: function(data) {
			if(data.retcode == "OK")
			{
				var strVal = data.retval;
				var stridx	=	new Array(<?php ECHO $strIdx;?>);

				for(var i=0;i<stridx.length;i++)
				{
					$("#category_money_"+stridx[i]).text(number_format(strVal[stridx[i]][1]));
					$("#real_money_"+stridx[i]).text(number_format(strVal[stridx[i]][2]));
				}

				//
			} else {
				alert("처리중 문제가 발생 되었습니다.");
			}
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
	});
}
get_auto_passble_money();

</script>
<?
include_once ('../admin.tail.php');
?>