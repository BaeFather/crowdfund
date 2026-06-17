<?

$sub_menu = '600000';
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

sleep(1);

//print_r($_POST); exit;

foreach($_POST as $k=>$v) { ${$_POST[$k]} = trim($v); }

$g5['title'] = '자동투자그룹 처리';
if($member['mb_level'] == '9') include_once(G5_ADMIN_PATH."/inc_sub_admin_access_check.php");		// 부관리자 접속로그 등록


$grp_title = sql_real_escape_string($grp_title);
if($auto_inv_limit_per > 0) $auto_inv_limit_per = sprintf('%.2f', $auto_inv_limit_per);
$mb11_limit_amt = preg_replace('/,/', '', $mb11_limit_amt);
$mb12_limit_amt = preg_replace('/,/', '', $mb12_limit_amt);
$mb13_limit_amt = preg_replace('/,/', '', $mb13_limit_amt);
$mb2_limit_amt  = preg_replace('/,/', '', $mb2_limit_amt);

$min_profit = sprintf('%.2f', $min_profit);
$max_profit = sprintf('%.2f', $max_profit);

if($action=="insert") {

	$sql = "
		INSERT INTO
			cf_auto_invest_config
		SET
			category           = '$category',
			grp_title          = '$grp_title',
			min_period_days    = '$min_period_days',
			max_period_days    = '$max_period_days',
			min_profit         = '$min_profit',
			max_profit         = '$max_profit',
			auto_inv_unlimited = '$auto_inv_unlimited',
			auto_inv_limit_per = '$auto_inv_limit_per',
			mb11_unlimited     = '$mb11_unlimited',
			mb11_limit_amt     = '$mb11_limit_amt',
			mb12_unlimited     = '$mb12_unlimited',
			mb12_limit_amt     = '$mb12_limit_amt',
			mb13_unlimited     = '$mb13_unlimited',
			mb13_limit_amt     = '$mb13_limit_amt',
			mb2_unlimited      = '$mb2_unlimited',
			mb2_limit_amt      = '$mb2_limit_amt',
			inv_order          = '$inv_order',
			summary            = '$summary',
			summary_m          = '$summary_m',
			display            = '$display',
			rdate              = NOW()";
	if(sql_query($sql)) {
		echo "신규 채권그룹이 등록되었습니다.";
	}

}
else if($action=="update") {

	$sql = "
		UPDATE
			cf_auto_invest_config
		SET
			category           = '$category',
			grp_title          = '$grp_title',
			min_period_days    = '$min_period_days',
			max_period_days    = '$max_period_days',
			min_profit         = '$min_profit',
			max_profit         = '$max_profit',
			auto_inv_unlimited = '$auto_inv_unlimited',
			auto_inv_limit_per = '$auto_inv_limit_per',
			mb11_unlimited     = '$mb11_unlimited',
			mb11_limit_amt     = '$mb11_limit_amt',
			mb12_unlimited     = '$mb12_unlimited',
			mb12_limit_amt     = '$mb12_limit_amt',
			mb13_unlimited     = '$mb13_unlimited',
			mb13_limit_amt     = '$mb13_limit_amt',
			mb2_unlimited      = '$mb2_unlimited',
			mb2_limit_amt      = '$mb2_limit_amt',
			inv_order          = '$inv_order',
			summary            = '$summary',
			summary_m          = '$summary_m',
			display            = '$display',
			edate              = NOW()
		WHERE
			idx = '$idx'";

	echo $sql;

	if(sql_query($sql)) {
		echo "채권그룹 등록정보가 수정되었습니다.";
	}

}

?>