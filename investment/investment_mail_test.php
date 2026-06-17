<?
include_once('_common.php');

include_once(G5_LIB_PATH.'/mailer.lib.php');

?>
<form method="post">
회원번호 : <?=$member["mb_no"]?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
투자번호 <input type="text" value="<?=$invest_idx?>" name=invest_idx /> <input type="submit" value="확인"/>
</form>

<?
if ($member["mb_no"] AND $invest_idx) {
	$mail_send_res = invest_cfm_mail($invest_idx);
}

function invest_cfm_mail($invest_idx) {
	
	global $CONF;
	global $member;

	echo "$invest_idx<br/>";

	/*
	$member_sql = "SELECT mb_name, mb_email FROM g5_member WHERE mb_no='".$member["mb_no"]."'";
	$member_res = sql_query($member_sql);
	$member_cnt = sql_num_rows($member_res);
	if (!$member_cnt) return;
	$member_row = sql_fetch_array($member_res);
	*/

	$invest_sql = "SELECT A.* ,
						  B.start_num , B.title , B.invest_period, B.invest_days, B.invest_return
					 FROM cf_product_invest A
				LEFT JOIN cf_product B ON(A.product_idx=B.idx)
					WHERE A.idx='$invest_idx' AND A.member_idx='".$member["mb_no"]."'";
	$invest_res = sql_query($invest_sql);
	$invest_row = sql_fetch_array($invest_res);

	$product_idx = $invest_row["product_idx"];
	$ho = $invest_row["start_num"];
	$title = $invest_row["title"];
	$amount = $invest_row["amount"];
	if ($invest_row["invest_period"]==1) $gigan = $invest_row["invest_days"]." 일";
	else $gigan = $invest_row["invest_period"]." 개월";
	$sooek = $invest_row["invest_return"];


	$mail_subject = "[헬로펀딩] 투자 확인서";
	$mail_from_name = "(주)헬로펀딩";
	$mail_from_email = "cs@hellofunding.co.kr";
	//$mail_from_email = $CONF['customer_mail'];
	

	$mail_to_name  = $member["mb_name"];
	$mail_to_email = $member["mb_email"];
	
	// 임시
	//$mail_to_name = "임금님";
	//$mail_to_email = "jsc6176@hellofunding.co.kr";
	//$mail_to_email = "jsc6176@naver.com";
	$mail_to_name = "이상규";
	$mail_to_email = "arpino123@naver.com";
echo $mail_subject."<br/>".$mail_from_name." ($mail_from_email)<br/>".$mail_to_name." ($mail_to_email)<br/>";	


	$mail_form = '
	<!doctype html>
	<html lang="en">
	 <head>
	  <meta charset="UTF-8">
	  <meta name="Generator" content="EditPlus®">
	  <meta name="Author" content="">
	  <meta name="Keywords" content="">
	  <meta name="Description" content="">
	  <title>Document</title>
	 </head>
	 <body>

	<div id="frameS" style="margin:0 auto;width:802px;background:#fff">

		<div style="width:100%;height:82px;"><img src="https://www.hellofunding.co.kr/images/mail/mail_top.png" width11="802"></div>
		
	  
		<div style="width:740px;padding:30px;min-height:250px;border-left:1px solid #1a1d28;border-right:1px solid #1a1d28; font-size;14px;border-bottom:0px solid #1a1d28">
	  
			<br><br>
			
			<span style="color: rgb(0, 0, 0);">[헬로펀딩] '.$ho.'호 상품 투자확인서</span>
			
			<br><br>
			
			<span style="color: rgb(0, 0, 0);">
				회원님이 투자하신 '.$ho.'호 상품의 투자확인서를 교부해드립니다.<br/>
				해당 상품의 내용을 충분히 이해하고 투자하였으며<br/>
				이용약관, 연계투자약관에 따른 투자위험을 확인하고 동의하였음을 확인합니다.<br/><br/>
				
				<b>상품명</b> : '.$title.'<br/>
				<b>투자금액</b> : '.number_format($amount).' 원<br/>
				<b>투자기간</b> : '.$gigan.'<br/>
				<b>예상 투자수익률</b> : 연 '.$sooek.' %<br/>
				<b>투자상품설명</b> : <a href="https://www.hellofunding.co.kr/investment/investment.php?prd_idx='.$product_idx.'" target=_blank style="border:1px solid #B7DFFC; background-color:#B7DFFC;color:black; font-size:12px; padding:3px; text-decoration:none; border-radius:3px;">상품설명보기</a><br/>
				
				<br/>감사합니다.<br/>

			</span>

			<br><br>
			
		</div>

		<div style="width:770px; padding-left:30px; padding-top: 10px; padding-bottom:10px; background-color:#F2F2F2; border:1px solid #1a1d28; border-top:0px;">
			<span style="font-size: 10pt;">본 메일은 온라인투자연계금융업 및 이용자 보호에 관한 법률에 따라 발송되었습니다.</span>
			<br><br/>
			<span style="font-size: 10pt;">
			㈜헬로핀테크<br/>
			서울특별시 강남구 대치동 945-10 KT&G 대치타워 7층<br/>
			1588-6760<br/>
			</span>
		</div>
				

	</div>

	 </body>
	</html>
	';


//$res = mail($mail_to_email, $mail_subject, "AAA",  implode("\r\n", $headers));

	//$mailSend = new MailSend;
	//$mailSend->NewHeader($mail_subject, $MAIL['senderName'], $MAIL['senderMail'], $MAIL['multi-part']);
	//$mailSend->AddBody($MAIL['contents']);
	//$rst = $mailSend->SendMail($MAIL['receiverMail']);
	
	//$res = mailer($mail_from_name, $mail_from_email, $mail_to_email, $mail_to_name, $mail_subject, $mail_form, 1);
	echo "<br/>--- res $res ---<br/>";
	?>	
	<br/><br/><br/><br/><br/>
	<?
	echo $mail_form;
	?>
	<?
}
?>
