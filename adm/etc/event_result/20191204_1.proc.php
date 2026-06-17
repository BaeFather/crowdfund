<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../../../lib/sms.lib.php');

$strRequest = ARRAY("idx","content","sphone","cphone");

FOR($i=0;$i<COUNT($strRequest);$i++)
{
	${$strRequest[$i]} = $_POST[$strRequest[$i]];
}

IF(!$idx[0] || COUNT($idx) == 0)
{
	/*
	$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
	ECHO json_encode($objval);
	EXIT;
	*/
}
/*
	$sphone 발신번호
	$content 내용
	$idx 받는사람
*/
$intTime = TIME();

// 관리자 발송

$idx2	=	 ARRAY("01032809295","01056179090");
FOR($i=0;$i<COUNT($idx2);$i++)
{
	unit_sms_send($sphone, $idx2[$i], $content, "");
}

$cphoneArr	=	EXPLODE("\n",$cphone);

IF(COUNT($cphoneArr) > 0)	// 수신번호 처리
{
	FOR($i=0;$i<COUNT($cphoneArr);$i++)
	{
		unit_sms_send($sphone, $cphoneArr[$i], $content, "");
	}
}

IF(COUNT($idx) > 0)
{
	FOR($i=0;$i<COUNT($idx);$i++)
	{
		unit_sms_send($sphone, $idx[$i], $content, "");
	}
}
sql_close($connect_for);

$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode("문자가 정상 발송 되었습니다")),"retval"=>"");
ECHO json_encode($objval);
EXIT;
?>