<h3>쿠폰발급 현황</h3>
<div class="type03 mb30 rguide">
	<table>
		<colgroup>
			<col style='width:30%'>
			<col style='width:70%'>
		</colgroup>
<?
if($clist_count) {
	for($i=0; $i<$clist_count; $i++) {
?>
		<tr style="height:20px;">
			<td style="background:#EFEFEF;color:#000">쿠폰구분</td>
			<td style="color:navy"><?=$CLIST[$i]['coupon_name']?></td>
		</tr>
		<tr style="height:20px;">
			<td style="background:#EFEFEF;color:#000">쿠폰번호</td>
			<td style="color:navy"><?=$CLIST[$i]["cnumber"]?></td>
		</tr>
		<tr style="height:20px;">
			<td style="background:#EFEFEF;color:#000">발급일</td>
			<td style="color:navy"><?=$CLIST[$i]["use_date"]?></td>
		</tr>
		<tr style="height:20px;">
			<td style="background:#EFEFEF;color:#000">유효기간</td>
			<td style="color:navy"><?=$CLIST[$i]['ava_sdate']?> ~ <?=$CLIST[$i]['ava_edate']?></td>
		</tr>
		<tr style="height:20px;">
			<td style="background:#EFEFEF;color:#000">대상이벤트</td>
			<td style="color:navy"><?=$CLIST[$i]['event_title']?></td>
		</tr>
<?
	}
}
else {
?>
		<tr style="height:20px;background:#E6EAF9;font-weight:bold;">
			<td colspan="2" style="color:navy">발급된 쿠폰이 없습니다.</td>
		</tr>
<?
}
?>
	</table>
</div>

<h3>추천 현황</h3>
<?
$kk = 0;
for($i=0; $i<count($RECRWD); $i++) {

	// 2020.01.02 마케팅요청  리스트가 있는 항목만 보이게
	if(count($RECRWD[$i]['LIST']) > 0) {

		//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') { print_rr($RECRWD[$i],'font-size:12px;line-height:15px;'); }

		$print_sum_reward_amount = ( in_array($RECRWD[$i]['recmdee_reward_type'], array('1','2')) ) ? number_format($RECRWD[$i]['sum_reward_amount']).'원' : '';

?>
<div style="padding:4px 8px;"><?=$RECRWD[$i]['event_title']?> : <?=$RECRWD[$i]['sdate']?> ~ <?=$RECRWD[$i]['edate']?></div>
<div class="mb30">
	<table class="tblX" style="border-top:2px solid #284893;">
		<colgroup>
			<col style="width:20%">
			<col style="width:40%">
			<col style="width:40%">
		</colgroup>
		<tr style="border-top:2px solid #284893;">
			<th style="background:#E6EAF9;color:navy;" rowspan="2"><b>합계</b></th>
			<th style="background:#E6EAF9;color:navy;"><b>건수</b></th>
			<th style="background:#E6EAF9;color:navy;text-align:center;"><?=count($RECRWD[$i]['LIST'])?>건</th>
		</tr>
		<tr>
			<th style="background:#E6EAF9;color:navy;"><b>보상금액</b></th>
			<th style="background:#E6EAF9;color:navy;text-align:center;"><!--<?=$print_sum_reward_amount?>--></th>
		</tr>
<?
	$list_count = count($RECRWD[$i]['LIST']);

	for($k=0,$num=$list_count; $k<$list_count; $k++,$num--) {

		$print_target_mb = ($RECRWD[$i]['LIST'][$k]['position']=='recdee') ? $RECRWD[$i]['LIST'][$k]['mb_id'] : substr($RECRWD[$i]['LIST'][$k]['mb_id'], 0, 2)."**********";
		$print_mb_date = substr($RECRWD[$i]['LIST'][$k]['mb_datetime'], 0, 10);
		if($print_mb_date == '0000-00-00') $print_mb_date = '';
		$print_appr_date = substr($RECRWD[$i]['LIST'][$k]['approved_datetime'], 0, 10);
		$print_paid_date = substr($RECRWD[$i]['LIST'][$k]['paid_datetime'], 0, 10);


		$_reward_type       = $RECRWD[$i]['LIST'][$k]['position'] . "_reward_type";
		$_reward_goods_name = $RECRWD[$i]['LIST'][$k]['position'] . '_reward_goods_name';
		$_reward_point      = $RECRWD[$i]['LIST'][$k]['position'] . "_reward_point";

		if($RECRWD[$i]['LIST'][$k]['approved']=='1') {

			if($RECRWD[$i][$_reward_type] == '3') {
				$print_goods_name   = ($RECRWD[$i][$_reward_goods_name]) ? $RECRWD[$i][$_reward_goods_name] : '상품권/쿠폰';
				$print_reward_point = "-";
			}
			else {
				$print_goods_name   = ($RECRWD[$i][$_reward_type] == '2') ? "포인트" : "예치금";
				$print_reward_point = number_format($RECRWD[$i][$_reward_point]) . '원';
			}

		}
		else {
			$print_goods_name = $print_reward_point = '';
		}

		$tr_bgcolor = (($k%2)==1) ? '#F2F2F2' : '';
		$fcolor = ($RECRWD[$i]['LIST'][$k]['member_idx']==$member['mb_no']) ? '#FF2222' : '';

?>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;" rowspan="6"><?=$num?></td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">아이디</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><span style="color:<?=$fcolor?>"><?=$print_target_mb?></span></td>
		</tr>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">가입일</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><?=$print_mb_date?></td>
		</tr>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">보상지급품</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><?=$print_goods_name?></td>
		</tr>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">보상금액</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><?=$print_reward_point?></td>
		</tr>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">보상확정일</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><?=$print_appr_date?></td>
		</tr>
		<tr>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;">지급일</td>
			<td style="background:<?=$tr_bgcolor?>;text-align:center;"><?=$print_paid_date?></td>
		</tr>
<?
	}
?>
	</table>
</div>
<?
		$kk++;
	}
}
?>

<p>&nbsp;</p>

<?
if($kk==0) {
?>
	<div class="type03 mb30">
		<table>
			<tr>
				<td style="text-align:center;">추천인 현황이 없습니다.</td>
			</tr>
		</table>
	</div>
<?
}

@sql_close();
exit;

?>