#!/usr/local/php/bin/php -c /etc/php.ini -q
<?

set_time_limit(0);

$path = "/home/crowdfund/public_html";

define("_GNUBOARD_", true);
define("G5_DISPLAY_SQL_ERROR", true);
define("G5_MYSQLI_USE", true);

include_once($path . "/data/dbconfig.php");
include_once($path . "/lib/common.lib.php");

$str = "https://map.naver.com/index.nhn?vrpanotype=3&query=7ZWY64Ko7IucIO2SjeyCsOuPmSDrr7jsgqzqsJXrs4Drj5nsm5DroZzsloTrk4Dtgaw%3D&searchCoord=&street=on&tab=1&lng=a733ce6852c84b38a4cbf0533b3ca220&vrpanopoi=off&mapMode=0&mpx=7e5e89173e69b9b158c67c1f599210255a823afbe371dbb606a5a9c2ac81f7b072b8af5429cd45927570ee176bbedcce&vrpanopan=-85.53&vrpanosky=on&vrpanolat=383c65739e3763722123dee5f0e7e7e4&lat=3ea5bc38089e5033f06658b3ed64c393&dlevel=12&enc=b64&menu=location&__fromRestorer=true&vrpanolng=a733ce6852c84b38a4cbf0533b3ca220&vrpanofov=120&vrpanoid=%2FbFjv6fM8qjUNBkd7dd6WA%3D%3D&vrpanotilt=34.89";

$connect_db = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD) or die('MySQL Connect Error!!!');
$select_db  = sql_select_db(G5_MYSQL_DB, $connect_db) or die('MySQL DB Error!!!');
sql_set_charset('utf8', $connect_db);

$tableA = "cf_product";
$tableB = "cf_product_container";

$sql = "
	SELECT
		idx, title,
		evaluate_score1, evaluate_score2, evaluate_score3, evaluate_score4,
		evaluate_star1, evaluate_star2, evaluate_star3, evaluate_star4,
		evaluate_grade1, evaluate_grade2, evaluate_grade3, evaluate_grade4,
		extend_1, extend_2, extend_3, extend_4, extend_5, extend_6, extend_7, extend_8, extend_9, extend_10,
		invest_summary, invest_summary_m, core_invest_point, product_summary, product_description,
		evidence, security_loan, special_info, screening, comment,
		judge, receiver, broker, commission_fee, right_display,
		right_set_date, right_pic, deposit_pic, field_pic
	FROM
		{$tableA}
	ORDER BY
		idx ASC
-- LIMIT 1
	";
$res = sql_query($sql, G5_DISPLAY_SQL_ERROR, $connect_db);

$j=1;
while($ROW = sql_fetch_array($res, G5_DISPLAY_SQL_ERROR, $connect_db)) {

	$ROW2 = sql_fetch("SELECT product_idx FROM {$tableB} WHERE product_idx='".$ROW['idx']."'", G5_DISPLAY_SQL_ERROR, $connect_db);
	if(!$ROW2['product_idx']) {

		$insert_sql = "
			INSERT INTO
				{$tableB}
			SET
				product_idx         = '".$ROW['idx']."',
				evaluate_score1     = '".$ROW['evaluate_score1']."',
				evaluate_score2     = '".$ROW['evaluate_score2']."',
				evaluate_score3     = '".$ROW['evaluate_score3']."',
				evaluate_score4     = '".$ROW['evaluate_score4']."',
				evaluate_star1      = '".$ROW['evaluate_star1']."',
				evaluate_star2      = '".$ROW['evaluate_star2']."',
				evaluate_star3      = '".$ROW['evaluate_star3']."',
				evaluate_star4      = '".$ROW['evaluate_star4']."',
				evaluate_grade1     = '".$ROW['evaluate_grade1']."',
				evaluate_grade2     = '".$ROW['evaluate_grade2']."',
				evaluate_grade3     = '".$ROW['evaluate_grade3']."',
				evaluate_grade4     = '".$ROW['evaluate_grade4']."',
				extend_1            = '".addSlashes($ROW['extend_1'])."',
				extend_2            = '".addSlashes($ROW['extend_2'])."',
				extend_3            = '".addSlashes($ROW['extend_3'])."',
				extend_4            = '".addSlashes($ROW['extend_4'])."',
				extend_5            = '".addSlashes($ROW['extend_5'])."',
				extend_6            = '".addSlashes($ROW['extend_6'])."',
				extend_7            = '".addSlashes($ROW['extend_7'])."',
				extend_8            = '".addSlashes($ROW['extend_8'])."',
				extend_9            = '".addSlashes($ROW['extend_9'])."',
				extend_10           = '".addSlashes($ROW['extend_10'])."',
				invest_summary      = '".addSlashes($ROW['invest_summary'])."',
				invest_summary_m    = '".addSlashes($ROW['invest_summary_m'])."',
				core_invest_point   = '".addSlashes($ROW['core_invest_point'])."',
				product_summary     = '".addSlashes($ROW['product_summary'])."',
				product_description = '".addSlashes($ROW['product_description'])."',
				evidence            = '".addSlashes($ROW['evidence'])."',
				security_loan       = '".addSlashes($ROW['security_loan'])."',
				special_info        = '".addSlashes($ROW['special_info'])."',
				screening           = '".addSlashes($ROW['screening'])."',
				comment             = '".addSlashes($ROW['comment'])."',
				judge               = '".addSlashes($ROW['judge'])."',
				receiver            = '".addSlashes($ROW['receiver'])."',
				broker              = '".addSlashes($ROW['broker'])."',
				commission_fee      = '".addSlashes($ROW['commission_fee'])."',
				right_display       = '".addSlashes($ROW['right_display'])."',
				right_set_date      = '".addSlashes($ROW['right_set_date'])."',
				right_pic           = '".addSlashes($ROW['right_pic'])."',
				deposit_pic         = '".addSlashes($ROW['deposit_pic'])."',
				field_pic           = '".addSlashes($ROW['field_pic'])."'";
		if( sql_query($insert_sql, G5_DISPLAY_SQL_ERROR, $connect_db) ) {
			echo $ROW['title']."\n";

			if(($j%100)==0) sleep(2);

		}

	}

	unset($ROW);

	$j++;

}

sql_free_result($res, G5_DISPLAY_SQL_ERROR, $connect_db);
sql_close($connect_db);

exit;

?>