<?
include_once('_common.php');

if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

//print_rr($_REQUEST, "font-size:11px");

while( list($k, $v) = each($_POST) ) { ${$k} = @trim($v); }

$sql = "
	SELECT
		*
	FROM
		cf_auto_invest_config
	WHERE
		display='Y'
	ORDER BY
		idx DESC";
$res = sql_query($sql);
$rows = $res->num_rows;
for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	$SETUP_DATA = sql_fetch("SELECT idx, setup_amount, invest_warning_agree FROM cf_auto_invest_config_user WHERE member_idx='".$member['mb_no']."' AND ai_grp_idx='".$LIST[$i]['idx']."' ORDER BY idx LIMIT 1");
	$LIST[$i]['setup_idx']            = $SETUP_DATA['idx'];
	$LIST[$i]['setup_amount']         = $SETUP_DATA['setup_amount'];
	$LIST[$i]['invest_warning_agree'] = $SETUP_DATA['invest_warning_agree'];
	$LIST[$i]['PRDT'] = array();

	//소속상품리스트 추가
	$sql2 = "
		SELECT
			idx, title, recruit_amount, invest_period, invest_days, invest_return, start_datetime
		FROM
			cf_product
		WHERE (1)
			AND state=''
			AND display='Y'
			AND ai_grp_idx='".$LIST[$i]['idx']."'
			-- AND start_datetime > NOW() --
		ORDER BY
			start_datetime ASC";
	$res2 = sql_query($sql2);
	$j = 0;
	while($PLIST[$j] = sql_fetch_array($res2)) {
		$PLIST[$j]['print_invest_period'] = ($PLIST[$j]['invest_days'] > 0 && $PLIST[$j]['invest_days'] < 30)  ? $PLIST[$j]['invest_days'] . '일' : $PLIST[$j]['invest_period'] . '개월';
		unset($PLIST[$j]['invest_period']);
		unset($PLIST[$j]['invest_days']);
		$LIST[$i]['PRDT'][$j] = $PLIST[$j];
		$j++;
	}
}
$list_count = count($LIST);
//print_rr($LIST, 'font-size:11px');

if($member['member_type']=='1' && $member['is_creditor']=='N') {
	$is_indi_member = ($member['member_investor_type'] < 3) ? true : false;
	if($is_indi_member) $group_product_limit = $INDI_INVESTOR[$member['member_investor_type']]['group_product_limit'];
}
?>
<script>
alert("투자 편의성을 위하여 확장개편되는 헬로펀딩 신규 자동투자는\n24일(월) 오후 1시부터 설정 가능합니다.\n\n감사합니다.");
history.back();
</script>
<? die(); ?>
<?
if(G5_IS_MOBILE) {
	include_once("ajax_auto_invest_config_m.php");
	return;
}

?>
<style>
.tblX { width:100%; border:1px solid #ccc }
.tblX th { padding:0 4px 0 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc; text-align:center; font-family:'NGB'; font-size:1.1em; }
.tblX td { padding:0 4px 0 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc; text-align:center; font-family:'NG'; font-size:1.0em; }
.btn_blue_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#284893; border:0; vertical-align:middle; cursor:pointer; }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2  { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888; border:0; vertical-align:middle; cursor:pointer; }

ul.this  { font-size:10pt; font-family:NG; padding-left:24px }
ul.this li { list-style-type:decimal;list-style-position:outside; color:#000; }
ul.this li.nn { list-style-type:none;color:#000; }

h4.title1 {font-size:11pt; font-family:NGB; color:#000; }

.text2 { height:33px; padding:0 5px; border:1px solid #AAA; vertical-align:middle; }
</style>

<script>
fSubmit = function(n, sidx, gidx, ca) {
  $aiflag_val = $('input:radio[name="auto_invest_flag' + n + '"]:checked').val();
  $samt_val = Number($('#setup_amount' + n).val());

	if($aiflag_val=='1') {
		if($samt_val <= 0) { alert('투자설정금액을 입력하십시요.'); $('#setup_amount' + n).focus(); return false; }
		$samt_val = $samt_val * <?=$CONF['min_invest_limit']?>;
		if(($samt_val%<?=$CONF['min_invest_limit']?>) > 0) { alert('설정금액은 <?=number2korean($CONF['min_invest_limit'])?>원 단위로 입력하여 주십시요.'); $('#setup_amount' + n).focus(); return false; }

		<? if($is_indi_member) { ?>
		if($samt_val > <?=$group_product_limit?>) { alert('자동투자 설정금액은 투자자 유형별 최대 투자금액을 초과할 수 없습니다.\n\n회원님의 최대 투자가능금액은 <?=price_cutting($group_product_limit)?>원 입니다.'); $('#setup_amount' + n).focus(); return false; }
		<? } ?>

		<? if($member['receive_method']=='1') { ?>
		if(ca=='3') {
			// 환급방식 강제전환
			alert('확정매출채권 자동투자 설정시 원리금은 예치금 상환방식으로 자동 변경됩니다.');
			$('#receive_method_change').val('1');
		}
		/*
		if(confirm("[투자 Tip]\n원리금을 예치금으로 받으시면, 편리하게 재투자 하실 수 있습니다.\n\n원리금을 예치금으로 받으시려면 '확인'을 클릭,\n\n원리금을 환급계좌로 받으시려면 '취소'를 클릭해주세요.")) {
			$('#receive_method_change').val('1');
		}
		*/
		<? } ?>

	}
	else {
		$samt_val = 0;
	}

  $('#setup_idx').val(sidx);
  $('#ai_grp_idx').val(gidx);
  $('#auto_invest_flag').val($aiflag_val);
  $('#setup_amount').val($samt_val);

  fdata = $('#auto_invest_form').serialize();
  $.ajax({
    url  : "./auto_invest_config_proc.php",
    type : "POST",
    data : fdata,
    success : function(data) {
      $('#ajax_return_txt').val(data);
      if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); location.replace('/'); }
      else if(data=="INSERT_FAIL" || data=="DROP_FAIL") { alert('저장실패'); }
      else if(data=="INSERT_OK") { alert('저장되었습니다.'); auto_invest_config(); }
      else if(data=="DROP_OK") { alert('설정 해제되었습니다.'); auto_invest_config(); }
			else if(data=="UNCHANGED") { alert('이미 저장된 내용과 동일한 설정입니다.'); }
			else { if(data!='') alert(data); }
		},
    error : function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); }
  });
}

fActivation = function(n) {
	$aiflag_val = $('input:radio[name="auto_invest_flag' + n + '"]:checked').val();
	$tObj = $('#setup_amount' + n);

	if($aiflag_val=='1') {
		$tObj.removeAttr('disabled');
		if(!confirm('해당 상품의 설명을 인지하셨습니까?')) {
			if( $('#swzone' + n).css('display')=='none' ) letsToggle(n);
		}
	}
	else {
		$tObj.attr('disabled', true);
	}
}

letsToggle = function(n) {
	$obj  = $('#btnToggle' + n);
	$obj2 = $('#swzone' + n);

	if($($obj2).css('display')=='block') {
		$($obj).text('보기 ▼')
		$($obj).removeClass();$($obj).addClass('btn_default');
	}
	else {
		$($obj).text('접기 ▲')
		$($obj).removeClass();$($obj).addClass('btn_blue_dis');
	}

	$($obj2).slideToggle('slow');
}
</script>

<h3>헬로펀딩 자동투자 설정</h3>
<div class="mb30">
  <div style="padding:10px 10px;">
    <h4 class="title1">헬로펀딩 자동투자를 신청하시면 시간, 장소에 구애 받지 않고 편안하게 투자하실 수 있습니다.<!--<br>자동투자는 매출채권 담보상품에만 해당됩니다.--></h4>
  </div>

  <table class="tblX">
    <colgroup>
      <col style='width:%'>
      <col style='width:15%'>
      <col style='width:10%'>
      <col style='width:10%'>
			<col style='width:10%'>
      <col style='width:12%'>
      <col style='width:14%'>
      <col style='width:10%'>
    </colgroup>
    <tr style="height:50px;background-color:#F7F7F7;border-top:2px solid #284893;">
      <th>자동투자그룹</th>
      <th>상품군</th>
			<th>투자기간</th>
      <th>수익률(연)</th>
      <th>상품설명</th>
      <th>자동투자</th>
      <th>1회투자<br>설정금액</th>
      <th>투자설정</th>
    </tr>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		if($LIST[$i]['category']=='1')      $print_category = '동산담보대출상품';
		else if($LIST[$i]['category']=='2') $print_category = '부동산담보대출상품';
		else if($LIST[$i]['category']=='3') $print_category = '확정매출채권담보대출';
		else                                $print_category = '전체';

		$period_days = ($LIST[$i]['min_period_days'] || $LIST[$i]['max_period_days']) ? $LIST[$i]['min_period_days'].' ~ '.$LIST[$i]['max_period_days'].'일' : '';
		$profit = ((int)$LIST[$i]['min_profit'] > 0 || (int)$LIST[$i]['max_profit'] > 0) ? (int)$LIST[$i]['min_profit'].' ~ '.(int)$LIST[$i]['max_profit'].'%' : '';
		$summary = $LIST[$i]['summary'];

		$print_amount = ($LIST[$i]['setup_amount']) ? $LIST[$i]['setup_amount']/10000 : 0;

		if($LIST[$i]['invest_warning_agree']) {
			$button_action = "fSubmit('$i','".$LIST[$i]['setup_idx']."','".$LIST[$i]['idx']."', '".$LIST[$i]['category']."');";
		}
		else {
			$button_action = "invest_warning_agree_open('".$i."','".$LIST[$i]['setup_idx']."','".$LIST[$i]['idx']."', '".$LIST[$i]['category']."');";
		}

		$prdt_count = count($LIST[$i]['PRDT']);

?>
    <tr style="height:50px;">
      <td><strong><?=$LIST[$i]['grp_title']?></strong></td>
      <td><?=$print_category?></td>
			<td><?=$period_days?></td>
      <td><?=$profit?></td>
      <td><button type="button" id="btnToggle<?=$i?>" onClick="letsToggle(<?=$i?>);" class="btn_default">보기 ▼</button></td>
      <td>
        <label style="color:#FF2222"><input type="radio" name="auto_invest_flag<?=$i?>" id="auto_invest_flag<?=$i?>" value="1" <?=($LIST[$i]['setup_idx'])?'checked':''?> onClick="fActivation('<?=$i?>');"> ON</label>
        <label style="margin-left:10px"><input type="radio" name="auto_invest_flag<?=$i?>" id="auto_invest_flag<?=$i?>" value="0" <?=($LIST[$i]['setup_idx']=='')?'checked':''?> onClick="fActivation('<?=$i?>');"> OFF</label>
      </td>
      <td><input type="text" id="setup_amount<?=$i?>" value="<?=$print_amount?>" class="text2" maxlength="8" style="width:80px;text-align:right;" onKeyup="onlyDigit(this);" <?=($LIST[$i]['setup_idx'])?'':'disabled'?>> 만원</td>
      <td><button type="button" onClick="<?=$button_action?>" class="btn_blue">저장</button></td>
    </tr>
    <tr>
      <td colspan="8" align="left" style="background:#FAFAFA;height:0;padding:0">
				<div id="swzone<?=$i?>" style="display:none;margin:10px 10px 20px;text-align:left;">
					<h4 class="title1">[진행중인 상품]</h4>
					<div class="this" style="width:100%;display:inline-block;padding-bottom:10px;margin-bottom:20px;border-bottom:1px dotted #aaa">
<?
		if($prdt_count) {
			for($j=0; $j<$prdt_count; $j++) {
				$print_status = ( time() < (strtotime($LIST[$i]['PRDT'][$j]['start_datetime'])-300) ) ? '예약가능' : '자동투자종료';
				echo '
					<ul style="clear:both;padding-left:12px">
						<li style="float:left">' .
							$LIST[$i]['PRDT'][$j]['title'] . ' / ' .
							price_cutting($LIST[$i]['PRDT'][$j]['recruit_amount']) . '원 / '.
							$LIST[$i]['PRDT'][$j]['invest_return'] . '% / '.
							$LIST[$i]['PRDT'][$j]['print_invest_period'] . ' / '.
							$print_status .
						'</li>
					</ul>' . PHP_EOL;
			}
		}
		else {
			echo '<ul style="clear:both;padding-left:12px"><li>현재 투자 대기중인 상품이 없습니다.</li></ul>' . PHP_EOL;
		}
?>
          </div>

<?=$summary?>
				</div>
			</td>
    </tr>
<?
	}
}
else {
	echo '<tr><td height="40" colspan="8" align="center">등록된 데이터가 없습니다.</td></tr>';
}
?>
  </table>

  <form name="auto_invest_form" id="auto_invest_form" method="post">
    <input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" name="setup_idx"        id="setup_idx"        placeholder="설정IDX">
    <input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" name="ai_grp_idx"       id="ai_grp_idx"       placeholder="자동투자그룹IDX">
    <input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" name="auto_invest_flag" id="auto_invest_flag" placeholder="설정플래그">
    <input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" name="setup_amount"     id="setup_amount"     placeholder="설정금액">
		<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" name="receive_method_change" id="receive_method_change" placeholder="원리금수취방식변경">
	</form>

  <div style="margin-top:40px; padding:10px 20px; border:1px solid #AAA;background:#FAFAFA; border-radius:5px;">
		<h4 class="title1">[설정방법]</h4>
    <ul class="this">
      <!--<li>자동투자는 매출채권 담보상품에만 해당됩니다.</li>-->
      <li>자동투자 'ON' 선택 시 투자자 유형별 최대 투자금액까지 설정 가능합니다.(일만원 단위)<br>
			- 일반회원: 500만원<br>
			- 소득적격투자자: 2,000만원<br>
			- 전문투자자, 법인회원: 제한없음<br>
			(투자금은 <?=number2korean($CONF['min_invest_limit'])?>원 단위로 설정하실 수 있습니다.)<br></li>
      <li>자동투자 'OFF' 선택 시 자동투자 설정금액은 0원으로 변경됩니다.</li>

    </ul>
    <br>
    <h4 class="title1">[유의사항]</h4>
    <ul class="this">
      <!--<li>자동투자는 투자시작 최소 1시간 전까지 저장된 설정건에 한해 실행됩니다.</li>-->
      <li>자동투자 신청시 일반투자보다 우선 투자됩니다.</li>
      <li>예치금이 설정된 투자금액보다 같거나 높은 경우에만 자동투자가 진행됩니다.</li>
      <li>자동투자 신청순서에 따라 자동투자 되므로 이전 자동투자 신청금액에 따라 투자가 진행되지 않을 수 있습니다.</li>
      <li>자동투자 설정 정보 변경시 투자순서는 변경됩니다.</li>
    </ul>
  </div>
</div>


<!-- 투자위험고지 팝업 시작 -->
<div id="invest_warning_agree" class="popbluetheme" style="height:auto;">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">※ 투자위험고지</div>
	<div class="con">
		<div class="con1">
			본 투자상품은 원금이 보장되지 않습니다. 모든 투자상품은 현행 법률 상 ‘유사수신 행위의 규제에 관한 법률’에 의거하여 원금과 수익을 보장할 수 없습니다.<br/>
			또한 차입자가 원금의 전부 또는 일부를 상환하지 못할 경우 발생하게 되는 투자금 손실 등 투자위험은 투자자가 부담하게 됩니다.
		</div>
		<div class="con2">
			<div style="color:#000">상기 내용을 확인하였으며 그 내용에</div>
			<div style="padding-top:10px;">
				<input type="text" id="str" maxlength="3" onKeyup="strCheck(this.value);" class="text1">
				<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="_n">
				<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="_sidx">
				<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="_gidx">
				<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="_agree">
				<input type="<?=($_COOKIE['debug_mode'])?'text':'hidden';?>" id="_ca">
			</div>
		</div>
		<div class="con3" style="color:#FF2222">※ 빈칸에 '동의함' 을 입력하셔야만 자동투자가 가능합니다.</div>
		<div class="btnArea">
			<button id="invest_warning_agree_btn" class="btn_big_blue2 off" style="ime-mode:active">확인</button>
		</div>
	</div>
</div>

<script type="text/javascript">
function invest_warning_agree_open(n, sidx, gidx, ca) {
  $aiflag_val = $('input:radio[name="auto_invest_flag' + n + '"]:checked').val();

	if($aiflag_val=='1') {

	  $samt_val = Number($('#setup_amount' + n).val());
		if($samt_val <= 0) { alert('투자설정금액을 입력하십시요.'); $('#setup_amount' + n).focus(); return; }
		$samt_val = $samt_val * <?=$CONF['min_invest_limit']?>;
		if(($samt_val%<?=$CONF['min_invest_limit']?>) > 0) { alert('설정금액은 <?=price_cutting($CONF['min_invest_limit'])?>원 단위로 입력하여 주십시요.'); $('#setup_amount' + n).focus(); return; }

		$('#str').val('');
		$('#_n').val(n);
		$('#_sidx').val(sidx);
		$('#_gidx').val(gidx);
		$('#_ca').val(ca);

		$.blockUI({
			message: $('#invest_warning_agree'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'1%',left:'1%', width:'98%', border:0, cursor:'default' }
			<? } else { ?>
			css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
			<? } ?>
		});
	}
}

function strCheck(arg) {
	if(arg=='동의함') {
		var agree_val = '1';
		var class_val = 'btn_big_blue2';
		$('#invest_warning_agree_btn').focus();
	}
	else {
		var agree_val = '';
		var class_val = 'btn_big_blue2 off';
	}
	$('#_agree').val(agree_val);
	$('#invest_warning_agree_btn').attr('class', class_val);
}

$('#invest_warning_agree_btn').click(function() {
	if( $('#_agree').val()!='1' ) {
		alert('폼에서 요구하는 문장을 입력하시기 합니다.');
	}
	else {
		_n = $('#_n').val();
		_sidx = $('#_sidx').val();
		_gidx = $('#_gidx').val();
		_ca = $('#_ca').val();

		$.unblockUI();
		fSubmit(_n, _sidx, _gidx, _ca);
		//alert('투자위험고지에 동의 하셨습니다.');
	}
});

$('#invest_warning_agree .close').click(function() {
	$.unblockUI();
	return false;
});
</script>
<!-- 투자위험고지 팝업 끝 -->