<?

include_once('./_common.php');


$DracoCounter_URL  = G5_ADMIN_URL  ."/status";  // 활성도통계 설치폴더
$DracoCounter_PATH = G5_ADMIN_PATH ."/status";  // 활성도통계 절대경로





$day = 30; //기간 - 기본검색설정된 기간입니다. 필요에따라 수정해서 사용가능합니다.

if (empty($fr_date)) $fr_date = date("Y-m-d", G5_SERVER_TIME-86400*$day);
if (empty($to_date)) $to_date = G5_TIME_YMD;

// m3stats 설정
$limit=(strtotime($to_date)- strtotime($fr_date)) / 86400;
$bar_width = 60; // 그래프 최대 너비 (기본 60)

$pluginDracoCounter = G5_PLUGIN_PATH.'/DracoCounter/gDracoCounter.php';
include_once $pluginDracoCounter;

$pluginDracoData = ShowDracoCounter(30, 29); // 날짜, 날짜의 가로폭 : 총 가로폭은 날짜 * 날짜가로폭


// 전체 회원수
$row = sql_fetch("SELECT COUNT(mb_id) AS cnt FROM ".$g5['member_table']." WHERE mb_level='1'");
$total_member  = $row['cnt'];


// 남/여 성비
$sql = "
	SELECT
		( SELECT COUNT(mb_sex) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_sex`='m' ) AS m_cnt,
		( SELECT COUNT(mb_sex) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_sex`='w' ) AS w_cnt";
$row = sql_fetch($sql);
$man_num   = $row['m_cnt'];
$woman_num = $row['w_cnt'];
$unknown_sex_num = $total_member - $man_num - $woman_num;

// 성비 계산
$total_num = $man_num + $woman_num;
$man_per   = @sprintf("%.2f",(($man_num / $total_num)*100));
$woman_per = @sprintf("%.2f",(($woman_num / $total_num)*100));
$unknown_sex_per = @sprintf("%.2f",(($unknown_sex_num / $total_num)*100));


// 연령분포
$old1 = date("Y-m-d",strtotime("-9 year", time()));		// 0~9세
$old2 = date("Y-m-d",strtotime("-19 year", time()));	// 10~19세
$old3 = date("Y-m-d",strtotime("-29 year", time()));	// 20~29세
$old4 = date("Y-m-d",strtotime("-39 year", time()));	// 30~39세
$old5 = date("Y-m-d",strtotime("-49 year", time()));	// 40~49세
// 50세 이상

$sql = "
	SELECT
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` > '$old1' ) AS year0,
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` > '$old2' AND `mb_birth` <= '$old1') AS year1,
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` > '$old3' AND `mb_birth` <= '$old2') AS year2,
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` > '$old4' AND `mb_birth` <= '$old3') AS year3,
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` > '$old5' AND `mb_birth` <= '$old4') AS year4,
		( SELECT COUNT(mb_birth) FROM ".$g5['member_table']." WHERE mb_level='1' AND member_group='F' AND `mb_birth` <= '$old5') AS year5";
$row = sql_fetch($sql);
$year0 = $row['year0'];
$year1 = $row['year1'];
$year2 = $row['year2'];
$year3 = $row['year3'];
$year4 = $row['year4'];
$year5 = $row['year5'];
$unknown_year = $total_member - $year0 - $year1 - $year2 - $year3 - $year4 - $year5;

// 연령분포 % 계산
$year0_per = @sprintf("%.2f",(($year0 / $total_num)*100));
$year1_per = @sprintf("%.2f",(($year1 / $total_num)*100));
$year2_per = @sprintf("%.2f",(($year2 / $total_num)*100));
$year3_per = @sprintf("%.2f",(($year3 / $total_num)*100));
$year4_per = @sprintf("%.2f",(($year4 / $total_num)*100));
$year5_per = @sprintf("%.2f",(($year5 / $total_num)*100));
$unknown_year_per = @sprintf("%.2f",(($unknown_year / $total_num)*100));


/*  거주지 분포  */
// 서울거주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%서울%'";
$row = sql_fetch($sql);
$seoul  = $row['addr'];

// 부산거주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%부산%'";
$row = sql_fetch($sql);
$busan  = $row['addr'];

// 대구거주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%대구%'";
$row = sql_fetch($sql);
$daegu  = $row['addr'];

// 인천거주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%인천%'";
$row = sql_fetch($sql);
$incheon  = $row['addr'];

// 광주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%광주%'";
$row = sql_fetch($sql);
$gwangju  = $row['addr'];

// 대전
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%대전%'";
$row = sql_fetch($sql);
$daejeon  = $row['addr'];

// 울산
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%울산%'";
$row = sql_fetch($sql);
$ulsan  = $row['addr'];

// 강원
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%강원%'";
$row = sql_fetch($sql);
$gangwon  = $row['addr'];

// 경기
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%경기%'";
$row = sql_fetch($sql);
$gyeonggi  = $row['addr'];

// 경남
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%경남%'";
$row = sql_fetch($sql);
$gyeongnam  = $row['addr'];

// 경북
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%경북%'";
$row = sql_fetch($sql);
$gyeongbuk  = $row['addr'];

// 전남
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%전남%'";
$row = sql_fetch($sql);
$jeonnam = $row['addr'];

// 전북
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%전북%'";
$row = sql_fetch($sql);
$jeonbuk  = $row['addr'];

// 제주
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%제주%'";
$row = sql_fetch($sql);
$jeju  = $row['addr'];

// 충남
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%충남%'";
$row = sql_fetch($sql);
$chungnam  = $row['addr'];

// 충북
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%충북%'";
$row = sql_fetch($sql);
$chungbuk  = $row['addr'];

// 해외
$sql = "SELECT COUNT(mb_addr1) AS addr FROM ".$g5['member_table']." WHERE `mb_addr1` LIKE '%해외%'";
$row = sql_fetch($sql);
$oversea  = $row['addr'];

// 지역분포 % 계산
$seoul_per     = @sprintf("%.2f",(($seoul / $total_num)*100));
$busan_per     = @sprintf("%.2f",(($busan / $total_num)*100));
$daegu_per     = @sprintf("%.2f",(($daegu / $total_num)*100));
$incheon_per   = @sprintf("%.2f",(($incheon / $total_num)*100));
$gwangju_per   = @sprintf("%.2f",(($gwangju / $total_num)*100));
$daejeon_per   = @sprintf("%.2f",(($daejeon / $total_num)*100));
$ulsan_per     = @sprintf("%.2f",(($ulsan / $total_num)*100));
$gangwon_per   = @sprintf("%.2f",(($gangwon / $total_num)*100));
$gyeonggi_per  = @sprintf("%.2f",(($gyeonggi / $total_num)*100));
$gyeongnam_per = @sprintf("%.2f",(($gyeongnam / $total_num)*100));
$gyeongbuk_per = @sprintf("%.2f",(($gyeongbuk / $total_num)*100));
$jeonnam_per   = @sprintf("%.2f",(($jeonnam / $total_num)*100));
$jeonbuk_per   = @sprintf("%.2f",(($jeonbuk / $total_num)*100));
$jeju_per      = @sprintf("%.2f",(($jeju / $total_num)*100));
$chungnam_per  = @sprintf("%.2f",(($chungnam / $total_num)*100));
$chungbuk_per  = @sprintf("%.2f",(($chungbuk / $total_num)*100));
$oversea_per   = @sprintf("%.2f",(($oversea / $total_num)*100));

?>
	<div style="float:left; width:100%;">
		<form name="fcount" method="get" style="padding:0;">
			<input type="hidden" name="ymd">
			<input type="hidden" name="gr_id" value="<?=$gr_id?>">
			<input type="hidden" name="bo_table" value="<?=$bo_table?>">
			<div style="padding:10px 0 15px 20px; background-color:#fff;"><strong>&lt;최근 <?=$day?>일 기준 방문자 그래프&gt;</strong></div>
			&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?=$fr_date?> ~ <?=$to_date?> &nbsp;<br>
			<div style="width:920px; padding:0 0 20px 0; background-color:#fff;"><?=$pluginDracoData?></div>
		</form>
	</div>

	<div style="float:left; width:100%;">
		<div style="float:left; width:240px;"><? include $DracoCounter_PATH.'/today_status.php'; ?></div>
		<div style="float:left; width:340px; padding-left:15px;"><? include $DracoCounter_PATH.'/visit_status.php'; ?></div>
		<div style="float:left; width:340px; padding-left:15px"><? include $DracoCounter_PATH.'/member_status.php'; ?></div>
	</div>

	<div style="float:left; width:100%; margin-top:20px;">
		<img src="./status/img/bul2.gif" align="absmiddle">
		<b>총 회원 수 : <?=number_format($total_member)?> 명</b> &nbsp;
		(오늘가입: <?=$member_cnt[0]?> 명 ,&nbsp;
    이달가입 <?=number_format($member_cnt['month'])?> 명)<br />

<?
if($member['mb_level']>='9') {
	echo " &nbsp;&nbsp; <font color='#666666'> 등급별 회원수 &nbsp;&nbsp;";
	for($i = 2 ; $i <= 14 ; $i++) {
		$sql = "SELECT COUNT(mb_no) AS mb_num FROM ".$g5['member_table']." WHERE mb_level = '$i'";
		$result = sql_query($sql);
		$row = sql_fetch_array($result);
	if($row['mb_num']!=0)

	echo " LEVEL $i : ".$row['mb_num']." 명 &nbsp;</font> "; }
}
?>

	</div>

	<div style="float:left; width:100%; margin-top:20px;">
		<div style="float:left; width:230px;">
			<img src="./status/img/bul2.gif" align="absmiddle"> <b>연령별 분포</b><br>
			<table class="tableX">
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 0 ~ 9세</td><td style="text-align:right;"><?=number_format($year0);?>명 (<?=$year0_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 10 ~ 19세</td><td style="text-align:right;"><?=number_format($year1);?>명 (<?=$year1_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 20 ~ 29세</td><td style="text-align:right;"><?=number_format($year2);?>명 (<?=$year2_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 30 ~ 39세</td><td style="text-align:right;"><?=number_format($year3);?>명 (<?=$year3_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 40 ~ 49세</td><td style="text-align:right;"><?=number_format($year4);?>명 (<?=$year4_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">만 50세 이상</td><td style="text-align:right;"><?=number_format($year5);?>명 (<?=$year5_per;?>%)</td>
				</tr>
				<tr>
					<td style="background:#FAFAFA;text-align:center;">그 외</td><td style="text-align:right;"><?=number_format($unknown_year);?>명 (<?=$unknown_year_per;?>%)</td>
				</tr>
			</table>
		</div>
		<div style="width:200px; float:left;">
			<img src="./status/img/bul2.gif" align="absmiddle"> <b>남/여성별 분포</b><br>
			<table class="tableX">
				<tr>
					<td style="background:#FAFAFA;text-align:center;">남자</td><td style="text-align:right;"><?=number_format($man_num);?>명 (<?=$man_per;?>%)</td>
				<tr>
				</tr>
					<td style="background:#FAFAFA;text-align:center;">여자</td><td style="text-align:right;"><?=number_format($woman_num);?>명 (<?=$woman_per;?>%)</td>
				<tr>
				</tr>
					<td style="background:#FAFAFA;text-align:center;">불명</td><td style="text-align:right;"><?=number_format($unknown_sex_num);?>명 (<?=$unknown_sex_per;?>%)<br></td>
				</tr>
			</table>
		</div>
		<div style="float:left; width:490px">
			<img src="./status/img/bul2.gif" align="absmiddle"> <b>지역별 분포</b><br>
			<div>
				<div style="float:left; width:163px;">
					<table class="tableX">
						<tr>
							<td style="background:#FAFAFA;text-align:center;">서울</td><td style="text-align:right;"><?=number_format($seoul);?> 명 (<?=$seoul_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">경기</td><td style="text-align:right;"><?=number_format($gyeonggi);?> 명 (<?=$gyeonggi_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">인천</td><td style="text-align:right;"><?=number_format($incheon);?> 명 (<?=$incheon_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">강원</td><td style="text-align:right;"><?=number_format($gangwon);?> 명 (<?=$gangwon_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">충북</td><td style="text-align:right;"><?=number_format($chungbuk);?> 명 (<?=$chungbuk_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">충남</td><td style="text-align:right;"><?=number_format($chungnam);?> 명 (<?=$chungnam_per;?>%)</td>
						</tr>
					</table>
				</div>
				<div style="float:left; width:163px;">
					<table class="tableX">
						<tr>
							<td style="background:#FAFAFA;text-align:center;">대전</td><td style="text-align:right;"><?=number_format($daejeon);?> 명 (<?=$daejeon_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">경북</td><td style="text-align:right;"><?=number_format($gyeongbuk);?> 명 (<?=$gyeongbuk_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">경남</td><td style="text-align:right;"><?=number_format($gyeongnam);?> 명 (<?=$gyeongnam_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">대구</td><td style="text-align:right;"><?=number_format($daegu);?> 명 (<?=$daegu_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">울산</td><td style="text-align:right;"><?=number_format($ulsan);?> 명 (<?=$ulsan_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">부산</td><td style="text-align:right;"><?=number_format($busan);?> 명 (<?=$busan_per;?>%)</td>
						</tr>
					</table>
				</div>
				<div style="float:left; width:163px;">
					<table class="tableX">
						<tr>
							<td style="background:#FAFAFA;text-align:center;">전북</td><td style="text-align:right;"><?=number_format($jeonbuk);?> 명 (<?=$jeonbuk_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">전남</td><td style="text-align:right;"><?=number_format($jeonnam);?> 명 (<?=$jeonnam_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">광주</td><td style="text-align:right;"><?=number_format($gwangju);?> 명 (<?=$gwangju_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">제주</td><td style="text-align:right;"><?=number_format($jeju);?> 명 (<?=$jeju_per;?>%)</td>
						<tr>
						</tr>
							<td style="background:#FAFAFA;text-align:center;">해외</td><td style="text-align:right;"><?=number_format($oversea);?> 명 (<?=$oversea_per;?>%)</td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<div id="div_visit" style="float:left; width:100%; margin-top:20px;">
		<table width="920">
			<tr bgcolor="#F8F8EF" align="center">
				<td>날짜</td>
				<td colspan="2">전체방문</td>
				<td colspan="2">직접방문</td>
				<td colspan="2">가입</td>
				<? if($config['cf_login_point']) { ?>
				<td colspan="2">로그인</td>
				<? } ?>
				<!--<td colspan="2">원글/댓글</td>-->
			</tr>
<?
	$day .= " (".get_yoil($print_date[i]).")";

	for($i=0; $i<$limit; $i++) {
		$date = date("Y-m-d", time()-$i*24*60*60);
		$print_date[$i] = substr($date,2);
		$date_1 = date("Y-m-d", time()-($i-1)*24*60*60);

		// 방문자 수
		$temp = sql_fetch("SELECT vs_count FROM ".$g5['visit_sum_table']." WHERE vs_date='$date'");
		$count_visit[$i] = intval($temp[vs_count]);
		if($max_count_visit<$count_visit[$i]) $max_count_visit = $count_visit[$i];

		// 직접 방문자 수 (referer가 없는 경우)
		$temp = sql_fetch("SELECT COUNT(*) AS total FROM ".$g5['visit_table']." WHERE vi_date='$date' AND vi_referer=''");
		$count_direct[$i] = $temp['total'];
		if($max_count_direct<$count_direct[$i]) {
			$max_count_direct = $count_direct[$i];
			if($max_count_direct>$config['cf_3']){
				sql_query("UPDATE ".$g5['config_table']." SET cf_3='$max_count_direct' ");
			}
		}
		// 가입자 수 (mb_datetime으로 확인)
		$temp = sql_fetch("SELECT COUNT(mb_no) AS total FROM `".$g5['member_table']."` WHERE mb_datetime LIKE '$date%'");
		$count_join[$i] = $temp['total'];
		if($max_count_join<$count_join[$i]) $max_count_join = $count_join[$i];
		// 로그인 수 (로그인 포인트가 없으면 계산 안되므로 안 띄운다)
		if($config[cf_login_point]) {
			$temp = sql_fetch("SELECT COUNT(*) AS total FROM ".$g5['point_table']." WHERE po_rel_table='@login' AND po_datetime LIKE '$date%'");
			$count_login[$i] = $temp['total'];
			if($max_count_login<$count_login[$i]) $max_count_login = $count_login[$i];
			if($max_count_login>$config['cf_2']){
				sql_query("UPDATE ".$g5['config_table']." SET cf_2='$max_count_login' ");
			}
		}
		// 원글 수
		$temp = sql_fetch("SELECT COUNT(*) AS total FROM ".$g5['board_new_table']." WHERE wr_id=wr_parent AND bn_datetime LIKE '$date%'");
		$count_article[$i] = $temp['total'];
		if($max_count_article<$count_article[$i]) $max_count_article = $count_article[$i];
		// 댓글 수
		$temp = sql_fetch("SELECT COUNT(*) AS total FROM ".$g5['board_new_table']." WHERE wr_id!=wr_parent AND bn_datetime LIKE '$date%'");
		$count_comment[$i] = $temp['total'];
		if($max_count_comment<$count_comment[$i]) $max_count_comment = $count_comment[$i];
	}

	for($i=0; $i<$limit; $i++) {
?>
			<tr>
				<td width=120 align="center"><?=$print_date[$i];?> (<?=get_yoil($print_date[$i]);?>)</td>
				<td class="m3stats_align_c"><?=number_format($count_visit[$i])?></td>
				<td><img src="<?=$DracoCounter_URL;?>/img/graph.gif" height="9" width="<?=ceil($count_visit[$i]/$max_count_visit*$bar_width);?>" /></td>
				<td class="m3stats_align_c"><?=number_format($count_direct[$i])?></td>
				<td><img src="<?=$DracoCounter_URL;?>/img/graph.gif" height="9" width="<?=ceil($count_direct[$i]/$max_count_direct*$bar_width);?>" /></td>
				<td class="m3stats_align_c"><?=number_format($count_join[$i])?></td>
				<td><img src="<?=$DracoCounter_URL;?>/img/graph.gif" height="9" width="<?=ceil($count_join[$i]/$max_count_join*$bar_width);?>" /></td>
				<? if($config['cf_login_point']) { ?>
				<td class="m3stats_align_c"><?=number_format($count_login[$i])?></td>
				<td><img src="<?=$DracoCounter_URL;?>/img/graph.gif" height="9" width="<?=ceil($count_login[$i]/$max_count_login*$bar_width);?>" /></td>
				<? } ?>
				<!--
				<td class="m3stats_align_c"><?=$count_article[$i];?>/<?=$count_comment[$i];?></td>
				<td><img src="<?=$DracoCounter_URL;?>/img/graph.gif" height="9" width="<?=ceil($count_article[$i]/$max_count_article*$bar_width);?>" /></td>
				//-->
			</tr>
<?
	}
?>
		</table>
	</div>
