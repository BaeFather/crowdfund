<?

exit;

include_once("_common.php");

$res = sql_query("SELECT idx, title FROM cf_product ORDER BY idx");
while($row = sql_fetch_array($res)) {

	print_rr($row);

	if( preg_match("/\[제/", $row['title']) && preg_match("/호\]/", $row['title']) ) {

		$product_start_num = @str_f6($row['title'], "[제", "호]");

		$sql = "UPDATE cf_product SET start_num='".$product_start_num."' WHERE idx='".$row['idx']."'";
		sql_query($sql);

	}


}


?>
