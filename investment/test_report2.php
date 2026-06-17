<?
################################################################################
## 2022-05-24 상품요약보고서 테스트
################################################################################

include_once('_common.php');

include_once(G5_LIB_PATH.'/sms.lib.php');
?>
<?
$today = $_REQUEST["today"];
if (!$today) $today = date("Y-m-d");

$sql = "
	SELECT
		A.idx, A.gr_idx, A.category, A.title,
		A.recruit_amount, A.live_invest_amount AS total_invest_amount,
		A.invest_return, A.invest_period, A.invest_usefee,
		A.open_datetime, A.start_datetime, A.end_datetime, A.recruit_period_start, A.recruit_period_end,
		A.advance_invest, A.advance_invest_ratio, A.platform, A.only_vip, A.vip_mb_no
	FROM
		cf_product A
	WHERE 1=1
		AND A.category = '3'
	ORDER BY A.idx DESC LIMIT 1";

$PRDT = sql_fetch($sql);


IF($PRDT["category"]=="3")
{
	echo "<br/>확정매출채권 일일통계<br/>";
	
	$chk_so_sql = "SELECT COUNT(*) scf_not_end FROM cf_product WHERE category='3' AND start_date='$today' AND invest_end_date=''";
	$chk_so_row = sql_fetch($chk_so_sql);
	if ($chk_so_row["scf_not_end"]==0) {
		
		
		$report_idx = fn_cf_product_admin_report_scf($today);		// 리포트 데이터 생성
		//$report_idx = "1467";
		fn_hello_status_smssend_scf($report_idx);			// SMS전송
	}
}

// 캐시파일 초기화
//@unlink(G5_DATA_PATH."/cache/productList-active.php");
//@unlink(G5_DATA_PATH."/cache/productList-latest.php");

?>
<?
function fn_hello_status_smssend_scf($report_idx) {

	$_admin_sms_number = "15886760";

	$intTime = TIME();
	$dtmH	 = DATE("H");
	$strSendYn = "";

	IF($dtmH >= "00" AND $dtmH <= "06") {
		$strSendYn = "1";	// SMS 발송대기 플래그
	}

	$Query = "SELECT pidx, title, product, content,reg_time FROM cf_product_admin_report WHERE pidx='".$report_idx."'";
	$Result = sql_query($Query);

	$i = 0;

	if ($Row=sql_fetch_array($Result)) {

		UNSET($sms_msg);

		$pidx		=	$Row["pidx"];
		$title		=	$Row["title"];
		$product	=	stripslashes($Row["product"]);
		$content	=	stripslashes($Row["content"]);
		$reg_time	=	$Row["reg_time"];

		//$Qm = "SELECT midx, cphone FROM cf_product_admin_user WHERE recyn='Y'";
		$Qm = "SELECT midx, cphone FROM cf_product_admin_user WHERE cphone='010-8624-6176' ";
		$Rm = sql_query($Qm);

		$i = 0;

		WHILE($Rowm=sql_fetch_array($Rm)) {

			$midx		=	$Rowm["midx"];
			$cphone		=	$Rowm["cphone"];

			$Q2 = "INSERT INTO cf_product_admin_report_send
				   (pidx,midx,send_time,reg_time,end_time,ipaddr,sendyn)
				   VALUES
				   ('".$pidx."','".$midx."',".$intTime.",0,0,'','".$strSendYn."');";

			sql_query($Q2);

			ECHO $_admin_sms_number."--".$title."--".$product."--".$cphone."--".$midx."--".$intTime."--".DATE("Y-m-d H:i:s",TIME())."--".DATE("Y-m-d H:i:s",(TIME()+600))."<BR>";

			$sms_msg = $title."\n\n";
			//$sms_msg .= $product."\n\n";
			$sms_msg .= "https://www.hellofunding.co.kr/hello_report/scf_report.php?RT=".$intTime.$midx;

			$cphone = "010-8624-6176";  // 전승찬
			//$cphone = "010-8894-4740";  // 이상규
			IF($strSendYn == "")	// 새벽시간에는 발송하지 않음.   cron설정 /home/crowdfund/schedule_work/hello_status_smssend_recommend.php 오전7시 실행  sendyn이 1인것만
			{
				////unit_sms_send($_admin_sms_number, $cphone, $sms_msg, DATE("Y-m-d H:i:s",$intTime+600));
				unit_sms_send($_admin_sms_number, $cphone, $sms_msg);
			}
			//unit_sms_send($_admin_sms_number, $cphone, $sms_msg);
			$i++;
		}

		IF($i > 0) {
			sql_free_result($Rm);
		}
		sql_free_result($Result);
	}

}
function fn_cf_product_admin_report_scf($today) {


	if (!$today) $today = date("Y-m-d");

	$chk_sql = "SELECT COUNT(*) scf_not_end FROM cf_product WHERE category='3' AND start_date='$today' AND invest_end_date=''";
	$chk_row = sql_fetch($chk_sql);

	$psql = "SELECT SUM(recruit_amount) sum_recruit_amount, count(idx) sum_idx FROM cf_product WHERE  category='3' AND start_date='$today'";
	$prow = sql_fetch($psql);
	$recruit_total_amount = $prow["sum_recruit_amount"];
	$recruit_total_prd = $prow["sum_idx"];

	$s_sql = "SELECT count";

	$sql = "
		SELECT
			B.mb_id, B.mb_name, B.mb_co_name, B.member_type, B.member_investor_type,
			A.idx as inv_idx, A.member_idx, A.amount, A.is_advance_invest, A.syndi_id AS flatform_id, A.first_inv,
			(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_count,
			(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_amount,
			(SELECT is_auto_invest FROM cf_product_invest_detail WHERE invest_idx=A.idx ORDER BY idx DESC LIMIT 1) AS is_auto_invest,
			(SELECT amount FROM cf_product_invest_detail WHERE invest_idx=A.idx AND is_auto_invest='1') AS auto_invest_amount
		FROM
			cf_product_invest A
		LEFT JOIN
			g5_member B  ON A.member_idx = B.mb_no
		LEFT JOIN
			cf_product C  ON A.product_idx = C.idx
		WHERE (1)
			AND C.category='3' AND C.start_date='$today'
			AND A.invest_state='Y'
			$where_plus
		ORDER BY
			A.amount DESC";
	//echo $sql;
	$res  = sql_query($sql);
	$rows = sql_num_rows($res);

	for($i=0; $i<$rows; $i++) {

		$LIST[$i] = sql_fetch_array($res);

		////////////////////////////////////
		// 전체 현황
		////////////////////////////////////
		$TOTAL['COUNT'] += 1;
		$TOTAL['AMOUNT'] += $LIST[$i]['amount'];
		if($LIST[$i]['is_auto_invest']=='1') {
			$TOTAL['AUTO_INVEST_AMOUNT'] += $LIST[$i]['auto_invest_amount'];
		}

		if($LIST[$i]['member_type']=='2') {
			$TOTAL['M2_COUNT'] += 1;
			$TOTAL['M2_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL['M1_COUNT'] += 1;
			$TOTAL['M1_AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_investor_type']=='2') {
				$TOTAL['M12_COUNT'] += 1;
				$TOTAL['M12_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['member_investor_type']=='3') {
				$TOTAL['M13_COUNT'] += 1;
				$TOTAL['M13_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL['M11_COUNT'] += 1;
				$TOTAL['M11_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

		if($LIST[$i]['flatform_id']=='finnq') {
			$TOTAL['M3_COUNT'] += 1;
			$TOTAL['M3_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='hktvwowstar') {
			$TOTAL['M32_COUNT'] += 1;
			$TOTAL['M32_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='chosun') {
			$TOTAL['M33_COUNT'] += 1;
			$TOTAL['M33_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='oligo') {
			$TOTAL['M34_COUNT'] += 1;
			$TOTAL['M34_AMOUNT'] += $LIST[$i]['amount'];
		}

		////////////////////////////////////
		// 최초 투자자 현황 데이터
		////////////////////////////////////
		//if($LIST[$i]['total_invest_count']==1) {
		if($LIST[$i]['first_inv']=="Y") {

			$TOTAL_A['COUNT'] += 1;
			$TOTAL_A['AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_type']=='2') {
				$TOTAL_A['M2_COUNT'] += 1;
				$TOTAL_A['M2_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_A['M1_COUNT'] += 1;
				$TOTAL_A['M1_AMOUNT'] += $LIST[$i]['amount'];

				if($LIST[$i]['member_investor_type']=='2') {
					$TOTAL_A['M12_COUNT'] += 1;
					$TOTAL_A['M12_AMOUNT'] += $LIST[$i]['amount'];
				}
				else if($LIST[$i]['member_investor_type']=='3') {
					$TOTAL_A['M13_COUNT'] += 1;
					$TOTAL_A['M13_AMOUNT'] += $LIST[$i]['amount'];
				}
				else {
					$TOTAL_A['M11_COUNT'] += 1;
					$TOTAL_A['M11_AMOUNT'] += $LIST[$i]['amount'];
				}
			}

			if($LIST[$i]['flatform_id']=='finnq') {
				$TOTAL_A['M3_COUNT'] += 1;
				$TOTAL_A['M3_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='hktvwowstar') {
				$TOTAL_A['M32_COUNT'] += 1;
				$TOTAL_A['M32_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='chosun') {
				$TOTAL_A['M33_COUNT'] += 1;
				$TOTAL_A['M33_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='oligo') {
				$TOTAL_A['M34_COUNT'] += 1;
				$TOTAL_A['M34_AMOUNT'] += $LIST[$i]['amount'];
			}

		}

		////////////////////////////////////
		// 기존 투자자 현황 데이터
		////////////////////////////////////
		else {

			$TOTAL_B['COUNT'] += 1;
			$TOTAL_B['AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_type']=='2') {
				$TOTAL_B['M2_COUNT'] += 1;
				$TOTAL_B['M2_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_B['M1_COUNT'] += 1;
				$TOTAL_B['M1_AMOUNT'] += $LIST[$i]['amount'];

				if($LIST[$i]['member_investor_type']=='2') {
					$TOTAL_B['M12_COUNT'] += 1;
					$TOTAL_B['M12_AMOUNT'] += $LIST[$i]['amount'];
				}
				else if($LIST[$i]['member_investor_type']=='3') {
					$TOTAL_B['M13_COUNT'] += 1;
					$TOTAL_B['M13_AMOUNT'] += $LIST[$i]['amount'];
				}
				else {
					$TOTAL_B['M11_COUNT'] += 1;
					$TOTAL_B['M11_AMOUNT'] += $LIST[$i]['amount'];
				}
			}

			if($LIST[$i]['flatform_id']=='finnq') {
				$TOTAL_B['M3_COUNT'] += 1;
				$TOTAL_B['M3_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='hktvwowstar') {
				$TOTAL_B['M32_COUNT'] += 1;
				$TOTAL_B['M32_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='chosun') {
				$TOTAL_B['M33_COUNT'] += 1;
				$TOTAL_B['M33_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['flatform_id']=='oligo') {
				$TOTAL_B['M34_COUNT'] += 1;
				$TOTAL_B['M34_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

	}


	$m=substr($today,5,2)*1;
	$d=substr($today,8,2)*1;

	$strTitle = "SCF 상품 투자요약 ($m/$d)";

	$strContent = "
	
	<html>
	<head>
	<title>확정매출채권 상품 투자 요약보고</title>
	<meta name='viewport' content='width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=2.0,user-scalable=yes'>
	<meta name='mobile-web-app-capable' content='yes'>
	<meta name='apple-mobile-web-app-capable' content='yes'>
	<link href='https://fonts.googleapis.com/css?family=Nanum+Gothic:400,700,800&subset=korean' rel='stylesheet'>
	<link href='/css/report.css?ver=".DATE('YmdHis')."' rel='stylesheet'>
	</head>
	<body>

	<div class='content_guide' style='width:90%;'>

		<table class='tb1_guide'>
		<tr>
			<th class='tb1_title_area' colspan='3'>".$strTitle."</th>
		</tr>
		<tr>
			<th class='th_33'>모집금액</th>
			<td class='td_int_area td_gray fb'>".number_format($recruit_total_prd)."건</td>
			<td class='td_int_area td_gray fb'>".price_cutting($recruit_total_amount)."원</td>
		</tr>

		<tr>
			<th class='th_33'>전체투자현황</th>
			<td class='td_int_area'>".NUMBER_FORMAT($TOTAL['COUNT'])."건</td>
			<td class='td_int_area'>".price_cutting($TOTAL['AMOUNT'])."원</td>
		</tr>

		<tr>
			<th class='th_33'>법인투자</th>
			<td class='td_int_area'>".NUMBER_FORMAT($TOTAL['M2_COUNT'])."건</td>
			<td class='td_int_area'>".price_cutting($TOTAL['M2_AMOUNT'])."원</td>
		</tr>

		<tr>
			<th class='th_33'>개인투자</th>
			<td class='td_int_area'>".NUMBER_FORMAT($TOTAL['M1_COUNT'])."건</td>
			<td class='td_int_area'>".price_cutting($TOTAL['M1_AMOUNT'])."원</td>
		</tr>

		<tr>
			<th class='th_deep_gray th_33'>개인-일반</th>
			<td class='td_int_area td_gray'>".NUMBER_FORMAT($TOTAL['M11_COUNT'])."건</td>
			<td class='td_int_area td_gray'>".price_cutting($TOTAL['M11_AMOUNT'])."원</td>
		</tr>
		<tr>
			<th class='th_deep_gray th_33'>개인-소득</th>
			<td class='td_int_area td_gray'>".NUMBER_FORMAT($TOTAL['M12_COUNT'])."건</td>
			<td class='td_int_area td_gray'>".price_cutting($TOTAL['M12_AMOUNT'])."원</td>
		</tr>
		<tr>
			<th class='th_deep_gray th_33'>개인-전문</th>
			<td class='td_int_area td_gray'>".NUMBER_FORMAT($TOTAL['M13_COUNT'])."건</td>
			<td class='td_int_area td_gray'>".price_cutting($TOTAL['M13_AMOUNT'])."원</td>
		</tr>
		<tr>
			<th class='th_33'>최초투자자</th>
			<td class='td_int_area'>".NUMBER_FORMAT($TOTAL_A['COUNT'])."건</td>
			<td class='td_int_area'>".price_cutting($TOTAL_A['AMOUNT'])."원</td>
		</tr>
		<tr>
			<th class='th_33'>기존투자자</th>
			<td class='td_int_area'>".NUMBER_FORMAT($TOTAL_B['COUNT'])."건</td>
			<td class='td_int_area'>".price_cutting($TOTAL_B['AMOUNT'])."원</td>
		</tr>
		</table>

		<table class='tb2_guide'>
		<tr>
			<th class='tb1_title_area' colspan='8'>투자 상세내역</th>
		</tr>
		<tr>
			<th class='th_5'>NO</th>
			<th>업체명<br />/성명</th>
			<th>투자자<br />유형</th>
			<!--th>투자처</th-->
			<th>투자금액</th>
			<!--th>투자<br />형태</th-->
			<th>누적<br />투자수</th>
			<th>누적<br />투자액</th>
		</tr>";

	FOR($i=0,$j=1,$lnum=21; $i<$rows; $i++,$j++,$lnum++) {
		$name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];
		if($LIST[$i]['member_type']=='2') {
			$member_type = '법인';
		}
		else {
			if($LIST[$i]['member_investor_type']=='3')  $member_type = '전문';
			else if($LIST[$i]['member_investor_type']=='2') $member_type = '소득적격';
			else $member_type = '개인';
		}
		if($LIST[$i]['flatform_id']=='finnq') {
			$flatform = '핀크';
		}
		else if($LIST[$i]['flatform_id']=='finnq') {
			$flatform = '한경';
		}
		else if($LIST[$i]['flatform_id']=='oligo') {
			$flatform = '올리고';
		}
		else if($LIST[$i]['flatform_id']=='kakaopay') {
			$flatform = '카카오페이';
		}
		else {
			$flatform = '헬로';
		}
		$invest_gubun = ($LIST[$i]['is_auto_invest']=='1') ? '자동투자' : '일반투자';

	$strContent .= "
		<tr>
			<td class='td_txt_area'>".$j."</td>
			<td class='td_txt_area'>".$name."</td>
			<td class='td_txt_area'>".$member_type."</td>
			<!--td class='td_txt_area'>".$flatform."</td-->
			<td class='td_int_area'>".price_cutting($LIST[$i]['amount'])."원</td>
			<!--td class='td_txt_area'>".$invest_gubun."</td-->
			<td class='td_int_area'>".number_format($LIST[$i]['total_invest_count'])."건</td>
			<td class='td_int_area'>".price_cutting($LIST[$i]['total_invest_amount'])."원</td>
		</tr>";

		}
	$strContent .= "</table>
	</div>
	<br/><br/>

	</body>
	</html>
	";

	$pidx_rep = "SCF_".date("Ymd");

	$Query = "INSERT INTO
						   cf_product_admin_report
						   (product_idx, title, product, content, reg_time)
						   VALUES
						   ('$pidx_rep','".addslashes($strTitle)."','$pidx_rep','".addslashes($strContent)."',now())";
	sql_query($Query);

	echo $strContent;

	return sql_insert_id();
}
?>