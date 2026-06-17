<?
if (!defined("_GNUBOARD_")) exit; // 개별 페이지 접근 불가
include_once(G5_LIB_PATH.'/thumbnail.lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css?ver=20201223">', 0);
add_javascript('<script src="<?=G5_JS_URL?>/viewimageresize.js"></script>', 0);

?>

<div id="content">
	<div class="location_top">

<? if($bo_table=='notice') { ?>
		<div>
			<h2 class="top_title">헬로펀딩 <span class="sky">공지사항</span></h2>
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
		<div class="type01 bbs">
			<table class="notice">
				<tbody>
					<tr>
						<td colspan="2">
							<div class="subject"><? echo cut_str(get_text($view['wr_subject']), 255); // 글제목 출력;?></div>
							<span class="date"><? echo date("Y.m.d", strtotime($view['wr_datetime'])) ?></span>
							<? if($is_admin=="super") { ?><span class="hit"><?=number_format($view['wr_hit'])?></span><? } ?>
						</td>
					</tr>
					<?php IF($bo_table == "notice" && $member['mb_level']=="9") { ?>
					<tr>
						<td colspan="2">
							<div class="subject"> 시작일 <? echo get_text($view['wr_3']);?> 일 ~ 종료일 <? echo get_text($view['wr_4']);?> 일 </div>
						</td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="2" class="con">
							<? echo get_view_thumbnail($view['content'],"1000"); ?>
						</td>
					</tr>
				<?
				if ($view['file']['count']) {
					$cnt = 0;
					for ($i=0; $i<count($view['file']); $i++) {
						if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
							$cnt++;
					}
				}
				?>

				<? if($cnt) { ?>
				<!-- 첨부파일 시작 { -->
					<tr>
						<th>첨부파일</th>
						<td>
						<?
						// 가변 파일
						for ($i=0; $i<count($view['file']); $i++) {
							if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
						 ?>
							<a href="<? echo $view['file'][$i]['href'];  ?>" class="view_file_download">
								<img src="../images/bbs/icon_file.png" alt="첨부파일" />
								<strong><? echo $view['file'][$i]['source'] ?></strong>
								<? echo $view['file'][$i]['content'] ?> (<? echo $view['file'][$i]['size'] ?>)
							</a>&nbsp;&nbsp;
						<?
							}
						}
				}
				?>
						</td>
					</tr>
				<!-- } 첨부파일 끝 -->
				</tbody>
			</table>
		</div>

		<div class="btnArea alignR">
			<? if ($update_href) { ?><a href="<? echo $update_href ?>" class="btn_blue" style="background-color:#ababab;">수정</a><? } ?>
			<? if ($delete_href) { ?><a href="<? echo $delete_href ?>" class="btn_blue" style="background-color:#ababab;" onclick="del(this.href); return false;">삭제</a><? } ?>
			<a href="<? echo $list_href ?>" class="btn_blue">목록</a>
		</div>

		<ul class="preList">
			<? if ($prev_href) { ?><li><span class="prev">이전글</span> <a href="<? echo $prev_href ?>"><? echo $prev_wr_subject;?></a> <span class="date"><? echo str_replace('-','.',substr($prev_datetime,0,10));?></span></li><? } ?>
			<? if ($next_href) { ?><li><span class="next">다음글</span> <a href="<? echo $next_href ?>"><? echo $next_wr_subject;?></a> <span class="date"><? echo str_replace('-','.',substr($next_datetime,0,10));?></span></li><? } ?>
		</ul>

	</div>
</div>
