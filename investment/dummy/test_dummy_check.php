<?php

include_once("_common.php");

$product_idx = 1000;
$member_idx  = 817;
$uniq_id = '';

$limit_read_time = 6;
$limit_process_count = 40;
$process_count_exec = G5_PATH . "/investment/dummy/process_counter.sh $product_idx";


$check_process_count = "";
$loop_count = 0;

$x = true;
$begin_time_x = get_microtime();
while($x > 0) {

	$lead_time = get_microtime() - $begin_time_x;

	if( $lead_time < $limit_read_time ) {

		$process_count = shell_exec($process_count_exec);

		$check_process_count = $process_count;

		if($process_count < $limit_process_count) {

			$uniq_id = strtoupper(uniqid());
			$my_process_exec = G5_PATH . "/investment/dummy/invest.sh $product_idx $member_idx $uniq_id > /dev/null 2>/dev/null &";		// 비동기식 실행 "/dev/null 2>/dev/null &"
			shell_exec($my_process_exec);

			echo "wait_time : " . sprintf("%.4f", $lead_time) . "<br/>\n";

			break;

		}
		else {
			usleep(1);
		}

	}
	else {

		$check_process_count = $process_count;
		echo "<font color=red>wait_time : " . sprintf("%.4f", $lead_time) . "</font><br/>\n";
		break;

	}

	$loop_count++;

}

echo "process : " . $check_process_count . "<br/>\n";
echo "loop_count : " . number_format($loop_count) . "회<br/>\n";

$my_process_id = shell_exec(G5_PATH . "/investment/dummy/process_id_check.sh $product_idx $member_idx $uniq_id");
if($my_process_id) {

	sleep(3);

	echo "my_process_id : " . $my_process_id . "<br/>\n";
	echo "kill $my_process_id : ";

	$kill_exec = "kill " . $my_process_id;
	exec($kill_exec);
}


sql_close();
exit;

?>