<?
###############################################################################
## 임의 문자 발송 프록그램
## sql 쿼리문을 수정해서 대상선별을 먼저 한다음 문자를 보내세요.
## 2021-01-19 전승찬 천재
###############################################################################

exit;

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";

include_once($base_path . "/common.cli.php");
include_once($base_path . "/lib/sms.lib.php");
?>
<?
// 대상 선정
$T_LIST = array();

$sql = "SELECT A.mb_no, A.mb_name, A.mb_hp
		  FROM g5_member A
		 WHERE A.mb_leave_date=''
		   AND A.is_rest='N'
		   AND A.mb_sms='1'
		 ";
$res = sql_query($sql);
$cnt = sql_num_rows($res);

echo "탈퇴하지 않고 휴면계정이 아니고 문자수신동의 한 회원 ".$cnt. " 명<br/>";

$idx=0;
for ($i=0 ; $i<$cnt ; $i++) {
	$row = sql_fetch_array($res);
	$row["mb_hp"] = masterDecrypt($row["mb_hp"]);
	$T_LIST[$idx++] = $row;
}





$sql2 = "SELECT B.mb_no, B.mb_name, B.mb_hp
		   FROM cf_auto_invest_config_user A
	  LEFT JOIN g5_member B ON(B.mb_no=A.member_idx) 
		  WHERE B.mb_sms='0'
	   GROUP BY A.member_idx";
$res2 = sql_query($sql2);
$cnt2 = sql_num_rows($res2);

for ($i=0 ; $i<$cnt2 ; $i++) {
	$row2 = sql_fetch_array($res2);
	$row2["mb_hp"] = masterDecrypt($row2["mb_hp"]);
	$T_LIST[$idx++] = $row2;
}

echo "문자수신동의 하지 않고 자동투자 설정한 회원".$cnt2. " 명<br/>";

$test_sql = "SELECT A.mb_hp FROM g5_member A WHERE mb_id='romrom'";
$test_res = sql_query($test_sql);
$test_row = sql_fetch_array($test_res);
$aa = masterDecrypt($test_row['mb_hp'],false);


echo $_admin_sms_number;
$send_msg = "[헬로펀딩 자동투자 서비스 중단 안내]

안녕하세요 헬로펀딩입니다.

\'온라인투자연계금융업 및 이용자 보호에 관한 법\' 시행에 따라 헬로펀딩 투자 회원님들의 권익보호 및 투자 서비스 개편을 위해 기존 이용하셨던 [자동투자] 서비스가 중단됨을 안내 드립니다.

기존 [자동투자] 서비스를 이용하시는 투자자 분들의 불편함을 최소화하기 위해 순차적으로 [자동투자] 서비스를 중단하고자 하오니 유의하여 주시기 바랍니다.

자세한 일정은 헬로펀딩 공지사항에서 확인 가능합니다. 더불어 빠른 투자 마감에 투자 기회 놓치지 않으시도록 \'헬로펀딩 플러스친구\' 추가하고 상품 소식 받아보세요.

제도권 금융으로 발전해 나갈 온라인투자연계금융 \'헬로펀딩\'에 앞으로도 많은 관심 부탁드립니다.

감사합니다.

공지사항 : https://bit.ly/2KpS38u

플러스친구 추가 : https://bit.ly/38U3GOn

무료거부 : 0809000982";
$to_hp = "01086246176"; // 전승찬
$to_hp = "01067241409"; // 이철규
//$to_hp = "01088944740"; // 이상규
$send_date="";

echo "<pre>";echo $send_msg;echo "</pre>";

//unit_sms_send($_admin_sms_number, $to_hp, $send_msg, $send_date);
//echo "<pre>";print_r($T_LIST); echo "</pre>";
echo "<table border=1>";
for ($m=0 ; $m<count($T_LIST) ; $m++) {
	$send = "";
	if ($T_LIST[$m]["mb_hp"]) {
		$send = "Y";
		unit_sms_send($_admin_sms_number, $T_LIST[$m]["mb_hp"], $send_msg, $send_date);
	}
	?>
	<tr>
		<td><?=$m?></td>
		<td><?=$T_LIST[$m]["mb_no"]?></td>
		<td><?=$T_LIST[$m]["mb_name"]?></td>
		<td><?=$T_LIST[$m]["mb_hp"]?></td>
		<td><?=$send?></td>
	</tr>
	<?
}
echo "</table>";
?>