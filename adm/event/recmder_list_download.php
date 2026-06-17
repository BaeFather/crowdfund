<?
// 추천인(추천하는 사람) 리스트 엑셀 다운로드

include_once('_common.php');

while( list($k,$v) = each($_REQUEST) ) { ${$k} = trim($v); }


// 이벤트 설정값
$EVENT_CONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no='".$event_no."'");


$where = "";
$where.= " AND A.member_group='F' AND A.mb_level NOT IN('9','10')";
$where.= " AND LEFT(A.mb_datetime, 10) BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'";
$where.= " AND A.rec_mb_no IS NOT NULL";
$where.= " AND position = 'recmder'";

// 2021-11-12 추가 ----------------------------------------------------------------
if($EVENT_CONF['only_rec_id']){
	$where.= " AND A.rec_mb_id='".$EVENT_CONF['only_rec_id']."'";
}
else {
	$where.= " AND A.rec_mb_id!='hello'";
}
// 2021-11-12 추가 ----------------------------------------------------------------

if($state) {
	if($state=='null') {
		$where.= " AND B.approved='' AND B.paid='' AND B.invalid=''";
	}
	else {
		if($state=='invalid') $where.= " AND B.invalid='1'";
		else if($state=='approved') $where.= " AND B.approved='1'";
		else if($state=='paid') $where.= " AND B.paid='1'";
	}
}
if($date_field) {
	if($sdate) $where.= " AND LEFT($date_field, 10)>='".$sdate."'";
	if($edate) $where.= " AND LEFT($date_field, 10)<='".$edate."'";
}
if($field && $keyword) {
	$where.= ($field=='A.mb_hp') ? " AND $field = '".masterEncrypt($keyword, false)."'" : " AND $field = '".$keyword."'";
}

$sql = "
	SELECT
		COUNT(A.mb_no) AS cnt
	FROM
		g5_member A
	LEFT JOIN
		recommend_reward_log B  ON A.mb_no=B.member_idx
	WHERE (1)
		$where";
//print_rr($sql, 'font-size:12px');
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$rows = 999999;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql_order = "";
$sql_order.= " ( CASE WHEN approved='' THEN 1 ELSE 2 END) DESC,";
$sql_order.= " recmder_invest_amount DESC, recmder_invest_count DESC,";
$sql_order.= " mb_no DESC";


//**** 이벤트에 따른 대상상품군 선별 ****//
if($EVENT_CONF['prdt_ca']=='2') {
	$sqlto = " AND CP.category='2' ";																		// 부동산 전체를 대상으로..
}
else if($EVENT_CONF['prdt_ca']=='2-1') {
	$sqlto = " AND CP.category='2' AND CP.mortgage_guarantees='' ";			// PF만
}
else if($EVENT_CONF['prdt_ca']=='2-2') {
	$sqlto = " AND CP.category='2' AND CP.mortgage_guarantees='1' ";		// 주담대만
}
else if($EVENT_CONF['prdt_ca']=='3') {
	$sqlto = " AND CP.category='3' ";																		// 매출채권 대상
}
else if($EVENT_CONF['prdt_ca']=='1') {
	$sqlto = " AND CP.category='1' ";																		// 동산
}
else {
	//
}
//**** 이벤트에 따른 대상상품군 선별 ****//


$sql = "
	SELECT
		mb_no, mb_id, mb_name, mb_co_name, mb_hp, mb_datetime, rec_mb_no, rec_mb_id,
		approved, approved_datetime, paid, paid_datetime, invalid, invalid_datetime,
		bank_code, bank_acct, bank_private_name,recmder_invest_count,recmder_invest_amount
	FROM
	(
		SELECT
			A.mb_no, A.mb_id, A.mb_name, A.mb_co_name, A.mb_hp, A.mb_datetime, A.rec_mb_no, A.rec_mb_id,
			B.approved, B.approved_datetime, B.paid, B.paid_datetime, B.invalid, B.invalid_datetime,
			B.bank_code, B.bank_acct, B.bank_private_name,
			(
				SELECT
					COUNT(CPI.idx)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.invest_state IN('Y','R')
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND CP.state IN('1','2','5','7','8')
					".$sqlto."
			) AS recmder_invest_count,
			(
				SELECT
					IFNULL(SUM(CPI.amount),0)
				FROM
					cf_product_invest CPI
				LEFT JOIN
					cf_product CP  ON CPI.product_idx=CP.idx
				WHERE (1)
					AND CPI.invest_state IN('Y','R')
					AND CPI.member_idx=A.mb_no
					AND CPI.insert_date BETWEEN '".$EVENT_CONF['sdate']."' AND '".$EVENT_CONF['edate']."'
					AND CP.state IN('1','2','5','7','8')
					".$sqlto."
			) AS recmder_invest_amount
		FROM
			g5_member A
		LEFT JOIN
			recommend_reward_log B  ON A.mb_no=B.member_idx
		WHERE (1)
			$where
	) t1
";

$sql.= "
	ORDER BY
		$sql_order
	-- LIMIT
	--	$from_record, $rows";

//if($member['mb_id']=='admin_sori9th') { print_rr($sql,'font-size:12px;line-height:14px;'); }

$result = sql_query($sql);
$rcount = $result->num_rows;
for($i=0; $i<$rcount; $i++) {

	$LIST[$i] = sql_fetch_array($result);

	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
	$LIST[$i]['reward_goods']  = '';
	$LIST[$i]['reward_amount'] = 0;
	$LIST[$i]['register_num']  = '';

	if($LIST[$i]['recmder_invest_amount'] >= $EVENT_CONF['use_point']) {			// 투자금액을 충족할 경우

		if($EVENT_CONF['recmder_reward_goods_name']) {
			$LIST[$i]['reward_goods'] = $EVENT_CONF['recmder_reward_goods_name'];
		}
		else {
			switch($EVENT_CONF['recmder_reward_type']) {
				case '2' : $LIST[$i]['reward_goods'] = '포인트'; break;
				case '3' : $LIST[$i]['reward_goods'] = '쿠폰'; break;
				case '1' :
				default  : $LIST[$i]['reward_goods'] = '예치금'; break;
			}
		}

		$LIST[$i]['reward_amount'] = $EVENT_CONF['recmder_reward_point'];

		// 승인 또는 지급완료 플래그가 있을 경우
		if($LIST[$i]['approved']=='1' || $LIST[$i]['paid']=='1') {
			if($LIST[$i]["member_type"] == '2') {
				$register_num = preg_replace("/-/", "", $LIST[$i]['mb_co_reg_num']);
				$LIST[$i]['register_num'] = substr($register_num,0,3)."-".substr($register_num,3,2)."-".substr($register_num,-5);
			}
			else {
				$register_num = getJumin($LIST[$i]['mb_no']);
				$LIST[$i]['register_num'] = substr($register_num,0,6)."-".substr($register_num,-7);
			}

			if($EVENT_CONF['recmder_reward_type']=='1') {
				$LIST[$i]['bank_name']         = $BANK[$LIST[$i]['bank_code']];
				$LIST[$i]['bank_acct']         = $LIST[$i]['bank_acct'];
				$LIST[$i]['bank_private_name'] = $LIST[$i]['bank_private_name'];
			}
			else {
				$LIST[$i]['bank_name'] = $LIST[$i]['bank_acct'] = $LIST[$i]['bank_private_name'] = '';
			}
		}

	}

}
$list_count = count($LIST);
sql_free_result($result);

$num = $total_count - $from_record;

//if($member['mb_id']=='admin_sori9th') { print_rr($LIST,'font-size:12px;line-height:14px;'); }

$file_name = $EVENT_CONF['event_title'] . "-추천인.xls";

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
				<th style="background:#3366CC;color:#FFF" class="text-center">NO</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">회원번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">아이디</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">성명</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">연락처</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">가입일</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">추천ID</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">피추천인번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자수(건)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">누적투자금(원)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상품</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상금액(원)</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">보상확정일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">무효처리일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">지급일시</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">은행</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">계좌번호</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">예금주</th>
				<th style="background:#3366CC;color:#FFF" class="text-center">주민번호</th>
			</tr>
		</thead>
		<form id="form1" name="form1">
			<input type="hidden" id="action" name="action">
			<input type="hidden" id="event_no" name="event_no" value="<?=$event_no?>">
		<tbody>
<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {
?>
			<tr align="center">
				<td><?=$num?></td>
				<td><?=$LIST[$i]['mb_no']?></a></td>
				<td><?=$LIST[$i]['mb_id']?></a></td>
				<td><?=$LIST[$i]['mb_name']?></a></td>
				<td style="mso-number-format:'\@';"><?=$LIST[$i]['mb_hp']?></td>
				<td><?=substr($LIST[$i]['mb_datetime'],0,16)?></td>
				<td style="color:#AAAAAA;"><?=$LIST[$i]['rec_mb_id']?></a></td>
				<td style="color:#AAAAAA;"><?=$LIST[$i]['rec_mb_no']?></a></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_count'])?></td>
				<td align="right"><?=number_format($LIST[$i]['recmder_invest_amount'])?></td>
				<td align="center"><?=$LIST[$i]['reward_goods']?></td>
				<td align="right"><?=number_format($LIST[$i]['reward_amount'])?></td>
				<td><?=($LIST[$i]['approved']=='1') ? substr($LIST[$i]['approved_datetime'],0,16) : ''; ?></td>
				<td style="color:#FF2222"><?=($LIST[$i]['invalid']=='1') ? substr($LIST[$i]['invalid_datetime'],0,16) : ''; ?></td>
				<td><?=($LIST[$i]['paid']=='1') ? substr($LIST[$i]['paid_datetime'],0,16) : ''; ?></td>
				<td><?=$LIST[$i]['bank_name']?></td>
				<td style="mso-number-format:'\@';"><?=$LIST[$i]['bank_acct']?></td>
				<td><?=$LIST[$i]['bank_private_name']?></td>
				<td><?=$LIST[$i]['register_num']?></td>
			</tr>
<?
		$num--;
	}
}
?>
		</tbody>
		</form>
	</table>