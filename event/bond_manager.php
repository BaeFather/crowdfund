<?php

include_once('./_common.php');


$g5['title'] = $EVENT['title'];
$g5['top_bn'] = "";
$g5['top_bn_alt'] = "";

if (!$member["mb_id"] and $_POST['gubun']=="my")
	alert("로그인 후 이용 가능합니다.", G5_BBS_URL."/login.php?url=" . urlencode($_SERVER[PHP_SELF]."?gubun=".$_REQUEST['gubun']));

if ($co['co_include_head'])
    @include_once($co['co_include_head']);
else
    include_once('./_head.php');

$event_idx = 4;


?>
<style>
#paging_span { margin:0; padding:0; text-align:center; }
#paging_span span.arrow { padding:0; border:0; line-height:0; }
#paging_span span { display:inline-block; min-width:36px; color:#585657; line-height:33px; border:1px solid #D0D0D0; cursor:pointer }
#paging_span span.now { color:#fff; background-color:#000; border:1px solid #000; cursor:default }
</style>
<?
if ($_REQUEST['gubun'] and $_REQUEST['gubun']<>"my") {
	if ($_REQUEST['gubun']=="2") $wh = " and (A.state='2' or A.state='5') ";
	else $wh = " and A.state='".$_REQUEST['gubun']."' ";
} 
if ($_REQUEST['search_title']) {
	$wh .= " and A.title like '%".$_REQUEST['search_title']."%' ";
}

if ($_REQUEST['gubun']=="my") {
	/*
	$sql_tot = "select count(A.idx) AS cnt
				from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B , cf_product_invest C
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y'
				and A.idx=B.product_idx $wh
				and A.idx=C.product_idx and C.member_idx='$member[mb_no]'
				order by A.state, A.title
				";
	$sql = "select A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
				   A.loan_end_date, A.loan_end_date_orig, A.loan_start_date, 
				   A.right_set_date, A.right_pic, A.deposit_pic, A.field_pic,
				   B.max_turn,
				   A.stream_url1, stream_url2
				from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B , cf_product_invest C
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y'
				and A.idx=B.product_idx $wh
				and A.idx=C.product_idx and C.member_idx='$member[mb_no]'
				order by A.state, A.title";
	*/
	$sql_tot = "select count(A.idx) AS cnt
				from cf_product A 
					LEFT JOIN (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B ON A.idx=B.product_idx, 
					cf_product_invest C
				where (A.state='1' or A.state='2' or A.state='5') 
					and A.right_display='Y' $wh
					and A.idx=C.product_idx and C.member_idx='$member[mb_no]'";
	$sql = "select A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
				   A.loan_end_date, A.loan_end_date_orig, A.loan_start_date, 
				   A.right_set_date, A.right_pic, A.deposit_pic, A.field_pic,
				   B.max_turn,
				   A.stream_url1, stream_url2,
				   substring_index(substring_index(A.title,'호',1),'제',-1)*1 ho
			from cf_product A 
				LEFT JOIN (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B ON A.idx=B.product_idx, 
				cf_product_invest C
			where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y' $wh
				and A.idx=C.product_idx and C.member_idx='$member[mb_no]'
				order by ho desc";
} else {

	/*
	$sql_tot = "select count(A.idx) AS cnt
				from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B 
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y'
				and A.idx=B.product_idx $wh
				order by A.state, A.title
				";	
	$sql = "select A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
				   A.loan_end_date, A.loan_end_date_orig, A.loan_start_date, 
				   A.right_set_date, A.right_pic, A.deposit_pic, A.field_pic,
				   B.max_turn,
				   A.stream_url1, stream_url2
				from cf_product A , (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B 
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y'
				and A.idx=B.product_idx $wh
				order by  A.open_datetime desc";
	*/
	$sql_tot = "select count(A.idx) AS cnt
				from cf_product A 
				LEFT JOIN (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B ON A.idx=B.product_idx
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y'
				$wh";
	$sql = "select A.idx, A.recruit_amount,A.invest_days, A.invest_period, A.title, A.invest_return ,A.state, A.invest_days, A.end_date,
				   A.loan_end_date, A.loan_end_date_orig, A.loan_start_date, 
				   A.right_set_date, A.right_pic, A.deposit_pic, A.field_pic,
				   B.max_turn,
				   A.stream_url1, stream_url2,
				   substring_index(substring_index(A.title,'호',1),'제',-1)*1 ho
				from cf_product A 
				LEFT JOIN (select product_idx,max(turn) max_turn from cf_product_success  group by product_idx) B ON A.idx=B.product_idx 
				where (A.state='1' or A.state='2' or A.state='5') 
				and A.right_display='Y' $wh
				order by  ho desc";
}

$row_tot = sql_fetch($sql_tot);
$total_count = $row_tot['cnt'];
$page_rows = 10;
$total_page  = ceil($total_count / $page_rows);  // 전체 페이지 계산
if ($page < 1) $page = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($page - 1) * $page_rows; // 시작 열을 구함
$num = $total_count - $from_record;

$result = sql_query($sql);
$list_count = sql_num_rows($result);
for($i=0; $i<$list_count; $i++) {
	$LIST[] = sql_fetch_array($result);
}
?>

<script>
function check_form() {
	var f = document.f_search;

	<? 
	if ($member["mb_id"]) {
		?>return true;<?
	} else {
		if (G5_IS_MOBILE) {
		} else {
			?>
			if (f.gubun[3].checked) {
				alert("로그인후에 이용해 주세요.");
				return false;
			}
			<?
		}
	}
	?>
}
</script>

<!-- 본문내용 START -->

<div id="content">
	<div class="location"><span><a href="<?=G5_URL?>/bbs/faq.php?fm_id=1">이용안내</a></span><b class="blue">채권관리 현황</b></div>

	<style>
	#stream_ready_notice { margin:auto auto; display:none;}
	<? if(G5_IS_MOBILE) { ?>
	#stream_ready_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	<? } else { ?>
	#stream_ready_notice .close { position:absolute; right:15px; top:12px; cursor:pointer; }
	<? } ?>
	</style>
	<div id="stream_ready_notice">
		<div class="title"><img src="/images/btn_close.png" alt="close" class="close"></div>
		<img id="stream_ready_image" src="" width="100%">
	</div>
	<script type="text/javascript">
	$('#stream_ready_image').click(function() {
		$.unblockUI();
		return false;
	});
	function openStreamReady(img_src) {
		$('#stream_ready_image').attr("src",img_src);
		$.blockUI({
			message: $('#stream_ready_notice'),
			<? if(G5_IS_MOBILE) { ?>
			css: { top:'20%', left:'1%', width:'98%', border:0, cursor:'default' }
			<? } else { ?>
			css: { top:'20%', width:'569px', margin:'auto auto', border:0, cursor:'default' }
			<? } ?>
		});
	}
	</script>

<? 
if(G5_IS_MOBILE) { ?>
  <div style="width:100%; padding:10px 2% 10px 2%; ">
		<div class="bond_cont">
			<p class="bond_tit">
			<span>헬로펀딩 채권관리 <strong>현황</strong></span><br/>
			업데이트 기준일 : <?=date("Y-m-d");?>
			</p>
			<form method="post" action="/event/bond_manager.php" onSubmit="return check_form();" name="f_search">
			<ul class="search_bar">
				<li>
				  <select name="gubun" onchange="reld();" >
					  <option value="">전체</option>
					  <option value="1"  <?=$_REQUEST['gubun']=="1"?"selected":""?>>이자상환중</option>
					  <option value="2"  <?=$_REQUEST['gubun']=="2"?"selected":""?>>상환완료</option>
					  <option value="my" <?=$_REQUEST['gubun']=="my"?"selected":""?>>나의 투자상품</option>
				  </select>
				</li>
				<li>
					<input type="text" name="search_title" class="text2" placeholder="상품명 검색" value="<?=$_REQUEST['search_title']?>" />
					<button type="submit" class="search_bar_btn_blue">검색</button>
				</li>
			</ul>
			
	<?
	for ($i=0; $i<$list_count; $i++) {
		$tmp = getNumberArr($LIST[$i]['recruit_amount']);
		if($LIST[$i]['invest_days'] > 0 && $LIST[$i]['invest_days'] < 30) {
			$invest_period = $LIST[$i]['invest_days'] . '일';
		}
		else {
			$invest_period = $LIST[$i]['invest_period'] . '개월';
		}
		
		$state = "";
		if ($LIST[$i]['state']=="1") $state = "이자상환중";
		else if ($LIST[$i]['state']=="2") $state = "상환완료";
		else if ($LIST[$i]['state']=="5") $state = "중도상환";
		
		$total_eja_time = $LIST[$i]['invest_period'];
		if (substr($LIST[$i]['loan_start_date'],-2)>5) $total_eja_time = $total_eja_time + 1;
		
		// 실시간 카메라 스트림
		$live_link = "";
		if($LIST[$i]['stream_url1']) {
			if($LIST[$i]['stream_url1']=='ready') {
				$live_link = "openStreamReady();";  // /popup/inc_stream_ready.php 에 함수 정의
			}
			else {
				$play_url = "http://hellolivetv.co.kr/onair/".$LIST[$i]['idx'];
				$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
				if(G5_IS_MOBILE) {
					$live_link = "window.open('".$play_url."','stream_win','toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
				}
				else {
					$live_link = "window.open('".$play_url."','stream_win','width=730,height=500,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
				}
			}
		}
		
		?>
	
			<table class="bond_table">
				<thead>
					<tr>
						<td colspan="2"><?=$LIST[$i]['title']?></td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>상품정보</td><td><?=$tmp[0]?><?=$tmp[1]?>원 / <?=number_format($LIST[$i]['invest_return'])?>% / <?=$invest_period?></td>
					</tr>
					<tr>
						<td>상품형태</td><td><?=$state?></td>
					</tr>
					<tr>
						<td>대출실행일</td><td><?=$LIST[$i]['loan_start_date']?></td>
					</tr>
						<td>원금 상환일</td><td><?=$LIST[$i]['loan_end_date']?$LIST[$i]['loan_end_date']:$LIST[$i]['loan_end_date_orig']?></td>
					<tr>
						<td>이자지급 회차</td><td><?=number_format($LIST[$i]['max_turn'])?> / <?=$total_eja_time?></td>
					</tr>
					
					<tr>
					    <td>권리설정일</td><td><?=$LIST[$i]['right_set_date']?></td>
					</tr>
					<tr>
						<td>권리설정 증빙자료</td><td><span class="bond_btn04">
							<a href="/data/product/<?=$LIST[$i]['right_pic']?>" target=_blank>
							증빙자료 보기</a></span></td>
					</tr>
					<tr>
						<td>헬로 live</td>
						<td>
							<?
							if ($live_link) {
								?>
								<span class="bond_btn03"><a onclick="<?=$live_link?>" style="cursor:pointer;">LIVE TV</a></span>
								<?
							} 
							?>
						</td>
					</tr>
					<tr>
						<td><!--현장사진-->업데이트 증빙자료</td>
						<td>
						<?
						if ($LIST[$i]['field_pic']) {
							?>
							<a href="/data/product/<?=$LIST[$i]['field_pic']?>" target=_blank><span class="bond_btn01">보기</span></a>
							<?
						}
						?>
						</td>
					</tr>
					<!--tr>
						<td>입금확인증</td>
						<td>
							<a href="/data/product/<?=$LIST[$i]['deposit_pic']?>" target=_blank><span class="bond_btn02">보기</span></a>
						</td>
					</tr-->
				</tbody>
			</table>
		<?
	}
	?>
		</div>
	</div>	
	<? 
} else { 
	?>

  <div style="width:100%; ">
		<div class="bond_cont">
			<p class="bond_tit">
			<span>헬로펀딩 채권관리 <strong>현황</strong></span><br/>
			업데이트 기준일 : <?=date("Y-m-d");?>
			</p>
			<form method="get" action="/event/bond_manager.php" onSubmit="return check_form();" name="f_search">
			<ul class="search_bar">
				<li>
				  <input type="radio" name="gubun" value=""   <?=!$_REQUEST['gubun']?"checked":""?>      onchange="reld();"  /> 전체 &nbsp;&nbsp;
				  <input type="radio" name="gubun" value="1"  <?=$_REQUEST['gubun']=="1"?"checked":""?>  onchange="reld();"  /> 이자상환중 &nbsp;&nbsp;
				  <input type="radio" name="gubun" value="2"  <?=$_REQUEST['gubun']=="2"?"checked":""?>  onchange="reld();"  /> 상환완료 &nbsp;&nbsp;
				  <input type="radio" name="gubun" value="my" <?=$_REQUEST['gubun']=="my"?"checked":""?> onchange="reld();"  /> 나의 투자상품
				</li>
				<li>
					<input type="text" name="search_title" placeholder="상품명 검색" value="<?=$_REQUEST['search_title']?>" />&nbsp;
					<button type="submit" class="btn_blue">검색</button>
				</li>
			</ul>
			</form>
		   <table class="bond_table">
				<thead>
					<tr>
						<!--td width="4%">번호</td-->
						<td>상품명</td>
						<td>상품정보</td>
						<td>상품형태</td>
						<td>대출실행일</td>
						<td>원금 상환일</td>
						<td>이자지급 회차</td>
						<td>권리설정일</td>
						<td>권리설정 증빙자료</td>
						<td>헬로 live</td>
						<td>업데이트 증빙자료</td>
						<!--td>입금확인증</td>-->
				</tr>
				</thead>
				<tbody>
	<?
	for ($i=0; $i<$list_count; $i++) {
		$tmp = getNumberArr($LIST[$i]['recruit_amount']);
		if($LIST[$i]['invest_days'] > 0 && $LIST[$i]['invest_days'] < 30) {
			$invest_period = $LIST[$i]['invest_days'] . '일';
		}
		else {
			$invest_period = $LIST[$i]['invest_period'] . '개월';
		}
		
		$state = "";
		if ($LIST[$i]['state']=="1") $state = "이자상환중";
		else if ($LIST[$i]['state']=="2") $state = "상환완료";
		else if ($LIST[$i]['state']=="5") $state = "중도상환";

		
		$total_eja_time = $LIST[$i]['invest_period'];
		if (substr($LIST[$i]['loan_start_date'],-2)>5) $total_eja_time = $total_eja_time + 1;
		
		// 실시간 카메라 스트림
		$live_link = "";
		if($LIST[$i]['stream_url1']) {
			if($LIST[$i]['stream_url1']=='ready') {

				$popup_image_url = "";
				if( in_array($LIST[$i]['idx'], array('153','154','156')) )  {									// 부산 정관신도시 일신메디컬센터 유동화자금
					$popup_image_url = "/popup/images/live_finished_20171212.png";
				}
				else if( in_array($LIST[$i]['idx'], array('175','176','177','206')) )  {			// 일산 대화동 다세대 주택 건축자금
					$popup_image_url = "/popup/images/live_finished_20180627.jpg";
				}
				else if( in_array($LIST[$i]['idx'], array('149','151','157','168','172','178')) ) {		// 울산 우정동
					$popup_image_url = "/popup/images/live_finished_20180326.jpg";
				}
				else if( in_array($LIST[$i]['idx'], array('205','207')) ) {		// 대구 이시아폴리스 메가맥스타워 유동화자금
					$popup_image_url = "/live_images/install0816.jpg";
				}
				else {
					$popup_image_url = "/popup/images/stream_ready_notice.jpg";
				}

				$live_link = "openStreamReady('".$popup_image_url."');";  // /popup/inc_stream_ready.php 에 함수 정의
			}
			else {
				$play_url = "http://hellolivetv.co.kr/onair.php?prd_idx=".$LIST[$i]['idx'];
				$play_url.= (preg_match("/dev.hellofunding/", $_SERVER['HTTP_HOST'])) ? "&mode=test" : "";
				if(G5_IS_MOBILE) {
					$live_link = "window.open('".$play_url."','stream_win','toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
				}
				else {
					$live_link = "window.open('".$play_url."','stream_win','width=730,height=500,toolbar=0,menubar=0,status=0,scrollbars=0,resizable=0');";
				}
			}
		}
		
		?>
				<tr>
					<!--td><?=$num?></td-->
					<td style="text-align:left;font-size:15px;width:255px;height:42px;padding:5px 0;line-height:22px;overflow:hidden;valign:middle;"><?=trim($LIST[$i]['title'])?></td>
					<td style="line-height:22px;width:130px;"><?=$tmp[0]?><?=$tmp[1]?>원 / <br/><?=number_format($LIST[$i]['invest_return'])?>% / <?=$invest_period?></td>
					<td><?=$state?></td>
					<td><?=$LIST[$i]['loan_start_date']?>
						<?
						//if ($LIST[$i]['state']=="2") echo "<br/>만기상환";
						//else if ($LIST[$i]['state']=="5") echo "<br/>중도상환";
						?>
					</td>
					<td style="line-height:22px;"><?//=preg_replace("/-/", ".", $LIST[$i]['loan_end_date_orig'])?><?=$LIST[$i]['loan_end_date']=="0000-00-00"?$LIST[$i]['loan_end_date_orig']:$LIST[$i]['loan_end_date']?>
					<? 
					if ($LIST[$i]['loan_end_date_orig']>date("Y-m-d") and ($LIST[$i]['loan_end_date']=="0000-00-00" or $LIST[$i]['loan_end_date']>date("Y-m-d")) ) {
						echo "<br/>(예정)";
					}
					?>
					</td>
					<td><?=number_format($LIST[$i]['max_turn'])?> / <?=$total_eja_time?></td>
					<td><?=$LIST[$i]['right_set_date']?></td>
					<td><span class="bond_btn04"><a href="/data/product/<?=$LIST[$i]['right_pic']?>" class="bond_btn04a" target="_blank">증빙자료 보기</a></span></td>
					<td>
					<?
					if ($live_link) {
						?>
						<span class="bond_btn03"><a onclick="<?=$live_link?>" class="bond_btn03a" style="cursor:pointer;">LIVE TV</a></span>
						<?
					}
					?>
					</td>
					<!--<td><span class="bond_btn02"><a href="/data/product/<?=$LIST[$i]['deposit_pic']?>" target=_blank>보기</a></span></td-->
					<td>
					<?
					if ($LIST[$i]['field_pic']) {
						?>
						<span class="bond_btn01"><a href="/data/product/<?=$LIST[$i]['field_pic']?>" class="bond_btn01a" target="_blank">증빙자료 보기</a></span>
						<?
					}
					?>
					</td>
				</tr>
		<?
		$num--;
	}
	?>
				</tbody>
			</table>
			
		</div>
		<!--div id="paging_span" style="width:100%; margin:10px 0 0 0; text-align:center;"><? paging($total_count, $page, $page_rows, 10); ?></div-->
		
	</div>
	<? 
} 
?>

</div>

<script type="text/javascript">
$(document).on('click', '#paging_span span.btn_paging', function() {
		var url = '<?=$_SERVER['PHP_SELF']?>'
		        + '?<?=$qstr?>&page=' + $(this).attr('data-page');
		$(location).attr('href', url);
});

function reld() {
	var f = document.f_search;
	<?
	if (!$member["mb_id"]) {
		if (G5_IS_MOBILE) {
			?>
			if (f.gubun.value=="my") {
				alert("로그인 후에 이용가능합니다.");
				if ("<?=$_REQUEST[gubun]?>"=="") f.gubun.options[0].selected=true;
				if ("<?=$_REQUEST[gubun]?>"=="1") f.gubun.options[1].selected=true;
				if ("<?=$_REQUEST[gubun]?>"=="2") f.gubun.options[2].selected=true;
				return;
			}
			<?
		} else {
			?>
			if (f.gubun[3].checked) {
				alert("로그인 후에 이용가능합니다.");
				if ("<?=$_REQUEST[gubun]?>"=="") f.gubun[0].checked=true;
				if ("<?=$_REQUEST[gubun]?>"=="1") f.gubun[1].checked=true;
				if ("<?=$_REQUEST[gubun]?>"=="2") f.gubun[2].checked=true;
				return;
			}
			<?
		}
	}
	?>
	f.submit();
}
</script>

<!-- 본문내용 E N D -->
<?php

if ($co['co_include_tail'])
    @include_once($co['co_include_tail']);
else
    include_once('./_tail.php');
?>