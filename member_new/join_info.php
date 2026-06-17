<?
/*
2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가
*/
include_once('./_common.php');

include_once('../lib/function_prc.php');
include_once('../lib/etc.lib.php');

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }


$pid = $_GET["pid"];

IF(!$pid) { $pid = get_cookie('ck_pid'); }

$g5['title'] = '회원가입';
$g5['top_bn'] = "/images/member/sub_join.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
	@include_once($co['co_include_head']);
else
	include_once('./_head.php');


if(!in_array($tab, array('p','c'))) { header('Location: /', true, 302); exit; }

$tab = ($tab) ? $tab : 'p';


//add_stylesheet('<link rel="stylesheet" href="../css_new/member.css">', 0);
//add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css">', 0);
add_stylesheet('<link rel="stylesheet" href="./css/common.css">', 0);
add_stylesheet('<link rel="stylesheet" href="./css/member_new.css">', 0);
?>


<!-- 본문내용 START -->

<div id="content" style="background-color: #f0f3f8">
	<div class="content" id="newSign">
		<div class="register_form">
			<p class="title"><? if($tab == 'p') { echo '개인'; } else { echo '법인'; } ?>회원가입</p>
			<div class="clearfix"></div>
			<br/>
			<div class="register_tabs">
				<ul>
					<li <? echo (empty($tab) || $tab=='p') ? 'class="on"' : '';?> onClick="location.href='?tab=p&pid=<?php ECHO $pid;?>';"><a href="#">개인</a></li>
					<li <? echo ($tab=='c') ? 'class="on"' : '';?> onClick="location.href='?tab=c&pid=<?php ECHO $pid;?>';"><a href="#">법인</a></li>
				</ul>
			</div>
			<div class="clearfix"></div>
			<?
				if(in_array($rec_mb_id, array('donga_expo','seoul_money_show'))) {
					include_once("join_info_form_p_expo.php");
				}
				else {
					include_once("join_info_form_".$tab.".php");
				}
			?>
		</div>
	</div>
</div>

<!-- 본문내용 E N D -->

<?
if ($co['co_include_tail'])
	@include_once($co['co_include_tail']);
else
	include_once('./_tail.php');
?>
