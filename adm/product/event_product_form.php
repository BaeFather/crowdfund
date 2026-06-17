<?php
$sub_menu = "600200";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql = "SELECT * FROM cf_event_product WHERE idx = '".$_GET['idx']."'";
$row = sql_fetch($sql);


$g5['title'] = (empty($_GET['idx']))?'이벤트 상품 등록':'이벤트 상품 수정';
include_once('../admin.head.php');

add_javascript(G5_POSTCODE_JS, 0);    //다음 주소 js
?>

<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="css/jquery-ui.min.css" rel="stylesheet">

<script src="js/jquery-ui.min.js"></script>
<script src="js/jquery.form.js"></script>

<div class="row">
	<form id="event_product_form" name="event_product_form" method="post" action="event_register_process.php" enctype="multipart/form-data" onsubmit="return fproduct_submit(this);" class="form-horizontal">
	<input type="hidden" name="token" value="">
	<input type="hidden" name="action" value="product_<?=(empty($_GET['idx']))?'insert':'update';?>">
	<input type="hidden" name="idx" value="<?=$_GET['idx']?>">
	<div class="col-lg-12">
		<div class="form-group">
			<label class="col-sm-1 control-label">카테고리</label>
			<div class="col-sm-10">
				<select name="category" class="form-control">
					<option value="1" <?=($row['category']=='1') ? 'selected' : ''; ?>>고정수익금 지급형</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">이벤트명</label>
			<div class="col-sm-10">
				<input type="text" name="title" value="<?=$row['title']?>" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">참여금액</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li><input type="text" name="invest_amount" value="<?=($row['invest_amount']) ? $row['invest_amount'] : 0;?>" class="form-control" onKeyup="onlyDigit(this);"></li>
				<li>원</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">참여수익금</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li><input type="text" name="invest_profit" value="<?=($row['invest_profit']) ? $row['invest_profit'] : 0;?>" class="form-control" onKeyup="onlyDigit(this);"></li>
				<li>원</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">총지급액</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li><input type="text" name="total_return_amount" value="<?=($row['total_return_amount']) ? $row['total_return_amount'] : 0;?>" class="form-control" onKeyup="onlyDigit(this);"></li>
				<li>원</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">참여수익율</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li>
					<input type="text" name="invest_return" value="<?=($row['invest_return']) ? $row['invest_return'] : 0;?>" class="form-control">
				</li>
				<li>% (표기이율)</li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">원천징수세율</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li>
					<input type="text" name="withhold_tax_rate" value="<?=($row['withhold_tax_rate']) ? $row['withhold_tax_rate'] : 0; ?>" class="form-control">
				</li>
				<li>%</li>
			</ul>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">모집기간</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li><input type="text" name="recruit_period_start" value="<?=$row['recruit_period_start']?>" class="form-control datepicker" placeholder="일자선택"></li>
				<li>~</li>
				<li><input type="text" name="recruit_period_end" value="<?=$row['recruit_period_end']?>" class="form-control datepicker" placeholder="일자선택"></li>
			</ul>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">모집금액</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li>
					<input type="text" name="recruit_amount" value="<?=($row['recruit_amount']) ? $row['recruit_amount'] : 0; ?>" class="form-control" onKeyup="onlyDigit(this);">
				</li>
				<li>원</li>
			</ul>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">수익금 지급일</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
				<li><input type="text" name="repay_day" value="<?=$row['repay_day']?>" class="form-control datepicker" placeholder="일자선택"></li>
			</ul>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">대표 이미지</label>
			<div class="col-sm-10">
				<div class="input-group" style="width:100%;">
					<input type="file" name="main_image" class="form-control">
					<? if ($row['main_image']) { ?>
					<div class="input-group-addon"><a href="<?=G5_DATA_URL.'/product_special/'.$row['main_image'];?>" target="_blank">이미지보기</a></div>
					<? } ?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">모바일 롤링 이미지</label>
			<div class="col-sm-10">
				<div class="input-group">
					<input type="file" name="main_image_m" class="form-control">
					<? if ($row['main_image_m']) { ?>
					<div class="input-group-addon"><a href="<? echo G5_DATA_URL.'/product_special/'.$row['main_image_m']?>" target="_blank">이미지보기</a></div>
					<? } ?>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-1 control-label">상세정보 이미지</label>
			<div class="col-sm-10">
				<select name="detail_image[]" class="form-control" multiple>
<?
				foreach ((array)explode('|', $row['detail_image']) as $key => $val) {
					if (!$val) {
						continue;
					}
?>
					<option value="<?=$val?>"><?=$val?></option>
<?
				}
?>
				</select>
				<div class="help-block">
					<button type="button" class="btn btn-default" onclick="uploadImage('detail_image');">업로드</button>
					<button type="button" class="btn btn-default" onclick="deleteImage('detail_image');">선택삭제</button>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">이벤트 내용</label>
			<div class="col-sm-10">
				<?=editor_html('invest_summary', get_text($row['invest_summary'], 0));?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">심사자</label>
			<div class="col-sm-10">
				<div class="input-group">
					<select name="judge">
						<option value=''>:: 선택 ::</option>
<?
$JUDGE_ARR = array_keys($JUDGE);
for($i=0; $i<count($JUDGE); $i++) {
	$selected = ($row['judge']==$JUDGE_ARR[$i]) ? 'selected' : '';
	echo "<option value='".$JUDGE_ARR[$i]."' $selected>".$JUDGE[$JUDGE_ARR[$i]]."</option>\n";
}
?>
					</select> 선택된 심사자의 프로필 배너가 투자상품 상세보기 페이지의 '투자 요약 상단'에 위치합니다.
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">상품평가</label>
			<div class="col-sm-10">
				<ul class="col-sm-10 list-inline">
					<li>안전성</li>
					<li><input type="text" name="evaluate_score1" value="<?=$row['evaluate_score1']?>" class="form-control"></li>
					<li>/ 40 (구 48점)</li>
				</ul>
				<ul class="col-sm-10 list-inline">
					<li>상환성</li>
					<li><input type="text" name="evaluate_score4" value="<?=$row['evaluate_score4']?>" class="form-control"></li>
					<li>/ 30 (구 42점)</li>
				</ul>
				<ul class="col-sm-10 list-inline">
					<li>환금성</li>
					<li><input type="text" name="evaluate_score3" value="<?=$row['evaluate_score3']?>" class="form-control"></li>
					<li>/ 30 (구 5점)</li>
				</ul>
				<ul class="col-sm-10 list-inline" style="margin-bottom:0;color:#999;">
					<li>수익성</li>
					<li><input type="text" name="evaluate_score2" value="<?=$row['evaluate_score2']?>" class="form-control"></li>
					<li>/ 5</li>
					<li>개 (* 입력하지 않으면 신 등급체계에 반영되지 않음)</li>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">심사총평</label>
			<div class="col-sm-10">
				<?=editor_html('screening', get_text($row['screening'], 0)); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">위치좌표</label>
			<div class="col-sm-10">
				<ul class="col-sm-10 list-inline">
					<li>위도</li>
					<li><input type="text" name="lat" value="<?=$row['lat']?>" class="form-control"></li>
					<li></li>
					<li>경도</li>
					<li><input type="text" name="lng" value="<?=$row['lng']?>" class="form-control"></li>
				</ul>
			</div>
		</div>

		<div class="form-group">
			<label class="col-sm-1 control-label">FAQ</label>
			<div class="col-sm-10">
				<?=editor_html('faq', get_text($row['faq'], 0));?>
			</div>
		</div>

		<div style="height:20px;"></div>

		<div class="form-group">
			<label class="col-sm-1 control-label">스케줄러</label>
			<div class="col-sm-10">
				<ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
					<li style="width:190px;">상품오픈 (투자시작불가)</li>
					<li><input type="text" name="open_date" value="<?=$row['open_date']?>" class="form-control datepicker" placeholder="일자선택" style="width:100px;"></li>
					<li style="padding-left:20px;"><input type="text" name="open_hour" value="<?=$row['open_hour']?>" class="form-control" placeholder="시" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="open_minute" value="<?=$row['open_minute']?>" class="form-control" placeholder="분" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="open_second" value="<?=$row['open_second']?>" class="form-control" placeholder="초" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<div class="help-block" style="padding-left:200px;">상품등록이 되어 사이트 노출되지만 투자신청(이벤트 참여)은 되지 않습니다.</div>
				</ul>
				<ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
					<li style="width:190px;">이벤트 오픈 (참여시작가능)</li>
					<li><input type="text" name="start_date" value="<?=$row['start_date']?>" class="form-control datepicker" placeholder="일자선택" style="width:100px;"></li>
					<li style="padding-left:20px;"><input type="text" name="start_hour" value="<?=$row['start_hour']?>" class="form-control" placeholder="시" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="start_minute" value="<?=$row['start_minute']?>" class="form-control" placeholder="분" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="start_second" value="<?=$row['start_second']?>" class="form-control" placeholder="초" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<div class="help-block" style="padding-left:200px;">실제 이벤트 시작 시점입니다.</div>
				</ul>
				<ul class="col-sm-10 list-inline" style="margin-bottom: 4px;">
					<li style="width:190px;">이벤트 종료 (마감)</li>
					<li><input type="text" name="end_date" value="<?=$row['end_date']?>" class="form-control datepicker" placeholder="일자선택" style="width:100px;"></li>
					<li style="padding-left:20px;"><input type="text" name="end_hour" value="<?=$row['end_hour']?>" class="form-control" placeholder="시" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="end_minute" value="<?=$row['end_minute']?>" class="form-control" placeholder="분" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<li><input type="text" name="end_second" value="<?=$row['end_second']?>" class="form-control" placeholder="초" style="width:50px;" onKeyup="onlyDigit(this);"></li>
					<div class="help-block" style="padding-left:200px;">이벤트 마감 일자 시점입니다.</div>
				</ul>
				<ul class="col-sm-10 list-inline" style="margin-bottom: 0;">
					<li style="width:190px;">노출여부</li>
					<li>
						<div class="radio">
							<label><input type="radio" name="display" value="Y" <?=($row['display'] == 'Y') ? 'checked' : ''; ?>> 노출</label>
						</div>
					</li>
					<li>
						<div class="radio">
							<label><input type="radio" name="display" value="N" <?=($row['display'] == 'N' || !$row['display']) ? 'checked' : ''; ?>> 비노출</label>
						</div>
					</li>
					<div class="help-block" style="padding-left:200px;">관리자 상품목록에는 삭제되지 않습니다. 프론트 단에 노출/비노출 설정입니다.</div>
				</ul>
			</div>
		</div>
	</div>

	<input type="hidden" name="loan_interest_rate"     value="<?=($row['loan_interest_rate']) ? $row['loan_interest_rate'] : 0; ?>" alt="대출자 이자율(%)">
	<input type="hidden" name="loan_usefee"            value="0" alt="대출자 플랫폼이용료(%)">
	<input type="hidden" name="invest_usefee"          value="0" alt="투자자 플랫폼이용료">
	<input type="hidden" name="invest_period"          value="1" alt="투자개월수">
	<input type="hidden" name="middle_withdraw_state"  value="2" alt="중도인출가능여부(1:가능|2:불가)">
	<input type="hidden" name="middle_withdraw_charge" value="0" alt="중도인출수수료">
	<input type="hidden" name="repay_type"             value="1" alt="상환방식(1:만기일시상환|2:원리금균등상환|3:원금균등상환)">

	<!-- /.col-lg-12 -->
	<p class="text-center">
		<button type="submit" class="btn btn-primary" style="width:150px">이벤트 <?=(count($row))?'수정':'등록'?></button>
		<a href="javascript:;" onclick="document.getElementById('event_product_form').reset();" class="btn btn-default" style="width:150px">취소</a>
	</p>
	</form>
</div>
<!-- /.row -->

<form id="upload_form" method="post" action="/builder/multiProcess.php" enctype="multipart/form-data">
  <input type="hidden" name="action" value="product_image_upload">
  <input type="file" name="image_upload" style="display: none;">
</form>

<script>
var options = {
	url: './event_register_process.php'
};

$(function() {
	$("input[name=image_upload]").change(function() {
		$("#upload_form").ajaxSubmit(options)[0].reset();
	});

	$("input[name=recruit_amount]").keyup(function(e) {
		$("#number_format").text(number_format(this.value));
	});
});

function fproduct_submit(f)
{
	<?=get_editor_js("invest_summary");?>
	<?=get_editor_js("screening");?>
	<?=get_editor_js("faq");?>

	$("select[name='detail_image[]'] option").prop('selected', true);

	return true;
}

function uploadImage(selector) {
	options.success = function(data) {
		$("select[name='"+selector+"[]']").append('<option value="'+data+'">'+data+'</option>');
	}

	$("input[name=image_upload]").trigger('click');
}

function deleteImage(selector) {
	file = $("select[name='"+selector+"[]'] :selected").val();
	$.post('./event_register_process.php', { action: 'product_image_delete', file: file }, function() {
		$("select[name='"+selector+"[]'] option[value="+file+"]").remove();
	});
}
</script>

<?php
include_once ('../admin.tail.php');
?>
