<?
###############################################################################
## https://www.hellofunding.co.kr/external/api/autoInveCheckHello.do
## 20. 헬로펀딩_자동투자조회
###############################################################################
include_once("../syndication_config.php");
include_once($syndi_base_path . "/inc_jsonPrint_head.php");

/*
$REQUEST['ci']
$REQUEST['comp_cd']
*/

/*
[카테고리]
부동산-주택담보 : mortgage
부동산-건축자금 : pf
매출채권-면세점 : dutyfree
매출채권-소상공인 : smalltrade
동산 : movable
*/

$ARR = array("code"=>"9999", "msg"=>"자동투자서비스가 종료되었습니다."); echo printJson($ARR); exit;


$REQUEST['ci'] = urldecode($REQUEST['ci']);
$mb_id = memberCheck($REQUEST['ci']);
if(!$mb_id) { $ARR = array("code"=>'9999', "msg"=>"가입자가 없습니다."); echo printJson($ARR); exit; }
if($REQUEST['comp_cd'] != $_CONF['comp_cd']) { $ARR = array('code'=>'9999', 'msg'=>'업체코드오류'); echo printJson($ARR); exit; }

$MB = get_member($mb_id);

$sql = "
	SELECT
		A.idx, B.category, B.grp_title, A.setup_amount, A.setup_amount2
	FROM
		cf_auto_invest_config_user A
	LEFT JOIN
		cf_auto_invest_config B  ON A.ai_grp_idx=B.idx
	WHERE 1
		AND A.member_idx='".$MB['mb_no']."'
	ORDER BY
		A.idx ASC";
//echo $sql;
$res = sql_query($sql);
if($res) {

	$ARR['code'] = "0000";
	$ARR['msg']  = "정상처리되었습니다.";

	$rows = sql_num_rows($res);
	$ARR['autoinve_yn'] = ($rows > 0) ? 'Y' : 'N';
	$ARR['autoInve_list'] = array();

	for($i=0; $i<$rows; $i++) {

		$row = sql_fetch_array($res);

		if($row['category']=='2') {
			$INP_ARR['prod_cate'] = ( preg_match("/주택담보/", $row['grp_title']) ) ? 'mortgage' : 'pf';
		}
		else if($row['category']=='3') {
			$INP_ARR['prod_cate'] = ( preg_match("/면세점/", $row['grp_title']) ) ? 'dutyfree' : 'smalltrade';
		}
		else {
			$INP_ARR['prod_cate'] = 'movable';
		}

		$INP_ARR['min_amt'] = (string)$row['setup_amount'];
		$INP_ARR['max_amt'] = (string)$row['setup_amount2'];

		array_push($ARR['autoInve_list'], $INP_ARR);

	}

}
else {

	$ARR = array("code"=>"9999", "msg"=>"DATABASE ERROR"); echo printJson($ARR); exit;

}





##############################
## 최종출력처리
##############################
include_once($syndi_base_path . "/inc_jsonPrint_tail.php");

?>