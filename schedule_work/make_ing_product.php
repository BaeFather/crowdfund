#!/usr/local/php/bin/php -q
<?
###############################################################################
## * * * * * /usr/local/php/bin/php -q /home/crowdfund/schedule_work/make_ing_product.php
###############################################################################

define('_GNUBOARD_', true);
define('G5_DISPLAY_SQL_ERROR', false);
define('G5_MYSQLI_USE', true);

$path = '/home/crowdfund/public_html';
include_once($path . '/data/dbconfig.php');
include_once($path . '/lib/common.lib.php');

$json_path = $path . "/data/cache/mobile_ing_product_list.json";

//---------------------------------------------------------------------------
$link = sql_connect(G5_MYSQL_HOST, G5_MYSQL_USER, G5_MYSQL_PASSWORD, G5_MYSQL_DB);
sql_set_charset("UTF8", $link);
//---------------------------------------------------------------------------

$sql = "
	SELECT
		A.idx, A.category, A.category2, A.mortgage_guarantees, A.start_num, A.recruit_amount, A.invest_return, A.invest_period, A.invest_days, A.main_image
	FROM
		cf_product A
	WHERE 1
		AND A.state='' AND A.display='Y' AND A.isTest='' AND A.only_vip='' AND A.invest_end_date='' AND A.start_datetime < NOW() AND A.recruit_amount > 10000
	ORDER BY
		A.start_num, A.idx";
$res  = sql_query($sql, G5_DISPLAY_SQL_ERROR, $link);
$rows = $res->num_rows;

$ARR = array();
$ARR['COUNT'] = $rows;

for($i=0; $i<$rows; $i++) {

	if( $R = sql_fetch_array($res) ) {
		$ARR['LIST'][] = array(
			'idx'            => $R['idx'],
			'start_num'      => '제'.$R['start_num'].'호',
			'category'       => get_product_type($R['category'], $R['mortgage_guarantees']),
			'invest_return'  => floatRtrim($R['invest_return']).'%',
			'invest_period'  => ($R['invest_period'] >= 1 && $R['invest_days'] == 0) ? $R['invest_period'].'개월' : $R['invest_days'].'일',
			'recruit_amount' => price_cutting($R['recruit_amount']).'원',
			'detail_url'     => '/investment/investment.php?prd_idx='.$R['idx'],
			'main_image'     => '/data/product/'.$R['main_image']
		);

	}

}
$ARR['version'] = date('YmdHis');

sql_close($link);

$json_str = json_encode($ARR, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT);

$fp = fopen($json_path, 'w+');
fputs($fp, $json_str);
fclose($fp);


exit;

?>
