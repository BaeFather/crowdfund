<?
//쿼리
FUNCTION deposit_query($SEC)
{
	global $gstrMemberSeq;
	global $gstrMemberId;

	global $gstrAccountNum;

	global $gstrVaBankCode;
	global $gstrVirtualAccount;
	global $gstrVirtualAccount2;

	global $gstrRulesetDate;
	global $gstrPointTitle;

	SWITCH($SEC)
	{
		CASE "IBAuthWidthDrWal_CNT" :
			$Query = "SELECT COUNT(mb_no) AS CNT FROM IB_auth_withdrawal WHERE mb_no='".add_str($gstrMemberSeq)."'";
			//즉시출금설정 정보 (계좌당 승인에서 회원번호당 승인으로 변경 : 2019-01-07 이정환차장 요청)
			// SELECT COUNT(mb_no) AS cnt FROM IB_auth_withdrawal WHERE mb_no='".$member['mb_no']."' AND account_num='".$member['account_num']."
		BREAK;

		CASE "TotalChargeAhst_CNT" :
			$Query = "
				SELECT		IFNULL(SUM(va.tr_amt), 0) AS CNT
				FROM		vacs_ahst va
				INNER JOIN	g5_member mem ON va.iacct_no = mem.virtual_account AND va.caninp_si=''
				WHERE		mem.mb_no='".add_str($gstrMemberSeq)."'
			";
		BREAK;

		CASE "TotalChargeP2P_CNT" :
			$Query = "
				SELECT		IFNULL(SUM(CONVERT(TR_AMT, unsigned)), 0) AS CNT
				FROM		IB_FB_P2P_IP
				WHERE		CUST_ID='".add_str($gstrMemberSeq)."'
			";
		BREAK;

		CASE "TotalInvest_SUM";
			$Query = "
				SELECT		SUM(amount) AS TSUM
				FROM		cf_product_invest
				WHERE		member_idx='".add_str($gstrMemberSeq)."' AND invest_state='Y'
			";
		BREAK;

		CASE "CfProductInvest_CNT" :
			$Query = "SELECT	COUNT(*) as CNT FROM cf_product_invest
					  WHERE		member_idx='".add_str($gstrMemberSeq)."'";
		BREAK;

		CASE "InvestList_STATE" :
			$Query = "
					SELECT CNT, TSUM1, TSUM2, INSUM FROM
					(
						SELECT	'1' as kind,COUNT(amount) AS CNT, SUM(amount) AS TSUM1 FROM cf_product_invest WHERE member_idx = '".add_str($gstrMemberSeq)."' AND invest_state='Y'
					) t1 LEFT JOIN
					(
						SELECT	'1' as kind,SUM(amount) AS TSUM2, SUM(invest_return) AS INSUM FROM cf_product_invest pi INNER JOIN cf_product pd ON pi.product_idx = pd.idx WHERE pi.member_idx = '".add_str($gstrMemberSeq)."' AND invest_state='Y' AND pd.state IN (2,5)
					) t2 ON t1.kind = t2.kind";
		BREAK;

		CASE "RepaymentInterest_SUM" :
			$Query  = "
					SELECT
						IFNULL(SUM(a.invest_amount),0) AS TSUM
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
						b.member_idx='".add_str($gstrMemberSeq)."'
						AND b.invest_state='Y'
						AND c.state IN('1','2','5')";
		BREAK;

		CASE "EventInvest_List" :
			$Query = "
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
						epi.member_idx='".add_str($gstrMemberSeq)."'
					ORDER BY
						epi.idx DESC
					 ";
		BREAK;

		CASE "FailReturnPrice_SUM" :
			$Query = "
					SELECT		IFNULL(SUM(A.amount),0) AS TSUM
					FROM		cf_product_invest AS A
					LEFT JOIN	cf_product AS B ON A.product_idx = B.idx
					WHERE
								B.state = ''
								AND B.end_datetime < now()
								AND B.invest_end_date = ''
								AND A.invest_state = 'Y'
								AND B.end_date > SUBSTRING(NOW(),1,10)
								AND A.member_idx = '".add_str($gstrMemberSeq)."'
			";
		BREAK;

		CASE "TotalLoanCancelReturnPrice_SUM" :
			$Query = "
					SELECT		IFNULL(SUM(amount),0) AS TSUM
					FROM		cf_product_invest
					WHERE		invest_state = 'R' AND member_idx = '".add_str($gstrMemberSeq)."'
			";
		BREAK;

		CASE "TotalWithdrawPrice_SUM" :
			$Query = "
					SELECT		SUM(req_price) AS TSUM
					FROM		g5_withdrawal
					WHERE		state = '2'	AND mb_id = '".add_str($gstrMemberId)."'
			";
		BREAK;

		CASE "TotalReturnAmount_SUM" :
			$Query = "
					SELECT		(IFNULL(SUM(interest),0) + IFNULL(SUM(principal),0)) AS TSUM
					FROM		cf_product_give
					WHERE		member_idx = '".add_str($gstrMemberSeq)."' AND receive_method='1'
			";
		BREAK;

		// 세틀뱅크
		CASE "BankVal_1"	:
			$Query = "
				SELECT bank_cd, acct_no, cmf_nm, acct_st FROM vacs_vact WHERE bank_cd='".add_str($gstrVaBankCode)."' AND acct_no='".add_str($gstrVirtualAccount)."' ORDER BY acct_no DESC LIMIT 1
					";
		BREAK;
		//신한
		CASE "BankVal_2"	:
			$Query = "
				SELECT BANK_CODE, VR_ACCT_NO, CORP_NAME, USE_FLAG FROM KSNET_VR_ACCOUNT WHERE VR_ACCT_NO='".add_str($gstrVirtualAccount2)."' ORDER BY VR_ACCT_NO DESC LIMIT 1
					";
		BREAK;

		CASE "CfProductGive" :
			$Query = "SELECT COUNT(idx) AS CNT FROM cf_product_give WHERE member_idx='".add_str($gstrMemberSeq)."'";
		BREAK;

		CASE "LastDeposit" :
			/*
			[2019-07-22 13:30 까지의 내용]
			$last_deposit_sql = "
				SELECT
					po_point AS tr_amt,
					po_datetime AS trans_dt
				FROM
					g5_point
				WHERE 1
					AND mb_no='".$member['mb_no']."'
					AND LEFT(po_datetime, 16)>='".$ruleset_date."'
					AND po_content IN('예치금 충전','예치금 충전 (TYPE B)')
				ORDER BY
					po_id DESC
				LIMIT 1";
			*/
			$Query = "
				SELECT
					TR_AMT AS tr_amt, ERP_TRANS_DT  AS  trans_dt
				FROM
					IB_FB_P2P_IP
				WHERE 1
					AND CUST_ID='".add_str($gstrMemberSeq)."'
					AND TR_AMT_GBN='10' AND MEDIA_GBN='OK'
					AND ERP_TRANS_DT>='".preg_replace("/(-|:| )/", "", add_str($gstrRulesetDate))."'
				ORDER BY
					ERP_TRANS_DT DESC
				LIMIT 1
			";
		BREAK;

		CASE "RewardPoint" :

			$Query = "
				SELECT SUM(po_point) AS TSUM FROM g5_point WHERE mb_id='".add_str($gstrMemberSeq)."' AND po_content='".add_str($gstrPointTitle)."'
			";
		BREAK;
	}

	IF(!$Query) { $Query = $SEC; }

	return $Query;
}

// 최종 입금내역(g5_point 기준) 확인
$gstrRulesetDate			=	"2019-05-24 00:00:00";

//추천인 현황 시작
$gstrEventNo				=	"1";
$gstrEventSdate				=	"2016-11-29";
$gstrEventEdate				=	"2016-12-09";
$gstrPointTitle				=	"추천인 보상(".$gstrEventNo."차)";

$recomment_where.= " WHERE rec_mb_no='".$gstrMemberSeq."'";
$recomment_where.= " AND rec_mb_id='".$gstrMemberId."'";
// 테스트시	 $recomment_where.= " WHERE  rec_mb_id='blackbear123'";
$recomment_where.= " AND (LEFT(mb_datetime, 10) BETWEEN '".$gstrEventSdate."' AND '".$gstrEventEdate."') ";
$recomment_where.= " AND va_bank_code!='' ";
$recomment_where.= " AND (rec_date IS NOT NULL AND rec_date!='0000-00-00 00:00:00') ";

$strQueryRecommend	= "SELECT COUNT(mb_no) AS CNT, (COUNT(mb_no)*1000) AS CNT2 FROM g5_member".$recomment_where;
$strQueryMember		= "SELECT mb_id, mb_datetime FROM g5_member ".$recomment_where." ORDER BY mb_no DESC";
?>