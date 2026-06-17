<?php
$sub_menu = "930030";
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

foreach($_REQUEST as $k=>$v) { $$_REQUEST[$k] = $v; }
if (!$srch_y) $srch_y = date("Y");

$g5['title'] = $srch_y.' 회원 통계';

include_once (G5_ADMIN_PATH.'/admin.head.php');
?>
<?
$month_before = date( 'Y-m-d', strtotime( date('Y-m-d') . ' -1 month' ) );

$start_m = 1;
$to_m = 12;
if ( $srch_y==date("Y") ) $to_m=date("m");

if ($srch_y == date("Y")) {
	$chk_before_data_sql = "SELECT count(*) cnt FROM cf_jipyo_first_invest WHERE ym='".substr($month_before,0,7)."'";
	$chk_before_data_row = sql_fetch($chk_before_data_sql);
	if (!$chk_before_data_row['cnt']) {
		?>
		<script>
		$.ajax({
			url: '/adm/jipyo/members_jip.php',
			type: 'GET',
			data: {ym:"<?=substr($month_before,0,7)?>"},
			dataType: 'JSON',
			success: function(data) {
				//console.log(data);
				if (data["result"]=="ok") {
					alert(data["ym"]+" 집계완료\n\n화면을 새로고칩니다.");
					self.location.reload();
				} else {
					alert("오류발생");
				}
			},
			error: function(e) { alert("네트워크 오류 입니다. 잠시 후 다시 요청하십시요."); return; }
		});
		</script>
		<?
	}
}
?>
<div class="tbl_head02 tbl_wrap">
	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
	<form method="post" name="f_srch">
		<select name="srch_y" onchange="go_srch();" style="height:25px;width:75px;">
			<?
			for ($i=date("Y") ; $i>="2016"; $i--) {
				?>
				<option value="<?=$i?>" <?=$srch_y==$i?"selected":""?> ><?=$i?></option>
				<?
			}
			?>
		</select>
	</form>
	</div>

	<table class="table table-striped table-bordered table-hover" style="min-width:1000px; padding-top:0; font-size:12px;">
		<tr>
			<th scope="col" style="text-align:center;border:1px solid green;" rowspan=2>구분</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=3>누적회원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=6>신규회원</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=5>투자회원 전환</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>탈퇴</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>휴면계정</th>
			<th scope="col" style="text-align:center;border:1px solid green;" colspan=2>방문자수</th>
		</tr>
		<tr>
			<!-- 누적회원 -->
			<th scope="col" style="text-align:center;border:1px solid green;">개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">법인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">증가율</th>

			<!-- 신규회원 -->
			<th scope="col" style="text-align:center;border:1px solid green;">개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">법인</th>
			<!--th scope="col" style="text-align:center;border:1px solid green;">증가율</th-->
			<th scope="col" style="text-align:center;border:1px solid green;">제휴사 유치</th>
			<th scope="col" style="text-align:center;border:1px solid green;">마케팅 제휴</th>
			<th scope="col" style="text-align:center;border:1px solid green;">외부행사</th>
			<th scope="col" style="text-align:center;border:1px solid green;">이벤트</th>

			<th scope="col" style="text-align:center;border:1px solid green;">개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">법인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">제휴사 유치</th>
			<th scope="col" style="text-align:center;border:1px solid green;">외부행사</th>
			<th scope="col" style="text-align:center;border:1px solid green;">이벤트</th>

			<th scope="col" style="text-align:center;border:1px solid green;">개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">법인</th>

			<th scope="col" style="text-align:center;border:1px solid green;">개인</th>
			<th scope="col" style="text-align:center;border:1px solid green;">법인</th>

			<th scope="col" style="text-align:center;border:1px solid green;">웹</th>
			<th scope="col" style="text-align:center;border:1px solid green;">모바일</th>
		</tr>
		<?

		for ($i = $to_m ; $i >= $start_m ; $i--) {

			$ii = str_pad($i, 2 , '0' , STR_PAD_LEFT);

			$before_ym = $day_before = date( 'Y-m', strtotime( $srch_y."-".$ii."-01" . ' -1 month' ) );

			$time_start = microtime_float();

			$new_nu = get_new_mem_nu("$srch_y-$ii");

			$new_bf_nu = get_new_mem_nu($before_ym);
			$add_per = @(($new_nu['total'] - $new_bf_nu['total']) / $new_nu['total']);
			$add_per = floor($add_per * 100 *100) /100;

			$new_m = get_new_mem("$srch_y-$ii");
			$new_bf_m = get_new_mem($before_ym);
			$new_per = @(($new_m['total'] - $new_bf_m['total']) / $new_m['total']);
			$new_per = floor($new_per * 100 *100) /100;

			$time_check = microtime_float();
			$t1 += $time_check - $time_start ;

			// 신규회원 제휴사 유치
			$pro_m = get_pro_mem("$srch_y-$ii");
			$pro_m_list = "";
			for ($j=0 ; $j<count($pro_m["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $pro_m_list .= "\n";
				$pro_m_list .= $pro_m["list"][$j]["pid"]." - ".$pro_m["list"][$j]["cnt"];
			}

			// 신규회원 마케팅 제휴사 유치
			$proM_m = get_Mpro_mem("$srch_y-$ii");
			$proM_m_list = "";
			for ($j=0 ; $j<count($proM_m["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $proM_m_list .= "\n";
				$proM_m_list .= $proM_m["list"][$j]["pid"]." - ".$proM_m["list"][$j]["cnt"];
			}


			$rec_m = get_rec_mem("$srch_y-$ii");
			$rec_m_list = "";
			for ($j=0 ; $j<count($rec_m["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $rec_m_list .= "\n";
				$rec_m_list .= $rec_m["list"][$j]["pid"]." - ".$rec_m["list"][$j]["cnt"];
			}

			$rec_m2 = get_rec_mem2("$srch_y-$ii");
			$rec_m_list2 = "";
			for ($j=0 ; $j<count($rec_m2["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $rec_m_list2 .= "\n";
				$rec_m_list2 .= $rec_m2["list"][$j]["pid"]." - ".$rec_m2["list"][$j]["cnt"];
			}

			$time_check2 = microtime_float();
			$t2 += $time_check2 - $time_check;


			// 첫 투자 추출 시작
			//$first_m = get_first_inv("$srch_y-$ii");
			//$first_m = get_first_inv2("$srch_y-$ii");
			//$first_m = get_first_inv3("$srch_y-$ii");
			//if ("$srch_y-$ii"==date("Y-m")) $first_m = get_first_inv("$srch_y-$ii");
			//else if ("$srch_y-$ii"=="$month_before" and date("d")<"05") $first_m = get_first_inv("$srch_y-$ii");
			//else $first_m = get_first_inv4("$srch_y-$ii");

			//if ("$srch_y-$ii"==date("Y-m")) $first_m = get_first_inv("$srch_y-$ii");
			//else $first_m = get_first_inv4("$srch_y-$ii");

			$first_m = get_first_inv("$srch_y-$ii");

			//if ("$srch_y-$ii"==date("Y-m")) get_first_inv_pro_jip("$srch_y-$ii");
			//else $first_m_pro = get_first_inv_pro("$srch_y-$ii");
			$first_m_pro = get_first_inv_pro_jip("$srch_y-$ii");

			$first_m_pro_list = "";
			for ($j=0 ; $j<count($first_m_pro["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $first_m_pro_list .= "\n";
				$first_m_pro_list .= $first_m_pro["list"][$j]["pid"]." - ".$first_m_pro["list"][$j]["cnt"];
			}

			//if ("$srch_y-$ii"==date("Y-m")) get_first_inv_evnt_jip("$srch_y-$ii");
			//else $first_m_evnt = get_first_inv_evnt("$srch_y-$ii");
			$first_m_evnt = get_first_inv_evnt_jip("$srch_y-$ii");

			$first_m_evnt_list = "";
			for ($j=0 ; $j<count($first_m_evnt["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $first_m_evnt_list .= "\n";
				$first_m_evnt_list .= $first_m_evnt["list"][$j]["pid"]." - ".$first_m_evnt["list"][$j]["cnt"];
			}

			//if ("$srch_y-$ii"==date("Y-m")) get_first_inv_evnt2_jip("$srch_y-$ii");
			//else $first_m_evnt2 = get_first_inv_evnt2("$srch_y-$ii");
			$first_m_evnt2 = get_first_inv_evnt2_jip("$srch_y-$ii");

			$first_m_evnt_list2 = "";
			for ($j=0 ; $j<count($first_m_evnt2["list"]) ; $j++) {
				if ($j>30) die("for loop error");

				if ($j<>0) $first_m_evnt_list2 .= "\n";
				$first_m_evnt_list2 .= $first_m_evnt2["list"][$j]["pid"]." - ".$first_m_evnt2["list"][$j]["cnt"];
			}

			// 첫 투자 추출 끝


			$time_check3 = microtime_float();
			$t3 += $time_check3 - $time_check2;

			$out_mem = get_out_mem("$srch_y-$ii");
			$time_check4 = microtime_float();
			$t4 += $time_check4 - $time_check3;

			$rest_mem = get_rest_mem("$srch_y-$ii");
			$time_check5 = microtime_float();
			$t5 += $time_check5 - $time_check4;

			if ("$srch_y-$ii"==date("Y-m")) $visit_mem=get_visit_mem("$srch_y-$ii");
			else $visit_mem = get_visit_mem_old("$srch_y-$ii");
			$time_check6= microtime_float();
			$t6 += $time_check6 - $time_check5;
			?>
		<tr>
			<td style="text-align:center;border:1px solid green;"><?=$ii?></td>

			<!-- 누적회원 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=number_format($new_nu['P'])?></div>
			</td>
			<td style="text-align:center;border:1px solid green;"><?=number_format($new_nu['C'])?></td>
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<a title="( <?=$new_nu['total']?>-<?=$new_bf_nu['total']?> ) / <?=$new_nu['total']?>">
				<?=$add_per?number_format($add_per,2)." %":""?></a>
				</div>
			</td>

			<!-- 신규회원 -->
			<td style="text-align:center;border:1px solid green;"><!-- 개인 -->
				<div style="border:0px solid red;width:70px;margin:0 auto;text-align:right;padding-right:23px;">
				<?=$new_m['P']?number_format($new_m['P']):""?></a>
				</div>
			</td>
			<td style="text-align:center;border:1px solid green;"><!-- 법인 -->
				<div style="border:0px solid red;width:50px;margin:0 auto;text-align:right;padding-right:32px;">
				<?=$new_m['C']?number_format($new_m['C']):""?></a>
				</div>
			</td>
			<!--td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red;width:60px;margin:0 auto;text-align:right;padding-right:10px;">
				<a title="( <?=$new_m['total']?>-<?=$new_bf_m['total']?> ) / <?=$new_m['total']?>">
				<?=$new_per?number_format($new_per,2)." %":""?></a>
				</div>
			</td-->
			<td style="text-align:center;border:1px solid green;"><!-- 제휴사 유치 -->
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<a title="<?=$pro_m_list?>">
				<?=$pro_m['total']?number_format($pro_m['total']):""?></a>
				</div>
			</td>
			<td style="text-align:center;border:1px solid green;"><!-- 마케팅 제휴사 유치 -->
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<a title="<?=$proM_m_list?>">
				<?=$proM_m['total']?number_format($proM_m['total']):""?></a>
				</div>
			</td>
			<td style="text-align:center;border:1px solid green;"><!-- 외부행사 -->
				<div style="border:0px solid red;width:50px;margin:0 auto;text-align:right;padding-right:23px;">
				<a title="<?=$rec_m_list?>">
				<?=$rec_m['total']?number_format($rec_m['total']):""?></a>
				</div>
			</td>
			<td style="text-align:center;border:1px solid green;"><!-- 이벤트 -->
				<div style="border:0px solid red;width:50px;margin:0 auto;text-align:right;padding-right:23px;">
				<a title="<?=$rec_m_list2?>">
				<?=$rec_m2['total']?number_format($rec_m2['total']):""?></a>
				</div>
			</td>

			<!-- 투자회원 전환 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red;text-align:right;padding-right:10px;">
				<?=$first_m['P']?number_format($first_m['P']):""?>
				</div>
			</td>

			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$first_m['C']?number_format($first_m['C']):""?>
				</div>
			</td>

			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<a title="<?=$first_m_pro_list?>">
				<?=$first_m_pro['total']?number_format($first_m_pro['total']):""?></a>
				</div>
			</td>

			<!-- 최초투자 --><!-- 외부행사 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red;width:50px;margin:0 auto;text-align:right;padding-right:23px;">
				<a title="<?=$first_m_evnt_list?>">
				<?=$first_m_evnt['total']?number_format($first_m_evnt['total']):""?></a>
				</div>
			</td>

			<!-- 최초투자 --><!-- 내부행사 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red;width:50px;margin:0 auto;text-align:right;padding-right:23px;">
				<a title="<?=$first_m_evnt_list2?>">
				<?=$first_m_evnt2['total']?number_format($first_m_evnt2['total']):""?></a>
				</div>
			</td>

			<!-- 탈퇴회원 --><!-- 개인 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$out_mem['P']?number_format($out_mem['P']):""?>
				</div>
			</td>

			<!-- 탈퇴회원 --><!-- 법인 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$out_mem['C']?number_format($out_mem['C']):""?>
				</div>
			</td>

			<!-- 휴먼회원 --><!-- 개인 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$out_mem['P']?number_format($rest_mem['P']):""?>
				</div>
			</td>

			<!-- 탈퇴회원 --><!-- 법인 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$out_mem['C']?number_format($rest_mem['C']):""?>
				</div>
			</td>

			<!-- 방문회원 --><!-- 웹 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$visit_mem['P']?number_format($visit_mem['P']):""?>
				</div>
			</td>

			<!-- 방문회원 --><!-- 모바일 -->
			<td style="text-align:center;border:1px solid green;">
				<div style="border:0px solid red; text-align:right;padding-right:10px;">
				<?=$visit_mem['M']?number_format($visit_mem['M']):""?>
				</div>
			</td>
		</tr>
			<?
		}
		?>
	</table>

</div>

<script>
function go_srch() {
	var f = document.f_srch;
	f.submit();
}
</script>

<?
function get_new_mem_nu($ym) {
	$sql = "SELECT member_type , count(mb_no) cnt FROM g5_member where mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			 GROUP BY member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		if ($row['member_type']=="1") {  // 개인회원
			$retval['P'] = $row['cnt'];
			$retval['total'] += $row['cnt'];
		} else if ($row['member_type']=="2") {  // 개인회원
			$retval['C'] = $row['cnt'];
			$retval['total'] += $row['cnt'];
		}
	}

	return $retval;
}
function get_new_mem($ym) {
	$sql = "SELECT member_type , count(mb_no) cnt FROM g5_member where mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			 GROUP BY member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>10) die("for loof error"); // safe loop

		$row = sql_fetch_array($res);

		if ($row['member_type']=="1") {  // 개인회원
			$retval['P'] = $row['cnt'];
			$retval['total'] += $row['cnt'];
		} else if ($row['member_type']=="2") {  // 개인회원
			$retval['C'] = $row['cnt'];
			$retval['total'] += $row['cnt'];
		}
	}

	return $retval;
}

function get_pro_mem($ym) {
	$idx = 0 ;

	// 공감엠엔씨
	$gmnc_cnt = get_pro_mem_gmnc($ym);
	if ($gmnc_cnt) {
		$retval['list'][$idx]['pid'] = '공감엠엔씨';
		$retval['list'][$idx]['cnt'] = $gmnc_cnt;
		$retval['total'] += $gmnc_cnt;
		$idx++;
	}

	// 아이템베이
	$itembay_cnt = get_pro_mem_itembay($ym);
	if ($itembay_cnt) {
		$retval['list'][$idx]['pid'] = 'itembay';
		$retval['list'][$idx]['cnt'] = $itembay_cnt;
		$retval['total'] += $itembay_cnt;
		$idx++;
	}

	// 올리고
	$oligo_cnt = get_pro_mem_oligo($ym);
	if ($oligo_cnt) {
		$retval['list'][$idx]['pid'] = 'oligo';
		$retval['list'][$idx]['cnt'] = $oligo_cnt;
		$retval['total'] += $oligo_cnt;
		$idx++;
	}

	// 핀크
	$finnq_cnt = get_pro_mem_finnq($ym);
	if ($finnq_cnt) {
		$retval['list'][$idx]['pid'] = 'finnq';
		$retval['list'][$idx]['cnt'] = $finnq_cnt;
		$retval['total'] += $finnq_cnt;
		$idx++;
	}

	// 한경TV
	$wowstar_cnt = get_pro_mem_wowstar($ym);
	if ($wowstar_cnt) {
		$retval['list'][$idx]['pid'] = '한경TV';
		$retval['list'][$idx]['cnt'] = $wowstar_cnt;
		$retval['total'] += $wowstar_cnt;
		$idx++;
	}

	// 조선일보
	$chosun_cnt = get_pro_mem_chosun($ym);
	if ($chosun_cnt) {
		$retval['list'][$idx]['pid'] = '땅집고';
		$retval['list'][$idx]['cnt'] = $chosun_cnt;
		$retval['total'] += $chosun_cnt;
		$idx++;
	}

	// 조선일보
	$r114_cnt = get_pro_mem_r114($ym);
	if ($r114_cnt) {
		$retval['list'][$idx]['pid'] = '부동산114';
		$retval['list'][$idx]['cnt'] = $r114_cnt;
		$retval['total'] += $r114_cnt;
		$idx++;
	}

	// 티비톡
	$tvtalk_cnt = get_pro_mem_tvtalk($ym);
	if ($tvtalk_cnt) {
		$retval['list'][$idx]['pid'] = 'tvtalk';
		$retval['list'][$idx]['cnt'] = $tvtalk_cnt;
		$retval['total'] += $tvtalk_cnt;
		$idx++;
	}

	// 캐시카우
	$cashcow_cnt = get_pro_mem_cashcow($ym);
	if ($cashcow_cnt) {
		$retval['list'][$idx]['pid'] = 'cashcow';
		$retval['list'][$idx]['cnt'] = $cashcow_cnt;
		$retval['total'] += $cashcow_cnt;
		$idx++;
	}

	// 투믹스
	/*
	$toomics_cnt = get_pro_mem_toomics($ym);
	if ($toomics_cnt) {
		$retval['list'][$idx]['pid'] = 'toomics';
		$retval['list'][$idx]['cnt'] = $toomics_cnt;
		$retval['total'] += $toomics_cnt;
		$idx++;
	}
	*/

	return $retval;
}

function get_Mpro_mem($ym) {

	$sql = "SELECT pid,COUNT(mb_no) cnt_mb_no FROM g5_member
			WHERE pid<>''
			  AND mb_level=1 AND (member_type='1' OR member_type='2')
			  AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			GROUP BY pid
			ORDER BY pid";
	$res = sql_query($sql);
	$idx = 0;
	while ($row = sql_fetch_array($res)) {
		$retval['list'][$idx]['pid'] = $row['pid'];
		$retval['list'][$idx]['cnt'] = $row['cnt_mb_no'];
		$retval['total'] += $retval['list'][$idx]['cnt'];
		$idx++;
	}
	return $retval;

}

function get_pro_mem_gmnc($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND pid='gmnc'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_itembay($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND itembay_userid<>'' AND itembay_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_oligo($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND oligo_userid<>'' AND oligo_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_finnq($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND finnq_userid<>'' AND finnq_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_wowstar($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND wowstar_userid<>'' AND wowstar_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_chosun($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND chosun_userid<>'' AND chosun_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_r114($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND r114_userid<>'' AND r114_rdate=mb_datetime";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_tvtalk($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND pid='TvTalk'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_cashcow($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND pid='cashcow'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_pro_mem_toomics($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND pid='toomics'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}






function get_pro_mem2($ym) {
	$sql = "SELECT pid, syndi_id, count(mb_no) cnt
			  FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND (pid<>'' OR syndi_id<>'')
			 GROUP BY pid,syndi_id";
	//echo "<br/>$sql";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		if ($i>30) die("for loof error"); // safe loop

		$row = sql_fetch_array($res);

		$retval['list'][$i]['pid'] = $row['pid']?$row['pid']:$row['syndi_id'];
		$retval['list'][$i]['cnt'] = $row['cnt'];
		$retval['total']  += $row['cnt'];
	}

	return $retval;
}



function get_rec_mem($ym) {
	$idx = 0 ;

	// 동아제테크핀테크쇼
	$donga_cnt = get_evntout_mem_donga($ym);
	if ($donga_cnt) {
		$retval['list'][$idx]['pid'] = '동아재테크핀테크쇼';
		$retval['list'][$idx]['cnt'] = $donga_cnt;
		$retval['total'] += $donga_cnt;
		$idx++;
	}

	// 서울머니쇼
	$seoul_cnt = get_evntout_mem_seoul($ym);
	if ($seoul_cnt) {
		$retval['list'][$idx]['pid'] = '서울머니쇼';
		$retval['list'][$idx]['cnt'] = $seoul_cnt;
		$retval['total'] += $seoul_cnt;
		$idx++;
	}

	return $retval;
}


function get_evntout_mem_donga($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND rec_mb_id='donga_expo'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_evntout_mem_seoul($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND rec_mb_id='seoul_money_show'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}



function get_rec_mem2($ym) {
	$idx = 0 ;

	// 천억돌파 이벤트
	$f1000_cnt = get_evntin_mem_f1000($ym);
	if ($f1000_cnt) {
		$retval['list'][$idx]['pid'] = '천억돌파 이벤트';
		$retval['list'][$idx]['cnt'] = $f1000_cnt;
		$retval['total'] += $f1000_cnt;
		$idx++;
	}

	// 럭키박스 이벤트
	$f10002_cnt = get_evntin_mem_f10002($ym);
	if ($f10002_cnt) {
		$retval['list'][$idx]['pid'] = '럭키박스 이벤트';
		$retval['list'][$idx]['cnt'] = $f10002_cnt;
		$retval['total'] += $f10002_cnt;
		$idx++;
	}

	return $retval;
}

function get_evntin_mem_f1000($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND event_id='100B'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_evntin_mem_f10002($ym) {
	$sql = "SELECT count(mb_no) cnt FROM g5_member
			 WHERE mb_level = '1' AND (member_type='1' OR member_type='2')
			   AND mb_datetime>='$ym-01 00:00:00' AND mb_datetime<='$ym-31 23:59:59'
			   AND member_group='F'
			   AND event_id='100BEVENT2'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_first_inv2($ym) {
	$sql = "SELECT COUNT(*) cnt FROM
				(SELECT A.mb_no, A.member_type,
					(SELECT B.insert_date  FROM cf_product_invest_detail B WHERE B.member_idx=A.mb_no  ORDER BY B.idx LIMIT 1) first_inv
					FROM g5_member A
				) t1
			WHERE member_type='1' AND  SUBSTRING(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['P'] = $row['cnt'];

	$sql = "SELECT COUNT(*) cnt FROM
				(SELECT A.mb_no, A.member_type,
					(SELECT B.insert_date  FROM cf_product_invest_detail B WHERE B.member_idx=A.mb_no  ORDER BY B.idx LIMIT 1) first_inv
					FROM g5_member A
				) t1
			WHERE member_type='2' AND  SUBSTRING(first_inv,1,7)='$ym'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['C'] = $row['cnt'];

	return $retval;
}

function get_first_inv3($ym) {
	$sql = "select member_type, count(*) cnt from view_first_invest where substring(first_inv,1,7)='$ym' group by member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt; $i++) {
		if ($i>10) die("safe die for loof");
		$row = sql_fetch_array($res);

		if ($row['member_type']=="1") $retval['P']=$row['cnt'];
		else if ($row['member_type']=="2") $retval['C']=$row['cnt'];
	}

	return $retval;
}

function get_first_inv4($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and (gubun='P' or gubun='C') ";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt; $i++) {
		if ($i>10) die("safe die for loof");
		$row = sql_fetch_array($res);

		if ($row['gubun']=="P") $retval['P']=$row['cnt'];
		else if ($row['gubun']=="C") $retval['C']=$row['cnt'];
	}

	return $retval;
}

function get_first_inv($ym) {

	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.member_type='1'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'"; */
	$sql = "SELECT count(A.idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7)='$ym'
			   AND B.member_type='1'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['P'] = $row['cnt'];

	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.member_type='2'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";  */
	$sql = "SELECT count(A.idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7)='$ym'
			   AND B.member_type='2'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$retval['C'] = $row['cnt'];

	return $retval;
}

function get_first_inv_pro_jip($ym) {
	$idx = 0 ;
	// 핀크
	$finnq_cnt = get_first_inv_finnq($ym);
	if ($finnq_cnt) {
		$retval['list'][$idx]['pid'] = 'finnq';
		$retval['list'][$idx]['cnt'] = $finnq_cnt;
		$retval['total'] += $finnq_cnt;
		$idx++;
	}

	// 부동산114
	$r114_cnt = get_first_inv_r114($ym);
	if ($r114_cnt) {
		$retval['list'][$idx]['pid'] = '부동산114';
		$retval['list'][$idx]['cnt'] = $r114_cnt;
		$retval['total'] += $r114_cnt;
		$idx++;
	}

	// 티비톡
	$tvtalk_cnt = get_first_inv_tvtalk($ym);
	if ($tvtalk_cnt) {
		$retval['list'][$idx]['pid'] = 'tvtalk';
		$retval['list'][$idx]['cnt'] = $tvtalk_cnt;
		$retval['total'] += $tvtalk_cnt;
		$idx++;
	}

	// 캐시카우
	$cashcow_cnt = get_first_inv_cashcow($ym);
	if ($cashcow_cnt) {
		$retval['list'][$idx]['pid'] = 'cashcow';
		$retval['list'][$idx]['cnt'] = $cashcow_cnt;
		$retval['total'] += $cashcow_cnt;
		$idx++;
	}

	// 투믹스
	$toomics_cnt = get_first_inv_toomics($ym);
	if ($toomics_cnt) {
		$retval['list'][$idx]['pid'] = 'toomics';
		$retval['list'][$idx]['cnt'] = $toomics_cnt;
		$retval['total'] += $toomics_cnt;
		$idx++;
	}

	return $retval;
}

function get_first_inv_pro($ym) {
	$idx = 0 ;
	// 핀크
	//$finnq_cnt = get_first_inv_finnq($ym);
	//$finnq_cnt = get_first_inv_finnq2($ym);
	$finnq_cnt = get_first_inv_finnq3($ym);
	if ($finnq_cnt) {
		$retval['list'][$idx]['pid'] = 'finnq';
		$retval['list'][$idx]['cnt'] = $finnq_cnt;
		$retval['total'] += $finnq_cnt;
		$idx++;
	}

	// 부동산114
	//$r114_cnt = get_first_inv_r114($ym);
	//$r114_cnt = get_first_inv_r1142($ym);
	$r114_cnt = get_first_inv_r1143($ym);
	if ($r114_cnt) {
		$retval['list'][$idx]['pid'] = '부동산114';
		$retval['list'][$idx]['cnt'] = $r114_cnt;
		$retval['total'] += $r114_cnt;
		$idx++;
	}

	// 티비톡
	//$tvtalk_cnt = get_first_inv_tvtalk($ym);
	//$tvtalk_cnt = get_first_inv_tvtalk2($ym);
	$tvtalk_cnt = get_first_inv_tvtalk3($ym);
	if ($tvtalk_cnt) {
		$retval['list'][$idx]['pid'] = 'tvtalk';
		$retval['list'][$idx]['cnt'] = $tvtalk_cnt;
		$retval['total'] += $tvtalk_cnt;
		$idx++;
	}

	// 캐시카우
	//$cashcow_cnt = get_first_inv_cashcow($ym);
	//$cashcow_cnt = get_first_inv_cashcow2($ym);
	$cashcow_cnt = get_first_inv_cashcow3($ym);
	if ($cashcow_cnt) {
		$retval['list'][$idx]['pid'] = 'cashcow';
		$retval['list'][$idx]['cnt'] = $cashcow_cnt;
		$retval['total'] += $cashcow_cnt;
		$idx++;
	}

	// 투믹스
	//$toomics_cnt = get_first_inv_toomics($ym);
	//$toomics_cnt = get_first_inv_toomics2($ym);
	$toomics_cnt = get_first_inv_toomics3($ym);
	if ($toomics_cnt) {
		$retval['list'][$idx]['pid'] = 'toomics';
		$retval['list'][$idx]['cnt'] = $toomics_cnt;
		$retval['total'] += $toomics_cnt;
		$idx++;
	}

	return $retval;
}

function get_first_inv_finnq($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.finnq_userid<>''
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'"; */
	$sql = "SELECT COUNT(A.idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B ON (B.mb_no = A.member_idx)
		     WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.finnq_userid<>''";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}
function get_first_inv_finnq2($ym) {
	/*
	$sql = "SELECT count(A.mb_no) cnt
				FROM g5_member A
				LEFT JOIN (SELECT member_idx,min(insert_date) ins_date  FROM cf_product_invest_detail GROUP BY member_idx ) B ON(A.mb_no=B.member_idx)
			WHERE substring(B.ins_date,1,7)='$ym' and A.finnq_userid<>''";
	*/
	$sql = "select count(mb_no) cnt from view_first_invest where substring(first_inv,1,7)='$ym' and finnq_userid<>''";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}
function get_first_inv_finnq3($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='finnq'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}


function get_first_inv_r114($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.r114_userid<>''
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'"; */
	$sql = "SELECT COUNT(A.idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B ON (B.mb_no = A.member_idx)
		     WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.r114_userid<>''";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}
function get_first_inv_r1142($ym) {
	//$sql = "select count(*) cnt from view_first_invest A LEFT JOIN g5_member B ON(A.member_idx=B.mb_no) where substring(A.insert_date,1,7)='$ym' and B.r114_userid<>''";
	$sql = "SELECT count(A.mb_no) cnt
				FROM g5_member A
				LEFT JOIN (SELECT member_idx,min(insert_date) ins_date  FROM cf_product_invest_detail GROUP BY member_idx ) B ON(A.mb_no=B.member_idx)
			WHERE substring(B.ins_date,1,7)='$ym' and A.r114_userid<>''";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}
function get_first_inv_r1143($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='r114'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}

function get_first_inv_tvtalk($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='TvTalk'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";  */
	$sql = "SELECT COUNT(A.idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B ON (B.mb_no = A.member_idx)
		     WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.pid='TvTalk'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}

function get_first_inv_tvtalk2($ym) {
	$sql = "SELECT count(A.mb_no) cnt
				FROM g5_member A
				LEFT JOIN (SELECT member_idx,min(insert_date) ins_date  FROM cf_product_invest_detail GROUP BY member_idx ) B ON(A.mb_no=B.member_idx)
			WHERE substring(B.ins_date,1,7)='$ym' and A.tvtalk_userid<>''";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}
function get_first_inv_tvtalk3($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='tvtalk'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}


function get_first_inv_cashcow($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='cashcow'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";  */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.pid='cashcow'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}
function get_first_inv_cashcow2($ym) {
	$sql = "SELECT count(A.mb_no) cnt
				FROM g5_member A
				LEFT JOIN (SELECT member_idx,min(insert_date) ins_date  FROM cf_product_invest_detail GROUP BY member_idx ) B ON(A.mb_no=B.member_idx)
			WHERE substring(B.ins_date,1,7)='$ym' and A.pid='cashcow'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}
function get_first_inv_cashcow3($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='cashcow'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_first_inv_toomics($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.pid='toomics'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";   */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.pid='toomics'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}
function get_first_inv_toomics2($ym) {
	$sql = "SELECT count(A.mb_no) cnt
				FROM g5_member A
				LEFT JOIN (SELECT member_idx,min(insert_date) ins_date  FROM cf_product_invest_detail GROUP BY member_idx ) B ON(A.mb_no=B.member_idx)
			WHERE substring(B.ins_date,1,7)='$ym' and A.pid='toomics'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}
function get_first_inv_toomics3($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='toomics'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];

	return $cnt;
}

function get_first_inv_evnt_jip($ym) { // 투자회우 종합
	$idx = 0 ;

	// 동아제테크핀테크쇼
	$donga_cnt = get_evntout_inv_donga($ym);
	if ($donga_cnt) {
		$retval['list'][$idx]['pid'] = '동아재테크핀테크쇼';
		$retval['list'][$idx]['cnt'] = $donga_cnt;
		$retval['total'] += $donga_cnt;
		$idx++;
	}

	// 서울머니쇼
	$seoul_cnt = get_evntout_inv_seoul($ym);
	if ($seoul_cnt) {
		$retval['list'][$idx]['pid'] = '서울머니쇼';
		$retval['list'][$idx]['cnt'] = $seoul_cnt;
		$retval['total'] += $seoul_cnt;
		$idx++;
	}

	return $retval;
}

function get_first_inv_evnt($ym) { // 투자회우 종합
	$idx = 0 ;

	// 동아제테크핀테크쇼
	//$donga_cnt = get_evntout_inv_donga($ym);
	$donga_cnt = get_evntout_inv_donga2($ym);
	if ($donga_cnt) {
		$retval['list'][$idx]['pid'] = '동아재테크핀테크쇼';
		$retval['list'][$idx]['cnt'] = $donga_cnt;
		$retval['total'] += $donga_cnt;
		$idx++;
	}

	// 서울머니쇼
	//$seoul_cnt = get_evntout_inv_seoul($ym);
	$seoul_cnt = get_evntout_inv_seoul2($ym);
	if ($seoul_cnt) {
		$retval['list'][$idx]['pid'] = '서울머니쇼';
		$retval['list'][$idx]['cnt'] = $seoul_cnt;
		$retval['total'] += $seoul_cnt;
		$idx++;
	}

	return $retval;
}

function get_evntout_inv_donga($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.rec_mb_id='donga_expo'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";   */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.rec_mb_id='donga_expo'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}
function get_evntout_inv_donga2($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='donga'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}

function get_evntout_inv_seoul($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.rec_mb_id='seoul_money_show'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";  */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.rec_mb_id='seoul_money_show'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}
function get_evntout_inv_seoul2($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='seoul'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}


function get_first_inv_evnt2_jip($ym) { // 투자회우 종합
	$idx = 0 ;

	// 천억돌파 이벤트
	$f1000_cnt = get_evntin_inv_f1000($ym);
	if ($f1000_cnt) {
		$retval['list'][$idx]['pid'] = '천억돌파 이벤트';
		$retval['list'][$idx]['cnt'] = $f1000_cnt;
		$retval['total'] += $f1000_cnt;
		$idx++;
	}

	// 럭키박스 이벤트
	$f10002_cnt = get_evntin_inv_f10002($ym);
	if ($f10002_cnt) {
		$retval['list'][$idx]['pid'] = '럭키박스 이벤트';
		$retval['list'][$idx]['cnt'] = $f10002_cnt;
		$retval['total'] += $f10002_cnt;
		$idx++;
	}

	return $retval;
}
function get_first_inv_evnt2($ym) { // 투자회우 종합
	$idx = 0 ;

	// 천억돌파 이벤트
	//$f1000_cnt = get_evntin_inv_f1000($ym);
	$f1000_cnt = get_evntin_inv_100b($ym);
	if ($f1000_cnt) {
		$retval['list'][$idx]['pid'] = '천억돌파 이벤트';
		$retval['list'][$idx]['cnt'] = $f1000_cnt;
		$retval['total'] += $f1000_cnt;
		$idx++;
	}

	// 럭키박스 이벤트
	//$f10002_cnt = get_evntin_inv_f10002($ym);
	$f10002_cnt = get_evntin_inv_luckybox($ym);
	if ($f10002_cnt) {
		$retval['list'][$idx]['pid'] = '럭키박스 이벤트';
		$retval['list'][$idx]['cnt'] = $f10002_cnt;
		$retval['total'] += $f10002_cnt;
		$idx++;
	}

	return $retval;
}

function get_evntin_inv_f1000($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.event_id='100B'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'"; */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.event_id='100B' ";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];


	return $cnt;
}
function get_evntin_inv_100b($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='100b'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}

function get_evntin_inv_f10002($ym) {
	/*
	$sql = "SELECT member_idx, MIN(concat(insert_date,' ',insert_time)) first_inv, B.member_type
			  FROM cf_product_invest_detail
    	 LEFT JOIN g5_member B on (B.mb_no = cf_product_invest_detail.member_idx)
		     WHERE B.event_id='100BEVENT2'
		  GROUP BY member_idx
		    HAVING substring(first_inv,1,7)='$ym'";  */
	$sql = "SELECT count(idx) cnt
			  FROM cf_product_invest A
		 LEFT JOIN g5_member B on (B.mb_no = A.member_idx)
			 WHERE A.first_inv='Y'
			   AND substring(A.insert_date,1,7) = '$ym'
			   AND B.event_id='100BEVENT2' ";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row["cnt"];

	return $cnt;
}
function get_evntin_inv_luckybox($ym) {
	$sql = "select * from cf_jipyo_first_invest where ym='$ym' and gubun='luckybox'";
	$res = sql_query($sql);
	$row = sql_fetch_array($res);
	$cnt = $row['cnt'];
	return $cnt;
}

function get_out_mem($ym) {

	$sql = "SELECT member_type , count(*) cnt FROM g5_member where mb_level='200' AND substring(mb_leave_date,1,7)='$ym' GROUP BY member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		if ($row['member_type']=="1") { // 개인 탈퇴 회원
			$retval['P'] = $row['cnt'];
		} else if ($row['member_type']=="2") { // 법인 탈퇴 회원
			$retval['C'] = $row['cnt'];
		}
	}

	return $retval;
}

function get_rest_mem($ym) {
	$sql = "SELECT B.member_type , count(A.idx) cnt FROM g5_member_rest_log A LEFT JOIN g5_member B on(A.mb_no=B.mb_no) WHERE A.gubun='rest' AND substring(A.rdate,1,7)='$ym'
		     GROUP BY B.member_type";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		if ($row['member_type']=="1") { // 개인 탈퇴 회원
			$retval['P'] = $row['cnt'];
		} else if ($row['member_type']=="2") { // 법인 탈퇴 회원
			$retval['C'] = $row['cnt'];
		}
	}

	return $retval;
}

//jip_visit("2020-01");
function jip_visit($ym) {

	$del_sql = "delete from cf_jipyo_visit where ym='$ym'";
	sql_query($del_sql);

	$sql = "SELECT vi_device , count(distinct vi_ip) cnt FROM g5_visit WHERE substring(vi_date,1,7)='$ym' GROUP BY vi_device";
	$res = sql_query($sql);
	$cnt = $res->num_rows;
	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);
		if ($row['vi_device']=="mobile") { // 모바일
			$retval['M'] = $row['cnt'];
			$ins_sql = " insert into cf_jipyo_visit set ym='$ym', gubun='mobile' , cnt = '$retval[M]'";
		} else { // 기타
			$retval['P'] = $row['cnt'];
			$ins_sql = " insert into cf_jipyo_visit set ym='$ym', gubun='pc' , cnt = '$retval[P]'";
		}
		sql_query($ins_sql);
	}


}

function get_visit_mem($ym) {
	$sql = "SELECT vi_device , count(distinct vi_ip) cnt FROM g5_visit WHERE substring(vi_date,1,7)='$ym' GROUP BY vi_device";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		if ($row['vi_device']=="mobile") { // 모바일
			$retval['M'] = $row['cnt'];
		} else { // 기타
			$retval['P'] = $row['cnt'];
		}
	}

	return $retval;
}

function get_visit_mem_old($ym) {
	$sql = "SELECT * FROM cf_jipyo_visit WHERE ym='$ym' GROUP BY gubun";
	$res = sql_query($sql);
	$cnt = $res->num_rows;

	for ($i=0 ; $i<$cnt ; $i++) {
		$row = sql_fetch_array($res);

		if ($row['gubun']=="mobile") { // 모바일
			$retval['M'] = $row['cnt'];
		} else { // 기타
			$retval['P'] = $row['cnt'];
		}
	}

	return $retval;
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
?>
<!--
<?=$t1?> : 누적회원 소요시간 <br/>
<?=$t2?> : 신규회원 소요시간 <br/>
<?=$t3?> : 첫투자추출 소요시간 <br/>
<?=$t4?> : 탈퇴회원 소요시간 <br/>
<?=$t5?> : 휴면계정 소요시간 <br/>
<?=$t6?> : 방문자수 소요시간 <br/>
-->

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>