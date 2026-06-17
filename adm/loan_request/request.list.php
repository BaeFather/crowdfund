<?

$sql_search = "";
$sql_search.= " AND blind=''";
if($type)     $sql_search.= " AND type='$type'";
if($relation) $sql_search.= " AND relation='$relation'";
if($purpose)  $sql_search.= " AND purpose='$relation'";
if($period)   $sql_search.= " AND period='$period'";
if($tenant)   $sql_search.= " AND tenant='1'";

if($start_date && $end_date) {
	$sql_search.= " AND LEFT(regdate,10) BETWEEN '$start_date' AND '$end_date'";
}
else {
	if($start_date) $sql_search.= " AND LEFT(regdate,10)>='$start_date' ";
	if($end_date)  $sql_search .= " AND LEFT(regdate,10)<='$end_date' ";
}

if($key_search && $keyword) {
	if($key_search=='name') {
		$sql_search .= " AND (name LIKE '%$keyword%' OR co_name LIKE '%$keyword%')";
	}
	else {
		$sql_search .= " AND $key_search LIKE '%$keyword%' ";
	}
}

$ST = $_REQUEST['ST'];
$st_count = count($ST);
if($st_count) {
	if($st_count==1) {
		$sql_search.= " AND judge_state='".$ST[0]."'";
	}
	else if($st_count > 1) {
		$sql_search.= " AND judge_state IN(";
		for($i=0,$j=1;$i<$st_count;$i++,$j++) {
			$sql_search.= "'".$ST[$i]."'";
			$sql_search.= ($j<$st_count) ? ",":"";
		}
		$sql_search.= ")";
	}
}

$sql_search .= " AND ip<>'154.16.51.115' ";  // 전승찬 봇 접근으로 임시처리 2021-04-23

$sql = "SELECT COUNT(idx) AS cnt FROM cf_apat_loan_request WHERE 1=1 $sql_search";
$row = sql_fetch($sql);
$total_count = $row['cnt'];

$sql = "SELECT COUNT(idx) AS cnt FROM cf_apat_loan_request WHERE 1=1 $sql_search AND type='1'";
$row1 = sql_fetch($sql);
$type1_count = $row1['cnt'];

$sql = "SELECT COUNT(idx) AS cnt FROM cf_apat_loan_request WHERE 1=1 $sql_search AND type='2'";
$row2 = sql_fetch($sql);
$type2_count = $row2['cnt'];


$rows = 20;
$total_page  = ceil($total_count / $rows);
if($page < 1) $page = 1;
$from_record = ($page - 1) * $rows;


$sql = "
	SELECT
		*
	FROM
		cf_apat_loan_request
	WHERE 1=1
		$sql_search
	ORDER BY
		idx DESC
	LIMIT
		$from_record, $rows";
//echo "<pre>".$sql."</pre>";
$result = sql_query($sql);
$rcount = $result->num_rows;

$num = $total_count - $from_record;

?>

<style>
.new_mark { display:inline-block; font-size:8pt; padding:0 2px; line-height:12px;color:#fff; background:red; border-radius:3px; }
</style>

	<!-- 검색영역 START -->
	<div style="display:inline-block;line-height:28px;margin-bottom:8px;">
		<form id="frmSearch" method="get" class="form-horizontal">
		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<label><input type="radio" name="type" value="" <?=($type=='')?'checked':''?>> 전체</label> &nbsp;
				<label><input type="radio" name="type" value="1" <?=($type=='1')?'checked':''?>> 아파트담보 대출 신청</label> &nbsp;
				<label><input type="radio" name="type" value="2" <?=($type=='2')?'checked':''?>> 취급법인 유동화 신청</label>
			</li><br />
			<li>
				<select name="relation" id="relation" class="form-control input-sm" style="width:150px">
					<option value="">:: 소유주와의 관계 ::</option>
					<option value="1" <?=($relation=='1')?'selected':''?>>본인</option>
					<option value="2" <?=($relation=='2')?'selected':''?>>가족</option>
					<option value="3" <?=($relation=='3')?'selected':''?>>중개인</option>
				</select>
			</li>
			<li>
				<select name="purpose" id="purpose" class="form-control input-sm" style="width:150px">
					<option value="">:: 대출목적 ::</option>
					<option value="1" <?=($purpose=='1')?'selected':''?>>기대출상환</option>
					<option value="2" <?=($purpose=='2')?'selected':''?>>기대출상환 및 추가대출</option>
					<option value="3" <?=($purpose=='3')?'selected':''?>>선순위대출</option>
					<option value="4" <?=($purpose=='4')?'selected':''?>>사업자금</option>
					<option value="5" <?=($purpose=='5')?'selected':''?>>전세퇴거자금</option>
					<option value="6" <?=($purpose=='6')?'selected':''?>>기타</option>
				</select>
			</li>
			<li>
				<select name="period" id="period" class="form-control input-sm" style="width:150px">
					<option value="">:: 대출기간 ::</option>
					<option value="6" <?=($period=='6')?'selected':''?>>6개월</option>
					<option value="9" <?=($period=='9')?'selected':''?>>9개월</option>
					<option value="12" <?=($period=='12')?'selected':''?>>12개월</option>
					<option value="12+" <?=($period=='12+')?'selected':''?>>12개월 초과</option>
				</select>
			</li>
			<li>
				<label class="checkbox-inline"><input type="checkbox" name="tenant" id="tenant" value="1" <?=($tenant=='1')?'checked':''?>>세입자 있음</label>
			</li>
			<li></li>
			<li>등록일</li>
			<li><input type="text" name="start_date" value="<?=$start_date?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
			<li>~</li>
			<li><input type="text" name="end_date" value="<?=$end_date?>" class="form-control input-sm datepicker" style="width:120px" readonly></li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>심사현황:</li>
<? for($n=1; $n<=count($JSTATE); $n++) { ?>
			<li><label class="checkbox-inline"><input type="checkbox" name="ST[]" value="<?=$n?>" <?=( @in_array($n, $ST) )?'checked':''?>><?=$JSTATE[$n]?></label></li>
<? } ?>
			<li>
				<select id="judge_name" name="judge_name" class="form-control input-sm" style="width:150px">
					<option value="">:: 물건담당자 ::</option>
<?

$jres = sql_query("
	SELECT
		mb_name
	FROM
		g5_member
	WHERE (1)
		AND mb_level='9'
		AND mb_name LIKE '%상품관리-%'
	ORDER BY
		mb_name, mb_no ASC");
while($JROW = sql_fetch_array($jres)) {
	$selected = ($judge_name == $JROW['mb_name']) ? 'selected' : '';
	echo "					<option value='".$JROW['mb_name']."' $selected>".$JROW['mb_name']."</option>\n";
}
sql_free_result($jres);
?>
				</select>
			</li>
		</ul>

		<ul class="col-sm-10 list-inline" style="width:100%;padding-left:0;margin-bottom:5px">
			<li>
				<select id="key_search" name="key_search" class="form-control input-sm" style="width:150px">
					<option value="">:: 필드선택 ::</option>
					<option value="name" <? if($key_search == 'name'){echo 'selected';} ?>>성명.담당자명.법인명</option>
					<option value="hp" <? if($key_search == 'hp'){echo 'selected';} ?>>연락처</option>
					<option value="email" <? if($key_search == 'email'){echo 'selected';} ?>>이메일</option>
					<option value="loc" <? if($key_search == 'loc'){echo 'selected';} ?>>물건소재지</option>
					<option value="content" <? if($key_search == 'content'){echo 'selected';} ?>>내용</option>
					<option value="pid" <? if($key_search == 'pid'){echo 'selected';} ?>>파트너</option>
				</select>
			</li>
			<li><input type="text" id="keyword" name="keyword" value="<?=$keyword?>" class="form-control input-sm" style="width:250px"></li>
			<li><button type="submit" class="btn btn-sm btn-warning" onClick="form_change();">검색</button></li>
		</ul>
		</form>
	</div>
	<!-- 검색영역 E N D -->


	<!-- 리스트 START -->

	<div style="float:right; display:inline-block; font-size:12px;line-height:20px;width:100%;">
		<span style="float:left">▣ 등록 : <?=number_format($total_count);?>건</span>
<? if($type=='') { ?>
		<span style="float:left;margin-left:20px;">▣ 아파트담보대출신청 : <?=number_format($type1_count);?>건</span>
		<span style="float:left;margin-left:20px;">▣ 취급법인유동화신청 : <?=number_format($type2_count);?>건</span>
<? } ?>
		<span style="float:right"><?=$page?> / <?=$total_page?> Page<span>
	</div>
	<table class="table table-striped table-bordered table-hover" style="padding-top:0; font-size:12px;">
		<caption style="padding:0"><?=$g5['title']?> 목록</caption>
		<thead>
		<tr>
			<th scope="col" style="text-align:center;width:60px">NO.</th>
			<th scope="col" style="text-align:center;">버전</th>
			<th scope="col" style="text-align:center;">구분</th>
			<th scope="col" style="text-align:center;">파트너</th>
			<th scope="col" style="text-align:center;">성명.법인명</th>
			<th scope="col" style="text-align:center;">연락처</th>
			<th scope="col" style="text-align:center;">이메일</th>
			<th scope="col" style="text-align:center;">물건소재지</th>
			<th scope="col" style="text-align:center;">희망대출금</th>
			<th scope="col" style="text-align:center;">대출기간</th>
			<th scope="col" style="text-align:center;">대출목적</th>
			<th scope="col" style="text-align:center;">상담가능시간</th>
			<th scope="col" style="text-align:center;">열람수</th>
			<th scope="col" style="text-align:center;">등록일시</th>
			<th scope="col" style="text-align:center;">상태</th>
			<th scope="col" style="text-align:center;">물건담당자</th>
			<th scope="col" style="text-align:center;">내용</th>
		</tr>
		</thead>
		<tbody>
<?
if($num > 0) {
	for ($i=0; $i<$rcount; $i++) {
		$row = sql_fetch_array($result);

		$row['name'] = htmlSpecialChars($row['name']);
		$row['loc']  = htmlSpecialChars($row['loc']);

		$new_mark = (time()-strtotime($row['regdate']) < 86400) ? '<span class="new_mark">new</span>' : '';

		if($row['pid']=='r114') {
			$print_pid = '부동산114';
		}
		else {
			$print_pid = htmlSpecialChars($row['pid']);
		}

		$print_type = $TYPE[$row['type']];
		$print_hp  = masterDecrypt($row['hp'], false);

		$print_loc = $print_wamt = $print_purpose = $print_period = $print_wtime = '';

		if($row['type']=='1') {
			$print_name    = $row['name'];
			$print_loc     = $row['loc'];
			$print_wamt    = price_cutting($row['wamt']).'원';
			$print_purpose = $PURPOSE[$row['purpose']];
			$print_period  = $PERIOD[$row['period']];
			$print_wtime   = $row['wtime'];
		}
		else {
			$print_name = $row['co_name'];
		}

		$print_view = ($row['view']==0) ? "<font color='#ccc'>0</font>" : number_format($row['view']);

?>
		<tr style="background:<?=($row['idx']==$idx)?'#FFDDDD':''?>">
			<td align="center"><?=$num?></td>
			<td align="center"><?=$row["skin"]?></td>
			<td align="center">
				<?=$new_mark?>
				<?=$print_type?>
			</td>
			<td align="center"><?=$print_pid?></td>
			<td align="center"><?=$print_name?></td>
			<td align="center"><?=$print_hp?></td>
			<td align="center"><?=$row['email']?></td>
			<td align="center"><?=$print_loc?></td>
			<td align="center"><?=$print_wamt?></td>
			<td align="center"><?=$print_period?></td>
			<td align="center"><?=$print_purpose?></td>
			<td align="center"><?=$print_wtime?></td>
			<td align="center"><?=$print_view?></td>
			<td align="center"><?=substr($row['regdate'],0,16)?></td>
			<td align="center"><?=$JSTATE[$row['judge_state']]?></td>
			<td align="center"><?=$row['judge_name']?></td>
			<td align="center">
				<button type="button" style="margin-top:2px;" onClick="location.href='request.php?<?=$qstr?>&idx=<?=$row['idx']?>&page=<?=$page?>&skin=<?php ECHO $row["skin"];?>'" class="btn btn-sm <?=($row['idx']==$idx)?'':'btn-default'?>">상세보기</button>
				<button type="button" style="margin-top:2px;" onClick="location.href='request.detail.php?mode=download&idx=<?=$row['idx']?>'" class="btn btn-sm btn-success">다운로드</button>
			</td>
		</tr>
<?
		$num--;
	}
}else {
?>

		<tr>
			<td colspan="20" align="center">검색된 데이터가 없습니다.</td>
		</tr>

<?
}
?>
	</table>
	<!-- 리스트 E N D -->

<?
echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, 'request.php?'.$qstr.'&amp;page=');
?>