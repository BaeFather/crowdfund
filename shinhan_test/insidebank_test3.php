<?
exit;
exit;
exit;

################################################################################
## /usr/local/php/bin/php -q /home/crowdfund/public_html/insidebank_test3.php
################################################################################

set_time_limit(0);

include_once("_common.php");

$base_path = "/home/crowdfund/public_html";
include_once($base_path."/lib/common.lib.php");
include_once($base_path."/lib/insidebank.lib.php");

$SHISDBK['target_host']       = "222.231.31.120";
//$SHISDBK['target_host']       = "222.231.31.34";
$SHISDBK['000']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5000";  //TESTCALL
$SHISDBK['128']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5001";
$SHISDBK['128']['enc_key']    = "ECgYB1tH7pFPbDvT";
$SHISDBK['256']['target_url'] = "http://".$SHISDBK['target_host']."/IFX5002";
$SHISDBK['256']['enc_key']    = "esYax1AADKlC7KmTjhdcd6itjLQ+2cyU";


while( list($k, $v)=@each($_REQUEST) ) { ${$k} = @trim($v); }

$enc_bit = ($_SERVER['argv']['1']) ? $_SERVER['argv']['1'] : $enc_bit;
$enc_bit = ( in_array($enc_bit, array('000','128','256')) ) ? $enc_bit : '256';
$mode    = ($_SERVER['argv']['2']) ? $_SERVER['argv']['2'] :	$mode;

/*
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5093','member_idx'=>'3351','prin_rcv_no'=>'M3351P168I5093','amount'=>'400,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5094','member_idx'=>'3352','prin_rcv_no'=>'M3352P168I5094','amount'=>'500,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5095','member_idx'=>'3537','prin_rcv_no'=>'M3537P168I5095','amount'=>'142,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5096','member_idx'=>'3641','prin_rcv_no'=>'M3641P168I5096','amount'=>'300,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5097','member_idx'=>'78','prin_rcv_no'=>'M78P168I5097','amount'=>'500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5098','member_idx'=>'3878','prin_rcv_no'=>'M3878P168I5098','amount'=>'500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5099','member_idx'=>'3714','prin_rcv_no'=>'M3714P168I5099','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5100','member_idx'=>'3077','prin_rcv_no'=>'M3077P168I5100','amount'=>'100,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5101','member_idx'=>'3920','prin_rcv_no'=>'M3920P168I5101','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5102','member_idx'=>'3298','prin_rcv_no'=>'M3298P168I5102','amount'=>'3,200,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5103','member_idx'=>'2120','prin_rcv_no'=>'M2120P168I5103','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5104','member_idx'=>'666','prin_rcv_no'=>'M666P168I5104','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5105','member_idx'=>'3723','prin_rcv_no'=>'M3723P168I5105','amount'=>'100,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5106','member_idx'=>'3839','prin_rcv_no'=>'M3839P168I5106','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5107','member_idx'=>'3665','prin_rcv_no'=>'M3665P168I5107','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5108','member_idx'=>'2289','prin_rcv_no'=>'M2289P168I5108','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5109','member_idx'=>'3085','prin_rcv_no'=>'M3085P168I5109','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5110','member_idx'=>'3589','prin_rcv_no'=>'M3589P168I5110','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5111','member_idx'=>'1939','prin_rcv_no'=>'M1939P168I5111','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5112','member_idx'=>'3932','prin_rcv_no'=>'M3932P168I5112','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5113','member_idx'=>'3505','prin_rcv_no'=>'M3505P168I5113','amount'=>'300,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5114','member_idx'=>'3689','prin_rcv_no'=>'M3689P168I5114','amount'=>'2,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5115','member_idx'=>'3464','prin_rcv_no'=>'M3464P168I5115','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5116','member_idx'=>'605','prin_rcv_no'=>'M605P168I5116','amount'=>'500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5117','member_idx'=>'1115','prin_rcv_no'=>'M1115P168I5117','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5118','member_idx'=>'2767','prin_rcv_no'=>'M2767P168I5118','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5119','member_idx'=>'3269','prin_rcv_no'=>'M3269P168I5119','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5120','member_idx'=>'3421','prin_rcv_no'=>'M3421P168I5120','amount'=>'2,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5121','member_idx'=>'3268','prin_rcv_no'=>'M3268P168I5121','amount'=>'3,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5122','member_idx'=>'1894','prin_rcv_no'=>'M1894P168I5122','amount'=>'400,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5123','member_idx'=>'3948','prin_rcv_no'=>'M3948P168I5123','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5124','member_idx'=>'3344','prin_rcv_no'=>'M3344P168I5124','amount'=>'3,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5125','member_idx'=>'3360','prin_rcv_no'=>'M3360P168I5125','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5126','member_idx'=>'1682','prin_rcv_no'=>'M1682P168I5126','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5127','member_idx'=>'464','prin_rcv_no'=>'M464P168I5127','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5128','member_idx'=>'976','prin_rcv_no'=>'M976P168I5128','amount'=>'500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5129','member_idx'=>'3952','prin_rcv_no'=>'M3952P168I5129','amount'=>'3,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5130','member_idx'=>'3899','prin_rcv_no'=>'M3899P168I5130','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5131','member_idx'=>'3895','prin_rcv_no'=>'M3895P168I5131','amount'=>'2,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5132','member_idx'=>'1529','prin_rcv_no'=>'M1529P168I5132','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5133','member_idx'=>'3959','prin_rcv_no'=>'M3959P168I5133','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5134','member_idx'=>'3957','prin_rcv_no'=>'M3957P168I5134','amount'=>'1,500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5135','member_idx'=>'3840','prin_rcv_no'=>'M3840P168I5135','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5136','member_idx'=>'2170','prin_rcv_no'=>'M2170P168I5136','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5137','member_idx'=>'3731','prin_rcv_no'=>'M3731P168I5137','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5138','member_idx'=>'3518','prin_rcv_no'=>'M3518P168I5138','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5139','member_idx'=>'1513','prin_rcv_no'=>'M1513P168I5139','amount'=>'3,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5140','member_idx'=>'3342','prin_rcv_no'=>'M3342P168I5140','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5141','member_idx'=>'3855','prin_rcv_no'=>'M3855P168I5141','amount'=>'400,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5142','member_idx'=>'2279','prin_rcv_no'=>'M2279P168I5142','amount'=>'2,600,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5143','member_idx'=>'3639','prin_rcv_no'=>'M3639P168I5143','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5144','member_idx'=>'3011','prin_rcv_no'=>'M3011P168I5144','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5145','member_idx'=>'2112','prin_rcv_no'=>'M2112P168I5145','amount'=>'200,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5146','member_idx'=>'3552','prin_rcv_no'=>'M3552P168I5146','amount'=>'200,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5147','member_idx'=>'3943','prin_rcv_no'=>'M3943P168I5147','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5148','member_idx'=>'2610','prin_rcv_no'=>'M2610P168I5148','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5149','member_idx'=>'3970','prin_rcv_no'=>'M3970P168I5149','amount'=>'2,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5150','member_idx'=>'3881','prin_rcv_no'=>'M3881P168I5150','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5151','member_idx'=>'3671','prin_rcv_no'=>'M3671P168I5151','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5152','member_idx'=>'3972','prin_rcv_no'=>'M3972P168I5152','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5153','member_idx'=>'3727','prin_rcv_no'=>'M3727P168I5153','amount'=>'3,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5154','member_idx'=>'3759','prin_rcv_no'=>'M3759P168I5154','amount'=>'1,500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5155','member_idx'=>'2392','prin_rcv_no'=>'M2392P168I5155','amount'=>'500,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5156','member_idx'=>'3079','prin_rcv_no'=>'M3079P168I5156','amount'=>'200,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5157','member_idx'=>'685','prin_rcv_no'=>'M685P168I5157','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5158','member_idx'=>'3544','prin_rcv_no'=>'M3544P168I5158','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5159','member_idx'=>'3953','prin_rcv_no'=>'M3953P168I5159','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5160','member_idx'=>'2657','prin_rcv_no'=>'M2657P168I5160','amount'=>'100,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5161','member_idx'=>'3886','prin_rcv_no'=>'M3886P168I5161','amount'=>'1,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5162','member_idx'=>'2742','prin_rcv_no'=>'M2742P168I5162','amount'=>'5,000,000');
$LIST[] = array('product_idx'=>'168','invest_idx'=>'5163','member_idx'=>'1459','prin_rcv_no'=>'M1459P168I5163','amount'=>'1,600,000');


for($i=0,$j=1; $i<count($LIST); $i++,$j++) {

	// 투자자등록(2200)
	$ARR['REQ_NUM']     = "020";
	$ARR['SUBMIT_GBN']  = "02";												// 거래구분(등록:02)
	$ARR['LOAN_SEQ']    = $LIST[$i]['product_idx'];		// 대출식별번호
	$ARR['INV_SEQ']     = $LIST[$i]['invest_idx'];		// 투자자건수일련번호(변경불가항목)
	$ARR['INV_CUST_ID'] = $LIST[$i]['member_idx'];		// 투자자고객ID
	$ARR['PRIN_RCV_NO'] = $LIST[$i]['prin_rcv_no'];		// M회원번호P상품번호I투자번호
	$ARR['INV_AMT']     = preg_replace('/,/', '', $LIST[$i]['amount']);

	print_rr($ARR);

	$insidebank_result = insidebank_request($enc_bit, $ARR, $mode);
	print_rr($insidebank_result);

	sleep(1);

}
*/

echo "exit";

exit;

?>