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
$strColumn	= ARRAY($intSeqName,"laddr","pname","ddmoney","mdate","recyn","reg_date","cname","mb_no","productyn","mb_name","ltvmoney","mkind","hellobase","honumber","vdate","CNT1","CNT2","CNT3");
$strTable	= "
	(SELECT st1.*, st2.cname,IFNULL(st3.mb_name,'') as mb_name FROM
	(SELECT ".$intSeqName.",laddr,pname,ddmoney,mdate,recyn,reg_date,hmseq,mb_no,productyn,ltvmoney,mkind,hellobase,IFNULL(honumber,'') as honumber,vdate
	,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=3 AND hcseq=t1.hcseq) as CNT1
	,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=2 AND hcseq=t1.hcseq) as CNT2
	,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=1 AND hcseq=t1.hcseq) as CNT3
	FROM hloan_content t1
	)  st1 JOIN hloan_member st2 ON st1.hmseq=st2.hmseq LEFT JOIN g5_member st3 ON st1.mb_no=st3.mb_no

	) t1";


$frQuery	= "";

IF($S1) {
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " hmseq='".add_str($S1)."'";
}
IF($S3) {	// 물건담당자
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " mb_no='".add_str($S3)."'";
}
IF($S4) {	// 헬로펀딩상품
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	$strWhere .= " productyn='".add_str($S4)."'";
}
IF($STXT) {
	IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
	IF($S2)
	{
		$strWhere .= "(".$S2." LIKE '%".add_str($STXT)."%')";
	} ELSE {
		$strWhere .= "(laddr LIKE '%".add_str($STXT)."%' OR pname LIKE '%".add_str($STXT)."%')";
	}
}
IF($SC) {
	IF($SC <> "A")
	{
		IF($strWhere) {  $strWhere .= " AND "; } ELSE { $strWhere = " WHERE "; }
		$strWhere .= " recyn='".add_str($SC)."'";
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
		<thead>
		<tr>
			<th rowspan="2" style="text-align:center;width:60px;border:1px solid #AAA;">NO.</th>
			<th rowspan="2" style="text-align:center;width:100px;border:1px solid #AAA;">등록일자</th>
			<th rowspan="2" style="text-align:center;width:100px;border:1px solid #AAA;">여신회사</th>
			<th colspan="3" style="text-align:center;width:300px;border:1px solid #AAA;">담보물 주소</th>
			<th colspan="6" style="text-align:center;width:500px;border:1px solid #AAA;">대출정보</th>
			<th rowspan="2" style="text-align:center;width:60px;border:1px solid #AAA;">상태</th>
			<th colspan="3" style="text-align:center;width:180px;border:1px solid #AAA;">심사</th>
			<th colspan="2" style="text-align:center;width:200px;border:1px solid #AAA;">헬로펀딩</th>
		</tr>
		<tr>
			<th style="text-align:center;width:50px;border:1px solid #AAA;">주소1</th>
			<th style="text-align:center;width:50px;border:1px solid #AAA;">주소2</th>
			<th style="text-align:center;width:200px;border:1px solid #AAA;">상세주소</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">원차주명</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">희망대출금액<br />(원)</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">대출기간<br />(일,개월)</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">헬로펀딩<br />기준금리</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">기표희망일</th>
			<th style="text-align:center;width:100px;border:1px solid #AAA;">LTV</th>
			<th style="text-align:center;width:60px;border:1px solid #AAA;">가결</th>
			<th style="text-align:center;width:60px;border:1px solid #AAA;">부결</th>
			<th style="text-align:center;width:60px;border:1px solid #AAA;">감액</th>
			<th style="text-align:center;;border:1px solid #AAA;">호번</th>
			<th style="text-align:center;;border:1px solid #AAA;">상품</th>
		</tr>
		</thead>
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
			<td align="center" style="border:1px solid #AAA;"><?=$reg_date?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$cname?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$strAddr[0]?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$strAddr[1]?></td>
			<td align="left" style="border:1px solid #AAA;"><?=$strAddr[2]?></td>

			<td align="center" style="border:1px solid #AAA;"><?=$pname?></td>
			<td align="center" style="border:1px solid #AAA;"><?=f_number($ddmoney)?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_mdate_pro($mkind,$mdate)?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$hellobase?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$vdate?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_check_ltv($ltvmoney)?></td>

			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($recyn,fn_recyn())?></td>
			<td align="center" style="border:1px solid #AAA;"><?php ECHO $CNT1;?></td>
			<td align="center" style="border:1px solid #AAA;"><?php ECHO $CNT2;?></td>
			<td align="center" style="border:1px solid #AAA;"><?php ECHO $CNT3;?></td>
			<td align="center" style="border:1px solid #AAA;"><?=$honumber?></td>
			<td align="center" style="border:1px solid #AAA;"><?=fn_general_txt($productyn,fn_product_hello())?></td>
		</tr>
<?php
			$bunho--;
		}
	} ELSE {
?>

		<tr>
			<td colspan="19" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?php
	}
?>
	</table>
	<!-- 리스트 E N D -->