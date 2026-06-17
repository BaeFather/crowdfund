<?

if (!defined('_GNUBOARD_')) exit;

if(OFFICE_CONNECT) {
	//ini_set('zlib.output_compression_level', 1);
	//if(!ob_start("ob_gzhandler")) { ob_start(); }
	//ob_start();
}

$begin_time = get_microtime();

include_once(G5_PATH.'/head.sub.php');

function print_menu1($key, $no)
{
	global $menu;
	$str = print_menu2($key, $no);
	return $str;
}

function print_menu2($key, $no)
{
	global $menu, $auth_menu, $is_admin, $auth, $g5, $subadmin_auth_arr;
	$str = "";
	$str .= "<ul class=\"gnb_2dul\">";
	for($i=1; $i<count($menu[$key]); $i++)
	{

		if ($is_admin != 'super' && (!array_key_exists($menu[$key][$i][0],$auth) || !strstr($auth[$menu[$key][$i][0]], 'r')))
			continue;

		if (($menu[$key][$i][4] == 1 && $gnb_grp_style == false) || ($menu[$key][$i][4] != 1 && $gnb_grp_style == true)) $gnb_grp_div = 'gnb_grp_div';
		else $gnb_grp_div = '';

		if ($menu[$key][$i][4] == 1) $gnb_grp_style = 'gnb_grp_style';
		else $gnb_grp_style = '';


		//echo count($subadmin_auth_arr)." | ";
		if(count($subadmin_auth_arr) > 0 && $menu[$key][$i][1] == '부관리자 정보') {
			$str .= '';
		}else {
			$str .= '<li class="gnb_2dli"><a href="'.$menu[$key][$i][2].'" class="gnb_2da '.$gnb_grp_style.' '.$gnb_grp_div.'">'.$menu[$key][$i][1].'</a></li>';
		}

		$auth_menu[$menu[$key][$i][0]] = $menu[$key][$i][1];
	}
	$str .= "</ul>";

	return $str;
}

?>

<script>
var tempX = 0;
var tempY = 0;

function imageview(id, w, h)
{
	menu(id);

	var el_id = document.getElementById(id);

//submenu = eval(name+".style");
	submenu = el_id.style;
	submenu.left = tempX - ( w + 11 );
	submenu.top  = tempY - ( h / 2 );

	selectBoxVisible();

	if (el_id.style.display != 'none')
		selectBoxHidden(id);
}
</script>

<div id="to_content"><a href="#container">본문 바로가기</a></div>

<header id="hd">
	<div id="hd_wrap" style="min-width:1500px;">
		<h1><?=$config['cf_title']?></h1>
		<div id="logo"><a href="<?=G5_ADMIN_URL?>"><img src="<?=G5_ADMIN_URL?>/img/logo.jpg" alt="<?=$config['cf_title']?> 관리자"></a></div>
		<ul id="tnb">
			<li><a href="<?=G5_ADMIN_URL?>/member_form.php?w=u&amp;mb_id=<?=$member['mb_id']?>">관리자정보</a></li>
			<li><a href="<?=G5_ADMIN_URL?>/config_form.php">기본환경</a></li>
			<li><a href="<?=G5_URL?>/">커뮤니티</a></li>
			<? if(defined('G5_USE_SHOP')) { ?>
				<!-- <li><a href="<?=G5_ADMIN_URL?>/shop_admin/configform.php">쇼핑몰환경</a></li> -->
				<li><a href="<?=G5_ADMIN_URL?>/service.php">부가서비스</a></li>
				<!-- <li><a href="<?=G5_SHOP_URL?>/">쇼핑몰</a></li> -->
			<? } ?>
			<li id="tnb_logout"><a href="<?=G5_BBS_URL?>/logout.php">로그아웃</a></li>
		</ul>

		<nav id="gnb">
			<h2>관리자 주메뉴</h2>
<?
	$gnb_str = "<ul id=\"gnb_1dul\">";

	foreach($amenu as $key=>$value) {

		if(count($subadmin_auth_arr) > 0) {
			if(in_array($key,$subadmin_auth_arr)) {
				$href1 = $href2 = '';
				if ($menu['menu'.$key][0][2]) {
					$href1 = '<a href="'.$menu['menu'.$key][0][2].'" class="gnb_1da">';
					$href2 = '</a>';
				} else {
					continue;
				}
				$current_class = "";
				if (isset($sub_menu) && (substr($sub_menu, 0, 3) == substr($menu['menu'.$key][0][0], 0, 3))) {
					$current_class = " gnb_1dli_air";
					//echo substr($menu['menu'.$key][0][0], 0, 3)."|";
				}
				//echo $menu['menu'.$key][0][1]."|";
				$gnb_str .= '<li class="gnb_1dli'.$current_class.'">'.PHP_EOL;
				$gnb_str .=  $href1 . $menu['menu'.$key][0][1] . $href2;
				$gnb_str .=  print_menu1('menu'.$key, 1);
				$gnb_str .=  "</li>";
			}
		}
		else {
			$href1 = $href2 = '';
			if ($menu['menu'.$key][0][2]) {
				$href1 = '<a href="'.$menu['menu'.$key][0][2].'" class="gnb_1da">';
				$href2 = '</a>';
			} else {
				continue;
			}
			$current_class = "";
			if (isset($sub_menu) && (substr($sub_menu, 0, 3) == substr($menu['menu'.$key][0][0], 0, 3))) {
				$current_class = " gnb_1dli_air";
			}
			$gnb_str .= '<li class="gnb_1dli'.$current_class.'">'.PHP_EOL;
			$gnb_str .=  $href1 . $menu['menu'.$key][0][1] . $href2;
			$gnb_str .=  print_menu1('menu'.$key, 1);
			$gnb_str .=  "</li>";
		}

	}

	$gnb_str .= "</ul>";
	echo $gnb_str;

?>
		</nav>
	</div>
</header>

<?
	if($sub_menu) {
?>
	<ul id="lnb">
<?
		$menu_key = substr($sub_menu, 0, 3);
		$nl = '';
		foreach($menu['menu'.$menu_key] as $key=>$value) {
			if($key > 0) {
				if ($is_admin != 'super' && (!array_key_exists($value[0],$auth) || !strstr($auth[$value[0]], 'r')))
					continue;

				$svc_class = ($value[3] == 'cf_service') ? ' class="lnb_svc"' : '';

				echo $nl.'<li><a href="'.$value[2].'"'.$svc_class.'>'.$value[1].'</a></li>';

				$nl = PHP_EOL;
			}
		}
?>
	</ul>
<?
	}
?>

<div id="wrapper">
	<div id="container">

		<!-- font_resize('엘리먼트id', '제거할 class', '추가할 class'); -->
		<!--
		<div id="text_size">
			<button onclick="font_resize('container', 'ts_up ts_up2', '');"><img src="<?=G5_ADMIN_URL?>/img/ts01.gif" alt="기본"></button>
			<button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up');"><img src="<?=G5_ADMIN_URL?>/img/ts02.gif" alt="크게"></button>
			<button onclick="font_resize('container', 'ts_up ts_up2', 'ts_up2');"><img src="<?=G5_ADMIN_URL?>/img/ts03.gif" alt="더크게"></button>
		</div>
		//-->

<?
	if(in_array($prd_idx, $CONF['OVDPRDT'])) {
?>
		<h1><div style="background:#FFDDDD;color:#FF2222"><?=$g5['title']?></div></h1>
<?
	}
	else {
?>
		<h1><?=$g5['title']?></h1>
<?
	}
?>
