<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");

include_once('./_common.php');
include_once('../admin.loan.function.php');
include_once('../../lib/sms.lib.php');

// 팝업
$page	=	clean_xss_tags($_GET["page"]);
$S =	clean_xss_tags($_GET["S"]);	// section 1 일반 2 예약
IF(!$S) { $S = "1"; }

IF(!$page) { $page = 1; }
$num_per_page = 50;
$strlimit2 = $num_per_page;

$gstrFileBoardUrl = "/data/helloloan";

$strColumn = ARRAY("mb_id","mb_name","reg_date","ifile","idx");
$strQuery = "";
$strWhere = "WHERE section='".$S."'";
$strOrder = "idx desc";
$strTable = "hloan_content_smssend";

$qstr = "?S=".$S;

IF($S == "1")
{
	$strThTxt = "발송일자";
	$strBtnTxt = "문자발송";
	$intCols = "4";
} ELSEIF($S == "2") {
	$strThTxt = "예약일자";
	$strBtnTxt = "예약문자등록";
	$intCols = "5";
}

$rowList = fr_board_list($strColumn,$strTable,$strQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

$total_page	=	$rowList[0];
$total_count	=	$rowList[1];
?>
<html>
<head>
<title>문자발송</title>
<style>
	.title_area {font-size:30px;margin-bottom:14px;border-bottom:1px solid #CCC;padding-bottom:5px;}
	.stable {width:880px;border-collapse:collapse}
	.stable tr th {width:100px;text-align:center;padding:7px 0px;background-color:#e5ecef;border:1px solid #CCC;}
	.stable tr td {text-align:left;padding:7px 10px;border:1px solid #CCC;}

	.list_tarea {font-size:15px;padding:20px 0 5px 0;}

	.stable2 {width:880px;border-collapse:collapse}
	.stable2 tr th {font-size:13px;text-align:center;padding:7px 0px;background-color:#e5ecef;border:1px solid #CCC;}
	.stable2 tr td {font-size:13px;text-align:left;padding:7px 7px;border:1px solid #CCC;}
	.stable2 tr td.tdc {text-align:center;}
	.w120 {width:120px;text-align:center;}

</style>
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="helloloan.js?ver=<?php ECHO RAND(1000000000,9999999999);?>"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
</head>

<body>
<div class="title_area"><?php ECHO $strBtnTxt;?></div>
<form name="regfm" id="regfm" encType="multipart/form-data">
<input type="hidden" name="kind" value="save" />
<input type="hidden" name="page" value="<?php ECHO $page;?>" />
<input type="hidden" name="S" value="<?php ECHO $S;?>" />
<table class="stable">
<tr>
	<th>파일선택</th>
	<td>
		<input type="file" name="s_file0"><input type="button" name="fbtn" value="<?php ECHO $strBtnTxt;?>" OnClick="check_file_form('regfm','<?php ECHO $S;?>',event);">
	</td>
</tr>
<?php IF($S == "2") { ?>
<tr>
	<th>일/시 선택</th>
	<td>
			<input type="text" name="reg_date" class="datepicker w120"> 일
			<select name="reg_h">
<?php
				FOR($i=9;$i<20;$i++)
				{
					ECHO "<option value='".$i."'>".SPRINTF("%02d",$i)."</option>";
				}
?>
			</select>
			시
			<select name="reg_i">
<?php
				FOR($i=0;$i<60;$i+=5)
				{
					ECHO "<option value='".$i."'>".SPRINTF("%02d",$i)."</option>";
				}
?>
			</select>
			분
	</td>
</tr>
<?php } ?>
</table>
</form>


<div class="list_tarea">총 : <?php ECHO $total_count;?>  건</div>

<table class="stable2">
<tr>
	<col width="10%">
	<col>
	<col width="30%">
	<col width="30%">
	<?php IF($S == "2") { ?>
	<col width="10%">
	<?php } ?>
</tr>
<tr>
	<th>No</th>
	<th><?php ECHO $strThTxt;?></th>
	<th>등록파일</th>
	<th>관리자</th>
	<?php IF($S == "2") { ?>
	<th>기타</th>
	<?php } ?>
</tr>

<?php
	IF($rowList[1] > 0)
	{
		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);
			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLinkDownLoad = $gstrPHPSELF."?page=".$page;
		ECHO "<tr>
				<td class='tdc'>".$bunho."</td>
				<td class='tdc'>".$reg_date."</td>
				<td><a href='".$gstrFileBoardUrl."/".$ifile."'>".$ifile."</a></td>
				<td class='tdc'>".$mb_id."(".$mb_name.")</td>";
				IF($S == "2")
				{
					ECHO "<td class='tdc'><a href='#none' OnClick=\"check_form_del('delform','".$idx."')\">[삭제]</a></td>";
				}
			  ECHO "</tr>";
			  $bunho--;
		}
	} ELSE {
		ECHO "<tr>
				<td colspan='".$intCols."' class='tdc'>등록된 파일이 없습니다.</td>
			  </tr>";
	}
?>
</table>

<form name="delform" id="delform">
	<input type="hidden" name="kind" value="del" />
	<input type="hidden" name="S" value="<?php ECHO $S;?>" />
	<input type="hidden" name="SE" id="SE" value="" />
</form>
<?php
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, $qstr.'&amp;page=');
?>

<script src="https://www.hellofunding.co.kr/adm/admin.js"></script>
<script>
$(function(){
	$(".datepicker").datepicker({
		dateFormat      : 'yy-mm-dd',
		changeYear      : true,
		changeMonth     : true,
		monthNamesShort : ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월'],
		dayNamesMin     : ['일' ,'월', '화', '수', '목', '금', '토']
	});
});
</script>
</body>
</html>
