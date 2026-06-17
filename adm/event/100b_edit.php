<?
include_once('./_common.php');

include_once(G5_ADMIN_PATH . '/admin.head.nomenu.php');

$ymd = $_GET["ymd"];

if (!$ymd) $newEvnt=true;

if ($_POST["mode"]=="update") {
	$up_sql = "update cf_event_10bM set
					product_name = '$product_name',
					prize = '$prize',
					funding_money = '$funding_money',
					stat = '$stat'
				where ymd = '$ymd'";
	sql_query($up_sql);
	?>
	<script>
	self.location.href="100b_edit.php?ymd=<?=$ymd?>";
	</script>
	<?
	die();
} else if ($_POST["mode"]=="new") {

	$chk_sql = "select count(*) chk_cnt from cf_event_10bM where ymd='$_POST[ymd]'";
	$chk_res = sql_query($chk_sql);
	$chk_row = sql_fetch_array($chk_res);
	if ($chk_row['chk_cnt']>0) {
		?>
		<script>
		alert("이미 등록된 일자입니다.");
		history.back();
		</script>
		<?
		die();
	}

	$ins_sql = "insert into cf_event_10bM set
					ymd='$_POST[ymd]',
					stat = '$_POST[stat]',
					product_name='$_POST[product_name]',
					prize = '$_POST[prize]'";
	sql_query($ins_sql);
	?>
	<script>
	alert("등록 완료");
	self.location.href="100b_edit.php?ymd=<?=$_POST['ymd']?>";
	</script>
	<?
	die();
}

$sql = "select * from cf_event_10bM where ymd='$ymd'";
$res = sql_query($sql);
$row = sql_fetch_array($res);

if ($row['confirm_frize']=="N") {
	echo "확정 안됨<br/>";
}

if (count($_POST["wlist"])>0) {
	for ($i=0 ; $i<count($_POST["wlist"]) ; $i++) {
		$in_arr .= $_POST["wlist"][$i].",";
	}
	$in_arr = substr($in_arr,0,-1);
	$up_sql = "update cf_event_10bS set win='Y' where idx in ($in_arr) and idx=1";
	$up_res = sql_query($up_sql);

	$up_sql = "update cf_event_10bM set confirm_prize='Y',prize_cnt='".count($_POST["wlist"])."' where ymd='$act_ymd'";
	sql_query($up_sql);
}

$ssql = "select S.*,A.mb_name from cf_event_10bS S LEFT JOIN g5_member A on(A.mb_no=S.mb_no) where S.ymd='$ymd' order by S.answer";
$sres = sql_query($ssql);
$scnt = $sres->num_rows;

$ii = 0 ;

for ($i=0 ; $i<$scnt ; $i++) {
	$srow = sql_fetch_array($sres);
	$app_list[$i] = $srow;
	if ($srow['win']=="Y") {
		$win_list[$ii] = $srow;
		$ii++;
	}
}

?>

<div id="wrapper2" style="display:block;clear;both;margin:20px 20px;min-width:200px;">

	<div id="container">

		<h1><?=$row['ymd']?></h1>

		<form name="f_main" method="post" class="form-horizontal">

		<input type="hidden" name="mode">

		<div>
			상품명 : <input type="text" name="product_name" value="<?=$row['product_name']?>" class="form-control input-sm" style="text-align:right;width:300px;display:inline;"/>
			<span style="margin-left:30px;">
			상태 : <select name="stat" class="form-control input-sm" style="width:100px;display:inline;">
					<option value="R" <?=$row["stat"]=="R"?"selected":""?>>중비중</option>
					<option value="I" <?=$row["stat"]=="I"?"selected":""?>>진행중</option>
					<option value="E" <?=$row["stat"]=="E"?"selected":""?>>종료</option>
				</select>
			</span>
		</div>

		<div style="margin-top:10px;">
			일자 : <input type="text" name="ymd" value="<?=$row["ymd"]?>" <?=$newEvnt?"":"readonly"?> class="form-control input-sm" style="text-align:right;width:120px;display:inline;" placeholder="20190101"/>

			<span style="margin-left:30px;">상금 : </span><input type="text" name="prize" value="<?=$row['prize']?>" class="form-control input-sm" style="text-align:right;width:120px;display:inline;"/>

			<span style="margin-left:30px;">펀딩확정금액 : </span><input type="text" name="funding_money" value="<?=$row['funding_money']?>" class="form-control input-sm" style="text-align:right;width:120px;display:inline;"/>

			<div style="margin-top:12px;text-align:center;">
			<?
			if ($ymd) {
				?>
				<input type="button" value="수정" class="btn btn-primary" onclick="go_save('E');"/>
				<?
			} else {
				?>
				<input type="button" value="신규" class="btn btn-primary" onclick="go_save('N');"/>
				<?
			}
			?>
			</div>
		</div>
		</from>
<?
if (!$newEvnt) {
	?>
		<div style="margin-top:10px;">
			당첨자 :
			<?
			if ($row['confirm_prize']=='Y') {
				for ($i=0 ; $i<count($win_list) ; $i++) {
				?>
				<div><?=$i+1?> <?=$win_list[$i]["mb_name"]?></div>
				<?
				}
			} else {
				?>
				<form method="post">
				<input type="hidden" name="act_ymd" value="<?=$ymd?>" />
				<input type="button" value="정답자 보기" class="btn btn-primary" onclick="get_win();" />
				<input type="button" value="정답자 확정" class="btn btn-primary" disabled/>
				<div id="winner">
				</div>
				</form>
				<?
			}
			?>
		</div>

		<div style="width:100%;">
		<h3 style="text-align:center;">응모자</h3>
		<table class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:12px;width:100%;">
		<?
		$dn = count($app_list);
		for ($i=0 ; $i<count($app_list) ; $i++) {
			?>
			<tr>
				<td><?=$dn--?></td>
				<td><?=$app_list[$i]["mb_id"]?></td>
				<td style="text-align:center;"><?=$app_list[$i]["mb_name"]?></td>
				<td style="text-align:right;"><?=number_format($app_list[$i]["answer"])?> 만원</td>
				<td style="text-align:center;"><?=$app_list[$i]["insert_datetime"]?></td>
			</tr>
			<?
		}
		?>
		</table>
		</div>

	<?
}
?>

	</div>

</div>

<script>
function go_save(gb) {

	var f = document.f_main;

	if (!f.ymd.value) {
		alert("일자는 필수 입력값입니다.");
		return;
	}

	if (gb=="E") {
		var yn = confirm("수정하시겠습니까?");
		if (!yn) return;

		f.mode.value="update";
		f.submit();
	} else if (gb=="N") {
		var yn = confirm("신규 등록하시겠습니까?");
		if (!yn) return;

		f.mode.value="new";
		f.submit();
	}
}
function get_win() {
<?
if (!$row['funding_money']) {
	?>
	alert("펀딩확정금액을 먼저 등록하셔야 합니다.");
	return;
	<?
}
?>
	$.ajax({
		url : 'ajax_100b_win.php',
		data : { 'target_ymd' : '<?=$ymd?>' },
		type : 'post',
		dataType : 'json',
		success : function(data) {
			console.log(data);
			if (data["win_cnt"]>0) {
				var insHtml = "";
				for (var i=0 ; i<data["win"].length ; i++) {
					insHtml += "<div>"+"<input type=checkbox name='wlist[]' value='"+ data["win"][i]["idx"] +"'>"+data["win"][i]["mb_id"]+"</div>";
				}
				$("#winner").html(insHtml);
			} else {
				alert("정답자가 없습니다.");
			}
		},
		error : function (jqXHR, textStatus, errorThrown) {
			//console.log(jqXHR);
			alert(jqXHR+"\n"+textStatus+"\n"+errorThrown);
		}
	});
}
</script>


<? include_once (G5_ADMIN_PATH . '/admin.tail.nomenu.php'); ?>