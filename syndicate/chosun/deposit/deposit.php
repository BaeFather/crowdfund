<?
###############################################################################
## 투자내역 (최종수정 2018.02.09 배재수)
###############################################################################
include_once('./_common.php');

$g5['title'] = '투자내역';
$g5['top_bn'] = "/images/mypage/sub_loanlist.jpg";
$g5['top_bn_alt'] = "대출내역 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if (!$member["mb_id"])
	alert("로그인 후 이용 가능합니다.", HF_PATH."/member/login.php?url=" . urlencode($_SERVER[PHP_SELF]."?tab=".$tab));
	//alert("로그인 후 이용 가능합니다.", G5_BBS_URL."/login.php?url=" . urlencode($_SERVER[PHP_SELF]."?tab=".$tab));


include_once(HF_PATH.'/hf_head.php');

// 회원일 경우..
if($is_member) {

	$bank_acct_registered    = ($member['bank_code'] && $member['account_num'] && $member['bank_private_name']) ? true : false;
	$virtual_acct_registered = ($member['va_bank_code2'] && $member['virtual_account2']) ? true : false;

	$tab = $_GET['tab'];  //추천인 이벤트를 통한 가입자가 자동으로 본 페이지로 리다이렉션 될때의 파라미터


	// 예치금 잔액
	$member_deposit_point = ($member['mb_point']) ? $member['mb_point'] : 0;

	// 충전금액 합계
	$sql = "
		SELECT
			IFNULL(SUM(va.tr_amt), 0) AS total_charge_amount
		FROM
			vacs_ahst va
		INNER JOIN
			g5_member mem ON va.iacct_no = mem.virtual_account AND va.caninp_si=''
		LEFT JOIN
			vacs_vact vv ON mem.virtual_account = vv.acct_no AND vv.acct_st = '1'
		WHERE
			mem.mb_id='{$_SESSION['ss_mb_id']}'";
	$ROW = sql_fetch($sql);
	$total_charge_amount0 = $ROW['total_charge_amount'];

	// 충전금액 합계(신한 제3자 예치시스템 적용)
	$sql = "
		SELECT
			IFNULL(SUM(CONVERT(A.TR_AMT, unsigned)), 0) AS total_charge_amount
		FROM
			IB_FB_P2P_IP A
		INNER JOIN
			g5_member B	 ON A.CUST_ID=B.mb_no
		LEFT JOIN
			IB_vact C  ON A.CUST_ID=C.CUST_ID
		WHERE
			A.CUST_ID='".$member['mb_no']."'";
	$ROW = sql_fetch($sql);
	$total_charge_amount1 = $ROW['total_charge_amount'];


	$total_charge_amount = $total_charge_amount0 + $total_charge_amount1;

	// 투자금합계
	$sql = "
		SELECT
			SUM(amount) AS total_invest_amout
		FROM
			cf_product_invest
		WHERE
			member_idx={$member['mb_no']} AND invest_state='Y'";
	$result = sql_query($sql);
	list($total_invest_amount) = mysqli_fetch_row($result);
	$total_invest_amount = $total_invest_amount > 0 ? number_format($total_invest_amount) : 0;

	$invest_list = array();
	$invest_list_total = array();

	$sql = "
		SELECT
			COUNT(*)
		FROM
			cf_product_invest pi
		INNER JOIN
			cf_product pd ON pi.product_idx = pd.idx
		WHERE
			pi.member_idx={$member['mb_no']}";

	$result = sql_query($sql);
	$row    = mysqli_fetch_array($result);
	$affect_num = $row[0];
	if(!$page) $page = 1;
	if(!$size) $size = 5;

	if($affect_num > 0) {

		if($page > ceil($affect_num / $size)) $page = ceil($affect_num / $size);
		$start_num = ($page - 1) * $size;

		$sql = "
			SELECT
				(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest where pd.idx = product_idx and invest_state='Y') AS total_invest_amount,
				pi.idx, pi.amount, pi.member_idx, pi.product_idx, pi.invest_state,
				pd.title, pd.loan_interest_rate, pd.loan_usefee, invest_period, pd.recruit_period_start, pd.recruit_period_end, pd.recruit_amount,
				pd.start_date, pd.end_date, pd.invest_return, pd.invest_usefee, pd.open_datetime, pd.start_datetime, pd.end_datetime,
				pd.start_hour, pd.start_minute, pd.start_second, pd.end_hour, pd.end_minute, pd.end_second, pd.state, pd.invest_end_date,
				pd.loan_start_date, pd.loan_end_date
			FROM
				cf_product_invest pi
			INNER JOIN
				cf_product pd ON pi.product_idx = pd.idx
			WHERE
				pi.member_idx = '{$member['mb_no']}'
			ORDER BY
				pi.idx DESC
			LIMIT ".$start_num.", ".$size;
		$result = sql_query($sql);

		while($list = sql_fetch_array($result)){
			array_push($invest_list, $list);
		}

		$sql = "
			SELECT
				pi.amount, pi.invest_state, pd.invest_return, pd.state
			FROM
				cf_product_invest pi
			INNER JOIN
				cf_product pd ON pi.product_idx = pd.idx
			WHERE
				pi.member_idx = '{$member['mb_no']}'
			ORDER BY
				pi.idx DESC";
		$result_total = sql_query($sql);
		while($list = sql_fetch_array($result_total)){
			array_push($invest_list_total, $list);
		}
	}

	// 미달성환불합계
	$sql = "
		SELECT
			IFNULL(SUM(A.amount),0) AS 'total_recruit_fail_return_price'
		FROM
			cf_product_invest AS A
		LEFT JOIN
			cf_product AS B ON A.product_idx = B.idx
		LEFT JOIN
			g5_member AS C ON	A.member_idx = C.mb_no
		WHERE
			B.state = ''
			AND B.end_datetime < now()
			AND B.invest_end_date = ''
			AND A.invest_state = 'Y'
			AND B.end_date > SUBSTRING(NOW(),1,10)
			AND C.mb_id = '{$_SESSION['ss_mb_id']}'";
	$row = sql_fetch($sql);
	$total_recruit_fail_return_price = $row['total_recruit_fail_return_price'];
	$sql = $row = NULL;

	// 반환금 합계 (대출취소)
	$sql = "
		SELECT
			IFNULL(SUM(A.amount),0) AS 'total_loan_cancel_return_price'
		FROM
			cf_product_invest AS A
		LEFT JOIN
			cf_product AS B ON A.product_idx = B.idx
		LEFT JOIN
			g5_member AS C ON	A.member_idx = C.mb_no
		WHERE
			A.invest_state = 'R'
			AND C.mb_id = '{$_SESSION['ss_mb_id']}'";
	$row = sql_fetch($sql);
	$total_loan_cancel_return_price = $row['total_loan_cancel_return_price'];
	$sql = $row = NULL;

	// 출금합계(일반출금)
	$sql = "
		SELECT
			SUM(req_price) total_withdraw_price
		FROM
			g5_withdrawal
		WHERE
			state = '2'
			AND mb_id = '{$_SESSION['ss_mb_id']}'";
	$row = sql_fetch($sql);
	$total_withdraw_price0 = $row['total_withdraw_price'];

	// 출금합계(원리금출금)
	$sql = "
		SELECT
			(IFNULL(SUM(interest),0) + IFNULL(SUM(principal),0)) AS total_return_amount
		FROM
			cf_product_give
		WHERE
			member_idx = '".$member['mb_no']."' AND receive_method='1'";
	$row = sql_fetch($sql);
	$total_withdraw_price1 = $row['total_return_amount'];

	$total_withdraw_price = $total_withdraw_price0 + $total_withdraw_price1;

	$sql = $row = NULL;

	// 예치금 현황 거래일(y),충전금액(n),투자금액(y),미달성환불(y),출금(n),잔액(y) 리스트
	/*
			작업자 ps.
			- price1~5 까지의 컬럼을 각각 필요 테이블에서 UNION 한 후, regdate로 group by 해놓았음
			- (n)으로 표시된 항목은 union이 안된 컬럼들임. (작업해야함)
			- UNION 으로 붙이면 됨
	*/
	$sql3 = "
		SELECT
			tbl.regdate,
			tbl.orderReg,
			tbl.price1,
			tbl.price2,
			tbl.price3,
			tbl.price4,
			tbl.price5,
			tbl.state
		FROM
		(
			SELECT
				CONCAT(SUBSTR(va.tr_il, 1, 4), '-', SUBSTR(va.tr_il, 5, 2), '-', SUBSTR(va.tr_il, 7, 2) ) AS 'regdate',
				CONCAT(SUBSTR(va.tr_il, 1, 4), '-', SUBSTR(va.tr_il, 5, 2), '-', SUBSTR(va.tr_il, 7, 2), ' ', SUBSTR(va.tr_si, 1, 2), ':', SUBSTR(va.tr_si, 3, 2), ':', SUBSTR(va.tr_si, 5, 2)  ) AS 'orderReg',
				va.tr_amt AS 'price1',
				'' AS 'price2',
				'' AS 'price3',
				'' AS 'price4',
				'' AS 'price5',
				'' AS 'state'
			FROM
				vacs_ahst va
			INNER JOIN
				vacs_vact vv ON va.iacct_no = vv.acct_no AND vv.acct_st = '1'
			LEFT JOIN
				g5_member mem ON va.iacct_no = mem.virtual_account
			WHERE
				mem.mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				insert_date AS 'regdate',
				CONCAT(A.insert_date, ' ', A.insert_time ) AS 'orderReg',
				'' AS 'price1',
				amount AS 'price2',
				'' AS 'price3',
				'' AS 'price4',
				'' AS 'price5',
				'' AS 'state'
			FROM
				cf_product_invest AS A
			LEFT JOIN
				g5_member AS B ON	A.member_idx = B.mb_no
			WHERE
				invest_state = 'Y' AND	B.mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				A.insert_date AS 'regdate',
				concat(A.insert_date, ' ', A.insert_time ) AS 'orderReg',
				'' AS 'price1',
				'' AS 'price2',
				A.amount AS 'price3',
				'' AS 'price4',
				'' AS 'price5',
				'' AS 'state'
			FROM
				cf_product_invest AS A
			LEFT JOIN
				cf_product AS B ON A.product_idx = B.idx
			LEFT JOIN
				g5_member AS C ON A.member_idx = C.mb_no
			WHERE
				B.state = ''
				AND B.end_datetime < now()
				AND B.invest_end_date = ''
				AND A.invest_state = 'Y'
				AND	B.end_date > SUBSTRING(NOW(),1,10)
				AND	C.mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				insert_date AS 'regdate',
				CONCAT(A.insert_date, ' ', A.insert_time ) AS 'orderReg',
				'' AS 'price1',
				amount AS 'price2',
				'' AS 'price3',
				'' AS 'price4',
				'' AS 'price5',
				'' AS 'state'
			FROM
				cf_event_product_invest AS A
			LEFT JOIN
				g5_member AS B ON	A.member_idx = B.mb_no
			WHERE
				invest_state = 'Y' AND	B.mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				A.insert_date AS 'regdate',
				concat(A.insert_date, ' ', A.insert_time ) AS 'orderReg',
				'' AS 'price1',
				'' AS 'price2',
				A.amount AS 'price3',
				'' AS 'price4',
				'' AS 'price5',
				'' AS 'state'
			FROM
				cf_event_product_invest AS A
			LEFT JOIN
				cf_event_product AS B ON A.product_idx = B.idx
			LEFT JOIN
				g5_member AS C ON A.member_idx = C.mb_no
			WHERE
				B.state = ''
				AND B.end_datetime < now()
				AND B.invest_end_date = ''
				AND A.invest_state = 'Y'
				AND	B.end_date > SUBSTRING(NOW(),1,10)
				AND	C.mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				date(regDate) AS 'regdate',
				regDate AS 'orderReg',
				'' AS 'price1',
				'' AS 'price2',
				'' AS 'price3',
				req_price AS 'price4',
				'' AS 'price5',
				state AS 'state'
			FROM
				g5_withdrawal
			WHERE
				state in('1', '2')
				AND mb_id = '{$_SESSION['ss_mb_id']}'

			UNION ALL

			SELECT
				SUBSTRING(po_datetime,1,10) AS 'regdate',
				po_datetime AS 'orderReg',
				'' AS 'price1',
				'' AS 'price2',
				'' AS 'price3',
				'' AS 'price4',
				po_point AS 'price5',
				'' AS 'state'
			FROM
				g5_point
			WHERE
				mb_id = '{$_SESSION['ss_mb_id']}'
				AND po_content in('예치금 지급', '예치금 차감')
		) AS tbl
		ORDER BY
			tbl.orderReg DESC";
	$result3 = sql_query($sql3);

	$point_list = array();
	while($list = sql_fetch_array($result3)){
		array_push($point_list, $list);
	}

}
else {
	//비회원 처리
}

$invest_amount_total  = 0;
$invest_count         = 0;
$invest_return_total  = 0;
$invest_return_count  = 0;
$repayment_value      = 0;

if($invest_list_total != null) {
	foreach($invest_list_total AS $Rows) {
		if($Rows['invest_state']=='Y') {
			$invest_amount_total = $invest_amount_total + $Rows['amount'];
			$invest_return_total = $invest_return_total + $Rows['invest_return'];
			$invest_return_count = $invest_return_count + 1;

			/* 총 상환금액 (2:상품마감, 5:중도일시상환) */
			if(in_array($Rows['state'], array('2','5'))) {
				$repayment_value = $repayment_value + $Rows['amount'];
			}
		}
		$invest_count = $invest_count + 1;
	}
}

/*  총상환이자  */
$repayment_interest_query  = "
	SELECT
		SUM(a.invest_amount) AS repayment_interest
	FROM
		cf_product_give a
	INNER JOIN
		cf_product_invest b
	ON
		a.invest_idx = b.idx
	INNER JOIN
		cf_product c
	ON
		a.product_idx=c.idx
	WHERE
		b.member_idx='{$member['mb_no']}'
		AND b.invest_state='Y'
		AND c.state IN('1','2','5')";
$repayment_interest_row = sql_fetch($repayment_interest_query);


/* 평균수익률  */
//$invest_return_average = ($invest_return_total) ? round(($invest_return_total/$invest_count),2) : 0;
$invest_return_average = ($invest_return_total) ? round(($invest_return_total/$invest_return_count),2) : 0;   // 2016-11-03 수정


$event_invest_list = array();
$sql = "
	SELECT
		(SELECT IFNULL(SUM(amount),0) FROM cf_event_product_invest WHERE epd.idx = product_idx AND epi.invest_state='Y') AS total_invest_amount,
		epi.idx, epi.amount, epi.member_idx, epi.product_idx, epi.invest_state,
		epd.title, epd.invest_profit, epd.invest_period, epd.recruit_period_start, epd.recruit_period_end, epd.recruit_amount,
		epd.start_date, epd.end_date, epd.invest_return, epd.invest_usefee, epd.open_datetime, epd.start_datetime, epd.end_datetime ,
		epd.start_hour, epd.start_minute, epd.start_second, epd.end_hour, epd.end_minute, epd.end_second, epd.state, epd.invest_end_date, epd.total_return_amount
	FROM
		cf_event_product_invest epi
	INNER JOIN
		cf_event_product epd ON epi.product_idx=epd.idx
	WHERE
		epi.member_idx='{$member['mb_no']}'
	ORDER BY
		epi.idx DESC";
$result = sql_query($sql);

$event_invest_count           = 0;  //총 투자건수
$event_invest_amount_total    = 0;  //총 투자금액
$event_repayment_value        = 0;  //총 상환금액
$event_repayment_profit_value = 0;  //총 상환이자
$event_invest_return_total    = 0;

while($list = sql_fetch_array($result)){
	if($list["invest_state"]=="Y") {
		//echo "<pre style='font-size:9pt'>"; print_r($list); echo "</pre>";
		$event_invest_amount_total = $event_invest_amount_total + $list["amount"];
		$event_invest_return_total = $event_invest_return_total + $list["invest_return"];

		if($list["state"]==2) {
			$event_repayment_value = $event_repayment_value + $list["amount"] + $list['invest_profit'];  // 총 상환금액
			$event_repayment_profit_value = $event_repayment_profit_value + $list['invest_profit'];  // 총 상환이자
		}
	}

	$event_invest_count = $event_invest_count + 1;

	array_push($event_invest_list, $list);
}
/* 이벤트 투자 평균수익률  */
$event_invest_return_average = ($event_invest_return_total) ? round(($event_invest_return_total/$event_invest_count), 2) : 0;

//echo "===>";
//print_r($member);

//환급계좌 등록 리다이렉션 URL
$bank_edit_url = "/member/member_confirm.php?url=".urlencode('/mypage/mypage.php#bank_edit');

$VACT = sql_fetch("SELECT bank_cd, acct_no, cmf_nm, acct_st FROM vacs_vact WHERE bank_cd='".$member['va_bank_code']."' AND acct_no='".$member['virtual_account']."' ORDER BY acct_no DESC LIMIT 1");		// 가상계좌 등록내역 (세틀뱅크)

$KSNET_VACT = sql_fetch("SELECT BANK_CODE, VR_ACCT_NO, CORP_NAME, USE_FLAG FROM KSNET_VR_ACCOUNT WHERE VR_ACCT_NO='".$member['virtual_account2']."' ORDER BY VR_ACCT_NO DESC LIMIT 1");		// 가상계좌 등록내역 (신한)

if($bank_acct_registered) {
	if($KSNET_VACT['USE_FLAG']=='Y') {
		$ib_vact_status  = '정상';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'가상계좌는 재발급 및 정보수정을 허용하지 않습니다.\');" class="btn_gray">가상계좌 발급받기</button>';
	}
	else if($KSNET_VACT['USE_FLAG']=='N') {
		$ib_vact_status = '거래불가';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'거래불가코드가 등록되었습니다.\\n고객센터로 문의하십시요.\');" class="btn_blue">가상계좌 발급받기</button>';
	}
	else {
		$ib_vact_status = '미발급';
		$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="vaOpen();" class="btn_blue">가상계좌 발급받기</button>';
	}
}
else {
	$ib_vact_status = '미발급';
	$vact_reg_button = '<button type="button" id="vact_reg_button" onClick="alert(\'원리금을 상환 받으실 환급계좌를 먼저 등록 하셔야 합니다.\');location.href=\''.$bank_edit_url.'\'" class="btn_blue_dis">가상계좌 발급받기</button>';
}


// ▼ 예치금 출금 버튼 동작설정▼ ---------------------------------------------------
if(!$bank_acct_registered) {
	$withdrawal_button = '<button type="button" class="btn_blue_out" onClick="alert(\'환급 계좌 등록 후 출금 가능합니다.\');location.href=\''.$bank_edit_url.'\';">예치금 출금</button>';
}
else if(!$virtual_acct_registered) {
	$withdrawal_button = '<button type="button" class="btn_blue_out" onClick="alert(\'가상 계좌 발급 후 출금 가능합니다.\');location.href=\'?tab=4\';">예치금 출금</button>';
}
else {
	if($member['insidebank_after_trans_target']=='1') {
		$tmp_msg = '2017년 10월 15일 18시 이전에 예치금을 보유하였으나 환급계좌가 없으셨던 분들은 신한은행 가상계좌 발급 후 신한은행으로 기존 예치금이 이관된 이후 출금이 가능합니다.\n\n(신한은행 가상계좌 발급 후 신한은행으로 예치금 이관에 소요되는 시간은  영업일 48시간 이내입니다.)';
		$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="alert(\''.$tmp_msg.'\');">예치금 출금</button>';
	}
	else {
		$withdrawal_button = '<button type="button" class="btn_big_blue" id="withdrawal">예치금 출금</button>';
	}
}

//$withdrawal_button = '<button type="button" class="btn_big_blue" onClick="alert(\'[예치금 출금 안내]\n\n신한은행 제3자 예치금관리 시스템 적용이 완료되었으며,\n현재 신한은행 가상계좌에서 회원별 예치금 매칭이 진행중입니다.\n예치금 매칭이 완료되는 2017년 10월 16일 12시 이후 출금신청이 가능한 점 양해부탁드립니다.\');">예치금 출금</button>';
// ▲ 예치금 출금 버튼 동작설정 ▲ ---------------------------------------------------


$tab = ($tab) ? $tab : 0;

// 모바일 분기
if(G5_IS_MOBILE){
	include_once('./deposit_m.php');
	return;
}
?>

<!-- 본문내용 START -->
<div id="content">

	
	<? if(G5_IS_MOBILE) { ?>
	<div >
    <div class="location"><span></span><b class="blue">투자내역</b></div>
	</div>
	<? } else { ?>
   <div class="location"><span></span><b class="blue">투자내역</b>
	</div>
	<? } ?>

	<div class="content">
		<div class="deposit">

			<h2 class="small">
				예치금잔액 <span id="realtime_point1" class="red"><?=number_format($member_deposit_point)?></span>원<br>
				<span style="10px;font-size:0.7em; color:#0071BC">투자 전 미리 예치금을 입금하세요.</span>
			</h2>

			<div style='height:30px;'></div>

			<!-- 탭메뉴 -->
			<ul class="tab_type03">
				<li id="invest_status"   data-gubun="tab1" <?=($tab==0)?'class="on"':''?> style="width:16%;">투자 현황</li>
				<li id="interest_status" data-gubun="tab2" <?=($tab==1)?'class="on"':''?> style="width:16%;">수익금 현황</li>
				<li id="money_status"    data-gubun="tab3" <?=($tab==2)?'class="on"':''?> style="width:20%;">예치금 현황 및 출금</li>
				<li id="va_info"         data-gubun="tab4" <?=($tab==3)?'class="on"':''?> style="width:16%;">가상계좌 정보</li>
				<li id="invest_limit"    data-gubun="tab5" <?=($tab==4)?'class="on"':''?> style="width:16%;">투자한도</li>
				<li id="auto_invest"     data-gubun="tab6" <?=($tab==5)?'class="on"':''?> style="width:16%;">자동투자 설정</li>
				<!--li id="rec_info"        data-gubun="tab7" <?=($tab==6)?'class="on"':''?>>추천인현황</li-->
			</ul>
			<script>
			// 탭 기능
			$(document).ready(function(){
				$(this).addClass('on').siblings().removeClass('on');
				$('.boxArea').hide();
				$('.boxArea:eq(<?=$tab?>)').show();

				$('.tab_type03 li').click(function() {
					$(this).addClass('on').siblings().removeClass('on');
					var cur = $(this).index();
					$('.boxArea').hide();
					$('.boxArea:eq('+cur+')').show();
				});
			});
			</script>

			<!-- 투자 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">
					<h3>투자 현황</h3>
					<div id="invest_status">
						<div class="type03">
							<table>
								<tbody>
								<tr>
									<th>구분</th>
									<th>총 투자금액</th>
									<th>총 상환금액</th>
									<th>총 상환이자</th>
									<th>투자잔액</th>
									<!--<th>평균수익률</th>-->
								</tr>
								<tr>
									<td>투자</td>
									<td><?=number_format($invest_amount_total)?>원</td>
									<td><?=number_format($repayment_value)?>원</td>
									<td><?=number_format($repayment_interest_row["repayment_interest"])?>원</td>
									<td><?=number_format($invest_amount_total-$repayment_value)?>원</td>
									<!--<td><?=$invest_return_average?>%</td>-->
								</tr>
								<tr>
									<td>이벤트</td>
									<td><?=number_format($event_invest_amount_total)?>원</td>
									<td><?=number_format($event_repayment_value)?>원</td>
									<td><?=number_format($event_repayment_profit_value)?>원</td>
									<td><!--<?=($event_invest_amount_total-$event_repayment_value > 0) ? number_format($event_invest_amount_total-$event_repayment_value) : '0';?>원--></td>
									<!--<td><?=$event_invest_return_average?>%</td>-->
								</tr>
								</tbody>
							</table>
						</div>
					</div>
					<p>&nbsp;</p>

					<h3>투자 내역</h3>
					<div style="margin:4px 0 8px; padding:10px; font-size:10pt;color:#222;text-align:left; background-color:#E9EDF7;border:1px solid #999;border-radius:5px;">
						<ol style="margin-left:20px;line-height:16px">
							<li style="list-style-type:decimal;">
								투자수익으로 인해 발생된 세금을 국세청에 원천징수 할 때에는 원단위를 절사합니다.<br>
								이 때 절사된 금액을 '실 지급액'에 합산하여 투자자분에게 지급하므로 실 지급액은 계산된 금액보다 클 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								투자 원금은 대출자의 원금 상환 후 영업일 5일 이내에 월이자와 함께 지급됩니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								이자 선지급 상품의 경우 각 회차별 지급예정일과 지급상태 표기일이 다를 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								만기일시상환을 기준으로 표기된 회차별 이자는 조기상환 등의 이유로 변동될 수 있습니다.
							</li>
							<li style="list-style-type:decimal;margin-top:6px;">
								매월 투자원금의 0.1% 를 플랫폼 이용료로 수취합니다. (단, 면제상품은 플랫폼 이용료를 수취하지 않습니다.)<br/>
								※ 플랫폼 이용료 산정식 : 투자금액의 연 1.2%(<strong>월 0.1%</strong>) 의 금액을 365일로 나눈 금액(˚일별플랫폼이용료)에 상환회차월별 일수를 곱한 금액을 산정합니다.<br/>
								※ 원천징수액 산정식 : 투자수익에 소득세(25%)와 주민세(2.5%가)가 추가되어 27.5%가 세금으로 산정됩니다.<br/>
									
							</li>
						</ol>
					</div>

					<div id="invest_list_area"></div>
					<script>
					load_invest_list = function(arg1, arg2) {
						var page = arg1;
						var search_state = arg2;
						$.ajax({
							url: '/deposit/ajax_invest_list.php',
							type: 'GET',
							data: {page:page, search_state:search_state},
							success: function(data) {

								if(data=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); return; }
								else if(data=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); return; }
								else {
									$('#ajax_return_txt').val(data);
									$('#invest_list_area').empty();
									$('#invest_list_area').html(data);
								}
							},
							error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
						});
					}

					$(document).on('click', '#paging_span span.btn_paging', function() {
						var page = $(this).attr('data-page');
						var search_state = $('#search_state').val();
						load_invest_list(page, search_state);
					});

					$('#invest_status').click(function() { load_invest_list('',''); });
					<? if($tab==0) { ?>$('document').ready(function() { load_invest_list('',''); });<? } ?>
					</script>

					<p>&nbsp;</p>

					<h3>이벤트 투자 내역</h3>
					<div id="event_invest_list">
						<div class="type03">
							<table>
								<colgroup>
									<col style="width:5%">
									<col style="width:10%">
									<col style="width:8%">
									<col style="width:12%">
									<col style="width:10%">
									<col style="width:16%">
									<col style="width:11%">
									
								</colgroup>
								<tbody>
								<tr>
									<th>No</th>
									<th>상품명</th>
									<th>투자금액</th>
									<th>지급(예정)금액</th>
									<th>진행상태</th>
									<th>모집기간</th>
									<th>이자율(회)</th>
									
								</tr>
								<?
								if($event_invest_list != null){

									foreach($event_invest_list as $RowsE) {

										//echo "<pre style='font-size:9pt'>"; print_r($RowsE); echo "</pre>";

										$event_product_open_date    = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["open_datetime"])));   // 상점오픈 (투자시작가능)
										$event_product_invest_sdate = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["start_datetime"])));  // 상품오픈 (투자시작가능)
										$event_product_invest_edate = str_replace(" ","",str_replace(":","",str_replace("-","",$RowsE["end_datetime"])));	   // 상품종료 (투자마감)

										$event_recruit_amount	     = $RowsE["recruit_amount"];
										$event_total_invest_amount = $RowsE["total_invest_amount"];
										$event_invest_end_date	   = str_replace("-", "", $RowsE["invest_end_date"]);
										$event_product_state = get_product_state(
											$RowsE["recruit_period_start"],
											$RowsE["recruit_period_end"],
											$event_product_open_date,
											$event_product_invest_sdate,
											$event_product_invest_edate,
											$RowsE["state"],
											$event_recruit_amount,
											$event_total_invest_amount,
											$event_invest_end_date
										);

										if($RowsE["state"]==2) {
											$fcolor = "#3366FF";
										}
										else {
											$fcolor = ($RowsE['invest_state']=='N') ? "#FF3333" : "#00C5B0";
										}

								?>
								<tr>
									<td><?=$event_invest_count--?></td>
									<td><a href="/event_invest/event_invest.php?prd_idx=<?=$RowsE['product_idx']?>"><?=$RowsE['title']?></a></td>
									<td><?=number_format($RowsE['amount'])?>원</td>
									<td><?=number_format($RowsE['total_return_amount'])?>원</td>
									<td><b style='color:<?=$fcolor?>'><?=($RowsE['invest_state']=="N") ? '취소' : $event_product_state;?></b></td>
									<td><?=$RowsE['recruit_period_start']?> ~ <? echo $RowsE['recruit_period_end']?></td>
									<td><?=$RowsE['invest_return']?>%</td>
									<td><!--<?=$RowsE['invest_usefee']?>%--></td>
								</tr>
								<?
										$event_invest_count--;
									}
								}
								?>
								</tbody>
							</table>
						</div>
						<span style='font-size:9pt'>이삼오(2.3.5) 이벤트의 원리금은 투자일 기준 <b>익주 월요일</b>에 지급됩니다.</span>
					</div>
				</div>
			</div>
			<!-- 투자 현황 끝 ------------------------------------------------------------------------------------->

			<!-- 수익금 현황 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="interest_status_area"></div>
			</div>
			<script>
			load_repay_stats = function() {
				$.ajax ({
					url : "/deposit/ajax_repay_stats.php",
					type: "GET",
					data: {type:1},
					success: function(data) {
						if(data=="ERROR-DATA") {
							alert("시스템 에러입니다. 관리자에 문의해주세요.");
							return;
						}
						else if(data=="ERROR-LOGIN") {
							alert("로그인후 이용 가능 합니다.");
							return;
						}
						else {
							$('#ajax_return_txt').val(data);
							$('#interest_status_area').html(data);
						}
					}
				});
			}
			$('#interest_status').click(function() { load_repay_stats(); });
			<? if($tab=='1') { ?>$(document).ready(function() { load_repay_stats(); });<? } ?>
			</script>
			<!-- 수익금 현황 끝 ------------------------------------------------------------------------------------->


			<!-- 예치금 현황 및 출금 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">

					<h3>예치금 현황</h3>
					<div class="type03 mb30">
						<table>
							<tbody>
							<tr>
								<th>입금합계</th>
								<th>투자금합계</th>
								<th>예치금잔액</th>
								<th>미달성환불합계</th>
								<th>반환금합계</th>
								<th>출금합계</th>
							</tr>
							<tr>
								<td><?=number_format($total_charge_amount)?>원</td>
								<td><?=number_format($invest_amount_total+$event_invest_amount_total)?>원</td>
								<td><span id="realtime_point2"><?=number_format($member_deposit_point)?></span>원</td>
								<td><?=number_format($total_recruit_fail_return_price);?>원</td>
								<td><?=number_format($total_loan_cancel_return_price);?>원</td>
								<td><?=number_format($total_withdraw_price)?>원</td>
							</tr>
							</tbody>
						</table>
						<div style="margin:8px 0 8px;">
							<center><?=$withdrawal_button?></center>
						</div>
					</div>

					<h3>상세내역</h3>
					<div class="type03" id="money_status_area"></div>

					<script>
					load_point_log = function() {
						$.ajax ({
							url : "/root_deposit/ajax_point_log.php",
							type: "GET",
							success: function(data) {
								if(data=="ERROR-DATA") {
									alert("시스템 에러입니다. 관리자에 문의해주세요.");
									return;
								}
								else if(data=="ERROR-LOGIN") {
									alert("로그인후 이용 가능 합니다.");
									return;
								}
								else {
									//data = data.replace(/\.\/ajax_point_log\.php/g,"\/root_deposit\/ajax_point_log\.php");
									$('#ajax_return_txt').val(data);
									$('#money_status_area').html(data);
								}
							}
						});
					}
					$('#money_status').click(function() { load_point_log(); });
					<? if($tab=='2') { ?>$(document).ready(function() { load_point_log(); });<? } ?>
					</script>

				</div>
			</div>
			<!-- 예치금 현황 및 출금 끝 ------------------------------------------------------------------------------------->

			<!-- 가상계좌정보 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box">
					<div class="title"><span class="blue"><?=$member['mb_name']?></span> 님 반갑습니다.</div>
					* 헬로펀딩의 투자전용 예치금 계좌(가상계좌)입니다.<br>
					* 발급 받으신 예치금 계좌로 예치금을 충전하신 후 투자가 가능합니다.
					<p>&nbsp;</p>

					<h3>신한은행 가상계좌 정보 <span style="color:#3366FF">(고객님의 소중한 자산은 신한은행의 자금 신탁 관리를 통하여 안전하게 운용됩니다.)</span></h3>
					<div class="type05 mb30">
						<table>
							<colgroup>
								<col width='20%'>
								<col width='80%'>
							</colgroup>
							<tbody>
							<tr>
								<th>계좌번호</th>
								<td style="text-align:left"><span style="color:#153FA1"><?=$BANK[$KSNET_VACT['BANK_CODE']]?> &nbsp; <?=$KSNET_VACT['VR_ACCT_NO']?> &nbsp; <?=$KSNET_VACT['CORP_NAME']?></span></td>
							</tr>
							<tr>
								<th>거래상태</th>
								<td style="text-align:left"><?=$ib_vact_status?></td>
							</tr>
							</tbody>
						</table>
						<p align='center' style="padding-top:9px">
							<?=$vact_reg_button?>
							<button type="button" onClick="location.href='<?=$bank_edit_url?>';" class="btn_green2">환급계좌 등록.변경</button>
						</p>
						<? if($_REQUEST['mode']=='debug') { ?><p align='center' style="padding-top:9px"><a href="javascript:;" id="withdrawal2" class="btn_blue">가상계좌 발급받기</a></p><? } ?>
					</div>

<!--
					<h3 style="opacity:0.6">가상계좌 정보 (구)</h3>
					<div class="type05 mb30" style="opacity:0.6">
						<table>
							<colgroup>
								<col width='20%'>
								<col width='80%'>
							</colgroup>
							<tbody>
								<tr>
									<th>계좌번호</th>
									<td style="text-align:left"><?=$BANK[$VACT['bank_cd']]?> &nbsp; <?=$VACT['acct_no']?> &nbsp; <?=$VACT['cmf_nm']?></td>
								</tr>
								<tr>
									<th>거래상태</th>
									<td style="text-align:left">거래불가</td>
								</tr>
							</tbody>
						</table>
					</div>
//-->

					<div style='height:20px;'></div>

					<h3>예치금 가이드</h3>
					<div class="guide_box">
						* 투자수익금은 고객님이 등록하신 환급신청 계좌로 입금됩니다.<br>
						* 예치금 계좌(가상계좌)는 최초 1회만 발급되며, 변경은 불가능 합니다.<br>
						* 예치금 계좌 입금 반영 시간은 약 1~10분 사이 입니다.<br>
						* 23:30 ~ 00:30분 사이에는 은행망 점검 시간으로 이체가 불가할 수 있습니다.<br>
						* 예치금 출금신청시 회원정보에 등록된 환급계좌로 실시간 지급됩니다.<br>
					</div>
				</div>

			</div>
			<!-- 가상계좌정보 끝 ------------------------------------------------------------------------------------->

			<!-- 투자한도 및 스케쥴 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="invest_limit_area"></div>
			</div>
			<script>
			load_invest_limit = function() {
				$.ajax({
					url : './ajax_invest_limit.php',
					type: 'GET',
					success: function(data) {
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#invest_limit_area').html(data); }
					},
					error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
				});
			}
			$('#invest_limit').click(function() { load_invest_limit(); });
			<? if($tab=='4') { ?>$(document).ready(function() { load_invest_limit(); });<? } ?>
			</script>
			<!-- 투자한도 및 스케쥴 끝 ------------------------------------------------------------------------------------->

			<!-- 자동투자 설정 시작 ------------------------------------------------------------------------------------->
			<div class="boxArea">
				<div class="box" id="auto_invest_area"></div>
			</div>
			<script>
			auto_invest_config = function(){
				$.ajax({
					url : './ajax_auto_invest_config.php',
					type: 'POST',
					success: function(data) {
						$('#ajax_return_txt').val(data);
						if(data=='ERROR-DATA') { alert('시스템 오류 입니다. 관리자에 문의해주세요.'); return; }
						else { $('#auto_invest_area').html(data); }
					},
					error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
				});
			}
			$('#auto_invest').click(function(){ auto_invest_config(); });
			<? if($tab==5) { ?>$(document).ready(function(){ auto_invest_config(); });<? } ?>
			</script>
			<!-- 자동투자 설정 끝 ------------------------------------------------------------------------------------->

			<!-- 추천인 현황 시작 ------------------------------------------------------------------------------------->
			<?
			$_CONF['event_no']	  = "1";
			$_CONF['event_sdate'] = "2016-11-29";
			$_CONF['event_edate'] = "2016-12-09";
			$_CONF['point_title'] = "추천인 보상(".$_CONF['event_no']."차)";

			//$recomment_where = "rec_mb_no='".$member['mb_no']."' AND rec_mb_id='".$member['mb_id']."' AND va_bank_code!=''";  //가상계좌 확인(o)

			$recomment_where = " 1=1 ";
			$recomment_where.= " AND rec_mb_no='".$member['mb_no']."' ";
			$recomment_where.= " AND rec_mb_id='".$member['mb_id']."' ";
			$recomment_where.= " AND (LEFT(mb_datetime, 10) BETWEEN '".$_CONF['event_sdate']."' AND '".$_CONF['event_edate']."') ";
			$recomment_where.= " AND va_bank_code!='' ";
			$recomment_where.= " AND (rec_date IS NOT NULL AND rec_date!='0000-00-00 00:00:00') ";


			$sql = "SELECT COUNT(mb_no) AS recommend_count, (COUNT(mb_no)*1000) AS recommend_point FROM g5_member WHERE $recomment_where";
			$ROW = sql_fetch($sql);

			$sql = "SELECT SUM(po_point) AS reward_point FROM g5_point WHERE mb_id='".$member['mb_id']."' AND po_content='".$_CONF['point_title']."'";
			$ROW2 = sql_fetch($sql);
			?>
			<div class="boxArea">
				<div class="box">

					<h3>추천 현황</h3>
					<div class="type03 mb30">
						<table>
							<colgroup>
								<col style='width:33.3%'>
								<col style='width:33.3%'>
								<col style='width:33.4%'>
							</colgroup>
							<tr>
								<th>추천수</th>
								<th>누적 예치금</th>
								<th>지급 예치금</th>
							</tr>
							<tr align="center">
								<td><?=number_format($ROW['recommend_count'])?>명</th>
								<td><?=number_format($ROW['recommend_point'])?>원</th>
								<td><?=number_format($ROW2['reward_point'])?>원</th>
							</tr>
						</table>
					</div>

					<p>&nbsp;</p>

					<h3>추천인 내역</h3>
					<div class="type03 mb30">
						<table>
							<colgroup>
								<col style='width:33.3%'>
								<col style='width:33.3%'>
								<col style='width:33.4%'>
							</colgroup>
							<tr>
								<th>NO</th>
								<th>아이디</th>
								<th>가입일</th>
							</tr>
							<?
							$sql  = "SELECT mb_id, mb_datetime FROM g5_member WHERE $recomment_where ORDER BY mb_no DESC";
							$res  = sql_query($sql);
							$rows = sql_num_rows($res);
							if($rows) {
								for($i=0, $j=$rows; $i<$rows; $i++, $j--) {
									$RECLIST = sql_fetch_array($res);
									$suffix = "";
									for($x=0; $x<strlen($RECLIST['mb_id'])-3; $x++) { $suffix.="*"; }
							?>
							<tr align="center">
								<td><?=$j?></td>
								<td><?=cut_str2($RECLIST['mb_id'], 3, $suffix)?></td>
								<td><?=date("Y.m.d", strtotime($RECLIST['mb_datetime']));?></td>
							</tr>
							<?
								}
							}
							else {
							?>
							<tr align="center">
								<td colspan="3">추천 데이터가 없습니다.</td>
							</tr>
							<?
							}
							?>
						</table>
					</div>

				</div>
			</div>
			<!-- 추천인 현황 끝 ------------------------------------------------------------------------------------->

		</div>

	</div>
</div>


<!-- 충천 - 예치금입금 팝업 -->
<div id="charge">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">예치금입금</div>
	<div class="con">
		<div class="title">예치금 계좌정보</div>
		<div class="type01">
			<table>
				<tbody>
				<tr>
					<td style="width:60px"><b>은행명</b></td>
					<td><?=$BANK[$KSNET_VACT['BANK_CODE']]?></td>
				</tr>
				<tr>
					<td><b>예금주</b></td>
					<td><?=$KSNET_VACT['CORP_NAME']?></td>
				</tr>
				<tr>
					<td><b>계좌번호</b></td>
					<td><?=$KSNET_VACT['VR_ACCT_NO']?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="info"><span class="green">*</span> 위 가상계좌로 투자금을 입금하시면 충전된 예치금으로 투자가 가능합니다. </div>
		<div class="title">예치금입금 가이드</div>
		<div class="box">헬로펀딩을 통해 발급된 가상계좌에 투자금을 입금하신 후 투자가 시작되는 상품에 투자를 진행하여 주시기 바랍니다.</div>
	</div>
</div>

<!-- 출금 - 예치금 출금 팝업 -->
<div id="withdraw" style="height:auto;" class="popbluetheme">
	<img src="/images/btn_close.gif" alt="close" class="close">
	<div class="title">예치금 출금</div>
	<div class="con">
		<div class="notes">출금 가능금액 <span id="realtime_point3" class="blue"><?=number_format($member_deposit_point)?></span> 원</div>
		<div class="type01">
			<table>
				<tbody>
				<tr>
					<td style="width:70px"><b>출금요청액</b></td>
					<td>
						<input type="text" name="req_price" id="req_price" class="text" style="text-align:right;" placeholder="0" maxlength="15" onKeyUp="NumberFormat(this);"> 원
						<input type="<?=($mode=='debug')?'text':'hidden'?>" name="now_point" id="now_point" value="<?=$member_deposit_point?>">
						<input type="hidden" name="mb_id" id="mb_id" value="<?=$member['mb_id']?>">
					</td>
				</tr>
				<tr>
					<td><b>계좌번호</b></td>
					<td><?=$BANK[$member['bank_code']]?> <?=$member['account_num']?></td>
				</tr>
				<tr>
					<td><b>예금주</b></td>
					<td><?=$member['bank_private_name']?></td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="btnArea">
			<button type="button" id="with_btn" class="btn_big_blue">출금신청</button>
		</div>
		<!--<div class="title">예치금출금 가이드</div>
		<div class="box">* 예치금은 <span style="color:red;font-weight:bold;">영업일 기준으로 24시 이전까지 출금 신청 시 다음 영업일 오전 12시</span>에<br> 일괄 지급처리 됩니다.</div>//-->
	</div>
</div>
<script>
function btn_event(arg) {
	if(arg=='send') {
		$('#with_btn').removeClass('btn_big_blue').addClass('btn_big_gray');
		$('#with_btn').text('전송중 >>>');
		$('#with_btn').attr('disabled', 'disabled');
	}
	else if(arg=='exit') {
		$('#with_btn').removeAttr('disabled');
		$('#with_btn').text('출금신청');
		$('#with_btn').removeClass('btn_big_gray').addClass('btn_big_blue');
	}
}

// 출금신청
$("#with_btn").click(function() {
	var req_price = $('#req_price').val();
	var now_point = $('#now_point').val();
	var mb_id	    = $('#mb_id').val();

	if(req_price == '') { alert('출금요청금액을 입력해주세요.'); return; }

	// 숫자단위 쉽표 제거
	req_price_len = req_price.length;
	for (i=0; i<req_price_len; i++) {
		req_price = req_price.replace(',', '');
	}

	req_price = Number(req_price);
	now_point = Number(now_point);

	if(req_price > now_point) { alert('요청금액이 출금가능금액보다 큽니다.'); return; }

	if(req_price!='' && req_price > 0) {

		$.ajax({
			url:'/root_deposit/withdrawal_request_proc.php',
			type:'POST',
			data:{
				'req_price':req_price,
				'mb_id':mb_id
			},
			error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요.");console.log(e); return; },
			beforeSend: function() { btn_event('send'); },
			complete: function() { btn_event('exit'); }
		}).done(function(data) {
			$('#ajax_return_txt').val(data);
			if(data == '1') {
				alert('출금신청이 완료되었습니다.');
				$('#req_price').val('');
				// 목록갱신
				$(location).attr('href','<?=BSC_URL?>/deposit/deposit.php?tab=2');
				$.ajax ({
					url : "/root_deposit/ajax_point_log.php",
					type: "GET",
					success: function(data2) {
						if(data2=="ERROR-DATA") { alert("시스템 에러입니다. 관리자에 문의해주세요."); }
						else if(data2=="ERROR-LOGIN") { alert("로그인후 이용 가능 합니다."); location.href='/bbs/login.php'; }
						else { $('#money_status_area').html(data2); }
					}
				});
				$.unblockUI();
				return;
			}
			else if(data == '2') { alert('출금신청 금액이 현재 보유 예치금보다 많습니다.'); return; }
			else { alert(data); return; }
		});
	}
	else { alert('출금 요청 금액을 입력하십시요.'); return; }
});
function NumberFormat() {}
</script>


<?
// 가상계좌번호 받기 팝업 (구)
//include_once(G5_PATH . "/deposit/inc_virtual_account_form.php");
?>

<!-- 투자내역 상세보기 -->
<div id="detail"></div>

<script>
$(document).ready(function(){

	$(document).on('click', '#detail #no, #detail .close', function() {
		$.unblockUI();
		return false;
	});

	//원리금 수취증서 프린트
	$('.certificate_print_btn').click(function() {
		var url = '/deposit/principal_interest_certificate.php?idx='+$(this).attr("data-idx");
		popup_window(url, 'certificate', 'width=936,height=768,left=0,top=0,scrolling=no');
	});

	//상세보기
	$('.funding_detail_btn').click(function() {
		ajax_data = $("#frm").serialize();
		$.ajax({
			url : "./ajax_product_detail.php?idx="+ $(this).attr("data-idx"),
			type: "GET",
			data : ajax_data,
			success: function(data) {
				if(data=="ERROR-DATA") { alert("시스템 오류 입니다. 관리자에 문의해주세요."); return; }
				else if(data=="ERROR-DATE") { alert("펀딩 투자 기간이 아닙니다. 펀딩 취소는 투자 기간안에만 가능 합니다."); return; }
				else{
					$("#detail").html(data);
					$.blockUI({
						message: $('#detail'),
						css: { top:'30px',left:'25%',width:'930px',border:0, cursor:'default' }
					});
				}
			},
			error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
		});
	});

	$(document).on('click','#detail #no, #detail .close',function(){
		$.unblockUI();
		return false;
	});

	$('#detail #no, #detail .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#withdraw .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#withdrawal2, #charging_dis').click(function() {
		$.blockUI({
			message: $('#withdraw2'),
			css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		});
	});

	$('#withdraw2 .close').click(function() {
		$.unblockUI();
		return false;
	});

	$('#charge .close').click(function() {
		$.unblockUI();
		return false;
	});


	//충전
	$('#charging').click(function() {
		$.blockUI({
			message: $('#charge'),
			css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		});
	});
	//출금
	$('#withdrawal').click(function() {
		$.blockUI({
			message: $('#withdraw'),
			css: { top:'16%',left:'33%',width:'605px',border:0, cursor:'default' }
		});
	});

});
</script>


<script type="text/javascript">
//실시간 포인트 갱신
$(document).ready(function() {
	setInterval(function() {
		
		$.ajax({
			url : "/root_deposit/ajax_point_check.php",
			success: function(data) {
				//console.log('ajax point check ok');

				if (isNaN(data)) {
					console.log('ajax point check fail');
				} else {
					// 단순출력항목
					$('#realtime_point1,#realtime_point2,#realtime_point3').empty();
					$('#realtime_point1,#realtime_point2,#realtime_point3').append(number_format(data));
					// 변환불가항목
					$('#now_point').empty();
					$('#now_point').val(data);
				}

			},error : function(e) { console.log("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); }
		});
	}	, 5*1000);
});
</script>


<!-- 본문내용 E N D -->
<?
//include_once(HF_PATH.'/_tail.php');
include_once(HF_PATH.'/_tail.php');
?>