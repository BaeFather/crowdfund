<?
// 추천인 현황

include_once('_common.php');

if (!$member["mb_id"]){ echo "ERROR-LOGIN"; exit; }

/********* 쿠폰 발급 (네이버 임시)  ************************************/

Class Coupon_lockup
{
	Public Function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public Function __destruct()
	{
	}

	Public Function fn_member_coupon_lockup()
	{
		global $member;

		$Query = "
			SELECT
				t2.pid, t2.cnumber, t2.use_date, t2.ava_sdate, t2.ava_edate
			FROM
				recommend_reward_log t1
			LEFT JOIN
				hloan_cupoint_reg t2  ON t1.cnumber=t2.cnumber
			WHERE
				t1.member_idx='".$member['mb_no']."'
			ORDER BY
				idx DESC";

		$Result = sql_query($Query);

		IF($Row=sql_fetch_array($Result))
		{
			$RowPid			  =	$Row["pid"];
			$RowCnumber		=	$Row["cnumber"];
			$RowUseDate		=	$Row["use_date"];
			$RowAvaSdate	=	$Row["ava_sdate"];
			$RowAvaEdate	=	$Row["ava_edate"];
			sql_free_result($Result);
		}

		$strPid		=	$this->fn_member_coupon_pid();
		$strCompany = "";

		FOR($i=0;$i<COUNT($strPid);$i++)
		{
			IF($strPid[$i]["pid"] == $RowPid)
			{
				$strCompany = $strPid[$i]["company"];
				break;
			}
		}

		return ARRAY(
						"company"=>$strCompany,				// 파트너 명
						"cnumber"=>$RowCnumber,				//쿠폰번호
						"usedate"=>SUBSTR($RowUseDate,0,10),	//발급일
						"sdate"=>$RowAvaSdate,				//쿠폰 사용 시작일
						"edate"=>$RowAvaEdate				//쿠폰 사용 종료일
				);
	}

	Public Function fn_member_coupon_pid()
	{
		$Query = "SELECT pid, company FROM hloan_ad_master";
		$Result = sql_query($Query);

		WHILE($Row=sql_fetch_array($Result))
		{
			$RowPid		=	$Row["pid"];
			$RowCompany	=	$Row["company"];

			$retval[]	=	ARRAY("pid"=>$RowPid, "company" => $RowCompany);
		}
		return $retval;

	}
}

$strCouponClass = new Coupon_lockup();

$strCoupon = $strCouponClass->fn_member_coupon_lockup();

/********* 쿠폰 발급 (네이버 임시)  ************************************/


$sql = "
	SELECT
		event_no, event_title, sdate, edate,
		recmdee_reward_type, recmdee_reward_goods_name, recmdee_reward_point,
		recmder_reward_type, recmder_reward_goods_name, recmder_reward_point,
		use_point, recm_kind
	FROM
		recommend_event_config
	ORDER BY
		idx DESC";
//if($_SERVER["REMOTE_ADDR"] == "220.117.134.164") { print_rr($sql,'text-align:left; font-size:12px;'); }
$res  = sql_query($sql);
$rows = sql_num_rows($res);

for($i=0; $i<$rows; $i++) {

	$REC_EVENT = sql_fetch_array($res);

	$RECRWD[$i] = $REC_EVENT;

	$RECRWD[$i]['sum_reward_amount'] = 0;

	if($REC_EVENT['event_no'] > 1) {

		// 추천, 피추천 로그
		$sqlx = "
			SELECT
				A.event_no, A.member_idx, A.`position`, A.target_member_idx, A.reward_amount, A.rdatetime,
				A.approved, A.approved_datetime, A.paid, A.paid_datetime, A.invalid, A.invalid_datetime,
				B.mb_id, B.mb_datetime
			FROM
				recommend_reward_log A
			LEFT JOIN
				g5_member B  ON A.member_idx=B.mb_no
			WHERE 1
				AND A.event_no = '".$REC_EVENT['event_no']."'
				AND A.`position` = 'recmder'
				AND (A.member_idx = '".$member['mb_no']."' OR A.target_member_idx = '".$member['mb_no']."')
			ORDER BY
				A.approved DESC,
				A.member_idx DESC,
				A.paid_datetime DESC";

		//print_rr($sqlx,'text-align:left; font-size:12px;');
		$resx	 = sql_query($sqlx);
		$rowsx = $resx->num_rows;

		for($k=0,$x=1; $k<$rowsx; $k++,$x++) {

			if( $R = sql_fetch_array($resx) ) {

				$RECRWD[$i]['LIST'][$k] = $R;

				// 피추천 내역(recmdee)일 경우 피추천 보상정보 가져오기
				if( $R['target_member_idx'] == $member['mb_no'] ) {
					$sqlx2 = "
						SELECT
							`position`, reward_amount, approved, approved_datetime, paid, paid_datetime, invalid, invalid_datetime, rdatetime
						FROM
							recommend_reward_log
						WHERE 1
							AND event_no = '".$R['event_no']."'
							AND position = 'recmdee'
							AND member_idx = '".$member['mb_no']."'
							AND target_member_idx = '".$R['member_idx']."'";
					$R2 = sql_fetch($sqlx2);

					//echo $sqlx2 . ";<br/>\n";
					//print_rr($R2,'text-align:left; font-size:12px;color:red');

					$RECRWD[$i]['LIST'][$k]['position'] = 'recmdee';

					if($R2) {
						$RECRWD[$i]['LIST'][$k]['reward_amount']     = ( in_array($R2['approved'], array('1','2')) ) ? $R2['reward_amount'] : '';
						$RECRWD[$i]['LIST'][$k]['rdatetime']         = $R2['rdatetime'];
						$RECRWD[$i]['LIST'][$k]['approved']          = $R2['approved'];
						$RECRWD[$i]['LIST'][$k]['approved_datetime'] = $R2['approved_datetime'];
						$RECRWD[$i]['LIST'][$k]['paid']              = $R2['paid'];
						$RECRWD[$i]['LIST'][$k]['paid_datetime']     = $R2['paid_datetime'];
						$RECRWD[$i]['LIST'][$k]['invalid']           = $R2['invalid'];
						$RECRWD[$i]['LIST'][$k]['invalid_datetime']  = $R2['invalid_datetime'];
					}

				}

			}

		}

	}
	else {

		// 1회차 이벤트는 지급 여부를 g5_point 에서 확인
		$point_title = "추천인 보상(".$REC_EVENT['event_no']."차)";

		$R = sql_fetch("SELECT po_point, po_datetime FROM g5_point WHERE mb_no='".$member['mb_no']."' AND po_content='".$point_title."'");
		$RECRWD[$i]['paid_amount']   = $R['po_point'];
		$RECRWD[$i]['paid_datetime'] = $R['po_datetime'];

		$RECRWD[$i]['LIST'] = array();

		// 피추천인 목록 (보상지급내역포함)
		$sqlx = "
			SELECT
				mb_no, mb_id, IF(A.member_type='2',A.mb_co_name,A.mb_name) AS mb_title, A.rdatetime
			FROM
				g5_member A
			WHERE 1
				AND LEFT(A.rdatetime,10) BETWEEN '".$REC_EVENT['sdate']."' AND '".$REC_EVENT['edate']."'
				AND A.rec_mb_no='".$member['mb_no']."'
			ORDER BY
				A.mb_no DESC";
		$resx	 = sql_query($sqlx);
		$rowsx = $resx->num_rows;
		for($k=0; $k<$rowsx; $k++) {
			$RECRWD[$i]['LIST'][$k] = sql_fetch_array($resx);
		}

	}

}

//print_rr($RECRWD,'text-align:left; font-size:12px; line-height:13px;');

if(G5_IS_MOBILE) {
	include_once("ajax_recommender_m.php");
	return;
}

?>
<style>
.rrlock {display:block;}
.rnone {display:none;}
</style>

<h3>쿠폰발급 현황</h3>
<div class="type03 mb30 rguide">
	<table>
		<colgroup>
			<col style='width:14.28%'>
			<col style='width:14.28%'>
			<col style='width:14.28%'>
			<col style='width:50%;'>
		</colgroup>
		<tr>
			<th>쿠폰구분</th>
			<th>쿠폰번호</th>
			<th>쿠폰발급일</th>
			<th>쿠폰유효기간</th>
		</tr>
		<tr style="background:#E6EAF9;font-weight:bold;">
			<td style="height:20px;color:navy"><?=$strCoupon["company"];?></td>
			<td style="height:20px;color:navy"><?=$strCoupon["cnumber"];?></td>
			<td style="height:20px;color:navy"><?=$strCoupon["usedate"];?></td>
			<td style="height:20px;color:navy"><?=$strCoupon["sdate"];?> ~ <?=$strCoupon["edate"];?></td>
		</tr>
	</table>
</div>

<h3>추천 현황</h3>
<?
$kk = 0;
for($i=0; $i<count($RECRWD); $i++) {

	// 2020.01.02 마케팅요청  리스트가 있는 항목만 보이게
	if(count($RECRWD[$i]['LIST']) > 0) {

		//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') { print_rr($RECRWD[$i],'font-size:12px;line-height:15px;'); }

		$print_sum_reward_amount = ( in_array($RECRWD[$i]['recmdee_reward_type'], array('1','2')) ) ? number_format($RECRWD[$i]['sum_reward_amount']).'원' : '';

?>
<div style="padding:4px 8px;"><?=$RECRWD[$i]['event_title']?> &nbsp; (대상기간: <?=$RECRWD[$i]['sdate']?> ~ <?=$RECRWD[$i]['edate']?>)</div>
<div class="type03 mb30" id="rarea<?=$i?>">
	<table>
		<colgroup>
			<col style='width:%'>
			<col style='width:15%'>
			<col style='width:15%'>
			<col style='width:15%'>
			<col style='width:15%'>
			<col style='width:15%'>
			<col style='width:15%'>
		</colgroup>
		<tr>
			<th>NO</th>
			<th>아이디</th>
			<th>가입일</th>
			<th>보상지급품</th>
			<th>보상금액</th>
			<th>보상확정일</th>
			<th>지급일</th>
		</tr>
		<tr style="background:#E6EAF9;font-weight:bold;">
			<td style="height:20px;color:navy">합계: <?=count($RECRWD[$i]['LIST'])?>건</td>
			<td style="height:20px;color:navy"></td>
			<td style="height:20px;color:navy"></td>
			<td style="height:20px;color:navy"></td>
			<td style="height:20px;color:navy"><!--<?=$print_sum_reward_amount?>--></td>
			<td style="height:20px;color:navy"></td>
			<td style="height:20px;color:navy"></td>
		</tr>
<?
	$list_count = count($RECRWD[$i]['LIST']);

	for($k=0,$num=$list_count; $k<$list_count; $k++,$num--) {

		$print_target_mb = ($RECRWD[$i]['LIST'][$k]['position']=='recdee') ? $RECRWD[$i]['LIST'][$k]['mb_id'] : substr($RECRWD[$i]['LIST'][$k]['mb_id'], 0, 2)."**********";
		$print_mb_date = substr($RECRWD[$i]['LIST'][$k]['rdatetime'], 0, 10);
		if($print_mb_date == '0000-00-00') $print_mb_date = '';
		$print_appr_date = substr($RECRWD[$i]['LIST'][$k]['approved_datetime'], 0, 10);
		$print_paid_date = substr($RECRWD[$i]['LIST'][$k]['paid_datetime'], 0, 10);


		$_reward_type       = $RECRWD[$i]['LIST'][$k]['position'] . "_reward_type";
		$_reward_goods_name = $RECRWD[$i]['LIST'][$k]['position'] . '_reward_goods_name';
		$_reward_point      = $RECRWD[$i]['LIST'][$k]['position'] . "_reward_point";

		if($RECRWD[$i]['LIST'][$k]['approved']=='1') {

			if($RECRWD[$i][$_reward_type] == '3') {
				$print_goods_name   = ($RECRWD[$i][$_reward_goods_name]) ? $RECRWD[$i][$_reward_goods_name] : '상품권/쿠폰';
				$print_reward_point = "-";
			}
			else {
				$print_goods_name   = ($RECRWD[$i][$_reward_type] == '2') ? "포인트" : "예치금";
				$print_reward_point = number_format($RECRWD[$i][$_reward_point]) . '원';
			}

		}
		else {
			$print_goods_name = $print_reward_point = '';
		}

		$tr_bgcolor = (($k%2)==1) ? '#F2F2F2' : '';
		$fcolor = ($RECRWD[$i]['LIST'][$k]['member_idx']==$member['mb_no']) ? '#FF2222' : '';

?>
			<tr style="height:20px;background:<?=$tr_bgcolor?>;">
				<td><?=$num?></td>
				<td><span style="color:<?=$fcolor?>"><?=$print_target_mb?></span></td>
				<td><?=$print_mb_date?></td>
				<td><?=$print_goods_name?></td>
				<td><?=$print_reward_point?></td>
				<td><?=$print_appr_date?></td>
				<td><?=$print_paid_date?></td>
			</tr>
<?
	}
?>
	</table>
</div>
<?
		$kk++;
	}
}

if($kk==0) {

	echo "<div>추천인 참여정보가 없습니다.</div>";

}
?>

<script>
function check_layer(targetlayer) {
	$(".rguide").css("display","none");
	$("#"+targetlayer).css("display","block");
}
</script>

<?

@sql_close();
exit;

?>