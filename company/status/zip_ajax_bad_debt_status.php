<?
///////////////////////////////////////////////////////////////////////////////
// 부실채권 매각 현황
///////////////////////////////////////////////////////////////////////////////

include_once('../../common.cli.php');

while(list($k, $v) = each($_REQUEST)) { if(!is_array(${$k})) ${$k} = trim($v); }


if($year=='' || strlen($year) > 4) $year = date('Y') - 1;

$date = $year;
$date.= ($month) ? '-'.$month : '';

$sql = "
	SELECT
		A.idx, A.product_idx, A.sale_amount, A.sale_place, A.sale_date, A.product_mask_title, A.mask_recruit_amount,
		B.start_num, B.title, B.recruit_amount, B.category, B.category2, B.mortgage_guarantees
	FROM
		cf_biz_info_re A
	LEFT JOIN
		cf_product B  ON A.product_idx=B.idx
	WHERE 1
		AND A.section = '4' AND A.product_idx != ''
 -- AND B.state = '9'
		AND LEFT(A.sale_date, ".strlen($date).") = '".$date."'
	ORDER BY
		A.idx DESC";
$res  = sql_query($sql);
$rows = $res->num_rows;

if($rows) {

	$ARR = array(
		'result' => 'SUCCESS',
		'sdata' => array(),
		'message' => ''
	);

	while( $row = sql_fetch_array($res) ) {

		//if($row['category']=='1') { $category = ($row['mortgage_guarantees']=='1') ? '주택담보' : '부동산 PF'; }
		//if($row['category']=='2') { $category = ($row['mortgage_guarantees']=='1') ? '주택담보' : '부동산 PF'; }
		if($row['category']=='2') { $category = ($row['mortgage_guarantees']=='1') ? '주택담보' : '부동산'; }
		else if($row['category']=='3') $category = '매출채권';
		else $category = '동산';

		$print_title = "";
		if($row['product_mask_title']) {
			$print_title = $row['product_mask_title'];
		}
		else {
			$print_title = ($row['start_num']) ? "제".$row['start_num']."호" : $row['title'];
		}

		$recruit_amount = ($row['mask_recruit_amount'] > 0) ? $row['mask_recruit_amount'] : $row['recruit_amount'];

		$add_array = array(
			'category'       => $category,
			'start_num'      => $print_title,
			'recruit_amount' => (string)$recruit_amount,
			'sale_amount'    => (string)$row['sale_amount'],
			'sale_place'     => $row['sale_place'],
			'sale_date'      => $row['sale_date']
		);

		array_push($ARR['sdata'], $add_array);

	}

	echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

}
else {

	$ARR = array('result'=>'FAIL', 'message'=>'채권 매각 내역이 없습니다.');
	echo json_encode($ARR, JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES);

}

sql_close();
exit;

?>
