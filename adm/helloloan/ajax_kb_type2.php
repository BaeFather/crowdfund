<?
include_once('./_common.php');
$use_sc_svr = "N";  // 내부 스크래핑 서버 사용 여부 Y는 사용
?>
<?
$chk_sql = "SELECT COUNT(*) cnt FROM hello_apt_kb WHERE mg_id='$mg_id' AND ju_seri2='' ";
$chk_row = sql_fetch($chk_sql);
if ($chk_row["cnt"]) {
	get_seri2($mg_id2);
}


function get_seri2($mg_id2) {
		
	global $use_sc_svr;

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
?>
<?
$sql = "SELECT ju_seri, jm, ju_seri2, top_gubun FROM hello_apt_kb WHERE mg_id='$mg_id' ORDER BY ju_seri";
$res = sql_query($sql);
$cnt = $res->num_rows;

$retn = array();
$retn["tp"] = array();

for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);

	$tp = array();

	$tp["ju_seri"] = $row["ju_seri"];
	$tp["jm"] = $row["jm"];
	$tp["ju_seri2"] = $row["ju_seri2"];
	$tp["top_gubun"] = $row["top_gubun"];

	
	$retn["tp"][$i] = $tp;
}

echo json_encode($retn, JSON_UNESCAPED_SLASHES);
?>