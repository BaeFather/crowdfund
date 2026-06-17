<?php
include_once('./_common.php');

// JSON 파일 조회
$list = array();
$DEVICE = array();
$currentConnectList = @json_decode(file_get_contents(G5_DATA_PATH.DIRECTORY_SEPARATOR."current_connect".DIRECTORY_SEPARATOR."currentConnect.json"), true);
if(isset($currentConnectList["list"]) && count($currentConnectList["list"])){
    $list = $currentConnectList["list"];
}

if(isset($currentConnectList["device"]) && count($currentConnectList["device"])){
    $DEVICE = $currentConnectList["device"];
}

$list_count = count($list);
?>
				<div style="padding:0 0 4px 4px;">
					접속자 <strong style="color:red"><?=number_format($list_count)?></strong>명 &nbsp;&nbsp;&nbsp;<span style='color:#ccc'>|</span>&nbsp;&nbsp;&nbsp;
					회원 <strong style="color:red"><?=number_format($currentConnectList["member"])?></strong>명 &nbsp;
					비회원 <strong style="color:red"><?=number_format($currentConnectList["no_member"])?></strong>명 &nbsp;&nbsp;&nbsp;<span style='color:#ccc'>|</span>&nbsp;&nbsp;&nbsp;
					PC <strong style="color:red"><?=number_format($DEVICE['PC']['cnt'])?></strong>명 &nbsp;
					모바일 <strong style="color:red"><?=number_format($DEVICE['MOBILE']['cnt'])?></strong>명 &nbsp;
					태블릿 <strong style="color:red"><?=number_format($DEVICE['TABLET']['cnt'])?></strong>명
				</div>
				<div style="width:49%;float:left;margin-right:4px;">
<?
if($list_count) {
?>
					<table id="current_connect_tbl" style="font-size:11px">
						<colgroup>
							<col style="width:25%">
							<col style="width:8%">
							<col style="width:22%">
							<col style="width:%">
							<col style="width:10%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col">회원명</th>
								<th scope="col" colspan="2">접속지</th>
								<th scope="col">열람페이지</th>
								<th scope="col">접속</th>
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
								<td style="text-align:center"><div style="width:100%;height:20px;line-height:20px;overflow-y:hidden">'.$list[$i]['name'].'</div></td>
								<td style="text-align:center;padding:0">'.$list[$i]['device_icon'].'</td>
								<td>'.$list[$i]['lo_ip'].'</td>
								<td><div style="width:100%;height:20px;line-height:20px;overflow-y:hidden">'.$display_location.'</div></td>
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
						<colgroup>
							<col style="width:25%">
							<col style="width:8%">
							<col style="width:22%">
							<col style="width:%">
							<col style="width:10%">
						</colgroup>
						<thead>
							<tr>
								<th scope="col" style="width:%">회원명</th>
								<th scope="col" style="width:%" colspan="2">접속지</th>
								<th scope="col" style="width:%">열람페이지</th>
								<th scope="col" style="width:10%">접속</th>
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