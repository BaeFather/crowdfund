<?
###############################################################################
## 서버점검등의 작업이 필요할 경우 점검 시작시간까지 남은 시간에 대한
## 표기가 필요한 경우 본 파일을 이용하면 된다.
## pc용 모바일용 head.php 에 호출 ajax 스크립트 있음.
###############################################################################

$limit_datetime = '2020-08-12 21:00';

$limit_timestamp = strtotime($limit_datetime);
$rest_timestamp = $limit_timestamp - time();
$rest_timestamp = max(array(0, $rest_timestamp));


$day    = floor(($rest_timestamp)/(60*60*24));
$hour   = floor(($rest_timestamp-($day*60*60*24))/(60*60));
$minute = floor(($rest_timestamp-($day*60*60*24)-($hour*60*60))/(60));
$second = $rest_timestamp - ($day*60*60*24) - ($hour*60*60) - ($minute*60);


$ARR['rest_timestamp'] = $rest_timestamp;

$ARR['rest_time'] = "";
if($day > 0)    $ARR['rest_time'].= $day."일 ";
if($hour > 0)   $ARR['rest_time'].= sprintf('%02d', $hour). ":";
if($minute > 0) $ARR['rest_time'].= sprintf('%02d', $minute). ":";
$ARR['rest_time'].= sprintf('%02d', $second). "초";


header('Content-Type: application/json');

echo json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

exit;

?>