<?
include_once('./_common.php');

$list = array();

//회원
$sql = "
	SELECT
		a.mb_id, a.lo_ip, a.lo_location, a.lo_url, a.lo_device, a.lo_datetime,
		b.mb_nick, b.mb_name, b.mb_email, b.mb_homepage, b.mb_open, b.mb_point
	FROM
		{$g5['login_table']} a
	LEFT JOIN
		{$g5['member_table']} b
	ON
		a.mb_id=b.mb_id
	WHERE (1)
		AND a.mb_id<>'{$config['cf_admin']}'
		AND a.mb_id!=''
	ORDER BY
		a.lo_datetime DESC";
$res   = sql_query($sql);
$rows = sql_num_rows($res);
for($i=0; $i<$rows; $i++) {
	$row1 = sql_fetch_array($res);
	$row1['lo_url'] = get_text($row1['lo_url']);

	$row1['name'] = get_sideview($row1['mb_id'], cut_str($row1['mb_name'], $config['cf_cut_name']), $row1['mb_email'], $row1['mb_homepage']);

	array_push($list, $row1);

}


//비회원
$sql2 = "
	SELECT
		a.mb_id, a.lo_ip, a.lo_location, a.lo_url, a.lo_device, a.lo_datetime
	FROM
		{$g5['login_table']} a
	WHERE
		a.mb_id=''
	ORDER BY
		a.lo_datetime DESC";
$res2  = sql_query($sql2);
$rows2 = sql_num_rows($res2);

for($i=0; $i<$rows2; $i++) {
	$row2 = sql_fetch_array($res2);
	$row2['lo_url'] = get_text($row2['lo_url']);
	$row2['name'] = '<span style="color:#ccc">unknown</span>';

	array_push($list, $row2);

}

$list_count = count($list);

$DEVICE['PC']     = sql_fetch("SELECT COUNT(lo_device) AS cnt FROM {$g5['login_table']} WHERE lo_device = 'PC'");
$DEVICE['MOBILE'] = sql_fetch("SELECT COUNT(lo_device) AS cnt FROM {$g5['login_table']} WHERE lo_device = 'MOBILE'");
$DEVICE['TABLET'] = sql_fetch("SELECT COUNT(lo_device) AS cnt FROM {$g5['login_table']} WHERE lo_device = 'TABLET'");

for($i=0; $i<count($list); $i++) {
	switch($list[$i]['lo_device']) {
		case 'PC'     : $list[$i]['device_icon'] = '<img src="/images/flaticon/pc.png" title="PC">';     break;
		case 'MOBILE' : $list[$i]['device_icon'] = '<img src="/images/flaticon/mobile.png"title="MOBILE">'; break;
		case 'TABLET' : $list[$i]['device_icon'] = '<img src="/images/flaticon/tablet.png"title="TABLET">'; break;
		default       : $list[$i]['device_icon'] = ''; break;
	}
}
//print_rr($list, 'font-size:12px');

?>

				<div style="padding:0 0 4px 4px;">
					접속자 <strong style="color:red"><?=number_format($list_count)?></strong>명 &nbsp;&nbsp;&nbsp;<span style='color:#ccc'>|</span>&nbsp;&nbsp;&nbsp;
					회원 <strong style="color:red"><?=number_format($rows)?></strong>명 &nbsp;
					비회원 <strong style="color:red"><?=number_format($rows2)?></strong>명 &nbsp;&nbsp;&nbsp;<span style='color:#ccc'>|</span>&nbsp;&nbsp;&nbsp;
					PC <strong style="color:red"><?=number_format($DEVICE['PC']['cnt'])?></strong>명 &nbsp;
					모바일 <strong style="color:red"><?=number_format($DEVICE['MOBILE']['cnt'])?></strong>명 &nbsp;
					태블릿 <strong style="color:red"><?=number_format($DEVICE['TABLET']['cnt'])?></strong>명
				</div>
				<div style="width:49%;float:left;margin-right:4px;">
<?
if($list_count) {
?>
					<table id="current_connect_tbl" style="font-size:11px">
						<thead>
							<tr>
								<th scope="col" style="width:%">회원명</th>
								<th scope="col" style="width:%" colspan="2">접속지</th>
								<th scope="col" style="width:%">열람페이지</th>
								<th scope="col" style="width:%">접속</th>
							</tr>
						</thead>
						<tbody>
<?
	for($i=0,$j=1; $i<$list_count; $i++,$j++) {
		//$location = conv_content($list[$i]['lo_location'], 0);
		$location = $list[$i]['lo_location'];

		// 최고관리자에게만 허용. 이 조건문은 가능한 변경하지 마십시오.
		$display_location = ($list[$i]['lo_url'] && $is_admin == 'super') ? "<a href=\"".$list[$i]['lo_url']."\">".$location."</a>" : $location;

		$lo_datetime = time()-strtotime($list[$i]['lo_datetime']);
		if($lo_datetime >= 86400) $after_time = ceil($lo_datetime/60) . '일전';
		if($lo_datetime >= 3600)  $after_time = ceil($lo_datetime/60) . '시간전';
		if($lo_datetime >= 60)    $after_time = ceil($lo_datetime/60) . '분전';
		if($lo_datetime < 60)     $after_time = '1분미만';
		unset($lo_datetime);

		echo '
							<tr>
								<td style="text-align:center">'.$list[$i]['name'].'</td>
								<td style="text-align:center;padding:0">'.$list[$i]['device_icon'].'</td>
								<td>'.$list[$i]['lo_ip'].'</td>
								<td>'.$display_location.'</td>
								<td style="text-align:center">'.$after_time.'</td>
							</tr>
		';

		if($list_count>10 && $j==round($list_count/2)) {
			echo '
						</tbody>
					</table>
				</div>

				<div style="width:49%;float:left">
					<table id="current_connect_tbl" style="font-size:11px">
						<thead>
							<tr>
								<th scope="col" style="width:%">회원명</th>
								<th scope="col" style="width:%" colspan="2">접속지</th>
								<th scope="col" style="width:%">열람페이지</th>
								<th scope="col" style="width:%">접속</th>
							</tr>
						</thead>
						<tbody>
			';
		}


	}
?>
						</tbody>
					</table>
<?
}
else {
	echo '
					<table id="current_connect_tbl">
						<tbody>
							<tr>
								<td class=\"empty_table\">현재 접속자가 없습니다.</td>
							</tr>
						</tbody>
					</table>
	';
}
?>
				</div>