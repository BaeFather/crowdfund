<?php

include_once('./_common.php');
include_once(G5_PATH.'/lib/vacs.lib.php');


// 가상계좌 생성
$result = vans_edit('ready', '003');

// 가상계좌 입금등록 (예치금 충전)
//$result = vans_edit('1', '003', '48004811197894', '5000');

print_r($result);
