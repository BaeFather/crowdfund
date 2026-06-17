<?

include_once('_common.php');
include_once(G5_LIB_PATH . '/wlf.lib.php');

$sdt = get_microtime();


$mb_no = '817';			// akorea:228, hellofintech:50524

$WLF_RES = WLFSend($mb_no, 'WLF 전송테스트');

echo "결과 : "; print_rr($WLF_RES);


echo sprintf("%.2f", (get_microtime()-$sdt));


?>