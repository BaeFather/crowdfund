<?
$sub_menu = "700200";
include_once('./_common.php');

while(list($k, $v)=each($_REQUEST)) { ${$k}=trim($v); }

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$prd_idx = $_REQUEST['idx'];

$PRDT = sql_fetch("SELECT title, recruit_amount, invest_return, recruit_period_start, loan_start_date, loan_end_date FROM cf_product WHERE idx='".$prd_idx."'");

$g5['title'] = '상품투자통계';
$g5['title'].= ' : '.$PRDT['title'];

if($type=='advence') {
	$g5['title'].= ' &nbsp; <span style="color:#3366FF">『사전투자』</span>';
	$where_plus = "AND A.is_advance_invest='Y'";
}
else if($type=='regular') {
	$g5['title'].= ' &nbsp; <span style="color:#3366FF">『정상투자』</span>';
	$where_plus = "AND A.is_advance_invest!='Y'";
}
else {
	$g5['title'].= ' &nbsp; <span style="color:#3366FF">『투자전체』</span>';
	$where_plus = "";
}

include_once('./admin.head.php');


$sql = "
	SELECT
		B.mb_id, B.mb_name, B.mb_co_name, B.member_type, B.member_investor_type,
		A.member_idx, A.amount, A.is_advance_invest, A.syndi_id AS flatform_id,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_count,
		(SELECT IFNULL(SUM(amount),0) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B  ON A.member_idx = B.mb_no
	WHERE (1)
		AND A.product_idx='".$prd_idx."'
		AND A.invest_state='Y'
		$where_plus
	ORDER BY
		A.amount DESC";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$TOTAL = array(
					'COUNT'      => 0,
					'AMOUNT'     => 0,
					'M1_COUNT'   => 0,
					'M1_AMOUNT'  => 0,
					'M11_COUNT'  => 0,
					'M11_AMOUNT' => 0,
					'M12_COUNT'  => 0,
					'M12_AMOUNT' => 0,
					'M13_COUNT'  => 0,
					'M13_AMOUNT' => 0,
					'M2_COUNT'   => 0,
					'M2_AMOUNT'  => 0,
					'M3_COUNT'   => 0,
					'M3_AMOUNT'  => 0,
					'M32_COUNT'   => 0,
					'M32_AMOUNT'  => 0,
					'M33_COUNT'   => 0,
					'M33_AMOUNT'  => 0,
					'M34_COUNT'   => 0,
					'M34_AMOUNT'  => 0
				);

$TOTAL_A = array(
						'COUNT'      => 0,
						'AMOUNT'     => 0,
						'M1_COUNT'   => 0,
						'M1_AMOUNT'  => 0,
						'M11_COUNT'  => 0,
						'M11_AMOUNT' => 0,
						'M12_COUNT'  => 0,
						'M12_AMOUNT' => 0,
						'M13_COUNT'  => 0,
						'M13_AMOUNT' => 0,
						'M2_COUNT'   => 0,
						'M2_AMOUNT'  => 0,
						'M3_COUNT'   => 0,
						'M3_AMOUNT'  => 0,
						'M32_COUNT'   => 0,
						'M32_AMOUNT'  => 0,
						'M33_COUNT'   => 0,
						'M33_AMOUNT'  => 0,
						'M34_COUNT'   => 0,
						'M34_AMOUNT'  => 0
					);

$TOTAL_B = array(
						'COUNT'      => 0,
						'AMOUNT'     => 0,
						'M1_COUNT'   => 0,
						'M1_AMOUNT'  => 0,
						'M11_COUNT'  => 0,
						'M11_AMOUNT' => 0,
						'M12_COUNT'  => 0,
						'M12_AMOUNT' => 0,
						'M13_COUNT'  => 0,
						'M13_AMOUNT' => 0,
						'M2_COUNT'   => 0,
						'M2_AMOUNT'  => 0,
						'M3_COUNT'   => 0,
						'M3_AMOUNT'  => 0,
						'M32_COUNT'   => 0,
						'M32_AMOUNT'  => 0,
						'M33_COUNT'   => 0,
						'M33_AMOUNT'  => 0,
						'M34_COUNT'   => 0,
						'M34_AMOUNT'  => 0
					);


for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	////////////////////////////////////
	// 전체 현황
	////////////////////////////////////
	$TOTAL['COUNT'] += 1;
	$TOTAL['AMOUNT'] += $LIST[$i]['amount'];

	if($LIST[$i]['member_type']=='2') {
		$TOTAL['M2_COUNT'] += 1;
		$TOTAL['M2_AMOUNT'] += $LIST[$i]['amount'];
	}
	else {
		$TOTAL['M1_COUNT'] += 1;
		$TOTAL['M1_AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_investor_type']=='2') {
			$TOTAL['M12_COUNT'] += 1;
			$TOTAL['M12_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['member_investor_type']=='3') {
			$TOTAL['M13_COUNT'] += 1;
			$TOTAL['M13_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL['M11_COUNT'] += 1;
			$TOTAL['M11_AMOUNT'] += $LIST[$i]['amount'];
		}
	}

	if($LIST[$i]['flatform_id']=='finnq') {
		$TOTAL['M3_COUNT'] += 1;
		$TOTAL['M3_AMOUNT'] += $LIST[$i]['amount'];
	}
	else if($LIST[$i]['flatform_id']=='hktvwowstar') {
		$TOTAL['M32_COUNT'] += 1;
		$TOTAL['M32_AMOUNT'] += $LIST[$i]['amount'];
	}
	else if($LIST[$i]['flatform_id']=='chosun') {
		$TOTAL['M33_COUNT'] += 1;
		$TOTAL['M33_AMOUNT'] += $LIST[$i]['amount'];
	}
	else if($LIST[$i]['flatform_id']=='oligo') {
		$TOTAL['M34_COUNT'] += 1;
		$TOTAL['M34_AMOUNT'] += $LIST[$i]['amount'];
	}


	////////////////////////////////////
	// 최초 투자자 현황 데이터
	////////////////////////////////////
	if($LIST[$i]['total_invest_count']==1) {

		$TOTAL_A['COUNT'] += 1;
		$TOTAL_A['AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_type']=='2') {
			$TOTAL_A['M2_COUNT'] += 1;
			$TOTAL_A['M2_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL_A['M1_COUNT'] += 1;
			$TOTAL_A['M1_AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_investor_type']=='2') {
				$TOTAL_A['M12_COUNT'] += 1;
				$TOTAL_A['M12_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['member_investor_type']=='3') {
				$TOTAL_A['M13_COUNT'] += 1;
				$TOTAL_A['M13_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_A['M11_COUNT'] += 1;
				$TOTAL_A['M11_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

		if($LIST[$i]['flatform_id']=='finnq') {
			$TOTAL_A['M3_COUNT'] += 1;
			$TOTAL_A['M3_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='hktvwowstar') {
			$TOTAL_A['M32_COUNT'] += 1;
			$TOTAL_A['M32_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='chosun') {
			$TOTAL_A['M33_COUNT'] += 1;
			$TOTAL_A['M33_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='oligo') {
			$TOTAL_A['M34_COUNT'] += 1;
			$TOTAL_A['M34_AMOUNT'] += $LIST[$i]['amount'];
		}

	}

	////////////////////////////////////
	// 기존 투자자 현황 데이터
	////////////////////////////////////
	else {

		$TOTAL_B['COUNT'] += 1;
		$TOTAL_B['AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_type']=='2') {
			$TOTAL_B['M2_COUNT'] += 1;
			$TOTAL_B['M2_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL_B['M1_COUNT'] += 1;
			$TOTAL_B['M1_AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_investor_type']=='2') {
				$TOTAL_B['M12_COUNT'] += 1;
				$TOTAL_B['M12_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['member_investor_type']=='3') {
				$TOTAL_B['M13_COUNT'] += 1;
				$TOTAL_B['M13_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_B['M11_COUNT'] += 1;
				$TOTAL_B['M11_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

		if($LIST[$i]['flatform_id']=='finnq') {
			$TOTAL_B['M3_COUNT'] += 1;
			$TOTAL_B['M3_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='hktvwowstar') {
			$TOTAL_B['M32_COUNT'] += 1;
			$TOTAL_B['M32_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='chosun') {
			$TOTAL_B['M33_COUNT'] += 1;
			$TOTAL_B['M33_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='oligo') {
			$TOTAL_B['M34_COUNT'] += 1;
			$TOTAL_B['M34_AMOUNT'] += $LIST[$i]['amount'];
		}

	}

}

?>

<div class="row" style="width:99.9%; margin:0 auto; padding:0 1%;">

	<ul style="display:inline-block;width:100%; padding:0; list-style:none;">
		<li style="float:left;margin-right:6px;"><button type="button" class="btn btn-<?=($type=='')?'gray':'default'?>" onClick="location.href='?idx=<?=$prd_idx?>'">투자전체</button></li>
		<li style="float:left;margin-right:6px;"><button type="button" class="btn btn-<?=($type=='advence')?'gray':'default'?>" onClick="location.href='?idx=<?=$prd_idx?>&type=advence'">사전투자</button></li>
		<li style="float:left;margin-right:6px;"><button type="button" class="btn btn-<?=($type=='regular')?'gray':'default'?>" onClick="location.href='?idx=<?=$prd_idx?>&type=regular'">정상투자</button></li>
		<li style="float:left;"><button type="button" class="btn btn-success" onClick="location.replace('product_investment_status_download.php?idx=<?=$prd_idx?>&type=regular');">보고자료엑셀 다운로드</button></li>
	</ul>


	<div style="float:left;width:33%; margin-right:0.5%;">
		<h3>전체 현황</h3>
		<table class="table table-bordered">
			<colgroup>
				<col width="34%">
				<col width="33%">
				<col width="33%">
			</colgroup>
			<tr>
				<td bgcolor="#F8F8EF">전체</td>
				<td align="right"><?=number_format($TOTAL['COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL['AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">법인투자자</td>
				<td align="right"><?=number_format($TOTAL['M2_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL['M2_AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">개인투자자</td>
				<td align="right"><?=number_format($TOTAL['M1_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL['M1_AMOUNT'])?>원</td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－일반투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M11_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M11_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－소득적격투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M12_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M12_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－전문투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M13_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M13_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">핀크</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M3_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M3_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">와우스타(한경TV)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M32_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M32_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">땅집고(조선일보)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M33_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M33_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">올리고</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_AMOUNT'])?>원</span></td>
			</tr>
		</table>
	</div>

	<div style="float:left;width:33%; margin-right:0.5%;">
		<h3>최초투자자</h3>
		<table class="table table-bordered">
			<colgroup>
				<col width="34%">
				<col width="33%">
				<col width="33%">
			</colgroup>
			<tr>
				<td bgcolor="#F8F8EF">전체</td>
				<td align="right"><?=number_format($TOTAL_A['COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_A['AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">법인투자자</td>
				<td align="right"><?=number_format($TOTAL_A['M2_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_A['M2_AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">개인투자자</td>
				<td align="right"><?=number_format($TOTAL_A['M1_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_A['M1_AMOUNT'])?>원</td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－일반투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M11_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M11_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－소득적격투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M12_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M12_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－전문투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M13_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M13_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">핀크</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M3_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M3_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">와우스타(한경TV)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M32_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M32_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">땅집고(조선일보)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M33_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_A['M33_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">올리고</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_AMOUNT'])?>원</span></td>
			</tr>
		</table>
	</div>

	<div style="float:left;width:33%;">
		<h3>기존투자자</h3>
		<table class="table table-bordered">
			<colgroup>
				<col width="34%">
				<col width="33%">
				<col width="33%">
			</colgroup>
			<tr>
				<td bgcolor="#F8F8EF">전체</td>
				<td align="right"><?=number_format($TOTAL_B['COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_B['AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">법인투자자</td>
				<td align="right"><?=number_format($TOTAL_B['M2_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_B['M2_AMOUNT'])?>원</td>
			</tr>
			<tr>
				<td bgcolor="#F8F8EF">개인투자자</td>
				<td align="right"><?=number_format($TOTAL_B['M1_COUNT'])?>명</td>
				<td align="right"><?=number_format($TOTAL_B['M1_AMOUNT'])?>원</td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－일반투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M11_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M11_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－소득적격투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M12_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M12_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#FAFAFA">
				<td><span style="color:#999">－전문투자자</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M13_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M13_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">핀크</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M3_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M3_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">와우스타(한경TV)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M32_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M32_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">땅집고(조선일보)</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M33_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL_B['M33_AMOUNT'])?>원</span></td>
			</tr>
			<tr bgcolor="#EEE">
				<td><span style="color:#999">올리고</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_COUNT'])?>명</span></td>
				<td align="right"><span style="color:#999"><?=number_format($TOTAL['M34_AMOUNT'])?>원</span></td>
			</tr>
		</table>
	</div>

	<div style="margin:0;">
		<h3>투자자 리스트 <span style="font-size:13px">(투자금액 내림차순)</span></h3>
		<table class="table table-striped table-bordered table-hover">
			<colgroup>
				<col style="%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
				<col style="width:11%">
			</colgroup>
			<tr align="center" style="background-color:#F8F8EF">
				<td rowspan="2">NO</td>
				<td colspan="3">회원정보</td>
				<td rowspan="2">투자금액</td>
				<td rowspan="2">투자구분</td>
				<td rowspan="2">투자플랫폼</td>
				<td colspan="2">누적투자</td>
			</tr>
			<tr align="center" style="background-color:#F8F8EF">
				<td>아이디</td>
				<td>구분</td>
				<td>업체명/성명</td>

				<td>참여</td>
				<td>금액</td>
			</tr>

<?
if($rows) {
	for($i=0,$j=1; $i<$rows; $i++,$j++) {

		if($LIST[$i]['member_type']=='2') {
			$name = $LIST[$i]['mb_co_name'];
			$member_type = "<font color='#FF2222'>법인</font>";
		}
		else {
			$name = $LIST[$i]['mb_name'];
			if($LIST[$i]['member_investor_type']=='3')  $member_type = "<font color='#2222FF'>전문</font>";
			else if($LIST[$i]['member_investor_type']=='2') $member_type = "<font color='#2222FF'>소득적격</font>";
			else $member_type = "개인";
		}


		$invest_gbn = ($LIST[$i]['is_advance_invest']=='Y') ? '사전투자' : '본투자';

		$flatform = ($LIST[$i]['flatform_id']) ? $CONF['SYNDICATOR'][$LIST[$i]['flatform_id']]['name'] : '';

?>
			<tr>
				<td align="center"><?=$j?></td>
				<td align="center"><?=$LIST[$i]['mb_id']?></td>
				<td align="center"><?=$member_type?></td>
				<td align="center"><?=$name?></td>
				<td align="right">
					<a href="javascript:;" onClick="balance_check(<?=$LIST[$i]['member_idx']?>)" style="color:blue">
					<?=number_format($LIST[$i]['amount'])?>원</a>
				</td>
				<td align="center"><?=$invest_gbn?></td>
				<td align="center" style="color:#FF2222"><?=$flatform?></td>
				<td align="right"><?=number_format($LIST[$i]['total_invest_count'])?>건</td>
				<td align="right"><?=number_format($LIST[$i]['total_invest_amount'])?>원</td>
			</tr>
<?
	}
}
else {
	echo "<tr><td colspan='20' align='center'>등록된 데이터가 없습니다.</td></tr>\n";
}
?>
		</table>
	</div>

</div>

<?

include_once ('./admin.tail.php');
?>