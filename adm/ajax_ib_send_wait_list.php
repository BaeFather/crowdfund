<?
################################################################################
## 신한인사이드뱅크 전송 대기 리스트
## product_calculate.php 에서 호출됨
################################################################################

include_once("_common.php");

if(!$is_admin) echo "ERROR:LOGIN-CHECK";

$sql = "
	SELECT
		DC_NB,
		(SELECT title FROM cf_product WHERE idx=DC_NB) AS title,
		turn, is_overdue,
		COUNT(PARTNER_CD) AS cnt,
		SUM(TR_AMT) AS sum_tr_amt,
		SUM(TR_AMT_P) AS sum_tr_amt_p,
		SUM(CTAX_AMT) AS sum_ctax_amt,
		SUM(FEE) AS sum_fee
	FROM
		IB_FB_P2P_REPAY_REQ_DETAIL
	WHERE 1=1
		AND SDATE=''
		AND REG_SEQ=''
	GROUP BY
		DC_NB, turn, is_overdue
	ORDER BY
		DC_NB, turn, is_overdue";
$res = sql_query($sql);
$rows = $res->num_rows;

echo "<table style='width:100%;font-size:12px;'>\n" .
		 "  <colgroup>\n" .
     "    <col width='4%'>\n" .
		 "    <col width='%'>\n" .
     "    <col width='18%'>\n" .
     "    <col width='12%'>\n" .
     "    <col width='25%'>\n" .
     "  </colgroup>\n" .
     "  <tr bgcolor='#F8F8EF' align='center' height='20'>\n" .
     "    <td><input type='checkbox' id='chkall'></td>\n" .
		 "    <td>대출상품</td>\n" .
     "    <td>원리금지급회차</td>\n" .
     "    <td>건수</td>\n" .
     "    <td>실지급액</td>\n" .
     "  </tr>\n";

$total_repay_count = 0;
$total_repay_amount = 0;

if($rows) {
	for($i=0; $i<$rows; $i++) {
		$row = sql_fetch_array($res);

		$turn = $row['turn'] . "회차";
		$turn.= ($row['is_overdue']=='Y') ? '(연체이자)' : '';
		$sum_amount = $row['sum_tr_amt'] + $row['sum_tr_amt_p'] + $row['sum_ctax_amt'] + $row['sum_fee'];

		$prdt_turn = $row['DC_NB']."&".$row['turn'];
		$prdt_turn.= ($row['is_overdue']=='Y') ? '&Y' : '';

		echo "  <tr>\n" .
				 "	  <td align='center'><input type='checkbox' name='PRDT_TURN[]' value='".$prdt_turn."'></td>\n" .
				 "    <td title='".$row['title']."'><div style='width:100%;height:20px;line-height:20px;overflow:hidden'>".$row['title']."</div></td>\n" .
				 "    <td align='center'>".$turn."</td>\n" .
				 "    <td align='right'>".number_format($row['cnt'])."건</td>\n" .
				 "    <td align='right'>".number_format($row['sum_tr_amt'])."원</td>\n" .
				 "  </tr>\n";

		$total_repay_count += $row['cnt'];
		$total_repay_amount += $row['sum_tr_amt'];

		unset($turn); unset($prdt_turn);
	}
}
else {
	echo "  <tr>\n" .
	     "    <td colspan='5' align='center'>전송 대기중인 데이터가 없습니다.</td>\n" .
	     "  </tr>\n";
}

echo "  <tr bgcolor='#FFDDDD' style='color:red'>\n" .
     "    <td colspan='2' align='center'>합계</td>\n" .
     "    <td align='center'>".$rows."개 회차</td>\n" .
     "    <td align='right'>".number_format($total_repay_count)."건</td>\n" .
     "    <td align='right'>".number_format($total_repay_amount)."원</td>\n" .
     "  </tr>\n" .
     "</table>\n";
?>

<script>
$(function() {
	$("#chkall").click(function() {
		$("input[name='PRDT_TURN[]']").prop('checked', this.checked);
	});
});
</script>

<?
sql_close();
exit;
?>