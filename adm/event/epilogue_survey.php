<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

include_once('./_common.php');

$sub_menu = "900300";
auth_check($auth[$sub_menu], 'w');

while( list($k, $v)=each($_REQUEST) ) { if(!is_array(${$k})) ${$k} = trim($v); }

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


###################################
## 이벤트 개요 설정
###################################
$_CONF['event_no']        = "20161221";
$_CONF['event_title']     = "응답하라 투자후기(1회)";
$_CONF['event_sub_title'] = "1회 이상 투자 수익금을 지급 받은 회원";
$_CONF['event_sdate']     = "2016-12-21";
$_CONF['event_edate']     = "2016-12-25";
$_CONF['event_gift']      = "영화티켓 2매";
$_CONF['event_special_gift'] = "빕스 5만원권 상품권";

$QUESTION = array(
	"1. 헬로펀딩은 어떻게 알게 되셨나요?",
	"2. 투자포인트는 무엇인가요?",
	"3. 현재 헬로펀딩에서 안전투자를 위한 투자자보호제도 (1. 사내투자심의위원회, 2. 법무법인, 감정평가법인 등 외부전문가의 권리분석, 3. 채권매입계약)가 있다는 것을 알고계시나요?",
	"4. 헬로펀딩에서 지급받은 수익금은 어떻게 활용하고 있나요?",
	"5. 헬로펀딩과 타 업체와의 차이점은 무엇이라고 생각하시나요?",
	"6. 헬로펀딩에 하고 싶은 말"
);

$sql = "
	SELECT
		A.*,
		B.mb_id, B.mb_name, B.mb_hp
	FROM
		invest_users_epilogue A,
		g5_member B
	WHERE
		B.mb_no=A.member_idx
	ORDER BY
		A.idx DESC";
$res = sql_query($sql);
$rows = $res->num_rows;

for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);
	$LIST[$i]['mb_hp'] = masterDecrypt($LIST[$i]['mb_hp'], false);
}


$g5['title'] = $menu["menu900"][5][1];
$g5['title'].= " > " . $_CONF['event_title'];

include_once(G5_ADMIN_PATH . '/admin.head.php');
?>

<div class="row" style="width:100%;">
	<div class="col-lg-12">
		<div class="panel-body">
			<div style="margin:4px 0 4px 0; padding:4px 20px 4px 20px; border:1px solid #ddd; border-radius:15px; background-color:#ffebcc;">
			이벤트명 : <?=$_CONF['event_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			기간 : <?=preg_replace("/-/", ".", $_CONF['event_sdate'])?> ~ <?=preg_replace("/-/", ".", $_CONF['event_edate'])?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			응모자격 : <?=$_CONF['event_sub_title']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급상품(일반) : <?=$_CONF['event_gift']?> <span style='color:#AAA;text-align:center;padding:0 20px 0 20px;'>|</span>
			지급상품(우수) : <?=$_CONF['event_special_gift']?>
			</div>
			<div class="dataTable_wrapper">
<?
$list_num = count($LIST);
for($i=0,$j=1; $i<count($LIST);$i++) {
	$gift_point = $LIST[$i]['recommend_count'] * $_CONF['event_gift_point'];

	$event_title = addSlashes($_CONF['point_title'] . ":::" . $LIST[$i]['recommend_count']);  //구분자 주의 (액션파일에서 구분자로 사용)

?>
				<table class="table table-striped table-bordered table-hover">
					<tbody>
						<tr align="center">
							<td><?=$LIST[$i]['idx']?></td>
							<td>아이디</td>
							<td><?=$LIST[$i]['mb_id']?></td>
							<td>성명</td>
							<td><?=$LIST[$i]['mb_name']?></td>
							<td>연락처</td>
							<td><?=$LIST[$i]['mb_hp']?></td>
							<td>등록일시</td>
							<td><?=substr($LIST[$i]['rdate'], 0, 16)?></td>
						</tr>
						<tr>
							<td colspan="10">
								<table>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[0]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=nl2br(stripSlashes($LIST[$i]['text1']))?></xmp></pre></td>
									</tr>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[1]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=stripSlashes($LIST[$i]['text2'])?></xmp></pre></td>
									</tr>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[2]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=stripSlashes($LIST[$i]['text3'])?></xmp></pre></td>
									</tr>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[3]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=stripSlashes($LIST[$i]['text4'])?></xmp></pre></td>
									</tr>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[4]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=stripSlashes($LIST[$i]['text5'])?></xmp></pre></td>
									</tr>
									<tr>
										<td style="border:0;text-align:left;color:#284893"><?=$QUESTION[5]?></td>
									</tr>
									<tr>
										<td style="border:0;"><pre style="font-size:9pt;background-color:#FDFECB;"><xmp><?=stripSlashes($LIST[$i]['text6'])?></xmp></pre></td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
<?
	$list_num--;
}
?>
			</div>
		</div>
		<!-- /.panel-body -->
		<div style="width:100%; text-align: center;">
			<ul class="pagination">
				<!--
				<?=get_paging(G5_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $page, $total_page, '?'.$qstr.'&amp;page='); ?>
				-->
			</ul>
		</div><!-- /.panel-body -->
		</form>
	</div><!-- /.col-lg-12 -->
</div><!-- /.row -->

<script>
$(function() {
	$(".datepicker").datepicker({
		dateFormat: 'yy-mm-dd'
	});

	$("input[name=chkall]").click(function() {
		$("input[name='chk[]']").prop('checked', this.checked);
	});
});
</script>

<?
include_once (G5_ADMIN_PATH . '/admin.tail.php');
?>