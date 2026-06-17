<?php
$sub_menu = "200310";
include_once('./_common.php');

auth_check($auth[$sub_menu], "r");

$DracoCounter_URL  = G5_ADMIN_URL  ."/status";  // 활성도통계 설치폴더
$DracoCounter_PATH = G5_ADMIN_PATH ."/status";  // 활성도통계 절대경로

$g5['title'] = "접속 키워드 로그";
include_once G5_ADMIN_PATH."/admin.head.php";

// 날짜 설정
if(!$fr_date) $fr_date = date("Y-m-d", strtotime("0 days ago"));
if(!$to_date) $to_date = G5_TIME_YMD;

// 주사 지랄 방지
$fr_date  = substr($fr_date, 0, 10);
$to_date  = substr($to_date, 0, 10);
$site     = substr($site, 0, 10);
$site_ori = $site;

// 검색사이트들
$site_arr = array("Naver", "Daum", "Nate", "Google", "Bing", "Yahoo");
$surl_arr = array("Naver" => "%search.naver.com%", "Daum" => "%search.daum.net/search?%", "Nate" => "%search.daum.net/nate?%", "Google" => "%www.google.co.kr%", "Bing" => "%www.bing.com/search%", "Yahoo" => "%search.yahoo.%");
$svar_arr = array("Naver" => "query", "Daum" => "q", "Nate" => "q", "Google" => "q", "Bing" => "q", "Yahoo" => "p");
?>
<style type="text/css">
#m3tbl { border:solid 1px #CCC; border-collapse:collapse;}
#m3tbl th { border:solid 1px #CCC; text-align:center;}
#m3tbl td { border:solid 1px #CCC; text-align:center; padding:2px 8px;}
#div_m3sq ul { display:inline; padding:0; margin:0; }
#div_m3sq ul li { display:inline; padding:0 10px; border:solid 1px #CCC; }
#div_m3sq ul li.s { background-color:#ABEDA8; }
</style>

<div id="div_m3sq" style="padding-left:20px; width:890px;">
  <img src="./status/img/bul2.gif" border=0 align=absmiddle> <b>외부 유입 검색어(키워드) 분석기</b><br><br>
  <ul>
    <li <?=(!$_REQUEST['site'])?"class='s'":"";?>><a href="<?=$_SERVER['PHP_SELF']?>?to_date=<?=$to_date?>&fr_date=<?=$fr_date?>">All</a></li>
<?
	foreach($site_arr as $site) {
		$class = ($_REQUEST['site']==$site) ? "class='s'" : "";
?>
    <li <?=$class?>><a href="<?=$_SERVER['PHP_SELF']?>?site=<?=$site?>&to_date=<?=$to_date?>&fr_date=<?=$fr_date?>"><?=$site?></a></li>
<?
	}
?>
  </ul>
  <br >
  <br >

  <form method="get" action="<?=$_SERVER['PHP_SELF']?>">
    <input type="hidden" name="site" value="<?=$site_ori?>" >
    시작 : <input type="text" name="fr_date" value="<?=$fr_date?>" class="datepicker" size="10" >
    끝 : <input type="text" name="to_date" value="<?=$to_date?>" class="datepicker" size="10" >
    <input type="submit" value="go" style="width:60px;" ><br>
  </form><br>
  <form action="javascript:;" onSubmit="findsq(getElementById('sq').value)" >
    결과내 검색 : <input type="text" id="sq" name="sq" value="<?php echo $sq;?>" >
    <input type="submit" value="search"  style="width:60px;" >
    <input type="button" value="reset" onclick="resetsq()"  style="width:60px;" >
    <span id="search_cnt"></span><br>
  </form>
  <br>

<?php
	// vi_referer에서 사이트 찾고, vi_date로 범위 정하기, 정렬은 vi_id 역순 (속도 개선 필요)
	if(in_array($site_ori, $site_arr)) {
		$where1 = "vi_referer LIKE '{$surl_arr[$site_ori]}' ";
	}
	else { // 5개 사이트 모두 포함
		$where1 = " ( ";
		foreach($surl_arr as $site => $surl) {
			$where1 .= " vi_referer LIKE '$surl' OR ";
		}
		$where1 .= " 0 )";
	}

	$sql = "SELECT * FROM ".$g5['visit_table']." WHERE $where1 AND vi_date between '$fr_date' AND '$to_date' ORDER BY vi_id DESC";
	$query = sql_query($sql);
	$rcount = $query->num_rows;
?>
<table id="m3tbl">
  <tr bgcolor="#EEEEEE">
		<th width="100">날짜</td>
    <th width="80">시간</td>
    <th width="100">사이트</td>
    <th width="%">검색어</td>
  </tr>
<?php

	// 카운트용 변수
	$cnt = 0;
	$cnt2 = array();

	for($i=0; $i<$rcount; $i++) {
		$row = sql_fetch_array($query);
		// 어느 사이트인지 찾기
		foreach($surl_arr as $site => $surl) {
			if(strstr($row['vi_referer'], str_replace("%", "", $surl))) {
				$engine = $site;
				break;
			}
		}
		// 검색문자열 찾기
		$regex = "/(\?|&){$svar_arr[$engine]}\=([^&]*)/i";
		preg_match($regex, $row['vi_referer'], $matches);
		$querystr = $matches[2];
		$querystr = str_replace("+", " ", $querystr);  // 보통 검색어 사이를 +로 넘긴다
		$querystr = urldecode($querystr);              // %ab 이런 식으로 된 걸 바꿔주기
		if($engine=="Naver") $querystr = utf8_urldecode($querystr);  // 네이버는 unicode로 된 경우도 있어서
		$charset = mb_detect_encoding($querystr, "ASCII, euc-KR, utf-8");  // 캐릭터셋이 utf-8인 경우는 euc-kr 고치기 (utf-8 유저는 euc-KR과 utf-8을 서로 바꿔주면 될 듯)
		if($charset=="euc-kr") $querystr = iconv("euc-kr", "utf-8", $querystr);
		//$charset = mb_detect_encoding($querystr, "ASCII, utf-8, euc-kr");
		//if($charset=="utf-8") $querystr = iconv("utf-8", "euc-kr", $querystr);
		// 자잘한 처리들
		$querystr = trim($querystr);
		$querystr = htmlspecialchars($querystr);
		// 가끔 빈 것들도 있다 -_-
		if(!strlen($querystr)) continue;

		echo "  <tr>
		<td>".$row['vi_date']."</td>
    <td>".$row['vi_time']."</td>
    <td><a href='".$PHP_SELF."?site=".$engine."'><img src='".$DracoCounter_URL."/img/".strtolower($engine).".jpg' ></a></td>
    <td id='m3sqtd[$cnt]' style='text-align:left'><a href='".$row['vi_referer']."' target='_blank'>".$querystr."</a></td>
  </tr>\n";

		// 카운트용 변수
		$cnt++;
		$cnt2[$engine]++;

	}
	ksort($cnt2);

	// 베짱이님 제공 함수
	function utf8_urldecode($str, $chr_set='CP949') {
		$callback_function = create_function('$matches, $chr_set="'.$chr_set.'"', 'return iconv("UTF-16BE", $chr_set, pack("n*", hexdec($matches[1])));');
		return rawurldecode(preg_replace_callback('/%u([[:alnum:]]{4})/', $callback_function, $str));
	}

?>
</table><br>
Total : <?php echo $days=(strtotime($to_date)-strtotime($fr_date))/(24*60*60)+1;?> days, <?=$cnt?> results (<?=sprintf("%.1f",$cnt/$days);?>/day)<br>
<?
	if(!$site_ori) { // 모든 사이트의 경우 비율 분석
		$cnt2 = array_reverse($cnt2);
		foreach($cnt2 as $engine => $count) {
			echo "$engine : $count (".sprintf("%.1f",$count/$cnt*100)."%)<br>";
		}
	}
?>

</div>

<script type="text/javascript">
function findsq(sq) {
	if(sq=="") return;
	var i = 0;
	var search_cnt = 0; // 결과내 검색 개수
	while(a = document.getElementById("m3sqtd["+i+"]")) {
		if(a.innerText.toLowerCase().match(sq.toLowerCase())) { // 찾는 값이 있으면 보이기
			a.parentNode.style.display="";
			search_cnt++;
		} else { // 찾는 값이 없으면 숨기기
			a.parentNode.style.display="none";
		}
		i++;
	}
	document.getElementById("search_cnt").innerText = "결과내 검색 : " + search_cnt + "건";
}
function resetsq() {
	var i = 0;
	while(a = document.getElementById("m3sqtd["+i+"]")) {
		a.parentNode.style.display=""; // 모든 행의 display 속성 reset
		i++;
	}
	document.getElementById("search_cnt").innerText = "";
	document.getElementById("sq").value = "";
}
</script>

<?php
//마지막 인클루드
include_once G5_ADMIN_PATH."/admin.tail.php";
?>