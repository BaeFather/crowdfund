<?

include_once('./_common.php');

if ($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');

while(list($key, $value) = each($_GET)) { if(!is_array(${$key})) ${$key} = trim($value); }

$sub_menu = "920100";
$g5['title'] = $menu['menu920'][1][1];

include_once ('../../admin.head.php');

// Main Sql
$sql = "
	SELECT 
		A.*,
		B.idx, B.title, B.state, B.recruit_amount, B.loan_interest_rate, B.overdue_rate, B.loan_start_date, B.loan_mb_no,
		C.mb_no, C.member_type, C.mb_name, C.mb_co_name, C.mb_co_reg_num, C.mb_hp, C.corp_phone,
		D.contents, D.reg_date, D.mb_id, D.writer
	FROM
		cf_pf_accounts_rcv A
	LEFT JOIN
		cf_product B ON A.product_idx = B.idx 
	LEFT JOIN
		g5_member C ON B.loan_mb_no = C.mb_no
	LEFT JOIN
		cf_pf_accounts_rcv_memo D ON A.idx = D.pf_ar_idx
	WHERE 
		A.idx='$idx'
";
$row = sql_fetch($sql);

// memo Sql
$msql = "
	SELECT
		idx, pf_ar_idx, contents, reg_date, mb_id, writer
	FROM
		cf_pf_accounts_rcv_memo 
	WHERE
		pf_ar_idx = $idx;
";
$mres = sql_query($msql);
$mrow = $mres->num_rows;


// PF 헬로상품 리스트
$psql = "
SELECT 
	A.idx, A.title
FROM 
	cf_product A
LEFT JOIN
	g5_member B ON A.loan_mb_no = B.mb_no
WHERE 
	A.category='2' AND A.mortgage_guarantees='' AND A.idx=A.gr_idx AND A.recruit_amount>=10000 AND A.isTest='' AND A.state IN(1,2,5,8)
ORDER BY 
	A.idx desc
";
$pres = sql_query($psql);


// 대출금액
$TOTAL_AMT = sql_fetch("
	SELECT SUM(recruit_amount) AS total_amount FROM cf_product WHERE gr_idx='".$row['idx']."' AND recruit_amount > 10000
");
$CHK_AMT = sql_fetch("
	SELECT 
		SUM(A.recruit_amount) AS chk_amount 
	FROM 
		cf_product A 
	LEFT JOIN 
		cf_pf_accounts_rcv B ON A.idx = B.product_idx
	WHERE 
		B.group_idx='".$row['idx']."' AND B.exec_yn='Y' AND A.recruit_amount > 10000
");

$row['loan_amount'] = $TOTAL_AMT['total_amount']-$CHK_AMT['chk_amount'];

// 미집행금액
$row['yet_amount'] = ($row['contract_amount']-$row['loan_amount']);


// 회원 구분
$loan_name = '';
$unique_num = '';
$phone_num = '';
$member_unique_num = getJumin($row['mb_no']);
if($row['member_type'] == '1') {
	$loan_name = $row['mb_name'];
	$unique_num = $member_unique_num;
	$phone_num = masterDecrypt($row['mb_hp']);
} else if($row['member_type'] == '2') {
	$loan_name = $row['mb_co_name'];
	$unique_num = $row['mb_co_reg_num'];
	$phone_num = masterDecrypt($row['corp_phone']);
}

// 연대보증인 정보
$guarantor_uniqno1 = masterDecrypt($row['guarantor_uniqno1']);
$guarantor_phone1 = masterDecrypt($row['guarantor_phone1']);
$guarantor_uniqno2 = masterDecrypt($row['guarantor_uniqno2']);
$guarantor_phone2 = masterDecrypt($row['guarantor_phone2']);
$guarantor_uniqno3 = masterDecrypt($row['guarantor_uniqno3']);
$guarantor_phone3 = masterDecrypt($row['guarantor_phone3']);
$guarantor_uniqno4 = masterDecrypt($row['guarantor_uniqno4']);
$guarantor_phone4 = masterDecrypt($row['guarantor_phone4']);
$guarantor_uniqno5 = masterDecrypt($row['guarantor_uniqno5']);
$guarantor_phone5 = masterDecrypt($row['guarantor_phone5']);


// 상품 상태
$state = '';
if($row['state'] == '1') {
	$state = '이자상환중';
} else if($row['state'] == '2') {
	$state = '상환완료';
} else if($row['state'] == '5') {
	$state = '중도상환';
} else if($row['state'] == '8') {
	$state = '연체';
}


// 수정, 저장 value 값
$pfidx = $_GET['idx'];

if($row['idx'] || !$row['idx']&&$row['note']) {
	$action='update'; 
} else {
	$action='insert';
}


// 파일
$ori_file = explode(";", $row['origin_file']);
$tmp_file = explode(";", $row['temp_file']);

// 암호화
$blind_unique_num = (strlen($unique_num) > 4) ? substr($unique_num, 0, strlen($unique_num)-4) . "●●●●" : $unique_num;
$blind_phone_num = (strlen($phone_num) > 4) ? substr($phone_num, 0, strlen($phone_num)-4) . "●●●●" : $phone_num;

$b_guarantor_uniqno1 = (strlen($guarantor_uniqno1) > 4) ? substr($guarantor_uniqno1, 0, strlen($guarantor_uniqno1)-4) . "●●●●" : $guarantor_uniqno1;
$b_guarantor_phone1 = (strlen($guarantor_phone1) > 4) ? substr($guarantor_phone1, 0, strlen($guarantor_phone1)-4) . "●●●●" : $guarantor_phone1;
$b_guarantor_uniqno2 = (strlen($guarantor_uniqno2) > 4) ? substr($guarantor_uniqno2, 0, strlen($guarantor_uniqno2)-4) . "●●●●" : $guarantor_uniqno2;
$b_guarantor_phone2 = (strlen($guarantor_phone2) > 4) ? substr($guarantor_phone2, 0, strlen($guarantor_phone2)-4) . "●●●●" : $guarantor_phone2;
$b_guarantor_uniqno3 = (strlen($guarantor_uniqno3) > 4) ? substr($guarantor_uniqno3, 0, strlen($guarantor_uniqno3)-4) . "●●●●" : $guarantor_uniqno3;
$b_guarantor_phone3 = (strlen($guarantor_phone3) > 4) ? substr($guarantor_phone3, 0, strlen($guarantor_phone3)-4) . "●●●●" : $guarantor_phone3;
$b_guarantor_uniqno4 = (strlen($guarantor_uniqno4) > 4) ? substr($guarantor_uniqno4, 0, strlen($guarantor_uniqno4)-4) . "●●●●" : $guarantor_uniqno4;
$b_guarantor_phone4 = (strlen($guarantor_phone4) > 4) ? substr($guarantor_phone4, 0, strlen($guarantor_phone4)-4) . "●●●●" : $guarantor_phone4;
$b_guarantor_uniqno5 = (strlen($guarantor_uniqno5) > 4) ? substr($guarantor_uniqno5, 0, strlen($guarantor_uniqno5)-4) . "●●●●" : $guarantor_uniqno5;
$b_guarantor_phone5 = (strlen($guarantor_phone5) > 4) ? substr($guarantor_phone5, 0, strlen($guarantor_phone5)-4) . "●●●●" : $guarantor_phone5;


// 권한 있는 사람만 확인 가능
if($member['mb_id']=='admin_ysm1351' || $member['mb_id']=='admin_youngsin1969' || $member['mb_id']=='admin_com482' || $member['mb_id']=='admin_kor11571' || $member['mb_id']=='admin_sundol4') {
	$uni_num = $unique_num;
	$ph_num = $phone_num;
} else {
	$uni_num = $blind_unique_num;
	$ph_num = $blind_phone_num;
	$guarantor_uniqno1 = $b_guarantor_uniqno1;
	$guarantor_phone1 = $b_guarantor_phone1;
	$guarantor_uniqno2 = $b_guarantor_uniqno2;
	$guarantor_phone2 = $b_guarantor_phone2;
	$guarantor_uniqno3 = $b_guarantor_uniqno3;
	$guarantor_phone3 = $b_guarantor_phone3;
	$guarantor_uniqno4 = $b_guarantor_uniqno4;
	$guarantor_phone4 = $b_guarantor_phone4;
	$guarantor_uniqno5 = $b_guarantor_uniqno5;
	$guarantor_phone5 = $b_guarantor_phone5;
}


add_stylesheet('<link rel="stylesheet" href="css/pf_rcv.css" />', 0);
?>


<div class="tbl_head02 tbl_wrap">
	<form id="frmPfRcv" name="frmPfRcv" method="post" class="form-horizontal" enctype="multipart/form-data">
		<input type="hidden" name="prdidx" value="<?=$row['idx']?>"/>
		<input type="hidden" name="action" value="<?=$action?>" />

		<p class="info-title">채권정보</p>
		<table class="frm-info">
			<colgroup>
				<col width="10%"/>
				<col width="30%"/>
				<col width="10%"/>
				<col width="20%"/>
				<col width="10%"/>
				<col width="20%"/>
			</colgroup>
			<tr>
				<th>헬로상품</th>
				<td>
					<select name="title" id="title" class="form-control input-sm" onchange="pfProductList(this.value);" <?=(!$row['idx'] || !$row['idx']&&$row['note'])?'':'disabled'?>>
						<option value="">상품 선택</option>
						<?
							while($prow = sql_fetch_array($pres)) {
								$selected = ($prow['idx']==$row['idx']) ? 'selected' : '';
								echo '<option value="'.$prow['idx'].'" '.$selected.'>'.$prow['title'].'</option>';
							}
						?>
					</select>
				</td>
				<th>상품상태</th>
				<td><input type="text" name="state" value="<?=$state?>" id="state" class="form-control input-sm read-input" readonly /></td>
				<th>비고</th>
				<td><input type="text" name="rcv_note" value="<?=$row['note']?>" id="rcv_note" class="form-control input-sm"/></td>
			</tr>
			<tr>
				<th>약정금액</th>
				<td><input type="text" name="contract_amount" value="<?=number_format($row['contract_amount'])?>" id="contract_amount" class="form-control input-sm" onkeyup="inputNumberFormat(this)"/></td>
				<th>이자율</th>
				<td class="rate">
					정상이자 : <input type="text" name="loan_interest_rate" value="<?=$row['loan_interest_rate']?>" id="loan_interest_rate" class="form-control input-sm read-input" readonly /> %
					<br /> 
					연체이자 : <input type="text" name="overdue_rate" value="<?=$row['overdue_rate']?>" id="overdue_rate" class="form-control input-sm read-input" readonly /> %
				</td>
				<th>약정기간</th>
				<td>
					<select name="rcv_period" id="rcv_period" class="form-control input-sm" onchange="periodChange(this.value);">
						<option value="">기간 선택</option>
						<?
							if($prow['idx'] || $row['idx']) {
								for($index=1; $index<=36; $index++) {
									$selected = ($index==$row['period']) ? 'selected' : '';
									echo '<option value="'.$index.'" '.$selected.'>'.$index.'개월</option>';
								}
							}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>대출금액</th>
				<td><input type="text" name="loan_amount" value="<?=number_format($row['loan_amount'])?>" id="loan_amount" class="form-control input-sm read-input" readonly />원</td>
				<th>미집행금액</th>
				<td><input type="text" name="yet_amount" value="<?=number_format($row['yet_amount'])?>" id="yet_amount" class="form-control input-sm read-input" readonly />원</td>
				<th>대출기간</th>
				<td class="loan-date">
					<input type="text" name="loan_start_date" value="<?=$row['loan_start_date']?>" id="loan_start_date" class="form-control input-sm read-input" readonly />
					~
					<input type="text" name="loan_end_date" value="<?=$row['loan_end_date']?>" id="loan_end_date" class="form-control input-sm read-input" readonly />
				</td>
			</tr>
			<tr>
				<th>시행사</th>
				<td><input type="text" name="developer" value="<?=$row['developer']?>" id="developer" class="form-control input-sm"/></td>
				<th>시공사</th>
				<td><input type="text" name="constructor" value="<?=$row['constructor']?>" id="constructor" class="form-control input-sm"/></td>
				<th>신탁사</th>
				<td><input type="text" name="trust" value="<?=$row['trust']?>" id="trust" class="form-control input-sm"/></td>
			</tr>
			<tr>
				<th>차주명</th>
				<td><input type="text" name="mb_loan_name" value="<?=$loan_name?>" id="mb_loan_name" class="form-control input-sm read-input" readonly /></td>
				<th>차주 고유번호</th>
				<td><input type="text" name="mb_loan_num" value="<?=$uni_num?>" id="mb_loan_num" class="form-control input-sm read-input" readonly /></td>
				<th>차주 연락처</th>
				<td><input type="text" name="mb_loan_phone" value="<?=$ph_num?>" id="mb_loan_phone" class="form-control input-sm read-input" readonly /></td>
			</tr>
			<tr>
				<th>연대보증인명<p class="more-info">▼</p></th>
				<td><input type="text" name="guarantor_name1" value="<?=$row['guarantor_name1']?>" id="guarantor_name1" class="form-control input-sm"/></td>
				<th>연대보증인 고유번호</th>
				<td><input type="text" name="guarantor_uniqno1" value="<?=$guarantor_uniqno1?>" id="guarantor_uniqno1" class="form-control input-sm" /></td>
				<th>연대보증인 연락처</th>
				<td><input type="text" name="guarantor_phone1" value="<?=$guarantor_phone1?>" id="guarantor_phone1" class="form-control input-sm" /></td>
			</tr>
			<tr class="guarantor-wrap">
				<th>연대보증인명</th>
				<td><input type="text" name="guarantor_name2" value="<?=$row['guarantor_name2']?>" id="guarantor_name2" class="form-control input-sm"/></td>
				<th>연대보증인 고유번호</th>
				<td><input type="text" name="guarantor_uniqno2" value="<?=$guarantor_uniqno2?>" id="guarantor_uniqno2" class="form-control input-sm" /></td>
				<th>연대보증인 연락처</th>
				<td><input type="text" name="guarantor_phone2" value="<?=$guarantor_phone2?>" id="guarantor_phone2" class="form-control input-sm" /></td>
			</tr>
			<tr class="guarantor-wrap">
				<th>연대보증인명</th>
				<td><input type="text" name="guarantor_name3" value="<?=$row['guarantor_name3']?>" id="guarantor_name3" class="form-control input-sm"/></td>
				<th>연대보증인 고유번호</th>
				<td><input type="text" name="guarantor_uniqno3" value="<?=$guarantor_uniqno3?>" id="guarantor_uniqno3" class="form-control input-sm" /></td>
				<th>연대보증인 연락처</th>
				<td><input type="text" name="guarantor_phone3" value="<?=$guarantor_phone3?>" id="guarantor_phone3" class="form-control input-sm" /></td>
			</tr>
			<tr class="guarantor-wrap">
				<th>연대보증인명</th>
				<td><input type="text" name="guarantor_name4" value="<?=$row['guarantor_name4']?>" id="guarantor_name4" class="form-control input-sm"/></td>
				<th>연대보증인 고유번호</th>
				<td><input type="text" name="guarantor_uniqno4" value="<?=$guarantor_uniqno4?>" id="guarantor_uniqno4" class="form-control input-sm" /></td>
				<th>연대보증인 연락처</th>
				<td><input type="text" name="guarantor_phone4" value="<?=$guarantor_phone4?>" id="guarantor_phone4" class="form-control input-sm" /></td>
			</tr>
			<tr class="guarantor-wrap">
				<th>연대보증인명</th>
				<td><input type="text" name="guarantor_name5" value="<?=$row['guarantor_name5']?>" id="guarantor_name5" class="form-control input-sm"/></td>
				<th>연대보증인 고유번호</th>
				<td><input type="text" name="guarantor_uniqno5" value="<?=$guarantor_uniqno5?>" id="guarantor_uniqno5" class="form-control input-sm" /></td>
				<th>연대보증인 연락처</th>
				<td><input type="text" name="guarantor_phone5" value="<?=$guarantor_phone5?>" id="guarantor_phone5" class="form-control input-sm" /></td>
			</tr>
			<tr>
				<th>사업지주소</th>
				<td colspan="5"><input type="text" name="company_addr" value="<?=$row['company_addr']?>" id="company_addr" class="form-control input-sm"/></td>
			</tr>
			<tr>
				<th>담보내용</th>
				<td colspan="5"><textarea name="content_text" id="content_text" cols="30" rows="10" class="form-control input-sm"><?=$row['content_text']?></textarea></td>
			</tr>
			<tr>
				<th>파일첨부</th>
				<td colspan="5">
					<input type="file" id="fileupload" name="origin_file[]" multiple>
					<label for="fileupload">파일추가</label>
					<div id="fileList">
					<? for($i=0; $i<count($ori_file); $i++) { ?>
						<p class="file_name_test">
							<a id="uploadFileName" href="./uploads/<?=$tmp_file[$i]?>" target="_blank"><?=$ori_file[$i]?></a>
							<? if($ori_file[$i]) { ?>
							<a onclick="deleteUploadFile('<?=$idx?>','<?=$ori_file[$i]?>','<?=$tmp_file[$i]?>');" class="upload_file_delbtn">삭제</a>
							<? } ?>
						</p>
					<? } ?>
					</div>
				</td>
			</tr>
			<tr>
				<th>메모</th>
				<td colspan="5">					
					<div class="memo-wrap">
						<ul class="comment-txtarea">
							<li><textarea id="memo" name="memo"></textarea></li>
							<li><button type="button" id="frmCmtSubmit" class="btn btn-primary" onclick="saveMemo(<?=$idx?>);">등 록</button></li>
						</ul>
						<table>
							<colgroup>
								<col width="75%">
								<col width="20%">
								<col width="5%">
							</colgroup>
							<tr style="background: #FAFAFA">
								<th align="center">내용</td>
								<th align="center">등록일시</td>
								<th align="center">삭제</td>
							</tr>
							
							<? for($j=0; $j<$mrow; $j++) {
								$MEMO[$j] = sql_fetch_array($mres);
								
								$memo = nl2br(htmlSpecialChars($MEMO[$j]['contents']));

								$del_btn = '';
								if($MEMO[$j]['mb_id'] == $member['mb_id']) {
									$del_btn = "<p class='memo-del-btn' onclick='memoDel(".$MEMO[$j]['idx'].");'>삭제</p>";	
								}
							?>
							<tr>
								<td><?=$memo?></td>	
								<td><?=$MEMO[$j]['reg_date']?></td>	
								<td><?=$del_btn?></td>	
							</tr>
							<? } ?>
						</table>
					</div> 
				</td>
			</tr>
		</table>
		<div class="btn-wrap">
			<button type="button" class="btn btn-primary" onclick="saveForm(<?=$idx?>);" id="rcvBtn"></button>
			<? if($action=='update') { ?>
			<button type="button" class="btn btn-delete" onclick="deleteForm(<?=$idx?>);">삭제</button>
			<? } ?>
		</div>
	</form>

	<!--------------------------------- 해당 상품의 동일차주 상품리스트 출력 --------------------------------------->
	<div class="pf-sub-list">
	</div>

</div>


<script type="text/javascript">

 
function pfProductList(opt) {
	
	if(opt) {
	
		var index = 1;
		var action = $('input[name=action]').val();

		for(index=1; index<=36; index++) {
			var periodOpt = "<option value="+index+">"+index+"개월</option>";
			$('#rcv_period').append(periodOpt);
		}

		// 해당 상품 동일 차주 상품 리스트
		$.ajax({
			type: "POST",
			url: "ajax_pf_product_list_ins.php",
			data: {'opt' : opt, 'action' : action},
			success: function(data) {
				if(action=='update') {
					$('.pf-sub-list').html(data);
				}
			}, 
			error: function(xhr, status, error) {
				alert('통신 오류 입니다. 잠시 후 다시 시도하십시오.');
			}
		});
		
		// 해당 상품 정보 
		$.ajax({
			type: "POST",
			url: "ajax_pf_product_info.php",
			data: {'opt' : opt},
			dataType : "json",
			success: function(data) {
				console.log(data);

				$('input[name=prdidx]').val(data.prdidx);
				$('#state').val(data.state);
				$('#loan_interest_rate').val(data.loan_interest_rate);
				$('#overdue_rate').val(data.overdue_rate);
				$("#loan_start_date").val(data.loan_start_date);
				$('#mb_loan_name').val(data.loan_name);
				$('#mb_loan_num').val(data.uni_num);
				$('#mb_loan_phone').val(data.ph_num);
				$('#recruit_amount').text(data.total_amount);

			}, 
			error: function(xhr, status, error) {
				alert('통신 오류 입니다. 잠시 후 다시 시도하십시오.');
			}
		});
	}
	
}



$(document).ready(function(){ pfProductList(<?=$row['idx']?>); });


// 체크박스 클릭했을 때
$('input[name=exec_yn]').on('click', function() {
	//console.log($('input[type=checkbox]:checked').length + ' 총 리스트 개수 : ' + data.list.length);
	//console.log($(this).parents());

	var total_chk = data.list.length;
	var none_chk = total_chk - $('input[type=checkbox]:checked').length;

});



/* file script */
// 파일 삭제 => 업로드 된 파일
function deleteUploadFile(idx, ori_name, tmp_name) {
	var delYn = confirm("선택한 파일을 삭제하시겠습니까?");
	if (delYn) {
		window.location.replace("deleteFile.php?idx="+idx+"&oname="+ori_name+"&tname="+tmp_name);
	}
}

// 파일이 추가되는 순간 addFiles 함수 실행
$(document).ready(function() {
	$("#fileupload").on("change", addFiles);
});

var filesTempArr = [];

// 파일 추가
function addFiles(e) {
	var files = e.target.files;
	var filesArr = Array.prototype.slice.call(files);
	var filesArrLen = filesArr.length;
	var filesTempArrLen = filesTempArr.length;

	for( var i=0; i<filesArrLen; i++ ) {
		filesTempArr.push(filesArr[i]);
		$("#fileList").append("<div>" + filesArr[i].name + "<span class='file_del_btn' onclick='deleteFile("+(filesTempArrLen + i)+");'>삭제</span></div>");
	}

}

// 파일 삭제 => 아직 업로드 되기 전 파일
function deleteFile(index) {

	filesTempArr.splice(i, 1);
	console.log(filesTempArr);
	var innerHtmlTemp = "";
	var filesTempArrLen = filesTempArr.length;

	for(var i=0; i<filesTempArrLen; i++) {
		innerHtmlTemp += "<div>" + filesTempArr[i].name + "<span class='file_del_btn' onclick='deleteFile(event,"+ i +");'>삭제</span></div>";
	}
	$(".file_del_btn").eq(index).parent().html('');

}
/* file script end */

// 등록, 수정 버튼 value값 변경
$(document).ready(function() {
	var btn_val = $('#rcvBtn');
	btn_val.attr('value', $('input[name=action]').val());

	if(btn_val.val()=='insert') {
		btn_val.text('등록');
	} else if(btn_val.val()=='update') {
		btn_val.text('수정');
	}
});


// 메모 삭제
function memoDel(midx) {
	if (confirm('등록된 메모를 지우시겠습니까?')) {
		window.location.replace('pf_rcv_memo_insert.php?midx='+midx);
	}
}

// 대출기간 종료일 설정
function periodChange(month) {
	var period_sel = $("#rcv_period").val();
	$("#rcv_period option:eq("+period_sel+")").attr("selected", "selected");

	var sdate = $("#loan_start_date").val();
	var edate = new Date(sdate);
	edate.setMonth(edate.getMonth() + parseInt(month));
	
	if(edate) {
		edate = dateFormat(edate);  // 날짜 포맷 함수 호출
		$('#loan_end_date').val(edate);
	}
}

// 날짜 포맷 함수
function dateFormat(date) {
        let month = date.getMonth() + 1;
        let day = date.getDate();
        let hour = date.getHours();
        let minute = date.getMinutes();
        let second = date.getSeconds();

        month = month >= 10 ? month : '0' + month;
        day = day >= 10 ? day : '0' + day;

        return date.getFullYear() + '-' + month + '-' + day + ' ';
}

// input 숫자만 입력 가능
function inNumber(){
	if(event.keyCode<48 || event.keyCode>57){
		event.returnValue=false;
	}
}

// input 숫자 입력시 자동 콤마
function inputNumberFormat(obj) {
    obj.value = comma(uncomma(obj.value));
}

function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

function uncomma(str) {
    str = String(str);
    return str.replace(/[^\d]+/g, '');
}

// 연대보증인 정보 폴딩
$(document).ready(function() {

	$('.guarantor-wrap').hide();
	
	$('.more-info').on('click', function() {
		if($('.guarantor-wrap').is(":visible")){
			$('.guarantor-wrap').hide();
			$('.more-info').text('▼');
		}
		else{
			$('.guarantor-wrap').show();
			$('.more-info').text('▲');
		}
	});
	
});


// 폼 저장
function saveForm(idx) {
	var f = document.getElementById('frmPfRcv');

	var title = document.getElementById('title').value;
	var rcv_note = document.getElementById('rcv_note').value;

	if(!title && !rcv_note) {
		alert('상품 미선택 시 비고 입력은 필수입니다.');
		document.getElementById('rcv_note').focus();
		return;
	}
	
	if($('input[name=action]').val()=='insert') {
		if(confirm("등록하시겠습니까?")) {
			f.action = "./pf_rcv_insert.php";
			f.target = "_self";
			f.submit();	
		}
	} else if($('input[name=action]').val()=='update') {
		if(confirm("수정하시겠습니까?")) {
			f.action = "./pf_rcv_insert.php?idx="+idx;
			f.target = "_self";
			f.submit();	
		}
	}

	
}

// 폼 삭제
function deleteForm(idx) {
	var f = document.getElementById('frmPfRcv');

	if(confirm("정말로 삭제하시겠습니까?")) {
		f.action = "./pf_rcv_insert.php?idx="+idx;
		f.target = "_self";
		f.action.value = "delete";
		f.submit();
	}
}

// 메모 저장
function saveMemo(idx) {
	var f = document.getElementById('frmPfRcv');

	if(confirm("등록하시겠습니까?")) {
		f.action = "./pf_rcv_memo_insert.php?idx="+idx;
		f.submit();
	}
}


</script>


<? include_once ('../../admin.tail.php'); ?>