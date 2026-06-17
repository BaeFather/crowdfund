<?

include_once("_common.php");


$sql = "SELECT COUNT(idx) AS cnt FROM cf_apat_loan_request WHERE judge_state = '1' AND judge_name =''";
$new_request_count = sql_fetch($sql)['cnt'];
//$new_request_count = '1';

$ARR = array('new_request_count'=>$new_request_count);
echo json_encode($ARR);

sql_close();
exit;

?>