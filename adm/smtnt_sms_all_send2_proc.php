<?php

include_once('./_common.php');

include_once('../lib/sms.lib.php');

// post로 받은 데이터를 변수화
foreach($_POST as $k=>$v) {
	$$_POST[$k] = $v;
}

##################################################################
## 메세지 발송
##################################################################

if($mode=="send") {

	if($send_time == 'r') {
		$send_date = $send_ymd.' '.$send_h.':'.$send_i.':00';
	}
	else {
		$send_date = null;
	}
	
	if(count($chk) == 0) {
		alert('발송할 회원이 선택되지 않았습니다.','./smtnt_sms_all_send2.php');
		exit;
	}
	else {
		$send_id = get_sms_send_id_smtnt();
		
		foreach($chk as $k => $v) {

			$to_hp = $v;

			if($to_hp != '') {
				$sms_res = unit_sms_send_smtnt($from_hp,$to_hp,$sms_msg,$send_date, $send_id);
			}
		}
		
		alert('선택하신 회원들에게 정상적으로 SMS 발송 되었습니다.', './smtnt_sms_all_send2.php');
		
	}

}

?>