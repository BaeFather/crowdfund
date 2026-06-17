<?
// 피추천인(추천받는 사람) 리스트 엑셀 다운로드

include_once('_common.php');

while( list($k,$v) = each($_REQUEST) ) { ${$k} = trim($v); }


// 이벤트 설정값
$EVENT_CONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no='".$event_no."'");


$recmder_count_sql = "SELECT COUNT(mb_no) FROM g5_member WHERE rec_mb_no=A.mb_no AND LEFT(mb_datetime,10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'";		// 추천받은 횟수

$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND ( $recmder_count_sql ) > 0";		// 추천받은 횟수가 있는 데이터만..

// 2021-11-12 추가 ----------------------------------------------------------------
if($EVENT_CONF['only_rec_id']) {
	$where.= " AND A.mb_id='".$EVENT_CONF['only_rec_id']."'";		// 피추천인이 고정된 이벤트의 경우 (피추천인ID: hello)
}
else {
	$where.= " AND A.mb_id!='hello'";			// 일반 추천이벤트의 경우
}
// 2021-11-12 추가 ----------------------------------------------------------------

if($field && $keyword) {
	$where.= ($field=='A.mb_hp') ? " AND $field = '".masterEncrypt($keyword, false)."'" : " AND $field = '".$keyword."'";
}


$sql_order = "";
if($sort_field) {
	$sql_order.= $sort_field." ".$sort.", ";
}
else {
	$sql_order.= "approved_count DESC, ";
	$sql_order.= "recmder_count DESC, ";
	$sql_order.= "recmder_invest_amount DESC, ";
}
$sql_order.= "mb_no DESC";


//**** 이벤트에 따른 대상상품군 선별 ****//
if($EVENT_CONF['prdt_ca']=='2') {
	$sqlto = " AND CC.category='2' ";																		// 부동산 전체를 대상으로..
}
else if($EVENT_CONF['prdt_ca']=='2-1') {
	$sqlto = " AND CC.category='2' AND CC.mortgage_guarantees='' ";			// PF만
}
else if($EVENT_CONF['prdt_ca']=='2-2') {
	$sqlto = " AND CC.category='2' AND CC.mortgage_guarantees='1' ";		// 주담대만
}
else if($EVENT_CONF['prdt_ca']=='3') {
	$sqlto = " AND CC.category='3' ";																		// 매출채권 대상
}
else if($EVENT_CONF['prdt_ca']=='1') {
	$sqlto = " AND CC.category='1' ";																		// 동산
}
else {
	//
}
//**** 이벤트에 따른 대상상품군 선별 ****//

$sql = "
	SELECT
		A.mb_no, A.member_type, A.mb_id, A.mb_name, A.mb_co_name, A.mb_hp, A.mb_datetime,
		A.va_bank_code2 AS bank_code,
		A.virtual_account2 AS bank_acct,
		A.va_private_name2 AS bank_private_name,
		(
			$recmder_count_sql
		) AS recmder_count,
		(
			SELECT
				COUNT(AA.idx)
			FROM
				cf_product_invest AA
			LEFT JOIN
				g5_member BB  ON AA.member_idx=BB.mb_no
			LEFT JOIN
				cf_product CC  ON AA.product_idx=CC.idx
			WHERE (1)
				AND AA.invest_state IN('Y','R')
				AND BB.rec_mb_no=A.mb_no
				AND LEFT(BB.mb_datetime,10) BETWEEN '".$EVENT_CONF["sdate"]."' AND '".$EVENT_CONF["edate"]."'
				AND AA.insert_date BETWEEN '".$EVENT_CONF["sdate"]."' AND '".$EVENT_CONF["edate"]."'
				AND CC.state IN('1','2','5','6')
				".$sqlto."
		) AS recmder_invest_count,
		(
			SELECT
				IFNULL(SUM(AA.amount),0)
			FROM
				cf_product_invest AA
			LEFT JOIN
				g5_member BB  ON AA.member_idx=BB.mb_no
			LEFT JOIN
				cf_product CC  ON AA.product_idx=CC.idx
			WHERE (1)
				AND AA.invest_state IN('Y','R')
				AND BB.rec_mb_no=A.mb_no
				AND LEFT(BB.mb_datetime,10) BETWEEN '".$EVENT_CONF["sdate"]."' AND '".$EVENT_CONF["edate"]."'
				AND AA.insert_date BETWEEN '".$EVENT_CONF["sdate"]."' AND '".$EVENT_CONF["edate"]."'
				AND CC.state IN('1','2','5','6')
				".$sqlto."
		) AS recmder_invest_amount,
		(
			SELECT
				COUNT(idx)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND approved='1' AND invalid=''
		) AS approved_count,
		(
			SELECT
				IFNULL(SUM(reward_amount),0)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND approved='1' AND invalid=''
		) AS approved_amount,
		(
			SELECT
				COUNT(idx)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND paid='1' AND invalid=''
		) AS paid_count,
		(
			SELECT
				IFNULL(SUM(reward_amount),0)
			FROM
				recommend_reward_log
			WHERE (1)
				AND member_idx=A.mb_no
				AND event_no='".$EVENT_CONF['event_no']."'
				AND position='recmdee'
				AND paid='1' AND invalid=''
		) AS paid_amount
	FROM
		g5_member A
	WHERE (1)
		$where
	ORDER BY
		$sql_order";

//if($member['mb_id']=='admin_sori9th') { print_rr($sql,'font-size:12px;line-height:14px;'); }

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {
	$LIST[$i] = sql_fetch_array($result);

	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);

	if($LIST[$i]['paid_count']) {
		$PAID = sql_fetch("SELECT bank_code, bank_acct, bank_private_name FROM recommend_reward_log WHERE  event_no = '".$EVENT_CONF['event_no']."' AND position = 'recmdee' AND member_idx = '".$LIST[$i]['mb_no']."'");
		$LIST[$i]['bank_code'] = $PAID['bank_code'];
		$LIST[$i]['bank_acct'] = $PAID['bank_acct'];
		$LIST[$i]['bank_private_name'] = $PAID['bank_private_name'];
	}

}
$list_count = count($LIST);
sql_free_result($result);

//if($member['mb_id']=='admin_sori9th') { print_rr($LIST,'font-size:12px;line-height:14px;'); exit;}


$file_name = $EVENT_CONF['event_title'] . "-피추천인.xls";

header( "Content-type: application/vnd.ms-excel;" );
header( "Content-Disposition: attachment; filename={$file_name}" );
header( "Content-description: PHP5 Generated Data" );

?>

	<table border=1>
		<colgroup>
			<col style="width:60px">
			<col style="width:60px">
		</colgroup>
		<thead>
			<tr>
				<th style="background:#CC3366;color:#FFF" class="text-center">NO</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">회원번호</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">아이디</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">성명</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">연락처</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">가입일시</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인수(명)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인<br/>누적투자수(건)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">추천인<br/>누적투자금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">보상확정건수</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">보상품</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">확정보상금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">지급건수</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">지급보상금(원)</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">은행</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">계좌번호</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">예금주</th>
				<th style="background:#CC3366;color:#FFF" class="text-center">주민등록번호</th>
			</tr>
		</thead>
		<tbody>
<?
if($list_count) {
	for($i=0,$num=$list_count; $i<$list_count; $i++,$num--) {

		$print_name = $print_reward_goods_name = $print_bank = $print_bank_acct = $print_bank_private_name = $register_num = '';

		$print_name = ($LIST[$i]['member_type'] == '2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];

		if($LIST[$i]['approved_count'] > 0) {

			if($EVENT_CONF['recmdee_reward_goods_name']) {
				$print_reward_goods_name = $EVENT_CONF['recmdee_reward_goods_name'];
			}
			else {
				switch($EVENT_CONF['recmdee_reward_goods_name']) {
					case '2' : $print_reward_goods_name = '포인트'; break;
					case '3' : $print_reward_goods_name = '쿠폰'; break;
					case '1' :
					default  : $print_reward_goods_name = '예치금'; break;
				}
			}

			if($EVENT_CONF['recmdee_reward_type']=='1') {
				$print_bank              = $BANK[$LIST[$i]['bank_code']];
				$print_bank_acct         = $LIST[$i]['bank_acct'];
				$print_bank_private_name = $LIST[$i]['bank_private_name'];
			}

			if($LIST[$i]['member_type'] == '2') {
				$register_num = str_replace("-", "", $LIST[$i]['mb_co_reg_num']);
				$register_num = substr($register_num,0,3)."-".substr($register_num,3,2)."-".substr($register_num,-5);
			}
			else {
				$register_num = getJumin($LIST[$i]['mb_no']);
				$register_num = substr($register_num,0,6)."-".substr($register_num,-7);
			}

		}

?>
			<tr align="center">
				<td><?=$num?></td>
				<td><?=$LIST[$i]['mb_no']?></td>
				<td><?=$LIST[$i]['mb_id']?></td>
				<td><?=$print_name?></td>
				<td style=mso-number-format:'\@'><?=$LIST[$i]['mb_hp']?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td align="right"><?=number_format($LIST[$i]['approved_count'])?></td>
				<td align="right"><?=$print_reward_goods_name?></td>
				<td align="right"><?=number_format($LIST[$i]['approved_amount'])?></td>
				<td align="right"><?=number_format($LIST[$i]['paid_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['paid_amount'])?></td>
				<td><?=$print_bank?></td>
				<td style="mso-number-format:'\@';"><?=$print_bank_acct?></td>
				<td><?=$print_bank_private_name?></td>
				<td><?=$register_num?></td>
			</tr>
<?
	}
}
else {
	echo "<tr><td colspan='18' align='center'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
	</table>

<?
	sql_close();
?>