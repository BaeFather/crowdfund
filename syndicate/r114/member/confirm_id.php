<?php
include_once('./_common.php');

//$mb_id = mysqli_real_escape_string($g5['connect_db'], trim($_POST['prm1']));
$mb_id = TRIM($_POST['prm1']);

$query = "SELECT mb_no FROM g5_member WHERE mb_id = '".$mb_id."' AND mb_leave_date=''";

$row = sql_fetch($query);
echo ($row['mb_no']) ? 'x' : 'o';
exit;
?>