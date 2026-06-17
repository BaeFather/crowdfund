<?
include_once('_common.php');
?>
<?
if ($member["mb_id"]) {

} else {
	for ($i=0 ; $i<count($chk_item) ; $i++) {
		$ss_auto_inv[$i]["grp_idx"] = $grp_idx[$i];
		$ss_auto_inv[$i]["inv_yn"] = $chk_item[$i];
		$ss_auto_inv[$i]["auto_money"] = $auto_money[$i];
		//echo "$grp_idx[$i] $chk_item[$i]<br/>";
		//set_session('ss_auto_inv["'.$i.'"]["grp_idx"]',$grp_idx[$i]);
		//set_session('ss_auto_inv["'.$i.'"]["inv_yn"]',$chk_item[$i]);
	}

	$_SESSION['ss_auto_inv'] = $ss_auto_inv;
	$_SESSION['ss_auto_amt'] = $auto_money;
	//set_session('test_aa',   "111");
	
	$res['sess_yn'] = "Y";
}
$res["aa"] = "abc";
echo json_encode($res);
?>