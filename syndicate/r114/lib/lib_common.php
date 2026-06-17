<?
/*************************************************************************
 **
 **  SQL 관련 함수 모음
 **
 *************************************************************************/

/*************************************************************************
 **
 **  일반 함수 모음
 **
 *************************************************************************/

 // 마이크로 타임을 얻어 계산 형식으로 만듦
function get_microtime()
{
	list($usec, $sec) = explode(" ",microtime());
	return ((float)$usec + (float)$sec);
}

// 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function get_paging($write_pages, $cur_page, $total_page, $url, $add="")
{
	//$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
	$url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

	$str = '';
	if($cur_page > 1) {
		$str .= '<a href="'.$url.'1'.$add.'" class="pg_page pg_start">처음</a>'.PHP_EOL;
	}

	$start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
	$end_page = $start_page + $write_pages - 1;

	if($end_page >= $total_page) $end_page = $total_page;

	if($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="pg_page pg_prev">이전</a>'.PHP_EOL;

	if($total_page > 1) {
		for($k=$start_page;$k<=$end_page;$k++) {
			if($cur_page != $k)
				$str .= '<a href="'.$url.$k.$add.'" class="pg_page">'.$k.'<span class="sound_only">페이지</span></a>'.PHP_EOL;
			else
				$str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
		}
	}

	if($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="pg_page pg_next">다음</a>'.PHP_EOL;

	if($cur_page < $total_page) {
		$str .= '<a href="'.$url.$total_page.$add.'" class="pg_page pg_end">맨끝</a>'.PHP_EOL;
	}

	if($str)
		return "<div class=\"pg_wrap\"><span class=\"pg\">{$str}</span></div>";
	else
		return "";
}

// 메타태그를 이용한 URL 이동
// header("location:URL") 을 대체
function goto_url($url)
{
	$url = str_replace("&amp;", "&", $url);
	//echo "<script> location.replace('$url'); </script>";

	if(!headers_sent())
		header('Location: '.$url);
	else {
		echo '<script>';
		echo 'location.replace("'.$url.'");';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
		echo '</noscript>';
	}
	exit;
}

// 세션변수 생성
function set_session($session_name, $value)
{
	if(PHP_VERSION < '5.3.0')
		session_register($session_name);
	// PHP 버전별 차이를 없애기 위한 방법
	$$session_name = $_SESSION[$session_name] = $value;
}


// 세션변수값 얻음
function get_session($session_name)
{
	return isset($_SESSION[$session_name]) ? $_SESSION[$session_name] : '';
}


// 쿠키변수 생성
function set_cookie($cookie_name, $value, $expire)
{
	global $g5;

	setcookie(md5($cookie_name), base64_encode($value), G5_SERVER_TIME + $expire, '/', G5_COOKIE_DOMAIN);
}

// 쿠키변수값 얻음
function get_cookie($cookie_name)
{
	$cookie = md5($cookie_name);
	if(array_key_exists($cookie, $_COOKIE))
		return base64_decode($_COOKIE[$cookie]);
	else
		return "";
}

// 경고메세지를 경고창으로
function alert($msg='', $url='', $error=true, $post=false)
{
	global $hf, $config, $member;
	global $is_admin;

	if(!$msg) $msg = '올바른 방법으로 이용해 주십시오.';

	$header = '';
	if(isset($hf['title'])) {
		$header = $hf['title'];
	}

	include_once(HF_PATH.'/alert.php');
	exit;
}

// 2017-07-18 추가분 -------------------------------------------------------------
function msg_go($msg="", $href=0, $target="window")
{
	$go = $href ? "$target.location.href='$href';" : "history.go(-1);";
	$alert = $msg ? "window.alert('$msg');" : "";

	echo " <script> ";
	echo "  $alert ";
	echo "  $go ";
	echo " </script> ";
	exit;
}

//히스토리 빽 방지를 위한 함수
function msg_replace($msg="", $href=0, $target="window")
{
	$go = $href ? "$target.location.replace('$href');" : "$target.location.replace('about:blank');";
	$alert = $msg ? "window.alert('$msg');" : "";

	echo " <script> ";
	echo "  $alert ";
	echo "  $go ";
	echo " </script> ";
	exit;
}

// 포인트 내역 합계
function get_point_sum($mb_id) {
	global $hf, $config;

	if($config['cf_point_term'] > 0) {
		// 소멸포인트가 있으면 내역 추가
		$expire_point = get_expire_point($mb_id);
		if($expire_point > 0) {
			$mb = get_member($mb_id, 'mb_point');
			$content = '포인트 소멸';
			$rel_table = '@expire';
			$rel_id = $mb_id;
			$rel_action = 'expire'.'-'.uniqid('');
			$point = $expire_point * (-1);
			$po_mb_point = $mb['mb_point'] + $point;
			$po_expire_date = HF_TIME_YMD;
			$po_expired = 1;

			$sql = "
				INSERT INTO
					{$hf['point_table']}
				SET
					mb_id = '$mb_id',
					po_datetime = '".HF_TIME_YMDHIS."',
					po_content = '".addslashes($content)."',
					po_point = '$point',
					po_use_point = '0',
					po_mb_point = '$po_mb_point',
					po_expired = '$po_expired',
					po_expire_date = '$po_expire_date',
					po_rel_table = '$rel_table',
					po_rel_id = '$rel_id',
					po_rel_action = '$rel_action' ";
			sql_query($sql);

			// 포인트를 사용한 경우 포인트 내역에 사용금액 기록
			if($point < 0) {
				insert_use_point($mb_id, $point);
			}
		}

		// 유효기간이 있을 때 기간이 지난 포인트 expired 체크
		$sql = "
			UPDATE
				{$hf['point_table']}
			SET
				po_expired = '1'
			WHERE
				mb_id = '$mb_id'
				AND po_expired <> '1'
				AND po_expire_date <> '9999-12-31'
				AND po_expire_date < '".HF_TIME_YMD."' ";
		sql_query($sql);
	}

	// 포인트합
	$sql = "SELECT SUM(po_point) AS sum_po_point FROM {$hf['point_table']} WHERE mb_id = '$mb_id'";
	$row = sql_fetch($sql);

	return $row['sum_po_point'];
}


function cut_str($str, $len, $suffix="…")
{
	$arr_str = preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
	$str_len = count($arr_str);

	if($str_len >= $len) {
		$slice_str = array_slice($arr_str, 0, $len);
		$str = join("", $slice_str);

		return $str . ($str_len > $len ? $suffix : '');
	} else {
		$str = join("", $arr_str);
		return $str;
	}
}

function cut_str2($str, $len, $suffix="") {
	$s = substr($str, 0, $len);
	$cnt = 0;
	for($i=0; $i<strlen($s); $i++) {
		if(ord($s[$i]) > 127) $cnt++;
	}
	$s = substr($s, 0, $len - ($cnt % 2));
	if(strlen($s) >= strlen($str)) $suffix = "";
	return $s . $suffix;
}


// TEXT 형식으로 변환
function get_text($str, $html=0, $restore=false)
{
	$source[] = "<";
	$target[] = "&lt;";
	$source[] = ">";
	$target[] = "&gt;";
	$source[] = "\"";
	$target[] = "&#034;";
	$source[] = "\'";
	$target[] = "&#039;";

	if($restore)
		$str = str_replace($target, $source, $str);

	// 3.31
	// TEXT 출력일 경우 &amp; &nbsp; 등의 코드를 정상으로 출력해 주기 위함
	if($html == 0) {
		$str = html_symbol($str);
	}

	if($html) {
		$source[] = "\n";
		$target[] = "<br/>";
	}

	return str_replace($source, $target, $str);
}


// 3.31
// HTML SYMBOL 변환
// &nbsp; &amp; &middot; 등을 정상으로 출력
function html_symbol($str)
{
	return preg_replace("/\&([a-z0-9]{1,20}|\#[0-9]{0,3});/i", "&#038;\\1;", $str);
}

/*************************************************************************
 **
 **  SQL 관련 함수 모음
 **
 *************************************************************************/

// DB 연결 ($db_port 인자값 추가 by 박명현 2016-08-22)
function sql_connect($host, $user, $pass, $db=HF_MYSQL_DB)
{
	global $g5;

	$db_port = '';

	//프록시 서버 10.22.160.28

	if( in_array($host, array('10.22.160.28','211.56.4.58')) ) {
		$db_port = '6033';
	}
	else {
		$db_port = '3306';
	}

	if(function_exists('mysqli_connect') && HF_MYSQLI_USE) {
		$link = mysqli_connect($host, $user, $pass, $db, $db_port);

		// 연결 오류 발생 시 스크립트 종료
		if(mysqli_connect_errno()) {
			die('Connect Error: '.mysqli_connect_error());
		}
	}
	else {
		$link = mysql_connect($host, $user, $pass);
	}
	return $link;
}

function sql_close($link='') {
	global $hf;

	if(!$link) $link = $hf['connect_db'];

	if(function_exists('mysqli_close') && HF_MYSQLI_USE) {
		mysqli_close($link);
	}
	else {
		mysql_close($link);
	}
}

// DB 선택
function sql_select_db($db, $connect)
{
	if(function_exists('mysqli_select_db') && HF_MYSQLI_USE)
		return @mysqli_select_db($connect, $db);
	else
		return @mysql_select_db($db, $connect);
}

function sql_set_charset($charset, $link=null)
{
	global $hf;

	if(!$link)
		$link = $hf['connect_db'];

	if(function_exists('mysqli_set_charset') && HF_MYSQLI_USE)
		mysqli_set_charset($link, $charset);
	else

	mysql_query(" set names {$charset} ", $link);
}

function sql_query($sql, $error=TRUE, $link=null)
{

	global $hf;

	if(!$link)
		$link = $hf['connect_db'];

	// Blind SQL Injection 취약점 해결
	$sql = trim($sql);

	// union의 사용을 허락하지 않습니다.
	//$sql = preg_replace("#^select.*from.*union.*#i", "select 1", $sql);
	$sql = preg_replace("#^select.*from.*[\s\(]+union[\s\)]+.*#i ", "select 1", $sql);

	// `information_schema` DB로의 접근을 허락하지 않습니다.
	$sql = preg_replace("#^select.*from.*where.*`?information_schema`?.*#i", "select 1", $sql);

	if(function_exists('mysqli_query') && HF_MYSQLI_USE) {

		//if($_SERVER['REMOTE_ADDR']=='220.117.134.164') $error = 1;
		if($error) {
			$result = @mysqli_query($link, $sql) or die("<p>$sql<p>" . mysqli_errno($link) . " : " .  mysqli_error($link) . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
		}
		else {
			$result = @mysqli_query($link, $sql);
		}
	}
	else {
		if($error) {
			$result = @mysql_query($sql, $link) or die("<p>$sql<p>" . mysql_errno() . " : " .  mysql_error() . "<p>error file : {$_SERVER['SCRIPT_NAME']}");
		}
		else {
			$result = @mysql_query($sql, $link);
		}
	}

	return $result;
}

// 쿼리를 실행한 후 결과값에서 한행을 얻는다.
function sql_fetch($sql, $error=TRUE, $link=null)
{
	global $hf;

	if(!$link)
		$link = $hf['connect_db'];

	$result = sql_query($sql, $error, $link);
	//$row = @sql_fetch_array($result) or die("<p>$sql<p>" . mysqli_errno() . " : " .  mysqli_error() . "<p>error file : $_SERVER['SCRIPT_NAME']");
	$row = sql_fetch_array($result);
	return $row;
}

// 결과값에서 한행 연관배열(이름으로)로 얻는다.
function sql_fetch_array($result)
{
	if(function_exists('mysqli_fetch_assoc') && HF_MYSQLI_USE)
		$row = @mysqli_fetch_assoc($result);
	else
		$row = @mysql_fetch_assoc($result);

	return $row;
}

// $result에 대한 메모리(memory)에 있는 내용을 모두 제거한다.
// sql_free_result()는 결과로부터 얻은 질의 값이 커서 많은 메모리를 사용할 염려가 있을 때 사용된다.
// 단, 결과 값은 스크립트(script) 실행부가 종료되면서 메모리에서 자동적으로 지워진다.
function sql_free_result($result)
{
	if(function_exists('mysqli_free_result') && HF_MYSQLI_USE)
		return mysqli_free_result($result);
	else
		return mysql_free_result($result);
}


function sql_password($value)
{
	// mysql 4.0x 이하 버전에서는 password() 함수의 결과가 16bytes
	// mysql 4.1x 이상 버전에서는 password() 함수의 결과가 41bytes
	$row = sql_fetch(" select password('$value') as pass ");

	return $row['pass'];
}


function sql_insert_id($link=null)
{
	global $hf;

	if(!$link)
		$link = $hf['connect_db'];

	if(function_exists('mysqli_insert_id') && HF_MYSQLI_USE)
		return mysqli_insert_id($link);
	else
		return mysql_insert_id($link);
}

function sql_num_rows($result)
{
	if(function_exists('mysqli_num_rows') && HF_MYSQLI_USE)
		return mysqli_num_rows($result);
	else
		return mysql_num_rows($result);
}


function sql_affected_rows($link=null)
{
	global $g5;

	$link = (!$link) ? $g5['connect_db'] : $link;

	if(function_exists('mysqli_affected_rows') && G5_MYSQLI_USE)
		return mysqli_affected_rows($link);
	else
		return mysql_affected_rows();
}


function query($query){
	global $g5;

	$result = mysqli_query($g5['connect_db'],$query);
	return $result ;
}

// 동일한 host url 인지
function check_url_host($url, $msg='', $return_url=G5_URL)
{
    if(!$msg)
        $msg = 'url에 타 도메인을 지정할 수 없습니다.';

    $p = @parse_url($url);
    $host = preg_replace('/:[0-9]+$/', '', $_SERVER['HTTP_HOST']);

    //20170508 오픈 리다이렉트 취약점(16-603) 수정 (v 5.2.6)
    if(stripos($url, 'http:') !== false) {
        if(!isset($p['scheme']) || !$p['scheme'] || !isset($p['host']) || !$p['host'])
            alert('url 정보가 올바르지 않습니다.', $return_url);
    }

    if((isset($p['scheme']) && $p['scheme']) || (isset($p['host']) && $p['host'])) {
        //if($p['host'].(isset($p['port']) ? ':'.$p['port'] : '') != $_SERVER['HTTP_HOST']) {
        if($p['host'] != $host) {
            echo '<script>'.PHP_EOL;
            echo 'alert("url에 타 도메인을 지정할 수 없습니다.");'.PHP_EOL;
            echo 'document.location.href = "'.$return_url.'";'.PHP_EOL;
            echo '</script>'.PHP_EOL;
            echo '<noscript>'.PHP_EOL;
            echo '<p>'.$msg.'</p>'.PHP_EOL;
            echo '<p><a href="'.$return_url.'">돌아가기</a></p>'.PHP_EOL;
            echo '</noscript>'.PHP_EOL;
            exit;
        }
    }
}


// 문자열 암복호화
class str_encrypt
{
    var $salt;
    var $lenght;

    function __construct($salt='')
    {
        if(!$salt)
            $this->salt = md5(G5_MYSQL_PASSWORD);
        else
            $this->salt = $salt;

        $this->length = strlen($this->salt);
    }

    function encrypt($str)
    {
        $length = strlen($str);
        $result = '';

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) + ord($keychar));
            $result .= $char;
        }

        return base64_encode($result);
    }

    function decrypt($str) {
        $result = '';
        $str    = base64_decode($str);
        $length = strlen($str);

        for($i=0; $i<$length; $i++) {
            $char    = substr($str, $i, 1);
            $keychar = substr($this->salt, ($i % $this->length) - 1, 1);
            $char    = chr(ord($char) - ord($keychar));
            $result .= $char;
        }

        return $result;
    }
}


function price_cutting($value, $text_only = false) {

	$unit_price    = 10000;		// 변환 최소단위
	$million_value = 0;
	$reail_value   = 0;

	if($value > 0) {
		$value = floor(floor($value) / $unit_price) * $unit_price;

		if($value >= 100000000) {
			$million_value = floor($value / 100000000);
			$return_str = $million_value.'억';
			$value = $value - ($million_value * 100000000);
			$reail_value = ($million_value * 100000000);
		}

		if($value > 0) {
			$value = floor($value / $unit_price);
			$reail_value = $reail_value+ ($value * $unit_price);
			$return_str =  $return_str . number_format($value) .'만';
		}
		else {
			if($return_str == '') $return_str = '0';
		}
	}
	else {
		$return_str = '0' ;
	}

	return $return_str;

}

function number2korean($num) {
  $return_val = "";
  if(!is_numeric($num)) {
		echo "<script>alert('유효한 숫자가 아닙니다')</script>";
		return 0;
  }

  $arr_number = strrev($num);

  for($i=strlen($arr_number)-1; $i>=0; $i--) {
		/////////////////////////////////////////////////
		// 현재 자리를 구함
		$digit = substr($arr_number, $i, 1);
		///////////////////////////////////////////////////////////
		// 각 자리 명칭
		switch($digit) {
			case '-' : $return_val.= "(-) "; break;
			case '0' : $return_val.= "";     break;
			case '1' : $return_val.= "일";   break;
			case '2' : $return_val.= "이";   break;
			case '3' : $return_val.= "삼";   break;
			case '4' : $return_val.= "사";   break;
			case '5' : $return_val.= "오";   break;
			case '6' : $return_val.= "육";   break;
			case '7' : $return_val.= "칠";   break;
			case '8' : $return_val.= "팔";   break;
			case '9' : $return_val.= "구";   break;
		}

		if($digit=="-") continue;

		///////////////////////////////////////////////////////////
		// 4자리 표기법 공통부분
		if($digit != 0) {
			if($i % 4 == 1)      $return_val.= "십";
			else if($i % 4 == 2) $return_val.= "백";
			else if($i % 4 == 3) $return_val.= "천";
		}

		///////////////////////////////////////////////////////////
		// 4자리 한자 표기법 단위
		if($i % 4 == 0) {
			if(floor($i / 4)==12) $return_val.= "극";
			else if(floor($i / 4)==11) $return_val.= "재";
			else if(floor($i / 4)==10) $return_val.= "정";
			else if(floor($i / 4)==9)  $return_val.= "간";
			else if(floor($i / 4)==8)  $return_val.= "구";
			else if(floor($i / 4)==7)  $return_val.= "양";
			else if(floor($i / 4)==6)  $return_val.= "자";
			else if(floor($i / 4)==5)  $return_val.= "해";
			else if(floor($i / 4)==4)  $return_val.= "경";
			else if(floor($i / 4)==3)  $return_val.= "조";
			else if(floor($i / 4)==2)  $return_val.= "억";
			else if(floor($i / 4)==1)  $return_val.= "만";
			else if(floor($i / 4)==0)  $return_val.= "";
		}
	}

	//$return_val = preg_replace("/조 만/", "조", $return_val);
	$return_val = preg_replace("/극만/", "극", $return_val);
	$return_val = preg_replace("/재만/", "재", $return_val);
	$return_val = preg_replace("/정만/", "정", $return_val);
	$return_val = preg_replace("/간만/", "간", $return_val);
	$return_val = preg_replace("/구만/", "구", $return_val);
	$return_val = preg_replace("/양만/", "양", $return_val);
	$return_val = preg_replace("/자만/", "자", $return_val);
	$return_val = preg_replace("/해만/", "해", $return_val);
	$return_val = preg_replace("/경만/", "경", $return_val);
	$return_val = preg_replace("/조만/", "조", $return_val);
	$return_val = preg_replace("/억만/", "억", $return_val);
	//if(preg_match("/^일만/",$return_val)) $return_val = preg_replace("/일만/", "만", $return_val);
	return $return_val;

}



function aes256Encrypt($key, $data) {
	if(32 !== strlen($key)) { $key = hash('MD5', $key, true); }
	return base64_encode(openssl_encrypt($data, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));
}

function aes256Decrypt($key, $data) {
  if(32 !== strlen($key)) { $key = hash('MD5', $key, true); }
	return openssl_decrypt(base64_decode($data), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
}



/*=============================================================================
   strtotime 보정 함수
  =============================================================================*/
function strtotimeMonth($sTime, $iTime=null) {

	if(is_null($iTime) === TRUE || is_int($iTime) === FALSE) {
		$iTime = time();
	}

	$sTransDay = date('d', $iTime);
	$sLastDay  = date('t', $iTime);

	if($sTransDay == $sLastDay) {
		$iResTime = strtotime('last day of ' . $sTime, $iTime);
	} else {
		$iResTime = strtotime($sTime, $iTime);
	}

	return $iResTime;

}

// 모바일 더보기 기능, 한페이지에 보여줄 행, 현재페이지, 총페이지수, URL
function m_get_paging($write_pages, $cur_page, $total_page)
{
    global $aslang;

    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $html = "";

    // 마지막 페이지 구하기
    // $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    // $end_page = $start_page + $write_pages - 1;

    if($total_page > 1)
    {
        // 다음
        if($cur_page < $total_page) {
            $html = '<button type="button" name="more_list" class="m_more_list" data-target="'.($cur_page+1).'">더보기 ('.$cur_page.'/'.$total_page.')</button>';
        }
    }

    return $html;
}

// html 제거
function html_clean($str)
{
    $str = str_replace("&nbsp;", " ", $str);
    $str = preg_replace('/\s+/', ' ',$str);
    $str = trim($str);
    return $str;
}


// 상품 상태값 출력 (파라미터값 기준)
function get_product_state($recruit_period_start, $recruit_period_end, $product_open_date, $product_invest_sdate, $product_invest_edate, $state, $recruit_amount, $total_invest_amount, $invest_end_date){

	$date     = date('Ymd');
	$datetime = $date.date('His');

	if(str_replace("-", "", $recruit_period_start) <= $date && str_replace("-", "", $recruit_period_end) >= $date) {			//모집기간중

		if($product_open_date > $datetime) {
			$product_state = "투자대기중";
		}
		else {

			if($state) {
				switch($state) {
					case '1' : $product_state = '이자상환중';    break;
					case '2' : $product_state = '투자상환완료';  break;	//상품마감
					case '3' : $product_state = '투자금모집실패';break;
					case '4' : $product_state = '부실';		      break;
					case '5' : $product_state = '중도상환완료';	  break;
					case '6' : $product_state = '대출계약취소';	  break;	//대출취소(기표전)
				}
			}
			else {

				if($product_invest_sdate < $datetime && $product_invest_edate > $datetime) {
					$product_state = '투자모집중';
				}

				if($recruit_amount <= $total_invest_amount) {
					$product_state = '투자마감';
				}

				if($product_invest_edate < $datetime) {
					$product_state = '투자마감';
				}
				else if($invest_end_date && $state=='') {
					$product_state = '투자마감';
				}

			}

		}
	}
	else{	 //모집기간 종료후

		if($state) {
			switch($state) {
				case '1' : $product_state = '이자상환중';			break;
				case '2' : $product_state = '투자상환완료';		break;	//상품마감
				case '3' : $product_state = '투자금모집실패';	break;
				case '4' : $product_state = '부실';						break;
				case '5' : $product_state = '중도상환완료';		break;
				case '7' : $product_state = '대출계약취소';		break;	//대출취소(기표후)
			}
		}
		else {
			if($recruit_amount <= $total_invest_amount) {
				$product_state = '투자마감';
			}

			if($product_invest_edate < $datetime) {
				$product_state = '투자마감';
			}
			else if($invest_end_date && $state == '') {
				$product_state = '투자마감';
			}
		}

	}
	return $product_state;
}


/**
 * 투자상품 상태값 찾기
 * @param string $state 진행현황
 * @param string $open_datetime 상품출력시작일시
 * @param string $invest_end_date 투자모집완료일
 * @param string $start_datetime 투자모집시작일시
 * @param string $end_datetime 투자모집만료일시
 * @param int $recruit_amount 모집금액(대출금액)
 * @param int $amount 투자금액 (cf_product_invest)
 * @return bool
 */
function getProductStat($prd_idx) {

	###################################
	## 리턴 상태코드(code) 예시 :
	## A01 : 이자상환중
	## A02 : 투자상환완료 (상품마감)
	## A03 : 투자모집실패
	## A04 : 부실(매각협의중)
	## A05 : 중도일시상환
	## A06 : 대출취소(기표전)
	## A07 : 대출취소(기표후)
	## A08 : 상환지연(연체)
	## A09 : 상환불가(부도)
	## B00 : 상품준비중
	## B01 : 투자대기중
	## B02 : 투자모집중
	## B03 : 투자모집완료
	## B04 : 투자모집실패
	###################################

	if(!$prd_idx) return;

	$sql = "
		SELECT
			A.state, A.title, A.recruit_amount,
			A.recruit_period_start, A.recruit_period_end, A.open_datetime, A.start_datetime, A.end_datetime, A.invest_end_date,
			A.advance_invest, A.advance_invest_ratio,
			( SELECT SUM(amount) FROM cf_product_invest WHERE product_idx=A.idx AND invest_state='Y' ) AS total_invest_amount
		FROM
			cf_product A
		WHERE
			idx='$prd_idx'";

	//$RESULT = array();

	if( $PRDT = sql_fetch($sql) ) {

		$RESULT = array(
			'code' => '',
			'code_str' => '',
			'advence_invest_ing' => '',
			'state' => '',
			'title' => ''
		);

		$nowdate = date('Y-m-d H:i:s');

		if($PRDT['state']) {
			if($PRDT['state']=='1')      {
				if( in_array($prd_idx, array('144','145','146')) && date('Y-m-d')>='2018-01-09') {		// 가평 상환지연중 표기 : 2018-01-08
					$code_str = "상환지연중";
				}
				else {
					$code_str = "이자상환중";
				}
				$RESULT['code']     = 'A01';
				$RESULT['code_str'] = $code_str;
			}
			else if($PRDT['state']=='2') {
				$RESULT['code']     = 'A02';
				$RESULT['code_str'] = '상환완료';
			}
			else if($PRDT['state']=='3') {
				$RESULT['code']     = 'A03';
				$RESULT['code_str'] = '모집실패';
			}
			else if($PRDT['state']=='4') {
				$RESULT['code']     = 'A04';
				$RESULT['code_str'] = '부실';
			}
			else if($PRDT['state']=='5') {
				$RESULT['code']     = 'A05';
				$RESULT['code_str'] = '중도상환완료';
			}
			else if($PRDT['state']=='6') {
				$RESULT['code']     = 'A06';
				$RESULT['code_str'] = '투자금반환완료';
			}
			else if($PRDT['state']=='7') {
				$RESULT['code']     = 'A07';
				$RESULT['code_str'] = '대출취소';
			}
			else if($PRDT['state']=='8') {
				$RESULT['code']     = 'A08';
				$RESULT['code_str'] = '상환지연중';
			}
			else if($PRDT['state']=='9') {
				$RESULT['code']     = 'A09';
				$RESULT['code_str'] = '상환불가';
			}
		}
		else {
			// "확정된 투자진행상태값이 없을 경우", 투자만료기록일 시점의 상태를 반환한다.
			if($PRDT['invest_end_date']!='') {
				if($PRDT['recruit_amount'] > $PRDT['total_invest_amount']) {
					$RESULT['code']     = 'B04';
					$RESULT['code_str'] = '투자모집실패';
				}
				else {
					$RESULT['code']     = 'B03';
					$RESULT['code_str'] = '투자모집완료';
				}
			}
			else {
				// 모집기간 전
				if($PRDT['start_datetime'] > $nowdate) {

					$RESULT['code']     = 'B01';
					$RESULT['code_str'] = '투자대기중';

					// 사전투자상품 -> 사전투자금이 다 모이지 않았을 경우
					if($PRDT['advance_invest']=='Y' && $PRDT['star_datetime'] <= $nowdate) {

						$advance_invest_amount = $PRDT['recruit_amount'] * $PRDT['advance_invest_ratio'] / 100;

						if($PRDT['total_invest_amount'] < $advance_invest_amount) {
							$RESULT['advence_invest_ing'] = 'Y';
						}

					}

				}
				// 모집기간 중
				else if($PRDT['start_datetime'] <= $nowdate && $PRDT['end_datetime'] >= $nowdate) {
					if($PRDT['recruit_amount'] > $PRDT['total_invest_amount']) {
						$RESULT['code']     = 'B02';
						$RESULT['code_str'] = '투자모집중';
					}
					else {
						$RESULT['code']     = 'B03';
						$RESULT['code_str'] = '투자모집완료';
					}
				}
				// 모집기간 후
				else if($PRDT['end_datetime'] < $nowdate) {
					if($PRDT['recruit_amount'] > $PRDT['total_invest_amount']) {
						$RESULT['code']     = 'B04';
						$RESULT['code_str'] = '투자모집실패';
					}
					else {
						$RESULT['code']     = 'B03';
						$RESULT['code_str'] = '투자모집완료';
					}
				}
				// 모집기간이 설정되지 않은 경우
				else {
					$RESULT['code']     = 'B00';
					$RESULT['code_str'] = '상품준비중';
				}
			}

		}

		$RESULT['state']          = $PRDT['state'];
		$RESULT['title']          = $PRDT['title'];
		//$RESULT['recruit_amount'] = $PRDT['recruit_amount'];
		//$RESULT['invest_count']   = $PRDT['invest_count'];
		//$RESULT['invest_amount']  = $PRDT['invest_amount'];

		return $RESULT;

	}
	else {
		return 0;
	}

}


function paging($total, $page, $size, $ppb=5) {
	if($total == 0) return;

	$total_page = ceil($total / $size);
	$temp = $page % $ppb;

	if($temp == 0) {
		$a = $ppb - 1;
		$b = $temp;
	}
	else {
		$a = $temp - 1;
		$b = $ppb - $temp;
	}

	$start = $page - $a;
	$end = $page + $b;
	//echo "<ul>\n";

	//처음페이지
	if($page > $ppb) {
		echo "	<span class='arrow btn_paging' data-page='1'><img src='/images/bbs/btn_first.gif' alt='맨앞'></span>\n";
	}

	//이전페이지
	if($page > $ppb) {
		$back_page = $start - 1;
		echo "	<span class='arrow btn_paging' data-page='".$back_page."'><img src='/images/bbs/btn_prev.gif' alt='이전'></span>\n";
	}

	//페이지 출력
	for($i = $start; $i <= $end; $i++) {
		if($i > $total_page) break;
		if($page == $i){
			echo "	<span class='now'>".$i."</span>\n";
		}
		else{
			echo "	<span class='btn_paging' data-page='".$i."'>".$i."</span>\n";
		}
	}

	//다음페이지
	if($end < $total_page) {
		$next_page = $end + 1;
		echo "	<span class='arrow btn_paging' data-page='".$next_page."'><img src='/images/bbs/btn_next.gif' alt='이전'></span>\n";
	}

	//마지막 페이지
	if($end < $total_page) {
		echo "	<span class='arrow btn_paging' data-page='".$total_page."'><img src='/images/bbs/btn_last.gif' alt='맨뒤'></span>\n";
	}

	//echo "</ul>\n";

}


// 소수점 자리수 끊기 (자리수 이하 버림)
function floatCutting($n, $commaUnderLength=0) {
	if(is_numeric($n)) {
		if(preg_match('/./', $n)) {
			$N = explode('.', $n);
			$value = $N[0];

			if($commaUnderLength) {
				for($i=0,$j=1; $i<strlen($N[1]);$i++,$j++) {
					if($i==0) $value.= '.';
					if($j <= $commaUnderLength) {
						$value.= substr($N[1], $i, 1);
					}
					else break;
				}
			}
		}
		else {
			$value = $n;
		}
	}
	return $value;
}

// 소수점 이하 0으로 끝나는 수 버림
function floatRtrim($number) {
	if($number > 0) {
		if(preg_match('/\./', $number)) {
			$_number = rtrim($number, "0");
			$locale_info = localeconv();
			$return_number = rtrim($_number, $locale_info['decimal_point']);
		}
		else {
			$return_number = $number;
		}
	}
	else {
		$return_number = 0;
	}
	return $return_number;
}


// XSS 관련 태그 제거
function clean_xss_tags($str)
{
	$str = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $str);

	return $str;
}

/*=============================================================================
  플랫폼 가져오기 (getPlatForm -> getDevice 로 대체할것)
 =============================================================================*/
function getDevice($user_agent = '') {

	if(!$user_agent) $user_agent = $_SERVER['HTTP_USER_AGENT'];

	if(preg_match("/(ipad|android\s3\.0|xoom|sch-i800|playbook|tablet|kindle\/i.test(window.navigator.userAgent.toLowerCase()))/i", $user_agent)) {
		$device = "TABLET";
	}
	else if(preg_match("/(iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile)/i", $user_agent)) {
		$device = "MOBILE";
	}
	else {
		$device = "PC";
	}
	return $device;
}


///////////////////////////////////////////////////////////////////////////////
// [투자수익현황 보기]
// 상품정보(PRDT), 이자및상환스케쥴(REPAY) 및 상환총액정보(REPAYSUM) 추출
// DB 등록데이터 기준. 배열로 출력
// 투자 시뮬레이션 및 투자현황용
///////////////////////////////////////////////////////////////////////////////
function investStatement($product_idx, $principal, $loan_start_date='', $loan_end_date='', $invest_idx='') {

	// 투자자플랫폼이용료[(투자금액 * 0.08%) * 차수 (A:월별징수, B:상환시징수)] 타입에 따른 플랫폼 요율 적용시점 작업예정
	// 중도상환시 월수계산, 일수계산 처리기능 부족

	global $CONF, $BANK, $VBANK, $member;

	$prdt_query = "
		SELECT
			idx, state, category, title,
			invest_return, withhold_tax_rate, loan_interest_rate, overdue_rate, withhold_tax_rate, loan_usefee, invest_usefee, invest_usefee_type,
			invest_period, invest_days, recruit_period_start, recruit_period_end, recruit_amount,
			repay_type, advanced_payment, loan_start_date, loan_end_date, loan_end_date_orig
		FROM
			cf_product
		WHERE
			idx = '".$product_idx."' ";
	$PRDT = sql_fetch($prdt_query);


	$INI['principal'] = $principal;  // 투자원금
	$INI['static_repay_day'] = 5;   // 약정정산일

	if($PRDT['loan_start_date'] > '0000-00-00') {
		$INI['loan_start_date'] = $PRDT['loan_start_date'];
	}
	else {
		$INI['loan_start_date'] = ($loan_start_date) ? $loan_start_date : date('Y-m-d');
	}

	$INI['loan_start_date_day'] = (int)substr($INI['loan_start_date'], 8, 2);								// 대출실행일의 일자

	$PRDT['invest_return']   = ($PRDT['invest_return']) ? $PRDT['invest_return'] : 0;				// 투자수익율
	$PRDT['invest_usefee']   = ($PRDT['invest_usefee']) ? $PRDT['invest_usefee'] : 0;				// 투자자 플랫폼 이용요율

	$SDATE_OBJ = new DateTime($INI['loan_start_date']);

	if($PRDT['invest_days'] > 0) $PRDT['loan_end_date'] = date('Y-m-d', strtotime($INI['loan_start_date'] . '+ ' . $PRDT['invest_days'] . 'day'));

	if($PRDT['loan_end_date'] > '0000-00-00') {
		// 종료일이 확정된 상품일 경우
		$EDATE_OBJ = new DateTime($PRDT['loan_end_date']);
	}
	else {
		// 종료일 미정인 상품일 경우
		if($PRDT['invest_period']==1 && $PRDT['invest_days']>0) {
			$EDATE_OBJ = new DateTime(date('Y-m-d', strtotime($INI['loan_start_date'].' +'.$PRDT['invest_days'].' day')));
		}
		else {
			$EDATE_OBJ = new DateTime(date('Y-m-d', strtotime($INI['loan_start_date'].' +'.$PRDT['invest_period'].' month')));
		}
	}

	$TOTAL_DATE_OBJ = date_diff($SDATE_OBJ, $EDATE_OBJ);

	$INI['total_day_count']     = $TOTAL_DATE_OBJ->days;
	$INI['loan_end_date']				= $EDATE_OBJ->format('Y-m-d');

	$INI['day_invest_interest'] = ($INI['principal'] * ($PRDT['invest_return']/100)) / 365;		// 일별 이자수익금
	$INI['day_invest_usefee']   = ($INI['principal'] * ($PRDT['invest_usefee']/100)) / 365;		// 일별 플랫폼이용료

	$withhold_tax_rate = ($member['is_credit'] == 'Y') ? 0 : sprintf("%0.3f", $PRDT['withhold_tax_rate']/100);

	//print_rr($INI, 'font-size:12px');

	if($PRDT['invest_period']==1 && $PRDT['invest_days']>0) {

		$INI['repay_count'] = 1;														// 정산 회차 추출

		$REPAY[0]['repay_day']    = $INI['loan_end_date'];
		$REPAY[0]['target_sdate'] = $INI['loan_start_date'];
		$REPAY[0]['target_edate'] = $INI['loan_end_date'];
		$REPAY[0]['day_count']	  = $INI['total_day_count'];
		$REPAY[0]['principal']    = $INI['principal'];

	}
	else {

		$INI['repay_count'] = $PRDT['invest_period'] + 1;		// 정산 회차 추출

		$x = 0;
		for($i=0,$j=1; $i<$INI['repay_count']; $i++,$j++) {

			$REPAY[$x]['repay_num'] = $x+1;

			$EDATE_OBJ = new DateTime(date('Y-m-d', strtotime($SDATE_OBJ->format('Y-m').' last day next month')));	// 매 정산월의 마지막 일자
			$DIFF_OBJ  = date_diff($SDATE_OBJ, $EDATE_OBJ);

			if($EDATE_OBJ->format('Y-m-d') < $INI['loan_end_date']) {
				$repay_day = $SDATE_OBJ->format('Y-m').'-'.sprintf('%02d', $INI['static_repay_day']);
				$repay_day = date('Y-m-d', strtotime($repay_day.' +1 month'));

				$REPAY[$x]['repay_day']    = $repay_day;											// 정산지급일
				$REPAY[$x]['target_sdate'] = $SDATE_OBJ->format('Y-m-d');			// 정산시작일
				$REPAY[$x]['target_edate'] = $EDATE_OBJ->format('Y-m-d');			// 정산종료일
				$REPAY[$x]['day_count']    = $DIFF_OBJ->days + 1;							// 일자수
				$REPAY[$x]['principal']    = 0;																// 상환원금
				$SDATE_OBJ->modify('first day of next month');

				$x++;

			}
			else {

				//마지막 달 계산
				$LOAN_DATE_OBJ    = new DateTime($INI['loan_end_date']);
				$DIFF_OBJ         = date_diff($SDATE_OBJ, $LOAN_DATE_OBJ);
				$repay_day        = $LOAN_DATE_OBJ->format('Y-m-d');
			//$repay_day        = date("Y-m-d", strtotime("-1 day", strtotime($repay_day)));
				$static_repay_day = substr($repay_day, 0, 7)."-".sprintf("%02d", $INI['static_repay_day']);
			//$static_repay_day = $LOAN_DATE_OBJ->format('Y-m')."-".sprintf("%02d", $INI['static_repay_day']);

/*
				if($repay_day <= $static_repay_day) {			// 최종상환일이 약정정산지급일 보다 앞에 있거나 같을 경우 (1~5일)
					if($PRDT['loan_end_date'] < $PRDT['loan_end_date_orig']) {
						$REPAY[$x-1]['repay_day']    = $repay_day;
					//$REPAY[$x-1]['target_sdate'] = $SDATE_OBJ->format('Y-m-d');
						$REPAY[$x-1]['target_edate'] = $repay_day;
						$REPAY[$x-1]['day_count']    = $REPAY[$x-1]['day_count'] + $DIFF_OBJ->days;
						$REPAY[$x-1]['principal']    = $INI['principal'];
						unset($REPAY[$x]);
						break;
					}
					else {
						$REPAY[$x]['repay_day']    = $repay_day;
						$REPAY[$x]['target_sdate'] = $SDATE_OBJ->format('Y-m-d');
						$REPAY[$x]['target_edate'] = $repay_day;
					//$REPAY[$x]['target_edate'] = $EDATE_OBJ->format('Y-m-d');
						$REPAY[$x]['day_count']	   = $DIFF_OBJ->days;
						$REPAY[$x]['principal']    = $INI['principal'];
					}
				}
				else {			// 최종상환일이 약정정산지급일 보다 뒤에 있을 경우 (6~31일)
*/
					$REPAY[$x]['repay_day']    = $repay_day;
					$REPAY[$x]['target_sdate'] = $SDATE_OBJ->format('Y-m-d');
					$REPAY[$x]['target_edate'] = $repay_day;
				//$REPAY[$x]['target_edate'] = $EDATE_OBJ->format('Y-m-d');
					$REPAY[$x]['day_count']	   = $DIFF_OBJ->days;
					$REPAY[$x]['principal']    = $INI['principal'];
/*
				}
*/
			}
		}

	}

	/////////////////////////
	// 투자 성패기록 추출
	/////////////////////////
	$INI['repay_count'] = count($REPAY);
	for($x=0,$y=1; $x<$INI['repay_count']; $x++,$y++) {

		////////////////////////////////////////////////
		// 전송된 $invest_idx 가 있으면 지급기록 조회
		////////////////////////////////////////////////
		if($invest_idx) {
			$give_sql = "
				SELECT
					idx, `date`, invest_amount, interest, principal, is_creditor, remit_fee, receive_method, bank_name, account_num, bank_private_name, banking_date, mgtKey
				FROM
					cf_product_give
				WHERE 1
					AND invest_idx='".$invest_idx."'
					AND product_idx='".$PRDT['idx']."'
					AND turn='$y'
					AND is_overdue='N'";
			$GIVE = sql_fetch($give_sql);
		}

		$REPAY[$x]['paied'] = ($GIVE['idx']) ? 'Y' : 'N';
		$REPAY[$x]['remit_fee'] = ($GIVE['remit_fee']=='1') ? $GIVE['remit_fee'] : $member['remit_fee'];

		if($REPAY[$x]['paied']=="Y") {
			$REPAY[$x]['paied_date']        = $GIVE['date'];
			$REPAY[$x]['give_idx']          = $GIVE['idx'];
			$REPAY[$x]['mgtKey']            = $GIVE['mgtKey'];
			$REPAY[$x]['is_creditor']       = $GIVE['is_creditor'];
			$REPAY[$x]['receive_method']	  = $GIVE['receive_method'];
			$REPAY[$x]['bank_name']			    = $GIVE['bank_name'];
			$REPAY[$x]['account_num']       = $GIVE['account_num'];
			$REPAY[$x]['bank_private_name'] = $GIVE['bank_private_name'];
			$REPAY[$x]['banking_date']      = $GIVE['banking_date'];
		}
		else {
			$REPAY[$x]['paied_date']     = '';
			$REPAY[$x]['give_idx']       = '';
			$REPAY[$x]['mgtKey']         = '';
			$REPAY[$x]['is_creditor']    = $member['is_creditor'];
			$REPAY[$x]['receive_method'] = $member['receive_method'];
			if($REPAY[$x]['receive_method']=='1') {
				$REPAY[$x]['bank_name']         = $member['bank_name'];
				$REPAY[$x]['account_num']       = $member['account_num'];
				$REPAY[$x]['bank_private_name'] = $member['bank_private_name'];
			}
			else if($REPAY[$x]['receive_method']=='2') {
				$REPAY[$x]['bank_name']         = $BANK[$member['va_bank_code2']];
				$REPAY[$x]['account_num']       = $member['virtual_account2'];
				$REPAY[$x]['bank_private_name'] = $member['va_private_name2'];
			}
			else {
				$REPAY[$x]['bank_name']         = "";
				$REPAY[$x]['account_num']       = "";
				$REPAY[$x]['bank_private_name'] = "";
			}
			$REPAY[$x]['banking_date'] = "";
		}


		$REPAY[$x]['invest_interest'] = floor($INI['day_invest_interest'] * $REPAY[$x]['day_count']);		// 투자수익(세전) -> 소수점이하 잘라냄


		////////////////////////////////////////////
		// 일별 플랫폼이용료 설정 (365일 기준, 예외설정사항을 최우선으로 적용)
		////////////////////////////////////////////
		$EXTFEE_ROW = sql_fetch("SELECT idx, fee FROM cf_platform_fee WHERE member_idx='".$member['mb_no']."' AND product_idx='".$PRDT['idx']."'");
		if($EXTFEE_ROW['idx']) {
			$INI['day_invest_usefee']  = ($INI['principal'] * ($EXTFEE_ROW['fee']/100)) / 365;
		}
		else {
			$INI['day_invest_usefee'] = ($REPAY[$x]['remit_fee']=='1') ? 0 : ($INI['principal'] * ($PRDT['invest_usefee']/100)) / 365;		// 플랫폼 수수료 면제 대상자처리 -> 일별 플랫폼 수수료를 0으로 설정
		}

		if($PRDT['invest_usefee_type']=='A') {
			// 투자자플랫폼이용료(분할징수) :::: 일별플랫폼이용료 * 일자수
			$REPAY[$x]['invest_usefee'] = floor($INI['day_invest_usefee'] * $REPAY[$x]['day_count']);	 // 소수점이하 절사
		}
		else {
			// 투자자플랫폼이용료(만기일시징수)
			if($y==$INI['repay_count']) {
				$REPAY[$x]['invest_usefee'] = floor($INI['day_invest_usefee'] * $INI['total_day_count']);	// 소수점이하 절사
			}
			else {
				$REPAY[$x]['invest_usefee'] = 0;
			}
		}


		// 원천징수액 계산 (대부업 회원 일때 원천징수 제로 처리)
		if($REPAY[$x]['is_creditor']=="Y") {
			$REPAY[$x]['interest_income_tax'] = 0;																																					// 이자소득세 => 0
			$REPAY[$x]['local_income_tax']    = 0;																																					// 지방소득세 => 0
		}
		else {
			$REPAY[$x]['interest_income_tax'] = floor( ($REPAY[$x]['invest_interest'] * 0.25) / 10 ) * 10;									// 이자소득세 => 이자수익 * 0.25 :::: 원단위 절사
		//$REPAY[$x]['interest_income_tax'] = floor( (($REPAY[$x]['invest_interest'] - $REPAY[$x]['invest_usefee']) * 0.25) / 10 ) * 10;			// 이자소득세 => 이자수익 - 플랫폼수수료 * 0.25 :::: 원단위 절사
			$REPAY[$x]['local_income_tax']    = floor( (($REPAY[$x]['interest_income_tax'] * 0.1) / 10) ) * 10;							// 당월 지방소득세 :::: 원단위 절사
		}

		$REPAY[$x]['withhold']   = $REPAY[$x]['interest_income_tax'] + $REPAY[$x]['local_income_tax'];										// 원천징수세액 = 이자소득세 + 지방소득세
		$REPAY[$x]['interest']   = $REPAY[$x]['invest_interest'] - $REPAY[$x]['withhold'] - $REPAY[$x]['invest_usefee'];	// 투자수익(세후) = 이자수익 - 세금 - 플랫폼이용료


		$REPAY[$x]['principal'] = ($y < $INI['repay_count']) ? 0 : $INI['principal'];			// 상환원금
		// 특수물건 상환원금 예외처리(171번 상품은 연체정산시 원금을 처리하도록 한다.)
		if($product_idx=='171') {
			$REPAY[$x]['principal'] = 0;
		}

		$REPAY[$x]['send_price'] = $REPAY[$x]['interest'] + $REPAY[$x]['principal'];																			// 실입금액(투자수익(세후) + 원금)


		$SUM['principal']          += $REPAY[$x]['principal'];
		$SUM['day_count']          += $REPAY[$x]['day_count'];
		$SUM['invest_interest']    += $REPAY[$x]['invest_interest'];																											// 전체 투자수익(세전)
		$SUM['invest_usefee']      += $REPAY[$x]['invest_usefee'];																												// 전체 플랫폼이용료
		$SUM['interest_income_tax']+= $REPAY[$x]['interest_income_tax'];																									// 전체 이자소득세
		$SUM['local_income_tax']   += $REPAY[$x]['local_income_tax'];																											// 전체 지방소득세
		$SUM['withhold']           += $REPAY[$x]['withhold'];																															// 전체 원천징수액
		$SUM['interest']           += $REPAY[$x]['interest'];																															// 전체 투자수익(세후)
		$SUM['send_price']         += $REPAY[$x]['send_price'];																														// 전체 실입금액


		/////////////////////////////////
		// 출력용 이자정산지급일 설정
		/////////////////////////////////
		$EXCEPTION_PRODUCT = array(94,95,97,98,109,111,117);
		if( $y < count($REPAY) ) {
			$REPAY[$x]['repay_schedule_date'] = $REPAY[$x]['repay_day'];
		}
		else {
			if(in_array($product_idx, $EXCEPTION_PRODUCT)) {
				$REPAY[$x]['repay_schedule_date'] = $INI['loan_end_date'];
			}
			else {

				$REPAY[$x]['repay_schedule_date'] = (in_array($product_idx, $EXCEPTION_PRODUCT)) ? $INI['loan_end_date'] : date("Y-m-d", strtotime("+5 day", strtotime($INI['loan_end_date'])));		// 최종정산일 +5일 적용시 (예외적용)

/*
				if( in_array($PRDT['state'], array('2','5','7')) ) {
					$REPAY[$x]['repay_schedule_date'] = $INI['loan_end_date']; //중도상환건은 최종정산일은 대출종료(상환)일과 동일하게 적용
				}
				else {
					$REPAY[$x]['repay_schedule_date'] = date("Y-m-d", strtotime("+5 day", strtotime($INI['loan_end_date'])));  // 최종정산일 - 대출종료(상환)일 +5일 적용
				}
*/
			}
		}


		$sql = "
			SELECT
				loan_interest_state, loan_principal_state, invest_give_state, invest_principal_give, overdue_receive, overdue_give,
				overdue_start_date, overdue_end_date
			FROM
				cf_product_success
			WHERE 1=1
				AND product_idx='".$PRDT['idx']."'
				AND turn='".$y."'";
		$SUCC = sql_fetch($sql);
		$REPAY[$x]['SUCCESS'] = $SUCC;

		if($SUCC['overdue_start_date']>'0000-00-00') {

			$ovd_day_invest_interest = ($INI['principal'] * ($PRDT['overdue_rate']/100)) / 365;
			$ovd_edate = ($SUCC['overdue_end_date']=='' || $SUCC['overdue_end_date']=='0000-00-00') ? G5_TIME_YMD : $SUCC['overdue_end_date'];

			$OVD_SDATE_OBJ = new DateTime($SUCC['overdue_start_date']);
			$OVD_EDATE_OBJ = new DateTime($ovd_edate);
			$OVD_TOTAL_DATE_OBJ = date_diff($OVD_SDATE_OBJ, $OVD_EDATE_OBJ);

			///////////////////////////////////
			// 연체이자 지급기록 추출 및 지급계좌 설정
			///////////////////////////////////
			$ovd_give_sql = "
				SELECT
					idx, `date`, invest_amount, interest, principal, is_creditor, remit_fee, receive_method, bank_name, account_num, bank_private_name, banking_date, mgtKey
				FROM
					cf_product_give
				WHERE 1
					AND invest_idx='".$invest_idx."'
					AND product_idx='".$PRDT['idx']."'
					AND turn='$y'
					AND is_overdue='Y'";
			$OVD_GIVE = sql_fetch($ovd_give_sql);


			$REPAY[$x]['OVERDUE']['repay_num']    = $y;
			$REPAY[$x]['OVERDUE']['target_sdate'] = $SUCC['overdue_start_date'];
			$REPAY[$x]['OVERDUE']['target_edate'] = $ovd_edate;
			$REPAY[$x]['OVERDUE']['day_count']    = $OVD_TOTAL_DATE_OBJ->days;
			$REPAY[$x]['OVERDUE']['principal']    = $OVD_GIVE['principal'];

			$REPAY[$x]['OVERDUE']['invest_interest'] = floor($ovd_day_invest_interest * $REPAY[$x]['OVERDUE']['day_count']);			// 투자수익(세전) -> 소수점이하 잘라냄
			$REPAY[$x]['OVERDUE']['invest_usefee']   = ($OVD_GIVE['remit_fee']=='1') ? 0 : floor($INI['day_invest_usefee'] * $REPAY[$x]['OVERDUE']['day_count']);			// 소수점이하 절사


			if($OVD_GIVE['idx']) {
				$REPAY[$x]['OVERDUE']['paied']               = 'Y';
				$REPAY[$x]['OVERDUE']['paied_date']          = $OVD_GIVE['date'];
				$REPAY[$x]['OVERDUE']['give_idx']            = $OVD_GIVE['idx'];
				$REPAY[$x]['OVERDUE']['mgtKey']              = $OVD_GIVE['mgtKey'];
				$REPAY[$x]['OVERDUE']['is_creditor']         = $OVD_GIVE['is_creditor'];
				$REPAY[$x]['OVERDUE']['receive_method']      = $OVD_GIVE['receive_method'];
				$REPAY[$x]['OVERDUE']['bank_name']           = $OVD_GIVE['bank_name'];
				$REPAY[$x]['OVERDUE']['account_num']         = $OVD_GIVE['account_num'];
				$REPAY[$x]['OVERDUE']['bank_private_name']   = $OVD_GIVE['bank_private_name'];
				$REPAY[$x]['OVERDUE']['banking_date']        = $OVD_GIVE['banking_date'];

				if($OVD_GIVE['is_creditor']=="Y") {     // 대부업 회원 일때 원천징수 제로 처리
					$REPAY[$x]['OVERDUE']['interest_income_tax'] = 0;
					$REPAY[$x]['OVERDUE']['local_income_tax']    = 0;
				}
				else {
					$REPAY[$x]['OVERDUE']['interest_income_tax'] = floor( ($REPAY[$x]['OVERDUE']['invest_interest'] * 0.25) / 10 ) * 10;
					$REPAY[$x]['OVERDUE']['local_income_tax']    = floor( (($REPAY[$x]['OVERDUE']['interest_income_tax'] * 0.1) / 10) ) * 10;
				}

				$REPAY[$x]['OVERDUE']['withhold']    = $REPAY[$x]['OVERDUE']['interest_income_tax'] + $REPAY[$x]['OVERDUE']['local_income_tax'];
				$REPAY[$x]['OVERDUE']['interest']    = $REPAY[$x]['OVERDUE']['invest_interest'] - $REPAY[$x]['OVERDUE']['withhold'] - $REPAY[$x]['OVERDUE']['invest_usefee'];

				if($product_idx=='171') {
					$REPAY[$x]['OVERDUE']['principal'] = $INI['principal'];
				}

				$REPAY[$x]['OVERDUE']['send_price']  = $REPAY[$x]['OVERDUE']['interest'] + $REPAY[$x]['OVERDUE']['principal'];

				$REPAY[$x]['OVERDUE']['repay_schedule_date'] = $REPAY[$x]['repay_schedule_date'];
			}

		}

	}

	// 최종 입금 회차 계산
	$r = sql_fetch("SELECT MAX(turn) AS max_turn FROM cf_product_success WHERE product_idx='".$PRDT['idx']."'");
	$SUM['last_repay_turn'] = $r['max_turn'];

	$RETURN_ARR = array("PRDT"=>$PRDT, "INI"=>$INI, "REPAY"=>$REPAY, "REPAYSUM"=>$SUM);
	return $RETURN_ARR;

}


function print_rr($arr, $style='') {
	echo "<pre style=\"$style\"><xmp>";
	print_r($arr);
	echo "</xmp></pre>";
}


///////////////////////////////////////////////////////////////////////////////
// (2017-06-21) 회원 변경내역 기록
///////////////////////////////////////////////////////////////////////////////
function member_edit_log($mb_no) {
	if($mb_no) {
		if(sql_query("INSERT INTO g5_member_history (SELECT * FROM g5_member WHERE mb_no='$mb_no')")) {
			return true;
		}
	}
}





















































































































































































































































































































































// 제목을 변환
function conv_subject($subject, $len, $suffix='')
{
	return get_text(cut_str($subject, $len, $suffix));
}

// 내용을 변환
function conv_content($content, $html, $filter=true)
{
	global $config, $board;

	if($html)
	{
		$source = array();
		$target = array();

		$source[] = "//";
		$target[] = "";

		if($html == 2) { // 자동 줄바꿈
			$source[] = "/\n/";
			$target[] = "<br/>";
		}

		// 테이블 태그의 개수를 세어 테이블이 깨지지 않도록 한다.
		$table_begin_count = substr_count(strtolower($content), "<table");
		$table_end_count = substr_count(strtolower($content), "</table");
		for($i=$table_end_count; $i<$table_begin_count; $i++)
		{
			$content .= "</table>";
		}

		$content = preg_replace($source, $target, $content);

		if($filter)
			$content = html_purifier($content);
	}
	else // text 이면
	{
		// & 처리 : &amp; &nbsp; 등의 코드를 정상 출력함
		$content = html_symbol($content);

		// 공백 처리
		//$content = preg_replace("/  /", "&nbsp; ", $content);
		$content = str_replace("  ", "&nbsp; ", $content);
		$content = str_replace("\n ", "\n&nbsp;", $content);

		$content = get_text($content, 1);
		$content = url_auto_link($content);
	}

	return $content;
}

// http://htmlpurifier.org/
// Standards-Compliant HTML Filtering
// Safe  : HTML Purifier defeats XSS with an audited whitelist
// Clean : HTML Purifier ensures standards-compliant output
// Open  : HTML Purifier is open-source and highly customizable
function html_purifier($html)
{
	$f = file(HF_PLUGIN_PATH.'/htmlpurifier/safeiframe.txt');
	$domains = array();
	foreach($f as $domain) {
		// 첫행이 # 이면 주석 처리
		if(!preg_match("/^#/", $domain)) {
			$domain = trim($domain);
			if($domain)
				array_push($domains, $domain);
		}
	}
	// 내 도메인도 추가
	array_push($domains, $_SERVER['HTTP_HOST'].'/');
	$safeiframe = implode('|', $domains);

	include_once(HF_PLUGIN_PATH.'/htmlpurifier/HTMLPurifier.standalone.php');
	$config = HTMLPurifier_Config::createDefault();
	// data/cache 디렉토리에 CSS, HTML, URI 디렉토리 등을 만든다.
	$config->set('Cache.SerializerPath', HF_DATA_PATH.'/cache');
	$config->set('HTML.SafeEmbed', false);
	$config->set('HTML.SafeObject', false);
	$config->set('Output.FlashCompat', false);
	$config->set('HTML.SafeIframe', true);
	$config->set('URI.SafeIframeRegexp','%^(https?:)?//('.$safeiframe.')%');
	$config->set('Attr.AllowedFrameTargets', array('_blank'));
	$purifier = new HTMLPurifier($config);
	return $purifier->purify($html);
}


// 회원 정보를 얻는다.
function get_member($mb_id, $fields='*')
{
	global $hf, $INDI_INVESTOR, $CONF;

	$row  = sql_fetch("SELECT $fields FROM {$hf['member_table']} WHERE mb_id=('$mb_id')");

	// 암호화된 내용 복호화
	if($row['mb_hp']) $row['mb_hp'] = masterDecrypt($row['mb_hp'], false);
	if($row['account_num']) $row['account_num'] = masterDecrypt($row['account_num'], false);

	// 개인 회원중 특별투자권한 자격정보 추출
	if($row['member_type']=='1' && $row['member_investor_type']>'1') {

		$row2 = sql_fetch("SELECT allow_date, rights_start_date, rights_end_date FROM investor_type_change_request WHERE idx='".$row['investor_judge_idx']."'");

		$row['special_investor'] = array();
		$row['special_investor']['allow_date']   = $row2['allow_date'];
		$row['special_investor']['rights_sdate'] = $row2['rights_start_date'];

		if( (empty($row2['rights_end_date']) || $row2['rights_end_date'] <= '2018-11-30') && G5_TIME_YMD <= '2018-12-31' ) {
			$row2['rights_end_date'] = "2018-11-30";		// 2018년 이전 가입자는 임의로 설정 (이정환 차장 요청)
		}

		$row['special_investor']['rights_edate'] = $row2['rights_end_date'];
		$row['special_investor']['valid_days'] = ceil((strtotime($row2['rights_end_date'])-time())/86400)+1;		// 자격 잔여일수

	}


	if( in_array($row['mb_level'],array('1','2','3')) && $row['member_group']=='F' ) {

		////////////////
		// 투자 정보
		////////////////

		$INV = array(
			// 누적 투자액
			'nujuk_amount_bds' => 0,	// 부동산
			'nujuk_amount_ds'  => 0,	// 동산
			'nujuk_amount'     => 0,

			// 투자 잔액
			'live_amount_bds'  => 0,
			'live_amount_ds'   => 0,
			'live_amount'      => 0,

			// 투자 가능액
			'able_amount_bds'  => 0,
			'able_amount_ds'   => 0,
			'able_amount'      => 0,
		);


		$base_sql = "
			SELECT
				IFNULL(SUM(A.amount), 0) AS sum_amount
			FROM
				cf_product_invest A
			LEFT JOIN
				cf_product B  ON A.product_idx=B.idx";


		// P2P 가이드라인 적용 이전 누적 투자액 (정상상품만)	-----------------------------------------
		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category='2' AND B.state NOT IN('3','6','7')
				AND A.product_idx <= '".$CONF['old_type_end_prdt_idx']."'";
		$RX1 = sql_fetch($sql);

		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category!='2' AND B.state NOT IN('3','6','7')
				AND A.product_idx <= '".$CONF['old_type_end_prdt_idx']."'";
		$RX2 = sql_fetch($sql);

		$OLD_INV['nujuk_amount_bds'] = $RX1['sum_amount'];
		$OLD_INV['nujuk_amount_ds']  = $RX2['sum_amount'];
		$OLD_INV['nujuk_amount']     = $RX1['sum_amount'] + $RX2['sum_amount'];
		// P2P 가이드라인 적용 이전 누적 투자액 (정상상품만)	-----------------------------------------


		// P2P 가이드라인 적용 이후 누적 투자액 (정상상품만)	-----------------------------------------
		//  - state = 1:이자상환중|2:상환완료(투자종료)|3:투자금모집실패|4:부실|5:중도상환|6:대출취소(기표전)|7:대출취소(기표후)|8:연채|9:부도(상환불가)
		// 누적 투자액 - 부동산
		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category='2'AND B.state NOT IN('3','6','7')
				AND A.product_idx > '".$CONF['old_type_end_prdt_idx']."'";
		$R1 = sql_fetch($sql);

		// 누적 투자액 - 동산,기타
		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category!='2' AND B.state NOT IN('3','6','7')
				AND A.product_idx > '".$CONF['old_type_end_prdt_idx']."'";
		$R2 = sql_fetch($sql);

		$INV['nujuk_amount_bds'] = $R1['sum_amount'];
		$INV['nujuk_amount_ds']  = $R2['sum_amount'];
		$INV['nujuk_amount']     = $R1['sum_amount'] + $R2['sum_amount'];
		// P2P 가이드라인 적용 이후 누적 투자액 (정상상품만)	-----------------------------------------


		// P2P 가이드라인 적용 이후 투자 잔액 추출 -----------------------------------------
		// 투자 잔액 - 부동산
		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category='2' AND B.state IN('','1')
				AND A.product_idx > '".$CONF['old_type_end_prdt_idx']."'";
		$R3 = sql_fetch($sql);

		// 투자 잔액 - 동산,기타
		$sql = $base_sql . "
			WHERE (1)
				AND A.member_idx='".$row['mb_no']."'
				AND A.invest_state='Y'
				AND B.category!='2' AND B.state IN('','1')
				AND A.product_idx > '".$CONF['old_type_end_prdt_idx']."'";
		$R4 = sql_fetch($sql);

		$INV['live_amount_bds'] = $R3['sum_amount'];
		$INV['live_amount_ds']  = $R4['sum_amount'];
		$INV['live_amount']     = $R3['sum_amount'] + $R4['sum_amount'];
		// P2P 가이드라인 적용 이후 투자 잔액 추출 -----------------------------------------


		if($row['member_type']=='1') {
			if( in_array($row['member_investor_type'], array('1','2')) ) {
				if($row['member_investor_type']=='1') {
					$INV['able_amount_bds'] = $INDI_INVESTOR['1']['prpt_limit'] - $INV['live_amount_bds'];
					$INV['able_amount_ds']  = $INDI_INVESTOR['1']['site_limit'] - $INV['live_amount_ds'];
					$INV['able_amount']     = $INDI_INVESTOR['1']['site_limit'] - $INV['live_amount'];
				}
				else {
					unset($INV['able_amount_bds']);
					unset($INV['able_amount_ds']);
					$INV['able_amount'] = $INDI_INVESTOR['2']['site_limit'] - $INV['live_amount'];
				}
			}
			else {
				unset($INV['able_amount_bds']);
				unset($INV['able_amount_ds']);
				$INV['able_amount'] = $INDI_INVESTOR['3']['site_limit'];	// 전문투자자 무제한
			}

			// P2P 가이드라인 적용 이전 투자 잔액 -------------------------------------
			// 부동산
			$sql = $base_sql . "
				WHERE (1)
					AND A.member_idx='".$row['mb_no']."'
					AND A.invest_state='Y'
					AND B.category='2' AND B.state IN('','1')
					AND A.product_idx <= '".$CONF['old_type_end_prdt_idx']."'";
			$R5 = sql_fetch($sql);

			// 동산,기타
			$sql = $base_sql . "
				WHERE (1)
					AND A.member_idx='".$row['mb_no']."'
					AND A.invest_state='Y'
					AND B.category!='2' AND B.state IN('','1')
					AND A.product_idx <= '".$CONF['old_type_end_prdt_idx']."'";
			$R6 = sql_fetch($sql);

			$OLD_INV['live_amount_bds'] = $R5['sum_amount'];
			$OLD_INV['live_amount_ds']  = $R6['sum_amount'];
			$OLD_INV['live_amount']     = $R5['sum_amount'] + $R6['sum_amount'];
			// P2P 가이드라인 적용 이전 투자 잔액 -------------------------------------

		}
		else {
			unset($INV['able_amount_bds']);
			unset($INV['able_amount_ds']);
			$INV['able_amount'] = $INDI_INVESTOR['3']['site_limit'];		// 법인 투자자 투자한도 무제한
		}


		unset($RX1); unset($RX2);
		unset($R1); unset($R2); unset($R3); unset($R4); unset($R5); unset($R6);

		$row['ing_invest_amount']           = $INV['live_amount'] + $OLD_INV['live_amount'];		// 투자 잔액 (전체)
		$row['ing_invest_amount_new']       = $INV['live_amount'];															// 투자 잔액 (가이드라인 이후)
		$row['ing_invest_amount_new_prpt']  = $INV['live_amount_bds'];													// 투자 잔액 (가이드라인 이후 부동산)
		$row['ing_invest_amount_new_ds']    = $INV['live_amount_ds'];														// 투자 잔액 (가이드라인 이후 동산)

		$row['total_invest_amount']         = $INV['nujuk_amount'] + $OLD_INV['nujuk_amount'];	// 누적 투자금액(전체)
		$row['total_invest_amount_new']     = $INV['nujuk_amount'];															// 누적 투자금액(가이드라인 이후)
		$row['total_invest_amount_new_prpt']= $INV['nujuk_amount_bds'];													// 누적 투자금액(가이드라인 이후 부동산)
		$row['total_invest_amount_new_ds']  = $INV['nujuk_amount_ds'];													// 누적 투자금액(가이드라인 이후 동산)

		$row['invest_possible_amount']      = $INV['able_amount'];															// 투자 가능금액(사이트 투자제한 금액 기준)
		$row['invest_possible_amount_prpt'] = $INV['able_amount_bds'];													// 투자 가능금액(부동산 투자제한 금액 기준)
		$row['invest_possible_amount_ds']   = $INV['able_amount_ds'];														// 투자 가능금액(동산 투자제한 금액 기준)

		// 사이트전체투자가능금액 < 상품 카테고리별 투자가능금액 의 경우 투자가능금액 보정
		if($row['member_type']=='1' && $row['member_investor_type']=='1') {
			if($row['invest_possible_amount'] < $row['invest_possible_amount_prpt']) $row['invest_possible_amount_prpt'] = $row['invest_possible_amount'];
			if($row['invest_possible_amount'] < $row['invest_possible_amount_ds'])   $row['invest_possible_amount_ds'] = $row['invest_possible_amount'];
		}

		// 출금가능금액 산출 : 현재예치금 - 현재시각기준24시간전 입금된 총금액 2019-05-23 적용 2019-05-24 부터 시행
		$before24_datetime = date("Y-m-d H:i:s", time()-86400);

		$BEFORE_1DAY = sql_fetch("
			SELECT
				(SELECT IFNULL(SUM(TR_AMT), 0) FROM IB_FB_P2P_IP WHERE 1 AND CUST_ID = '".$row['mb_no']."' AND ERP_TRANS_DT > '".preg_replace("/(-| |:)/","",$before24_datetime)."') AS insert_amt,
				(SELECT IFNULL(SUM(amount), 0) AS invest_amt FROM cf_product_invest WHERE 1 AND member_idx = '".$row['mb_no']."' AND invest_state = 'Y' AND insert_datetime > '".$before24_datetime."') AS invest_amt,
				(SELECT IFNULL(SUM(req_price), 0) AS withdrawal_amt FROM g5_withdrawal WHERE 1 AND mb_id='".$row['mb_id']."' AND regdate > '".$before24_datetime."' AND state='2') AS withdrawal_amt
		");
		//print_rr($BEFORE_1DAY,'font-size:12px;line-height:14px;');

		$now_amt    = get_point_sum($mb_id);
		$lock_amt   = max(($BEFORE_1DAY['insert_amt'] - $BEFORE_1DAY['invest_amt']), 0);
		$unlock_amt = max(($now_amt - $lock_amt), 0);		// 출금가능금액

		$row['lock_amount'] = (int)$lock_amt;
		$row['withdrawal_posible_amount'] = (int)$unlock_amt;

	}
	return $row;

}

function getJumin_new($member_idx) {

	global $member;

	//crypt.lib.php 가 필요함

	if( defined('HF_MYSQL_HOST2') && defined('HF_MYSQL_USER2') && defined('HF_MYSQL_PASSWORD2') && defined('HF_MYSQL_DB2') && function_exists('masterDecrypt') ) {

		$linkX = sql_connect(HF_MYSQL_HOST2, HF_MYSQL_USER2, HF_MYSQL_PASSWORD2, HF_MYSQL_DB2) or die('DB2 Connect Error!!!');;

		$row = sql_fetch("SELECT * FROM member_private WHERE mb_no = '".$member_idx."' ORDER BY idx DESC LIMIT 1", G5_DISPLAY_SQL_ERROR, $linkX);
		if( trim($row['regist_number']) ) {
			return masterDecrypt($row['regist_number'], true);
		}

		// 로그기록
		$req_url = G5_URL . $_SERVER['REQUEST_URI'];
		$sql = "
			INSERT INTO connect_log
			SET
				mb_no = '".$member['mb_no']."',
				mb_id = '".$member['mb_id']."',
				request_url = '".$X_req_url."',
				ip = '".$_SERVER['REMOTE_ADDR']."',
				rdate = NOW()";
		sql_query($sql, G5_DISPLAY_SQL_ERROR, $linkX);

		sql_close($linkX);

	}
	else {
		return false;
	}

}


// 관리자인가?
function is_admin($mb_id)
{
	global $config, $group, $board;

	if(!$mb_id) return;

	$add_sql = "
		SELECT
			COUNT(A.idx) AS 'cnt'
		FROM
			g5_sub_admin AS A
		LEFT JOIN
			g5_member AS B
		ON	A.mb_no = B.mb_no
		WHERE
			B.mb_id = '{$mb_id}'";

	$sad = sql_fetch($add_sql);

	if($config['cf_admin'] == $mb_id || $sad['cnt'] > 0) return 'super';
	if(isset($group['gr_admin']) && ($group['gr_admin'] == $mb_id)) return 'group';
	if(isset($board['bo_admin']) && ($board['bo_admin'] == $mb_id)) return 'board';
	return '';
}







// 포인트 부여
function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='', $expire=0)
{
	global $config;
	global $hf;
	global $is_admin;

	// 포인트 사용을 하지 않는다면 return

	if(!$config['cf_use_point']) { return 0; }

	// 포인트가 없다면 업데이트 할 필요 없음
	if($point == 0) { return 0; }

	// 회원아이디가 없다면 업데이트 할 필요 없음
	if($mb_id == '') { return 0; }
	$mb = sql_fetch("SELECT mb_no, mb_id, mb_point FROM {$g5['member_table']} WHERE mb_id='".$mb_id."'");
	if(!$mb['mb_id']) { return 0; }

	// 회원포인트
	$mb_point = get_point_sum($mb_id);
  //$mb_point = $mb["mb_point"];

	// 이미 등록된 내역이라면 건너뜀
	if($rel_table || $rel_id || $rel_action)
	{
		$sql = "
			SELECT
				COUNT(*) AS cnt
			FROM
				{$g5['point_table']}
			WHERE 1
				AND mb_id = '$mb_id'
				AND po_rel_table = '$rel_table'
				AND po_rel_id = '$rel_id'
				AND po_rel_action = '$rel_action' ";
		$row = sql_fetch($sql);
		if($row['cnt'])
			return -1;
	}

	// 포인트 건별 생성
	$po_expire_date = '9999-12-31';
	if($config['cf_point_term'] > 0) {
		if($expire > 0)
			$po_expire_date = date('Y-m-d', strtotime('+'.($expire - 1).' days', G5_SERVER_TIME));
		else
			$po_expire_date = date('Y-m-d', strtotime('+'.($config['cf_point_term'] - 1).' days', G5_SERVER_TIME));
	}

	$po_expired = 0;
	if($point < 0) {
		$po_expired = 1;
		$po_expire_date = HF_TIME_YMD;
	}
	$po_mb_point = $mb_point + $point;
	$sql = "
		INSERT INTO
			{$hf['point_table']}
	  SET
			mb_no = '".$mb['mb_no']."',
			mb_id = '$mb_id',
			po_datetime = '".HF_TIME_YMDHIS."',
			po_content = '".addslashes($content)."',
			po_point = '$point',
			po_use_point = '0',
			po_mb_point = '$po_mb_point',
			po_expired = '$po_expired',
			po_expire_date = '$po_expire_date',
			po_rel_table = '$rel_table',
			po_rel_id = '$rel_id',
			po_rel_action = '$rel_action' ";
	sql_query($sql);

	// 포인트를 사용한 경우 포인트 내역에 사용금액 기록
	if($point < 0) {
		insert_use_point($mb_id, $point);
	}

	// 포인트 UPDATE
	$sql = "UPDATE {$hf['member_table']} SET mb_point='$po_mb_point' WHERE mb_id='$mb_id'";
	sql_query($sql);

	return 1;
}


// 사용포인트 입력
function insert_use_point($mb_id, $point, $po_id='') {
	global $hf, $config;

	$sql_order = ($config['cf_point_term']) ? " ORDER BY po_expire_date ASC, po_id ASC " : " ORDER BY po_id ASC ";

	$point1 = abs($point);
	$sql = "
		SELECT
			po_id, po_point, po_use_point
		FROM
			{$hf['point_table']}
		WHERE
			mb_id = '$mb_id'
			AND po_id <> '$po_id'
			AND po_expired = '0'
			AND po_point > po_use_point
		$sql_order ";
	$result = sql_query($sql);
	for($i=0; $row=sql_fetch_array($result); $i++) {
		$point2 = $row['po_point'];
		$point3 = $row['po_use_point'];

		if(($point2 - $point3) > $point1) {
			$sql = "
				UPDATE
					{$hf['point_table']}
				SET
					po_use_point = po_use_point + '$point1'
				WHERE
					po_id = '{$row['po_id']}' ";
			sql_query($sql);
			break;
		}
		else {
			$point4 = $point2 - $point3;
			$sql = "
				UPDATE
					{$hf['point_table']}
			  SET
					po_use_point = po_use_point + '$point4',
					po_expired = '100'
			  WHERE
					po_id = '{$row['po_id']}' ";
			sql_query($sql);

			$point1 -= $point4;
		}
	}
}


































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































































// 한글 요일
function get_yoil($date, $full=0)
{
	$arr_yoil = array ('일', '월', '화', '수', '목', '금', '토');

	$yoil = date("w", strtotime($date));
	$str = $arr_yoil[$yoil];
	if($full) {
		$str .= '요일';
	}
	return $str;
}



















// UTF-8 문자열 자르기
// 출처 : https://www.google.co.kr/search?q=utf8_strcut&aq=f&oq=utf8_strcut&aqs=chrome.0.57j0l3.826j0&sourceid=chrome&ie=UTF-8
function utf8_strcut( $str, $size, $suffix='...' )
{
	$substr = substr( $str, 0, $size * 2 );
	$multi_size = preg_match_all( '/[\x80-\xff]/', $substr, $multi_chars );

	if( $multi_size > 0 )
		$size = $size + intval( $multi_size / 3 ) - 1;

	if( strlen( $str ) > $size ) {
		$str = substr( $str, 0, $size );
		$str = preg_replace( '/(([\x80-\xff]{3})*?)([\x80-\xff]{0,2})$/', '$1', $str );
		$str .= $suffix;
	}

	return $str;
}



// mysqli_real_escape_string 의 alias 기능을 한다.
function sql_real_escape_string($str, $link=null)
{
	global $hf;

	if(!$link)
		$link = $hf['connect_db'];

	return mysqli_real_escape_string($link, $str);
}








function is_mobile()
{
	return preg_match('/'.G5_MOBILE_AGENT.'/i', $_SERVER['HTTP_USER_AGENT']);
}


































// HTML 마지막 처리
function html_end()
{
	global $html_process;

	return $html_process->run();
}

function add_stylesheet($stylesheet, $order=0)
{
	global $html_process;

	if(trim($stylesheet))
		$html_process->merge_stylesheet($stylesheet, $order);
}

function add_javascript($javascript, $order=0)
{
	global $html_process;

	if(trim($javascript))
		$html_process->merge_javascript($javascript, $order);
}

class html_process {
	protected $css = array();
	protected $js  = array();

	function merge_stylesheet($stylesheet, $order)
	{
		$links = $this->css;
		$is_merge = true;

		foreach($links as $link) {
			if($link[1] == $stylesheet) {
				$is_merge = false;
				break;
			}
		}

		if($is_merge)
			$this->css[] = array($order, $stylesheet);
	}

	function merge_javascript($javascript, $order)
	{
		$scripts = $this->js;
		$is_merge = true;

		foreach($scripts as $script) {
			if($script[1] == $javascript) {
				$is_merge = false;
				break;
			}
		}

		if($is_merge)
			$this->js[] = array($order, $javascript);
	}

	function run()
	{
		global $config, $hf, $member;

		$device = getDevice();

		// 현재접속자 처리
		$tmp_sql = "SELECT COUNT(*) AS cnt FROM {$hf['login_table']} WHERE lo_ip='".$_SERVER['REMOTE_ADDR']."'";

		$tmp_row = sql_fetch($tmp_sql);

		if($tmp_row['cnt']) {
			$tmp_sql = "
				update
					{$hf['login_table']}
				set
					mb_id='{$member['mb_id']}',
					lo_datetime='".HF_TIME_YMDHIS."',
					lo_location='{$hf['lo_location']}',
					lo_url='{$hf['lo_url']}',
					lo_device='$device'
				WHERE
					lo_ip = '{$_SERVER['REMOTE_ADDR']}'";
			sql_query($tmp_sql, FALSE);
		}
		else {
			$tmp_sql = "INSERT INTO {$g5['login_table']} (lo_ip, mb_id, lo_datetime, lo_location, lo_url, lo_device) VALUES ('{$_SERVER['REMOTE_ADDR']}', '{$member['mb_id']}', '".G5_TIME_YMDHIS."', '{$g5['lo_location']}',  '{$g5['lo_url']}', '$device')";
			sql_query($tmp_sql, FALSE);

			// 시간이 지난 접속은 삭제한다
			sql_query(" delete from {$hf['login_table']} where lo_datetime < '".date("Y-m-d H:i:s", HF_SERVER_TIME - (60 * $config['cf_login_minutes']))."' ");

			// 부담(overhead)이 있다면 테이블 최적화
			//$row = sql_fetch(" SHOW TABLE STATUS FROM `$mysql_db` LIKE '$g5['login_table']' ");
			//if($row['Data_free'] > 0) sql_query(" OPTIMIZE TABLE $g5['login_table'] ");
		}

		$buffer = ob_get_contents();
		ob_end_clean();

		// 세션을 선택적으로 시작함
		lazy_session_start();

		$stylesheet = '';
		$links = $this->css;

		if(!empty($links)) {
			foreach ($links as $key => $row) {
				$order[$key] = $row[0];
				$index[$key] = $key;
				$style[$key] = $row[1];
			}

			array_multisort($order, SORT_ASC, $index, SORT_ASC, $links);

			foreach($links as $link) {
				if(!trim($link[1]))
					continue;

				$stylesheet .= PHP_EOL.$link[1];
			}
		}

		$javascript = '';
		$scripts = $this->js;
		$php_eol = '';

		unset($order);
		unset($index);

		if(!empty($scripts)) {
			foreach ($scripts as $key => $row) {
				$order[$key] = $row[0];
				$index[$key] = $key;
				$script[$key] = $row[1];
			}

			array_multisort($order, SORT_ASC, $index, SORT_ASC, $scripts);

			foreach($scripts as $js) {
				if(!trim($js[1]))
					continue;

				$javascript .= $php_eol.$js[1];
				$php_eol = PHP_EOL;
			}
		}

		/*
		</title>
		<link rel="stylesheet" href="default.css">
		밑으로 스킨의 스타일시트가 위치하도록 하게 한다.
		*/
		$buffer = preg_replace('#(</title>[^<]*<link[^>]+>)#', "$1$stylesheet", $buffer);

		/*
		</head>
		<body>
		전에 스킨의 자바스크립트가 위치하도록 하게 한다.
		*/
		$buffer = preg_replace('#(</head>[^<]*<body[^>]*>)#', "$javascript\n$1", $buffer);

		return $buffer;
	}
}


























































































































































// 로그인 후 이동할 URL
function login_url($url='')
{
	if(!$url) $url = HF_URL;
	return urlencode(clean_xss_tags(urldecode($url)));
}

// 문자열 암호화
function get_encrypt_string($str)
{
	$encrypt = (defined('HF_STRING_ENCRYPT_FUNCTION') && HF_STRING_ENCRYPT_FUNCTION) ? call_user_func(HF_STRING_ENCRYPT_FUNCTION, $str) : sql_password($str);
	return $encrypt;
}

// 문자열 암호화 (SHA256 salt 방식)
function get_encrypt_string2($str)
{
	$strsalt = $str;
	for($i=0; $i<strlen($str); $i++) {
		$strsalt.= "$i";
	}

	$encrypt = "*" . strtoupper(hash('sha256', $strsalt));
	return $encrypt;
}

// 비밀번호 비교
function check_password($pass, $hash)
{
	$password = get_encrypt_string($pass);
	return ($password === $hash);
}

// 비밀번호 비교(신)
function check_password2($pass, $hash)
{
	$password = get_encrypt_string2($pass);
	return ($password === $hash);
}


function IP_AREA($ip) {
	if($ip)	{

		$GeoIP = geoip_record_by_name($ip);
		$GeoIP['region_code'] = $GeoIP['region']; unset($GeoIP['region']);
		$GeoIP['region_name'] = ($GeoIP['country_code'] && $GeoIP['region_code']) ? geoip_region_name_by_code($GeoIP['country_code'], $GeoIP['region_code']) : '';


	//$ARR['continent_code'] = $GeoIP['continent_code'];
    $ARR['country_code']   = $GeoIP['country_code'];
  //$ARR['country_code3']  = $GeoIP['country_code3'];
    $ARR['country_name']   = $GeoIP['country_name'];
    $ARR['region_code']    = $GeoIP['region_code'];
    $ARR['region_name']    = preg_replace("/\'/", "", $GeoIP['region_name']);
		$ARR['city']           = $GeoIP['city'];
  //$ARR['postal_code']    = $GeoIP['postal_code'];
		$ARR['latitude']       = $GeoIP['latitude'];
		$ARR['longitude']      = $GeoIP['longitude'];
  //$ARR['dma_code']       = $GeoIP['dma_code'];
  //$ARR['area_code']      = $GeoIP['area_code'];

		return $ARR;
	}
}

?>