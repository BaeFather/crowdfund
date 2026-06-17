<?

include_once('./_common.php');

/*
if( !preg_match('/183\.98\.101/', $_SERVER['REMOTE_ADDR']) ) {
	msg_replace("온라인투자연계금융업 시스템 변경으로\\n신규 회원가입이 일시 중단됩니다.\\n빠른 시일 내에 회원가입 서비스를 제공하겠습니다.", "/");
}
*/

/*
if( preg_match('/183\.98\.101\.114/', $_SERVER['REMOTE_ADDR']) ) {
	print_rr($_REQUEST); exit;
}
*/

include_once('../lib/function_prc.php');
include_once('../lib/etc.lib.php');

$strMemberClass = new strMemberRefererCheck();

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }
while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = preg_replace("/(\'|\"|\#|\=|\(|\)|\+|\%|\*)/iu", "$1;", $v); }

if(!$pid) { $pid = get_cookie('ck_pid'); }			// 입력된 pid가 쿠키pid 보다 우선한다.

if($pid) {

	if( in_array($pid, array_keys($CONF['PARTNER'])) ) {
		// 기간이 있는 이벤트 pid 인경우 기간 체크
		$ingEvent = sql_fetch("SELECT event_no, sdate, edate FROM cf_partner_event_config WHERE pid='$pid' ORDER BY idx DESC LIMIT 1");
		if($ingEvent['event_no'] && $ingEvent['edate'] < G5_TIME_YMD) {
			set_cookie("ck_pid", "", -1);		// 기간종료시 쿠키 제거
			unset($pid);
		}
	}
	else {
		set_cookie("ck_pid", "", -1);		// 미설정 pid 인 경우 쿠키 제거
		unset($pid);
	}

}

$g5['title'] = '회원가입';
$g5['top_bn'] = "/images/member/sub_join.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

if ($co['co_include_head'])
	@include_once($co['co_include_head']);
else
	include_once('./_head.php');


if(!in_array($tab, array('p','c'))) { header('Location: /', true, 302); exit; }

$tab = ($tab) ? $tab : 'p';


add_stylesheet('<link rel="stylesheet" href="'.$member_skin_url.'/style.css?ver=2022040600">', 0);

?>
<!-- 본문내용 START -->

<style>
.register_form input::placeholder { font-size:12px;color:#bbb; }
</style>

<script>
var idpw_type     = '<?=$idpw_type?>';
var id_min_length = <?=$ID_LIMIT[$idpw_type]['min_length']?>;
var id_max_length = <?=$ID_LIMIT[$idpw_type]['max_length']?>;
var pw_min_length = <?=$PW_LIMIT[$idpw_type]['min_length']?>;
var pw_max_length = <?=$PW_LIMIT[$idpw_type]['max_length']?>;
var pw_describe   = '<?=$PW_LIMIT[$idpw_type]['describe']?>';
</script>

<script src="/js/join_info_form.js?ver=2022101101"></script>

<div id="content">
	<div class="content">
		<div class="register_form">
			<span class="title">헬로펀딩 <strong>회원가입</strong></span>
			<div class="clearfix"></div>
			<br/>
			<div class="register_tabs">
				<ul>
					<li <?=(empty($tab) || $tab=='p')?'class="on"':'';?> onClick="location.href='?tab=p<?=($pid)?"&pid={$pid}":"";?>';"><a href="#">개인 회원가입</a></li>
					<li <?=($tab=='c')?'class="on"':'';?> onClick="location.href='?tab=c<?=($pid)?"&pid={$pid}":"";?>';"><a href="#">법인 회원가입</a></li>
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