<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
include_once('./hellosetting.class.php');
// 팝업

	$SE		&=	$_GET["SE"];
	$SE2	&=	$_GET["SE2"];
	$num_per_page = 10;
	$page	= 1;

	IF(!$SE) { ECHO "접근이 올바르지 않습니다."; exit; }

	$intSeqName	=   "hcsseq";
	$strColumn	=	ARRAY(
							$intSeqName, "mb_id","hmseq","title","addr_si","addr_yn","addr_gu","rec_date","reg_date","recyn","hcssseq","ltvs","ltvl","ms","ml","period"
					);

	FOR($i=0;$i<COUNT($strColumn);$i++)
	{
		${$strColumn[$i]} = "";
	}

	$strTable	=	"
		(
			SELECT
			t1.hcsseq, t1.mb_id, t1.hmseq, t1.title, t1.addr_si, t1.addr_yn, t1.addr_gu, t1.rec_date,t1.reg_date, t1.recyn, t1.period, IFNULL(t2.hcssseq,0) as hcssseq, IFNULL(t2.ltvs,'') ltvs,IFNULL(t2.ltvl,'') ltvl,IFNULL(t2.ms,'') ms,IFNULL(t2.ml,'') ml
			FROM hloan_content_setting_history t1 LEFT JOIN hloan_content_setting_slave_history t2
			ON t1.seq = t2.seq WHERE t1.seq='".add_str($SE)."'
		) t1
	";
	$strWhere	=	"";
	$strOrder	=	$intSeqName.",ltvs";
	$intLimit1	=	0;
	$intLimit2	=	100;
	$intStrlen	=	100;

	$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

	IF($rowView[0][$intSeqName])
	{
		FOR($j=0;$j<COUNT($rowView);$j++)
		{
			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]}[$j] = $rowView[$j][$strColumn[$i]];
			}
		}
	} ELSE {
	   ECHO "접근이 올바르지 않습니다.";
	   EXIT;
	}

	$strHistoryList = $ClassHelloSetting->fn_setting_history($SE);

	$strInputText1		= "txt1";
	$strRadioText		= "txt";
	$strSelectBox		= "txt";
	$strPassword		= "txt";
?>

<!doctype html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta http-equiv="imagetoolbar" content="no">
<meta http-equiv="X-UA-Compatible" content="IE=10,chrome=1">
<title>업체관리 목록 | 헬로펀딩</title>
<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?ver=20180826">
<link rel="stylesheet" type="text/css" href="/adm/css/admin.css">
<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="/adm/css/bootstrap.min.css">
<!--[if lte IE 8]>
<script src="https://www.hellofunding.co.kr/js/html5.js"></script>
<![endif]-->
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
<script type="text/javascript" src="/js/jquery.menu.js"></script>
<script type="text/javascript" src="/js/common.js?v=20200619"></script>
<script type="text/javascript" src="/js/wrest.js"></script>
<script type="text/javascript" src="/adm/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.blockUI.js"></script>
<style>
input.radioarea {float:left;margin-top:7px;margin-left:10px;}
.selectarea {width:180px;padding:5px 0;}
label {float:left;display:block;padding:5px 5px;}
.fred {color:#ff0000;}
.fl {float:left;}
.cb {clear:both;}
.ul_guide {width:100%;list-style:none;}
.ul_guide > .li1 {width:20%;float:left;}
.input1 {width:100px;text-align:right;padding:0 7px;}
.input2 {width:100px;text-align:center;padding:0 7px;}
.bbtn {width:50px;text-align:center;color:#FFF;font-weight:bold;background-color:#0000FF;border-color:#0000FF}

.stable {width:100%;border:0px;border-collapse:collapse;}
.stable tr td.td01 {width:90%; border:0px;}
.stable tr td.td02 {width:10%;vertical-align:top;border:0px;}
#ltvarea {clear:both;width:100%;}
.w100 {width:100%;cursor:pointer;}
</style>
<script type="text/javascript" src="hellosetting.js?ver=<?php ECHO RAND(100000000,999999999);?>" /></script>
</title>
</head>
<body>
	<select name="addr_si" style="display:none;">
		<option value="<?php ECHO $addr_si[0];?>" /></option>
	</select>
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th scope="col">제목</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"title","","","required itemname='업체명' style='width:80%'",$title[0]);?></td>
		</tr>
		<tr>
			<th scope="col">조견업체</th>
			<td colspan="3"><?php ECHO fn_general_select($hmseq[0],$strSelectBox,$ClassHelloSetting->fn_hloan_member(),":조견업체:","hmseq","class='form-control input-sm' style='width:150px'","");?></td>
		</tr>
		<tr>
			<th scope="col">취급지역</th>
			<td colspan="3">
				<div class="fl">
					<?php ECHO fn_general_select($addr_si[0],$strSelectBox,$ClassHelloSetting->fn_addr_si(),":취급지역:","addr_si","class='form-control input-sm' style='width:150px'","");?>
				</div>
				<div class="fl">
					<?php ECHO fn_general_select($addr_yn[0],$strSelectBox,$ClassHelloSetting->fn_addr_yn(),":구분:","addr_yn","class='radioarea' OnClick=\"check_addr_yn(this.value,'');\"","");?>
				</div>
				<div class="cb"></div>
				<div id="addr_gu_area"></div>
			</td>
		</tr>
		<tr>
			<th scope="col">LTV 및 금리</th>
			<td colspan="3">
					<?php
						FOR($i=0;$i<COUNT($ltvs);$i++)
						{
					?>
						<div style='width:100%;padding:7px 0;'>
						<input type="hidden" name="SE2" value="<?php ECHO $hcssseq;?>" />
						LTV <?php ECHO INPUT_FORM($strInputText1,"ltvs[]","input1","","",$ltvs[$i]);?>% 이상
						<?php ECHO INPUT_FORM($strInputText1,"ltvl[]","input1","","",$ltvl[$i]);?>% 이하
						&nbsp;&nbsp;
						선순위 금리 <?php ECHO INPUT_FORM($strInputText1,"ms[]","input1","","",$ms[$i]);?>%
						후순위 금리 <?php ECHO INPUT_FORM($strInputText1,"ml[]","input1","","",$ml[$i]);?>%
						</div>
					<?php
						}
					?>
			</td>
		</tr>
		<tr>
			<th scope="col">적용일자</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"rec_date","input2 datepicker","","",$rec_date[0]);?></td>
		</tr>
		<tr>
			<th scope="col">적용</th>
			<td colspan="3"><?php ECHO fn_general_select($recyn[0],"txt",$ClassHelloSetting->fn_setting_recyn(),":적용:","recyn","class='radioarea'","");?></td>
		</tr>
		<tr>
			<th scope="col">대출기간</th>
			<td colspan="3"><?php ECHO INPUT_FORM($strInputText1,"period","input2 datepicker","","",$period[0]);?> 개월</td>
		</tr>
	</table>

	<script type="text/javascript">
	<!--
		var addr_yn = "<?php ECHO $addr_yn[0];?>";
		var addr_gu = "<?php ECHO $addr_gu[0];?>";
		itemcnt = 0;

		check_addr_yn_pop(addr_yn,addr_gu);
	//-->
	</script>

	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="top.self.close();" class="btn btn-default">창닫기</button>
	</div>
</body>
</html>