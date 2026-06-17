<?

include_once('_common.php');
include_once(G5_LIB_PATH . '/wlf.lib.php');


//$sdt = get_microtime();


$MB_NO = array('78','79','86','106','107','817','3311','6412','8491','9622','10794','13733','14581','19627','20283','42557','46808','48343','49294','50079','50274','50524','50611','58615');

for($i=0; $i<count($MB_NO); $i++) {

	$WLF_RES = WLFSend($MB_NO[$i], 'WLF 전송테스트');

	echo "결과 : ";
	print_rr($WLF_RES, 'color:#AAA');
	echo "<br>\n";

}


//echo sprintf("%.2f", (get_microtime()-$sdt));


?>