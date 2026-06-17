<?php
include_once('./_common.php');

if(!$is_admin) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

$g5['title'] = '현재접속자';
include_once('./_head.php');

$list = array();

//회원
$sql = "
	SELECT
		a.mb_id, a.lo_ip, a.lo_location, a.lo_url, a.lo_device,
		b.mb_nick, b.mb_name, b.mb_email, b.mb_homepage, b.mb_open, b.mb_point
	FROM
		{$g5['login_table']} a
	LEFT JOIN
		{$g5['member_table']} b
	ON
		a.mb_id=b.mb_id
	WHERE (1)
		AND a.mb_id!=''
		AND a.mb_id<>'{$config['cf_admin']}'
	ORDER BY
		a.lo_datetime DESC";
//echo $sql; exit;

$res   = sql_query($sql);
$rows1 = sql_num_rows($res);
for($i=0; $i<$rows1; $i++) {
	$row = sql_fetch_array($res);
	$row['lo_url'] = get_text($row['lo_url']);

	$list[$i] = $row;
	$list[$i]['name'] = get_sideview($row['mb_id'], cut_str($row['mb_name'], $config['cf_cut_name']), $row['mb_email'], $row['mb_homepage']);
}

$list_count = $rows1;


//비회원
$sql2 = "
	SELECT
		a.mb_id, a.lo_ip, a.lo_location, a.lo_url, a.lo_device
	FROM
		{$g5['login_table']} a
	WHERE
		a.mb_id=''
	ORDER BY
		a.lo_datetime DESC";
$res2  = sql_query($sql2);
$rows2 = sql_num_rows($res2);

for($i=0; $i<$rows2; $i++) {
	$row = sql_fetch_array($res2);
	$row['lo_url'] = get_text($row['lo_url']);
	$row['name'] = '<span style="color:#ccc">unknown</span>';

	array_push($list, $row);
}

for($i=0; $i<count($list); $i++) {
	switch($list[$i]['lo_device']) {
		case 'PC'     : $list[$i]['device_icon'] = '<img src="/images/flaticon/pc.png" title="PC">';        break;
		case 'MOBILE' : $list[$i]['device_icon'] = '<img src="/images/flaticon/mobile.png"title="MOBILE">'; break;
		case 'TABLET' : $list[$i]['device_icon'] = '<img src="/images/flaticon/tablet.png"title="TABLET">'; break;
		default       : $list[$i]['device_icon'] = ''; break;
	}
}
//print_rr($list, 'font-size:12px');

include_once($connect_skin_path.'/current_connect.skin.php');


include_once('./_tail.php');

//echo $connect_skin_path;
?>