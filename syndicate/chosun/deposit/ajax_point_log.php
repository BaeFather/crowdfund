<?
include_once('_common.php');


if ($_SERVER["REQUEST_METHOD"]!="GET") { echo "ERROR-DATA"; exit; }
if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }


while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

$sql = "SELECT count(po_id) AS cnt_id FROM g5_point WHERE mb_id='".$member['mb_id']."'";
$row = sql_fetch($sql);
$affect_num = $row['cnt_id'];
if(!$page) $page = 1;
$size = (G5_IS_MOBILE) ? 5 : 10;

if($affect_num > 0) {

	if($page > ceil($affect_num / $size)) $page = ceil($affect_num / $size);
	$start_num = ($page - 1) * $size;

	$sql = "
		SELECT * FROM g5_point
		WHERE mb_id='{$member['mb_id']}'
		ORDER BY po_datetime DESC
		LIMIT $start_num, $size";
	$res = sql_query($sql);
	$rows = sql_num_rows($res);
	for($i=0; $i<$rows; $i++) {
		$LIST[$i] = sql_fetch_array($res);

		$LIST[$i]['gubun'] = ($LIST[$i]['po_point'] > 0) ? 'plus' : 'minus';
		if($LIST[$i]['po_point'] > 0) {
			$LIST[$i]['charge_amount'] = (int)$LIST[$i]['po_point'];
			$LIST[$i]['balance_amount'] = 0;
		}
		else {
			$LIST[$i]['charge_amount'] = 0;
			$LIST[$i]['balance_amount'] = (int)$LIST[$i]['po_point'];
		}
	}
	//print_rr($LIST, "font-size:11px");
}


if(G5_IS_MOBILE) {
	//include_once("ajax_point_log_m.php");
	//return;
}

?>

<style>
.tblX { width:100%; border:1px solid #ccc }
.tblX th, .tblX td { padding:0 4px 0 4px; border-left:1px solid #ccc; border-bottom:1px solid #ccc }
.btn_black_s { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#000000; border:0; vertical-align:middle; cursor:pointer; }
.btn_gray_s  { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#777; border-radius:3px; background-color:#CCCCCC; border:0; vertical-align:middle; cursor:pointer; }
.btn_red     { display:inline-block; padding:0 10px; line-height:22px; text-align:center; font-family:'NG'; font-size:12px; color:#fff; border-radius:3px; background-color:#FF6633; border:0; vertical-align:middle; cursor:pointer; }
.btn_red:hover, .btn_green:active { color:#fff; background-color:#FF2222; }
.btn_gray_s2 { display:inline-block; padding:0 10px; line-height:18px; text-align:center; font-family:'NG'; font-size:11px; color:#fff; border-radius:3px; background-color:#888888; border:0; vertical-align:middle; cursor:pointer; }
span.left  { float:left; }
span.right { float:right; }
</style>

<table class="tblX">
	<colgroup>
		<col width="7%">
		<col width="18%">
		<col width="10%">
		<col width="15%">
		<col width="">
		<col width="15%">
	</colgroup>
	<tbody>
		<tr>
			<th>No</th>
			<th>Date</th>
			<th>구분</th>
			<th>금액</th>
			<th>상세내용</th>
			<th>예치금잔액</th>
		</tr>
<?
if(count($LIST)) {

	$No = $affect_num - $size * ($page - 1);

	for($i=0; $i<count($LIST); $i++) {

		$fcolor = ($LIST[$i]['gubun']=='plus') ? '#153FA1' : '#FF3333';

?>
		<tr onMouseOver="this.bgColor='#F7F7F7'" onMouseOut="this.bgColor=''">
			<td style="text-align:center"><?=$No?></td>
			<td style="text-align:center"><?=preg_replace("/-/", ".", substr($LIST[$i]['po_datetime'], 0, 16))?></td>
			<td style="text-align:center;color:<?=$fcolor?>"><?=($LIST[$i]['gubun']=='plus')?'입금':'차감'?></td>
			<td style="text-align:right;color:<?=$fcolor?>"><?=number_format($LIST[$i]['po_point'])?>원</td>
			<td style="color:<?=$fcolor?>"><?=$LIST[$i]['po_content']?></td>
			<td style="text-align:right;color:#000"><?=number_format($LIST[$i]['po_mb_point'])?>원</td>
		</tr>
<?
		$No--;

	}
}
else {
?>
		<tr>
			<td colspan="10">검색된 데이터가 없습니다.</td>
		</tr>
<?
}
?>
	</tbody>
</table>

<div id="paging_span" class="mt10 mb20_2">
	<? paging($affect_num, $page, $size); ?>
</div>

<script type="text/javascript">
$('.mb20_2 .btn_paging').click(function(){
	$.ajax({
		url : "./ajax_point_log.php",
		type: "GET",
		data : {page : $(this).attr("data-page")},
		success: function(data, textStatus, jqXHR){
			if(data=="ERROR-DATA"){
				alert("시스템 에러입니다. 관리자에 문의해주세요.");
				return;
			}
			else if(data=="ERROR-LOGIN"){
				alert("로그인후 이용 가능 합니다.");
				return;
			}
			else{
				$("#money_status_area").empty();
				$("#money_status_area").html(data);
			}
		},
		error: function (jqXHR, textStatus, errorThrown)	{
			//
		}
	});
});
</script>

<?if($_COOKIE['debug_mode']) { echo "<div style='color:#FF6633;font-size:11px;'>".$_SERVER['PHP_SELF']."</div>"; } ?>