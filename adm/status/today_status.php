<?php
$sql = " select sum(IF(mb_id<>'',1,0)) as mb_cnt, count(*) as total_cnt from ".$g5['login_table']." where mb_id <> '$config[cf_admin]' ";
$row = sql_fetch($sql);
$g_count = $row['total_cnt']-$row['mb_cnt'];
$m_count = $row['total_cnt']-$g_count;
if ($row['total_cnt']>$config[cf_8]) {
	$max=$row['total_cnt'];
	sql_query(" update ".$g5['config_table']." set cf_8='$max' ");
}

$temp = sql_fetch("select vs_count from ".$g5['visit_sum_table']." where vs_date = '".G5_TIME_YMD."'");
$today_visit = intval($temp[vs_count]);

$temp1 = sql_fetch("select vs_count from `".$g5['visit_sum_table']."` where vs_date = DATE_SUB('".G5_TIME_YMD."', INTERVAL 1 DAY)");
$yester_visit = intval($temp1[vs_count]);

$sql = " select max(vs_count) as cnt from ".$g5['visit_sum_table'];
$row = sql_fetch($sql);
$vi_max = $row[cnt];

$sql = " select sum(vs_count) as cnt from ".$g5['visit_sum_table'];
$row = sql_fetch($sql);
$visit_total = $row['cnt'];


// 금월
$sql = "select sum(vs_count) as cnt from ".$g5['visit_sum_table']." where vs_date between '".date("Y-m-01",time())."' and '".date("Y-m-d",time())."' ";
$row = sql_fetch($sql);
$visit_cnt[month] = $row['cnt'];
$visit_href_thismonth = G5_ADMIN_URL."/visit_date.php?fr_date=".date("Y-m-01",time())."&to_date=".date("Y-m-d",time());

// 전체 게시물수
$sql = " select sum(bo_count_write) as total from ".$g5['board_table']."";
$row = sql_fetch($sql);
$total_write  = $row[total];

// 전체 코멘트수
$sql = " select sum(bo_count_comment) as total from ".$g5['board_table']."";
$row = sql_fetch($sql);
$total_comment  = $row[total];

// 그누보드 전체 디비용량 구하기
$sql = "SHOW TABLE STATUS FROM ".G5_MYSQL_DB;
//$sql = "SHOW TABLE STATUS FROM ".G5_MYSQL_DB." LIKE 'g5%'";
$result = sql_query($sql);
$db_size = 0;
while($dbData=sql_fetch_array($result)){
	$db_size += $dbData['Data_length'] + $dbData['Index_length'];
}

// 계정 용량 구하기
$du = `du ../ -csk`;
?>

<style>
.tablex { border:0; }
.tdL { text-align:center; }
.tdR { text-align:right; padding-right:4px; }
</style>

<table width="180" border="0" cellpadding="0" cellspacing="1" bgcolor="e5e5e5">
  <tr>
    <td>
      <table width="100%" bgcolor="#ffffff" class="tablex">
				<tr>
          <td class="tdL" rowspan="2"><a href='<?=G5_BBS_URL?>/current_connect.php' target='_blank'>실시간사용자</a></td><td class="tdR">일반 <font color="red"><?=number_format($g_count)?> 명</font></td>
        </tr>
				<tr>
          <td class="tdR">로그인 <font color="red"><?=number_format($m_count)?> 명</font></td>
        </tr>
				<tr>
          <td class="tdL">총방문객</td><td class="tdR"><?=number_format($visit_total)?> 명</td>
        </tr>
				<tr>
					<td class="tdL">최대방문</td><td class="tdR"><?=number_format($vi_max)?> 명</td>
        </tr>
				<tr>
					<td class="tdL">오늘방문</td><td class="tdR"><?=number_format($today_visit)?> 명</td>
        </tr>
				<tr>
					<td class="tdL">어제방문</td><td class="tdR"><?=number_format($yester_visit)?> 명</td>
        </tr>
				<tr>
					<td class="tdL">이달방문</td><td class="tdR"><?=number_format($visit_cnt['month'])?> 명</td>
        </tr>
				<tr>
					<td colspan="2" height="1" bgcolor="#EFEFEF"></td>
        </tr>
				<tr>
					<td class="tdL">총 게시물</td><td class="tdR"><?=number_format($total_write)?> 개</td>
        </tr>
				<tr>
					<td class="tdL">총 코멘트</td><td class="tdR"><?=number_format($total_comment)?> 개</td>
        </tr>
				<tr>
					<td class="tdL">DB 사용량</td><td class="tdR"><?=sprintf("%0.1f", $db_size/(1024*1024))?> MB</td>
        </tr>
				<tr>
					<td class="tdL">계정 사용량</td><td class="tdR"><?=sprintf("%0.1f", $du/1024); ?> MB</td>
        </tr>
      </table>
    </td>
  </tr>
</table>