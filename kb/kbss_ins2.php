<?php include_once('../common.php'); ?>
<?php include_once('../lib/function_prc.php'); ?>
<?
die("safe die");
$json_params = file_get_contents("php://input");

$d = json_decode($json_params, true);

$uid = $_REQUEST["uid"];
$d_code = $_REQUEST["d_code"];
$d_name = $_REQUEST["d_name"];

$cnt = count($d["dataBody"]["data"]);

$chk_zip_sql = "SELECT * FROM scrap_kbss_zip WHERE uid='$uid' AND d_code='$d_code'";
$chk_zip_res = sql_query($chk_zip_sql);
$chk_zip_cnt = sql_num_rows($chk_zip_res);
if($chk_zip_cnt) {
	$chk_zip_row = sql_fetch_array($chk_zip_res);
	$zip_sql = "UPDATE scrap_kbss_zip SET ss_cnt=$cnt+$chk_zip_row[ss_cnt] WHERE idx='$chk_zip_row[idx]'";
	sql_query($zip_sql);
} else {
	$zip_sql = "INSERT INTO scrap_kbss_zip SET uid='$uid', d_code='$d_code', d_name='$d_name', ss_cnt='$cnt', insert_datetime=NOW()";
	sql_query($zip_sql);
}

echo "uid => $uid<br/>";
echo "d_code => $d_code<br/>";
echo "d_name => $d_name<br/>";
echo "<br/><br/>";
echo "$cnt 건<br/>";
echo "<br/><br/>";

for ($i=0 ; $i<$cnt ; $i++) {

	$r = array();

	$kday = $d["dataBody"]["data"]["$i"]["시세마감년월일"];
	$r["kijun"] = substr($kday,0,4).".".substr($kday,4,2).".".substr($kday,6,2);

	$r["mg_id"] = $d["dataBody"]["data"]["$i"]["시세물건식별자"];

	$r["dj_name"] = $d["dataBody"]["data"]["$i"]["단지명"];

	$r["addr"] = $d["dataBody"]["data"]["$i"]["주소"];

	if ($r["mg_id"]<>"KBA012138") continue;

	for ($j=0 ; $j<count($d["dataBody"]["data"]["$i"]["매매"]) ; $j++) {

		$r["ju_seri"] = $j+1;
		$r["jm"] = $d["dataBody"]["data"]["$i"]["매매"][$j]["전용면적"];
		$r["mm_t"] = str_replace(",","", $d["dataBody"]["data"]["$i"]["매매"][$j]["상위평균"] );
		$r["mm"] = str_replace(",","", $d["dataBody"]["data"]["$i"]["매매"][$j]["일반평균"] );
		$r["mm_b"] = str_replace(",","", $d["dataBody"]["data"]["$i"]["매매"][$j]["하위평균"] );
		

		echo "$i ";
		print_r($r);
		echo "<br/><br/>";

		$ins_sql = "INSERT INTO scrap_kbss SET
						uid = '$uid',
						d_code = '$d_code',
						d_code8 = substring('$d_code',1,8),
						d_name = '$d_name',
						kijun = '$r[kijun]',
						mg_id = '$r[mg_id]',
						dj_name = '$r[dj_name]',
						ju_seri = '$r[ju_seri]',
						jm = '$r[jm]',
						addr = '$r[addr]',
						mm_t = '$r[mm_t]',
						mm = '$r[mm]',
						mm_b = '$r[mm_b]',
						input_datetime = NOW()
					";
		sql_query($ins_sql);

		/* real 대상 테이블 업데이트 추가 김성환*/
		$rowIdx = "";
		$tablename = "hello_apt_kb";
		$SEcolumn = "idx";

		$Q2 = "SELECT idx FROM ".$tablename." WHERE mg_id='".$r["mg_id"]."' AND ju_seri='".$r["ju_seri"]."' order by idx";

		$R2 = sql_query($Q2);

		IF($R3=sql_fetch_array($R2))
		{
			$rowIdx		=	$R3["idx"];
			sql_free_result($R2);
		}

		IF($rowIdx)
		{
			$kind = "update";
		} ELSE {
			$kind = "save";
		}
		$kind = "save";

		$column = ARRAY("uid","d_code","d_name","kijun","mg_id","jong_code","jong_name","dj_name","ju_seri","row_id","jm","jmp","mp","ar1_code","ar1_name","ar2_code","ar2_name","ar3_code","ar3_name","tot_house","addr","mm_t","mm","mm_b","jun_t","jun","jun_b","ls_pri","ls_fee","cr_date","tot_block","in_date","srch_stop","pic","count_rs","pr_stop","input_datetime");

		$cvalues = ARRAY($uid,$d_code,$d_name,$r[kijun],$r[mg_id],$r[jong_code],$r[jong_name],$r[dj_name],$r[ju_seri],$r[row_id],$r[jm],$r[jmp],$r[mp],$r[ar1_code],$r[ar1_name],$r[ar2_code],$r[ar2_name],$r[ar3_code],$r[ar3_name],$r[tot_house],$r[addr],$r[mm_t],$r[mm],$r[mm_b],$r[jun_t],$r[jun],$r[jun_b],$r[ls_pri],$r[ls_fee],$r[cr_date],$r[tot_block],$r[in_date],$r[srch_stop],$r[pic],$r[count_rs],$r[pr_stop],DATE("Y-m-d"));

		fn_general_query_update($kind,$column,$cvalues,$tablename,$SEcolumn,$rowIdx,"",$connect_for);
		/* 업데이트 종료*/

	}


}

echo "<pre>";print_r($data);echo "<br/><br/>";
?>