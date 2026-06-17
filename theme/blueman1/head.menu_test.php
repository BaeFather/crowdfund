<?
include_once("_common.php");
?>
	<nav>
		<ul id="main-menu">
<?
$gnb_menus = array();

$sql = "SELECT * FROM {$g5['menu_table']}
        WHERE me_use = '1'
        AND length(me_code) = '2'
        ORDER BY me_order, me_id ";
$result = sql_query($sql, false);
$gnb_zindex = 999; // gnb_1dli z-index 값 설정용

for ($i=0; $row=sql_fetch_array($result); $i++) {
?>
			<li onClick="window.<?=$row['me_target']?>.location.href='<?=$row['me_link']?>'" style="cursor:pointer;">
				<?=$row['me_name']?>
<?
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

		echo '						<div class="smenu">' . PHP_EOL;

		$sql2 = "SELECT * FROM {$g5['menu_table']}
		         WHERE me_use = '1'
		         AND length(me_code) = '4'
		         AND substring(me_code, 1, 2) = '{$row['me_code']}'
		         ORDER BY me_order, me_id ";
		$result2 = sql_query($sql2);

		for ($k=0; $row2=sql_fetch_array($result2); $k++) {
			echo '							<p><a href="'.$row2['me_link'].'" target="_'.$row2['me_target'].'">'.$row2['me_name'].'</a></p>' . PHP_EOL;
		}

		echo '					</div>' . PHP_EOL;

	}
?>
			</li>
<?
}
?>
		</ul>
		<div id="member">
<?
if ($is_member) {
	//로그인후
	if ($is_admin) {
?>
			<div class="admin_logout" style="cursor:pointer">
				<a href="<?=G5_ADMIN_URL?>" target="_self"><?=$member["mb_name"]?></a>
				<div class="smenu">
					<p><a href="<?=G5_ADMIN_URL?>" target="_self">관리자툴</a></p>
					<p><a href="<?=G5_BBS_URL?>/logout.php" target="_self">로그아웃</a></p>
				</div>
			</div>
<?
	}
	else {
?>
			<div class="logout" style="cursor:pointer">
				<a href="#" target="_self"><?=$member["mb_name"]?> 님</a>
				<div class="smenu">
					<p><a href="<?=G5_BBS_URL?>/member_confirm.php?url=/mypage/mypage.php" target="_self">회원정보</a></p>
					<p><a href="<?=G5_URL?>/deposit/deposit.php" target="_self">투자내역</a></p>
					<p><a href="<?=G5_BBS_URL?>/logout.php" target="_self">로그아웃</a></p>
				</div>
			</div>
<?
	}
}
else {
?>
			<div class="login"><a href="<?=G5_BBS_URL?>/login.php">로그인</a></div>
			<div class="join"><a href="<?=G5_BBS_URL?>/register_choice.php">회원가입</a></div>
<?
}
?>
		</div>
	</nav>
