<?php
$sub_menu = "600400";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$sql = "SELECT * FROM cf_invest";
$row = sql_fetch($sql);

$g5['title'] = '투자금 관련설정';
include_once('../admin.head.php');


$file = "../../data/cache/nujuk_state.php";

if(is_file($file)) {
	unlink($file);
}
?>

<style>
.tmp1 { width:32%; }
.left { float:left; }
.tmp1 .tmp_title {width:30%;}
</style>

<div class="row">
	<h2 class="page-header text-center">투자금 한도설정</h2>
	<form id="finvest_form" method="post" action="../register_process.php" enctype="multipart/form-data" onsubmit="return finvest_submit(this);" class="form-horizontal">
	<input type="hidden" name="w" value="">
	<input type="hidden" name="token" value="">
	<input type="hidden" name="action" value="invest_update">
	<div class="col-lg-12">
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">최소투자금<br>한도설정</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li><input type="text" name="min_invest_limit" value="<?=$row['min_invest_limit']?>" class="form-control" <?=($row['min_invest_limit'] == 'Y') ? 'disabled' : '';?>></li><li>원</li>
				<li>
					<div class="checkbox">
						<label><input type="checkbox" name="min_invest_nolimit" value="Y" <?=($row['min_invest_nolimit']=='Y') ? 'checked' : '';?>> 체크시 한도없음</label>
					</div>
				</li>
				<div class="help-block" style="padding-left: 5px;">투자자 최소 투자금 한도 설정입니다. 입력한 금액 이상이어야 투자가 가능합니다.</div>
			</ul>
		</div>
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">최대투자금<br>한도설정</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li><input type="text" name="max_invest_limit" value="<?=$row['max_invest_limit']?>" class="form-control" <?=($row['max_invest_nolimit'] == 'Y') ? 'disabled' : ''; ?>></li><li>원</li>
				<li>
					<div class="checkbox">
						<label><input type="checkbox" name="max_invest_nolimit" value="Y" <?=($row['max_invest_nolimit'] == 'Y') ? 'checked' : ''; ?>> 체크시 한도없음</label>
					</div>
				</li>
				<div class="help-block" style="padding-left: 5px;">투자자 최대 투자금 한도 설정입니다. 입력한 금액 이상이어야 투자가 가능합니다.</div>
			</ul>
		</div>
	</div>
	<!-- /.col-lg-12 -->

	<h2 class="page-header text-center">메인페이지 누적투자금설정</h2>
	<div class="col-lg-12">
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title"><?=$PRNT_SUBJECT['average_return']?></label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="average_return" value="<?=$row['average_return']?>" class="form-control">
				</li>
				<li>%</li>
				<div class="help-block" style="padding-left:5px;">
					입력한 수치가 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title"><?=$PRNT_SUBJECT['total_invest']?></label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="total_invest" value="<?=$row['total_invest']?>" class="form-control">
				</li>
				<li>원</li>
				<div class="help-block" style="padding-left:5px;">
					입력한 수치가 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>

		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title"><?=$PRNT_SUBJECT['total_repay']?></label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="total_repay" value="<?=$row['total_repay']?>" class="form-control">
				</li>
				<li>원</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 수치가 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>

	</div>
	<div class="col-lg-12">

		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title"><?=$PRNT_SUBJECT['invest_ing_amount']?></label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="invest_ing_amount" value="<?=$row['invest_ing_amount']?>" class="form-control">
				</li>
				<li>원</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 내용이 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>

		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">연체율</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="overdue_perc" value="<?=$row['overdue_perc']?>" class="form-control">
				</li>
				<li>%</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 내용이 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>

		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">부실율</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="bankruptcy" value="<?=$row['bankruptcy']?>" class="form-control">
				</li>
				<li>%</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 내용이 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>

	</div>
	<div class="col-lg-12">

		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">집계기준일</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="standard_date" value="<?=$row['standard_date']?>" readonly class="form-control datepicker">
				</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 내용이 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">펀딩성공건수</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<input type="text" name="invest_success_count" value="<?=$row['invest_success_count']?>" class="form-control">
				</li>
				<li>건</li>
				<div class="help-block" style="padding-left: 5px;">
					입력한 수치가 메인페이지에 노출됩니다.
				</div>
			</ul>
		</div>
		<div class="form-group tmp1 left">
			<label class="col-sm-1 control-label tmp_title">노출여부</label>
			<ul class="col-sm-10 list-inline" style="margin-bottom:0; width:70%">
				<li>
					<div class="radio">
						<label>
							<input type="radio" name="display" value="Y" <?php echo ($row['display'] == 'Y' || !$row['display']) ? 'checked' : ''; ?>> 노출
						</label>
					</div>
				</li>
				<li>
					<div class="radio">
						<label>
							<input type="radio" name="display" value="N" <?php echo ($row['display'] == 'N') ? 'checked' : ''; ?>> 비노출
						</label>
					</div>
				</li>
			</ul>
		</form>

	</div>
	<div class="col-lg-12">
		<!-- /.col-lg-12 -->
		<p class="text-center">
			<button type="submit" class="btn btn-success">설정을 저장합니다.</button>
		</p>
	</div>
	</form>
</div>
<!-- /.row -->

<script>
function finquiry_submit(f) {
	return true;
}
</script>

<?php
include_once ('../admin.tail.php');
?>
