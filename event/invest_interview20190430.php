<?


// redirect_url 이 설정된 페이지일 경우 실 URL로 접속시 redirect_url로 이동
if($_SERVER['REDIRECT_URL']=='') {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /special_interview/20190430");
	exit;
}

include_once('./_common.php');

$g5['title'] = $EVENT['title'];
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$event_idx = 4;

?>
<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span><a href="">HELLO! 투자자 인터뷰</a></span></div>

<? if(G5_IS_MOBILE) { ?>
  <div style="width:100%; padding:10px 2% 10px 2%; ">
		<div style="width:100%; margin:0 auto; border:1px solid #e8e8e8;">
			<p>
				<img src="/images/event/yim_img01.jpg" width="100%">
				<img src="/images/event/yim_img02.jpg" width="100%">
				<img src="/images/event/yim_img03.jpg" width="100%" >
				<img src="/images/event/yim_img04.jpg" width="100%">
				<img src="/images/event/yim_img05.jpg" width="100%">
				<img src="/images/event/yim_img06.jpg" width="100%">
				<img src="/images/event/yim_img07.jpg" width="100%">
				<img src="/images/event/yim_img08.jpg" width="100%">
				<img src="/images/event/yim_img09.jpg" width="100%">
				<img src="/images/event/yim_img10.jpg" width="100%">
		   </p>
		</div>
		<div class="b_r_btn_c"><span class="b_r_btn"><a href="<?=G5_URL?>/bbs/epilogue.php">전체 투자후기</a></span></div>
	</div>
<? } else { ?>
  <div style="width:100%; padding:10px 0;">
		<div style="width:750px; margin:0 auto;border:1px solid #e8e8e8;">
			<p>
				<img src="/images/event/yim_img01.jpg">
				<img src="/images/event/yim_img02.jpg">
				<img src="/images/event/yim_img03.jpg">
				<img src="/images/event/yim_img04.jpg">
				<img src="/images/event/yim_img05.jpg">
				<img src="/images/event/yim_img06.jpg">
				<img src="/images/event/yim_img07.jpg">
				<img src="/images/event/yim_img08.jpg">
				<img src="/images/event/yim_img09.jpg">
				<img src="/images/event/yim_img10.jpg">
			</p>
		</div>
		<div class="b_r_btn_c"><span class="b_r_btn"><a href="<?=G5_URL?>/bbs/epilogue.php">전체 투자후기</a></span></div>
	</div>
<? } ?>

</div>



<!-- 본문내용 E N D -->
<?

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');


if($_SERVER['REMOTE_ADDR']=='220.117.134.164') {
	//print_rr($_SERVER,'font-size:12px');
}

?>