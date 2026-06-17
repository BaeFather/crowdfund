<?
header("Charset=UTF-8");

include_once('./_common.php');
include_once(G5_EDITOR_LIB);

if(!$PRDT) {
	$PRDT = sql_fetch("
		SELECT
			A.*,
			B.*
		FROM
			cf_product A
		LEFT JOIN
			cf_product_container B  ON A.idx=B.product_idx
		WHERE
			idx='".$_REQUEST['idx']."'");
}

?>

<table>
	<colgroup>
		<col width="12%">
		<col width="88%">
	</colgroup>

	<tr>
		<th title="extend_8">업데이트현황</th>
		<td><?=editor_html('extend_8', get_text($PRDT['extend_8'], 0))?></td>
	</tr>

	<tr>
		<th title="extend_9">증빙자료<br>(2017.06.15 기준)</th>
		<td><?=editor_html('extend_9', get_text($PRDT['extend_9'], 0))?></td>
	</tr>

<? if($prd_idx < '142') { ?>
	<tr>
		<th title="extend_6">채권매입보증</th>
		<td><?=editor_html('extend_6', get_text($PRDT['extend_6'], 0))?></td>
	</tr>
<? } ?>

	<tr>
		<th title="invest_summary">투자설명서<br>(구.투자요약)</th>
		<td><?=editor_html('invest_summary', get_text($PRDT['invest_summary'], 0))?></td>
	</tr>

	<tr>
		<th title="invest_summary_m">투자설명서<br>(모바일)</th>
		<td><?=editor_html('invest_summary_m', get_text($PRDT['invest_summary_m'], 0))?></td>
	</tr>

<? if($prd_idx < '142') { ?>
	<tr>
		<th id="chgTitle1" style="color:brown;" title="core_invest_point">핵심 투자포인트</th>
		<td><?=editor_html('core_invest_point', get_text($PRDT['core_invest_point'], 0))?></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th id="chgTitle2" style="color:brown;" title="extend_4">투자자 보호장치</th>
		<td><?=editor_html('extend_4', get_text($PRDT['extend_4'], 0))?></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th id="chgTitle3" style="color:brown;" title="extend_1">담보분석 및 평가</th>
		<td><?=editor_html('extend_1', get_text($PRDT['extend_1'], 0))?></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th id="chgTitle4" title="extend_2">신용 및 부채정보</th>
		<td><?=editor_html('extend_2', get_text($PRDT['extend_2'], 0))?></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th>투자 구조도</th>
		<td>
			※ 문서링크 버튼태그 : <font color='brown'>&lt;a href="http://문서URL" class="btn_blue_document"&gt;&lt;img src="/images/flaticon/text-document.png"&gt;&nbsp;문서제목 보기&lt;/a&gt;</font><br>
			&nbsp;&nbsp;&nbsp; <font color='red'>주의) 반드시 위지윅 에디터의 입력상태를 HTML로 선택한 후 붙여넣기 하세요.</font>
			<?=editor_html('extend_3', get_text($PRDT['extend_3'], 0)); ?>
		</td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th>평가기관 의견</th>
		<td><div id="chgStyle1"><?=editor_html('extend_5', get_text($PRDT['extend_5'], 0))?></div></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th>심사총평</th>
		<td><div id="chgStyle2"><?=editor_html('screening', get_text($PRDT['screening'], 0))?></div></td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th>심사자</th>
		<td><ul class="list-inline" style="margin:0;padding:0;">
				<li><select name="judge" class="form-control" style="width:120px;">
						<option value=''>:: 선택 ::</option>
<?
$JUDGE_ARR = array_keys($JUDGE);
for($i=0; $i<count($JUDGE); $i++) {
	$selected = ($PRDT['judge']==$JUDGE_ARR[$i]) ? 'selected' : '';
	echo "<option value='".$JUDGE_ARR[$i]."' $selected>".$JUDGE[$JUDGE_ARR[$i]]."</option>\n";
}
?>
					</select></li>
				<li>선택된 심사자의 프로필 배너가 투자상품 상세보기 페이지의 '투자 요약 상단'에 위치합니다.</li>
			</ul>
		</td>
	</tr>
<? } ?>

<? if($prd_idx < '142') { ?>
	<tr>
		<th>투자관련 도움말<br>(구 FAQ)</th>
		<td><?=editor_html('extend_7', get_text($PRDT['extend_7'], 0))?></td>
	</tr>
<? } ?>
</table>
