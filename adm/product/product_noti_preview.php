<?

include_once('./_common.php');

while(list($k, $v) = each($_POST)) { if(!is_array($k)) ${$k} = @trim($v); }


$gubun_title = '헬로펀딩 ';

if($gubun == '2') $gubun_title.= ' 공지사항';
else if($gubun == '3') $gubun_title.= ' 긴급공지';
else $gubun_title.= ' 상품 안내';

?>

<style>
@import url(https://fonts.googleapis.com/earlyaccess/notosanskr.css);
#sms_noti { margin:auto; width:100%; max-width:600px; font-size:1.2em; font-family:'Noto Sans KR', sans-serif; word-break:break-all; }
#sms_noti div { padding:8px; }
#sms_noti .titlebar { background:#073190; color:#FFFFFF; font-size:1.1em; text-align:center; }
#sms_noti .subject { margin-top:20px; font-size:1.0em; font-weight:500; text-align:center;}
#sms_noti .content { margin:0 auto; width:96%; padding:15px 5% 20px; font-size:0.8em; background:#F9F9F9; }
#sms_noti .bgOn { background:#EEE; }
#sms_noti .buttonArea { margin-top:10px; }
#sms_noti .btn_green { display:inline-block; padding:8px 0; width:100%; font-family:"NG"; font-size:0.9em; color:#fff; border-radius:3px; background-color:#00C5B0; border:0; cursor:pointer; }
#sms_noti table td {border:0}
</style>

<div id="sms_noti">
	<div class="titlebar"><?=$gubun_title?></div>
	<div class="subject"><?=$subject?></div>
<?
	for($i=0; $i<count($_REQUEST['cont']); $i++) {
		$bg = (($i%2)==0) ? 'bgOn' : '';

		$print_text = nl2br(preg_replace("/( )/", "&nbsp;", $_REQUEST['cont'][$i]));
		$print_text = url_auto_link($print_text);

		if( trim($_REQUEST['cont'][$i]) ) {
			echo "	<div class=\"content $bg\">\n";
			echo "		<table style=\"width:100%;\">\n";
			echo "			<tr>\n";
			echo "				<td>".$print_text."</td>\n";
			echo "			</tr>\n";
			echo "		</table>\n";
			echo "	</div>\n";
		}
	}
?>
	<div class="buttonArea">
		<button type="button" onClick="location.href=\'/investment/invest_list.php\';" class="btn_green">투자상품보기</button>
	</div>
</div>
