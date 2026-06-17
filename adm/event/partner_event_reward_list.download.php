<?
set_time_limit(0);

include_once("_common.php");

$file_name = "[헬로펀딩]" . $TARGET_EVENT['event_title'] . "_" . date('Ymd_Hi') . ".xls";
$file_name = iconv("utf-8", "euc-kr", $file_name);


header( "Content-type: application/vnd.ms-excel; charset=utf-8" );
header( "Content-Disposition: attachment; filename=$file_name" );
header( "Content-description: PHP5 Generated Data" );

?>
	<table id="dataList" border="1" style="font-size:10pt">
		<tr>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">NO</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">이벤트</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">회원번호</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">아이디</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">성명</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">연락처</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">참여일</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">PID</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">쿠폰번호</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">쿠폰발송일</th>
			<th colspan="8" style="background:#F8F8EF" class="text-center">2차 투자보상</th>
			<th rowspan="2" style="background:#F8F8EF" class="text-center">CI</th>
		</tr>
		<tr>
			<th style="background:#F8F8EF" class="text-center">누적투자수(건)</th>
			<th style="background:#F8F8EF" class="text-center">누적투자금(원)</th>
			<th style="background:#F8F8EF" class="text-center">보상품</th>
			<th style="background:#F8F8EF" class="text-center">보상확정일시</th>
			<th style="background:#F8F8EF" class="text-center">은행</th>
			<th style="background:#F8F8EF" class="text-center">계좌번호</th>
			<th style="background:#F8F8EF" class="text-center">예금주</th>
			<th style="background:#F8F8EF" class="text-center">주민번호</th>
		</tr>

<?
if($list_count) {
	for($i=0; $i<$list_count; $i++) {

		$mb_id = (in_array($LIST[$i]['mb_level'], array('1','2','3','4','5'))) ? $LIST[$i]['mb_id'] : '';

		$print_bank = $print_bank_acct = $print_bank_private_name = $tmp_register_num = $register_num = '';

		if($LIST[$i]['invalid']=='') {
			if( in_array($LIST[$i]['mb_level'], array('1','2','3','4','5')) ) {
				if($LIST[$i]['approved']=='1') {
					$print_bank              = $BANK[$LIST[$i]['bank_code']];
					$print_bank_acct         = $LIST[$i]['bank_acct'];
					$print_bank_private_name = $LIST[$i]['bank_private_name'];
					if($LIST[$i]['member_type'] == '2') {
						$tmp_register_num = preg_replace("/-/", "", $LIST[$i]['mb_co_reg_num']);
						$register_num = substr($tmp_register_num,0,3).'-'.substr($tmp_register_num,3,2).'-'.substr($tmp_register_num,-5);
					}
					else {
						$tmp_register_num = getJumin($LIST[$i]['member_idx']);
						$register_num = substr($tmp_register_num,0,6).'-'.substr($tmp_register_num,-7);
					}
				}
			}
			else {
				if($LIST[$i]['paid']=='1') {
					$print_bank              = $BANK[$LIST[$i]['bank_code']];
					$print_bank_acct         = $LIST[$i]['bank_acct'];
					$print_bank_private_name = $LIST[$i]['bank_private_name'];
				}
				else {
					$print_bank = $print_bank_acct = $print_bank_private_name = '';
				}
			}

		}


		$bgColor = (!in_array($LIST[$i]['mb_level'],array('1','2','3','4','5'))) ? '#FFDDDD' : '';
		$fcolor1 = ($LIST[$i]['invest_count'] > 0) ? '' : '#CCC';
		$fcolor2 = ($LIST[$i]['invest_amount'] > 0) ? '' : '#CCC';

		$reward_target = ($TARGET_EVENT['idx'] && $TARGET_EVENT['is_real'] && $LIST[$i]['iam_rwd_target'] && $LIST[$i]['invalid']=='' && $LIST[$i]['paid']=='') ? true : false;

		if($reward_target) $bgColor = '#FFFFCC';

		$reward_goods = '';
		if($EVENT[$LIST[$i]['event_no']]['invest_rwd_give']=='1' && $LIST[$i]['iam_rwd_target']) {
			if($EVENT[$LIST[$i]['event_no']]['invest_rwd_amt'] > 0 || $EVENT[$LIST[$i]['event_no']]['invest_rwd_point'] > 0) {
				$reward_goods.= ($EVENT[$LIST[$i]['event_no']]['invest_rwd_amt']) ? $EVENT[$LIST[$i]['event_no']]['invest_rwd_amt'] . '원' : $EVENT[$LIST[$i]['event_no']]['invest_rwd_point'] . 'P';
			}
		}

?>
			<tr align="center" style="background:<?=$bgColor?>">
				<td><?=$num?></td>
				<td><?=$TARGET_EVENT['event_title']?></td>
				<td><?=$LIST[$i]['member_idx']?></td>
				<td><?=$mb_id?></td>
				<td><?=$LIST[$i]['mb_title']?></td>
				<td style="mso-number-format:'@';"><?=$LIST[$i]['mb_hp']?></td>
				<td style="mso-number-format:'@';"><?=substr($LIST[$i]['rdatetime'],0,10)?></td>
				<td><?=$LIST[$i]['pid']?></td>
				<td style="mso-number-format:'@';"><span style="color:#FF2222"><?=$LIST[$i]['coupon_serial_no']?></span></td>
				<td style="mso-number-format:'@';"><?=substr($LIST[$i]['coupon_send_dt'], 0, 10)?></td>
				<td align="right" style="color:<?=$fcolor1?>"><?=number_format($LIST[$i]['invest_count'])?></td>
				<td align="right" style="color:<?=$fcolor2?>"><?=number_format($LIST[$i]['invest_amount'])?></td>
				<td><span style="color:#FF2222"><?=$reward_goods?></span></td>
				<td style="mso-number-format:'@';"><?=($LIST[$i]['invalid']=='1') ? "<span style='color:brown'>".substr($LIST[$i]['invalid_datetime'],0,16)."</span>" : substr($LIST[$i]['approved_datetime'],0,16); ?></td>
				<td><?=$print_bank?></td>
				<td style="mso-number-format:'@';"><?=$print_bank_acct?></td>
				<td><?=$print_bank_private_name?></td>
				<td><?=$register_num?></td>
				<td style="mso-number-format:'@';"><?=$LIST[$i]['mb_ci']?></td>
			</tr>
<?
		$num--;
	}
}
else {
	echo "<tr><td colspan='20' align='center'>데이터가 없습니다.</td></tr>\n";
}
?>
		</tbody>
	</table>

<?

sql_close();
exit;

?>