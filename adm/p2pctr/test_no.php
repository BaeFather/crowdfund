<?
include_once('./_common.php');
?>
<?
include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');
?>
<?
echo "proxy=> ".$use_proxy."<br/><br/>";

echo "<br/><br/>".$_SERVER['REMOTE_ADDR'];
echo "<br/><br/>".$_SERVER['HTTP_X_FORWARDED_FOR'];
echo "<br/><br/>";
 $gubun="IVCT";
 /*
	$sql = "INSERT INTO p2pctr_order_no2_test(ymd, gubun, odno)
			SELECT ymd, gubun, max(odno)+1 FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
	$res = sql_query($sql);
	$idx = sql_insert_id();

echo $idx."<br/>---<br/>";
	$sql2 = "SELECT * FROM p2pctr_order_no2_test WHERE idx='$idx'";
echo $sql2."<br/>";	
	$row2 = sql_fetch($sql2);
var_dump($row2);	
	$ret = $row2["odno"];
	
	echo "<br/>".$row2["odno"];
*/	
?>
<br/> ============================================================================= <br/>
<?

$se1_sql =  "SELECT ymd, gubun, max(odno)+1 nw_odno FROM p2pctr_order_no2_test WHERE ymd='".date("Ymd")."' AND gubun='$gubun'";
$se1_res = sql_query($se1_sql);
$se1_row = sql_fetch_array($se1_res);

$se_odno = $se1_row["nw_odno"];

$se2_sql = "insert into p2pctr_order_no2_test set ymd='".date("Ymd")."' , gubun='$gubun' , odno='$se_odno'";
$res = sql_query($se2_sql);
	$idx = sql_insert_id();


echo $idx."<br/>---<br/>";
	$sql2 = "SELECT * FROM p2pctr_order_no2_test WHERE idx='$idx'";
echo $sql2."<br/>";	

	//$row2 = sql_fetch($sql2);
	$res2 = sql_query($sql2);
	$row2 = sql_fetch_array($res2);
	
	
var_dump($row2);	
	$ret = $row2["odno"];
	
	echo "<br/>".$row2["odno"];
?>

