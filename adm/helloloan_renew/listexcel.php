<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$S1		=	$_GET["S1"];
$S2		=	$_GET["S2"];
$S3		=	$_GET["S3"];
$S4		=	$_GET["S4"];
$SC		=	$_GET["SC"];
$STXT	=	$_GET["STXT"];

header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=여신회사주담대리스트".DATE("YmdHis").".xls");  //File name extension was wrong
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false);

$num_per_page = 10000000;
$intSeqName = "hcseq";
$strColumn	= ARRAY($intSeqName,"hnum","si","gu","dg","jibun","aptname","aptarea","dong","floor","ho","ddmoney","maxbond","loankind","recyn","arecyn","lenmember","lenphone","lenother","okddmoney","okInterest","loan_sdate","loan_edate","loan_over","okfees","cname","mb_name","len_date","mb_no");

$strTable	= "
	(SELECT st1.*, st2.cname,IFNULL(st3.mb_name,'') as mb_name,phmseq FROM hloan_content_renew st1 LEFT JOIN hloan_member_renew st2 ON st1.hmseq=st2.hmseq LEFT JOIN g5_member st3 ON st1.mb_no=st3.mb_no WHERE LEFT(st1.votdate,10) <> '0000-00-00'
	) t1";


$frQuery	= "";

IF($S1) {	// 물건순위
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " loankind='".add_str($S1)."'";
}
IF($S3) {	// 헬로 진행상황
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " recyn='".add_str($S3)."'";
}
IF($S4) {	// 중개법인
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " phmseq='".add_str($S4)."'";
}
IF($S5) {	// 심사 진행사항
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " arecyn='".add_str($S5)."'";
}
IF($STXT || $S2) {
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	IF($S2)
	{
		IF($S2 == "laddr")
		{
			$S2Val = "concat(si,gu,dong,aptname,dong,'동 ',floor,'층 ',ho,'호')";
		} ELSE {
			$S2Val = $S2;
		}
		$strWhere .= "(".$S2Val." LIKE '%".add_str($STXT)."%')";
	} ELSE {
		$strWhere .= "(concat(si,gu,dong,aptname,dong,'동 ',floor,'층 ',ho,'호')  LIKE '%".add_str($STXT)."%' OR lenmember LIKE '%".add_str($STXT)."%' OR hnum LIKE '%".add_str($STXT)."%')";
	}
}

IF($Sdate || $Edate)
{
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }

	IF($Sdate && $Edate)
	{
		$strWhere .= "(LEFT(reg_date,10)>='".$Sdate."' AND LEFT(reg_date,10)<='".$Edate."')";
	} ELSE {
		IF($Sdate)
		{
			$strWhere .= "LEFT(reg_date,10)>='".$Sdate."'";
		} ELSEIF($Edate) {
			$strWhere .= "LEFT(reg_date,10)<='".$Edate."'";
		}
	}
}

$strOrder	=	$intSeqName." DESC";
$strlimit2	=	$num_per_page;

IF(!$page) { $page = 1; }

$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect);

?>
<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
input.radioarea {float:left;margin-top:7px;margin-left:10px;border:1px solid #ff0000;}
label {float:left;display:block;padding:0px 5px;}
</style>
	<!-- 리스트 START -->
	<table style="max-width:1800px;border-collapse:collapse;font-size:12px;">
		<caption style="padding:0"><?=$g5['title']?> 목록</caption>
		<thead>
		<tr>
			<th rowspan="2" style="text-align:center;width:60px;border:1px solid #AAA;">NO.</th>
			<th colspan="7" style="text-align:center;border:1px solid #AAA;">기본정보</th>
			<th rowspan="2" style="text-align:center;border:1px solid #AAA;width:100px;">1차 심사</th>
			<th rowspan="2" style="text-align:center;border:1px solid #AAA;width:100px;">헬로 심사</th>
			<th rowspan="2" style="text-align:center;border:1px solid #AAA;width:100px;">심사자</th>
			<th colspan="3" style="text-align:center;border:1px solid #AAA;width:100px;">원차주</th>
			<th colspan="4" style="text-align:center;border:1px solid #AAA;">대출정보</th>
			<!--<th rowspan="2" style="text-align:center;">기타</th>//-->
		</tr>
		<tr>
			<th style="text-align:center;border:1px solid #AAA;width:60px;">접수번호</th>
			<th style="text-align:center;border:1px solid #AAA;width:80px;">상품호번</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">중개법인</th>
			<th style="text-align:center;border:1px solid #AAA;width:50px;">주소1</th>
			<th style="text-align:center;border:1px solid #AAA;width:50px;">주소2</th>
			<th style="text-align:center;border:1px solid #AAA;width:200px;">상세주소</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">물건순위</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">이름</th>
			<th style="text-align:center;border:1px solid #AAA;width:150px;">등록일</th>
			<th style="text-align:center;border:1px solid #AAA;width:150px;">연락처</th>
			<th style="text-align:center;border:1px solid #AAA;width:150px;">대출금액</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">금리</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">대출기간</th>
			<th style="text-align:center;border:1px solid #AAA;width:100px;">수수료율</th>
		</tr>
		</thead>
		<tbody>
		<tbody>
<?php
	IF($rowList[1] > 0)
	{
		$intDdmoneySum = 0;
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$intDdmoneySum += $ddmoney;
		}
?>
		<tr>
			<th colspan="7">합 계</th>
			<th colspan="11" style="text-align:left;letter-spacing:0px;font-size:13px;padding-left:15px;border:1px solid #AAA;"><?php ECHO f_number($intDdmoneySum);?></th>
		</tr>
<?php
		$bunho=($rowList[1])-(($page-1) * $num_per_page); //리스트의 넘버수
		FOR($i=0;$i<COUNT($rowList[2]);$i++)
		{
			unset($RowLink);

			FOR($j=0;$j<COUNT($strColumn);$j++)
			{
				${$strColumn[$j]} = $rowList[2][$i][$j];
			}
			$RowLink = $gstrPHPSELF."?KD=".$KD."&RD=2&SE=".$hcseq."&page=".$page."&S1=".$S1."&S2=".$S2."&STXT=".$STXT."&SC=".$SC;

			$strAddr = fn_check_addr($laddr);
?>
		 <tr>
			<td align="center" style="border:1px solid #AAA;"><?=$bunho?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$hnum?></td>
			<td align="center" style="border:1px solid #AAA;"></td>
			<td align="center" style="border:1px solid #AAA;"><?=$cname?></td>
			<td align="center" style="border:1px solid #AAA;"><?php ECHO $si?></td>
			<td align="center" style="border:1px solid #AAA;"><?php ECHO $gu?></td>
			<td align="left" style="border:1px solid #AAA;"><?=$dongArr[1]?> <?php ECHO fn_loan_name_replace($aptname,",")?> <?php ECHO $jibun;?> <?php ECHO $dong?>동 <?php ECHO $floor?>층 <?php ECHO $ho?>호 (<?php ECHO fn_loan_name_replace($aptarea,",")?> ㎡)</td>

			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($loankind,fn_loankind())?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($arecyn,fn_loan_arecyn())?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($recyn,fn_hellloan_search_kind_renew())?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($mb_no,hloan_admin_member($connect_for))?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$lenmember?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$len_date?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$lenphone?></td>

			<td align="center" style="border:1px solid #AAA;"><?=f_number($okddmoney)?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$okInterest?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$loan_sdate?>~<?=$loan_edate?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$okfees?></td>
		</tr>

<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="20" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->