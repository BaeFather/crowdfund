<?php
if (!defined('_GNUBOARD_')) exit;

$use_sc_svr = "";  // 내부 스크래핑 서버 사용 여부 Y는 사용

// 로그를 파일에 쓴다
function write_log($file, $log) {
    $fp = fopen($file, "a+");
    ob_start();
    print_r($log);
    $msg = ob_get_contents();
    ob_end_clean();
    fwrite($fp, $msg);
    fclose($fp);
}

function get_dj($dcode) {

	global $use_sc_svr;

	$chk_sql = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE d_code='$dcode' AND (mg_id2='' OR mg_id2 is NULL) ";
	$chk_row = sql_fetch($chk_sql);

	if ($chk_row["cnt"]>0) {
		
		if ($use_sc_svr=="Y") {

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
			curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/get_dj.php?d_code=".$dcode);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);
			$response = curl_exec($ch);
			curl_close($ch);

		} else {

			$param3 = array('법정동코드' => $dcode,'유형' => 1);

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
			curl_setopt($ch, CURLOPT_URL, "https://api.kbland.kr/land-price/price/fastPriceComplexName?".http_build_query($param3));
			$response = curl_exec($ch);
			curl_close($ch);

		}

		$d = json_decode($response, true);

		for ($i=0 ; $i<count($d["dataBody"]["data"]); $i++) {

			$mg_id  = $d["dataBody"]["data"][$i]["시세물건식별자"];
			$mg_id2 = $d["dataBody"]["data"][$i]["단지기본일련번호"];
			$addr2  = $d["dataBody"]["data"][$i]["주소"];

			$chk_sql2 = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE d_code='$dcode' AND mg_id='".$mg_id."' AND (mg_id2='' OR mg_id2 is NULL)";
			$chk_row2 = sql_fetch($chk_sql2);

			if ($chk_row2["cnt"]>0) {
				$up_sql = "UPDATE hello_apt_kb SET mg_id2='$mg_id2', addr2='".$addr2."' WHERE d_code='$dcode' AND mg_id='".$mg_id."' AND (mg_id2='' OR mg_id2 is NULL)";
				sql_query($up_sql);
			}			

		}
	} 
}

function get_seri2($mg_id) {
		
	global $use_sc_svr;

	$osql = "SELECT mg_id2 FROM hello_apt_kb WHERE mg_id='$mg_id' LIMIT 1";
	$orow = sql_fetch($osql);
	$mg_id2 = $orow["mg_id2"];

	if ($use_sc_svr=="Y") {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/get_seri2.php?mg_id2=".$mg_id2);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);

	} else {

		$param3 = array('단지기본일련번호' => $mg_id2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "https://api.kbland.kr/land-complex/complex/typInfo?".http_build_query($param3));
		$response = curl_exec($ch);
		curl_close($ch);

	}

		$d = json_decode($response, true);

		//echo "<pre>"; print_r($d); echo "</pre>";

		for ($i=0 ; $i<count($d["dataBody"]["data"]); $i++) {

			$ju_seri2 = $d["dataBody"]["data"][$i]["면적일련번호"];
			$jm = $d["dataBody"]["data"][$i]["전용면적"];
			$tot_house = $d["dataBody"]["data"][$i]["세대수"];
			$top_gubun = $d["dataBody"]["data"][$i]["주택형타입내용"];
			$i2 = $i+1;

			//$up_sql = "UPDATE hello_apt_kb SET ju_seri2='$ju_seri2' WHERE mg_id2='$mg_id2' AND ju_seri='".$i2."' AND jm='$jm'";
			//$up_sql = "UPDATE hello_apt_kb SET ju_seri2='$ju_seri2' WHERE mg_id2='$mg_id2' AND jm='$jm' AND tot_house='$tot_house'";
			//$up_sql = "UPDATE hello_apt_kb SET ju_seri2='$ju_seri2' WHERE mg_id2='$mg_id2' AND jm='$jm' AND top_gubun='$top_gubun'";
			$up_sql = "UPDATE hello_apt_kb 
						  SET ju_seri2='$ju_seri2', 
							  tot_house='$tot_house', 
							  top_gubun='$top_gubun' 
						WHERE mg_id2='$mg_id2' AND format(jm,2)='$jm' AND ju_seri2=''
						LIMIT 1";
			sql_query($up_sql);
		}
}

function get_sise2($mg_id, $ju_seri) {

	global $use_sc_svr;

	$osql = "SELECT d_code, mg_id2, ju_seri2 FROM hello_apt_kb WHERE mg_id='$mg_id' AND ju_seri='$ju_seri'";
	$orow = sql_fetch($osql);
	$mg_id2 = $orow["mg_id2"];
	$ju_seri2 = $orow["ju_seri2"];
	$d_code = $orow["d_code"];

	if (!$mg_id2) return;
	
	if ($use_sc_svr=="Y") {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/kb_sise.php?d_code=$d_code&mg_id2=$mg_id2");
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response = curl_exec($ch);
		curl_close($ch);

	} else {

		$param3 = array('법정동코드' => $d_code, '유형' => 1, '거래유형' => 1, '단지기본일련번호' => $mg_id2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "https://api.kbland.kr/land-price/price/fastPriceInfo?".http_build_query($param3));
		$response = curl_exec($ch);
		curl_close($ch);

	}


	$d = json_decode($response, true);

	//echo $response."<br/><br/>";
	//echo "<pre>";print_r($d); echo "</pre>";
	$this_day = date("Y-m-d");

	$kijun = $d["dataBody"]["data"][0]["시세마감년월일"];

	//$kijun = $d["dataBody"]["data"]["시세마감년월일"];
	$kijun = substr($kijun,0,4).".".substr($kijun,4,2).".".substr($kijun,-2);
	$chk_sql = "SELECT * FROM hello_apt_kb WHERE mg_id2='$mg_id2' ORDER BY kijun DESC LIMIT 1";
	$chk_row = sql_fetch($chk_sql);
	$old_kijun = $chk_row["kijun"];



	if (!$old_kijun OR $old_kijun<$kijun) {

		for ($i=0 ; $i<COUNT($d["dataBody"]["data"][0]["매매"]) ; $i++) {
		//for ($i=0 ; $i<COUNT($d["dataBody"]["data"]["매매"]) ; $i++) {

			$r = $d["dataBody"]["data"][0]["매매"][$i];
			//$r = $d["dataBody"]["data"]["매매"][$i];

			$jm = $r["전용면적"];
			//$top_gubun = $r["연결구분명"];
			$top_gubun = $r["주택형타입내용"];

			$mm_t = $r["상위평균"];
			$mm = $r["일반평균"];
			$mm_b = $r["하위평균"];

			$update_sql = "UPDATE hello_apt_kb
							  SET kijun = '$kijun',
								  mm_t = '$mm_t',
								  mm = '$mm',
								  mm_b = '$mm_b',
								  input_datetime = '$this_day'
							WHERE mg_id2='$mg_id2'
							  AND format(jm,2)='$jm'
							  AND top_gubun='$top_gubun'
			";

			sql_query($update_sql);

		}
		
	}

	
	if ($use_sc_svr=="Y") {

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "http://scrap2.hellofunding.kr/scrap2/kb_sil.php?d_code=$d_code&mg_id2=$mg_id2&ju_seri2=$ju_seri2");
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$response2 = curl_exec($ch);
		curl_close($ch);

	} else {

		$param3 = array('단지기본일련번호' => $mg_id2,'면적일련번호' => $ju_seri2);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    //요청 결과를 문자열로 반환
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   //원격 서버의 인증서가 유효한지 검사 안함
		curl_setopt($ch, CURLOPT_URL, "https://api.kbland.kr/land-price/price/BasePrcInfoNew?".http_build_query($param3));
		$response2 = curl_exec($ch);
		curl_close($ch);

	}

	$d2 = json_decode($response2, true);

	$sil_price = $d2["dataBody"]["data"]["시세"][0]["매매거래금액"];
	$sil_day   = $d2["dataBody"]["data"]["시세"][0]["매매계약종료년월일"];

	$up_sql = "UPDATE hello_apt_kb SET mm_sil='$sil_price' , mm_date='$sil_day' WHERE mg_id2='$mg_id2' AND ju_seri2='$ju_seri2'";
	sql_query($up_sql);

}

// 회원가입 referer 체크 및 pid 명시
Class strMemberRefererCheck
{
	// G5_COOKIE_DOMAIN  /config.php

	public $ndate;
	public $strReferer;
	public $strLinkUrl;

	Public Function __construct()
	{
		$this->ndate = DATE("Y-m-d");
		$this->strReferer = $_SERVER["HTTP_REFERER"];

		IF(!$this->strReferer)
		{
		//$this->strReferer = "okcashbag.com";
		//$this->strReferer = "campaign.naver.com";
		}
	}

	Public Function __destruct()
	{
	}

	Public Function fn_referer()
	{
		$strArr	=	$this->fn_partner();

		//if( preg_match('/183\.98\.101\.114/', $_SERVER['REMOTE_ADDR']) ) { print_rr($strArr); exit; }

		$strKind = false;
		$strPid = "";

		FOR($i=0; $i<COUNT($strArr); $i++)
		{
			IF(preg_match("#".$strArr[$i][2]."#", $this->strReferer))
			{
			//setcookie("ck_pid", $strArr[$i][0], 0, "/", G5_COOKIE_DOMAIN);
				set_cookie("ck_pid", $strArr[$i][0], 1000);
				$strPid = $strArr[$i][0];
				$strKind = true;
				break;
			}
		}

		IF($strKind == true)
		{
			goto_url($this->strLinkUrl."&pid=".$strPid);
		}
		ELSE {
			ECHO "접근이 잘못 되었습니다.";
		}
	}

  Public Function fn_pid_check($pid)
	{

    IF($pid)
    {
				set_cookie("ck_pid", $pid, 86400);

        IF(strpos($this->strLinkUrl,"?") === false)
        {
          $strchr = "?";
        } ELSE {
          $strchr = "&";
        }
        //goto_url($this->strLinkUrl.$strchr."pid=".$pid);
        goto_url($this->strLinkUrl);
		}
	}

	Public Function fn_partner()
	{
		$strVal = ARRAY(
			ARRAY("okcashbag",    "오케이캐쉬백", "okcashbag.com"),
			ARRAY("hellofunding", "헬로펀딩",     "hellofunding.co.kr"),
			ARRAY("naverpay",     "네이버페이",   "campaign.naver.com"),
			ARRAY("remberapp",    "리멤버앱",     "")
		);
		return $strVal;
	}
}

// 아파트 시세 조회
Class strAptPrice
{
	// G5_COOKIE_DOMAIN  /config.php

	public $ndate;
	public $strLinkUrl;

	Public Function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public Function __destruct()
	{
	}

	Public Function addr_si()
	{
		$retval = ARRAY(
							"서울특별시",
							"인천광역시",
							"광주광역시",
							"대전광역시",
							"대구광역시",
							"울산광역시",
							"부산광역시",
							"경기도"
					);
		return $retval;
	}

	Public Function addr_gu($strSi)
	{
		$Query = "SELECT gu FROM add_code where si='".addslashes($strSi)."' AND gu <>'' AND RIGHT(gu,1) <> '군'  GROUP BY gu ORDER BY binary(gu) ASC ";
		$Result = sql_query($Query);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	$Row["gu"];
		}

		return $retval;
	}

	Public Function addr_dong($strSi, $strGu)
	{
		$Query = "SELECT code, dong FROM add_code where si='".addslashes($strSi)."' AND gu='".addslashes($strGu)."' AND dong <>'' AND (RIGHT(dong,1) <> '읍' AND RIGHT(dong,1) <> '면')  GROUP BY dong ORDER BY binary(dong) ASC ";
		$Result = sql_query($Query);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	ARRAY($Row["code"], $Row["dong"]);
		}

		return $retval;
	}

	Public Function Apt_name($d_code)
	{
		IF(STRLEN($d_code) == 10)
		{
			$d_code = SUBSTR($d_code,0,8)."00";
		}

		get_dj($d_code);

		$Query = "SELECT mg_id, dj_name FROM hello_apt_kb WHERE d_code='".addslashes($d_code)."' GROUP BY mg_id ORDER BY  binary(dj_name) ASC ";

		$Result = sql_query($Query);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	ARRAY($Row["mg_id"],$Row["dj_name"]);
		}
		return $retval;
	}

	Public Function Apt_area($strMgid)
	{

		$chk_sql = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE mg_id='$strMgid' AND ju_seri2='' ";
		$chk_row = sql_fetch($chk_sql);
		if ($chk_row["cnt"]) {
			get_seri2($strMgid);
		}

		$Query = "SELECT ju_seri, jm FROM hello_apt_kb WHERE mg_id='".addslashes($strMgid)."' GROUP BY jm,ju_seri ORDER BY jm ASC,mm ASC";
		$Result = sql_query($Query);

		$ju_seriOr = "";
		WHILE($Row=sql_fetch_array($Result))
		{
			$ju_txt = "";
			IF($jmOr == $Row["jm"])
			{
				$ju_txt = " (탑층)";
			}
			$retval[]	=	ARRAY($Row["ju_seri"],$Row["jm"].$ju_txt);
			$jmOr = $Row["jm"];
		}

		/*
		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	ARRAY($Row["ju_seri"],$Row["jm"]);
		}
		*/
		return $retval;
	}

	Public Function Apt_select($mg_id, $ju_seri)
	{
		$strWhere = " WHERE mg_id='".addslashes($mg_id)."' AND ju_seri='".addslashes($ju_seri)."'";

		$Query = "SELECT mm FROM hello_apt_kb ".$strWhere." ORDER BY idx DESC";

		$Result = sql_query($Query, $connect);

		IF($Row=sql_fetch_array($Result))
		{
			$RowPrice	=	$Row["mm"];
			sql_free_result($Result);
		}

		return $RowPrice;
	}

	Public Function Apt_select_old($strAddr, $apt_name, $apt_area,$apt_floor)
	{
		$strWhere = " WHERE addr1='".addslashes($strAddr)."' AND danji='".addslashes($apt_name)."' AND area='".addslashes($apt_area)."'";

		$Query = "SELECT floor FROM hello_realestate ".$strWhere. " GROUP BY floor ORDER BY  floor" ;
		$i = 0;
		WHILE($Row=sql_fetch_array($Result))
		{
			$RowFloor[]	=	$Row["floor"];
			$i++;
		}
		IF($i > 0)
		{
			sql_free_result($Result);
		}

		$strAptFloor = "";
		FOR($i=0;$i<COUNT($RowFloor);$i++)
		{
			IF($RowFloor[$i] == $apt_floor)
			{
				$strAptFloor = $apt_floor;
				break;
			}
		}

		IF($strAptFloor)
		{
			$strWhere .= " AND floor='".$strAptFloor."'";
		}

		$Query = "SELECT seq, price FROM hello_realestate ".$strWhere." ORDER BY seq DESC";
		$Result = sql_query($Query, $connect);

		IF($Row=sql_fetch_array($Result))
		{
			$RowPrice	=	$Row["price"];
			sql_free_result($Result);
		}

		return $RowPrice;
	}

	Public Function Sale_percent($si)
	{
		SWITCH($si)
		{
			CASE "서울특별시" : $retval = 0.83; BREAK;
			CASE "경기도" : $retval = 0.80; BREAK;
			CASE "인천광역시" : $retval = 0.80; BREAK;
			DEFAULT : $retval = 0.75; BREAK;
		}
		return $retval;
	}

	Public Function Ad_pid()
	{
		$retval =	ARRAY(
							ARRAY("gdn","GDN"),
							ARRAY("naverkeyword","네이버키워드"),
							ARRAY("naverbrand","N브랜드"),
							ARRAY("daumkeyword","다음키워드"),
							ARRAY("goolekeyword","구글키워드"),
							ARRAY("kakaopay","카카오페이")
						 );
		return $retval;
	}

	Public FUNCTION Query_Save($strVal)
	{

		$dongArr = EXPLODE(",",$strVal["dong"]);
		$apt_nameArr = EXPLODE(",",$strVal["apt_name"]);
		$apt_areaArr = EXPLODE(",",$strVal["apt_area"]);

		IF($strVal["rdo_apt"] == "1") {
			//$addr	=	 $strVal["si"]." ".$strVal["gu"]." ".$dongArr[1]." ".$apt_nameArr[1]." ".$strVal["dong_num"]."동 ".$strVal["floor_num"]."층 ".$strVal["ho_num"]."호 (".$apt_areaArr[1]." ㎡)";
			$addr	=	 $strVal["si"]." ".$strVal["gu"]." ".$dongArr[1]." ".$apt_nameArr[1]." ".$strVal["dong_num"]."동 ".$strVal["floor_num"]."층 ".$strVal["ho_num"]."호 (".$apt_areaArr[2]." ㎡)";
			$other = "apt_area=".$strVal["dong_num"]."^dong_num=".$strVal["dong_num"]."^floor_num=".$strVal["floor_num"]."^ho_num=".$strVal["ho_num"]."^price=".$strVal["price"]."^rprice=".$strVal["rprice"]."^ramount=".$strVal["ramount"];


		} ELSEIF($strVal["rdo_apt"] == "2") { // 직접 입력
			$addr	=	$strVal["si"]." ".$strVal["gu"]." ".$dongArr[1]." ".$strVal["apt_name2"];

			$other = "price=".$strVal["price"]."^rprice=".$strVal["rprice"]."^ramount=".$strVal["ramount"];
		}

		$ip     = $_SERVER['REMOTE_ADDR'];
		$device = getDevice();
		$area   = $_SERVER['GEOIP_CITY'];

		$ramount	 = STR_REPLACE(",","",$strVal["ramount"])*10000;

		$strVal["pid"]			=	preg_replace("!<script(.*?)<\/script>!is","",$strVal["pid"]);
		$strVal["rname"]		=	preg_replace("!<script(.*?)<\/script>!is","",$strVal["rname"]);

		$strVal["pid"]			=	strip_tags($strVal["pid"]);
		$strVal["rname"]		=	strip_tags($strVal["rname"]);

		$sql = "
			INSERT INTO
				cf_apat_loan_request
			SET
				skin		      = '2',
				type          = '1',
				name          = '".sql_real_escape_string($strVal["rname"])."',
				co_name       = '',
				hp            = '".masterEncrypt($strVal["rphone"], false)."',
				email         = '',
				loc           = '".sql_real_escape_string($addr)."',
				already_dept  = '".$already_dept."',
				tadwo         = '".$tadwo."',
				relation      = '".$relation."',
				wamt          = '".sql_real_escape_string($ramount)."',
				purpose       = '',
				period        = '',
				income        = '',
				wtime         = '',
				tenant        = '',
				content       = '',
				ip            = '".$ip."',
				device        = '".$device."',
				area          = '".$area."',
				judge_state   = '1',
				pid			      = '".sql_real_escape_string($strVal["pid"])."',
				other		      = '".$other."',
				kb_limit	    = '".$strVal["price"]."',
				max_limit	    = '".$strVal["rprice"]."',
				regdate       = NOW()";

//		echo $sql;
		sql_query($sql);
	}
}
?>
