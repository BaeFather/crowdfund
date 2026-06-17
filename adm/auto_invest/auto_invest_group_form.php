<?
###############################################################################
## 신규 자동투자그룹 등록폼 2018.01.22
###############################################################################

$sub_menu = "601200";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');


if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$is_chrome = (preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT'])) ? true : false;
if(preg_match('/edge/i', $_SERVER['HTTP_USER_AGENT'])) $is_chrome = false;


foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$DATA = sql_fetch("SELECT * FROM cf_auto_invest_config WHERE idx='$idx'");
//print_rr($DATA);

$g5['title'] = "신규 채권그룹 등록";
include_once (G5_ADMIN_PATH.'/admin.head.php');

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');
?>

<style>
.button_area_scroll { position:fixed; z-index:10; bottom:0; border-top:1px solid #222; background-color:#fff; opacity:0.7; }
.mgpd0 {margin:0;padding:0}
.mgpd0 li {margin:0;padding:0}
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

	<form id="form1" name="form1">
	<input type="hidden" name="token" value="<?=$token?>">
	<input type="hidden" name="action" value="<?=($idx)?'update':'insert';?>">
	<input type="hidden" name="idx" id="prd_idx" value="<?=$idx?>">

	<div class="form-group">
		<table class="table table-bordered">
			<colgroup>
				<col width="15%">
				<col width="35%">
				<col width="15%">
				<col width="35%">
			</colgroup>
			<tbody>
			<tr>
				<th style="background:#F8F8EF">담보형태</th>
				<td>
					<ul class="col-sm-10 list-inline mgpd0">
						<li style="margin-right:8px"><label class="radio-inline"><input type="radio" name="category" value="2" <?=($DATA['category']=='2')?'checked':''?>>부동산</label></li>
						<li style="margin-right:8px"><label class="radio-inline"><input type="radio" name="category" value="1" <?=($DATA['category']=='1')?'checked':''?>>동산</label></li>
						<li style="margin-right:8px"><label class="radio-inline"><input type="radio" name="category" value="3" <?=($DATA['category']=='' || $DATA['category']=='3')?'checked':''?>>확정매출채권</label></li>
					</ul>
				</td>
				<th style="background:#F8F8EF">자동투자그룹명</th>
				<td><input type="text" id="grp_title" name="grp_title" value="<?=$DATA['grp_title']?>" class="form-control" style="width:400px" required></td>
			</tr>
			<tr>
				<th style="background:#F8F8EF">투자상품범위 설정</th>
				<td>
					<ul class="col-sm-10 list-inline mgpd0">
						<li>투자기간</li>
						<li style="margin-left:10px"><input type="text" id="min_period_days" name="min_period_days" value="<?=$DATA['min_period_days']?>" class="form-control" style="width:50px;text-align:center" maxlength="3" onKeyUp="onlyDigit(this)"></li>
						<li>~</li>
						<li><input type="text" id="max_period_days" name="max_period_days" value="<?=$DATA['max_period_days']?>" class="form-control" style="width:50px;text-align:center" maxlength="3" onKeyUp="onlyDigit(this)"></li>
						<li>일</li>
						<li style="margin-left:30px">수익률(연)</li>
						<li style="margin-left:10px"><input type="text" id="min_profit" name="min_profit" value="<?=$DATA['min_profit']?>" class="form-control" style="width:60px;text-align:center" maxlength="6"></li>
						<li>~</li>
						<li><input type="text" id="max_profit" name="max_profit" value="<?=$DATA['max_profit']?>" class="form-control" style="width:60px;text-align:center" maxlength="6"></li>
						<li>%</li>
						<li></li>
					</ul>
				</td>
				<th style="background:#F8F8EF">자동투자 가능금액 설정</th>
				<td>
					<ul class="col-sm-10 list-inline mgpd0">
						<li>전체 모집금의</li>
						<li style="margin-left:10px"><input type="text" id="auto_inv_limit_per" name="auto_inv_limit_per" value="<?=sprintf('%.2f', $DATA['auto_inv_limit_per'])?>" class="form-control" style="text-align:center;width:80px;" maxlength="5"></li>
						<li>% (소수점 이하 2자리 허용)</li>
						<li style="margin-left:20px;"><label class="checkbox-inline"><input type="checkbox" id="auto_inv_unlimited" name="auto_inv_unlimited" value="1" <?=($DATA['auto_inv_unlimited']=='1')?'checked':''?>>제한없음</label></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th style="background:#F8F8EF">투자등급별<br>자동투자 제한금액</th>
				<td colspan="3">
					<div style="float:left;">
						<ul class="col-sm-10 list-inline mgpd0">
							<li>법인회원</li>
							<li style="margin-left:20px"><input type="text" id="mb2_limit_amt" name="mb2_limit_amt" value="<?=number_format($DATA['mb2_limit_amt'])?>" onKeyUp="NumberFormat(this);" class="form-control" style="width:100px;text-align:right"></li>
							<li>원</li>
							<li style="margin-left:20px;"><label class="checkbox-inline"><input type="checkbox" id="mb2_unlimited" name="mb2_unlimited" value="1" <?=($DATA['mb2_unlimited']=='1')?'checked':''?>>제한없음</label></li>
						</ul>
					</div>
					<div style="float:left;">
						<ul class="col-sm-10 list-inline mgpd0" style="margin-top:4px;">
							<li style="height:32px;">개인회원</li>
						</ul>
						<ul class="col-sm-10 list-inline mgpd0" style="margin-top:4px;">
							<li>일반투자자</li>
							<li style="margin-left:20px"><input type="text" id="mb11_limit_amt" name="mb11_limit_amt" value="<?=number_format($DATA['mb11_limit_amt'])?>" onKeyUp="NumberFormat(this);" class="form-control" style="width:100px;text-align:right"></li>
							<li>원</li>
							<li style="margin-left:20px;"><label class="checkbox-inline"><input type="checkbox" id="mb11_unlimited" name="mb11_unlimited" value="1" <?=($DATA['mb11_unlimited']=='1')?'checked':''?>>제한없음</label></li>
						</ul>
						<ul class="col-sm-10 list-inline mgpd0" style="margin-top:4px;">
							<li>소득적격자</li>
							<li style="margin-left:20px"><input type="text" id="mb12_limit_amt" name="mb12_limit_amt" value="<?=number_format($DATA['mb12_limit_amt'])?>" onKeyUp="NumberFormat(this);" class="form-control" style="width:100px;text-align:right"></li>
							<li>원</li>
							<li style="margin-left:20px;"><label class="checkbox-inline"><input type="checkbox" id="mb12_unlimited" name="mb12_unlimited" value="1" <?=($DATA['mb12_unlimited']=='1')?'checked':''?>>제한없음</label></li>
						</ul>
						<ul class="col-sm-10 list-inline mgpd0" style="margin-top:4px;">
							<li>전문투자자</li>
							<li style="margin-left:20px"><input type="text" id="mb13_limit_amt" name="mb13_limit_amt" value="<?=number_format($DATA['mb13_limit_amt'])?>" onKeyUp="NumberFormat(this);" class="form-control" style="width:100px;text-align:right"></li>
							<li>원</li>
							<li style="margin-left:20px;"><label class="checkbox-inline"><input type="checkbox" id="mb13_unlimited" name="mb13_unlimited" value="1" <?=($DATA['mb13_unlimited']=='1')?'checked':''?>>제한없음</label></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<th style="background:#F8F8EF">자동투자 우선순위</th>
				<td>
					<ul class="col-sm-10 list-inline mgpd0">
						<li style="font-size:11px;color:#FF2222">고정우선순위 : 자동투자선순위대상자 > 자동투자이력이 없는 회원 > 이하 설정</li><br/>
						<li><label class="radio-inline"><input type="radio" name="inv_order" value='1' <?=($DATA['inv_order']=='1')?'checked':''?>>개인우선</label></li>
						<li style="margin-left:20px"><label class="radio-inline"><input type="radio" name="inv_order" value='2' <?=($DATA['inv_order']=='2')?'checked':''?>>법인우선</label></li>
						<li style="margin-left:20px"><label class="radio-inline"><input type="radio" name="inv_order" value='3' <?=($DATA['inv_order']=='3')?'checked':''?>>고액우선</label></li>
						<li style="margin-left:20px"><label class="radio-inline"><input type="radio" name="inv_order" value='0' <?=($DATA['inv_order']==0)?'checked':''?>>선착순</label></li>
					</ul>
				</td>
				<th style="background:#F8F8EF">노출설정</th>
				<td>
					<ul class="col-sm-10 list-inline mgpd0">
						<li><label class="radio-inline"><input type="radio" name="display" value='Y' <?=($DATA['display']=='Y')?'checked':''?>>노출</label></li>
						<li style="margin-left:20px"><label class="radio-inline"><input type="radio" name="display" value='N' <?=(empty($DATA['display']) || $DATA['display']=='N')?'checked':''?>>비노출</label></li>
					</ul>
				</td>
			</tr>
			<tr>
				<th style="background:#F8F8EF">상품설명</th>
				<td colspan="3"><?=editor_html('summary', get_text($DATA['summary'], 0))?></td>
			</tr>
			<tr>
				<th style="background:#F8F8EF">상품설명(모바일)</th>
				<td colspan="3"><?=editor_html('summary_m', get_text($DATA['summary_m'], 0))?></td>
			</tr>
		</table>

	</div>

	<div style="height:54px">
		<p id="button_area" style="width:100%;margin:0;left:0;padding:10px 0 10px 0;" class="text-center">
			<button type="button" id="btn_submit" class="btn btn-primary" style="width:70%;">그룹<?=($DATA['idx'])?'수정':'등록'?></button>
			<button type="button" onClick="location.href='/adm/auto_invest/auto_invest_group_list.php?<?=$_SERVER['QUERY_STRING']?>';" class="btn btn_gray" style="width:29%;">목록</button>
		</p>
	</div>
</div>

<div style="position:fixed; display:none; z-index:1002; top:150px;left:30px; border:1px solid #bbb; padding:4px;background-color:#FAFAFA;">
	top_position : <input type="text" id="top_position"> &nbsp;
	scroll_top : <input type="text" id="scroll_top">
</div>
<script>
$(document).ready(function(){
	var m_height = 54;
	setTimeout(function() {
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
	}, 2000);
});
</script>

<script>
$(document).ready(function() {
	$('#auto_inv_limit_per').attr('disabled', ($('#auto_inv_unlimited:checked').val()==1)?true:false);
	$('#mb2_limit_amt').attr('disabled', ($('#mb2_unlimited:checked').val()==1)?true:false);
	$('#mb11_limit_amt').attr('disabled', ($('#mb11_unlimited:checked').val()==1)?true:false);
	$('#mb12_limit_amt').attr('disabled', ($('#mb12_unlimited:checked').val()==1)?true:false);
	$('#mb13_limit_amt').attr('disabled', ($('#mb13_unlimited:checked').val()==1)?true:false);
});

$('#auto_inv_unlimited').on('click', function() {
	$('#auto_inv_limit_per').attr('disabled', ($('#auto_inv_unlimited:checked').val()=='1')?true:false);
});
$('#mb2_unlimited').on('click', function() {
	$('#mb2_limit_amt').attr('disabled', ($('#mb2_unlimited:checked').val()=='1')?true:false);
});
$('#mb11_unlimited').on('click', function() {
	$('#mb11_limit_amt').attr('disabled', ($('#mb11_unlimited:checked').val()=='1')?true:false);
});
$('#mb12_unlimited').on('click', function() {
	$('#mb12_limit_amt').attr('disabled', ($('#mb12_unlimited:checked').val()=='1')?true:false);
});
$('#mb13_unlimited').on('click', function() {
	$('#mb13_limit_amt').attr('disabled', ($('#mb13_unlimited:checked').val()=='1')?true:false);
});

<?
if($idx) {
	$next_action = 'document.location.reload();';
}
else {
	$next_action = 'window.location.replace("auto_invest_group_list.php");';
}
?>

$('#btn_submit').click(function() {

	<?=get_editor_js('summary');?>
	<?=get_editor_js('summary_m');?>

	if($('#grp_title').val()=='') { alert('자동투자그룹명을 입력하십시요.'); $('#grp_title').focus(); }
	else if($('#auto_inv_unlimited:checked').val()!='1' && ($('#auto_inv_limit_per').val()=='' || $('#auto_inv_limit_per').val()<='0.00')) { alert('자동투자가능금액비율을 설정하십시요.'); $('#auto_inv_limit_per').focus(); }
	else {
		if( confirm("그룹을 <?=($DATA['idx'])?'수정':'등록'?> 하시겠습니까?") ) {
			var fdata = $("#form1").serialize();
			$.ajax({
				url : "auto_invest_group_proc.php",
				type: "POST",
				data: fdata,
				success: function(data) {
					$('#ajax_return_txt').val(data);
					<?/*=$next_action*/?>
				},
				beforeSend: function() { loading('on'); },
				complete: function() { loading('off'); },
				error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}
})
</script>

<?

include_once ('../admin.tail.php');

?>