<?
/*************************************************
** 운영팀이 신한은행 발신 로그를 볼수 없어 자주 개발팀을 찾아오니 **
** 내 이를 어여삐 여겨 새로 로그 리스트를 맹가노니           **
** 유용하게 사용하도록 하라 .                          **
**                             2021-06-14 전승찬  **
**********************************************  */
$sub_menu = "";
include_once('./_common.php');


if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

//$g5['title'] = $menu['menu600'][1][1];
$g5['title'] = "신한은행 발신 전문 리스트";

include_once('../admin.head.php');
?>
<?
$sql_total = "SELECT count(*) cnt FROM IB_request_log WHERE mb_id like 'admin_%'";
$row_total = sql_fetch($sql_total);
$total_count = $row_total['cnt'];	

$rows = 30;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows; // 시작 열을 구함

$num = $total_count - $from_record;

$sql = "SELECT * FROM IB_request_log WHERE mb_id like 'admin_%' ORDER BY regdate DESC LIMIT $from_record, $rows";
$result = sql_query($sql);

echo $total_count;

?>
<div style="width:100%">
	<div class="panel-body">
		<div class="dataTable_wrapper">

			<!-- 검색영역 START -->
			<div style="line-height:28px;">
			</div>
			<!-- 검색영역 E N D -->

			<div class="dataTable_wrapper">
				<table id="dataList" class="table table-striped table-bordered table-hover" style="font-size:12px;">
					<thead style="font-size:13px">
						<tr>
							<th class="text-center" style="background:#F8F8EF">NO.</th>
							<th class="text-center" style="background:#F8F8EF">전문번호</th>
							<th class="text-center" style="background:#F8F8EF">진행상태</th>
							<th class="text-center" style="background:#F8F8EF">상품명</th>
							<th class="text-center" style="background:#F8F8EF">목표금액</th>
							<th class="text-center" style="background:#F8F8EF;padding:0;">

							</th>
							<th class="text-center"  style="padding:0;background:#F8F8EF">

							</th>
							<th class="text-center" style="background:#F8F8EF">마감일</th>
							<th class="text-center" style="background:#F8F8EF">투자자수</th>
							<th class="text-center" style="background:#F8F8EF">투자금액</th>
							<th class="text-center" style="background:#F8F8EF">기표</th>
							<th class="text-center" style="background:#F8F8EF">상환용<br>가상계좌</th>
							<th class="text-center" style="background:#F8F8EF">등록일</th>
							<th class="text-center" style="background:#F8F8EF">PROC</th>
						</tr>
					</thead>
					<tbody>
<?
for ($i = 0 ; $i<$rows ; $i++) {

	$row = sql_fetch_array($result);

	$request_arr = explode("&",$row["request_arr"]);
	$tmp = explode("=",$request_arr[0]);
	$jmNum = $tmp[1];
	?>
						<tr class="odd" style="background:<?=$bgcolor?>">
							<td align="center"><?=$num?></td>
							<td align="center"><?=$jmNum?></td>
							<td align="center">
								<?=$pstate?>
								<? if ( ($LIST[$i]["state"]=="2" or $LIST[$i]["state"]=="5") and $LIST[$i]['loan_end_date']==date("Y-m-d")) { ?>
									<a class="btn btn-sm btn-primary" style="margin-top:4px;" onclick="go_end_sms('<?=$LIST[$i][idx]?>');" <?=$sms_btn_dis?> >상환문자</a>
								<? } ?>
							</td>
							<td align="left">
								<? if($LIST[$i]['ib_trust']=='Y') { ?><span class="flag p01">예치금신탁</span><? } ?>
								<? if($LIST[$i]['ai_grp_idx']!='') { ?><span class="flag p02">자동투자</span><? } ?>
								<? if($LIST[$i]['purchase_guarantees']=='Y') { ?><span class="flag p03">채권매입보증</span><? } ?>
								<? if($LIST[$i]['advanced_payment']=='Y') { ?><span class="flag p04">이자선지급</span><? } ?>
								<? if($LIST[$i]['success_example']=='Y') { ?><span class="flag p05">투자성공사례</span><? } ?>
								<? if($LIST[$i]['popular_goods']=='Y') { ?><span class="flag p06">인기상품</span><? } ?>
								<? if($LIST[$i]['advance_invest']=='Y') { ?><span class="flag p07">사전투자</span><? } ?>
								<? if($LIST[$i]['portfolio']=='Y') { ?><span class="flag p08">포트폴리오</span><? } ?>
								<? if($LIST[$i]['isConsor']=='1') { ?><span class="flag p09">컨소시엄</span><? } ?>
								<? if($LIST[$i]['only_vip']=='1') { ?><span class="flag p10">법인전용</span><? } ?>
								<div><?=$LIST[$i]['title']?></div>
							</td>
							<td align="right">
								<?=price_cutting($LIST[$i]['recruit_amount'])?>원
							</td>
							<td align="center" style="padding:0">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0"><?=preg_replace("/-/", ".", $LIST[$i]['recruit_period_start'])?> ~ <?=preg_replace("/-/", ".", $LIST[$i]['recruit_period_end'])?></li>
									<li style="width:100%;padding:5px;"><a title="<?=$loan_start_datetime?>" style="color:#333333;"><?=($LIST[$i]['loan_start_date'] && $LIST[$i]['loan_start_date']!='0000-00-00') ? preg_replace("/-/", ".", $LIST[$i]['loan_start_date']).'</a> ~ '.preg_replace("/-/", ".", $LIST[$i]['loan_end_date']) : ''?></li>
								</ul>
							</td>
							<td align="center" style="padding:0">
								<ul class="list-inline" style="margin:0 0;">
									<li style="width:100%;padding:5px;border-bottom:1px solid #e0e0e0"><?=$LIST[$i]['loan_interest_rate'];?>%</li>
									<li style="width:100%;padding:5px;"><?=$invest_period?></li>
								</ul>
							</td>
							<td align="center"><?=$LIST[$i]['invest_end_date']?></td>
							<td align="right"><span style="color:<?=($INVEST['cnt']>0)?'blue':'gray';?>"><?=number_format($INVEST['cnt'])?>명</span></td>
							<td align="right">
								<span style="color:<?=($INVEST['cnt']>0)?'blue':'gray';?>"><?=number_format($INVEST['amount'])?>원</span>
								<? if($LIST[$i]['ptl_repay_amount']&& $LIST[$i]['ptl_repay_amount'] < $LIST[$i]['recruit_amount']){ ?><br/><span style="color:#FF2222">일부상환 <?=price_cutting($LIST[$i]['ptl_repay_amount'])?>원</span><? } ?>
							</td>
							<td align="center"><?=$dc_ip_result?></td>
							<td align="center">
								<?=$LIST[$i]['repay_acct_no']?>
								<? if($LIST[$i]['loan_mb_no']) { ?>
								<div style="margin-top:4px;padding:8px 0 0;font-size:12px;line-height:13px;border-top:1px dotted #AAA">
									<?=$LIST[$i]['mb_title']?><br>
									( <?=$LIST[$i]['mb_id']?> )
								</div>
								<? } ?>
							</td>
							<td align="center"><?=substr($LIST[$i]['insert_date'], 0, 10)?></td>
							<td align="center" style="min-width:120px">
							</td>
						</tr>
	<?
	$num--;
}
?>
					</tbody>
				</table>
			</div>
			<div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $rows, 10); ?></div>

		</div>
	</div>
</div>

<? include_once ('../admin.tail.php'); ?>