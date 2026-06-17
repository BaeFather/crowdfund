<?php
set_time_limit(0);

include_once('./_common.php');
include_once('../../lib/sms.lib.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while( list($k, $v) = each($_POST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

$action = "save";

IF(!$event_no || !$action) { ECHO "접근이 올바르지 않습니다"; EXIT; }

// 이벤트 설정값
$ECONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no='".$event_no."'");


FUNCTION fn_partner_event_sms_send($intRidx, $strCNumber,$strEdate,$strCphone)
{
	global $_admin_sms_number;

	IF(!$_admin_sms_number || !$strCNumber)
	{

	} ELSE {

		$strEdateArr = EXPLODE("-",$strEdate);

		$sms_msg = "
		[헬로펀딩] 이벤트 쿠폰지급

		네이버페이x헬로펀딩 이벤트에 참여해 주셔서 감사합니다.
		네이버페이 포인트 4천원 쿠폰을 전달 드리오니 등록 후 사용하시기 바랍니다.


		■ 쿠폰번호  : ".$strCNumber."

		■ 쿠폰 등록 바로가기

		* Mobile
		https://m.pay.naver.com/b/pointcoupon?couponNumber=".$strCNumber."

		*PC
		https://benefit.pay.naver.com/pointcoupon?couponNumber=".$strCNumber."


		[사용방법 및 유의사항]
		★ 등록기간: ".$strEdateArr[0]."년 ".$strEdateArr[1]."월 ".$strEdateArr[2]."일 까지

		★ 사용방법(PC&모바일)
		① 네이버페이 홈 접속(pay.naver.com)
		② 네이버ID로 로그인
		③ 이벤트/쿠폰 메뉴 접속
		④ 쿠폰등록
		⑤ 쿠폰번호 입력

		- 문의 : 헬로펀딩 1588-6760
		";

		//$intTime = TIME()+1000;
		$intTime = DATE("Y-m-d H:i:s",TIME()+600);

		//$strCphone = "01023334749";

		unit_sms_send($_admin_sms_number, $strCphone, $sms_msg, $intTime);


		$Query = "INSERT INTO hloan_partner_event_log (ridx, cnumber, cphone, msg, reg_date) VALUES ('".$intRidx."','".$strCNumber."', '".$strCphone."','".$sms_msg."',now())";
		sql_query($Query);
	}
}


IF($action=='save')
{
	$Query = "SELECT rcidx, cnumber,ava_edate FROM hloan_cupoint_reg WHERE recyn='N' AND pid='naverpay' ORDER BY rcidx ASC";
	$Result = sql_query($Query);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$rcidx			=	$Row["rcidx"];
		$cnumber		=	$Row["cnumber"];
		$ava_edate		=	$Row["ava_edate"];
		$strCoupon[]	=	ARRAY($rcidx, $cnumber, $ava_edate);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}

	$where = " WHERE member_group='F' AND mb_level NOT IN('9','10')";
	$where.= " AND LEFT(mb_datetime, 10) BETWEEN '".$ECONF["sdate"]."' AND '".$ECONF["edate"]."'";
	$where.= " AND pid='naverpay'";
	$where.= " AND (2020-left(mb_birth,4)) >= 19";

	$Query = "	SELECT A.mb_no, A.mb_hp, IFNULL(B.idx,0) as idx, IFNULL(rcidx,0) as rcidx FROM
				(
					SELECT mb_no, mb_hp FROM g5_member ".$where."
				) A
				LEFT JOIN
				(SELECT idx, rcidx, member_idx FROM recommend_reward_log WHERE event_no='".$event_no."' AND position='recmder') B
				ON A.mb_no=B.member_idx
				WHERE B.rcidx is null OR B.rcidx=0
				";

	$Result = sql_query($Query);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		UNSET($Q2);

		$RowMbNo	=	$Row["mb_no"];
		$RowIdx		=	$Row["idx"];
		$RowMbHp	=	$Row["mb_hp"];

		IF($RowIdx)
		{
			$Q2 = "UPDATE recommend_reward_log SET";
			$Q2 .= " rcidx='".$strCoupon[$i][0]."',";
			$Q2 .= " cnumber='".$strCoupon[$i][1]."'";
			$Q2 .= " WHERE idx='".$RowIdx."'";
		} ELSE {
			$Q2 = "INSERT INTO recommend_reward_log SET";
			$Q2 .= " event_no='".$event_no."',";
			$Q2 .= " member_idx='".$RowMbNo."',";
			$Q2 .= " position='recmder',";
			$Q2 .= " rcidx='".$strCoupon[$i][0]."',";
			$Q2 .= " cnumber='".$strCoupon[$i][1]."'";

		}
		sql_query($Q2);

		/*** SMS전송 ****/
		//$RowMbHpVal = masterDecrypt($RowMbHp, false);
		//fn_partner_event_sms_send($strCoupon[$i][0],$strCoupon[$i][1], $strCoupon[$i][2], $RowMbHpVal);
		/*** SMS전송 ****/

		// 쿠폰테이블 업데이트
		$Q3 = "UPDATE hloan_cupoint_reg SET recyn='Y', mem_date=now(), use_date=now() WHERE rcidx='".$strCoupon[$i][0]."'";
		sql_query($Q3);

		$i++;
	}

	IF($i > 0)
	{
		sql_free_result($Result);
	}

	$RETURN_ARR = array('result'=>'SUCCESS', 'message'=>'쿠폰이 정상 발급 되었습니다.');
	echo json_encode($RETURN_ARR);
}
exit;
?>