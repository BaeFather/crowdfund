<?
###############################################################################
##   - 2019-01-21 업데이트 : 주민번호, 전화번호, 계좌번호 암,복호화 추가
###############################################################################

$sub_menu = "500200";
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "출금신청 정보";


$g5['title'] = $html_title.' 상세';

include_once (G5_ADMIN_PATH.'/admin.head.php');

$sql = "
		SELECT
			A.*,
			B.mb_email, B.mb_name, B.mb_co_name, B.mb_hp, B.bank_name, B.bank_private_name, B.account_num, B.member_type
		FROM
			g5_withdrawal A
		LEFT JOIN
			g5_member B
		ON
			A.mb_id = B.mb_id
		WHERE
			A.idx = {$_GET['idx']}";
$mb = sql_fetch($sql);
$mb['mb_hp'] = masterDecrypt($mb['mb_hp'], false);
$mb['account_num'] = masterDecrypt($mb['account_num'], false);

$WSTATE = array(
	'1' => '출금 신청',
	'2' => '출금 완료',
	'3' => '출금 신청 취소',
);
$WSTATE_KEYS = array_keys($WSTATE);


if (!$mb['idx']) alert('존재하지 않는 자료입니다.');

?>

<div class="tbl_frm01 tbl_wrap">
	<table>
		<caption><?=$g5['title']?></caption>
		<colgroup>
			<col class="grid_4">
			<col>
		</colgroup>

		<form name="fmember" id="fmember">
			<input type="hidden" name="idx" value="<?=$_GET['idx']?>">
			<input type="hidden" name="qstr" value="state=<?=$_REQUEST['state']?>&sdate=<?=$_REQUEST['sdate']?>&edate=<?=$_REQUEST['sdate']?>&key_search=<?=$_REQUEST['key_search']?>&keyword=<?=$_REQUEST['keyword']?>&page=<?=$_REQUEST['page']?>">
		<tbody>
			<tr>
				<th scope="row"><label for="mb_name">이름</label></th>
				<td><?=$mb['mb_name']?></td>
			</tr>
			<tr>
				<th scope="row"><label for="mb_id">아이디</label></th>
				<td><?=$mb['mb_id']?></td>
			</tr>

			<tr>
				<th scope="row"><label for="mb_hp">핸드폰 번호</label></th>
				<td><?=($_SESSION['ss_accounting_admin']) ? $mb['mb_hp'] : '<font style="color:#ccc">열람불가</font>'; ?></td>
			</tr>
			<tr>
				<th scope="row"><b>계좌정보</b></th>
				<td>
					<?=$mb['bank_name']?>
					<?=($_SESSION['ss_accounting_admin']) ? $mb['account_num'] : '<font style="color:#ccc">열람불가</font>'; ?>
					<?=$mb['bank_private_name']?>
				</td>
			</tr>
			<tr>
				<th scope="row"><b>요청금액</b></th>
				<td><?=number_format($mb['req_price'])?>원</td>
			</tr>
			<tr>
				<th scope="row"><b>신청일</b></th>
				<td><?=str_replace('-','.', $mb['regdate'])?></td>
			</tr>
			<tr>
				<th scope="row"><b>상태</b></th>
				<td>
					<?=$WSTATE[$mb['state']]?>
					<? if($mb['state']==1) { ?>
					&nbsp; <select name="state">
						<!--option value="1">출금 신청</option-->
						<option value="">::변경선택::</option>
						<option value="2">출금 처리 완료</option>
						<option value="3">출금 신청 취소</option>
					</select>
					<? } ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><b>관리자 메모</b></th>
				<td>
					<textarea name="admin_memo" rows="10" style="width:90%;" placeholder="관리자 메모 입력란"><?=$mb['admin_memo'];?></textarea>
				</td>
			</tr>
		</tbody>
		</form>
	</table>

	<div class="text-center" style="margin-top:25px;">
		<input type="button" id="fmember_submit" class="btn btn-md btn-success" value="수정">
		<input type="button" class="btn btn-md btn-default" value="원래대로" onclick="fmember_reset();">
		<a href="/adm/withdrawal_list.php?<?=preg_replace("/&idx=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']);?>" class="btn btn-md btn-primary">목록</a>
	</div>
</div>

<script>
function fmember_reset() {
	$("form")[0].reset();
}

$('#fmember_submit').click(function() {
	if( confirm('수정 하시겠습니까?') ) {
		$('#fmember_submit').attr('disabled', true);

		var f = document.fmember;
		f.method = 'post';
		f.action = '/adm/withdrawal_form_update.php';
		f.submit();
	}
});
</script>


<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>