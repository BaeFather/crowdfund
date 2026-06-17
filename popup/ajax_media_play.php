<?

$path = $_SERVER['DOCUMENT_ROOT'];
include_once($path . '/common.php');

while(list($k, $v)=each($_REQUEST)) { ${$k} = trim($v); }

if($type=='vod') {
	$table = "media_video_list";
}
else if($type=='live') {
	$table = "media_tv_list";
}

$allowfullscreen = (G5_IS_MOBILE) ? "allowfullscreen" : "allowfullscreen";

$DATA = sql_fetch("SELECT * FROM $table WHERE id='".$id."' AND display_yn='Y'");
if($DATA['id']) {

	if(preg_match("/youtube\.com/i", $DATA['video_link'])) {
		echo '<iframe id="video0" width="100%" height="100%" src="'.$DATA['video_link'].'?enablejsapi=1&playerapiid=video0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen allowTransparency="true"></iframe>' . PHP_EOL;
	}
	else {

		if($_SERVER['HTTPS']=='on') {
			echo '<iframe id="video0" width="100%" height="100%" src="http://hellolivetv.co.kr:80/onair.php?prd_idx=342" frameborder="0" allowTransparency="true"></iframe>' . PHP_EOL;
		}
		else {

?>

<video id="player1" autoplay controls preload stretching="fill" width="100%" height="100%" style="background:#000;" allowTransparency="true"></video>' . PHP_EOL;
<script>
	var video = document.getElementById('player1');
	if(Hls.isSupported()) {
		var hls = new Hls();
		hls.loadSource('<?=$DATA['video_link']?>');
		hls.attachMedia(video);
		hls.on(Hls.Events.MANIFEST_PARSED,function() {
			vide.play();
		});
	}
	else if(video.canPlayType('application/vnd.apple.mpegurl')) {
		video.src = '<?=$DATA['video_link']?>';
		video.addEventListener('canplay',function() {
			video.play();
		});
	}
</script>

<?
		}
	}

}
else {
	echo '<iframe id="video0" width="100%" height="100%" src="https://www.youtube.com/embed/cydrqyuABBw?enablejsapi=1&playerapiid=video0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen allowTransparency="true"></iframe>' . PHP_EOL;
}

?>