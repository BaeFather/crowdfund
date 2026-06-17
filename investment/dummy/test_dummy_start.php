<?php

include_once('_common.php');


$product_idx = 1000;

debug_flush("<div style='width:800px;height:500px;border:1px solid #000;overflow:scroll;font-size:12px'>\n");

$x = true;
while($x > 0) {

	$member_idx = rand(3000,4000);
	$uniq_id = strtoupper(uniqid());

	// invest.sh 상품번호 회원번호로 더미프로세스 실행
	$exec_str = G5_PATH . "/investment/dummy/invest.sh $product_idx $member_idx $uniq_id > /dev/null 2>/dev/null &";		// 비동기식 실행 "/dev/null 2>/dev/null &"
	shell_exec($exec_str);

	// process_counter.sh를 실행하여 실행중인 더미프로세스 수를 카운팅 한다.
	$exec_str2 = G5_PATH . "/investment/dummy/process_counter.sh &";
	$process_count = shell_exec($exec_str2);
	$process_count = trim($process_count);

	debug_flush($exec_str . " (".$process_count.")<br/>\n");

	usleep(rand(0,1000000));

	if(rand(1,10000)==1) break;

}

debug_flush("</div>\n");

exit;

?>