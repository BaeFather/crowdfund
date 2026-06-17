<?php
include_once('./_common.php');
$sub_menu = "300780";

auth_check($auth[$sub_menu], 'r');

$g5['title'] = "팝업관리";
include_once (G5_ADMIN_PATH.'/admin.head.php');

//전체삭제일경우
if($mode == 'delete_all'){
	for($i = 0; $i < sizeof($num); $i++){  //sizeof() 변수 크기
		$sql = "delete from $g5[site_popup_table] where no={$no[$num[$i]]}";
		sql_query($sql);
	}
}

//전체수정일경우
if($mode == 'modify_all'){

	for($i = 0; $i < sizeof($num); $i++){

		$sql="
			update  
				$g5[site_popup_table] 
			set
				check_use='{$use[$num[$i]]}'
			where 
				no={$no[$num[$i]]}
			";

		sql_query($sql);
	}
}


$colspan=10;

$sql="select * from $g5[site_popup_table] where 1 order by no desc";
$result= sql_query($sql);
$total_count = sql_num_rows($result);

$rows = $config[cf_page_rows];
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if (!$page) $page = 1;                      // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $rows;         // 시작 열을 구함

$sql="select * from $g5[site_popup_table] where 1 order by no desc limit $from_record, $rows";
$result = sql_query($sql);

$listall = '<a href="'.$_SERVER['PHP_SELF'].'" class="ov_listall">전체목록</a>';

?>

<div class="local_ov01 local_ov">
    <?php echo $listall ?>
    생성된 팝업수 <?php echo number_format($total_count) ?>개
</div>

<div class="btn_add01 btn_add">
    <a href="./site_popup_form.php" id="bo_add">팝업창 추가</a>
</div>

<p style="padding: 0 20px; font-size: 12px; color: red;">※ 팝업 사이즈는 <b>520x570(기본값)</b> 사이즈로 맞춰서 업로드하시길 바랍니다.</p>

<form name="flist" method="post" action='<?=$PHP_SELF?>'>
	<input type="hidden" name="page" value="<?=$page?>">
	<input type="hidden" name="mode" value="">

	<div class="tbl_head01 tbl_wrap">
		<table width="100%" cellpadding="0" cellspacing="1">
			<colgroup width="40px">
			<colgroup width="">
			<colgroup width="80">
			<colgroup width="80">
			<colgroup width="120">
			<colgroup width="120">
			<colgroup width="70">
			<colgroup width="70">
			<colgroup width="100">

			<thead>
			<tr>
				<th scope="col">&nbsp;</th>
				<th scope="col">제목</th>
				<th scope="col">타입</th>
				<th scope="col">레벨적용</th>
				<th scope="col">시작날짜</th>
				<th scope="col">종료날짜</th>
				<th scope="col">기간</th>
				<th scope="col">노출여부</th>
				<th scope="col">관리</th>
			</tr>
			</thead>

			<?php
			for($i = 0; $row = sql_fetch_array($result); $i++){

				$title = stripslashes($row[title]);
				$width = $row[width];
				$height = $row[height];

				$condition = "$row[level]";
				if($condition == '0'){
					$con_text = '전체레벨';
				} else {
					$con_text = $condition."레벨";
				}

				$check_use = $row[check_use];
				
				if(!$check_use) $check_use = 'N';

				$gigan = $row[gigan]."일";

			?>
			<tr class='list$list col1 ht center'>
				<td style="padding: 10px 15px;"><input type="checkbox" name="num[]" value="<?=$i?>"></td>
				
				<input type="hidden" name='no[]' value='<?=$row[no]?>'>
				
				<td align="left">&nbsp;<?=$title?></td>
				<td align="center"><?=$row[type]?></td>
				<!--td align="center"><?=$width?>/<?=$height?></td-->
				<td align="center"><?=$con_text?></td>
				<td align="center"><?=$row[reg_date]?></td>
				<td align="center"><?=$row[end_date]?></td>
				<td align="center"><?=$gigan?></td>
				<td align="center">
				<?if($check_use=='Y') echo "노출";
				  else echo "미노출";
				?>
				</td>
				<td align=center>
				<a href="./site_popup_form.php?mode=modify&no=<?=$row[no]?>">수정</a>
				</td>
			</tr>
			<tr>
				<td colspan='<?=$colspan?>' class='line2'></td>
			</tr>
		<? } ?>
		<? if(!$total_count) { ?>
			<tr>
				<td colspan='<?=$colspan?>' align="center" height="100" bgcolor="#fff">팝업이 생성되지 않았습니다.</td>
			</tr>
			<tr>
				<td colspan='<?=$colspan?>' class='line2'></td>
			</tr>
		<? } ?>
		</table>
	</div>

	<div class="btn_list01 btn_list">
		<input type='button' value='선택'  onclick="multiCheck(1);" class="btn1">
		<input type='button' value='취소'  onclick="multiCheck(2);" class="btn1">
		<input type='button' value='반전'  onclick="multiCheck(3);" class="btn1">
		&nbsp;&nbsp;
		<input type='button' value="선택 삭제" onclick="actionQue('delete_all')" class='btn1'>
	</div>
	<? 
	  $pagelist = get_paging($config[cf_write_pages], $page, $total_page, "?page=");
	?>
</form>

<? echo get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, "{$_SERVER['SCRIPT_NAME']}?$qstr&amp;page="); ?>



<script language="javascript">
function multiCheck(n) {
	var i;
	var l = document.getElementsByName('num[]');

	for (i = 0; i < l.length; i++)
	{
		if (n == 1) l[i].checked = true;
		if (n == 2) l[i].checked = false;
		if (n == 3) l[i].checked = !l[i].checked;
	}
}
function actionQue(val) {
	var f = document.flist;
	var i;
	var j = 0;
	var l = document.getElementsByName('num[]');

	for (i = 0; i < l.length; i++) {
		if (l[i].checked == true) j++;
	}
	if (j == 0) {
		alert('선택된 데이터가 없습니다.');
		return false;
	}

	if(val == 'delete_all') {
		if(confirm('삭제 후 복구가 불가능합니다. 정말로 실행하시겠습니까?')) {
			f.mode.value = val;
			f.submit();
		}
	} else if(val == 'modify_all') {
		if(confirm('수정 후 복구가 불가능합니다. 정말로 실행하시겠습니까?')) {
			f.mode.value = val;
			f.submit();
		}
	}
	return false;
}

</script>

<?php
include_once("./admin.tail.php");
?>
