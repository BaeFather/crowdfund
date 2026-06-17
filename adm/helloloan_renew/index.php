<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '910400';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][3][1];

$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';

// 받은 데이터를 변수화
foreach($_REQUEST as $k=>$v) { ${$_REQUEST[$k]} = $v; }

$qstr = $_SERVER['QUERY_STRING'];
if($idx) {
	$qstr = preg_replace("/&idx=([0-9]){1,10}/", "", $qstr);
}
if($page) {
	$qstr = preg_replace("/&page=([0-9]){1,10}/", "", $qstr);
}

$countUp = false;
if($idx && $mode!='download') {
	if($_COOKIE['loan_request_view']) {
		$VIEW_IDX = explode(",", $_COOKIE['loan_request_view']);
		if(!in_array($idx, $VIEW_IDX)) {
			$addIdx = $_COOKIE['loan_request_view'] . "," . $idx;
			setcookie("loan_request_view", $addIdx, strtotime(date('Y-m-d')." 23:59:59"), "/");
			$countUp = true;
		}
	}
	else {
		setcookie("loan_request_view", $idx, strtotime(date('Y-m-d')." 23:59:59"), "/");
		$countUp = true;
	}
}

include_once (G5_ADMIN_PATH.'/admin.head.php');

FUNCTION fn_auction()
{
	$retval = ARRAY(
					ARRAY("Y","예"),
					ARRAY("N","아니오")
			  );
	return $retval;
}

FUNCTION fn_arecyn()
{
	$retval = ARRAY(
					ARRAY("2","대출신청"),
					ARRAY("3","신청취소")
			  );
	return $retval;
}
?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">
<?php
	$gstrFileBoardUrl = "/data/helloloan";

	SWITCH($RD)
	{
		CASE "3"	:	// write ,update
		CASE "2"	:	// read
		$strKind			=	"save";
			$strBtnTxt			=	"등록하기";

			$strInputText1		= "text";
			$strInputText2		= "txt1";
			$strRadioText		= "radio";
			$strSelectBox		= "txt";
			$strSelectBox2		= "";
			$strSelectBox3		= "";
			$strPassword		= "password";
			$strInputFile		= "file";
			$strInputTextarea	= "textarea";

			$intSeqName	=	"hcseq";
			$strColumn	=	ARRAY(
									$intSeqName, "hnum","si","gu","dg","jibun","aptname","aptarea",
									"floor","dong","ho","ddmoney","maxbond","loankind",
									"auctionyn","fees","ifile","kbmoney","lenmember",
									"lenphone","lenother","promember","prophone","proother",
									"okmoney","ltv","Interest","feesmoney","auth_date","reg_date","len_date","okddmoney","arecyn","recyn","productyn","honumber",
									"votyn","votdate","skind","bcode","loan_over","loan_jumin","purpose","smsyn",
									"conditions","sfile","pidx","cname","hname","hphone","recyn_other","recyn_other2","mb_no","okmaxbond","okloankind","okauctionyn","okkbmoney","okfeesmoney","okfees","okltv","okInterest","aptcrdate","atptot","loan_addr","other","oname","content","hmseq2","seq"
							);

			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($idx)
			{
				$strTable	=	"
				(
					SELECT st1.*, st2.cname,st2.hname,st2.hphone FROM
					(SELECT ".$intSeqName.",hnum,si,gu,dg,aptname,aptarea,floor,dong,jibun,ho,ddmoney,maxbond,loankind,auctionyn,fees,ifile,kbmoney,lenmember,lenphone,lenother,promember,prophone,proother,okmoney,ltv,Interest,feesmoney,auth_date,reg_date,len_date,okddmoney,arecyn,recyn,hideyn,hmseq,mb_no,productyn,honumber,votyn,votdate,skind,bcode,okInterest,okltv,
					okfees,loan_sdate,loan_edate,loan_over,loan_jumin,loan_addr,
					purpose,smsyn,conditions,sfile,pidx,recyn_other,recyn_other2,okmaxbond,
					okloankind,okauctionyn,okkbmoney,okfeesmoney,aptcrdate,atptot,other,oname,content,hmseq2,seq
					FROM hloan_content_renew) st1 JOIN hloan_member_renew st2 ON st1.hmseq=st2.hmseq
				) t1";

				$strWhere	=	" WHERE hcseq='".add_str($idx)."'";
				$strOrder	=	$intSeqName;
				$intLimit1	=	0;
				$intLimit2	=	1;
				$intStrlen	=	100;

				$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

				IF($rowView[0][$intSeqName])
				{
					FOR($i=0;$i<COUNT($strColumn);$i++)
					{
						${$strColumn[$i]} = $rowView[0][$strColumn[$i]];
					}
					$aptnameArr	=	EXPLODE(",",$aptname);
					$aptareaArr		=	EXPLODE(",",$aptarea);
					$sfileArr	=	EXPLODE("^",$sfile);

					$maxbond		=	EXPLODE(",",$maxbond);
					$okmaxbond		=	EXPLODE(",",$okmaxbond);

					FOR($k=0;$k<COUNT($maxbond);$k++)
					{
						IF($maxbond[$k] > 0)
						{
							$maxbond[$k] = f_number($maxbond[$k]);
						}
					}


					IF($mdate == 0) { $mdate = ""; }

					$strLevClass = "";
					IF($ltvmoney > 80) { $strLevClass = " fred"; }
				} ELSE {
					alert_back("접근이 올바르지 않습니다","-1");
					EXIT;
				}

				$strKind			=	"update";
				$strBtnTxt			=	"수정하기";
			}

			$strListUrl = "?S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&S5=".$S5."&Sdate=".$Sdate."&Edate=".$Edate."&STXT=".$STXT."&page=".$page;

			SWITCH($SD)	// 페이지 분리
			{
				CASE "1" : $strSubUrl = "detail_01.php"; BREAK;
				CASE "2" :

					IF($arecyn == 7)	// 최종승인 이라면 승인값 출력
					{
						$ddmoney	=	$okddmoney;
						$loankind	=	$okloankind;
						$auctionyn	=	$okauctionyn;
						$fees		=	$okfees;
						$kbmoney	=	$okkbmoney;
						$Interest	=	$okInterest;
						$ltv		=	$okltv;
						$feesmoney	=	$okfeesmoney;

						FOR($k=0;$k<COUNT($okmaxbond);$k++)
						{
							IF($okmaxbond[$k] > 0)
							{
								$maxbond[$k] = f_number($okmaxbond[$k]);
								$intTotokMaxBond     += replace_integer($okmaxbond[$k]);
							}
						}
					}

					$dongVal	=	EXPLODE(",",$dong);
					$strSubUrl = "detail_02.php";
				BREAK;
				CASE "3" :
					$fnLimitSelect = new Limit_Select();
					IF($okddmoney)
					{
						$retobj = $fnLimitSelect->fn_limit_select($okddmoney,$intTotokMaxBond, $okfees, $okloankind,($okkbmoney/10000),$okauctionyn, $hmseq2, $si, $gu );

						// 담보 여유금액 (kb시세 -희망대출금액 -선순위채권금액

						$strLastLtv = $kbmoney - $okddmoney- $intTotokMaxBond;
					}

					$strSubUrl = "detail_03.php";
				BREAK;
				DEFAULT : $strSubUrl = "detail_01.php"; BREAK;
			}

			include_once("detail.php");

			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
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
						$S2Val = "concat(si,gu,dong,aptname,dg,'동 ',floor,'층 ',ho,'호')";
					} ELSE {
						$S2Val = $S2;
					}
					$strWhere .= "(".$S2Val." LIKE '%".add_str($STXT)."%')";
				} ELSE {
					$strWhere .= "(concat(si,gu,dong,aptname,dg,'동 ',floor,'층 ',ho,'호')  LIKE '%".add_str($STXT)."%' OR lenmember LIKE '%".add_str($STXT)."%' OR hnum LIKE '%".add_str($STXT)."%')";
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

			$total_page	=	$rowList[0];
			$total_count	=	$rowList[1];

			$qstr = "?S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&S5=".$S5."&STXT=".$STXT."&Sdate=".$Sdate."&Edate=".$Edate;

			include_once("list.php");
		BREAK;
	}
?>

</div>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>