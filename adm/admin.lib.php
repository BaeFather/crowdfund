<?php
if (!defined('_GNUBOARD_')) exit;

/*
// 081022 : CSRF 방지를 위해 코드를 작성했으나 효과가 없어 주석처리 함
if (!get_session('ss_admin')) {
    set_session('ss_admin', true);
    goto_url('.');
}
*/

// 스킨디렉토리를 SELECT 형식으로 얻음
function get_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    global $config;

    $skins = array();

    if(defined('G5_THEME_PATH') && $config['cf_theme']) {
        $dirs = get_skin_dir($skin_gubun, G5_THEME_PATH.'/'.G5_SKIN_DIR);
        if(!empty($dirs)) {
            foreach($dirs as $dir) {
                $skins[] = 'theme/'.$dir;
            }
        }
    }

    $skins = array_merge($skins, get_skin_dir($skin_gubun));

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
        if(preg_match('#^theme/(.+)$#', $skins[$i], $match))
            $text = '(테마) '.$match[1];
        else
            $text = $skins[$i];

        $str .= option_selected($skins[$i], $selected, $text);
    }
    $str .= "</select>";
    return $str;
}

// 모바일 스킨디렉토리를 SELECT 형식으로 얻음
function get_mobile_skin_select($skin_gubun, $id, $name, $selected='', $event='')
{
    global $config;

    $skins = array();

    if(defined('G5_THEME_PATH') && $config['cf_theme']) {
        $dirs = get_skin_dir($skin_gubun, G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR);
        if(!empty($dirs)) {
            foreach($dirs as $dir) {
                $skins[] = 'theme/'.$dir;
            }
        }
    }

    $skins = array_merge($skins, get_skin_dir($skin_gubun, G5_MOBILE_PATH.'/'.G5_SKIN_DIR));

    $str = "<select id=\"$id\" name=\"$name\" $event>\n";
    for ($i=0; $i<count($skins); $i++) {
        if ($i == 0) $str .= "<option value=\"\">선택</option>";
        if(preg_match('#^theme/(.+)$#', $skins[$i], $match))
            $text = '(테마) '.$match[1];
        else
            $text = $skins[$i];

        $str .= option_selected($skins[$i], $selected, $text);
    }
    $str .= "</select>";
    return $str;
}


// 스킨경로를 얻는다
function get_skin_dir($skin, $skin_path=G5_SKIN_PATH)
{
    global $g5;

    $result_array = array();

    $dirname = $skin_path.'/'.$skin.'/';
    if(!is_dir($dirname))
        return;

    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if (is_dir($dirname.$file)) $result_array[] = $file;
    }
    closedir($handle);
    sort($result_array);

    return $result_array;
}


// 테마
function get_theme_dir()
{
    $result_array = array();

    $dirname = G5_PATH.'/'.G5_THEME_DIR.'/';
    $handle = opendir($dirname);
    while ($file = readdir($handle)) {
        if($file == '.'||$file == '..') continue;

        if (is_dir($dirname.$file)) {
            $theme_path = $dirname.$file;
            if(is_file($theme_path.'/index.php') && is_file($theme_path.'/head.php') && is_file($theme_path.'/tail.php'))
                $result_array[] = $file;
        }
    }
    closedir($handle);
    natsort($result_array);

    return $result_array;
}


// 테마정보
function get_theme_info($dir)
{
    $info = array();
    $path = G5_PATH.'/'.G5_THEME_DIR.'/'.$dir;

    if(is_dir($path)) {
        $screenshot = $path.'/screenshot.png';
        if(is_file($screenshot)) {
            $size = @getimagesize($screenshot);

            if($size[2] == 3)
                $screenshot_url = str_replace(G5_PATH, G5_URL, $screenshot);
        }

        $info['screenshot'] = $screenshot_url;

        $text = $path.'/readme.txt';
        if(is_file($text)) {
            $content = file($text, false);
            $content = array_map('trim', $content);

            preg_match('#^Theme Name:(.+)$#i', $content[0], $m0);
            preg_match('#^Theme URI:(.+)$#i', $content[1], $m1);
            preg_match('#^Maker:(.+)$#i', $content[2], $m2);
            preg_match('#^Maker URI:(.+)$#i', $content[3], $m3);
            preg_match('#^Version:(.+)$#i', $content[4], $m4);
            preg_match('#^Detail:(.+)$#i', $content[5], $m5);
            preg_match('#^License:(.+)$#i', $content[6], $m6);
            preg_match('#^License URI:(.+)$#i', $content[7], $m7);

            $info['theme_name'] = trim($m0[1]);
            $info['theme_uri'] = trim($m1[1]);
            $info['maker'] = trim($m2[1]);
            $info['maker_uri'] = trim($m3[1]);
            $info['version'] = trim($m4[1]);
            $info['detail'] = trim($m5[1]);
            $info['license'] = trim($m6[1]);
            $info['license_uri'] = trim($m7[1]);
        }

        if(!$info['theme_name'])
            $info['theme_name'] = $dir;
    }

    return $info;
}


// 테마설정 정보
function get_theme_config_value($dir, $key='*')
{
    $tconfig = array();

    $theme_config_file = G5_PATH.'/'.G5_THEME_DIR.'/'.$dir.'/theme.config.php';
    if(is_file) {
        include($theme_config_file);

        if($key == '*') {
            $tconfig = $theme_config;
        } else {
            $keys = array_map('trim', explode(',', $key));
            foreach($keys as $v) {
                $tconfig[$v] = trim($theme_config[$v]);
            }
        }
    }

    return $tconfig;
}


// 회원권한을 SELECT 형식으로 얻음
function get_member_level_select($name, $start_id=0, $end_id=10, $selected="", $event="")
{
    global $g5;

    $str = "\n<select id=\"{$name}\" name=\"{$name}\"";
    if ($event) $str .= " $event";
    $str .= ">\n";
    for ($i=$start_id; $i<=$end_id; $i++) {
        $str .= '<option value="'.$i.'"';
        if ($i == $selected)
            $str .= ' selected="selected"';
        $str .= ">{$i}</option>\n";
    }
    $str .= "</select>\n";
    return $str;
}


// 회원아이디를 SELECT 형식으로 얻음
function get_member_id_select($name, $level, $selected="", $event="")
{
    global $g5;

    $sql = " select mb_id from {$g5['member_table']} where mb_level >= '{$level}' ";
    $result = sql_query($sql);
    $str = '<select id="'.$name.'" name="'.$name.'" '.$event.'><option value="">선택안함</option>';
    for ($i=0; $row=sql_fetch_array($result); $i++)
    {
        $str .= '<option value="'.$row['mb_id'].'"';
        if ($row['mb_id'] == $selected) $str .= ' selected';
        $str .= '>'.$row['mb_id'].'</option>';
    }
    $str .= '</select>';
    return $str;
}

// 권한 검사
function auth_check($auth, $attr, $return=false)
{
    global $is_admin;

    if ($is_admin == 'super') return;

    if (!trim($auth)) {
        $msg = '이 메뉴에는 접근 권한이 없습니다.\\n\\n접근 권한은 최고관리자만 부여할 수 있습니다.';
        if($return)
            return $msg;
        else
            alert($msg);
    }

    $attr = strtolower($attr);

    if (!strstr($auth, $attr)) {
        if ($attr == 'r') {
            $msg = '읽을 권한이 없습니다.';
            if($return)
                return $msg;
            else
                alert($msg);
        } else if ($attr == 'w') {
            $msg = '입력, 추가, 생성, 수정 권한이 없습니다.';
            if($return)
                return $msg;
            else
                alert($msg);
        } else if ($attr == 'd') {
            $msg = '삭제 권한이 없습니다.';
            if($return)
                return $msg;
            else
                alert($msg);
        } else {
            $msg = '속성이 잘못 되었습니다.';
            if($return)
                return $msg;
            else
                alert($msg);
        }
    }
}


// 작업아이콘 출력
function icon($act, $link='', $target='_parent')
{
    global $g5;

    $img = array('입력'=>'insert', '추가'=>'insert', '생성'=>'insert', '수정'=>'modify', '삭제'=>'delete', '이동'=>'move', '그룹'=>'move', '보기'=>'view', '미리보기'=>'view', '복사'=>'copy');
    $icon = '<img src="'.G5_ADMIN_PATH.'/img/icon_'.$img[$act].'.gif" title="'.$act.'">';
    if ($link)
        $s = '<a href="'.$link.'">'.$icon.'</a>';
    else
        $s = $icon;
    return $s;
}


// rm -rf 옵션 : exec(), system() 함수를 사용할 수 없는 서버 또는 win32용 대체
// www.php.net 참고 : pal at degerstrom dot com
function rm_rf($file)
{
    if (file_exists($file)) {
        if (is_dir($file)) {
            $handle = opendir($file);
            while($filename = readdir($handle)) {
                if ($filename != '.' && $filename != '..')
                    rm_rf($file.'/'.$filename);
            }
            closedir($handle);

            @chmod($file, G5_DIR_PERMISSION);
            @rmdir($file);
        } else {
            @chmod($file, G5_FILE_PERMISSION);
            @unlink($file);
        }
    }
}

// 입력 폼 안내문
function help($help="")
{
    global $g5;

    $str  = '<span class="frm_info">'.str_replace("\n", "<br>", $help).'</span>';

    return $str;
}

// 출력순서
function order_select($fld, $sel='')
{
    $s = '<select name="'.$fld.'" id="'.$fld.'">';
    for ($i=1; $i<=100; $i++) {
        $s .= '<option value="'.$i.'" ';
        if ($sel) {
            if ($i == $sel) {
                $s .= 'selected';
            }
        } else {
            if ($i == 50) {
                $s .= 'selected';
            }
        }
        $s .= '>'.$i.'</option>';
    }
    $s .= '</select>';

    return $s;
}

// 불법접근을 막도록 토큰을 생성하면서 토큰값을 리턴
function get_admin_token()
{
    $token = md5(uniqid(rand(), true));
    set_session('ss_admin_token', $token);

    return $token;
}


// POST로 넘어온 토큰과 세션에 저장된 토큰 비교
function check_admin_token()
{
    $token = get_session('ss_admin_token');
    set_session('ss_admin_token', '');

    if(!$token || !$_REQUEST['token'] || $token != $_REQUEST['token'])
        alert('올바른 방법으로 이용해 주십시오.', G5_URL);

    return true;
}

// 관리자 페이지 referer 체크
function admin_referer_check($return=false)
{
    $referer = trim($_SERVER['HTTP_REFERER']);
    if(!$referer) {
        $msg = '정보가 올바르지 않습니다.';

        if($return)
            return $msg;
        else
            alert($msg, G5_URL);
    }

    $p = @parse_url($referer);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);

    if($host != $p['host']) {
        $msg = '올바른 방법으로 이용해 주십시오.';

        if($return)
            return $msg;
        else
            alert($msg, G5_URL);
    }
}

// 접근 권한 검사
if (!$member['mb_id'] && $path != "/adm/login.php")
{
		//alert('로그인 하십시오.', G5_BBS_URL.'/login.php?url=' . urlencode(G5_ADMIN_URL));
		ob_start();
		header("HTTP/1.0 404 Not Found");
}
else if ($is_admin != 'super')
{
		$auth = array();
    $sql = " select au_menu, au_auth from {$g5['auth_table']} where mb_id = '{$member['mb_id']}' ";
    $result = sql_query($sql);
    for($i=0; $row=sql_fetch_array($result); $i++)
    {
        $auth[$row['au_menu']] = $row['au_auth'];
    }

    if (!$i)
    {
      //alert('최고관리자 또는 관리권한이 있는 회원만 접근 가능합니다.', G5_URL);
			ob_start();
			header("HTTP/1.0 404 Not Found");
		}
}



// 관리자의 아이피, 브라우저와 다르다면 세션을 끊고 관리자에게 메일을 보낸다.
$admin_key = md5($member['mb_datetime'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT']);
if(get_session('ss_mb_key') !== $admin_key) {

		session_destroy();

		// 메일 알림
		include_once(G5_LIB_PATH.'/mailer.lib.php');
    mailer($member['mb_nick'], $member['mb_email'], $member['mb_email'], 'XSS 공격 알림', $_SERVER['REMOTE_ADDR'].' 아이피로 XSS 공격이 있었습니다.<br><br>관리자 권한을 탈취하려는 접근이므로 주의하시기 바랍니다.<br><br>해당 아이피는 차단하시고 의심되는 게시물이 있는지 확인하시기 바랍니다.'.G5_URL, 0);

		msg_replace("", '/', 'top');
}

@ksort($auth);

// 가변 메뉴
unset($auth_menu);
unset($menu);
unset($amenu);
$tmp = dir(G5_ADMIN_PATH);

while($entry = $tmp->read()) {

	if (!preg_match('/^admin.menu([0-9]{3}).*\.php$/', $entry, $m)) {
		continue;  // 파일명이 menu 으로 시작하지 않으면 무시한다.
	}

	$amenu[$m[1]] = $entry;
	include_once(G5_ADMIN_PATH.'/'.$entry);
}
@ksort($amenu);

$arr_query = array();
if (isset($sst))  $arr_query[] = 'sst='.$sst;
if (isset($sod))  $arr_query[] = 'sod='.$sod;
if (isset($sfl))  $arr_query[] = 'sfl='.$sfl;
if (isset($stx))  $arr_query[] = 'stx='.$stx;
if (isset($page)) $arr_query[] = 'page='.$page;
$qstr = implode("&amp;", $arr_query);

// 관리자에서는 추가 스크립트는 사용하지 않는다.
//$config['cf_add_script'] = '';

function get_auto_real_money($s_type, $ret_list, $gr_idx="") {

	$RES = array();

	$csql = "select * from cf_auto_invest_config where idx='$s_type'";
	$cres = sql_query($csql);
	$crow = sql_fetch_array($cres);
	$RES['category'] = $crow['category'];
	$RES['grp_title'] = $crow['grp_title'];

	$sql1 = "select a.*, b.mb_point, b.member_type, b.member_investor_type, b.mb_name, b.mb_co_name, b.mb_no  from cf_auto_invest_config_user a LEFT join (SELECT * FROM g5_member WHERE mb_level='1') b on(a.member_idx = b.mb_no) where ai_grp_idx='$s_type'  AND b.mb_no is not null order by a.idx";

	$res1 = sql_query($sql1);
	$cnt1 = sql_num_rows($res1);

	for ($i=0 ; $i<$cnt1 ; $i++) {

		$row1 = sql_fetch_array($res1);

		$RES["LIST"][$i]["mb_point"] = $row1['mb_point'];

		$setup_amount_total += $row1['setup_amount'];
		$setup_amount2_total += $row1['setup_amount2'];
		$mb_point_total += $row1['mb_point'];

		// 자동투자 설정 금액과 예치금 잔액 비교 이전
		//if ($row1['setup_amount'] <= $row1['mb_point']) $real_money = $row1['setup_amount'];
		//else $real_money = 0;

		// 자동투자 설정 금액과 예치금 잔액 비교
		if ($row1['setup_amount2'] < $row1['mb_point'])
		{
			$real_money = $row1['setup_amount2'];
		} ELSE {
			if ($row1['setup_amount'] <= $row1['mb_point'] && $row1['setup_amount2'] >= $row1['mb_point']) {
				$real_money = floor($row1['mb_point'] / 10000) * 10000;
			} else  {
				$real_money = 0;
			}
		}


		// 이미 투자된 금액 추출해서 비교
		$ing_sql = "SELECT sum(a.amount) ing_amt, case when b.category='2' then 'b' ELSE 'c' end big_cat
						FROM cf_product_invest a LEFT JOIN cf_product b ON(a.product_idx = b.idx)
						WHERE a.member_idx='$row1[member_idx]'
						AND a.invest_state='Y'
						AND b.state IN ('','1')
						GROUP BY case when b.category='2' then 'b' ELSE 'c' end";
		$ing_res = sql_query($ing_sql);
		$ing_cnt = sql_num_rows($ing_res);


		//if ($row1['member_idx']=="5713") echo "$ing_sql<br/>";

		$t_ing_money = 0;  // 총 투자중인 금액
		$b_ing_money = 0;  // 부동산 투자중인 금액
		$c_ing_money = 0;  // 그외 투자중인 금액

		for ($j=0 ; $j<$ing_cnt ; $j++) {

			$ing_row = sql_fetch_array($ing_res);
			if ($ing_row['big_cat']=="b") {
				$b_ing_money = $ing_row['ing_amt'];
			} else {
				$c_ing_money = $ing_row['ing_amt'];
			}
			$t_ing_money += $ing_row['ing_amt'];
		}

		$max_b = get_max_inv($row1['member_type'], $row1["member_investor_type"],"b");  // 부동산
		$max_c = get_max_inv($row1['member_type'], $row1["member_investor_type"],"c");  // 부동산외
		$max_t = get_max_inv($row1['member_type'], $row1["member_investor_type"],"t");  // 카테고리별
		$max_d = get_max_inv($row1['member_type'], $row1["member_investor_type"],"d");  // 동일차주

		if ($row1["member_investor_type"]=="2") {
			$passb_b = $max_t - $t_ing_money;		// 총 투자 남은금액
			$passb_c = $max_t - $t_ing_money;		// 부동산 투자중 남은금액
			$passb_t = $max_t - $t_ing_money;		// 그외 투자중 남은금액
		} else if ($row1["member_investor_type"]=="3") {  // 전문 투자자
			$passb_b = 9999999999;
			$passb_c = 9999999999;
			$passb_t = 9999999999;
		} else {  // 일반
			$passb_b = $max_b - $b_ing_money;
			$passb_c = $max_c - $c_ing_money;
			$passb_t = $max_t - $t_ing_money;
		}

		if ($row1['member_type'] == "2") {
			$passb_b = 9999999999;
			$passb_c = 9999999999;
			$passb_t = 9999999999;
		}

		if ($crow['category']=="2") {
			if ($real_money>$passb_b) $real_money = $passb_b;
		} else {
			if ($real_money>$passb_c) $real_money = $passb_c;
		}

		/*
		IF($row1["member_idx"] == "80")
		{
			echo $max_b.">>".$max_c.">>".$max_t."<BR>";
			echo $passb_b." >> ".$passb_c.">> ".$passb_t.">> "."<BR>";
			echo $b_ing_money." >> ".$c_ing_money.">> ".$t_ing_money.">> "."<BR>";
			echo $real_money;
		}
		*/
		if ($real_money>$passb_t) $real_money = 0;  // 카테고리별 제한금액

		$dong_cha_money = 0;

		// 사용금액이 존재한다면
		IF($real_money > 0)
		{
			IF($row1["member_type"]=="1" && ($row1["member_investor_type"]=="1" || $row1["member_investor_type"]=="2"))
			{
				// 동일차주
				$ing_dong_sql = "SELECT sum(a.amount) ing_amt
								FROM cf_product_invest a LEFT JOIN cf_product b ON(a.product_idx = b.idx)
								WHERE a.member_idx='$row1[member_idx]'
								AND (b.gr_idx = '$gr_idx')
								AND a.invest_state='Y'
								AND b.state In ('','1')";

				$RES["LIST"][$i]["sql"] = $ing_dong_sql;
				$ing_dong_res = sql_query($ing_dong_sql);
				$ing_dong_row = sql_fetch_array($ing_dong_res);
				$dong_cha_money = $ing_dong_row["ing_amt"];

				$real_money_temp = 0;
				IF($dong_cha_money > 0)
				{
					$real_money_temp = ($max_d - $dong_cha_money);

				} ELSE {
					$real_money_temp = $max_d;
				}

				IF($real_money <= $real_money_temp && $real_money >= $real_money_temp)
				{
					$real_money		=	$real_money_temp;
				} ELSE {
					IF($real_money >= $real_money_temp)
					{
						$real_money		=	$real_money_temp;
					} ELSE {
						$real_money		=	$real_money;
					}
				}
			}
		}

		//echo "real_money : ".$row1["member_idx"]." / ".$real_money."--".$max_d."<<".$real_money_temp.">>".$dong_cha_money."<BR>";

		if ($real_money<$row1['setup_amount']) $real_money=0;

		$total_setup += $row1['setup_amount'];
		$total_point += $row1['mb_point'];
		$total_amount += $real_money;

		if ($ret_list=="Y")
		{
			$RES["LIST"][$i]["dong_cha_money"] = $dong_cha_money;

			$RES["LIST"][$i]["mem_idx"] = $row1["member_idx"];
			$RES["LIST"][$i]["mb_name"] = $row1["mb_name"];
			$RES["LIST"][$i]["mb_co_name"] = $row1["mb_co_name"];
			$RES["LIST"][$i]["member_type"] = $row1["member_type"];
			$RES["LIST"][$i]["inv_type"] = $row1["member_investor_type"];

			if ($row1["member_type"]=="2") $inv_type_name = "법인";
			else {
				if ($row1["member_investor_type"]=="2") $inv_type_name = "소득적격";
				else if ($row1["member_investor_type"]=="3") $inv_type_name = "전문투자자";
				else $inv_type_name = "개인";
			}

			$RES["LIST"][$i]["inv_type_name"] = $inv_type_name;

			$RES["LIST"][$i]["setup_amount"] = $row1["setup_amount"];
			$RES["LIST"][$i]["setup_amount2"] = $row1["setup_amount2"];

			$RES["LIST"][$i]["real_money"] = $real_money;

			$RES["LIST"][$i]["b_ed_money"] = $b_ing_money;
			$RES["LIST"][$i]["b_ps_money"] = $passb_b;
			$RES["LIST"][$i]["b_mx_money"] = $max_b;

			$RES["LIST"][$i]["c_ed_money"] = $c_ing_money;
			$RES["LIST"][$i]["c_ps_money"] = $passb_c;
			$RES["LIST"][$i]["c_mx_money"] = $max_c;

			$RES["LIST"][$i]["t_ed_money"] = $t_ing_money;
			$RES["LIST"][$i]["t_ps_money"] = $passb_t;
			$RES["LIST"][$i]["t_mx_money"] = $max_t;

			$RES["LIST"][$i]["n_ed_money"] = $RES["LIST"][$i]["t_ed_money"];
			$RES["LIST"][$i]["n_ps_money"] = $RES["LIST"][$i]["t_ps_money"];
			$RES["LIST"][$i]["n_mx_money"] = $RES["LIST"][$i]["t_mx_money"];

			$n_dong_cha_money += $RES["LIST"][$i]["dong_cha_money"]; //동일차주 머니
			$n_ed_money += $RES["LIST"][$i]["n_ed_money"];
			$b_ed_money += $RES["LIST"][$i]["b_ed_money"]; //부동산
		}
	}

	$RES["dong_cha_money"] = $n_dong_cha_money;
	$RES["setup_amount_total"] = $setup_amount_total;
	$RES["setup_amount2_total"] = $setup_amount2_total;
	$RES["mb_point_total"] = $mb_point_total;
	$RES["n_ed_money"] = $n_ed_money;
	$RES["b_ed_money"] = $b_ed_money; //부동산
	$RES["total_amount"] = $total_amount;
	return $RES;

}


function get_auto_real_money_old($s_type, $ret_list, $gr_idx="") {

	$RES = array();

	$csql = "select * from cf_auto_invest_config where idx='$s_type'";
	$cres = sql_query($csql);
	$crow = sql_fetch_array($cres);
	$RES['category'] = $crow['category'];
	$RES['grp_title'] = $crow['grp_title'];

	$sql1 = "select a.*, b.mb_point, b.member_type, b.member_investor_type, b.mb_name, b.mb_co_name  from cf_auto_invest_config_user a LEFT join g5_member b on(a.member_idx = b.mb_no) where ai_grp_idx='$s_type' and (b.mb_level='1' or b.mb_level='2') order by a.idx";
	//$sql1 = "select a.*, b.mb_point, b.member_type, b.member_investor_type, b.mb_name  from cf_auto_invest_config_user a LEFT join g5_member b on(a.member_idx = b.mb_no) where ai_grp_idx='$s_type' and b.mb_no='10639' order by a.idx";

	$res1 = sql_query($sql1);
	$cnt1 = sql_num_rows($res1);

	for ($i=0 ; $i<$cnt1 ; $i++) {

		$row1 = sql_fetch_array($res1);

		$RES["LIST"][$i]["mb_point"] = $row1['mb_point'];

		$setup_amount_total += $row1['setup_amount'];
		$setup_amount2_total += $row1['setup_amount2'];
		$mb_point_total += $row1['mb_point'];

		// 자동투자 설정 금액과 예치금 잔액 비교
		if ($row1['setup_amount'] <= $row1['mb_point']) $real_money = $row1['setup_amount'];
		//else $real_money = $row1['mb_point'];
		else $real_money = 0;

		// 이미 투자된 금액 추출해서 비교
		$ing_sql = "SELECT sum(a.amount) ing_amt, case when b.category='2' then 'b' ELSE 'c' end big_cat
						FROM cf_product_invest a LEFT JOIN cf_product b ON(a.product_idx = b.idx)
						WHERE a.member_idx='$row1[member_idx]'
						AND a.invest_state='Y'
						AND b.state='1'
						GROUP BY case when b.category='2' then 'b' ELSE 'c' end";
		$ing_res = sql_query($ing_sql);
		$ing_cnt = sql_num_rows($ing_res);


		//if ($row1['member_idx']=="5713") echo "$ing_sql<br/>";

		$t_ing_money = 0;  // 총 투자중인 금액
		$b_ing_money = 0;  // 부동산 투자중인 금액
		$c_ing_money = 0;  // 그외 투자중인 금액

		for ($j=0 ; $j<$ing_cnt ; $j++) {

			$ing_row = sql_fetch_array($ing_res);
			if ($ing_row['big_cat']=="b") {
				$b_ing_money = $ing_row['ing_amt'];
			} else {
				$c_ing_money = $ing_row['ing_amt'];
			}
			$t_ing_money += $ing_row['ing_amt'];
		}

		$max_b = get_max_inv($row1['member_type'], $row1["member_investor_type"],"b");  // 부동산
		$max_c = get_max_inv($row1['member_type'], $row1["member_investor_type"],"c");  // 부동산외
		$max_t = get_max_inv($row1['member_type'], $row1["member_investor_type"],"t");  // 카테고리별
		$max_d = get_max_inv($row1['member_type'], $row1["member_investor_type"],"d");  // 동일차주

		if ($row1["member_investor_type"]=="2") {
			$passb_b = $max_t - $t_ing_money;
			$passb_c = $max_t - $t_ing_money;
			$passb_t = $max_t - $t_ing_money;
		} else if ($row1["member_investor_type"]=="3") {  // 전문 투자자
			$passb_b = 9999999999;
			$passb_c = 9999999999;
			$passb_t = 9999999999;
		} else {  // 일반
			$passb_b = $max_b - $b_ing_money;
			$passb_c = $max_c - $c_ing_money;
			$passb_t = $max_t - $t_ing_money;
		}


		if ($row1['member_type'] == "2") {
			$passb_b = 9999999999;
			$passb_c = 9999999999;
			$passb_t = 9999999999;
		}
		//echo "$passb_b";
		//if ($row1['member_idx']=="8323") echo "$passb_c <br/>";

		if ($crow['category']=="2") {
			if ($real_money>$passb_b) $real_money = $passb_b;
		} else {
			if ($real_money>$passb_c) $real_money = $passb_c;
		}

		//if ($row1['member_idx']=="8323") echo "$real_money<br/>$passb_c <br/>";

		if ($real_money>$passb_t) $real_money = 0;  // 카테고리별 제한금액


		$dong_cha_money = 0;
		if ($gr_idx and $real_money>0) {

			if ($crow['category']=="2") {  // 부동산

				if ($b_ing_money+$real_money > $max_b) $real_money = 0;
				else $real_money = $max_b - $b_ing_money;

			} else {  // 그외
				if ($c_ing_money+$real_money > $max_c) $real_money = 0;
				else $real_money = $max_c - $c_ing_money;
			}

			if ($real_money>=$row1['setup_amount']) $real_money = $row1['setup_amount'];
			else $real_money = 0;

			//if ($row1['member_idx']=="8323") echo "$real_money <br/>";

			if ($real_money>0) {

				$ing_dong_sql = "SELECT sum(a.amount) ing_amt
								FROM cf_product_invest a LEFT JOIN cf_product b ON(a.product_idx = b.idx)
								WHERE a.member_idx='$row1[member_idx]'
								AND b.gr_idx = '$gr_idx'
								AND a.invest_state='Y'
								AND b.state='1' ";
				$RES["LIST"][$i]["sql"] = $ing_dong_sql;
				$ing_dong_res = sql_query($ing_dong_sql);
				$ing_dong_row = sql_fetch_array($ing_dong_res);
				$dong_cha_money = $ing_dong_row["ing_amt"];


				if ($crow['category']=="2") {  // 부동산
					if ($dong_cha_money+$real_money > $max_d) $real_money = 0;
					else $real_money = $max_d - $dong_cha_money;
				} else {
					if ($dong_cha_money+$real_money > $max_d) $real_money = 0;
					else $real_money = $max_d - $dong_cha_money;
				}
				if ($real_money>=$row1['setup_amount']) $real_money = $row1['setup_amount'];
			}

		}

		if ($real_money<$row1['setup_amount']) $real_money=0;



		$total_setup += $row1['setup_amount'];
		$total_point += $row1['mb_point'];
		$total_amount += $real_money;

		if ($ret_list=="Y") {

			$RES["LIST"][$i]["dong_cha_money"] = $dong_cha_money;

			$RES["LIST"][$i]["mem_idx"] = $row1["member_idx"];
			$RES["LIST"][$i]["mb_name"] = $row1["mb_name"];
			$RES["LIST"][$i]["mb_co_name"] = $row1["mb_co_name"];
			$RES["LIST"][$i]["member_type"] = $row1["member_type"];
			$RES["LIST"][$i]["inv_type"] = $row1["member_investor_type"];

			if ($row1["member_type"]=="2") $inv_type_name = "법인";
			else {
				if ($row1["member_investor_type"]=="2") $inv_type_name = "소득적격";
				else if ($row1["member_investor_type"]=="3") $inv_type_name = "전문투자자";
				else $inv_type_name = "개인";
			}

			$RES["LIST"][$i]["inv_type_name"] = $inv_type_name;

			$RES["LIST"][$i]["setup_amount"] = $row1["setup_amount"];
			$RES["LIST"][$i]["setup_amount2"] = $row1["setup_amount2"];

			$RES["LIST"][$i]["real_money"] = $real_money;

			$RES["LIST"][$i]["b_ed_money"] = $b_ing_money;
			$RES["LIST"][$i]["b_ps_money"] = $passb_b;
			$RES["LIST"][$i]["b_mx_money"] = $max_b;

			$RES["LIST"][$i]["c_ed_money"] = $c_ing_money;
			$RES["LIST"][$i]["c_ps_money"] = $passb_c;
			$RES["LIST"][$i]["c_mx_money"] = $max_c;

			$RES["LIST"][$i]["t_ed_money"] = $t_ing_money;
			$RES["LIST"][$i]["t_ps_money"] = $passb_t;
			$RES["LIST"][$i]["t_mx_money"] = $max_t;

			if ($crow['category']=="2") {
				$RES["LIST"][$i]["n_ed_money"] = $RES["LIST"][$i]["b_ed_money"];
				$RES["LIST"][$i]["n_ps_money"] = $RES["LIST"][$i]["b_ps_money"];
				$RES["LIST"][$i]["n_mx_money"] = $RES["LIST"][$i]["b_mx_money"];
			} else {
				$RES["LIST"][$i]["n_ed_money"] = $RES["LIST"][$i]["c_ed_money"];
				$RES["LIST"][$i]["n_ps_money"] = $RES["LIST"][$i]["c_ps_money"];
				$RES["LIST"][$i]["n_mx_money"] = $RES["LIST"][$i]["c_mx_money"];
			}
			$n_ed_money += $RES["LIST"][$i]["n_ed_money"];
			$b_ed_money += $RES["LIST"][$i]["b_ed_money"]; //부동산
		}
	}

	$RES["setup_amount_total"] = $setup_amount_total;
	$RES["setup_amount2_total"] = $setup_amount2_total;
	$RES["mb_point_total"] = $mb_point_total;
	$RES["n_ed_money"] = $n_ed_money;
	$RES["b_ed_money"] = $b_ed_money; //부동산
	$RES["total_amount"] = $total_amount;

	//echo "$total_setup\n$total_point\n$total_amount";
	return $RES;

}

function get_max_inv($mem_type, $mem_inv_type, $prd_type) {

	global $INDI_INVESTOR;

	if ($mem_inv_type=="3" or $mem_type=="2") {  //  3 전문 투자자
		$max_t = $INDI_INVESTOR['3']['site_limit'];
		$max_b = $INDI_INVESTOR['3']['site_limit'];							// 부동산 투자한도
		$max_c = $INDI_INVESTOR['3']['site_limit'];							// 부동산외 투자한도
		$max_d = $INDI_INVESTOR['3']['single_product_limit'];		// 단일상품 투자한도
	}
	else if ($mem_inv_type=="2") {							// 2 소득적격 투자자
		$max_t = $INDI_INVESTOR['2']['site_limit'];
		$max_b = $INDI_INVESTOR['2']['site_limit'];
		$max_c = $INDI_INVESTOR['2']['site_limit'];
		$max_d = $INDI_INVESTOR['2']['single_product_limit'];
	}
	else {																			// 1 일반 개인 투자자
		$max_t = $INDI_INVESTOR['1']['site_limit'];
		$max_b = $INDI_INVESTOR['1']['prpt_limit'];							// 부동산 투자한도
		$max_c = $INDI_INVESTOR['1']['site_limit'];							// 부동산외 투자한도
		$max_d = $INDI_INVESTOR['1']['single_product_limit'];		// 단일상품 투자한도
	}

	if ($prd_type=="b") $ret_val = $max_b;
	else if ($prd_type=="c") $ret_val = $max_c;
	else if ($prd_type=="d") $ret_val = $max_d;
	else $ret_val = $max_t;

	return $ret_val;
}

function get_auto_inv_conf($mb_no) {

	if (!$mb_no) return false;

	$sql = "select a.*, b.grp_title from cf_auto_invest_config_user a LEFT JOIN cf_auto_invest_config b on (a.ai_grp_idx=b.idx) where a.member_idx='$mb_no' order by a.ai_grp_idx";
	$res = sql_query($sql);
	$cnt = sql_num_rows($res);

	$auto_res = array();

	for ($i=0 ; $i<$cnt ; $i++) {
		$auto_res[$i] = sql_fetch_array($res);
	}
	return $auto_res;
}

function query_get_auto_real_money($obj)
{
	/*
	원하는값은 각 자동투자그룹별 실제 투자를 할수 있는 금액을 산정한다.
	(헬로페이, 동산, 주택담보, 부동산)은 멤버의 기준에 따른다 (member_type,  member_investor_type)

	cf_auto_invest_config  idx (부동산, 주택담보 등)
	cf_auto_invest_config_user (유저 금액단위 설정)  ai_grp_idx  (cf_auto_invest_config)
							   , setup_amount(시작),setup_amount2( 종료) , member_idx (회원고유값)
	cf_product_invest 투자테이블  amount  (투자금액)  meember_idx (회원고유값) , product_idx(상품 고유값)
								  invest_state Y (정상 상태)

	cf_product  상품테이블     idx, state (1이 투자진행)
	g5_member (회원정보)  member_type,  member_investor_type, mb_no (회원고유값)  ,mb_point (예치금)


	member_idx : 회원고유값
	ai_grp_idx (cf_auto_invest_config 의 고유값)
	mb_point : 회원포인트
	setup_amount : 설정 시작값
	setup_amount2 : 설정 종료값
	send_point : 사용가능포인트
	onecnt : 1회제한 포인트
	midcnt : 카테고리별 제한금액 (member_type 1인회원중 mem_inv_type 이 1인 회원만)
          	부동산투자시 동산 50%; ,부동산투자없을시 동산100%
	maxcnt : 총 투자 제한금액
	use_point : 현재 사용포인트 (투자포인트)
	category : 1, 부동산 2, 부동산 외  (member_type 1인 mem_inv_type 이 1인  회원 예외처리를 위한 값)
	*/

	$strQuery = "
	SELECT t1.idx,t1.member_idx, ai_grp_idx,
	mb_point,member_type, member_investor_type,setup_amount,setup_amount2,
	mb_name, mb_co_name,
    CASE WHEN category = '2' AND member_type = '1' AND member_investor_type = '1' THEN
		CASE WHEN (10000000-ifnull(t3.use_point,0)) < (CASE WHEN
			(CAST(mb_point AS SIGNED)-CAST(setup_amount2 AS SIGNED)) >= 0 THEN setup_amount2
			WHEN mb_point <= setup_amount2 AND mb_point >= setup_amount THEN TRUNCATE(mb_point,-4)
			ELSE 0 END) THEN
				(10000000-ifnull(t3.use_point,0))

        ELSE
			(CASE WHEN
				(CAST(mb_point AS SIGNED)-CAST(setup_amount2 AS SIGNED)) >= 0 THEN setup_amount2
				WHEN mb_point <= setup_amount2 AND mb_point >= setup_amount THEN TRUNCATE(mb_point,-4)
				ELSE 0 END)
		END
    ELSE
		CASE WHEN
		(CAST(mb_point AS SIGNED)-CAST(setup_amount2 AS SIGNED)) >= 0 THEN setup_amount2
		WHEN mb_point <= setup_amount2 AND mb_point >= setup_amount THEN TRUNCATE(mb_point,-4)
		ELSE 0 END
    END AS send_point
	,onecnt , midcnt, maxcnt, category
	,CASE WHEN category = '2' THEN '1' ELSE '2' END as tcategory
	,ifnull(t3.use_point,0) as use_point1
	,ifnull(t4.use_point,0) as use_point2

	FROM
	(
		SELECT st1.idx, st1.ai_grp_idx, st1.member_idx,st1.setup_amount,st1.setup_amount2,st2.category FROM (SELECT t1.idx, t1.ai_grp_idx,t1.member_idx,t1.setup_amount,setup_amount2 FROM cf_auto_invest_config_user  t1 LEFT JOIN g5_member t2 ON t1.member_idx=t2.mb_no WHERE t2.mb_level='1') st1
		LEFT JOIN
		cf_auto_invest_config st2
		ON st1.ai_grp_idx=st2.idx
	) t1
	LEFT JOIN
	(
		SELECT st1.mb_no, st1.mb_point, onecnt, midcnt,maxcnt , st1.member_type, st1.member_investor_type,
				st1.mb_name, st1.mb_co_name
		FROM
		(SELECT mb_no, mb_point, member_type, member_investor_type,mb_name,mb_co_name FROM g5_member)  st1
		LEFT JOIN
		(
		SELECT 1 as member_type,1 as member_investor_type, 5000000 as onecnt, 10000000 as midcnt, 20000000 as maxcnt
		UNION
		SELECT 1,2 , 20000000, 20000000, 40000000
		UNION
		SELECT 1,3 , 99999999999, 99999999999 , 99999999999
		UNION
		SELECT 2,0 , 99999999999, 99999999999 , 99999999999
		) st2
		ON st1.member_type=st2.member_type
		WHERE IFNULL(st1.member_investor_type,0)=st2.member_investor_type
	) t2
	ON t1.member_idx=t2.mb_no
	LEFT JOIN
	(
		SELECT member_idx, SUM(st1.amount) as use_point FROM
		cf_product_invest st1 LEFT JOIN cf_product st2
		ON st1.product_idx = st2.idx
		WHERE st1.invest_state='Y'
		AND st2.state IN ('','1')
		AND st2.category = '2'
		GROUP BY  member_idx
	) t3
	ON t1.member_idx=t3.member_idx
	LEFT JOIN
	(
		SELECT member_idx, SUM(st1.amount) as use_point FROM
		cf_product_invest st1 LEFT JOIN cf_product st2
		ON st1.product_idx = st2.idx
		WHERE st1.invest_state='Y'
		AND st2.state IN ('','1')
		AND st2.category <> '2'
		GROUP BY  member_idx
	) t4
	ON t1.member_idx=t4.member_idx
";
	IF($obj)
	{
		$strQuery .= " WHERE ai_grp_idx='".$obj."'";
	}
	$strQuery .= "
	ORDER BY t1.idx, ai_grp_idx
	";

	return $strQuery;
}

function get_auto_real_money_new($obj)
{
	global $connect_db;

	$strQuery = query_get_auto_real_money($obj);
	$Result = sql_query($strQuery);

	// intProductMoney 상품 기준  투자가능금액 (자동투자그룹)
	// intCategoryMoney 카테고리 기준 투자가능금액 (담보형태)

	$intMemberCnt[1]			=	0;	//일반개인투자가 부동산금액 체크 변수
	$intMemberCnt[2]			=	0;	//일반개인투자가 부동산외 금액 체크 변수
	$intMemberCnt[0]			=	0;  //일반투자가외 총 투자금액

	$intProductCnt				=	ARRAY();

	$intProductMoney			=	0;
	$intCategoryMoney			=	0;
	$intCntTarget				=	0;

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$RowMemberIdx			=	$Row["member_idx"];
		$RowAiGrpIdx			=	$Row["ai_grp_idx"];
		$RowMbPoint				=	$Row["mb_point"];
		$RowMemberType			=	$Row["member_type"];
		$RowMemberInvestorType	=	$Row["member_investor_type"];
		$RowSendPoint			=	$Row["send_point"];	//실제 투자가능금액 (설정금액)
		$RowOneCnt				=	$Row["onecnt"];
		$RowMidCnt				=	$Row["midcnt"];
		$RowMaxCnt				=	$Row["maxcnt"];
		$RowUsePoint1			=	$Row["use_point1"];	//현재 부동산 투자포인트
		$RowUsePoint2			=	$Row["use_point2"];	//현재 부동산 외 투자포인트
		$RowCategory			=	$Row["category"];
		$RowTcategory			=	$Row["tcategory"];	// 1 부동산 2, 그외

		$RowSetupAmount			=	$Row["setup_amount"];
		$RowSetupAmount2		=	$Row["setup_amount2"];

		$intReturnMoney	=	fn_get_auto_real_money_calc(
															$RowMemberType,
															$RowMemberInvestorType,
															$RowOneCnt,
															$RowMidCnt,
															$RowMaxCnt,
															$RowSendPoint,
															$RowUsePoint1,
															$RowUsePoint2,
															$RowSetupAmount,
															$RowSetupAmount2,
															$RowTcategory
														);

		// intReturnMoney[0]  카테고리 투자금액  intReturnMoney[0] 상품 투자금액

		/*
		IF($RowMemberIdx=="922")
		{
			ECHO "RowTcategory : ".$RowTcategory;
			ECHO " / intCntTarget : ".$intCntTarget;
			ECHO " / intUsePoint : ".$intUsePoint;
			ECHO " / intUsePointSUm : ".($intCntTarget-$intUsePoint);
			ECHO " / RowSetupAmount : ".$RowSetupAmount;
			ECHO " / RowSetupAmount2 : ".$RowSetupAmount2."<BR><BR><BR>";
		}

		//echo $RowMemberIdx." : ".$RowUsePoint." : ".$RowAiGrpIdx." : ".$intCategoryMoney." : ".$intProductMoney."<BR>";
		*/

		// 반영
		$retval[$RowAiGrpIdx][1]		+= $intReturnMoney[0]; // 카테고리 기준 투자가능금액
		$retval[$RowAiGrpIdx][2]		+= $intReturnMoney[1];  // 상품 기준  투자가능금액
		$i++;
	}
	return $retval;

}

/*
$strVal = get_auto_real_money_new();

$strIdx	=	ARRAY(13,14,15,16,17);
FOR($j=0;$j<COUNT($strIdx);$j++)
{
	ECHO $strIdx[$j]."-----";
	ECHO $strVal[$strIdx[$j]]["TSUM"]." >>>  ";
	ECHO $strVal[$strIdx[$j]]["category_money"]." : ";
	ECHO $strVal[$strIdx[$j]]["product_money"];
	ECHO "<BR>";
}
exit;
*/
FUNCTION get_max_inv_new($mem_type, $mem_inv_type)
{
	$intMaxMoney		= 0;
	$intTotalMaxMoney	= 0;
	$intLevel			= "";
	$intAuth			= "";

	SWITCH($mem_type)
	{
		CASE "1" :	// 개인투자자

			SWITCH($mem_inv_type)
			{
				CASE "1" :	// 일반개인투자
					$intMaxMoney		=		 5000000;
					$intMidMoney		=		10000000;
					$intTotalMaxMoney	=		20000000;
					$intLevel			=		"1";  // 부동산투자시 비부동산 50%; ,부동산투자없을시 비부동산100%
					$intAuth			=		"1";	// 동일차주 제한 (동일차주1건만)
					$strName			=		"일반개인";
				BREAK;
				CASE "2" : // 소득적격
					$intMaxMoney		=		20000000;
					$intMidMoney		=		20000000;
					$intTotalMaxMoney	=		40000000;
					$intLevel			=		"2";  // 부동산, 동산 제한없음
					$intAuth			=		"1";	// 동일차주 제한 (동일차주1건만)
					$strName			=		"소득적격";
				BREAK;
				CASE "3" : //전문 투자자
					$intMaxMoney		=	 99999999999;
					$intMidMoney		=	 99999999999;
					$intTotalMaxMoney	= 	 99999999999;
					$intLevel			=		"2";  // 부동산, 동산 제한없음
					$intAuth			=		"2";	// 동일차주 제한없음
					$strName			=		"전문투자자";
				BREAK;
			}
		BREAK;
		CASE "2" :	// 법인투자자
			$intMaxMoney		=		99999999999;
			$intMidMoney		=		99999999999;
			$intTotalMaxMoney	= 		99999999999;
			$intLevel			=		"2";  // 부동산, 동산 제한없음
			$intAuth			=		"2";	// 동일차주 제한없음
			$strName			=		"법인";
		BREAK;
	}

	return ARRAY(
					"onemoney" => $intMaxMoney,
					"midmoney" => $intMidMoney,
					"maxmoney" => $intTotalMaxMoney,
					"level"	   => $intLevel,
					"auth"	   => $intAuth,
					"name"	   => $strName
			);
}

FUNCTION fn_get_auto_real_money_calc($RowMemberType,$RowMemberInvestorType,$RowOneCnt,$RowMidCnt,$RowMaxCnt,$RowSendPoint,$RowUsePoint1,$RowUsePoint2, $RowSetupAmount1, $RowSetupAmount2,$RowTcategory)
{
	global  $RowMemberIdx;
	$intCntTarget		= 0;		// 투자가능금액
	$intCategoryMoney	= 0;		// 카테고리기준
	$intProductMoney	= 0;		// 상품기준

	IF($RowMemberType == "1" && $RowMemberInvestorType == "1")	//  일반개인투자 예외처리
	{

		/* 부동산이 있다면 부동산은 1000까지, 부동산은 1000을 넘을수 없다. 나머진 총금액-부동산금액에서 투자가능
		ex) 부동산 100,나머지 1900 투자가능
		*/

		//$intCntTarget	=	$RowMaxCnt - $RowUsePoint1;

		IF($RowTcategory == "1") {	// 부동산
			$intCntTarget		=	$RowMidCnt;	// 최대 가능금액 1000
			$intUsePoint		=	$RowUsePoint1;
		} ELSE {
			$intCntTarget		=	$RowMaxCnt;
			$intUsePoint		=	$RowUsePoint2;
		}

	} ELSE {
		$intCntTarget		=	$RowMaxCnt;
		$intUsePoint		=	$RowUsePoint1 + $RowUsePoint2;
	}

	// 카테고리 기준 투자금액  intCategoryMoney
	// 상품 기준 투자금액      intProductMoney

	IF($intUsePoint	>= $intCntTarget)
	{
		$intCategoryMoney		=	0;
	} ELSE {
		$intCategoryMoney		=	$intCntTarget - $intUsePoint;

		IF($intCategoryMoney < 0)
		{
			$intCategoryMoney	=	0;
		} ELSE {
			IF($intCategoryMoney >= $RowSendPoint)
			{
				$intCategoryMoney = $RowSendPoint;
			}
		}
	}
	// 상품 기준 투자금액
	IF($intUsePoint >= $intCntTarget)
	{
		$intProductMoney		=	0;
	} ELSE {
		IF($RowSendPoint >= $RowOneCnt)
		{
			$intProductMoney		=	$RowOneCnt;
		} ELSE {
			$intProductMoney		=	$RowSendPoint;
		}
	}

	$intProductMoney = $intCategoryMoney;

	IF($RowMemberIdx == "7754")
	{
//		echo $RowSendPoint."---".$RowTcategory."--".$intCategoryMoney."--".$intProductMoney."<BR>";
//		exit;
	}
	/*
	IF($RowMemberIdx == "6461")
	{
	ECHO "DDD : ". $RowMidCnt."--->".$RowUsePoint1. ": ".$intUsePoint." : ".$intCntTarget." : ".$RowSetupAmount2." : ".$RowSendPoint."<BR>";
	echo "intCategoryMoney : ".$intCategoryMoney."<BR>";
	echo "intProductMoney : ".$intProductMoney."<BR>";
//DDD : 10000000--->9500000: 9500000 : 10000000 : 1000000 : 0
//intCategoryMoney : 0
//intProductMoney : 0
	exit;
	}
	*/
	IF($RowMemberIdx == "8452" && $_SERVER["REMOTE_ADDR"] == "220.117.134.205")
	{
//		ECHO $RowSendPoint."---".($intCntTarget-$intUsePoint)."---".$RowSetupAmount2."<BR>";
		// 4050000---4030000---5000000
		//exit;
	}

	IF($RowSetupAmount1 == $RowSetupAmount2)
	{
		IF(($intCntTarget-$intUsePoint) < $RowSetupAmount2)
		{
			$intCategoryMoney		=	0;
			$intProductMoney		=	0;
		}
	} ELSE {
		IF(($intCntTarget-$intUsePoint) < $RowSetupAmount1)
		{
			$intCategoryMoney		=	0;
			$intProductMoney		=	0;
		}
	}
	/*
	IF(($intCntTarget-$intUsePoint) < $RowSetupAmount2 && ($intCntTarget-$intUsePoint) > $RowSetupAmount1) //
	{
		$intCategoryMoney		=	0;
		$intProductMoney		=	0;
	}
	*/
	return ARRAY($intCategoryMoney, $intProductMoney);
}

function get_loan_start_time($prd_idx, $y) {
	if ($y=="0000") return "0000-00-00 00:00:00";
	if ($y==date("Y")) $tb_name = "IB_request_log";
	else $tb_name = "IB_request_log_$y";

	$sql = "SELECT edate FROM IB_request_log WHERE request_code='2300' AND request_arr LIKE '%LOAN_SEQ=$prd_idx'";
	$result = sql_query($sql);
	$cnt = sql_num_rows($result);

	if (!$cnt) return "0000-00-00 00:00:00";

	$row = sql_fetch_array($result);

	return $row['edate'];
}
?>