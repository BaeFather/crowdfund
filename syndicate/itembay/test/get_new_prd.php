<?
header("Access-Control-Allow-Origin: *"); 
//header('Access-Control-Allow-Headers: Content-Type');
//header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
//header("Content-type:text/html;charset=UTF-8");

include "../lib/dbconfig.php";

$nowdate = date('Y-m-d H:i:s');
$new_prd_list = array();

$con = mysqli_connect(HF_MYSQL_HOST, HF_MYSQL_USER , HF_MYSQL_PASSWORD , HF_MYSQL_DB, 3307);
mysqli_query($con, "set names 'utf8'");

$sql = "select A.idx, A.title ,
			( SELECT SUM(amount) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS total_invest_amount
			from cf_product A
			where (A.state is NULL or A.state='')
			and (A.invest_end_date is NULL or A.invest_end_date='')
			and (A.start_datetime <= '$nowdate' and A.end_datetime >= '$nowdate') 
			and A.recruit_amount > ( SELECT SUM(amount) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) order by idx desc";

$res = mysqli_query($con,$sql);
$cnt = mysqli_num_rows($res);
$cnt = 1;

for ($i=0 ; $i<$cnt ; $i++) {
	$row = mysqli_fetch_array($res);
	$new_prd_list = array("title"=>$row['title']);
}	

mysqli_close($con);


echo json_encode($new_prd_list,  JSON_UNESCAPED_UNICODE );


?>