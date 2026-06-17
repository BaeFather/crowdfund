<?
include_once('./_common.php');

if(!$member['mb_id']) { alert("로그인 후 이용 가능합니다.", G5_BBS_URL."/login.php?url=" . urlencode($_SERVER[PHP_SELF]."?prd_idx=".$prd_idx)); exit; }
if(!$_REQUEST['idx']) { alert("올바른 경로가 아닙니다.","/"); exit; }

$invest_idx = $_REQUEST['idx'];

$sql = "
	SELECT
		a.amount, a.prin_rcv_no,
		b.*,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE b.idx = product_idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product_invest a
	INNER JOIN
		cf_product b
	ON
		a.product_idx = b.idx
	WHERE
		a.member_idx='".$member['mb_no']."'
		AND a.invest_state= 'Y'
		AND a.idx ='".$invest_idx."'";
$INVEST = sql_fetch($sql);
if(!$INVEST) { alert("올바른 경로가 아닙니다.","/"); exit; }

$product_open_date    = preg_replace("/(-|:| )/", "", $INVEST["open_datetime"]);		//상품공개일시
$product_invest_sdate = preg_replace("/(-|:| )/", "", $INVEST["start_datetime"]);		// 투자마감일시
$product_invest_edate = preg_replace("/(-|:| )/", "", $INVEST["end_datetime"]);			// 투자종료일시

//if($product_invest_sdate<=date("YmdHis") && $product_invest_edate>=date("YmdHis")){
if($product_open_date<=date("YmdHis") && $product_invest_edate>=date("YmdHis")) {
	if($INVEST["recruit_amount"] <= $INVEST["total_invest_amount"]) { alert("펀딩 투자가 완료  되어 취소가 불가능 합니다.","/"); exit; }
}
else {
	alert("펀딩 취소는 투자 기간안에만 가능 합니다.","/"); exit;
}


$g5['title'] = '예치금정보';
$g5['top_bn'] = "/images/mypage/sub_loanlist.jpg";
$g5['top_bn_alt'] = "대출내역 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";


include_once(HF_PATH.'/hf_head.php');


// 모바일 분기
if(G5_IS_MOBILE){
	//include_once('./funding_cancel_m.php');
	//return;
}

?>

<!-- 본문내용 START -->
<div id="content" style="height:500px">
	<div class="location"><span><a href="<?=G5_URL?>/deposit/deposit.php">예치금정보</a></span><b class="blue">펀딩취소하기</b></div>

	<div class="content">
		<form method="post" name="frm" id="frm">
			<input type="hidden" name="invest_idx" value="<?=$invest_idx?>">
		</form>

		<div class="deposit">
			<div class="type03_2 mb10">
				<table>
					<thead>
						<tr>
							<th>상품명</th>
							<th>기간</th>
							<th>투자금</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td><?=$INVEST["title"]?></td>
							<td><?=$INVEST["recruit_period_start"]?> ~ <?=$INVEST["recruit_period_end"]?></td>
							<td><?=number_format($INVEST["amount"])?>원</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="btnArea">
				<span class="btn_big_blue" id="btn_cancel">편딩취소하기</span>
			</div>
		</div>

	</div>
</div>

<!-- 레이어 팝업 내용 -->
<div id="complete" class="deposit">
	<img src="../images/btn_close.gif" alt="close" class="close">
	<div class="title">펀딩취소</div>
	<div class="text">
		<?=$INVEST["title"]?>
		<br>투자를 취소하시겠습니까?
	</div>

	<button type="button" id="yes" class="btn_big_blue">예</button> &nbsp;
	<button type="button" id="no" class="btn_big_link">아니오</button>
</div>

<div id="complete3" class="deposit">
	<img src="../images/btn_close.gif" alt="close" class="close">
	<div class="title">펀딩취소완료</div>
	<div class="text">
	  <span class="blue"><?=$INVEST["title"]?></span> <span class="red">투자취소</span>가 완료되었습니다<br>
	  예치금으로 환불처리 됩니다
	</div>
	<a href="/deposit/deposit.php" id="main" class="btn_big_link">돌아가기</a>
</div>

<script>
function btn_event(arg) {
	if(arg=='send') {
		$('#yes').removeClass('btn_big_blue').addClass('btn_big_gray');
		$('#yes').text('전송중 >>>');
		$('#yes').attr('disabled', 'disabled');
	}
	else if(arg=='exit') {
		$('#yes').removeAttr('disabled');
		$('#yes').text('편딩취소');
		$('#yes').removeClass('btn_big_gray').addClass('btn_big_blue');
	}
	else {
		return;
	}
}

$(document).ready(function(){

	$('#btn_cancel').click(function() {
		$.blockUI({
			message: $('#complete'),
			<?
			if(G5_IS_MOBILE) {
				?>
				css: { border:0, cursor:'default', top:'5%',left:'1%', width:'98%' }
				<?
			} else {
				?>
			css: { border:0, cursor:'default', width:'585px' }
				<?
			}
			?>
		});
	});

	$('#complete #no, #complete .close, #complete3 .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#complete #yes').click(function() {

		btn_event('send');

		ajax_data = $("#frm").serialize();

		$.ajax({
			url : "/root_deposit/ajax_funding_cancel.php",
			type: "POST",
			data : ajax_data,
			success: function(data) {
				$('#ajax_return_txt').val(data);
				if(data=="SUCCESS") {
					$.blockUI({
						message: $('#complete3'),
			<?
			if(G5_IS_MOBILE) {
				?>
				css: { border:0, cursor:'default', top:'5%',left:'1%', width:'98%' }
				<?
			} else {
				?>
			css: { border:0, cursor:'default', width:'585px' }
				<?
			}
			?>
			
					});
				}
				else if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
				else if(data=="ERROR-DATE") { alert("펀딩 투자 기간이 아닙니다. 펀딩 취소는 투자 기간안에만 가능 합니다."); return; }
				else if(data=="ERROR-END")  { alert("펀딩 투자가 완료되어 취소가 불가능 합니다."); return; }
				else { alert(data); return; }

				if(array_result[0]!='SUCCESS') { btn_event('exit'); }

			},
			error: function(e) {
				alert('네트워크 에러 입니다. 잠시 후 다시 시도 하십시요.');
				btn_event('exit');
				return;
			}
		});

	});

});
</script>

<!-- 본문내용 E N D -->
<?
include_once(HF_PATH.'/_tail.php');
?>