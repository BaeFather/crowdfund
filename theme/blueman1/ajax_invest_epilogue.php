<?
include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

$sql = "
	SELECT
		A.subject, A.text1, A.text2, A.text3, A.text4, A.text5, A.text6, A.rdate, A.status,
		B.mb_no, B.mb_id, B.mb_name
	FROM
		invest_users_epilogue A,
		g5_member B
	WHERE
		A.idx='$idx'
		AND B.mb_no=A.member_idx";
$DATA = sql_fetch($sql);
//print_rr($DATA, 'font-size:8pt');

$user_id = substr($DATA['mb_id'], 0, 2);
$loop = strlen($DATA['mb_id'])-2;
for($i=0; $i<$loop; $i++) {
	$user_id.="*";
}

$QUESTION = array(
	"1. 헬로펀딩은 어떻게 알게 되셨나요?",
	"2. 투자포인트는 무엇인가요?",
	"3. 현재 헬로펀딩에서 안전투자를 위한 투자자보호제도 (1. 사내투자심의위원회, 2. 법무법인, 감정평가법인 등 외부전문가의 권리분석, 3. 채권매입계약)가 있다는 것을 알고계시나요?",
	"4. 헬로펀딩에서 지급받은 수익금은 어떻게 활용하고 있나요?",
	"5. 헬로펀딩과 타 업체와의 차이점은 무엇이라고 생각하시나요?",
	"6. 헬로펀딩에 하고 싶은 말"
);

?>

		<div class="title"><?=$DATA['subject']?></div>
		<div class="writer">
		  id. <?=$user_id?> &nbsp;&nbsp;&nbsp;&nbsp;
			name. <?=cut_hangul_last(substr($DATA['mb_name'], 0, 3))?>**
			<!--date. <?=substr($DATA['rdate'], 0, 10)?>-->
		</div>
		<div class="type01">
			<table style="width:100%;" border=0>
				<tbody>
<? for($i=0,$j=1; $i<6; $i++,$j++) { ?>
					<tr>
						<td style="padding-top:10px;">
							<span class="question"><?=$QUESTION[$i]?></span>
							<div class="answer"><?=nl2br(htmlSpecialChars(stripSlashes($DATA['text'.$j])));?></div>
						</td>
					</tr>
<? } ?>
				</tbody>
			</table>
		</div>