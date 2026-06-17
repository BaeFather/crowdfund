<?
###############################################################################
## 상품등록폼
###############################################################################
## 2017-08 신한 제3자 예치 시스템 적용
###############################################################################

$sub_menu = "600100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], 'w');


if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$is_chrome = (preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT'])) ? true : false;
if(preg_match('/edge/i', $_SERVER['HTTP_USER_AGENT'])) $is_chrome = false;


while(list($k, $v) = each($_REQUEST)) { ${$k} = trim($v); }

$PRDT = sql_fetch("SELECT * FROM cf_product WHERE idx='$idx'");

if(empty($PRDT)) {
	if($copy_idx) {
		$ROW = sql_fetch("SELECT * FROM cf_product WHERE idx='$copy_idx'");

		$PRDT = array(
			'ai_grp_idx'             => $ROW['ai_grp_idx'],
			'category'               => $ROW['category'],
			'title'                  => $ROW['title'],
			'invest_period'          => $ROW['invest_period'],
			'invest_days'            => $ROW['invest_days'],
			'invest_return'          => $ROW['invest_return'],
			'loan_interest_rate'     => $ROW['loan_interest_rate'],
			'overdue_rate'           => $ROW['overdue_rate'],
			'withhold_tax_rate'      => $ROW['withhold_tax_rate'],
			'loan_interest_type'     => $ROW['loan_interest_type'],
			'loan_advanced_count'    => $ROW['loan_advanced_count'],
			'loan_usefee'            => $ROW['loan_usefee'],
			'invest_usefee'          => $ROW['invest_usefee'],
			'invest_usefee_type'     => $ROW['invest_usefee_type'],
			'middle_withdraw_state'  => $ROW['middle_withdraw_state'],
			'middle_withdraw_charge' => $ROW['middle_withdraw_charge'],
			'repay_type'             => $ROW['repay_type'],
			'repay_acct_no'          => $ROW['repay_acct_no'],
			'loan_mb_no'             => $ROW['loan_mb_no'],
			'purchase_guarantees'    => $ROW['purchase_guarantees'],
			'portfolio'              => $ROW['portfolio'],
			'mortgage_guarantees'    => $ROW['mortgage_guarantees'],
			'loan_dep_bank_cd1'      => $ROW['loan_dep_bank_cd1'],
			'loan_dep_acct_nb1'      => $ROW['loan_dep_acct_nb1'],
			'loan_dep_amt1'          => $ROW['loan_dep_amt1'],
			'loan_dep_acct_memo1'    => $ROW['loan_dep_acct_memo1'],
			'loan_dep_bank_cd2'      => $ROW['loan_dep_bank_cd2'],
			'loan_dep_acct_nb2'      => $ROW['loan_dep_acct_nb2'],
			'loan_dep_amt2'          => $ROW['loan_dep_amt2'],
			'loan_dep_acct_memo2'    => $ROW['loan_dep_acct_memo2'],
			'loan_dep_bank_cd3'      => $ROW['loan_dep_bank_cd3'],
			'loan_dep_acct_nb3'      => $ROW['loan_dep_acct_nb3'],
			'loan_dep_amt3'          => $ROW['loan_dep_amt3'],
			'loan_dep_acct_memo3'    => $ROW['loan_dep_acct_memo3'],
			'loan_dep_bank_cd4'      => $ROW['loan_dep_bank_cd4'],
			'loan_dep_acct_nb4'      => $ROW['loan_dep_acct_nb4'],
			'loan_dep_amt4'          => $ROW['loan_dep_amt4'],
			'loan_dep_acct_memo4'    => $ROW['loan_dep_acct_memo4'],
			'loan_dep_bank_cd5'      => $ROW['loan_dep_bank_cd5'],
			'loan_dep_acct_nb5'      => $ROW['loan_dep_acct_nb5'],
			'loan_dep_amt5'          => $ROW['loan_dep_amt5'],
			'loan_dep_acct_memo5'    => $ROW['loan_dep_acct_memo5'],
			'judge'                  => $ROW['judge'],
			'screening'              => $ROW['screening'],
			'comment'                => $ROW['comment'],
			'receiver'               => $ROW['receiver'],
			'broker'                 => $ROW['broker'],
			'commission_fee'         => $ROW['commission_fee']
		);
		//print_rr($PRDT, 'font-size:12px');
	}
}


if($PRDT['state']=='') {
	if($PRDT['ib_trust']=='Y') {
		if($PRDT['ib_loan_start']!='S')	{
			$button_caption = ($PRDT['ib_product_regist']=='Y') ? '기관등록정보변경':'기관등록';
			if($PRDT['loan_mb_no']) {
				$ib_vact_reg_button = "<button type='button' id='btn_sh_regist' class='btn btn-danger' style='width:20%;'>".$button_caption."</button>";
			}
			else {
				$ib_vact_reg_button = "<button type='button' onClick=\"alert('먼저 대출자를 등록(선택)한 후 상품정보를 저장하면 기관등록기능이 활성화 됩니다.');$('#loan_mb_no').focus();\" class='btn' style='width:20%;'>".$button_caption."</button>";
			}
		}
		else {
			$ib_vact_reg_button = "<button type='button' onClick=\"alert('금융기관의 대출실행처리가 완료된 상품의 관련자료 수정은 허용하지 않습니다.');\" class='btn' style='width:20%;'>기관등록</button>";
		}
	}
	else {
		$ib_vact_reg_button = "<button type='button' onClick=\"alert('신한은행연계 예치금 신탁상품으로 등록 후 저장하십시요.');$('#ib_trust').focus();\" class='btn' style='width:20%;'>기관등록</button>";
	}
}
else {
	$ib_vact_reg_button = "<button type='button' onClick=\"alert('투자금 모집실패 또는 대출실행이 완료된 상품의 기관 등록용 자료의 수정은 허용하지 않습니다.');\" class='btn' style='width:20%;'>기관등록</button>";
}


// 매출채권 배열화
$res = sql_query("SELECT idx, grp_title FROM cf_auto_invest_config WHERE display='Y' ORDER BY idx");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$AR_LIST[] = sql_fetch_array($res);
}

// 상품그룹 배열화
$res = sql_query("SELECT idx, title FROM cf_product WHERE idx=gr_idx AND display='Y' ORDER BY start_datetime DESC, idx DESC");
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$PARENT[] = sql_fetch_array($res);
}


$g5['title'] = '상품등록';
include_once('../admin.head.php');

add_javascript(G5_POSTCODE_JS, 0);		//다음 주소 js
?>

<link href="/adm/css/bootstrap.min.css" rel="stylesheet">
<link href="/adm/css/jquery-ui.min.css" rel="stylesheet">
<script src="/adm/js/jquery-ui.min.js"></script>
<script src="/adm/js/jquery.form.js"></script>

<style>
.form-group {width:99.8%;}
.col-sm-1 { width:180px; }
label {font-weight:normal}
.button_area_scroll { position:fixed; z-index:10; bottom:0; border-top:1px solid #222; background-color:#fff; opacity:0.7; }
</style>


<div class="tbl_head02 tbl_wrap">

	<form id="product_form" name="product_form" method="post" action="product_form_update.php" enctype="multipart/form-data">
	<input type="hidden" name="token" value="">
	<input type="hidden" name="action" value="<?=($PRDT['idx'])?'product_update':'product_insert';?>">
	<input type="hidden" name="idx" id="prd_idx" value="<?=$PRDT['idx']?>">

<? if($PRDT['idx']=='164') { echo "<h3 style='color:red'>[정보삭제예정상품]</h3>"; } ?>

	<h3>대출정보</h3>
	<table class="table table-bordered" style="min-width:1200px">
		<colgroup>
			<col width="12%">
			<col width="38%">
			<col width="12%">
			<col width="38%">
		</colgroup>
		<tbody>
		<tr>
			<th>담보물건형태</th>
			<td>
				<ul class="col-sm-10 list-inline" style="margin-bottom:0;">
					<li style="float:left"><label class="radio-inline"><input type="radio" name="category" id="category2" value="2" <?=(empty($PRDT['category']) || $PRDT['category']=='2')?'checked':'';?>>부동산</label></li>
					<li style="float:left"><label class="radio-inline"><input type="radio" name="category" id="category1" value="1" <?=($PRDT['category']=='1')?'checked':'';?>>동산</label></li>
					<li style="float:left"><label class="radio-inline"><input type="radio" name="category" id="category3" value="3" <?=($PRDT['category']=='3')?'checked':'';?>>확정매출채권</label></li>
				</ul>
			</td>
			<th>동일차주설정</th>
			<td>
				<select name="gr_idx" id="gr_idx" onChange="copy_product();" class="form-control">
					<option value="">:: 선택 ::</option>
<?
	for($i=0; $i<count($PARENT); $i++) {
		if($copy_idx) {
			$selected = ($PARENT[$i]['idx']==$copy_idx) ? 'selected' : '';
		}
		else {
			$selected = ($PARENT[$i]['idx']==$PRDT['gr_idx']) ? 'selected' : '';
		}
		echo '<option value="'.$PARENT[$i]['idx'].'" '.$selected.'>'.$PARENT[$i]['title'].'</option>' . PHP_EOL;
	}
?>
				</select>
				<span style="font-size:12px;color:brown">※ 기존 대출에서 사용된 상환용 가상계좌를 재사용 할 경우 설정하세요.</span>
			</td>
		</tr>

		<tr>
			<th>상품구분</th>
			<td height="72">
				<ul class="col-sm-10 list-inline" style="margin-bottom:10px;">
					<? if($PRDT['state']) { ?>
					<input type="hidden" id="ib_trust" name="ib_trust" value="<?=$PRDT['ib_trust']?>">
					<li><label class="checkbox-inline" style="color:#FF2222;">신한은행연계 예치금 신탁상품</label></li>
					<? } else { ?>
					<li><label class="checkbox-inline" style="color:#FF2222;"><input type="checkbox" id="ib_trust" name="ib_trust" value="Y" <?=($PRDT['ib_trust']=='Y')?'checked':'';?>>신한은행연계 예치금 신탁상품</label></li>
					<? } ?>
					<li><label class="checkbox-inline"><input type="checkbox" name="purchase_guarantees" value="Y" <?=($PRDT['purchase_guarantees']=='Y')?'checked':'';?>>채권매입보증</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="portfolio" value="Y" <?=($PRDT['portfolio']=='Y')?'checked':'';?>>포트폴리오상품</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="advanced_payment" value="Y" <?=($PRDT['advanced_payment']=='Y')?'checked':'';?>>이자선지급</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="mortgage_guarantees" value="1" <?=($PRDT['mortgage_guarantees']=='1')?'checked':'';?>>주택담보대출</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" id="ai_flag" onClick="selectUsable()" <?=($PRDT['ai_grp_idx'])?'checked':'';?>>자동투자상품</label></li>
					<li style="height:16px;"><div id="ai_zone" style="float:left; position:absolute; z-index:2;">
							<select id="ai_grp_idx" name="ai_grp_idx" class="form-control" onChange="showDetailInfo();" onFocus="openSelectList();" onBlur="closeSelectList();hideDetailInfo();">
								<option value="">자동투자그룹선택</option>
<?
	for($i=0; $i<count($AR_LIST); $i++) {
		$selected = ($PRDT['ai_grp_idx']==$AR_LIST[$i]['idx']) ? 'selected' : '';
		echo '<option value="'.$AR_LIST[$i]['idx'].'" '.$selected.'>'.$AR_LIST[$i]['grp_title'].'</option>' . PHP_EOL;
	}
?>
							</select>
						</div>
						<div id="ai_grp_detail_zone" style="margin-left:158px; position:absolute; z-index:3; padding:4px; border:2px solid #000; background:#FFF; display:none;"></div>
					</li>
				</ul>

<script>
selectUsable = function() {
	if( $('input:checkbox[id="ai_flag"]').is(':checked')==true ) {
		$('#ai_grp_idx').removeAttr('disabled');
	}
	else {
		$('#ai_grp_idx').attr('disabled','true');
	}
}
$(document).ready(function(){ selectUsable(); });

openSelectList = function() {
	var obj = document.getElementById("ai_grp_idx");
	var n = obj.options.length;
	obj.size = n;
}
closeSelectList = function() {
	var obj = document.getElementById("ai_grp_idx");
	obj.size = 1;
}


showDetailInfo = function() {
	idx = $('#ai_grp_idx').val();
	if(idx) {
		$.ajax({
			type: "GET",
			url: "/adm/auto_invest/ajax_auto_invest_group_detail.php",
			data: {idx:idx},
			success: function(result) {
				if(result!='NULL') {
					$('#ai_grp_detail_zone').show();
					$('#ai_grp_detail_zone').html(result);
				}
			},
			error: function(e) { }
		});
	}
	else {
		$('#ai_grp_detail_zone').hide();
	}
}

hideDetailInfo = function() {
	$('#ai_grp_detail_zone').hide();
}
</script>
			</td>
			<th>프론트노출플래그</th>
			<td colspan="3">
				<ul class="col-sm-10 list-inline" style="margin-bottom:0;">
					<li><label class="checkbox-inline"><input type="checkbox" name="success_example" value="Y" <?=($PRDT['success_example']=='Y')?'checked':'';?>>투자성공사례(투자성공상품 목록에 출력)</label></li>
					<li><label class="checkbox-inline"><input type="checkbox" name="popular_goods" value="Y" <?=($PRDT['popular_goods']=='Y')?'checked':'';?>>인기상품</label></li>
				</ul>
			</td>
		</tr>

<?
if($PRDT['ib_trust']=='Y') {
?>
		<tr>
			<th style="background:#FFE4B9">신탁설정</th>
			<td colspan="3" style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tr>
						<th style="background:#FFE4B9">대출자</th>
						<td>
							<select name="loan_mb_no" id="loan_mb_no" class="form-control">
								<option value="">:: 대출회원 선택 ::</option>
<?
	$resx = sql_query("
		SELECT
			A.mb_no, A.mb_id, A.mb_co_name, A.mb_name, A.member_type,
			B.acct_no
		FROM
			g5_member A
			LEFT JOIN IB_vact_hellocrowd B
			ON A.mb_no=B.CUST_ID
		WHERE
			A.member_group='L'
		ORDER BY
			A.mb_no DESC");
	while($LOANER = sql_fetch_array($resx)) {
		$selected = ($PRDT['loan_mb_no']==$LOANER['mb_no']) ? 'selected' : '';
		$print_loaner_name = ($LOANER['member_type']=='2') ? $LOANER['mb_co_name'] : $LOANER['mb_name'];
		echo '<option value="'.$LOANER['mb_no'].'" data-repay_acct_no="'.$LOANER['acct_no'].'" '.$selected.'>'.$print_loaner_name.' ('.$LOANER['mb_id'].')</option>' . PHP_EOL;
	}
?>
							</select>
						</td>
						<th style="background:#FFE4B9">원리금상환<br>가상계좌</th>
						<td>
							<input type="text" id="repay_acct_no" name="repay_acct_no" value="<?=$PRDT['repay_acct_no']?>" class="form-control" style="width:200px">

							<div id="vacct"><?=($PRDT['repay_acct_no']) ? '<span style="color:#3366FF">'.$BANK['088'].' '.$PRDT['repay_acct_no'].'</span>' : '<span style="color:brown">대출회원 설정 저장시 자동 발급됩니다.<br>동일차주 하위 대출상품은 최상위 대출의 가상계좌 자동승계됨.<span>';?></div>
						</td>
					</tr>
					<tr>
						<th style="background:#FFE4B9">대출금입금계좌</th>
						<td colspan="3" style="padding:2px">
<?
	$BANK_KEYS = array_keys($BANK);
	for($i=0,$j=1; $i<5; $i++,$j++) {
?>
							<ul class="list-inline" style="margin:0;">
								<li>
									<select id="loan_dep_bank_cd<?=$j?>" name="loan_dep_bank_cd<?=$j?>" class="form-control">
										<option value="">:: 은행선택 ::</option>
<?
		for($x=0; $x<count($BANK); $x++) {
			$selected = ($PRDT['loan_dep_bank_cd'.$j]==$BANK_KEYS[$x]) ? 'selected' : '';
			echo '<option value="'.$BANK_KEYS[$x].'" '.$selected.'>'.$BANK[$BANK_KEYS[$x]].'</option>' . PHP_EOL;
		}
?>
									</select>
								</li>
								<li><input type="text" id="loan_dep_acct_nb<?=$j?>" name="loan_dep_acct_nb<?=$j?>" value="<?=$PRDT['loan_dep_acct_nb'.$j]?>" placeholder="계좌번호" onKeyUp="onlyDigit(this);" class="form-control"></li>
								<li><input type="text" id="loan_dep_amt<?=$j?>" name="loan_dep_amt<?=$j?>" value="<?=$PRDT['loan_dep_amt'.$j]?>" placeholder="금액" onKeyUp="onlyDigit(this);" class="form-control"></li>
								<li style="padding-left:0">원 &nbsp;&nbsp;</li>
								<li><input type="text" id="loan_dep_acct_memo<?=$j?>" name="loan_dep_acct_memo<?=$j?>" value="<?=$PRDT['loan_dep_acct_memo'.$j]?>" placeholder="계좌용도" class="form-control" style="width:200px"></li>
							</ul>
<?
	}
?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?
}
?>

		<tr>
			<th>상품명</th>
			<td colspan="3"><input type="text" name="title" value="<?=$PRDT['title']?>" style="width:800px" class="form-control" required></td>
		</tr>

		<tr>
			<th>모집목표금액</th>
			<td style="padding-left:2px">
				<ul class="list-inline" style="margin:0;">
					<li><input type="text" name="recruit_amount" value="<?=$PRDT['recruit_amount']?>" class="form-control" required></li>
					<li><span id="number_format"><?=number_format($PRDT['recruit_amount'])?></span>원</li>
				</ul>
			</td>
			<th>투자기간</th>
			<td style="padding-left:2px">
				<ul class="list-inline" style="margin:0;float:left;">
					<li><select id="invest_period" name="invest_period" class="form-control" style="width:120px" required onChange="inpAct();">
							<option value="">투자기간선택</option>
							<option value="under1month" <?=($PRDT['invest_period']==1 && $PRDT['invest_days']>0)?'selected':''?>>1개월 미만</option>
<?
	for($m=1; $m<=60; $m++) {
		$selected = ($PRDT['invest_period']==$m && $PRDT['invest_days']==0) ? 'selected' : '';
		echo '<option value="'.$m.'" '.$selected.'>'.$m.'개월</option>' . PHP_EOL;
	}
?>
						</select>
					</li>
				</ul>
				<ul id="invest_days_zone" class="list-inline" style="margin:0;float:left;">
					<li>→</li>
					<li><input type="text" id="invest_days" name="invest_days" value="<?=$PRDT['invest_days']?>" class="form-control" maxlength="2" style="width:50px;text-align:right" onKeyup="onlyDigit(this);"></li>
					<li style="padding-left:0">일</li>
				</ul>
			</td>
		</tr>

<script>
inpAct = function() {
	var sval = $('select[id="invest_period"]').val();
	dpval = (sval=='under1month') ? 'block' : 'none';
	$('#invest_days_zone').css('display', dpval);
}
$(document).ready(function(){ inpAct(); });
</script>

		<tr>
			<th>연 이율</th>
			<td colspan="3" style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tr>
						<th>투자수익률</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li>연</li>
								<li><input type="text" name="invest_return" value="<?=$PRDT['invest_return']?>" class="form-control" style="width:80px" required></li>
								<li>%</li>
							</ul>
						</td>
						<th>대출이율</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li>연</li>
								<li><input type="text" name="loan_interest_rate" value="<?=$PRDT['loan_interest_rate']?>" class="form-control" style="width:80px" required></li>
								<li>%</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th>원천징수율</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li><input type="text" name="withhold_tax_rate" value="27.5" readonly class="form-control" style="width:80px"></li>
								<li>%</li>
								<li style="font-size:12px;color:#aaa">이자소득세 25% + 지방소득세 2.5%</li>
							</ul>
						</td>
						<th>연체이자</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li>연</li>
								<li><input type="text" name="overdue_rate" value="<?=($PRDT['overdue_rate'])?$PRDT['overdue_rate']:'24';?>" class="form-control" style="width:80px"></li>
								<li>%</li>
							</ul>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>플랫폼이용료율</th>
			<td colspan="3" style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tr>
						<th>대출자</th>
						<td colspan="3" style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li>연</li>
								<li><input type="text" name="loan_usefee" value="<?=$PRDT['loan_usefee']?>" class="form-control" style="width:150px" required></li>
								<li>%</li>
								<li style="font-size:12px;color:#aaa">(선취시 0으로 표기)</li>
							</ul>
						</td>
					</tr>
					<tr>
						<th>투자자</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li>연</li>
								<li><input type="text" name="invest_usefee" value="<?=($PRDT['invest_usefee']) ? sprintf('%.2f', $PRDT['invest_usefee']) : '1.20'; ?>" class="form-control" style="width:80px" required></li>
								<li>%</li>
								<li style="font-size:12px;color:#aaa">((투자금x설정요율/100) / 365) x 투자일수. 소수점이하 절사처리</li>
							</ul>

<?
if($PRDT['idx']) {
	$resx = sql_query("SELECT mb_no, mb_co_name FROM g5_member WHERE mb_level='1' AND member_group='F' AND member_type='2' AND mb_co_reg_num='4428100445' ORDER BY mb_no");		// 피델리스
	$mb_no = '';
	while( $row = sql_fetch_array($resx) ) {
		$mb_no.= $row['mb_no'] . ',';
	}
	if($mb_no) {
		$mb_no = substr($mb_no, 0, -1);
		$options= '<option value="'.$mb_no.'">피델리스</option>' . PHP_EOL;
	}
?>
							<ul class="list-inline" style="margin:4px 0 0;">
								<li>예외처리</li>
								<li><select id="exp_mb_no" class="form-control input-sm">
										<option value="">::대상선택::</option>
										<?=$options?>
									</select>
								</li>
								<li>연</li>
								<li><input type="text" id="exp_invest_fee" class="form-control input-sm" style="width:80px"></li>
								<li>%</li>
								<li><button type="button" id="exp_fee_regist" class="btn btn-sm btn-primary">등록</button></li>
							</ul>
							<div id="exp_list" style="max-height:170px;overflow-y:auto"></div>
<script>
$(document).ready(function() {
	$.ajax({
		type: "post",
		url: "ajax_exp_invest_fee_proc.php",
		data: {exp_prd_idx:'<?=$idx?>'},
		success: function(data) { $('#exp_list').html(data); },
		error: function(e) { return; }
	});
});

$('#exp_fee_regist').on('click', function() {
	if($('#exp_mb_no').val()=='') {
		alert('예외처리 대상을 선택하십시요.');
		return;
	}
	else {
		$.ajax({
			type: "post",
			url: "ajax_exp_invest_fee_proc.php",
			data: {
				exp_prd_idx:'<?=$idx?>',
				exp_mb_no:$('#exp_mb_no').val(),
				exp_invest_fee:$('#exp_invest_fee').val()
			},
			success:function(data) {
				$('#ajax_return_txt').val(data);
				if(data=='NONE_MB_NO') { alert('전송된 예외처리 대상이 없습니다.'); }
			//else if(data=='NONE_FEE') { alert('예외처리 대상을 선택하십시요.'); }
				else { $('#exp_list').html(data); }
				loading('off');
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function(e) { return; }
		});
	}
});

exp_delete = function(n) {
	if(confirm('삭제하시겠습니까?')) {
		$.ajax({
			type: "post",
			url: "ajax_exp_invest_fee_proc.php",
			data: {
				exp_prd_idx:'<?=$idx?>',
				exp_idx:n,
				exp_drop:'1'
			},
			success:function(data) {
				$('#exp_list').html(data);
				loading('off');
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function(e) { return; }
		});
	}
}
</script>
<?
}
?>
						</td>
						<th>징수방식</th>
						<td>
							<ul class="col-sm-10 list-inline" style="margin-bottom:0;">
								<li>
									<label class="radio-inline"><input type="radio" name="invest_usefee_type" value="A" <?=(empty($PRDT['invest_usefee_type']) || $PRDT['invest_usefee_type']=='A')?'checked':''?>> 월별분할징수</label>
									<label class="radio-inline" style="color:#ccc"><input type="radio" name="invest_usefee_type" value="B" <?=($PRDT['invest_usefee_type']=='B')?'checked':''?> disabled> 만기일시징수</label>
								</li>
							</ul>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>대출자 이자징수</th>
			<td><ul class="col-sm-10 list-inline" style="margin:0">
					<li>
						<label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type" value="0" <?=(empty($PRDT['loan_interest_type']) || $PRDT['loan_interest_type']=='0')?'checked':''?>> 월이자</label><br>
						<label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type" value="1" <?=($PRDT['loan_interest_type']=='1')?'checked':''?>> 선이자</label>
					</li>
				</ul>
				<ul class="col-sm-10 list-inline" style="margin:0">
					<li><label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type" value="2" <?=($PRDT['loan_interest_type']=='2')?'checked':''?>> 부분선이자</label>
					<li><input type="text" id="bb" name="loan_advanced_count" onKeyUp="onlyDigit(this)" value="<?=$PRDT['loan_advanced_count']?>" class="form-control" style="width:50px;text-align:right"></li>
					<li>회차까지</li>
				</ul>
			</td>
			<th>상환방식</th>
			<td><ul class="col-sm-10 list-inline" style="margin:0">
					<li>
						<label class="radio-inline"><input type="radio" name="repay_type" value="1" <? echo (empty($PRDT['repay_type']) || $PRDT['repay_type']=='1') ? 'checked' : ''; ?>>원금 만기일시상환</label><br>
						<label class="radio-inline"><input type="radio" name="repay_type" value="2" <? echo ($PRDT['repay_type']=='2') ? 'checked' : ''; ?>>원리금 균등상환</label><br>
						<label class="radio-inline"><input type="radio" name="repay_type" value="3" <? echo ($PRDT['repay_type']=='3') ? 'checked' : ''; ?>>원금 균등상환</label><br>
						<label class="radio-inline" style="color:#FF2222"><input type="radio" name="repay_type" value="4" <? echo ($PRDT['repay_type']=='4') ? 'checked' : ''; ?>>원리금 만기일시상환</label>
					</li>
				</ul>
			</td>
		</tr>

		<tr>
			<th>중개자</th>
			<td><ul class="col-sm-10 list-inline" style="margin-bottom:0;">
					<li>중개자</li>
					<li><input type="text" name="broker" value="<?=$PRDT['broker'];?>" class="form-control"></li>
					<li></li>
					<li>중개수수료</li>
					<li><input type="text" name="commission_fee" value="<?=$PRDT['commission_fee'];?>" class="form-control" style="width:80px"></li>
					<li>%</li>
				</ul>
			</td>
			<th>접수자</th>
			<td><input type="text" name="receiver" value="<?=$PRDT['receiver'];?>" class="form-control" style="width:150px"></li>
		</tr>

		<tr>
			<th>중도인출</th>
			<td>
				<ul class="col-sm-10 list-inline" style="margin-bottom:0;">
					<li><label class="radio-inline"><input type="radio" name="middle_withdraw_state" value="1" <?=($PRDT['middle_withdraw_state']=='1') ? 'checked' : ''; ?>> 가능</label></li>
					<li><label class="radio-inline"><input type="radio" name="middle_withdraw_state" value="2" <?=(empty($PRDT['middle_withdraw_state']) || $PRDT['middle_withdraw_state']=='2') ? 'checked' : ''; ?>>불가능</label></li>
				</ul>
			</td>
			<th>중도인출 수수료</th>
			<td style="padding-left:2px">
				<ul class="list-inline" style="margin:0;">
					<li><input type="text" name="middle_withdraw_charge" id="middle_withdraw_charge" value="<?=$PRDT['middle_withdraw_charge']?>" class="form-control" style="width:80px"></li>
					<li>%</li>
				</ul>
			</td>
		</tr>

		<tr>
			<th>사전투자</th>
			<td>
				<ul class="col-sm-10 list-inline">
					<li><label class="radio-inline"><input type="radio" name="advance_invest" value="Y" <?=($PRDT['advance_invest']=='Y')?'checked':''?>>가능</label></li>
					<li><label class="radio-inline"><input type="radio" name="advance_invest" value="N" <?=(empty($PRDT['advance_invest']) || $PRDT['advance_invest']=='N')?'checked':''?>>불가능</label></li>
				</ul>
			</td>
			<th>사전투자비율</th>
			<td style="padding-left:2px">
				<ul class="list-inline" style="margin:0;">
					<li><input type="text" name="advance_invest_ratio" id="advance_invest_ratio" value="<?=$PRDT['advance_invest_ratio']?>" class="form-control" style="width:80px"></li>
					<li>%</li>
				</ul>
			</td>
		</tr>

		<tr>
			<th>모집기간</th>
			<td colspan="3" style="padding-left:2px">
				<ul class="list-inline" style="margin:0;">
					<li><input type="text" name="recruit_period_start" value="<?=$PRDT['recruit_period_start']?>" class="form-control datepicker" required></li>
					<li>~</li>
					<li><input type="text" name="recruit_period_end" value="<?=$PRDT['recruit_period_end']?>" class="form-control datepicker" required></li>
				</ul>
			</td>
		</tr>

		<tr>
			<th>스케쥴러</th>
			<td colspan="3" style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="88%">
					</colgroup>
					<tr>
						<th>상품공개</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li><input type="text" name="open_date" value="<?=$PRDT['open_date']?>" class="form-control datepicker"></li>
								<li><input type="text" name="open_hour" value="<?=$PRDT['open_hour']?>" class="form-control" style="width:60px"></li>
								<li>시</li>
								<li><input type="text" name="open_minute" value="<?=$PRDT['open_minute']?>" class="form-control" style="width:60px"></li>
								<li>분</li>
								<li><input type="text" name="open_second" value="<?=$PRDT['open_second']?>" class="form-control" style="width:60px"></li>
								<li>초</li>
							</ul>
							<div class="help-block" style="display:none;">※ 상품 등록이 되어 상품 목록에 노출은 되나 실투자는 되지 않음. 사전투자 가능 상품의 경우 투자 가능.</div>
						</td>
					</tr>
					<tr>
						<th>모집시작</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li><input type="text" name="start_date" value="<?=$PRDT['start_date']?>" class="form-control datepicker"></li>
								<li><input type="text" name="start_hour" value="<?=$PRDT['start_hour']?>" class="form-control" style="width:60px"></li>
								<li>시</li>
								<li><input type="text" name="start_minute" value="<?=$PRDT['start_minute']?>" class="form-control" style="width:60px"></li>
								<li>분</li>
								<li><input type="text" name="start_second" value="<?=$PRDT['start_second']?>" class="form-control" style="width:60px"></li>
								<li>초</li>
							</ul>
							<div class="help-block" style="display:none;">※ 실제 투자 시작 시점입니다.</div>
						</td>
					</tr>
					<tr>
						<th>모집마감</th>
						<td style="padding-left:2px">
							<ul class="list-inline" style="margin:0;">
								<li><input type="text" name="end_date" value="<?=$PRDT['end_date']?>" class="form-control datepicker"></li>
								<li><input type="text" name="end_hour" value="<?=$PRDT['end_hour']?>" class="form-control" style="width:60px"></li>
								<li>시</li>
								<li><input type="text" name="end_minute" value="<?=$PRDT['end_minute']?>" class="form-control" style="width:60px"></li>
								<li>분</li>
								<li><input type="text" name="end_second" value="<?=$PRDT['end_second']?>" class="form-control" style="width:60px"></li>
								<li>초</li>
							</ul>
							<div class="help-block" style="display:none;">※ 투자 마감 일자 시점입니다.</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>노출설정</th>
			<td colspan="3">
				<ul class="col-sm-10 list-inline">
					<li><label class="radio-inline"><input type="radio" name="display" value="Y" <? echo ($PRDT['display'] == 'Y') ? 'checked' : ''; ?>>노출</label></li>
					<li><label class="radio-inline"><input type="radio" name="display" value="N" <? echo ($PRDT['display'] == 'N' || !$PRDT['display']) ? 'checked' : ''; ?>>비노출</label></li>
				</ul>
			</td>
		</tr>

		</tbody>
	</table>

	<br>

	<h3>상품정보</h3>

	<table class="table table-bordered" style="min-width:1200px">
		<colgroup>
			<col width="12%">
			<col width="88%">
		</colgroup>
		<tbody>
		<tr>
			<th>이미지</th>
			<td style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="88%">
					</colgroup>
					<tr>
						<th>대표이미지</th>
						<td>
							<div class="input-group">
								<input type="file" name="main_image" class="form-control">
								<? if ($PRDT['main_image']) { ?>
								<div class="input-group-addon"><a href="<? echo G5_DATA_URL.'/product/'.$PRDT['main_image']?>" target="_blank">이미지보기</a></div>
								<? } ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>모바일 롤링 이미지</th>
						<td>
							<div class="input-group">
								<input type="file" name="main_image_m" class="form-control">
								<? if ($PRDT['main_image_m']) { ?>
								<div class="input-group-addon"><a href="<? echo G5_DATA_URL.'/product/'.$PRDT['main_image_m']?>" target="_blank">이미지보기</a></div>
								<? } ?>
							</div>
						</td>
					</tr>
					<tr>
						<th>상세정보 이미지</th>
						<td>
							<select name="detail_image[]" class="form-control" multiple>
<?
	foreach ((array)explode('|', $PRDT['detail_image']) as $key => $val) {
		if (!$val) {
			continue;
		}

		echo "<option value='".$val."'>".$val."</option>\n";

	}
?>
							</select>
							<div class="help-block">
								<button type="button" class="btn btn-primary" onclick="uploadImage('detail_image');">업로드</button>
								<button type="button" class="btn btn-danger" onclick="deleteImage('detail_image');">선택삭제</button>
							</div>
							<span style="font-size:12px;color:#aaa">권장(569 x 442)</span>
						</td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<th>현장카메라</th>
			<td style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="38%">
						<col width="12%">
						<col width="38%">
					</colgroup>
					<tr>
						<th>URL1</th>
						<td>
							<input type="text" name="stream_url1" value="<?=$PRDT['stream_url1']?>" class="form-control">
							<div style="font-size:12px;color:#aaa">※ ready 입력시 라이브버튼 활성화, 클릭시 안내팝업 오픈</div>
						</td>
						<th>URL2</th>
						<td>
							<input type="text" name="stream_url2" value="<?=$PRDT['stream_url2']?>" class="form-control">
							<div style="font-size:12px;color:#aaa">※ ready 입력시 라이브버튼 활성화, 클릭시 안내팝업 오픈</div>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?
if($PRDT['stream_url1'] || $PRDT['stream_url2']) {
	$play_url = "http://hellolivetv.co.kr/onair.php?prd_idx=".$PRDT['idx'];
	$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
?>
		<tr>
			<th>카메라뷰 배너링크</th>
			<td style="padding:2px"><table>
					<colgroup>
						<col width="12%">
						<col width="88%">
					</colgroup>
					<tr>
						<th>PD</th>
						<td>
							<textarea class="form-control" style="font-size:11px;height:78px" readonly>
설치전: <img src="/images/investment/live_ban01.gif" width="100%" onClick="openStreamReady()" style="cursor:pointer;">
설치후: <img src="/images/investment/live_ban01.gif" width="100%" onClick="window.open('<?=$play_url?>','stream_win','width=730,height=500,toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no');" style="cursor:pointer;"></textarea>
						</td>
					</tr>
					<tr>
						<th>모바일</th>
						<td>
							<textarea class="form-control" style="font-size:11px;height:78px" readonly>
설치전: <img src="/images/investment/live_ban01_m.gif" width="100%" onClick="openStreamReady()" style="cursor:pointer;">
설치후: <img src="/images/investment/live_ban01_m.gif" width="100%" onClick="window.open('<?=$play_url?>','stream_win','toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no');" style="cursor:pointer;"></textarea>
						</td>
					</tr>
				</table>
			</td>
		</tr>
<?
}
?>

		<tr>
			<th>상품평가 <button type="button" id="evaluate_on" style="width:55px;font-size:12px;font-weight:normal;float:right">펼치기</button></th>
			<td style="padding:2px">
				<div id="evaluate_zone" style="display:none;">
					<table>
						<colgroup>
							<col width="12%">
							<col width="38%">
							<col width="12%">
							<col width="38%">
						</colgroup>
						<tr>
							<th>안전성</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_score1" value="<?=$PRDT['evaluate_score1']?>" class="form-control" style="margin:0;width:80px"><li>
									<li>/ 40 (구 48점)</li>
								</ul>
							</td>
							<th>별점</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_star1" value="<?=$PRDT['evaluate_star1']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>개</li>
								</ul>
							</td>
						</tr>

						<tr>
							<th>상환성</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_score4" value="<?=$PRDT['evaluate_score4']?>" class="form-control" style="margin:0;width:80px"><li>
									<li>/ 30 (구 42점)</li>
								</ul>
							</td>
							<th>별점</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_star4" value="<?=$PRDT['evaluate_star4']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>개</li>
								</ul>
							</td>
						<tr>

						<tr>
							<th>환금성</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_score3" value="<?=$PRDT['evaluate_score3']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>/ 30 (구 5점)</li>
								</ul>
							</td>
							<th>별점</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_star3" value="<?=$PRDT['evaluate_star3']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>개 &nbsp; <span style='color:#FF2222'>(* 29, 30만 기제요망)</span></li>
								</ul>
							</td>
						<tr>

<? if($PRDT['evaluate_score2'] || $PRDT['evaluate_star2']) { ?>
						<tr>
							<th>수익성</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_score2" value="<?=$PRDT['evaluate_score2']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>/ 5</li>
								</ul>
							</td>
							<th>별점</th>
							<td><ul class="list-inline" style="margin:0;padding:0;">
									<li><input type="text" name="evaluate_star2" value="<?=$PRDT['evaluate_star2']?>" class="form-control" style="margin:0;width:80px"></li>
									<li>개 &nbsp (* 입력하지 않으면 신 등급체계에 반영되지 않음)</li>
								</ul>
							</td>
						<tr>
<? } ?>
					</table>
				</div>

			</td>
		</tr>

		<tr>
			<th>주소 <button type="button" id="addr_on" style="width:55px;font-size:12px;font-weight:normal;float:right">펼치기</button></th>
			<td style="padding:2px">
				<div id="addr_zone" style="display:none;">
					<table>
						<colgroup>
							<col width="12%">
							<col width="38%">
							<col width="12%">
							<col width="38%">
						</colgroup>
						<tr>
							<th>주소입력</th>
							<td colspan="3">
								<ul class="list-inline" style="margin:0;padding:0;display:inline-block;">
									<li style="float:left"><input type="text" name="zipcode" value="<?=$PRDT['zipcode']?>" id="zipcode" class="form-control" readonly size="5" maxlength="6"></li>
									<li style="float:left"><button type="button" class="btn btn-success" onClick="win_zip('product_form', 'zipcode', 'address', 'address_detail', 'address2', 'address3');">주소 검색</button></li>
								</ul>
								<input type="text" name="address" value="<?=$PRDT['address'] ?>" id="address" class="form-control" style="width:800px; margin-bottom:2px;" readonly>
								<input type="text" name="address_detail" value="<?=$PRDT['address_detail'] ?>" id="address_detail" class="form-control" style="width:800px;">
								<input type="hidden" name="address2" value="<?=$PRDT['address2'] ?>" id="address2" class="form-control">
								<input type="hidden" name="address3" value="<?=$PRDT['address3'] ?>" id="address3" class="form-control">
							</td>
						</tr>
						<tr>
							<th>위도</th>
							<td><input type="text" name="lat" value="<?=$PRDT['lat']?>" class="form-control" style="width:120px"></td>
							<th>경도</th>
							<td><input type="text" name="lng" value="<?=$PRDT['lng']?>" class="form-control" style="width:120px"></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr>
			<th>상세내용구성 <button type="button" id="content_on" style="width:55px;font-size:12px;font-weight:normal;float:right">펼치기</button></th>
			<td style="padding:2px">
				<div id="content_zone" style="display:block"></div>
			</td>
		</tr>

		<tr>
			<th>증빙서류</th>
			<td colspan="3">
				<select name="evidence[]" class="form-control" multiple>
<?
	foreach ((array)explode('|', $PRDT['evidence']) as $key => $val) {
		if (!$val) {
			continue;
		}

		echo "<option value='".$val."'>".$val."</option>\n";

	}
?>
				</select>
				<div style="margin-top:8px;">
					<button type="button" class="btn btn-default" onclick="uploadImage('evidence');">업로드</button>
					<button type="button" class="btn btn-default" onclick="deleteImage('evidence');">선택삭제</button>
				</div>
			</td>
		</tr>
		</tbody>
	</table>

	<div style="height:54px">
		<p id="button_area" style="width:100%;margin:0;left:0;padding:10px 0 10px 0;" class="text-center">
			<button type="button" onClick="formSubmit(document.product_form);" class="btn btn-success" style="width:40%;">상품<?=($PRDT['idx'])?'수정':'등록'?></button>
			<?=$ib_vact_reg_button?>
		</p>
	</div>

	</form>
</div>

<form id="upload_form" method="post" action="/builder/multiProcess.php" enctype="multipart/form-data">
	<input type="hidden" name="action" value="product_image_upload">
	<input type="file" name="image_upload" style="display:none;">
</form>

<div style="position:fixed; display:none; z-index:1002; top:150px;left:30px; border:1px solid #bbb; padding:4px;background-color:#FAFAFA;">
	top_position : <input type="text" id="top_position"> &nbsp;
	scroll_top : <input type="text" id="scroll_top">
</div>

<script>
$('#evaluate_on').click(function() {
	if($('#evaluate_zone').css('display')=='block') {
		$('#evaluate_on').text('펼치기');
	}
	else {
		$('#evaluate_on').text('접기');
	}
	$('#evaluate_zone').slideToggle('slow');
});

$(document).ready(function() {
	$.ajax({
		type: "get",
		dataType: "text html",
		url: "ajax_product_content_form.php",
		data: {idx:'<?=($copy_idx)?'':$PRDT['idx'];?>'},
		success:function(data) {
			$('#ajax_return_txt').val(data);
			$('#content_zone').html(data);
		//setTimeout("$('#content_zone').css('display','none');", 10*1000);
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function(e) { return; }
	});
});

$('#content_on').click(function() {
	if($('#content_zone').css('display')=='block') {
		$('#content_on').text('펼치기');
	}
	else {
		$('#content_on').text('접기');
	}
	$('#content_zone').slideToggle('slow');
});

$('#addr_on').click(function() {
	if($('#addr_zone').css('display')=='block') {
		$('#addr_on').text('펼치기');
	}
	else {
		$('#addr_on').text('접기');
	}
	$('#addr_zone').slideToggle('slow');
});
</script>

<script>
function changeTitle() {
	var caval = $('input:radio[name="category"]:checked').val();

	$('#chgTitle1').empty();
  $('#chgTitle2').empty();
  $('#chgTitle3').empty();
	if(caval==1) {
		$('#chgTitle1').append('대출자 정보');
		$('#chgTitle2').append('담보물 정보');
		$('#chgTitle3').append('투자자 보호장치');
		$('#chgStyle1').css({'display':'none'}); // 평가기관 의견 히든
		$('#chgStyle2').css({'display':'none'}); // 심사총평 히든
	}
	else {
		$('#chgTitle1').append('핵심 투자포인트');
		$('#chgTitle2').append('투자자 보호장치');
		$('#chgTitle3').append('담보분석 및 평가');
		$('#chgStyle1').css({'display':'block'});
		$('#chgStyle2').css({'display':'block'});
	}
}

$('#category1, #category2').click(function() {
	changeTitle();
});

$(document).ready(function() {
	changeTitle();
});
</script>

<script>
$(document).ready(function() {
	var _value = $('input:radio[name=loan_interest_type]:checked').val();
	var disabled = (_value==2) ? false : true;
	$('#bb').attr('disabled', disabled);
});

$('input:radio[name=loan_interest_type]').click(function() {
	var _value = $('input:radio[name=loan_interest_type]:checked').val();
	var disabled = (_value==2) ? false : true;
	$('#bb').attr('disabled', disabled);
});
</script>

<script>
$(document).ready(function() {
	var _value = $('input:radio[name=middle_withdraw_state]:checked').val();
	var disabled = (_value=='1') ? false : true;
	$('#middle_withdraw_charge').attr('disabled', disabled);
});
$('input:radio[name=middle_withdraw_state]').click(function() {
	var _value = $('input:radio[name=middle_withdraw_state]:checked').val();
	var disabled = (_value=='1') ? false : true;
	$('#middle_withdraw_charge').attr('disabled', disabled);
});
</script>

<script>
$(document).ready(function() {
	var _value = $('input:radio[name=advance_invest]:checked').val();
	var disabled = (_value=='Y') ? false : true;
	$('#advance_invest_ratio').attr('disabled', disabled);
});
$('input:radio[name=advance_invest]').click(function() {
	var _value = $('input:radio[name=advance_invest]:checked').val();
	var disabled = (_value=='Y') ? false : true;
	$('#advance_invest_ratio').attr('disabled', disabled);
});
</script>

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
var options = {
	url: './product_form_update.php'
};

$(function() {
	$("input[name=image_upload]").change(function() {
		$("#upload_form").ajaxSubmit(options)[0].reset();
	});

	$("input[name=recruit_amount]").keyup(function(e) {
		$("#number_format").text(number_format(this.value));
	});
});

function formSubmit(f) {

	<?=get_editor_js("extend_1");?>
	<?=get_editor_js("extend_2");?>
	<?=get_editor_js("extend_3");?>
	<?=get_editor_js("extend_4");?>
	<?=get_editor_js("extend_5");?>
	<?=get_editor_js("extend_6");?>
	<?=get_editor_js("extend_7");?>
	<?=get_editor_js("extend_8");?>
	<?=get_editor_js("extend_9");?>
	<?=get_editor_js("core_invest_point");?>
	<?=get_editor_js("screening");?>
	<?=get_editor_js("invest_summary");?>
	<?=get_editor_js("invest_summary_m");?>

	if($('input:radio[name="category"]:checked').val()=='') {
		alert('담보물건를 선택하십시요.');
	}
	else {
		$("select[name='detail_image[]'] option").prop('selected', true);
		$("select[name='evidence[]'] option").prop('selected', true);
		f.submit();
	}

}

function uploadImage(selector) {
	options.success = function(data) {
		$("select[name='"+selector+"[]']").append('<option value="'+data+'">'+data+'</option>');
	}

	$("input[name=image_upload]").trigger('click');
}

function deleteImage(selector) {
	file = $("select[name='"+selector+"[]'] :selected").val();
	$.post('./product_form_update.php', { action: 'product_image_delete', file: file }, function() {
		$("select[name='"+selector+"[]'] option[value="+file+"]").remove();
	});
}

$('#btn_sh_regist').click(function() {
	$('#btn_sh_regist').attr('disabled', 'disabled');

<?
	$request_mode = ($PRDT['ib_product_regist']=='Y') ? 'edit' : 'new';
	$success_msg = ($PRDT['ib_product_regist']=='Y') ? ' 기관등록정보변경 완료' : ' 기관등록 완료';
?>

	prd_idx = $('#prd_idx').val();
	gr_idx = $('#gr_idx').val();
<?	for($i=0,$j=1; $i<5; $i++,$j++) {	?>
	loan_dep_bank_cd<?=$j?>   = $('#loan_dep_bank_cd<?=$j?>').val();
	loan_dep_acct_nb<?=$j?>   = $('#loan_dep_acct_nb<?=$j?>').val();
	loan_dep_amt<?=$j?>       = $('#loan_dep_amt<?=$j?>').val();
	loan_dep_acct_memo<?=$j?> = $('#loan_dep_acct_memo<?=$j?>').val();
<?	}	?>
	$.ajax({
		type: "POST",
		url: "ajax_invest_shinhan_proc.php",
		data: {
			mode:'<?=$request_mode?>',
			prd_idx:prd_idx,
<?	for($i=0,$j=1; $i<5; $i++,$j++) {	?>
			loan_dep_bank_cd<?=$j?>:loan_dep_bank_cd<?=$j?>,
			loan_dep_acct_nb<?=$j?>:loan_dep_acct_nb<?=$j?>,
			loan_dep_amt<?=$j?>:loan_dep_amt<?=$j?>,
			loan_dep_acct_memo<?=$j?>:loan_dep_acct_memo<?=$j?>,
<?	}	?>
			gr_idx:gr_idx
		},
		success:function(result) {

			$('#ajax_return_txt').val(result);

			array_result = result.split(':');		// 결과값 배열화

			if(array_result[0]=='SUCCESS') {

				var success_msg = '<?=$success_msg?>';

				$('#btn_sh_regist').removeClass('btn-danger');

				$('#vacct').html(array_result[1]);
				$('#btn_sh_regist').text(success_msg);		//버튼캡션 변경
				alert(success_msg);
			}
			else if(array_result[0]=='ERROR') {
				if(array_result[1]=='LOGIN')                        { $(location).attr('href', '/'); }
				else if(array_result[1]=='NONE_MEMBER')             { $(location).attr('href', '/'); }
				else if(array_result[1]=='NONE_PRODUCT')            { alert('대출상품정보가 없습니다.'); }
				else if(array_result[1]=='EMPTY_LOANER_INFO')       { alert('대출자정보(대출회원정보)가 없습니다.\n대출회원을 등록하고 본상품에 해당 대출자로 선택하여야 합니다.'); }
				else if(array_result[1]=='EMPTY_LOANER_ACCT_INFO')  { alert('대출 입금계좌를 1개 이상 설정하십시요.'); }
				else if(array_result[1]=='DIFFRENT_DEPOSIT_AMOUNT') { alert('대출 입금계좌에 등록된 금액의 합과 모집목표금액이 상이합니다.'); }
				else if(array_result[1]=='SH_VA_INSUFFICIENCY')     { alert('배정 가능한 가상계좌(헬로크라우드대부용)가 없습니다.\n여유 가상계좌를 확보하십시요.'); }
				else { alert(array_result[1]); }

				$('#btn_sh_regist').removeAttr('disabled');

			}
			else {
				alert(result);
				$('#btn_sh_regist').removeAttr('disabled');
			}
		},
		error: function () {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.");
			$('#btn_sh_regist').removeAttr('disabled');
		}
	});

});

function copy_product(){
<? if($PRDT['idx']=='') { ?>
	var select_prdt = $('#gr_idx').val();
	if(select_prdt!='') { window.location.replace('product_form.php?copy_idx=' + select_prdt); }
<? } ?>
}
</script>

<?
include_once ('../admin.tail.php');
?>
