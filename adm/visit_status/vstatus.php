<?

$sub_menu = "200500";
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');

if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

$g5['title'] = '전환 통계';
include_once(G5_ADMIN_PATH . '/admin.head.php');

?>

<script type="text/javascript" src="/adm/js/jquery.form.js"></script>

<style>
table {border-collapse:collapse}
.content .tabX { height:42px; background:url('/images/tab_bg.gif') repeat-x left bottom; }
.content .tabX li { float:left; width:200px; margin-right:3px; line-height:40px; text-align:center; font-size:16px; color:#aaa; background-color:#f7f7f7; border:1px solid #e5e5e5; border-bottom:0; cursor:pointer; }
.content .tabX li.on { border:1px solid #ccc; background-color:#fff; border-bottom-color:#fff; color:#000 }
.content .tabX li:last-child { margin:0; display:inline-block; }
.content .tabXarea { display:block;margin:0; padding:20px; min-height:400px;border-left:1px solid #ccc; border-right:1px solid #ccc; border-bottom:1px solid #ccc; }
</style>

<script type="text/javascript" src="/js/jqbar/jqbar.js"></script>
<link rel="stylesheet" type="text/css" href="/js/jqbar/jqbar.css">

<div class="tbl_head02 tbl_wrap">
	<div class="content" style="margin:30px auto">
		<ul class="tabX" style="width:100%;list-style:none;padding-left:20px;;margin:0;">
			<li data-url="ajax.visit_status.php" class="on">가입전환통계</li>
			<li data-url="ajax.ad_join_status.php">광고전환통계</li>
			<li data-url="ajax.keyword_status.php">키워드통계</li>
		</ul>
		<div class="tabXarea"></div>
	</div>
</div>

<script>
$('.tabX li').click(function() {
	var cur = $(this).index();
	var url = $(this).data('url');

	//$('.tabXarea').empty();

	$.ajax({
		url : url,
		type: 'POST',
		data: {sdate:'<?=$sdate?>', edate:'<?=$edate?>'},
		success:function(data, textStatus, jqXHR) {
			if(data=="ERROR-LOGIN") {
				window.location.replace('/bbs/login.php');
			}
			else {
				$('.tabXarea').html(data);
			}
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	});

	$(this).addClass('on').siblings().removeClass('on');
});

$(document).ready(function() {
	$.ajax({
		url : "./ajax.visit_status.php",
		type: 'POST',
		data: {sdate:'<?=$sdate?>', edate:'<?=$edate?>'},
		success:function(data, textStatus, jqXHR){
			//console.log(jqXHR);
			if(data=='ERROR-SEARCHDATE') {
				alert('검색일자 조건을 다시 설정하십시요!'); return false;
			}
			else {
				if(data=="ERROR-LOGIN") {
					window.location.replace('/bbs/login.php');
				}
				else {
					$('.tabXarea').html(data);
				}
			}
		},
		beforeSend: function() { loading('on'); },
		complete: function() { loading('off'); },
		error: function () { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
	})
});
</script>

<?

include_once (G5_ADMIN_PATH . '/admin.tail.php');

?>