<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/spotImg.do
## 14. 현장실사
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

if(!$REQUEST['prod_cd']) { $ARR = array("code"=>'9999', "msg"=>"상품코드오류"); echo printJson($ARR); exit; }

$PRDT = sql_fetch("
	SELECT
		idx, open_date, main_image, detail_image, stream_url1
	FROM
		cf_product
	WHERE
		idx='".$REQUEST['prod_cd']."'");
if(!$PRDT['idx']) { $ARR = array("code"=>'9999', "msg"=>"미존재상품"); echo printJson($ARR); exit; }

$ARR["code"] = "0000";
$ARR['msg'] = "정상처리되었습니다.";
$ARR['data']['comp_cd'] = $_CONF['comp_cd'];
$ARR['data']['prod_cd'] = $REQUEST['prod_cd'];
$ARR['data']['cur_process_rate'] = '';
$ARR['data']['spot_list'] = array();

$TMPARR['reg_dt'] = preg_replace("/-/", "", $PRDT['open_date']);
$TMPARR['title']  = ($PRDT['stream_url1'] && $PRDT['stream_url1']!='ready') ? '실시간 현장카메라' : '';
$TMPARR['note']   = '';
$TMPARR['url_list'] = array();

/*
if($PRDT['main_image']) {
	if(file_exists(G5_DATA_PATH . "/product/".$PRDT['main_image'])) {
		$target_str	 = preg_replace("/\//", "\/", G5_DATA_PATH);
		$main_image_url = G5_DATA_URL."/product/".$PRDT['main_image'];

		array_push($TMPARR['url_list'], array("img_url"=>$main_image_url));
	}
}
*/

if($PRDT['stream_url1'] && $PRDT['stream_url1']!='ready') {
	$stream_url = "http://hellolivetv.co.kr/onair/".$PRDT['idx'];
	array_push($TMPARR['url_list'], array("img_url"=>$stream_url));
}

array_push($ARR['data']['spot_list'], $TMPARR);

##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>