<?
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$html_title = "HYPHEN log";
$g5['title'] = $html_title;

//include_once (G5_ADMIN_PATH.'/admin.head.php');
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) { ${$_GET[$k]} = $v; }

error_reporting(E_ALL^ E_WARNING ^ E_NOTICE); 

?>
<div id="wrapper">

	<div id="container">

		<div class="tbl_head02 tbl_wrap">

			<table class="table table-striped table-bordered table-hover floatThead-table" style="border-collapse: collapse; border-width: 1px 1px 10px; border-style: solid; border-color: rgb(221, 221, 221); border-image: initial; display: table; width: 1863px; margin: 0px; table-layout: fixed;"><colgroup>

			<?
			$sql = "SELECT * FROM hyphen_request_log ORDER BY idx DESC LIMIT 100";
			$res = sql_query($sql);
			$cnt = $res->num_rows;

			for ($i=0 ; $i<$cnt ; $i++) {

				$row = sql_fetch_array($res);

				$rarr=array();$rmsg="";

				$rarr = json_decode($row["res_body"] , true);
				//echo "<pre>";print_r($rarr); echo "</pre>";

				if (array_key_exists("name", $rarr)) $rmsg = $rarr["name"]."<br/>";
				if (array_key_exists("errMsg", $rarr["outH0001"])) $rmsg = $rmsg.$rarr["outH0001"]["errMsg"]."<br/>";

				$rmsg = substr($rmsg, 0 , -5);

				$hl = "";
				if ($row["mod_datetime"] AND $row["ins_datetime"]) $hl = strtotime($row["mod_datetime"]) - strtotime($row["ins_datetime"]);
				?>
				<tr>
					<td><?=$row["idx"]?></td>
					<td><?=$row["apiTitle"]?></td>
					<td style="text-align:center;">
						<a onclick="go_detail('<?=$row[idx]?>');" style="cursor:pointer;">
						<?=$row["rdate"]?> <?=$row["rtime"]?></a>
					</td>
					<td style="text-align:center;"><?=$row["op"]?></td>
					<td><?=$row["resMsg"]?></td>
					<td><?=$rmsg?></td>
					<td style="text-align:right;"><?=$hl?> 초</td>
				</tr>
				<?
			}
			?>

			</table>

		</div>
	</div>

</div>

<script>
function go_detail(idx) {
	var ww = 800;
	var wh = 800;

	var top  = ($(window).height()/2)-(wh/2);
	var left = ($(window).width()/2)-(ww/2);

	window.open('hyphen_log_detail.php?idx='+idx,'hyphen_det','top='+top+',left='+left+',width='+ww+',height='+wh+',toolbar=0,menubar=0,status=0,scrollbars=yes,resizable=yes');
}
</script>