<?php
	if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가
?>
<ul class="navi">
<?php
$gnb_menus = array();

$sql = "SELECT * FROM {$g5['menu_table']}
        WHERE me_use = '1'
        AND length(me_code) = '2'
        ORDER BY me_order, me_id ";
$result = sql_query($sql, false);
$gnb_zindex = 999; // gnb_1dli z-index 값 설정용

for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
	<li class="m" onClick="window.<?=$row['me_target']?>.location.href='<?=$row['me_link']?>';" style="cursor:pointer">
		<a href="<?=$row['me_link']?>" target="_<?=$row['me_target']?>" ><?=$row['me_name']?></a>
<?php
	$submenus = '';

	$sql_c = "SELECT COUNT(*) AS 'sub_cnt' FROM {$g5['menu_table']}
						WHERE me_use = '1'
						AND length(me_code) = '4'
						AND substring(me_code, 1, 2) = '{$row['me_code']}'
						ORDER BY me_order, me_id ";
	$row_c = sql_fetch($sql_c);
	$sub_m_cnt = $row_c['sub_cnt'];


	// 서브메뉴가 있다면
	if($sub_m_cnt > 0) {

		echo '<ul class="subMenu">';

		$sql2 = "SELECT * FROM {$g5['menu_table']}
		         WHERE me_use = '1'
		         AND length(me_code) = '4'
		         AND substring(me_code, 1, 2) = '{$row['me_code']}'
		         ORDER BY me_order, me_id ";
		$result2 = sql_query($sql2);

		for ($k=0; $row2=sql_fetch_array($result2); $k++) {
?>
			<li><a href="<?=$row2['me_link']?>" target="_<?=$row2['me_target']?>"><?=$row2['me_name']?></a></li>
<?php
		}
		echo '</ul>';
	}
?>
	</li>
<?php
}
?>

<?	if ($is_member) {	?>
	<!-- //로그인후 -->
	<li class="mem">
<?		if ($is_admin) {	?>
		<a href="<?=G5_ADMIN_URL?>">관리자</a>
		<ul class="subMenu">
			<li><a href="<?=G5_ADMIN_URL?>">관리자툴</a></li>
			<li><a href="<?=G5_BBS_URL?>/logout.php">로그아웃</a></li>
		</ul>
<?		} else {	?>
		<a href="#"><?=$member["mb_name"]?> 님</a>
		<ul class="subMenu">
			<li><a href="<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php">회원정보</a></li>
			<li><a href="<?=G5_URL?>/deposit/deposit.php">투자내역</a></li>
			<li><a href="<?=G5_BBS_URL?>/logout.php">로그아웃</a></li>
		</ul>
<?		} ?>
	</li>
<?	} else {	?>
	<li class="login"><a href="<?=G5_BBS_URL?>/login.php">로그인</a></li>
	<li class="join"><a href="<?=G5_BBS_URL?>/register_choice.php">회원가입</a></li>
<?	}	?>
</ul>
