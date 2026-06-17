<?
$root_dir = "/home/crowdfund/public_html";

include_once($root_dir.'/common.php');


$ymdhis = date("Y-m-d H:i:s");
$ymd    = date("Y-m-d");

$sql = "SELECT idx , recruit_amount, title
		  FROM cf_product 
		 WHERE display='Y'
		   AND start_datetime<='$ymdhis'
		   AND state=''
		   AND isTest<>'Y'
		   AND recruit_amount>0";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

//echo "$cnt 건 $sql\n";

$ing_prd = array();

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$prd_idx = $row["idx"];
	
	$inv_sql = "SELECT sum(amount) sum_amount FROM cf_product_invest WHERE product_idx='$row[idx]' AND invest_state='Y'";
	$inv_res = sql_query($inv_sql);
	$inv_row = sql_fetch_array($inv_res);

	$ing_prd["LIST"][$prd_idx]["product_idx"] = $row["idx"];
	$ing_prd["LIST"][$prd_idx]["title"] = $row["title"];
	$ing_prd["LIST"][$prd_idx]["recruit_amount"] = $row["recruit_amount"];
	$ing_prd["LIST"][$prd_idx]["ing_amount"] = $inv_row["sum_amount"];
	
	//echo $row['idx']."-".$inv_row['sum_amount'] . "\n";
	//echo "\n";
}

sql_close();

//print_r($ing_prd);
$json_data = json_encode($ing_prd , JSON_UNESCAPED_UNICODE);

$file = $root_dir."/data/ing_prd.txt";
file_put_contents($file , $json_data);
?>