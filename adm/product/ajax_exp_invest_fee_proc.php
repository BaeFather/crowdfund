<?
include_once("_common.php");

auth_check($auth[$sub_menu], 'w');

if ($is_admin!= 'super') exit;

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

if(!$exp_prd_idx)    { exit; }

$prd_idx    = $exp_prd_idx;
$invest_fee = $exp_invest_fee;
$MB_NO      = explode(",", $exp_mb_no);
$PRDT       = sql_fetch("SELECT invest_usefee FROM cf_product WHERE idx='".$prd_idx."'");

if($exp_mb_no) {

	//if(!$exp_invest_fee) { echo "NONE_FEE"; exit; }

	for($i=0; $i<count($MB_NO); $i++) {
		$sql = "SELECT idx, fee FROM cf_platform_fee WHERE member_idx='".$MB_NO[$i]."' AND product_idx='".$prd_idx."'";
		$row = sql_fetch($sql);
		if($row['idx']) {
			if($invest_fee > 0) {
				if($row['fee']<>$invest_fee) {
					$sqlx = "UPDATE cf_platform_fee SET fee='".$exp_invest_fee."' WHERE idx='".$row['idx']."'";
					//echo $sqlx."\n";
					sql_query($sqlx);
				}
			}
			else {
				$sqlx = "DELETE FROM cf_platform_fee WHERE idx='".$row['idx']."'";
				//echo $sqlx."\n";
				sql_query($sqlx);
			}
		}
		else {
			$sqlx = "INSERT INTO cf_platform_fee (member_idx, product_idx, fee, rdate ) VALUES ('".$MB_NO[$i]."', '".$prd_idx."', '".$invest_fee."', NOW())";
			//echo $sqlx."\n";
			sql_query($sqlx);
		}
	}

}

// 부분삭제
if($exp_drop && $exp_idx) {
	$sqlx = "DELETE FROM cf_platform_fee WHERE idx='".$exp_idx."'";
	sql_query($sqlx);
}


// 설정자 리스트 (출력용)
$sql = "
	SELECT
		A.idx, A.fee, A.rdate,
		B.member_type, B.mb_id, B.mb_name, B.mb_co_name
	FROM
		cf_platform_fee A
			LEFT JOIN g5_member B  ON A.member_idx=B.mb_no
	WHERE
		A.product_idx='$prd_idx'
	ORDER BY
		A.member_idx DESC";
$res = sql_query($sql);
$rows = $res->num_rows;
if($rows) {

	echo '<table border="1" style="margin-top:4px; font-size:12px">' . PHP_EOL .
	     '  <tr align="center" bgcolor="#F8F8EF">' . PHP_EOL .
	     '    <td>ID</td>' . PHP_EOL .
	     '    <td>성명/상호명</td>' . PHP_EOL .
	     '    <td>설정율</td>' . PHP_EOL .
	     '    <td>설정일</td>' . PHP_EOL .
	     '    <td></td>' . PHP_EOL .
	     '  </tr>' .  PHP_EOL;

	for($i=0; $i<$rows; $i++) {
		$ROW = sql_fetch_array($res);

		$print_name = ($ROW['member_type']=='2') ? $ROW['mb_co_name'] : $ROW['mb_name'];
		$print_fee = sprintf('%.2f', $ROW['fee']) . '%';
		$print_fee_month = sprintf('%.2f', ($ROW['fee']/12)) . '%';
		$print_rdate = substr($ROW['rdate'], 0, 10);

		echo '  <tr align="center">' .  PHP_EOL .
	       '    <td>'.$ROW['mb_id'].'</td>' .  PHP_EOL .
	       '    <td>'.$print_name.'</td>' .  PHP_EOL .
	       '    <td>'.$print_fee.' (월평균'.$print_fee_month.')</td>' .  PHP_EOL .
	       '    <td>'.$print_rdate.'</td>' .  PHP_EOL .
	       '    <td><button type="button" onClick="exp_delete('.$ROW['idx'].');" class="btn btn-sm btn-default" style="color:red">삭제</button></td>' . PHP_EOL .
	       '  </tr>' .  PHP_EOL;
	}

	echo '</table>';

}


exit;

?>