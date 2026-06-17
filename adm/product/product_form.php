<?
###############################################################################
## 상품등록폼
###############################################################################
## 2017-08 신한 제3자 예치 시스템 적용
## 2018-04-07 웹에디터 변경
###############################################################################

if(isset($_REQUEST['idx']) && $_REQUEST['idx']<=244) {
	include_once("./product_form.old.php");
	return;
}

$sub_menu = "600100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

// 권한체크
auth_check($auth[$sub_menu], 'w');


if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$is_chrome = (preg_match('/chrome/i', $_SERVER['HTTP_USER_AGENT'])) ? true : false;
if(preg_match('/edge/i', $_SERVER['HTTP_USER_AGENT'])) $is_chrome = false;


while(list($k, $v) = each($_REQUEST)) { ${$k} = trim($v); }

$sqlx = "
	SELECT
		A.*,
		B.*,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y') AS invest_amount
	FROM
		cf_product A
	LEFT JOIN
		cf_product_container B  ON A.idx=B.product_idx
	WHERE
		A.idx='$idx'";
$PRDT = sql_fetch($sqlx);

$stdYear = ($PRDT['loan_start_date'] > '0000-00-00') ? substr($PRDT['loan_start_date'],0,4) : date('Y');		// 기준년도
$dayCountOfYear = ( in_array($stdYear, $CONF['LEAP_YEAR']) ) ? 366 : 365;

if( $PRDT['idx'] ) {

	$repayDayCount = repayDayCount($PRDT['loan_start_date'], $PRDT['loan_end_date']);

	if($PRDT['platform']) { $PLATFORM = explode("|", $PRDT['platform']); }

}
else {
	if($copy_idx) {
		$ROW  = sql_fetch("
			SELECT
				A.*,
				B.*
			FROM
				cf_product A
			LEFT JOIN
				cf_product_container B  ON A.idx=B.product_idx
			WHERE
				idx='$copy_idx'");

		$PRDT = array(
			'gr_idx'                 => $ROW['gr_idx'],
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
			'loan_usefee_type'       => $ROW['loan_usefee_type'],
			'invest_usefee'          => $ROW['invest_usefee'],
			'invest_usefee_type'     => $ROW['invest_usefee_type'],
			'middle_withdraw_state'  => $ROW['middle_withdraw_state'],
			'middle_withdraw_charge' => $ROW['middle_withdraw_charge'],
			'repay_type'             => $ROW['repay_type'],
		//'repay_acct_no'          => $ROW['repay_acct_no'],
		//'loan_mb_no'             => $ROW['loan_mb_no'],
			'scrap_out'              => $ROW['scrap_out'],
			'purchase_guarantees'    => $ROW['purchase_guarantees'],
			'portfolio'              => $ROW['portfolio'],
			'mortgage_guarantees'    => $ROW['mortgage_guarantees'],
			'isConsor'               => $ROW['isConsor'],
			'consor_co'              => $ROW['consor_co'],
			'loan_dep_bank_cd1'      => $ROW['loan_dep_bank_cd1'],
		//'loan_dep_acct_nb1'      => $ROW['loan_dep_acct_nb1'],
		//'loan_dep_amt1'          => $ROW['loan_dep_amt1'],
		//'loan_dep_acct_memo1'    => $ROW['loan_dep_acct_memo1'],
		//'loan_dep_bank_cd2'      => $ROW['loan_dep_bank_cd2'],
		//'loan_dep_acct_nb2'      => $ROW['loan_dep_acct_nb2'],
		//'loan_dep_amt2'          => $ROW['loan_dep_amt2'],
		//'loan_dep_acct_memo2'    => $ROW['loan_dep_acct_memo2'],
		//'loan_dep_bank_cd3'      => $ROW['loan_dep_bank_cd3'],
		//'loan_dep_acct_nb3'      => $ROW['loan_dep_acct_nb3'],
		//'loan_dep_amt3'          => $ROW['loan_dep_amt3'],
		//'loan_dep_acct_memo3'    => $ROW['loan_dep_acct_memo3'],
		//'loan_dep_bank_cd4'      => $ROW['loan_dep_bank_cd4'],
		//'loan_dep_acct_nb4'      => $ROW['loan_dep_acct_nb4'],
		//'loan_dep_amt4'          => $ROW['loan_dep_amt4'],
		//'loan_dep_acct_memo4'    => $ROW['loan_dep_acct_memo4'],
		//'loan_dep_bank_cd5'      => $ROW['loan_dep_bank_cd5'],
		//'loan_dep_acct_nb5'      => $ROW['loan_dep_acct_nb5'],
		//'loan_dep_amt5'          => $ROW['loan_dep_amt5'],
		//'loan_dep_acct_memo5'    => $ROW['loan_dep_acct_memo5'],
			'judge'                  => $ROW['judge'],
			'screening'              => $ROW['screening'],
			'comment'                => $ROW['comment'],
			'receiver'               => $ROW['receiver'],
			'broker'                 => $ROW['broker'],
			'commission_fee'         => $ROW['commission_fee'],
			'isEtcCost'              => $ROW['isEtcCost']
		);
		//print_rr($PRDT, 'font-size:12px');
	}

}
//$chk_old = sql_fetch("SELECT COUNT(idx) AS cnt, IFNULL(SUM(recruit_amount),0) AS amt FROM cf_product WHERE gr_idx='".$PRDT['gr_idx']."' AND state IN('1','8') AND isTest='' AND recruit_amount >= 10000");
$GRPRDT['repaying'] = sql_fetch("SELECT COUNT(idx) AS cnt, IFNULL(SUM(recruit_amount),0) AS amt FROM cf_product WHERE gr_idx='".$PRDT['gr_idx']."' AND state IN('1','8') AND isTest='' AND recruit_amount >= 10000");
$GRPRDT['finished'] = sql_fetch("SELECT COUNT(idx) AS cnt, IFNULL(SUM(recruit_amount),0) AS amt FROM cf_product WHERE gr_idx='".$PRDT['gr_idx']."' AND state IN('2','5') AND isTest='' AND recruit_amount >= 10000");


// 기관등록버튼
if( in_array($PRDT['state'],array('','1')) ) {
	if($PRDT['ib_trust']=='Y') {
		$button_caption = ($PRDT['ib_product_regist']=='Y') ? '기관등록정보변경':'기관등록';
		if($PRDT['loan_mb_no']) {
			// 목표금액 모집완료시 기관등록버튼 활성화
			if($PRDT['invest_amount'] == $PRDT['recruit_amount']) {
				$ib_vact_reg_button = "<button type='button' id='btn_sh_regist' class='btn btn-danger' style='width:20%;'>".$button_caption."</button>";
			}
			else {
				$ib_vact_reg_button = "<button type='button' onClick=\"alert('투자금 모집완료 후 버튼이 활성화 됩니다.');$('#loan_mb_no').focus();\" class='btn btn-default' style='width:20%;'>".$button_caption."</button>";
			}

		}
		else {
			$ib_vact_reg_button = "<button type='button' onClick=\"alert('먼저 대출자를 등록(선택)한 후 상품정보를 저장하면 기관등록기능이 활성화 됩니다.');$('#loan_mb_no').focus();\" class='btn btn-default' style='width:20%;'>".$button_caption."</button>";
		}
	}
	else {
		$ib_vact_reg_button = "<button type='button' onClick=\"alert('신한은행연계 예치금 신탁상품으로 등록 후 저장하십시요.');$('#ib_trust').focus();\" class='btn btn-default' style='width:20%;'>기관등록</button>";
	}
}
else {
	$ib_vact_reg_button = "<button type='button' disabled onClick=\"alert('투자금 모집실패 또는 대출실행이 완료된 상품의 기관 등록용 자료의 수정은 허용하지 않습니다.');\" class='btn btn-default' style='width:20%;'>기관등록</button>";
}


//카카오페이 등록/수정/삭제 버튼 : 카카오페이에 상품을 등록하려면 상품정보를 먼저 등록하고 신디케이션정보에 카카오페이를 추가하여야 함.
$kko_reg_button = $kko_del_button = '';
if($PRDT['idx']) {
	if($PRDT['kakaopay_product_id']) {
		$kko_reg_button = "<button type='button' id='kko_reg_button' data-prd_idx='".$PRDT['idx']."' data-action='edit'   class='btn btn-warning' style='width:200px'>카카오페이 수정</button>";		// 수정버튼 : 카카오페이측 상품판매상태코드(sale_status)가 등록됨(REGISTERED)인 상품만 수정 가능
		$kko_del_button = "<button type='button' id='kko_del_button' data-prd_idx='".$PRDT['idx']."' data-action='delete' class='btn btn-danger'  style='width:200px'>카카오페이 삭제</button>";		// 삭제버튼 : 카카오페이측 상품판매상태코드(sale_status)가 등록됨(REGISTERED)이거나 QC거절(QC_REJECTED)인 상품만 삭제 가능
	}
	else {
		$kko_reg_button = "<button type='button' id='kko_reg_button' data-prd_idx='".$PRDT['idx']."' data-action='new'    class='btn btn-warning' style='width:200px'>카카오페이 등록</button>";
	}
}

// 매출채권 배열화
$res = sql_query("SELECT idx, grp_title FROM cf_auto_invest_config WHERE display='Y' ORDER BY idx");
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$AR_LIST[] = sql_fetch_array($res);
}

// 상품그룹 배열화
$res = sql_query("
	SELECT
		idx, title, repay_acct_no, ib_product_regist
	FROM
		cf_product
	WHERE 1
		AND idx=gr_idx
	  -- AND isTest=''
		AND repay_acct_no!=''
	  -- AND ib_product_regist='Y'
	  -- AND ib_loan_start='S'
	ORDER BY
		category DESC,
		start_num DESC,
		idx DESC");
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$PARENT[] = sql_fetch_array($res);
}

// 상환참조상품 배열화
if($PRDT['isRefPrdt']=='') {
	$refRes = sql_query("SELECT idx, state, title, repay_acct_no FROM cf_product WHERE isRefPrdt='1' ORDER BY idx DESC");
	while( $TMP = sql_fetch_array($refRes) ) {
		$REF_PRDT[] = $TMP;
	}
	sql_free_result($refRes);
}


$g5['title'] = '상품등록';
include_once('../admin.head.php');

add_javascript(G5_POSTCODE_JS, 0);		//다음 주소 js
add_stylesheet('<link rel="stylesheet" type="text/css" href="/investment/css/investment_info_m.css">', 1);
add_stylesheet('<link rel="stylesheet" type="text/css" href="/investment/css/investment_info.css">', 0);

?>
<div class="tbl_head02 tbl_wrap">

	<form id="product_form" name="product_form" method="post" action="product_form_update.php" enctype="multipart/form-data" class="form-horizontal">

		<input type="hidden" name="token" value="">
		<input type="hidden" name="action" value="<?=($PRDT['idx'])?'product_update':'product_insert';?>">
		<input type="hidden" name="idx" id="prd_idx" value="<?=$PRDT['idx']?>">

		<? if($PRDT['idx']=='164') { echo "<h3 style='color:red'>[정보삭제예정상품]</h3>"; } ?>

		<h3>대출정보<?=($PRDT['title'] && !$copy_idx)?": <font style='color:#FF2222'>" . $PRDT['title'] . "</font>":''?> <?if($PRDT['state']){?><a href="/adm/repayment/repay_calculate.php?&idx=<?=$PRDT['idx']?>" style="float:right;"><button type="button" class="btn btn-default">정산내역</button></a><?}?></h3>
		<table class="table-bordered table-condensed" style="min-width:1500px; margin-top:20px;">
			<colgroup>
				<col width="12%">
				<col width="38%">
				<col width="12%">
				<col width="38%">
			</colgroup>
			<tbody>

			<tr>
				<th>상품군 설정</th>
				<td>
					<ul class="col-sm-10 list-inline" onchange="cat2_onoff();">
						<li style="width:160px"><label class="radio-inline"><input type="radio" name="category" id="category2" value="2" <?=(empty($PRDT['category']) || $PRDT['category']=='2')?'checked':'';?> onClick="printScheduleMoney();">부동산</label></li>
						<li style="margin-top:8px;">
							<select name="mortgage_guarantees" id="mortgage_guarantees" class="form-control input-sm" style="width:120px" onChange="printScheduleMoney();">
								<option value="">::구분::</option>
								<option value="none" <?=($PRDT['category']=='2' && $PRDT['mortgage_guarantees']=='')?'selected':'';?>>PF</option>
								<option value="1" <?=($PRDT['category']=='2' && $PRDT['mortgage_guarantees']=='1')?'selected':'';?>>주택담보</option>
							</select>
						</li><br/>
						<li style="width:160px"><label class="radio-inline"><input type="radio" name="category" id="category3" value="3" <?=($PRDT['category']=='3')?'checked':'';?> onClick="printScheduleMoney();">매출채권(헬로페이)</label></li>
						<li style="margin-top:8px;">
							<select name="m_category2" id="m_category2" class="form-control input-sm" style="width:120px" onChange="printScheduleMoney();">
								<option value="">::구분::</option>
								<option value="1" <?=($PRDT['category']=="3" and $PRDT['category2']=='1')?'selected':'';?> >소상공인</option>
								<option value="2" <?=($PRDT['category']=="3" and $PRDT['category2']=='2')?'selected':'';?> >면세점</option>
							</select>
						</li><br/>
						<li style="width:160px"><label class="radio-inline"><input type="radio" name="category" id="category1" value="1" <?=($PRDT['category']=='1')?'checked':'';?> onClick="printScheduleMoney();">동산</label></li><br/>
					</ul>
<?
// cat2_onoff 함수용 변수 설정
$mg_default_value  = '';
$ca2_default_value = '';
if($PRDT['category']=='2') {
	$mg_default_value = ($PRDT['mortgage_guarantees']) ? $PRDT['mortgage_guarantees'] : 'none';
}
else if($PRDT['category']=='3') {
	$ca2_default_value = ($PRDT['category2']) ? $PRDT['category2'] : '';
}
?>
					<script>
					cat2_onoff = function() {
						var catValue = $('input:radio[name="category"]:checked').val();
						if(catValue==2) {
							$('#mortgage_guarantees').attr('disabled',false);																				// 부동산 하위구분 선택 활성
							<?if($mg_default_value){?>$('#mortgage_guarantees').val('<?=$mg_default_value?>').trigger('click');<?}?>
							$('#m_category2').attr('disabled',true);
							$('#m_category2').val('').trigger('click');		// 헬로페이 하위구분 선택 비활성
						}
						else if(catValue==3) {
							$('#mortgage_guarantees').attr('disabled',true);
							$('#mortgage_guarantees').val('').trigger('click');
							$('#m_category2').attr('disabled',false);
							<?if($ca2_default_value){?>$('#m_category2').val('<?=$ca2_default_value?>').trigger('click');<?}?>
						}
						else {
							$('#mortgage_guarantees').attr('disabled',true);
							$('#mortgage_guarantees').val('').trigger('click');
							$('#m_category2').attr('disabled',true);
							$('#m_category2').val('').trigger('click');
						}
					}
					$(document).ready(function() { cat2_onoff(); });
					</script>
				</td>
				<th>동일차주설정</th>
				<td>
					<select name="gr_idx" id="gr_idx" onChange="copy_product();" class="form-control input-sm">
						<option value="">::선택::</option>
						<?
							for($i=0; $i<count($PARENT); $i++) {
								if($copy_idx) {
									$selected = ($PARENT[$i]['idx']==$copy_idx) ? 'selected' : '';
								}
								else {
									$selected = ($PARENT[$i]['idx']==$PRDT['gr_idx']) ? 'selected' : '';
								}

								$regist_mark = ($PARENT[$i]['ib_product_regist']=='Y') ? '':' (기관미등록)';

								echo '<option value="'.$PARENT[$i]['idx'].'" '.$selected.'>'.$PARENT[$i]['title'].$regist_mark.'</option>' . PHP_EOL;
							}
						?>
					</select>
					<span class="help-inline" style="font-size:12px;color:brown">※ 기존 대출에서 사용된 상환용 가상계좌를 재사용 할 경우 설정하세요.</span>
					<? if($GRPRDT['repaying']['cnt'] || $GRPRDT['finished']['cnt']) { ?>
					<div style="font-size:12px;color:green">- 이자상환중인 동일차주상품 : <?=$GRPRDT['repaying']['cnt']?>건 (<?=number_format($GRPRDT['repaying']['amt'])?>원)</div>
					<div style="font-size:12px;color:green">- 동일차주상품 상환완료액 : <?=$GRPRDT['finished']['cnt']?>건 (<?=number_format($GRPRDT['finished']['amt'])?>원)</div>
					<? } ?>
				</td>
			</tr>

			<tr>
				<th>상품구분</th>
				<td height="72">
					<ul class="col-sm-10 list-inline" style="margin-bottom:10px;">
						<? if($PRDT['state']) { ?>
						<input type="hidden" id="ib_trust" name="ib_trust" value="<?=$PRDT['ib_trust']?>">
						<li><label class="checkbox-inline" style="color:#FF2222;">신한은행연계 예치금 신탁상품</label></li><br/>
						<? } else { ?>
						<li><label class="checkbox-inline" style="color:#FF2222;"><input type="checkbox" id="ib_trust" name="ib_trust" value="Y" <?=($PRDT['ib_trust']=='Y')?'checked':'';?> />신한은행연계 예치금 신탁상품</label></li><br/>
						<? } ?>
						<li><label class="checkbox-inline"><input type="checkbox" name="purchase_guarantees" value="Y" <?=($PRDT['purchase_guarantees']=='Y')?'checked':'';?> />채권매입보증</label></li>
						<li><label class="checkbox-inline"><input type="checkbox" name="portfolio" value="Y" <?=($PRDT['portfolio']=='Y')?'checked':'';?> />포트폴리오상품</label></li>
						<li><label class="checkbox-inline"><input type="checkbox" name="advanced_payment" value="Y" <?=($PRDT['advanced_payment']=='Y')?'checked':'';?> />이자선지급</label></li><br/>
						<li><label class="checkbox-inline"><input type="checkbox" id="ai_flag" onClick="selectUsable()" <?=($PRDT['ai_grp_idx'])?'checked':'';?> />자동투자상품</label></li>
						<li style="height:16px;">
							<div id="ai_zone" style="float:left; position:absolute; z-index:2;">
								<select id="ai_grp_idx" name="ai_grp_idx" class="form-control input-sm" onChange="showDetailInfo();" onFocus="openSelectList();" onBlur="closeSelectList();hideDetailInfo();" style="height:auto;">
									<option value="">::자동투자그룹선택::</option>
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
					<div id="auto_inv_passb_amt" style="margin-top:110px;margin-left:10px; cursor:pointer;" onclick='view_auto_list()'></div>

				<script type="text/javascript">
					selectUsable = function() {
						if( $('input:checkbox[id="ai_flag"]').is(':checked')==true ) {
							$('#ai_grp_idx').removeAttr('disabled');
						}
						else {
							$('#ai_grp_idx').attr('disabled','true');
						}
					};
					$(document).ready(function(){ selectUsable(); });

					openSelectList = function() {
						var obj = document.getElementById("ai_grp_idx");
						var n = obj.options.length;
						obj.size = n;
					};
					closeSelectList = function() {
						var obj = document.getElementById("ai_grp_idx");
						obj.size = 1;
					};


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
						check_auto_inv();
					};

					hideDetailInfo = function() {
						$('#ai_grp_detail_zone').hide();
					}
				</script>

				</td>
				<th>프론트노출플래그</th>
				<td colspan="3">
					<ul class="col-sm-10 list-inline">
						<li><label class="checkbox-inline"><input type="checkbox" name="success_example" value="Y" <?=($PRDT['success_example']=='Y')?'checked':'';?>>투자성공사례(투자성공상품 목록에 출력)</label></li>
						<li><label class="checkbox-inline"><input type="checkbox" name="popular_goods" value="Y" <?=($PRDT['popular_goods']=='Y')?'checked':'';?>>인기상품</label></li><br/>
						<li><label class="checkbox-inline"><input type="checkbox" name="isConsor" value="1" <?=($PRDT['isConsor']=='1')?'checked':'';?>>컨소시엄 상품</label></li><br/>
						<li style="margin-left:20px;">컨소시엄 업체</li>
						<li><input type="text" name="consor_co" value="<?=$PRDT['consor_co']?>" class="form-control input-sm" style="min-width:250px;"></li>
					</ul>
				</td>
			</tr>

			<tr>
				<th>노출설정</th>
				<td>
					<ul class="col-sm-10 list-inline">
						<li>
							<label class="radio-inline"><input type="radio" id="displayY" name="display" onClick="isTestSetup();" value="Y" <?=($PRDT['display'] == 'Y') ? 'checked' : ''; ?>>노출</label><br/>
							<label class="radio-inline"><input type="radio" id="displayN" name="display" onClick="isTestSetup();" value="N" <?=(in_array($PRDT['display'],array('','N'))) ? 'checked' : ''; ?>>비노출</label> &nbsp;&nbsp;&nbsp;
							<label class="checkbox-inline"><input type="checkbox" id="isTest" name="isTest" value="1" <?=($PRDT['idx']=='' || $PRDT['isTest'])?'checked':''?>> 테스트용</label>
						</li>
					</ul>
					<script>
					function isTestSetup() {
						if( $('input:radio[id=displayN]').is(':checked') ) {
							$('input:checkbox[name=isTest]').attr('disabled', false);
						}
						else {
							$('input:checkbox[name=isTest]').attr('disabled', true);
						}
					}
					$(document).ready(function() { isTestSetup(); });
					</script>
				</td>
				<th>외부스크랩핑</th>
				<td>
					<ul class="col-sm-10 list-inline">
						<li><label class="radio-inline"><input type="radio" name="scrap_out" onClick="scrapYn();" value="" <?=($PRDT['scrap_out']=='')?'checked':'';?>>허용</label></li>
						<li><label class="radio-inline"><input type="radio" name="scrap_out" onClick="scrapYn();" value="1" <?=($PRDT['scrap_out']=='1')?'checked':'';?>>불가</label></li><br/>
						<li style="text-align:left;">
<?
	//if( !preg_match("/chosun/", $PRDT['platform']) ) unset($CONF['SYNDICATOR']['chosun']);	// 신디케이션 리스트에서 땅집고 제외
	$scount = count($CONF['SYNDICATOR']);
	$skey = array_keys($CONF['SYNDICATOR']);
	for($i=0,$j=1; $i<$scount; $i++,$j++) {
		$checked = ( @in_array($skey[$i], $PLATFORM) ) ? "checked" : "";
		$disabled = ( $CONF['SYNDICATOR'][$skey[$i]]['disabled'] ) ? 'disabled' : '';
		echo "							<label class='checkbox-inline flatform_checkbox'><input type='checkbox' id='platform{$i}' name='PLATFORM[]' value='".$skey[$i]."' {$checked} {$disabled}>" . $CONF['SYNDICATOR'][$skey[$i]]['name']."</label>";
		if($j%4==0) echo "<br/>\n";

		if($checked && $skey[$i]=="kakaopay") $prd_kakao = "Y";

	}
	//if($prd_kakao=="Y") echo "<br/><button type='button' class='btn btn-success' style='width:150px;margin-top:10px;color:black;background-color:#CDE4CE;border-color:#CDE4CE;' onclick='go_view_kakao();'>kakaopay 상품정보</button>\n";
?>
						</li>
					</ul>
					<script>
					function scrapYn() {
						_value = $('input:radio[name="scrap_out"]:checked').val();
						if(_value=='1') {
							$('input:checkbox[name="PLATFORM[]"]').each(function() {
								$('input:checkbox[name="PLATFORM[]"]').attr('disabled', true);
								$('.flatform_checkbox').css('color','#AAA');
							});
						}
						else {
							$('input:checkbox[name="PLATFORM[]"]').each(function() {
								$('input:checkbox[name="PLATFORM[]"]').attr('disabled', false);
								$('.flatform_checkbox').css('color','');
							});
						}
					}

					function go_view_kakao() {
						var prd_idx = '<?=$idx?>';
						w_kakao_prd(prd_idx);
					}

					function w_kakao_prd(prd_idx) {
						var w = window.open('/adm/product/kakaopay_prd_info.php?prd_idx=' + prd_idx, '_blank', 'width=600,height=500');
					}
					</script>
				</td>
			</tr>

			<tr>
				<th>특별구분</th>
				<td>
					<ul class="col-sm-10 list-inline" style="width:708px;">
						<li><label class="checkbox-inline" style="color:#3333FF"><input type="checkbox" id="only_vip" name="only_vip" value="1" <?=($PRDT['only_vip']=='1')?'checked':'';?> />법인전용상품</label></li>
						<li style="margin-left:20px;">투자자 번호</li>
						<li><input type="text" name="vip_mb_no" value="<?=$PRDT['vip_mb_no']?>" class="form-control input-sm" style="min-width:250px;"></li>
						<li>쉼표(,)로 구분</li>
					</ul>
				</td>
				<th>LTV</th>
				<td style="vertical-align:middle;">
					<input type="text" name="ltv" value="<?=$PRDT['ltv']?>" class="form-control" style="display:inline; width:80px; text-align:right;" placeholder="0.00"> %
				</td>
			</tr>

			<tr>
				<th style="background:#FFE4B9">신탁설정</th>
				<td colspan="3">
					<table class="table-condensed table-bordered">
						<colgroup>
							<col width="12%">
							<col width="38%">
							<col width="12%">
							<col width="38%">
						</colgroup>
						<tr>
							<th rowspan="2" style="background:#FFE4B9">대출자</th>
							<td rowspan="2" >
								<input type="hidden" id="loan_mb_no" name="loan_mb_no" value="<?=$PRDT['loan_mb_no']?>">
								<select id="sel_loan_mb_no" class="form-control input-sm" style="width:400px">
									<option value="">::대출회원 선택::</option>
<?
	$resx = sql_query("
		SELECT
			A.mb_no, A.mb_id, A.mb_co_name, A.mb_name, A.member_type,
			B.acct_no
		FROM
			g5_member A
		LEFT JOIN
			IB_vact_hellocrowd B  ON A.mb_no=B.CUST_ID
		WHERE 1
			AND A.member_group='L'
			AND B.acct_st='1'
		ORDER BY
			A.mb_co_name, A.mb_no DESC");
	while($LOANER = sql_fetch_array($resx)) {
		$selected = ($PRDT['loan_mb_no']==$LOANER['mb_no'] && $LOANER['acct_no']==$PRDT['repay_acct_no']) ? 'selected' : '';
		$print_loaner_name = ($LOANER['member_type']=='2') ? $LOANER['mb_co_name'] : $LOANER['mb_name'];

		$value = NULL;
		$value = $LOANER['mb_no'] . ":*" . $LOANER['acct_no'];
		echo '<option value="'.$value.'" '.$selected.'>'.$print_loaner_name.' ('.$LOANER['mb_no'].' / '.$LOANER['mb_id'].')</option>' . PHP_EOL;
	}
?>
								</select>
							</td>
							<th style="background:#FFE4B9">원리금상환계좌(가상)</th>
							<td>
								<ul class="col-sm-10 list-inline" style="margin:0;padding:0">
									<li><input type="text" id="repay_acct_no" name="repay_acct_no" value="<?=$PRDT['repay_acct_no']?>" class="form-control input-sm" style="width:200px"></li>
									<li style="padding-left:8px"><label class="checkbox-inline"><input type="checkbox" id="isRefPrdt" name="isRefPrdt" value="1" <?=($PRDT['isRefPrdt']=='1')?'checked':''?>> 타상품 상환참조계좌로 선정</label></li>
								</ul>
							</td>
						</tr>
						<tr>
							<th style="background:#FFE4B9"><? if($PRDT['isRefPrdt']=='') { ?>상환참조용상품<? } ?></th>
							<td>
<? if(count($REF_PRDT)) { ?>
								<select id="ref_prdt_idx" name="ref_prdt_idx" class="form-control input-sm">
									<option value="">:: 상환참조계좌 선택 ::</option>
<?
	for($i=0; $i<count($REF_PRDT); $i++) {
		$selected = ($PRDT['ref_prdt_idx']==$REF_PRDT[$i]['idx']) ? 'selected' : '';
		$print_title = $REF_PRDT[$i]['idx'].": ".$REF_PRDT[$i]['title']." / ".$REF_PRDT[$i]['repay_acct_no']."";
		echo "<option value='".$REF_PRDT[$i]['idx']."' $selected>".$print_title."</option>\n";
	}
?>
								</select>
<? } ?>
							</td>
						</tr>

<script>
$('#sel_loan_mb_no').on('change', function() {

	_value = $('#sel_loan_mb_no').val();
	arr = _value.split(':*');
	_loan_mb_no = arr[0];
	_repay_acct_no = arr[1];

	$('#loan_mb_no').val(_loan_mb_no);
	$('#repay_acct_no').val(_repay_acct_no);

});
</script>
						<tr>
							<th style="background:#FFE4B9">대출금입금계좌</th>
							<td colspan="3" style="padding:2px">
								<?
									$BANK_KEYS = array_keys($BANK);
									for($i=0,$j=1; $i<5; $i++,$j++) {
								?>
								<ul class="list-inline" style="margin:0 0 4px;">
									<li>
										<select id="loan_dep_bank_cd<?=$j?>" name="loan_dep_bank_cd<?=$j?>" class="form-control input-sm">
											<option value="">::은행선택::</option>
											<?
											for($x=0; $x<count($BANK); $x++) {
												$selected = ($PRDT['loan_dep_bank_cd'.$j]==$BANK_KEYS[$x]) ? 'selected' : '';
												echo '<option value="'.$BANK_KEYS[$x].'" '.$selected.'>'.$BANK[$BANK_KEYS[$x]].'</option>' . PHP_EOL;
											}
											?>
										</select>
									</li>
									<li><input type="text" id="loan_dep_acct_nb<?=$j?>" name="loan_dep_acct_nb<?=$j?>" value="<?=$PRDT['loan_dep_acct_nb'.$j]?>" placeholder="계좌번호" onKeyUp="onlyDigit(this);" class="form-control input-sm"></li>
									<li><input type="text" id="loan_dep_amt<?=$j?>" name="loan_dep_amt<?=$j?>" value="<?=$PRDT['loan_dep_amt'.$j]?>" placeholder="금액" onKeyUp="onlyDigit(this);" class="form-control input-sm"></li>
									<li style="padding-left:0">원 &nbsp;&nbsp;</li>
									<li><input type="text" id="loan_dep_acct_memo<?=$j?>" name="loan_dep_acct_memo<?=$j?>" value="<?=$PRDT['loan_dep_acct_memo'.$j]?>" placeholder="계좌용도" class="form-control input-sm" style="width:200px"></li>
									<li style="vertical-align:top;">
										<button type="button" class="btn btn-primary btn-sm" onclick="go_sh_srch_acc(document.product_form.loan_dep_bank_cd<?=$j?>.value , document.product_form.loan_dep_acct_nb<?=$j?>.value , <?=$j?>);">계좌조회</button>
										&nbsp;&nbsp;<span id="loan_dep_acct_nm1[<?=$j?>]"></span>
									</li>
								</ul>
								<?
									}
								?>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th>상품명</th>
				<td colspan="3">
					<input type="text" name="title" value="<?=$PRDT['title']?>" style="width:800px;display:inline;" class="form-control input-sm" required="required">
					<input type='button' class='btn btn-sm btn-default' value='호번중복체크' onclick="chk_hobun(document.product_form.title.value);" />
					<label class="checkbox-inline" style="margin-left:20px;"><input type="checkbox" name="isEtcCost" id="isEtcCost" value="1" <?=($PRDT['isEtcCost']=='1')?'checked':'';?>>기타비용처리용상품</label>
				</td>
			</tr>

			<tr>
				<th>모집목표금액</th>
				<td style="padding-left:2px">
					<ul class="list-inline" style="margin:0;">
						<li><input type="text" name="recruit_amount" value="<?=$PRDT['recruit_amount']?>" class="form-control input-sm" required="required"></li>
						<li><span id="number_format"><?=number_format($PRDT['recruit_amount'])?></span>원</li>
					</ul>
				</td>
				<th>투자기간</th>
				<td style="padding-left:2px">
					<ul class="list-inline" style="margin:0;float:left;">
						<li>
							<select id="invest_period" name="invest_period" class="form-control input-sm" style="width:200px" required onChange="inpAct();">
								<option value="">::투자기간선택::</option>
								<option value="under1month" <?=($PRDT['invest_period']==1 && $PRDT['invest_days']>0)?'selected':''?>>1개월 미만</option>
								<?
									for($m=1; $m<=120; $m++) {
										if( $m <= '24' || in_array($m, array('30','36','48','60','72','84','96','108','120')) ) {
											$selected = ($PRDT['invest_period']==$m && $PRDT['invest_days']==0) ? 'selected' : '';
											$print_reriod = $m.'개월';
											if($m%12==0) $print_reriod.= " (". ($m/12) ."년)";
											echo '<option value="'.$m.'" '.$selected.'>'.$print_reriod.'</option>' . PHP_EOL;
										}
									}
								?>
							</select>
						</li>
					</ul>
					<ul id="invest_days_zone" class="list-inline" style="margin:0;float:left;">
						<li>→</li>
						<li><input type="text" id="invest_days" name="invest_days" value="<?=$PRDT['invest_days']?>" class="form-control input-sm" maxlength="2" style="width:50px;text-align:right" onKeyup="onlyDigit(this);"></li>
						<li style="padding-left:0">일</li>
					</ul>
					<ul class="list-inline" style="margin:0;float:left;">
						<li><select id="calc_type" name="calc_type" class="form-control input-sm" style="width:200px">
								<option value="1" <?if($PRDT['calc_type']=='' || $PRDT['calc_type']=='1')echo'selected';?>>초일산입(말일불산입)</option>
								<option value="2" <?if($PRDT['calc_type']=='2')echo'selected';?>>말일산입(초일불산입)</option>
							</select>
						</li>
					</ul>
				</td>
			</tr>

			<script type="text/javascript">
				inpAct = function() {
					var sval = $('select[id="invest_period"]').val();
					dpval = (sval=='under1month') ? 'block' : 'none';
					$('#invest_days_zone').css('display', dpval);
				};
				$(document).ready(function(){ inpAct(); });
			</script>

			<tr>
				<th>연 이율</th>
				<td colspan="3"><table>
						<colgroup>
							<col width="12%">
							<col width="38%">
							<col width="12%">
							<col width="38%">
						</colgroup>
						<tr>
							<th>투자수익률</th>
							<td>
								<ul class="list-inline" style="margin:0;">
									<li>연</li>
									<li><input type="text" name="invest_return" value="<?=floatRtrim($PRDT['invest_return'])?>" class="form-control input-sm" style="width:80px" required="required"></li>
									<li>%</li>
								</ul>
							</td>
							<th>대출이율</th>
							<td>
								<ul class="list-inline" style="margin:0;">
									<li>연</li>
									<li><input type="text" name="loan_interest_rate" value="<?=floatRtrim($PRDT['loan_interest_rate'])?>" class="form-control input-sm" style="width:80px" required="required"></li>
									<li>%</li>
								</ul>
							</td>
						</tr>
						<tr>
							<th>원천징수율</th>
							<td style="padding-left:2px">
								<ul class="list-inline" style="margin:0;">
									<li style="font-size:13px">
										개인 : 이자소득세 <?=$CONF['indi']['interest_tax_ratio']*100?>% + 지방세 <?=$CONF['indi']['interest_tax_ratio']*10?>%<br/>
										법인 : 이자소득세 <?=$CONF['corp']['interest_tax_ratio']*100?>% + 지방세 <?=$CONF['corp']['interest_tax_ratio']*10?>%
									</li>
									<!--<li><input type="text" name="withhold_tax_rate" value="27.5" readonly class="form-control input-sm" style="width:80px"></li>-->
								</ul>
							</td>
							<th>연체이자</th>
							<td>
								<ul class="list-inline" style="margin:0;">
									<li>연</li>
									<li><input type="text" name="overdue_rate" value="<?=($PRDT['overdue_rate'])?floatRtrim($PRDT['overdue_rate']):'24';?>" class="form-control input-sm" style="width:80px"></li>
									<li>%</li>
								</ul>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th>투자자 플랫폼이용료</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li>연</li>
						<li><input type="text" name="invest_usefee" value="<?=($PRDT['invest_usefee']) ? floatRtrim($PRDT['invest_usefee']) : '1.2'; ?>" class="form-control input-sm" style="width:80px" required="required"></li>
						<li>%</li>
						<li><span class="help-block" style="color:brown;font-size:12px">((투자금 x 설정요율 / 100) / <?=$dayCountOfYear?>) x 투자일수 &nbsp; (소수점 이하 절사)</span></li>
					</ul>
				</td>
				<th>징수방식</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li>
							<label class="radio-inline"><input type="radio" name="invest_usefee_type" value="A" <?=(empty($PRDT['invest_usefee_type']) || $PRDT['invest_usefee_type']=='A')?'checked':'';?>> 월별분할징수</label><br/>
							<label class="radio-inline"><input type="radio" name="invest_usefee_type" value="B" <?=($PRDT['invest_usefee_type']=='B')?'checked':'';?> disabled="disabled"> 만기일시징수</label>
						</li>
					</ul>
				</td>
			</tr>

			<tr>
				<th>대출자 이자징수</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li>
							<label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type0" value="0" <?=(empty($PRDT['loan_interest_type']) || $PRDT['loan_interest_type']=='0')?'checked':''?>> 월이자</label><br/>
							<label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type1" value="1" <?=($PRDT['loan_interest_type']=='1')?'checked':''?>> 선이자</label><br/>
							<label class="radio-inline"><input type="radio" name="loan_interest_type" id="loan_interest_type2" value="2" <?=($PRDT['loan_interest_type']=='2')?'checked':''?>> 부분선이자</label><br/>
						</li>
						<li><input type="text" id="bb" name="loan_advanced_count" onKeyUp="onlyDigit(this)" value="<?=$PRDT['loan_advanced_count']?>" class="form-control input-sm" style="width:50px;text-align:right"></li>
						<li>회차까지</li>
					</ul>
				</td>
				<th>상환방식</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li>
							<label class="radio-inline"><input type="radio" name="repay_type" value="1" <? echo (empty($PRDT['repay_type']) || $PRDT['repay_type']=='1') ? 'checked' : ''; ?>>원금 만기일시상환</label><br/>
							<label class="radio-inline" style="color:#CCC;"><input type="radio" name="repay_type" value="2" <? echo ($PRDT['repay_type']=='2') ? 'checked' : ''; ?> disabled>원리금 균등상환</label><br/>
							<label class="radio-inline" style="color:#CCC;"><input type="radio" name="repay_type" value="3" <? echo ($PRDT['repay_type']=='3') ? 'checked' : ''; ?> disabled>원금 균등상환</label><br/>
							<!--<label class="radio-inline" style="color:#CCC;"><input type="radio" name="repay_type" value="4" <? echo ($PRDT['repay_type']=='4') ? 'checked' : ''; ?> disabled>원리금 만기일시상환</label>-->
						</li>
					</ul>
				</td>
			</tr>

			<tr>
				<th>대출자 플랫폼이용료</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li><input type="text" name="loan_usefee" value="<?=floatRtrim($PRDT['loan_usefee']);?>" onKeyUp='printScheduleMoney();' class="form-control input-sm" style="width:80px" required="required"></li>
						<li>%</li>
						<li id="calculationFormula" style="margin-left:20px; font-size:12px;color:brown"></li>
						<br/>
						<li style="margin-top:8px;">
							<table class="table-bordered" style="font-size:12px">
								<tr align="center" style="background:#F8F8EF">
									<td style="padding:2px;">상품군</td>
									<td style="min-width:200px;padding:2px;">산출방식(총액)</td>
								</tr>
								<tr align="center">
									<td align="left" style="padding:2px 10px;">부동산 > PF</td>
									<td style="padding:2px 10px;" id="C2">대출금 x 설정요율 / 100</td>
								</tr>
								<tr align="center">
									<td align="left" style="padding:2px 10px;">부동산 > 주택담보</td>
									<td style="padding:2px 10px;" id="C21">대출금 x 설정요율 / 100</td>
								</tr>
								<tr align="center">
									<td align="left" style="padding:2px 10px;">매출채권 > 소상공인</td>
									<td style="padding:2px 10px;" id="C31">((대출금 x 설정요율 / 100) / 365일) x 투자일수</td>
								</tr>
								<tr align="center">
									<td align="left" style="padding:2px 10px;">매출채권 > 면세점</td>
									<td style="padding:2px 10px;" id="C32">대출금 x 설정요율 / 100</td>
								</tr>
								<tr align="center">
									<td align="left" style="padding:2px 10px;">동산</td>
									<td style="padding:2px 10px;" id="C1">대출금 x 설정요율 / 100</td>
								</tr>
							</table>
						</li>
					</ul>
				</td>
				<th rowspan="2">징수방식</th>
				<td rowspan="2">
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li>
							<label class="radio-inline"><input type="radio" name="loan_usefee_type" value="B" <?=($PRDT['loan_usefee_type']=='B')?'checked':'';?> onClick="loanFeeCntView();printScheduleMoney();"> 선취(일시징수)</label><br/>
							<label class="radio-inline"><input type="radio" name="loan_usefee_type" value="A" <?=($PRDT['loan_usefee_type']=='A')?'checked':'';?> onClick="loanFeeCntView();printScheduleMoney();"> 후취(월별분할징수)</label>
						</li>
						<li><input type="text" id="loan_usefee_repay_count" name="loan_usefee_repay_count" value="<?=$PRDT['loan_usefee_repay_count']?>" onClick="onlyDigit(this);" onKeyUp='printScheduleMoney();' class="form-control input-sm" style="width:50px;text-align:right"></li>
						<li>회 분납</li><br/>
						<li style="margin-top:20px;">
							<table class="table-bordered" style="width:400px; font-size:12px">
								<tr style="background:#F8F8EF">
									<td colspan="4" align="center" style="padding:2px;">대출자 징수 내역</td>
								</tr>
								<tr align="center" style="background:#F8F8EF">
									<td style="width:25%;padding:2px;">플랫폼이용료</td>
									<td style="width:25%;padding:2px;">중개수수료</td>
									<td style="width:25%;padding:2px;">합계</td>
									<td style="padding:2px;">납입회수</td>
								</tr>
								<tr align="right">
									<td style="padding:2px 4px;" id="print_loan_usefee_amt">&nbsp</td>
									<td style="padding:2px 4px;" id="print_commission_fee_amt"></td>
									<td style="padding:2px 4px;" id="print_fee_sum_amt"></td>
									<td style="padding:2px 4px;" id="print_fee_repay_count" align="center"></td>
								</tr>
							</table>
						</li>
					</ul>
					<script>
					loanFeeCntView = function() {
						$('input:radio[name="loan_usefee_type"]').each(function() {
							if(this.checked){		//checked 처리된 항목의 값
								if(this.value=='A') {
									$('#loan_fee_cnt_area').css('display', 'block');
									$('#loan_usefee_repay_count').attr('disabled', false);
								}
								else {
									$('#loan_fee_cnt_area').css('display', 'none');
									$('#loan_usefee_repay_count').attr('disabled', true);
								}
							}
						});
					}
					$(document).ready(function(){ loanFeeCntView(); });
					</script>
				</td>
			</tr>
			<tr>
				<th>중개자 설정</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin:0">
						<li style="margin-bottom:4px;width:70px;">중개자</li>
						<li style="margin-bottom:4px;"><input type="text" name="broker" value="<?=$PRDT['broker'];?>" class="form-control input-sm" style="width:150px"></li><br/>
						<li style="margin-bottom:4px;width:70px;">수수료율</li>
						<li style="margin-bottom:4px;"><input type="text" name="commission_fee" value="<?=floatRtrim($PRDT['commission_fee']);?>" class="form-control input-sm" style="width:80px"></li><li>%</li><br/>
						<li style="width:70px;">접수자</li>
						<li><input type="text" name="receiver" value="<?=$PRDT['receiver'];?>" class="form-control input-sm" style="width:150px"></li>
					</ul>
				</td>
			</tr>

			<script>
			printScheduleMoney = function() {

				var dailyCalc = false;		// 일할계산여부(소상공인확정매출채권 상품)
				var catValue = $('input:radio[name=category]:checked').val();
				if(catValue == '3') {
					var cat2Value = $('select[name=m_category2]').val();
					if( cat2Value=='1' ) dailyCalc = true;
				}

				recruit_amount = ( $('input[name=recruit_amount]').val() > 0 ) ? $('input[name=recruit_amount]').val() : 0;
				loan_usefee    = ( $('input[name=loan_usefee]').val() > 0 ) ? $('input[name=loan_usefee]').val() : 0;
				commission_fee = ( $('input[name=commission_fee]').val() > 0 ) ? $('input[name=commission_fee]').val() : 0;

				loan_usefee_repay_count = 0;
				if( $('input:radio[name=loan_usefee_type]:checked').val()=='A' ) {
					// 후취
					loan_usefee_repay_count = ( $('input[name=loan_usefee_repay_count]').val() > 0 ) ? $('input[name=loan_usefee_repay_count]').val() : 1;
				}
				else if( $('input:radio[name=loan_usefee_type]:checked').val()=='B' ) {
					// 선취
					loan_usefee_repay_count = 1;
				}


				if(dailyCalc) {
					var day_count_of_year = <?=$dayCountOfYear?>;
					var repay_day_count = <?=($repayDayCount) ? $repayDayCount : 0;?>;
					loan_usefee_amt = ((recruit_amount * loan_usefee / 100 ) / day_count_of_year) * repay_day_count;

					calculationFormula = '((' + recruit_amount + ' * ' + loan_usefee + ' / 100 ) / ' + day_count_of_year + ') * ' + repay_day_count + ' (소수점 이하 절사)';
					$('#calculationFormula').html(calculationFormula);
				}
				else {
					loan_usefee_amt = recruit_amount * loan_usefee / 100;

					calculationFormula = recruit_amount + ' * ' + loan_usefee + ' / 100 (소수점 이하 절사)';
					$('#calculationFormula').html(calculationFormula);
				}

				commission_fee_amt = recruit_amount * commission_fee / 100;
				fee_sum_amt        = loan_usefee_amt + commission_fee_amt;

				print_loan_usefee_amt    = (loan_usefee_amt > 0) ? number_format(Math.floor(loan_usefee_amt)) : 0;
				print_commission_fee_amt = (commission_fee_amt > 0) ? number_format(Math.floor(commission_fee_amt)) : 0;
				print_fee_sum_amt        = (fee_sum_amt > 0) ? number_format(Math.floor(fee_sum_amt)) : 0;

				$('#print_loan_usefee_amt').html(print_loan_usefee_amt + '원');
				$('#print_commission_fee_amt').html(print_commission_fee_amt + '원');
				$('#print_fee_sum_amt').html(print_fee_sum_amt + '원');
				$('#print_fee_repay_count').html(loan_usefee_repay_count + '회');

			}

			$(document).ready(function() {
				setTimeout(function() { printScheduleMoney(); }, 500);
			});
			</script>


			<tr>
				<th>중도인출</th>
				<td>
					<ul class="col-sm-10 list-inline" style="margin-bottom:0;">
						<li><label class="radio-inline"><input type="radio" name="middle_withdraw_state" value="1" <?=($PRDT['middle_withdraw_state']=='1') ? 'checked' : ''; ?> disabled> 가능</label></li>
						<li><label class="radio-inline"><input type="radio" name="middle_withdraw_state" value="2" <?=(empty($PRDT['middle_withdraw_state']) || $PRDT['middle_withdraw_state']=='2') ? 'checked' : ''; ?>>불가능</label></li>
					</ul>
				</td>
				<th>중도인출 수수료</th>
				<td>
					<ul class="list-inline" style="margin:0;">
						<li><input type="text" name="middle_withdraw_charge" id="middle_withdraw_charge" value="<?=floatRtrim($PRDT['middle_withdraw_charge'])?>" class="form-control input-sm" style="width:80px"></li>
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
				<td>
					<ul class="list-inline" style="margin:0;">
						<li><input type="text" name="advance_invest_ratio" id="advance_invest_ratio" value="<?=floatRtrim($PRDT['advance_invest_ratio'])?>" class="form-control input-sm" style="width:80px"></li>
						<li>%</li>
					</ul>
				</td>
			</tr>

			<tr>
				<th>모집기간</th>
				<td colspan="3">
					<ul class="col-sm-10 list-inline" style="margin:0;">
						<li><input type="text" name="recruit_period_start" value="<?=$PRDT['recruit_period_start']?>" class="form-control input-sm datepicker" required="required"></li>
						<li>~</li>
						<li><input type="text" name="recruit_period_end" value="<?=$PRDT['recruit_period_end']?>" class="form-control input-sm datepicker" required="required"></li>
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
							<th style="background:#FFD9E4;color:brown">상품공개</th>
							<td style="padding-left:2px">
								<ul class="list-inline" style="margin:0;">
									<li><input type="text" name="open_date" value="<?=$PRDT['open_date']?>" class="form-control input-sm datepicker"></li>
									<li><input type="text" name="open_hour" value="<?=sprintf('%02d', $PRDT['open_hour'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>시</li>
									<li><input type="text" name="open_minute" value="<?=sprintf('%02d', $PRDT['open_minute'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>분</li>
									<li><input type="text" name="open_second" value="<?=sprintf('%02d', $PRDT['open_second'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>초</li>
									<li></li>
									<li style="color:red">※ 상품정렬기준인자</li>
								</ul>
								<div class="help-block" style="display:none;">※ 상품 등록이 되어 상품 목록에 노출은 되나 실투자는 되지 않음. 사전투자 가능 상품의 경우 투자 가능.</div>
							</td>
						</tr>
						<tr>
							<th>모집시작</th>
							<td style="padding-left:2px">
								<ul class="list-inline" style="margin:0;">
									<li><input type="text" name="start_date" value="<?=$PRDT['start_date']?>" class="form-control input-sm datepicker"></li>
									<li><input type="text" name="start_hour" value="<?=sprintf('%02d', $PRDT['start_hour'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>시</li>
									<li><input type="text" name="start_minute" value="<?=sprintf('%02d', $PRDT['start_minute'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>분</li>
									<li><input type="text" name="start_second" value="<?=sprintf('%02d', $PRDT['start_second'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>초</li>
								</ul>
								<div class="help-block" style="display:none;">※ 실제 투자 시작 시점입니다.</div>
							</td>
						</tr>
						<tr>
							<th>모집마감</th>
							<td style="padding-left:2px">
								<ul class="list-inline" style="margin:0;">
									<li><input type="text" name="end_date" value="<?=$PRDT['end_date']?>" class="form-control input-sm datepicker"></li>
									<li><input type="text" name="end_hour" value="<?=sprintf('%02d', $PRDT['end_hour'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>시</li>
									<li><input type="text" name="end_minute" value="<?=sprintf('%02d', $PRDT['end_minute'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>분</li>
									<li><input type="text" name="end_second" value="<?=sprintf('%02d', $PRDT['end_second'])?>" class="form-control input-sm" style="width:60px" maxlength="2"></li><li>초</li>
								</ul>
								<div class="help-block" style="display:none;">※ 투자 마감 일자 시점입니다.</div>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			</tbody>
		</table>

		<br/>

		<h3>상품정보</h3>

		<table class="table-bordered table-condensed" style="min-width:1200px">
			<colgroup>
				<col width="12%">
				<col width="88%">
			</colgroup>
			<tbody>
			<tr>
				<th>이미지</th>
				<td>
					<table class="table-condensed table-bordered">
						<colgroup>
							<col width="12%">
							<col width="88%">
						</colgroup>
						<tr>
							<th>대표이미지</th>
							<td>
								<div class="pull-left">
									<input type="file" name="main_image" accept="image/*">
								</div>

								<? if ($PRDT['main_image']) { ?>
								<input type="hidden" name="main_image_ori" value="<?=$PRDT['main_image']?>"/>
								<div class="btn btn-default pull-right btn-sm">
									<a href="<? echo G5_DATA_URL.'/product/'.$PRDT['main_image']?>" target="_blank">이미지보기</a>
								</div>
								<? } ?>
							</td>
						</tr>
						<tr>
							<th>모바일 롤링 이미지</th>
							<td>
								<div class="pull-left">
									<input type="file" name="main_image_m" accept="image/*">
								</div>
								<div class="btn btn-default pull-right btn-sm">
									<? if ($PRDT['main_image_m']) { ?>
										<input type="hidden" name="main_image_m_ori" value="<?=$PRDT['main_image_m']?>"/>
										<a href="<? echo G5_DATA_URL.'/product/'.$PRDT['main_image_m']?>" target="_blank">이미지보기</a>
									<? } ?>
								</div>
							</td>
						</tr>
						<tr>
							<th>상세정보 이미지</th>
							<td>
								<select id="detail_image" name="detail_image[]" class="form-control input-sm" multiple="multiple">
								<?
									foreach ((array)explode('|', $PRDT['detail_image']) as $key => $val) {
										if (!$val) {
											continue;
										}
										echo "<option value='".$val."'>".$val."</option>\n";
									}
								?>
								</select>
								<input type="hidden" name="detail_image_ori" value="<?=$val?>"/>
								<div class="help-block">
									<button type="button" class="btn btn-primary btn-sm" onclick="uploadImage('detail_image');">업로드</button>
									<button type="button" class="btn btn-danger btn-sm" onclick="deleteImage('detail_image');">선택삭제</button>
								</div>
								<span style="font-size:12px;color:#aaa">권장(569 x 442)</span>
							</td>
						</tr>
						<tr>
							<th>카카오 로드뷰</th>
							<td>
								<input type="text" name="loadview_url" value="<?=$PRDT['loadview_url']?>" class="form-control input-sm" style="width:700px;"/>
								<span class="help-block">※ <a href="https://map.kakao.com" target="_blank">카카오 로드뷰</a> 공유 URL 주소를 입력하세요.</span>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th>현장카메라</th>
				<td>
					<table class="table-condensed table-bordered">
						<colgroup>
							<col width="12%">
							<col width="38%">
							<col width="12%">
							<col width="38%">
						</colgroup>
						<tr>
							<th>URL1</th>
							<td>
								<input type="text" name="stream_url1" value="<?=$PRDT['stream_url1']?>" class="form-control input-sm">
								<span class="help-block">※ ready 입력시 라이브버튼 활성화, 클릭시 안내팝업 오픈</span>
							</td>
							<th>URL2</th>
							<td>
								<input type="text" name="stream_url2" value="<?=$PRDT['stream_url2']?>" class="form-control input-sm">
								<span class="help-block">※ ready 입력시 라이브버튼 활성화, 클릭시 안내팝업 오픈</span>
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
				<td>
					<table class="table table-condensed table-bordered">
						<colgroup>
							<col width="12%">
							<col width="88%">
						</colgroup>
						<tr>
							<th>PD</th>
							<td>
								<textarea class="form-control input-sm" style="font-size:11px;height:78px" readonly>
								설치전: <img src="/images/investment/live_ban01.gif" width="100%" onClick="openStreamReady()" style="cursor:pointer;">
								설치후: <img src="/images/investment/live_ban01.gif" width="100%" onClick="window.open('<?=$play_url;?>','stream_win','width=730,height=500,toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no');" style="cursor:pointer;"></textarea>
							</td>
						</tr>
						<tr>
							<th>모바일</th>
							<td>
								<textarea class="form-control input-sm" style="font-size:11px;height:78px" readonly>
								설치전: <img src="/images/investment/live_ban01_m.gif" width="100%" onClick="openStreamReady()" style="cursor:pointer;">
								설치후: <img src="/images/investment/live_ban01_m.gif" width="100%" onClick="window.open('<?=$play_url;?>','stream_win','toolbar=no,menubar=no,status=no,scrollbars=no,resizable=no');" style="cursor:pointer;"></textarea>
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<?
			}
			?>

			<tr>
				<th>상품평가 <button type="button" id="evaluate_on" class="btn btn-default btn-sm pull-right">펼치기</button></th>
				<td>
					<div id="evaluate_zone" style="display:none;">
						<table class="table-condensed table-bordered">
							<colgroup>
								<col width="12%">
								<col width="38%">
								<col width="12%">
								<col width="38%">
							</colgroup>
							<tr>
								<th>안전성</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_score1" value="<?=$PRDT['evaluate_score1'];?>" class="form-control input-sm" style="margin:0;width:80px"><li>
										<li>/ 40 (구 48점)</li>
									</ul>
								</td>
								<th>별점</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_star1" value="<?=$PRDT['evaluate_star1'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
										<li>개</li>
									</ul>
								</td>
							</tr>

							<tr>
								<th>상환성</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_score4" value="<?=$PRDT['evaluate_score4'];?>" class="form-control input-sm" style="margin:0;width:80px"><li>
										<li>/ 30 (구 42점)</li>
									</ul>
								</td>
								<th>별점</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_star4" value="<?=$PRDT['evaluate_star4'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
										<li>개</li>
									</ul>
								</td>
							<tr>

							<tr>
								<th>환금성</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_score3" value="<?=$PRDT['evaluate_score3'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
										<li>/ 30 (구 5점)</li>
									</ul>
								</td>
								<th>별점</th>
								<td><ul class="list-inline" style="margin:0;padding:0;">
										<li><input type="text" name="evaluate_star3" value="<?=$PRDT['evaluate_star3'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
										<li>개 &nbsp; <span style='color:#FF2222'>(* 29, 30만 기제요망)</span></li>
									</ul>
								</td>
							<tr>

							<? if($PRDT['evaluate_score2'] || $PRDT['evaluate_star2']) { ?>
								<tr>
									<th>수익성</th>
									<td><ul class="list-inline" style="margin:0;padding:0;">
											<li><input type="text" name="evaluate_score2" value="<?=$PRDT['evaluate_score2'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
											<li>/ 5</li>
										</ul>
									</td>
									<th>별점</th>
									<td><ul class="list-inline" style="margin:0;padding:0;">
											<li><input type="text" name="evaluate_star2" value="<?=$PRDT['evaluate_star2'];?>" class="form-control input-sm" style="margin:0;width:80px"></li>
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
				<th>주소 <button type="button" id="addr_on" class="btn btn-default btn-sm pull-right">펼치기</button></th>
				<td>
					<div id="addr_zone" style="display:none;">
						<table class="table-condensed table-bordered">
							<colgroup>
								<col width="12%">
								<col width="38%">
								<col width="12%">
								<col width="38%">
							</colgroup>
							<tr>
								<th>주소입력</th>
								<td colspan="3">
									<ul class="list-inline">
										<li class="list-inline-item">
											<input type="text" name="zipcode" value="<?=$PRDT['zipcode'];?>" id="zipcode" class="form-control input-sm" readonly size="5" maxlength="6">
										</li>
										<li class="list-inline-item">
											<button type="button" class="btn btn-sm btn-success" onClick="win_zip('product_form', 'zipcode', 'address', 'address_detail', 'address2', 'address3');">주소 검색</button>
										</li>
									</ul>

									<div class="input-group">
										<input type="text" name="address" value="<?=$PRDT['address'];?>" id="address" class="form-control input-sm" style="width:800px; margin-bottom:2px;" readonly="readonly">
										<input type="text" name="address_detail" value="<?=$PRDT['address_detail'];?>" id="address_detail" class="form-control input-sm" style="width:800px;">
										<input type="hidden" name="address2" value="<?=$PRDT['address2'];?>" id="address2" class="form-control input-sm">
										<input type="hidden" name="address3" value="<?=$PRDT['address3'];?>" id="address3" class="form-control input-sm">
									</div>
								</td>
							</tr>
							<tr>
								<th>위도</th>
								<td><input type="text" name="lat" value="<?=$PRDT['lat'];?>" class="form-control input-sm" style="width:120px"></td>
								<th>경도</th>
								<td><input type="text" name="lng" value="<?=$PRDT['lng'];?>" class="form-control input-sm" style="width:120px"></td>
							</tr>
						</table>
					</div>
				</td>
			</tr>

			<tr>
				<th>상세내용구성 <button type="button" id="content_on" class="btn btn-default btn-sm pull-right">펼치기</button></th>
				<td>

					<!-- 에디터 영역 //-->
					<div id="content_zone" style="display:block">
						<table class="table-boreded table-condensed">
							<colgroup>
								<col width="150px">
								<col width="*">
							</colgroup>
							<tr>
								<th>상품요약</th>
								<td><textarea name="product_summary" class="product-detail"><? echo get_text($PRDT['product_summary'], 0);?></textarea></td>
							</tr>
							<tr>
								<th>투자 포인트</th>
								<td><textarea name="core_invest_point" class="product-detail"><? echo get_text($PRDT['core_invest_point'], 0);?></textarea></td>
							</tr>
							<tr>
								<th>상품개요</th>
								<td><textarea name="product_description" class="product-detail"><? echo get_text($PRDT['product_description'], 0);?></textarea></td>
							</tr>

							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th>채권매입보증</th>
									<td><textarea name="extend_6" class="product-detail"><? echo get_text($PRDT['extend_6'], 0);?></textarea></td>
								</tr>
							<? } ?>

							<tr>
								<th>투자설명서 (PC)</th>
								<td><textarea name="invest_summary" class="product-detail"><? echo get_text($PRDT['invest_summary'], 0);?></textarea></td>
							</tr>

							<tr>
								<th>투자설명서 (모바일)</th>
								<td><textarea name="invest_summary_m" class="product-detail"><? echo get_text($PRDT['invest_summary_m'], 0);?></textarea></td>
							</tr>
							<tr>
								<th>안전장치 업데이트</th>
								<td><textarea name="extend_8" class="product-detail"><? echo get_text($PRDT['extend_8'], 0);?></textarea></td>
							</tr>
							<tr>
								<th>증빙자료</th>
								<td><textarea name="extend_9" class="product-detail"><? echo get_text($PRDT['extend_9'], 0);?></textarea></td>
							</tr>
							<tr>
								<th>투자 시 유의사항</th>
								<td><textarea name="extend_7" class="product-detail"><? echo get_text($PRDT['extend_7'], 0);?></textarea></td>
							</tr>
							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th class="chgTitle2" style="color:brown;">투자자 보호장치</th>
									<td><textarea name="extend_4" class="product-detail"><? echo get_text($PRDT['extend_4'], 0);?></textarea></td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th class="chgTitle3" style="color:brown;">담보분석 및 평가</th>
									<td><textarea name="extend_1" class="product-detail"><? echo get_text($PRDT['extend_1'], 0);?></textarea></td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th class="chgTitle4">신용 및 부채정보</th>
									<td><textarea name="extend_2" class="product-detail"><? echo get_text($PRDT['extend_2'], 0);?></textarea></td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th>투자 구조도</th>
									<td>
										※ 문서링크 버튼태그 : <span style='color: brown'>&lt;a href="http://문서URL" class="btn_blue_document"&gt;&lt;img src="/images/flaticon/text-document.png"&gt;&nbsp;문서제목 보기&lt;/a&gt;</span><br/>
									&nbsp;&nbsp;&nbsp; <span style='color: red'>주의) 반드시 위지윅 에디터의 입력상태를 HTML로 선택한 후 붙여넣기 하세요.</span>
										<textarea name="extend_3" class="product-detail"><? echo get_text($PRDT['extend_3'], 0);?></textarea>
									</td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '207') { ?>
								<tr>
									<th>평가기관 의견</th>
									<td><div id="chgStyle1"><textarea name="extend_5" class="product-detail"><? echo get_text($PRDT['extend_5'], 0);?></textarea></div></td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '142') { ?>
								<tr>
									<th>심사총평</th>
									<td><div id="chgStyle2"><textarea name="screening" class="product-detail"><? echo get_text($PRDT['screening'], 0);?></textarea></div></td>
								</tr>
							<? } ?>

							<? if ($prd_idx && $PRDT['prd_idx'] < '142') { ?>
								<tr>
									<th>심사자</th>
									<td>
										<ul class="list-inline" style="margin:0;padding:0;">
											<li>
												<select name="judge" class="form-control input-sm" style="width:120px;">
													<option value=''>::선택::</option>
													<?
													$JUDGE_ARR = array_keys($JUDGE);
													for ($i = 0; $i < count($JUDGE); $i++) {
														$selected = ($PRDT['judge'] == $JUDGE_ARR[$i]) ? 'selected' : '';
														echo "<option value='" . $JUDGE_ARR[$i] . "' $selected>" . $JUDGE[$JUDGE_ARR[$i]] . "</option>\n";
													}
													?>
												</select>
											</li>
											<li><span class="help-inline">*선택된 심사자의 프로필 배너가 투자상품 상세보기 페이지의 '투자 요약 상단'에 위치합니다.</span></li>
										</ul>
									 </td>
								</tr>
							<? } ?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<th>증빙서류</th>
				<td colspan="3">
					<select name="evidence[]" class="form-control input-sm" multiple="multiple">
					<?
					foreach ((array)explode('|', $PRDT['evidence']) as $key => $val) {
						if (!$val) { continue; }
						echo "<option value='".$val."'>".$val."</option>\n";
					}
					?>
					</select>
					<div class="help-block">
						<button type="button" class="btn btn-primary btn-sm" onClick="uploadImage('evidence');">업로드</button>
						<button type="button" class="btn btn-danger btn-sm" onClick="deleteImage('evidence');">선택삭제</button>
					</div>
				</td>
			</tr>
			</tbody>
		</table>
		<br/>
		<br/>
		<div>
			<p id="button_area" class="text-center" style="width: 100%;padding: 10px 0 !important; margin: 0 !important;">
<?
$sbutton_act = ( in_array($member['mb_id'], $CONF['OPERATOR']) ) ? '' : 'disabled';
//$sbutton_act = ( $PRDT['state'],array('2','5')) ) ? 'disabled' : '';
?>

				<button type="button" onClick="formSubmit(document.product_form);" class="btn btn-success" style="width:40%" <?=$sbutton_act?>><?=($PRDT['idx'])?'상품수정':'상품등록';?></button>
				<?=$ib_vact_reg_button?>
				<? if($_REQUEST['idx']) { ?><button type="button" class="btn btn-success" style="width:20%;" onClick="go_copy();">상품복사</button><? } ?>
				<? if($PRDT['recruit_amount']==1 && $PRDT['invest_count']==0)	{ ?><button type="button" id="investRegist1won" class="btn btn-danger">투자등록(<?=$PRDT['recruit_amount']?>원)</button><? } ?>
			</p>
		</div>
	</form>
</div>

<script>
go_copy = function() {

	var yn = confirm("이 상품을 복사하여 새로운 상품을 만드시겠습니까?");

	var f = document.product_form;

	if (yn) {

		f.action.value = "product_copy";

		f.display[1].checked = true;
		f.isTest.disabled = false;
		f.isTest.checked = true;

		f.success_example.checked = false;
		f.recruit_period_start.value = "";
		f.recruit_period_end.value = "";
		f.open_date.value = "";
		f.start_date.value = "";
		f.end_date.value = "";
		f.recruit_amount.value = "10";

		formSubmit(f);

	}
}
</script>

<script>
$('#investRegist1won').click(function() {
	if(confirm('본 상품의 투자정보를 등록하시겠습니까?')) {
		//$('#ajax_return_txt_zone').css('display','block');
		$.ajax({
			type: 'post',
			dataType: 'html',
			url: 'ajax_invest_finish.php',
			data: {idx:'<?=$PRDT['idx']?>'},
			success:function(data) {
				if(data=='ok') {
					alert('투자자 등록 완료'); window.location.reload();
				}
				else {
					alert(data);
				}
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function(e) { return; }
		});
	}
	else {
		return false;
	}
});
</script>

<form id="upload_form" method="post" action="/builder/multiProcess.php" enctype="multipart/form-data">
	<input type="hidden" name="action" value="product_image_upload">
	<input type="file" name="image_upload" style="display:none;">
</form>

<div style="position:fixed; display:none; z-index:1002; top:150px;left:30px; border:1px solid #bbb; padding:4px;background-color:#FAFAFA;">
	top_position : <input type="text" id="top_position"> &nbsp;
	scroll_top : <input type="text" id="scroll_top">
</div>


<link type="text/css" href="/adm/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="/adm/css/jquery-ui.min.css" rel="stylesheet">
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/styles/shCore.min.css" rel="stylesheet">
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/styles/shThemeDefault.min.css"  rel="stylesheet">
<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
<!--<link type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.css" rel="stylesheet">-->
<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.8.0/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" >-->
<!--<link href="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.8.0/css/froala_style.min.css" rel="stylesheet" type="text/css" >-->

<link href="<? echo G5_PLUGIN_URL;?>/editor/froalaeditor/css/froala_style.css" rel="stylesheet" type="text/css">
<link href="<? echo G5_PLUGIN_URL;?>/editor/froalaeditor/css/froala_editor.pkgd.css" rel="stylesheet" type="text/css">
<!--<link href="--><? //echo G5_PLUGIN_URL;?><!--/editor/froalaeditor/css/hide-froala-license.css" rel="stylesheet" type="text/css">-->

<script type="text/javascript" src="/adm/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="/adm/js/jquery.form.js"></script>

<script type="text/javascript" src="<? echo G5_PLUGIN_URL;?>/editor/froalaeditor/js/froala_editor.pkgd.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/mode/xml/xml.min.js"></script>
<!--<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.8.0/js/froala_editor.pkgd.min.js"></script>-->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/froala-editor/2.7.6/js/languages/ko.js"></script>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shCore.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/SyntaxHighlighter/3.0.83/scripts/shBrushJScript.min.js"></script>

<script type="text/javascript">
	SyntaxHighlighter.all();

	// 2018-04-07 김국현 추가
	// froalaEditor 무료버전 사용
	// 한국어 패치적용
	// 속도향상을 위한 캐시사용
	$(function() {
		var prd_idx = $('#prd_idx').val();
		$('textarea.product-detail').froalaEditor({
			inlineMode: false,
			language: 'ko',
			heightMin: 300,
			heightMax: 400,
			tabSpaces: false,
			imageUploadURL: g5_url + '/plugin/editor/froalaeditor/upload.php',
			imageUploadParams: {onlyImg: 1, idx: prd_idx},
			imageUploadMethod: 'POST',
			imageMaxSize: 5 * 1024 * 1024,
			imageAllowedTypes: ['jpeg', 'jpg', 'png', 'gif'],
			toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', 'fontFamily', 'fontSize', '|', 'color', 'emoticons', 'inlineStyle', 'paragraphStyle', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', '-', 'insertLink', 'insertImage', 'insertVideo', 'insertFile', 'insertTable', '|', 'undo', 'redo', 'clearFormatting', 'selectAll', 'html', 'print'],
			fileUploadURL: g5_url + '/plugin/editor/froalaeditor/upload.php',
			fileUploadParams: {onlyImg: 2, idx: prd_idx},
			fileUploadMethod: 'POST',
			fileMaxSize: 20 * 1024 * 1024, // 20MB
			fileAllowedTypes: ['*'],
//			enter: $.FroalaEditor.ENTER_BR,
			imageDefaultWidth: 0,
			placeholderText: '',
			htmlAllowComments: false,
			linkAlwaysNoFollow: false,
			linkAlwaysBlank: true,
			useClasses: true,
			saveInterval: 999999

		}).on('froalaEditor.image.beforeUpload', function (e, editor, images) {
				// Return false if you want to stop the image upload.
		}).on('froalaEditor.image.uploaded', function (e, editor, response) {
				var data = JSON.parse(response);
				var imgUrl = data.link;
				editor.image.insert(imgUrl, false, null, editor.image.get(), response);
		}).on('froalaEditor.image.inserted', function (e, editor, $img, response) {
				// Image was inserted in the editor.
		}).on('froalaEditor.image.replaced', function (e, editor, $img, response) {
				// Image was replaced in the editor.
		}).on('froalaEditor.image.error', function (e, editor, error, response) {
			if (error.code == 1) {  alert("파일첨부를 할 수 없는 상황입니다."); }
			else if (error.code == 2) {  alert("파일을 첨부할 수 없습니다.");  }
			else if (error.code == 3) {  alert("해당 이미지를 첨부할 수 없습니다.");  }
			else if (error.code == 4) {  alert("파일첨부중 오류가 발생하였습니다.");  }
			else if (error.code == 5) {  alert("이미지가 너무 큽니다. 작은 이미지를 사용하세요.");  }
			else if (error.code == 6) {  alert("첨부할 수 없는 파일 확장자입니다.");  }
			else if (error.code == 7) {  alert("웹브라우저가 호환되지 않습니다.");  }
		}).on('froalaEditor.file.beforeUpload', function (e, editor, files) {
			// Return false if you want to stop the file upload.
		}).on('froalaEditor.file.uploaded', function (e, editor, response) {
		}).on('froalaEditor.file.inserted', function (e, editor, $file, response) {
				// File was inserted in the editor.
		}).on('froalaEditor.file.error', function (e, editor, error, response) {
			if (error.code == 1) {  alert("파일첨부를 할 수 없는 상황입니다."); }
			else if (error.code == 2) {  alert("파일을 첨부할 수 없습니다.");  }
			else if (error.code == 3) {  alert("해당 파일을 첨부할 수 없습니다.");  }
			else if (error.code == 4) {  alert("파일첨부중 오류가 발생하였습니다.");  }
			else if (error.code == 5) {  alert("파일크기가 너무 큽니다. 작은 이미지를 사용하세요.");  }
			else if (error.code == 6) {  alert("첨부할 수 없는 파일 확장자입니다.");  }
			else if (error.code == 7) {  alert("웹브라우저가 호환되지 않습니다.");  }
		});
	});

	/* var div = document.querySelector('.product-detail');
	div.focus();

	try {
		var text = div.firstChild;
		var selection = document.getSelection();
		var range = document.createRange();

		text.remove();
		range.setStart(text, 0);
		range.setEnd(text, 1);
		selection.addRange(range);
	} catch(ignored) {

	}*/

	function desctroy($textarea) {
		if ($textarea.data('froala.editor')) {
			$textarea.froalaEditor('destroy');
		}
		$textarea.remove();
	}

	/*$.FroalaEditor.DefineIcon('dynContent', { NAME: 'users' });
	$.FroalaEditor.RegisterCommand('dynContent', {
		title: 'Insert dynamic content',
		focus: true,
		undo: true,
		refreshAfterCallback: true,
		callback: function () {
			let editor = this;
			var insertHtml;
			let dialogRef = self.dialog.open(DynamicContentDialog, {
				height: '700px',
				width: '1000px'
			});
			dialogRef.afterClosed().subscribe(result => {
				if (result != null) {
					insertHtml = result.string;
					editor.html.insert(result.string)
				}
			});
		}
	});*/

	var loading = function(arg) {
		if(arg=='on') {
			$('#loading').css('display','block');
		}
		else {
			$('#loading').css('display','none');
		}
	};

	$('#evaluate_on').click(function() {
		if($('#evaluate_zone').css('display')=='block') {
			$('#evaluate_on').text('펼치기');
		}
		else {
			$('#evaluate_on').text('접기');
		}
		$('#evaluate_zone').slideToggle('fast');
	});

	/*
	$(document).ready(function() {
		$.ajax({
			type: "get",
			dataType: "text html",
			url: "ajax_product_content_form.php",
			data: {idx:'<? echo ($copy_idx)?$copy_idx:$PRDT['idx'];?>'},
			success:function(data) {
				$('#content_zone').html(data);
				setTimeout("$('#content_zone').css('display','none');", 10*1000);
			},
			beforeSend: function() { loading('on'); },
			complete: function() { loading('off'); },
			error: function(e) { return; }
		});
	});
	 */

	$('#content_on').click(function() {
		if($('#content_zone').css('display')=='block') {
			$('#content_on').text('펼치기');
		}
		else {
			$('#content_on').text('접기');
		}
		$('#content_zone').slideToggle('fast');
	});

	$('#addr_on').click(function() {
		if($('#addr_zone').css('display')=='block') {
			$('#addr_on').text('펼치기');
		}
		else {
			$('#addr_on').text('접기');
		}
		$('#addr_zone').slideToggle('fast');
	});

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
			$('#chgTitle1').append('핵심 투자포인트 (투자 포인트)');
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
		if($('input:radio[name="category"]:checked').val()=='') {
			alert('담보물건를 선택하십시요.');
		}
		else {
			$("select[name='detail_image[]'] option").prop('selected', true);
			$("select[name='evidence[]'] option").prop('selected', true);

			if(confirm('본 상품을 등록 하시겠습니까?')) {
				f.submit();
			}
		}
	}

	function uploadImage(selector) {
		options.success = function(data) {
			$("select[name='"+selector+"[]']").append('<option value="'+data+'">'+data+'</option>');
		};

		$("input[name=image_upload]").trigger('click');
	}

	function deleteImage(selector) {
		var file_name = $("select[name='"+selector+"[]'] :selected").val();
		$.post('./product_form_update.php', {
				action: 'product_image_delete',
				idx: '<?=$idx?>',
				fname: file_name
			}, function() {
				//$("select[name='"+selector+"[]'] option[value=" + file_name + "]").remove();
				$("select[name='"+selector+"[]'] :selected").remove();
			}
		);
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
				loan_dep_amt<?=$j?>	   = $('#loan_dep_amt<?=$j?>').val();
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
					if(array_result[1]=='LOGIN')						{ $(location).attr('href', '/'); }
					else if(array_result[1]=='NONE_MEMBER')			 { $(location).attr('href', '/'); }
					else if(array_result[1]=='NONE_PRODUCT')			{ alert('대출상품정보가 없습니다.'); }
					else if(array_result[1]=='EMPTY_LOANER_INFO')	   { alert('대출자정보(대출회원정보)가 없습니다.\n대출회원을 등록하고 본상품에 해당 대출자로 선택하여야 합니다.'); }
					else if(array_result[1]=='EMPTY_LOANER_ACCT_INFO')  { alert('대출 입금계좌를 1개 이상 설정하십시요.'); }
					else if(array_result[1]=='DIFFRENT_DEPOSIT_AMOUNT') { alert('대출 입금계좌에 등록된 금액의 합과 모집목표금액이 상이합니다.'); }
					else if(array_result[1]=='SH_VA_INSUFFICIENCY')	 { alert('배정 가능한 가상계좌(헬로크라우드대부용)가 없습니다.\n여유 가상계좌를 확보하십시요.'); }
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

<script>
function check_auto_inv() {

	var grp_idx = $("#gr_idx").val();
	var ai_grp_idx = $("#ai_grp_idx").val();
	console.log(grp_idx+" "+ai_grp_idx);
	$.ajax({
		url: "/adm/auto_invest/ajax_auto_inv_money.php",
		type: 'POST',
		data: {s_type:ai_grp_idx, ai_grp_idx:grp_idx},
		dataType: "json",
		success: function(res , textStatus, jqXHR) {
			//console.log(res);
			//console.log(textStatus);
			//console.log(jqXHR);
			//alert(res.total_amount);
			$("#auto_inv_passb_amt").text("자동투자시 예상금액: "+number_format(res["total_amount"])+"원");
		},
		error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요..!"); return; }
	});
}
function view_auto_list(s_type, ai_grp_idx) {
	var gr_idx = $("#gr_idx").val();
	var ai_grp_idx = $("#ai_grp_idx").val();
	window.open("/adm/auto_invest/auto_inv_real_money_list.html?s_type="+ai_grp_idx+"&gr_idx="+gr_idx, "_blank", "left=50,top=30,width=1200,height=1000,scrollbars=yes");
}
</script>

<script>
function go_sh_srch_acc(bank_code, acc, j) {

	if (!bank_code || !acc) {
		alert("은행선택을 조회할 계좌번호를 입력해주세요.");
		return false;
	}

	$("#loan_dep_acct_nm1\\["+j+"\\]").text("");

	$.ajax({
		type: 'post',
		dataType: 'json',
		url: '/adm/product/ajax_sh_srch_acc.php',
		data: {'bank_code':bank_code , 'acc':acc},
		success:function(ares) {
			console.log(ares);

			if(ares.RCODE=='00000000') {
				$("#loan_dep_acct_nm1\\["+j+"\\]").text(ares.ACCT_OWNER_NM);
			} else {
				alert("ERROR : "+ares.ERRMSG);
			}
		},
		//beforeSend: function() { loading('on'); },
		//complete: function() { loading('off'); },
		error: function(e) { console.log(e); }

	});
}
</script>

<? include_once ('../admin.tail.php'); ?>