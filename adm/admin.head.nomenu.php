<?

if (!defined('_GNUBOARD_')) exit;

if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
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