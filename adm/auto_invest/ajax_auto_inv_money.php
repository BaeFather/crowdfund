<?
include_once("_common.php");
?>
<?
$s_type = trim($_REQUEST['s_type']);
$ai_grp_idx = trim($_REQUEST['ai_grp_idx']);

$auto_money = get_auto_real_money($s_type, "N", $ai_grp_idx);

echo json_encode($auto_money);
//echo "<pre>"; print_r($auto_money); echo "</pre>";
exit();
?>
<?
function get_auto_real_money11($s_type, $ret_list) {

	$RES = array();

	$csql = "select * from cf_auto_invest_config where idx='$s_type'";
	$cres = sql_query($csql);
	$crow = sql_fetch_array($cres);
	$RES['category'] = $crow['category'];
	$RES['grp_title'] = $crow['grp_title'];

	$sql1 = "
		select
			a.*,
			b.mb_point, b.member_investor_type, b.mb_name
		from
			cf_auto_invest_config_user a
		LEFT join
			g5_member b  on(a.member_idx = b.mb_no)
		where
			ai_grp_idx='$s_type'
		order by
			a.idx";
	$res1 = sql_query($sql1);
	$cnt1 = sql_num_rows($res1);

	for ($i=0 ; $i<$cnt1 ; $i++) {

		$row1 = sql_fetch_array($res1);

		$RES["LIST"][$i]["mb_point"] = $row1['mb_point'];

		$setup_amount_total += $row1['setup_amount'];
		$mb_point_total += $row1['mb_point'];

		// 자동투자 설정 금액과 예치금 잔액 비교
		if ($row1['setup_amount'] <= $row1['mb_point']) $real_money = $row1['setup_amount'];
		else $real_money = $row1['mb_point'];

		// 이미 투자된 금액 추출해서 비교
		$ing_sql = "
			SELECT
				sum(a.amount) ing_amt,
				case when b.category='2' then 'b' ELSE 'c' end big_cat
			FROM
				cf_product_invest a
			LEFT JOIN
				cf_product b  ON(a.product_idx = b.idx)
			WHERE
				a.member_idx='$row1[member_idx]'
				AND a.invest_state='Y'
				AND b.state='1'
			GROUP BY
				case when b.category='2' then 'b' ELSE 'c' end";
		$ing_res = sql_query($ing_sql);
		$ing_cnt = sql_num_rows($ing_res);

		//if ($row1['member_idx']=="5713") echo "$ing_sql<br/>";

		$t_ing_money = 0;  // 총 투자중인 금액
		$b_ing_money = 0;  // 부동산 투자중인 금액
		$c_ing_money = 0;  // 그외 투자중인 금액

		for ($j=0 ; $j<$ing_cnt ; $j++) {

			$ing_row = sql_fetch_array($ing_res);
			if ($ing_row['big_cat']=="b") {
				$b_ing_money = $ing_row['ing_amt'];
			} else {
				$c_ing_money = $ing_row['ing_amt'];
			}
			$t_ing_money += $ing_row['ing_amt'];
		}

		$max_b = get_max_inv($row1["member_investor_type"],"b");
		$max_c = get_max_inv($row1["member_investor_type"],"c");
		$max_t = get_max_inv($row1["member_investor_type"],"t");

		if ($row1["member_investor_type"]=="2") {
			$passb_b = $max_t - $t_ing_money;
			$passb_c = $max_t - $t_ing_money;
			$passb_t = $max_t - $t_ing_money;
		} else if ($row1["member_investor_type"]=="3") {  // 전문 투자자
			$passb_b = 99999999;
			$passb_c = 99999999;
			$passb_t = 99999999;
		} else {  // 일반
			$passb_b = $max_b - $b_ing_money;
			$passb_c = $max_c - $c_ing_money;
			$passb_t = $max_t - $t_ing_money;
		}


		if ($crow['category']=="2") {
			if ($real_money>$passb_b) $real_money = $passb_b;
		} else {

			if ($real_money>$passb_c) $real_money = $passb_c;
		}

		if ($real_money<$row1['setup_amount']) $real_money=0;


		$total_setup += $row1['setup_amount'];
		$total_point += $row1['mb_point'];
		$total_amount += $real_money;

		if ($ret_list=="Y") {


			$RES["LIST"][$i]["mem_idx"] = $row1["member_idx"];
			$RES["LIST"][$i]["mb_name"] = $row1["mb_name"];
			$RES["LIST"][$i]["inv_type"] = $row1["member_investor_type"];

			if ($row1["member_investor_type"]=="2") $inv_type_name = "소득적격";
			else if ($row1["member_investor_type"]=="3") $inv_type_name = "전문투자자";
			else $inv_type_name = "개인";

			$RES["LIST"][$i]["inv_type_name"] = $inv_type_name;

			$RES["LIST"][$i]["setup_amount"] = $row1["setup_amount"];

			$RES["LIST"][$i]["real_money"] = $real_money;

			$RES["LIST"][$i]["b_ed_money"] = $b_ing_money;
			$RES["LIST"][$i]["b_ps_money"] = $passb_b;
			$RES["LIST"][$i]["b_mx_money"] = $max_b;

			$RES["LIST"][$i]["c_ed_money"] = $c_ing_money;
			$RES["LIST"][$i]["c_ps_money"] = $passb_c;
			$RES["LIST"][$i]["c_mx_money"] = $max_c;

			$RES["LIST"][$i]["t_ed_money"] = $t_ing_money;
			$RES["LIST"][$i]["t_ps_money"] = $passb_t;
			$RES["LIST"][$i]["t_mx_money"] = $max_t;

			if ($crow['category']=="2") {
				$RES["LIST"][$i]["n_ed_money"] = $RES["LIST"][$i]["b_ed_money"];
				$RES["LIST"][$i]["n_ps_money"] = $RES["LIST"][$i]["b_ps_money"];
				$RES["LIST"][$i]["n_mx_money"] = $RES["LIST"][$i]["b_mx_money"];
			} else {
				$RES["LIST"][$i]["n_ed_money"] = $RES["LIST"][$i]["c_ed_money"];
				$RES["LIST"][$i]["n_ps_money"] = $RES["LIST"][$i]["c_ps_money"];
				$RES["LIST"][$i]["n_mx_money"] = $RES["LIST"][$i]["c_mx_money"];
			}
			$n_ed_money += $RES["LIST"][$i]["n_ed_money"];
		}
	}

	$RES["setup_amount_total"] = $setup_amount_total;
	$RES["mb_point_total"] = $mb_point_total;
	$RES["n_ed_money"] = $n_ed_money;
	$RES["total_amount"] = $total_amount;

	//echo "$total_setup\n$total_point\n$total_amount";
	return $RES;

}
?>