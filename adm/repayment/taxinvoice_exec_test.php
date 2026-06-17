#!/usr/local/php/bin/php -q
<?

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";
include_once($base_path . "/common.php");

$loop_count = "100";

$str = trim(@$_SERVER['argv']['1']);

for($i=0; $i<$loop_count; $i++) {

	$sql = "
		INSERT INTO
			test
		SET
			test_str='".$str."',
			rdatetime=NOW()";
	sql_query($sql);

	usleep(100000);

}

echo "Finish\n";

sql_close();
exit;

?>