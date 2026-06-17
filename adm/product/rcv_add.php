<?
/**
 * 투자상품 목록
 */
$sub_menu = "920100";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);
auth_check($auth[$sub_menu], 'w');

//$g5['title'] = $menu['menu600'][4][1];
$g5['title'] = "채권관리 상품등록";


if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while( list($key, $value) = each($_GET) ) {
	if(!is_array(${$key})) ${$key} = trim($value);
}


if ($_REQUEST['product_idx']) {
	$product_idx = $_REQUEST['product_idx'];

	$product_info = get_product_info($product_idx);
}

function get_product_info($idx) {

	$sql = "
		SELECT
			A.idx, A.title, A.recruit_amount, A.invest_return, A.invest_period, A.state,
			A.loan_start_date, A.loan_end_date_orig, A.loan_end_date,
			A.stream_url1, A.stream_url2,
			B.right_display, B.right_set_date, B.right_pic, B.deposit_pic, B.field_pic
		FROM
			cf_product A
		LEFT JOIN
			cf_product_container B  ON A.idx=B.product_idx
		WHERE
			A.idx='$idx'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);



	if($row['state']=="1") $row['state_txt']="이자상환중";
	else if($row['state']=="2") $row['state_txt']="상환완료";
	else if($row['state']=="3") $row['state_txt']="투자금모집실패";
	else if($row['state']=="4") $row['state_txt']="부실(매각처리중)";
	else if($row['state']=="5") $row['state_txt']="중도상환";
	else if($row['state']=="6") $row['state_txt']="대출최소(기표전)";
	else if($row['state']=="7") $row['state_txt']="대출최소(기표후)";
	else if($row['state']=="8") $row['state_txt']="연체";
	else if($row['state']=="9") $row['state_txt']="부도(상환불가)";
	else $row['state_txt'] = $row['state'];

	$row['loan_end_date_txt'] = ($row['loan_end_date']=='0000-00-00') ? $row['loan_end_date_orig'] : $row['loan_end_date'];

	$tmp = getNumberArr($row['recruit_amount']);
	$row['recruit_amount_txt'] = $tmp[0].$tmp[1]."원";

	$row['invest_return_txt'] = number_format($row['invest_return'])."%";

	$row['invest_period_txt'] = $row['invest_period']."개월";

	$row['info3'] = $row['recruit_amount_txt']." / ".$row['invest_return_txt']." / ".$row['invest_period_txt'];



	if (number_format(substr($row['loan_start_date'],-2))<=5) $row['total_inter'] = $row['invest_period'];
	else $row['total_inter'] = $row['invest_period']+1 ;

	$inter_sql = "SELECT IFNULL(MAX(turn),0) AS real_turn FROM cf_product_success WHERE product_idx='".$idx."'";
	$inter_row = sql_fetch($inter_sql);

	$row['real_inter'] = ($inter_row['real_turn']) ? $inter_row['real_turn'] : 0;
	$row['inter_txt'] = $row['real_inter']." / ".$row['total_inter'];

	return $row;
}

include_once('../admin.head.php');

?>
<form name="f_right" method="post" action="rcv_update.php" enctype="multipart/form-data">
<input type="hidden" name="idx" value="<?=$idx?>"/>
<input type="hidden" name="action_mode" value=""/>
<div class="tbl_head02 tbl_wrap">
	<table class="table table-bordered table-condensed" style="min-width:1200px">
		<tr>
			<th>상품번호</th>
			<td>
				<input type="text" name="idx" id="idx" value="<?=$product_idx?>" class="form-control input-sm" style="width:50px;display:inline;" readonly />&nbsp;&nbsp;
				<select onchange="load_data(this.value);" class="input-sm">
					<option value=""></option>
<?
if($product_idx) {
	$sql2 = "SELECT idx, title FROM cf_product WHERE idx='$product_idx'";
}
else {
	$sql2 = "SELECT A.idx, A.title FROM cf_product A LEFT JOIN cf_product_container B ON A.idx=B.product_idx WHERE A.state IN(1, 2, 5) AND B.right_set_date='0000-00-00' ORDER BY A.idx DESC";
}
$res2 = sql_query($sql2);
$cnt2 = $res2->num_rows;
for ($i=0 ; $i<$cnt2 ; $i++) {
	$row2 = sql_fetch_array($res2);
?>
					<option value="<?=$row2['idx']?>" <?=$product_idx==$row2['idx']?"selected":""?>><?=$row2['title']?></option>
<?
}
?>
				</select>
			</td>
			<th>채권관리 리스트에 등록</th>
			<td >
				<label class="checkbox-inline" style="margin-left: 3px;"><input name="right_display" type="checkbox" value="Y" <?=$product_info['right_display']=="Y"?"checked":""?>> 등록</label>
				(체크된 상품만 리스트에 나옵니다.)
			</td>
			<th></th>
			<td></td>
		</tr>
		<tr>
			<th>상품명</th>
			<td><span id="txt_ttl"><?=$product_info['title']?></span></td>
			<th>상품정보</th>
			<td><span id="txt_info3"><?=$product_info['info3']?></span></td>
			<th>상품형태</th>
			<td><span id="txt_state"><?=$product_info['state_txt']?></span></td>
		</tr>
		<tr>
			<th>대출실행일</th>
			<td><span id="txt_loan_start"><?=$product_info['loan_start_date']?></span></td>
			<th>원금상환일(예정일)</th>
			<td>
				<span id="txt_loan_end">
				<?=$product_info['loan_end_date_txt']?>&nbsp;&nbsp;
				<?
				//if ($product_info['loan_end_date']<>"0000-00-00" and $product_info['loan_end_date']<>$product_info['loan_end_date_orig']) {
				if ($product_info['state']=="2") {
					echo "정상상환";
				} else if ($product_info['state']=="5") {
					echo "중도상환";
				} else {
				}
				?>
				</span>
			</td>
			<th>이자지급회차</th>
			<td><span id="txt_inter_txt"><?=$product_info['inter_txt']?></span></td>
		</tr>
		<tr>
			<th>권리설정일</th>
			<td><input type="text" name="right_set_date" id="right_set_date" class="form-control input-sm" style="width:110px;" value="<?=$product_info['right_set_date']?>" /></td>
			<th>헬로 live</th>
			<td><span id="txt_stream_url"><?=($product_info['stream_url1'] || $product_info['stream_url2']) ? 'live TV' : '';?></span></td>
			<th></th>
			<td>
			</td>
		</tr>
	</table>
	<table class="table table-bordered table-condensed" style="min-width:1200px">
		<tr>
			<th>권리설정 증빙자료 관리</th>
			<td>
				<input name="right_pic" type="file" style="float:left;margin-left:20px;">
				<input type="hidden" name="right_pic_ori" value="<?=$product_info['right_pic']?>" />
				<?=$product_info['right_pic']?>
				<? if($product_info['right_pic']) { ?>&nbsp; <input type="checkbox" name="right_pic_del" value="Y"> 삭제<? } ?>
			</td>
		</tr>
		<!--tr>
			<th>입금확인증 관리</th>
			<td>
				<input type="text" name="deposit_pic_ori" readonly class="input-sm" style="float:left;" value="<?=$product_info['deposit_pic']?>"/>
				<input type="checkbox" name="deposit_pic_del" value="Y" style="float:left;"><span style="float:left">삭제</span>
				<input name="deposit_pic" type="file" style="float:left;margin-left:20px;">
			</td>
		</tr-->
		<tr>
			<th><!--현장 사진 관리-->업데이트 증빙자료</th>
			<td>
				<input name="field_pic" type="file" style="float:left;margin-left:20px;">
				<input type="hidden" name="field_pic_ori" value="<?=$product_info['field_pic']?>" />
				<?=$product_info['field_pic']?>
				<? if($product_info['field_pic']) { ?>&nbsp; <input type="checkbox" name="field_pic_del" value="Y"> 삭제<? } ?>
			</td>
		</tr>
	</table>
</div>

<div class="text-center" style="margin-top: 10px;">
	<input class="btn btn-md btn-success" style="width: 80px;" onclick="go_submit()" type="button" value="저장">
	<a class="btn btn-md btn-default" style="width: 80px;" onclick="fmember_reset();" href="javascript">리셋</a>
</div>
</form>
<script>
function go_submit() {
	if(!confirm('저장 하시겠습니까?')){ return false; }
	var f = document.f_right;
	f.action_mode.value="right_update";
	f.submit();
}
function load_data(idx) {
	//idx = $('#idx').val();
	//alert(idx);
	var send_data = '{ "idx" : "'+idx+'" }';
	//alert(send_data);
	$.ajax({
		type: "POST",
		url: "/adm/product/ajax_get_product_info.php",
		data: {idx:idx},
		dataType: 'json',
		success: function(response, status, settings) {
			prd_disp(response);
		},
		error: function(xhr, status, error) {
			alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요.=n"+error+status+xhr);
		}
	});
}
function prd_disp(res) {
	$("#idx").val(res.idx);
	$("#txt_ttl").text(res.title);
	$("#txt_info3").text(res.info3);
	$("#txt_state").text(res.state_txt);
	$("#txt_loan_start").text(res.loan_start_date);
	if (res.loan_end_date) $("#txt_loan_end").text(res.loan_end_date);
	else $("txt_loan_end").text(res.loan_end_date_orig);
	$("#txt_inter_txt").text(res.inter_txt);
	$("#right_set_date").val(res.right_set_date);
	if (res.stream_url) $("#txt_stream_url").text("<a href='"+res.stream_url1+"' target=_blank>Live TV</a>");
}
</script>