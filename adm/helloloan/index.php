<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
//include_once('../../lib/crypt.lib.php');

$sub_menu = '910300';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][3][1];

$g5['title'] = ($idx!='') ? $html_title.' 상세보기' : $html_title.' 목록';
$g5['title'] = "주택담보대출 심사";

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
?>

<style>
#paging_span { margin-top:10px;  text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:30px; padding:0 5px; color:#585657; line-height:30px; border:1px solid #d0d0d0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#284893; border-color:#284893; cursor:default }
</style>

<div class="tbl_head02 tbl_wrap">

<?php
	SWITCH($RD)
	{
		CASE "3"	:	// write ,update
		CASE "2"	:	// read
		$strKind			=	"save";
			$strBtnTxt			=	"등록하기";

			IF($RD == "2")
			{
				$strInputText1		= "txt1";
				$strRadioText		= "txt";
				$strSelectBox		= "txt";
				$strSelectBox2		= "";
				$strSelectBox3		= "label";
				$strPassword		= "txt";
				$strInputTextarea	= "txt2";
				$strInputFile		= "filetxt";
			} ELSEIF($RD == "3") {
				$strInputText1		= "text";
				$strRadioText		= "radio";
				$strSelectBox		= "";
				$strSelectBox2		= "txt";
				$strSelectBox3		= "";
				$strPassword		= "password";
				$strInputFile		= "file";
				$strInputTextarea	= "textarea";
			}

			$intSeqName	=	"hcseq";
			$strColumn	=	ARRAY(
									$intSeqName,"product_idx","laddr","pname","regist_number","pphone1","crating","comday",
									"pname_E_first","pname_E_last", "sale_per", "dambo_pname", "dambo_pphone" , "pcp_income",
									"pcp_job_group", "pcp_company", "loan_for", "hloan_end_date",
									"pcp_comp_addr_post", "pcp_comp_addr", "pcp_comp_addr2",
									"land_yn", "house_deposit", "hm_fees",
									"hholds","ddmoney","bsmoney","mdate","kbarea",
									"kbprice","kbllimit","kbcharter","examount","maxbond",
									"ltvmoney", "ltvmoney2" ,"ltvkind","rowner","tenant","content",
									"reg_date","ipaddr","recyn","hideyn","hmseq","cname","hname","hphone","mb_no","productyn",
									"mkind","loankind","loanother","vdate","hellobase", "add_hellobase",
									"hellofee","ifile","ifile_ori","honumber","kbquote","skind","fees",
									"kb_mg_id", "kb_ju_seri", "kb_mg_id2", "kb_ju_seri2", "d_code",
									"kb_mm_sil", "kb_mm_sil_date", "kb_date", "laddr_num"
							);

			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				${$strColumn[$i]} = "";
			}

			IF($idx)
			{
				$strVoteMember = hloan_admin_member_vote($idx,$connect_db);
/*
				$strTable	=	"
				(
					SELECT st1.*, st2.cname,st2.hname,st2.hphone FROM
					(SELECT ".$intSeqName.",product_idx,laddr,pname,crating,comday,hholds,ddmoney,bsmoney,mdate,
							kbarea,kbprice,kbllimit,kbcharter,examount,maxbond,ltvmoney,ltvkind,rowner,tenant,content,
							reg_date,ipaddr,recyn,hideyn,hmseq,mb_no,productyn,mkind,loankind,loanother,vdate,hellobase,
							hellofee,ifile,IFNULL(honumber,'') as honumber,IFNULL(kbquote,'') as kbquote,skind,fees,
							kb_mg_id, kb_ju_seri
					FROM hloan_content) st1 JOIN hloan_member st2 ON st1.hmseq=st2.hmseq
				) t1";
*/
				$strTable	=	"
				(
					SELECT st1.*, st2.cname,st2.hname,st2.hphone FROM
					(SELECT ".$intSeqName.",product_idx,laddr,pname, regist_number, pphone1, crating,comday,
							pname_E_first, pname_E_last, sale_per, dambo_pname, dambo_pphone, pcp_income,
							pcp_job_group, pcp_company, loan_for, hloan_end_date,
							pcp_comp_addr_post, pcp_comp_addr, pcp_comp_addr2,
							land_yn, house_deposit, hm_fees,
							hholds,ddmoney,bsmoney,mdate,
							kbarea,kbprice,kbllimit,kbcharter,examount,maxbond,ltvmoney,ltvmoney2,ltvkind,rowner,tenant,content,
							reg_date,ipaddr,recyn,hideyn,hmseq,mb_no,productyn,mkind,loankind,loanother,vdate,hellobase, add_hellobase,
							hellofee,ifile,ifile_ori,IFNULL(honumber,'') as honumber,IFNULL(kbquote,'') as kbquote,skind,fees,
							kb_mg_id, kb_ju_seri, kb_mg_id2, kb_ju_seri2, d_code,
							kb_mm_sil, kb_mm_sil_date, kb_date, laddr_num
					FROM hloan_content) st1 LEFT JOIN hloan_member st2 ON st1.hmseq=st2.hmseq
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
					$examountArr	=	EXPLODE(":",$examount);
					$maxbondArr		=	EXPLODE(":",$maxbond);

					IF($ifile)
					{
						$ifileArr	=	EXPLODE("^",$ifile);
					}

					if ($ifile_ori) $ifileArr_ori = explode("^", $ifile_ori);
					else $ifileArr_ori = $ifileArr;

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

			$strListUrl = "?S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&STXT=".$STXT."&page=".$page;

			if (!$d_code) {
				$d_sql = "SELECT d_code FROM hello_apt_kb WHERE mg_id='$kb_mg_id' AND ju_seri='$kb_ju_seri'";
				$d_row = sql_fetch($d_sql);
				$d_code = $d_row["d_code"];
			}

			include_once("detail.php");
			echo "<br /><br />\n";

		BREAK;
		CASE "1"	:		// list
		DEFAULT		:
			$num_per_page = 20;
			$intSeqName = "hcseq";
			$strColumn	= ARRAY($intSeqName,"product_idx","laddr","pname","ddmoney","mdate","recyn","reg_date","cname","mb_no","productyn","mb_name","ltvmoney","mkind","hellobase","honumber","vdate","CNT1","CNT2","CNT3","votyn","pphone1", "regist_number");
/*
			$strTable	= "
				(SELECT st1.*, st2.cname,IFNULL(st3.mb_name,'') as mb_name FROM
				(SELECT ".$intSeqName.",product_idx,laddr,pname,ddmoney,mdate,recyn,reg_date,hmseq,mb_no,productyn,ltvmoney,mkind,hellobase,IFNULL(honumber,'') as honumber,vdate
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=3 AND hcseq=t1.hcseq) as CNT1
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=2 AND hcseq=t1.hcseq) as CNT2
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=1 AND hcseq=t1.hcseq) as CNT3
				,(SELECT votyn FROM hloan_admin_member_vote WHERE midx=4 AND hcseq=t1.hcseq) as votyn
				FROM hloan_content t1
				)  st1 JOIN hloan_member st2 ON st1.hmseq=st2.hmseq LEFT JOIN g5_member st3 ON st1.mb_no=st3.mb_no

				) t1";
*/
			$strTable	= "
				(SELECT st1.*, st2.cname,IFNULL(st3.mb_name,'') as mb_name FROM
				(SELECT ".$intSeqName.",product_idx,laddr,pname,ddmoney,mdate,recyn,reg_date,hmseq,mb_no,productyn,ltvmoney,mkind,hellobase,IFNULL(honumber,'') as honumber,vdate, pphone1, regist_number, del
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=3 AND hcseq=t1.hcseq) as CNT1
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=2 AND hcseq=t1.hcseq) as CNT2
				,(SELECT COUNT(*) as CNT FROM hloan_admin_member_vote WHERE votyn=1 AND hcseq=t1.hcseq) as CNT3
				,(SELECT votyn FROM hloan_admin_member_vote WHERE midx=4 AND hcseq=t1.hcseq) as votyn
				FROM hloan_content t1
				)  st1 LEFT JOIN hloan_member st2 ON st1.hmseq=st2.hmseq LEFT JOIN g5_member st3 ON st1.mb_no=st3.mb_no

				) t1";

			$frQuery	= "";

			$strWhere = " WHERE del<>'Y' ";

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

			$total_page	=	$rowList[0];
			$total_count	=	$rowList[1];

			$qstr = "?S1=".$S1."&S2=".$S2."&S3=".$S3."&S4=".$S4."&STXT=".$STXT."&SC=".$SC;

			include_once("list.php");
		BREAK;
	}
?>

</div>

<script>
function go_new() {
	self.location.href="hloan_detail.php";
}
</script>

<?php include_once (G5_ADMIN_PATH.'/admin.tail.php'); ?>