<?
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가


// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver=20201223">', 0);


?>

<div id="content">
	<div class="location_top">

<? if($bo_table=='notice') { ?>
		<div>
			<h2 class="top_title">헬로펀딩 공지사항</h2>
			<p class="top_text">헬로펀딩의 다양한 소식을 확인할 수 있습니다.<br class="br"></p>
		</div>
<? } ?>

		<!--div class="location">
<?
if(false) {
	if($bo_table=='notice') {
		echo '<span><a href="'.G5_URL.'/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">공지사항</b>' . PHP_EOL;
	}
	else if($bo_table=='recruit') {
		echo "<span></span><b class=\"blue\">채용안내</b>\n";
	}
}
?>
		</div-->
	</div>

	<div class="content">
<?
if($bo_table=='notice') {
?>
		<div style="border:0px solid black;margin-bottom:20px;font-size:20px;height:20px;">
			<ul style="list-style:none;float:right;">
				<li style="float:left;font-family:spoqahansans;font-weight:400;margin-top:-6px;font-weight:<?=($sca=='')?'bold':'normal';?>;">
					<a href="/bbs/board.php?bo_table=notice" style="color:<?=($sca=='')?'#ee321f':'black';?>;">전체</a>
				</li>
				<li style="float:left;margin-left:20px;margin-top:-4px;">|</li>
				<li style="float:left;font-family:spoqahansans;font-weight:400;margin-top:-6px;margin-left:20px; font-weight:<?=($sca=='상품소식')?'bold':'normal';?>;">
					<a href="/bbs/board.php?bo_table=notice&sca=상품소식" style="color:<?=($sca=='상품소식')?'#ee321f':'black';?>;">상품소식</a>
				</li>
				<!--
				<li style="float:left;margin-left:20px;margin-top:-4px;">|</li>
				<li style="float:left;font-family:spoqahansans;font-weight:400;margin-top:-6px;margin-left:20px; font-weight:<?=($sca=='이벤트')?'bold':'normal';?>;">
					<a href="/bbs/board.php?bo_table=notice&sca=이벤트" style="color:<?=($sca=='이벤트')?'#5A86DF':'black';?>;">이벤트</a>
				</li>
				//-->
				<li style="float:left;margin-left:20px;margin-top:-4px;">|</li>
				<li style="float:left;font-family:spoqahansans;font-weight:400;margin-top:-6px;margin-left:20px; font-weight:<?=($sca=='이용안내')?'bold':'normal';?>;">
					<a href="/bbs/board.php?bo_table=notice&sca=이용안내" style="color:<?=($sca=='이용안내')?'#ee321f':'black';?>;">이용안내</a>
				</li>
			</ul>
		</div>
<?
}
?>
		<div class="type01">
			<table class="notice list">
				<tbody>
<?
for ($i=0; $i<count($list); $i++) {

	IF($list[$i]["wr_2"] and $bo_table=='notice')
	{
		$strLink = $list[$i]["wr_2"];		// 지정URL 존재시 해당 URL을 링크로 잡는다.
	} ELSE {
		$strLink = $list[$i]['href'];
	}
?>
					<tr>
						<th class="no">
							<?
							if ($list[$i]['is_notice']) // 공지사항
								echo '<strong>공지</strong>';
							else if ($wr_id == $list[$i]['wr_id'])
								echo "<span class=\"bo_current\">열람중</span>";
							else
								echo $list[$i]['num'];
							?>
						</th>
						<td>
							<div class="subject"><a href="<? echo $strLink; ?>"><? echo $list[$i]['subject']; ?></a></div>
							<span class="date"><?=str_replace('-','.',$list[$i]['datetime'])?></span>
							<? if($is_admin=="super") { ?><span class="hit"><a href="<?php ECHO $list[$i]['href'];?>"><?=number_format($list[$i]['wr_hit'])?></a></span><? } ?>
						</td>
					</tr>
<?
}
?>
				</tbody>
			</table>
		</div>

		<div style="margin-top:10px; text-align:right;">
			<? if($write_href) { ?><a href="<?=$write_href?>" class="btn_blue">글쓰기</a><? } ?>
		</div>
		<!--a href="/bbs/board.php?bo_table=notice&sca=상품소식"></a-->
		<!--a href="/bbs/board.php?bo_table=notice&sca=이용안내"></a-->

		<!-- 페이지 -->
		<?=$write_pages?>

	</div>
</div>
