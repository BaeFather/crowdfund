<?

include_once('./_common.php');

$JUDGE_STATE = array(
	'1'=>'대기중',
	'2'=>'진행중',
	'3'=>'부결',
	'4'=>'승인');

$DATA = sql_fetch("SELECT * FROM cf_apat_loan_request WHERE idx='".$idx."'");

$print_gubun = ($DATA['type']=='2') ? '취급법인 유동화 신청' : '아파트담보 대출 신청';

$print_type    = $TYPE[$DATA['type']];
$print_hp      = masterDecrypt($DATA['hp'], false);

$print_already_dept = ($DATA['already_dept']) ? price_cutting($DATA['already_dept']).'원' : '';
$print_tadwo   = ($DATA['tadwo']) ? price_cutting($DATA['tadwo']).'원' : '';
$print_wamt    = ($DATA['wamt']) ? price_cutting($DATA['wamt']).'원' : '';
$print_purpose = $PURPOSE[$DATA['purpose']];
$print_period  = $PERIOD[$DATA['period']];
$print_income  = ($DATA['income']) ? price_cutting($DATA['income']).'원' : '';
$print_tenant  = ($DATA['tenant']) ? '있음' : '';
$print_content = ($DATA['content']) ? nl2br(htmlSpecialChars($DATA['content'])) : '';

$print_conn_info = "";
$print_conn_info.= ($DATA['ip']) ? $DATA['ip']."/" : "";
$print_conn_info.= ($DATA['area']) ? $DATA['area']."/" : "";
$print_conn_info.= ($DATA['device']) ? $DATA['device']."/" : "";
$print_conn_info = substr($print_conn_info, 0, strlen($print_conn_info)-1);

if($mode=='download') {

	$print_content = ($DATA['content']) ? $DATA['content'] : '';

	include_once(G5_LIB_PATH."/PHPExcel_1.8.0/Classes/PHPExcel.php");

	$objPHPExcel = new PHPExcel();

	// Excel 문서 속성을 지정해주는 부분이다. 적당히 수정하면 된다.
	$objPHPExcel->getProperties()->setCreator("헬로펀딩 정산시스템")
															 ->setLastModifiedBy("헬로펀딩 정산시스템")
															 ->setTitle($print_gubun)
															 ->setSubject($print_gubun)
															 ->setDescription($print_gubun)
															 ->setKeywords($print_gubun)
															 ->setCategory("(주)헬로핀테크");


	// 제목줄 ------------------------------------------------------------------------
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("A1:D1")->setCellValue("A1", $print_gubun);
	$objPHPExcel->getActiveSheet()->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);		//가운데 정렬
	// 제목줄 ------------------------------------------------------------------------

	//셀너비
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(18.13);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(43.13);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(18.13);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(43.13);

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B2:D2")
							->setCellValue("A2", "등록번호")
							->setCellValue("B2", $DATA['idx']);
	$objPHPExcel->getActiveSheet()->getStyle("A2")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A2:B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A3", "업체명")
							->setCellValue("B3", $DATA['co_name'])
							->setCellValue("C3", "성명.담당자명")
							->setCellValue("D3", $DATA['name']);
	$objPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");	//배경색 변경
	$objPHPExcel->getActiveSheet()->getStyle("C3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A3:D3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A4", "연락처")
							->setCellValueExplicit("B4", $print_hp, PHPExcel_Cell_DataType::TYPE_STRING)
							->setCellValue("C4", "E-Mail")
							->setCellValue("D4", $DATA['email']);
	$objPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");	//배경색 변경
	$objPHPExcel->getActiveSheet()->getStyle("C4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A4:D4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B5:D5")
							->setCellValue("A5", "담보물 주소")
							->setCellValue("B5", $DATA['loc']);
	$objPHPExcel->getActiveSheet()->getStyle("A5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A5:B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A6", "소유주와의 관계")
							->setCellValue("B6", $RELATION[$DATA['relation']])
							->setCellValue("C6", "대출금액")
							->setCellValue("D6", $print_wamt);
	$objPHPExcel->getActiveSheet()->getStyle("A6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A6:D6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A7", "대출목적")
							->setCellValue("B7", $print_purpose)
							->setCellValue("C7", "희망대출기간")
							->setCellValue("D7", $print_period);
	$objPHPExcel->getActiveSheet()->getStyle("A7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A7:D7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A8", "기대출금액")
							->setCellValue("B8", $print_already_dept)
							->setCellValue("C8", "채권최고액")
							->setCellValue("D8", $print_tadwo);
	$objPHPExcel->getActiveSheet()->getStyle("A8")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C8")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A8:D8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A9", "연소득")
							->setCellValue("B9", $print_income)
							->setCellValue("C9", "상담가능시간")
							->setCellValue("D9", $DATA['wtime']);
	$objPHPExcel->getActiveSheet()->getStyle("A9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A9:D9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A10", "세입자 유무")
							->setCellValue("B10", $print_tenant)
							->setCellValue("C10", "등록일시")
							->setCellValue("D10", substr($DATA['regdate'],0,16));
	$objPHPExcel->getActiveSheet()->getStyle("A10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A10:D10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B11:D11")
							->setCellValue("A11", "내용")
							->setCellValue("B11", $print_content);
	$objPHPExcel->getActiveSheet()->getStyle("A11")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle("A11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
	$objPHPExcel->getActiveSheet()->getStyle("B11")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

	$objPHPExcel->setActiveSheetIndex(0)
							->setCellValue("A12", "심사진행현황")
							->setCellValue("B12", $JUDGE_STATE[$DATA['judge_state']])
							->setCellValue("C12", "현황설정일")
							->setCellValue("D12", substr($DATA['last_editdate'],0,16));
	$objPHPExcel->getActiveSheet()->getStyle("A12")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("C12")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
	$objPHPExcel->getActiveSheet()->getStyle("A12:D12")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


	$objPHPExcel->getActiveSheet()->getStyle("A2:D12")->getFont()->setName("맑은 고딕")->setSize(11);
	$objPHPExcel->getActiveSheet()->getStyle("A2:D12")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

	$objPHPExcel->getActiveSheet()->getStyle("B11")->getAlignment()->setWrapText(true);
	$objPHPExcel->getActiveSheet()->getRowDimension(11)->setRowHeight(270);


	// 제목 글자스타일
	$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setName("맑은 고딕");
	$objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(16);

	$file_subject = preg_replace('/( )/', '', trim($print_gubun)) . "(신청번호-".$DATA['idx'].")";
	$filename = iconv("UTF-8", "EUC-KR", $file_subject);			// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.

	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');

	exit;

}

?>

	<div style="max-width:1000px;text-align:center;">
		<h3><?=$print_gubun?></h3>
	</div>
	<table class="table table-bordered" style="max-width:1000px;">
		<colgroup>
			<col width="15%">
			<col width="35%">
			<col width="15%">
			<col width="35%">
		</colgroup>
		<tr>
			<th scope="col">업체명</th>
			<td><?=htmlSpecialChars($DATA['co_name'])?></td>
			<th scope="col">성명.담당자명</th>
			<td><?=htmlSpecialChars($DATA['name'])?></td>
		</tr>
		<tr>
			<th scope="col">연락처</th>
			<td><?=$print_hp?></td>
			<th scope="col">E-mail</th>
			<td><?=htmlSpecialChars($DATA['email'])?></td>
		</tr>
		<tr>
			<th scope="col">담보물 주소</th>
			<td colspan="3"><?=htmlSpecialChars($DATA['loc'])?></td>
		</tr>
		<tr>
			<th scope="col">소유주와의 관계</th>
			<td><?=$RELATION[$DATA['relation']]?></td>
			<th scope="col">대출금액</th>
			<td><?=$print_wamt?></td>
		</tr>
		<tr>
			<th scope="col">대출목적</th>
			<td><?=$print_purpose?></td>
			<th scope="col">희망대출기간</th>
			<td><?=$print_period?></td>
		</tr>
		<tr>
			<th scope="col">기대출금액</th>
			<td><?=$print_already_dept?></td>
			<th scope="col">채권최고액</th>
			<td><?=$print_tadwo?></td>
		</tr>
		<tr>
			<th scope="col">연소득</th>
			<td><?=$print_income?></td>
			<th scope="col">상담가능시간</th>
			<td><?=$DATA['wtime']?></td>
		</tr>
		<tr>
			<th scope="col">세입자 유무</th>
			<td><?=$print_tenant?></td>
			<th scope="col">등록일시</th>
			<td><?=substr($DATA['regdate'],0,16)?></td>
		</tr>
		<tr>
			<th scope="col">내용</th>
			<td colspan="3" style="height:200px;" valign="top"><?=$print_content?></td>
		</tr>
		<tr>
			<th scope="col">등록자정보</th>
			<td><span style="color:#aaa"><?=$DATA['ip']?> / <?=$DATA['area']?> / <?=$DATA['device']?></span></td>
			<th scope="col">물건담당자</th>
			<td><select id="judge_name" class="input-sm" onChange="setJudgeName()">
					<option value="">:: 물건담당자 ::</option>
<?
$sql = "
	SELECT
		mb_name
	FROM
		g5_member
	WHERE (1)
		AND mb_level='9'
		AND mb_name LIKE '%상품관리-%'
	ORDER BY
		mb_name, mb_no ASC";
$res = sql_query($sql);
while($R = sql_fetch_array($res)) {
	$selected = ($R['mb_name'] == $DATA['judge_name']) ? 'selected' : '';
	echo "					<option value='".$R['mb_name']."' $selected>".$R['mb_name']."</option>\n";
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="col">심사현황</th>
			<td><select id="judge_state" class="input-sm" onChange="setJudgeState()">
					<option value="1" <?=($DATA['judge_state']=='1')?'selected':'';?>>대기중</option>
					<option value="2" <?=($DATA['judge_state']=='2')?'selected':'';?>>진행중</option>
					<option value="3" <?=($DATA['judge_state']=='3')?'selected':'';?>>부결</option>
					<option value="4" <?=($DATA['judge_state']=='4')?'selected':'';?>>승인</option>
				</select>
			</td>
			<th scope="col">최종수정일시</th>
			<td><?=substr($DATA['last_editdate'],0,16)?> / <?=$DATA['judge_name']?></td>
		</tr>
		<tr>
			<th scope="col">파트너코드</th>
			<td colspan="3"><?php ECHO $DATA["pid"];?></td>
		</tr>
	</table>

	<div style="max-width:1000px;text-align:right;">
		<button type="button" id="list_button" onClick="location.href='<?=$_SERVER['PHP_SELF']?><?=($_SERVER['QUERY_STRING'])? '?'.preg_replace("/&idx=([0-9]){1,10}/", "", $_SERVER['QUERY_STRING']):'';?>';" class="btn btn-default">목록보기</button>
		<button type="button" id="download_button" class="btn btn-success">엑셀시트 다운로드</button>
	</div>

	<!-- 코멘트 //-->
	<div style="margin-top:30px; max-width:1000px;">
		<h3>COMMENT & LOG</h3>
		<ul class="list-inline" style="margin-bottom:20px;">
			<li style="width:85%;height:80px"><textarea id="comment" style="width:100%;height:100%;" required></textarea></li>
			<li style="width:14.6%"><button type="button" id="frmCmtSubmit" class="btn btn-primary" style="width:100%;height:80px;">등 록</button></li>
		</ul>
		<script>
		$('#frmCmtSubmit').click(function() {
			if( $('#comment').val()=='' ) {
				alert('내용을 입력하십시요.');  $('#comment').focus();
			}
			else {
				$.ajax({
					url : "request.proc.ajax.php",
					type: "POST",
					dataType: "JSON",
					data:{
						mode: 'new',
						idx: '<?=$DATA['idx']?>',
						comment: $('#comment').val()
					},
					success:function(data) {
						if(data.result=='SUCCESS') { window.location.reload(); }
						else { console.log(result); }
					},
					error: function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
		});
		</script>

<?
$cres  = sql_query("SELECT idx, writer, comment, regdate FROM cf_apat_loan_request_judge_log WHERE req_idx='".$idx."' ORDER BY idx DESC");
$crows = $cres->num_rows;
if($crows) {
	for($c=0,$cno=1; $c<$crows; $c++,$cno++) {
		$CROW = sql_fetch_array($cres);
		if($CROW['writer']=='system') {
			$cwriter = $CROW['writer'];
		}
		else {
			$MEM  = sql_fetch("SELECT mb_name FROM g5_member WHERE mb_id='".$CROW['writer']."' AND mb_level IN(9, 10)");
			$cwriter = ($MEM['mb_name']) ?  $MEM['mb_name'] : $CROW['writer'];
		}

		$delete_tag = "";
		if($CROW['writer']==$member['mb_id'] || $member['mb_level']=='10') {
			$delete_tag = "<span onClick='dropComment(".$CROW['idx'].")' style='cursor:pointer;color:red'>×</span>";
		}

		$comm = nl2br(htmlSpecialChars($CROW['comment']));

?>
		<table style="font-size:12px">
			<colgroup>
				<col width="200">
				<col width="">
				<col width="30">
			</colgroup>
			<tr style='background:#FAFAFA'>
				<td align="left"><?=$cwriter?></td>
				<td align="right"><span style="color:#aaa"><?=$CROW['regdate']?></span></td>
				<td align="center"><?=$delete_tag?></td>
			</tr>
			<tr>
				<td colspan="3" style="padding:8px 20px"><?=$comm?></td>
			</tr>
		</table>
		<div style="height:10px;"></div>
<?
	}
}
?>
	</div>
	<!-- 코멘트 //-->

	<div style='width:100%;margin-top:50px;border-bottom:1px dashed #ccc'></div>

	<script>
	setJudgeName = function() {
		var judge_name = $('#judge_name').val();
		if(confirm('물건 담당자가 다음과 같이 변경됩니다. \n\n [ <?=$DATA['judge_name']?> -> '  + $('#judge_name option:selected').text()+ ']\n\n처리 하시겠습니까?')) {
			$.ajax({
				url : "request.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data:{
					mode: 'change_judge',
					idx: '<?=$DATA['idx']?>',
					judge_name: judge_name
				},
				success:function(data) {
					if(data.result=='SUCCESS') { alert(data.message); window.location.reload(); }
					else { alert(data.message); }
				},
				error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}

	setJudgeState = function() {
		var state = $('#judge_state').val();
		if(state != '<?=$DATA['judge_state']?>') {
			if(confirm('심사진행 상태가 다음과 같이 변경\n\n [ <?=$JUDGE_STATE[$DATA['judge_state']]?> -> '  + $('#judge_state option:selected').text() + ']\n\n처리 하시겠습니까?')) {
				$.ajax({
					url : "request.proc.ajax.php",
					type: "POST",
					dataType: "JSON",
					data:{
						mode: 'change_state',
						idx: '<?=$DATA['idx']?>',
						state: state
					},
					success:function(data) {
						if(data.result=='SUCCESS') { window.location.reload(); }
						else { alert(data.message); }
					},
					error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
				});
			}
		}
	}

	dropComment = function(commidx) {
		if(confirm('게시글을 삭제 하시겠습니까?')) {
			$.ajax({
				url : "request.proc.ajax.php",
				type: "POST",
				dataType: "JSON",
				data:{
					mode: 'delete',
					commidx: commidx
				},
				success:function(data) {
					if(data.result=='SUCCESS') { alert('삭제 되었습니다.'); window.location.reload(); }
					else { alert(data.message); }
				},
				error:function (e) { alert("통신 에러입니다. 잠시 후 다시 시도하여 주십시요."); }
			});
		}
	}

	$('#download_button').click(function() {
		axFrame.location.replace('./request.detail.php?idx=<?=$DATA['idx']?>&mode=download');
	});
	</script>