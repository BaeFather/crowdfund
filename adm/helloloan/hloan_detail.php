<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');

$sub_menu = '910300';
auth_check($auth[$sub_menu], "w");

$html_title = $menu['menu910'][3][1];

$g5['title'] = '주담대 신청';

include_once (G5_ADMIN_PATH.'/admin.head.php');

?>

<div class="tbl_frm01 tbl_wrap">

<form name="fm" method="post" action="hloan_detail_save.php" autocomplete="off" enctype="multipart/form-data">

	<table class="table table-bordered" style="max-width:1000px; margin: 10px auto;">
		<colgroup>
			<col style="width:14%">
			<col style="width:36%">
			<col style="width:17%">
			<col style="width:33%">
		</colgroup>
		<tbody>

			<tr>
				<th colspan=4 style="background-color:#76C9DD; text-align:center; ">협력사 주담대 신청 상세보기</th>
			</tr>

			<tr>
				<th scope="row" class="tit">상태</th>
				<td>
					<select name="recyn" id="recyn" class="input-sm" style="">
						<option value="">상태</option>
						<option value="A">전체</option>
						<option value="1">심사중</option>
						<option value="2">승인</option>
						<option value="4">최종승인</option>
						<option value="5">부재</option>
						<option value="6">대출취소</option>
						<option value="7">협의지연</option>
						<option value="8">차주통화완료</option>
						<option value="9">고객자서예정</option>
						<option value="10">고객자서완료</option>
						<option value="11">펀딩중</option>
						<option value="12">기표완료</option>
						<option value="13">반려</option>
						<option value="14">감액요청</option>
					</select>
				</td>
				<th scope="row" class="tit">협력사명</th>
				<td>
					<?
					$hm_sql = "SELECT hmseq, cname FROM hloan_member ORDER BY cname";
					$hm_res = sql_query($hm_sql);
					$hm_cnt = $hm_res->num_rows;
					?>
					<select name="hmseq" id="hmseq" class="input-sm" style="">
						<option value="">협력사</option>
					<?
					for ($i=0 ; $i<$hm_cnt ; $i++) {
						$hm_row = sql_fetch_array($hm_res);
						?>
						<option value="<?=$hm_row["hmseq"]?>"><?=$hm_row["cname"]?></option>
						<?
					}
					?>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">담보물 주소</th>
				<td colspan=3>
					<input type="text" class="form-control input-sm" style="width:500px;display:inline;" name="laddr" id="laddr" />
					<input type="hidden" name="d_code"   id="d_code" />
					<input type="hidden" name="mg_id"   id="mg_id" />
					<input type="hidden" name="ju_seri" id="ju_seri" />
					<input type="hidden" name="mg_id2"   id="mg_id2" /> <!-- 단지기본일련번호 -->
					<input type="hidden" name="ju_seri2" id="ju_seri2" /> <!-- 면적일련번호 -->
					<input type="hidden" name="laddr2" id="laddr2" />
					<!--button type="button" onclick="go_kb_addr_srch();" style="margin-right:15px;" class="btn btn-sm btn-default">검색</button-->
					<button type="button" onclick="go_kb_addr_srch2();" style="margin-right:15px;" class="btn btn-sm btn-default">검색</button>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">KB시세 URL</th>
				<td colspan=3>
					<input type="text" class="form-control input-sm" style="width:100%;" name="kbquote" id="kbquote" value="<?=$row['kbquote']?>"/>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">원차주명(한글)</th>
				<td>
					<input type="text" class="form-control input-sm" name="pname" id="pname" value="<?=$row['pname']?>" style="width:200px;"/>
				</td>
				<th scope="row" class="tit">원차주명(영문)</th>
				<td>
					성   <input type="text" class="form-control input-sm" name="pname_E_first" id="pname_E_first" value="<?=$row['pname_E_first']?>" style="width:70px;display:inline;"/>&nbsp;&nbsp;&nbsp;
					이름 <input type="text" class="form-control input-sm" name="pname_E_last"  id="pname_E_last"  value="<?=$row['pname_E_last']?>"  style="width:120px;display:inline;"/>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">차주 주민번호</th>
				<td>
					<input type="text" class="form-control input-sm" name="jumin" id="jumin" value="" style="width:200px;"/>
				</td>
				<th>차주 연락처</th>
				<td>
					<input type="text" class="form-control input-sm" name="pphone1" id="pphone1" value="" style="width:200px;"/>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">담보 제공자</th>
				<td>
					<input type="text" class="form-control input-sm" name="dambo_pname" id="dambo_pname" value="" style="width:200px;"/>
				</td>
				<th scope="row" class="tit">담보제공자 연락처</th>
				<td>
					<input type="text" class="form-control input-sm" name="dambo_pphone" id="dambo_pphone" value="" style="width:200px;"/>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">소득(원)</th>
				<td>
					<select name="pcp_income" id="pcp_income" class="form-control input-sm" style="display:inline;width:150px;">
						<option value="">소득구간</option>
						<option value="3천이하">3천이하</option>
						<option value="3천초과 ~ 5천이하">3천 초과 ~ 5천 이하</option>
						<option value="5천초과 ~ 1억이하">5천 초과 ~ 1억 이하</option>
						<option value="1억초과">1억 초과</option>
					</select>
				</td>
				<th scope="row" class="tit">신용점수</th>
				<td>
					<input type="text" class="form-control input-sm" name="crating" id="crating" value="" style="width:50px;"/>
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">대출기간</th>
				<td>
					<select name="mkind" id="mkind" class="form-control input-sm" style="display:inline;width:150px;">
						<option value=""></option>
						<option value="A">1개월 미만</option>
						<option value="1">1개월</option>
						<option value="2">2개월</option>
						<option value="3">3개월</option>
						<option value="4">4개월</option>
						<option value="5">5개월</option>
						<option value="6">6개월</option>
						<option value="7">7개월</option>
						<option value="8">8개월</option>
						<option value="9">9개월</option>
						<option value="10">10개월</option>
						<option value="11">11개월</option>
						<option value="12" selected>12개월</option>
						<option value="13">13개월</option>
						<option value="14">14개월</option>
						<option value="15">15개월</option>
						<option value="16">16개월</option>
						<option value="17">17개월</option>
						<option value="18">18개월</option>
						<option value="19">19개월</option>
						<option value="20">20개월</option>
						<option value="21">21개월</option>
						<option value="22">22개월</option>
						<option value="23">23개월</option>
						<option value="24">24개월</option>
					</select>
				</td>
				<th scope="row" class="tit">거래 목적</th>
				<td>
					<input type="radio" name="loan_for" id="rr1" style="margin:0;" value="가계"> <label for="rr1" style="margin-right:15px;">가계</label>
					<input type="radio" name="loan_for" id="rr2" style="margin:0;" value="대환"> <label for="rr2" style="margin-right:15px;">대환</label>
					<input type="radio" name="loan_for" id="rr3" style="margin:0;" value="매매"> <label for="rr3">매매</label>
				</td>
			</tr>

			<tr>
				<th>직업</th>
				<td>
					<select name="pcp_job_group" id="pcp_job_group" class="form-control input-sm" style="display:inline;width:150px;">
						<option>직업선택</option>
						<option value="회사원">회사원</option>
						<option value="전문직">전문직</option>
						<option value="공무원">공무원</option>
						<option value="농축산업종사자">농축산업종사자</option>
						<option value="자유직/프리랜서">자유직/프리랜서</option>
						<option value="전업주부">전업주부</option>
						<option value="학생/군인">학생/군인</option>
						<option value="무직">무직</option>
						<option value="개인사업자/자영업자">개인사업자/자영업자</option>
						<option value="카지노사업">카지노사업</option>
						<option value="대부업">대부업</option>
						<option value="환전업">환전업</option>
						<option value="고가귀금속판매업">고가귀금속판매업</option>
						<option value="가상통화산업 관련 종사자">가상통화산업 관련 종사자</option>
					</select>
				</td>
				<th>직장명</th>
				<td>
					<input type="text" class="form-control input-sm" name="pcp_company" id="pcp_company" value="" style="width:250px;"/>
				</td>
			</tr>

			<tr>
				<th>직장주소</th>
				<td colspan=3>
					<input type="text" class="form-control input-sm" name="pcp_comp_addr_post" id="pcp_comp_addr_post" value="" style="width:100px; margin-bottom:5px; display:inline; "/>
					<button type="button" id="save_button" class="btn btn-default" onclick="sample4_execDaumPostcode()" onclick11="go_addr_srch('c');" style="height:30px; margin-bottom:5px;">검색</button>
					<input type="text" name="pcp_comp_addr" id="pcp_comp_addr" class="form-control input-sm" style="width:500px; margin-bottom:5px;"/>
					<input type="text" name="pcp_comp_addr2" id="pcp_comp_addr2" class="form-control input-sm" style="width:400px;"/>
				</td>
			</tr>

			<tr>
				<th colspan=4 style="background-color:#76C9DD; text-align:center; ">KB 시세 <span id="kijun"></span>
					<input type="hidden" name="kb_date" id="kb_date" value="<?=$kb_date?>"/>
					<!--button type="button" class="btn btn-default" onclick="srch_sise();" style="margin-left:15px; height:25px; padding-top:2px;">가져오기</button>
					&nbsp;&nbsp;&nbsp; -->
					<button type="button" class="btn btn-default" onclick="srch_sise2();" style="margin-left:15px; height:25px; padding-top:2px;">가져오기</button>
				</th>
			</tr>

			<tr>
				<td colspan=4 style="padding:0;">
					<table class="table table-bordered">					
						<tr>
							<th style="text-align:center;height:40px;">전용면적(㎡)</th>
							<th style="text-align:center;">일반가</td>
							<th style="text-align:center;">하한가</td>
							<th style="text-align:center;">최근매매가 <span id="kb_sil_date_disp"></span><input type="hidden" name="kb_sil_date" id="kb_sil_date"/></td>
							<th style="text-align:center;">세대수</td>
							<th style="text-align:center;">대지권등기</td>
							<th style="text-align:center;">소액임차보증금</td>
						</tr>
						<tr>
							<td style="text-align:center;height:40px;">
								<input type="text" class="form-control input-sm" name="kb_jm" id="kb_jm" value="" style="width:70px; display:inline; text-align:center; " onkeyup="fn_number_comaX('kb_jm',this.value, $(this).index());" />
							</td>
							<td style="text-align:center;">
								<input type="text" class="form-control input-sm" name="kb_il" id="kb_il" value="" style="width:150px; display:inline; text-align:center; " onkeyup="fn_number_comaX('kb_il',this.value, $(this).index());"/>
							</td>
							<td style="text-align:center;">
								<input type="text" class="form-control input-sm" name="kb_low" id="kb_low" value="" style="width:150px; display:inline; text-align:center; " onkeyup="fn_number_comaX('kb_low',this.value, $(this).index());" />
							</td>
							<td style="text-align:center;">
								<input type="text" class="form-control input-sm" name="kb_sil" id="kb_sil" value="" style="width:150px; display:inline; text-align:center; " onkeyup="fn_number_comaX('kb_low',this.value, $(this).index());" />
							</td>
							<td style="text-align:center;">
								<input type="text" class="form-control input-sm" name="kb_tot_house" id="kb_tot_house" value="" style="width:50px; display:inline; text-align:center; "/>
							</td>
							<td style="text-align:center;">
								<select class="form-control input-sm" name="land_yn" id="land_yn" style="width:70px; display:inline; ">
									<option value="">선택</option>
									<option value="Y" selected>Y</option>
									<option value="N">N</option>
								</select>
							</td>
							<td style="text-align:center;">
								<input type="text" class="form-control input-sm" name="house_deposit" id="house_deposit" value="" style="width:120px; display:inline; text-align:center; " onkeyup="fn_number_comaX('house_deposit',this.value, $(this).index());" />
							</td>
						</tr>
					</table>
				</td>
			</tr>

			<tr>
				<th colspan=4 style="background-color:#76C9DD; text-align:center; ">대출 정보</th>
			</tr>

			<tr>
				<th>대출신청금액</th>
				<td>
					<input type="text" name="ddmoney" id="ddmoney" class="form-control input-sm" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('ddmoney',this.value, $(this).index());"/>
				</td>
				<th>채권최고액</th>
				<td>
					<input type="text" name="bsmoney" id="bsmoney" class="form-control input-sm" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('bsmoney',this.value, $(this).index());" />
				</td>
			</tr>

			<tr>
				<th>
					<a OnClick="fn_additem_examount('plus');" style="cursor:pointer;">+</a>
					기대출금액</th>
				<td>
					<input type="text" name="examount[]" class="form-control input-sm" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('examount[]',this.value, $(this).index());" />
					<div id="examountarea"></div>
				</td>
				<th>선순위 채권최고액</th>
				<td>
					<input type="text" name="maxbond[]" class="form-control input-sm" style="width:200px; text-align:right;" onkeyup="fn_number_comaX('maxbond[]',this.value, $(this).index());" />
					<div id="maxbondarea"></div>
				</td>
			</tr>

			<tr>
				<th rowspan=2>시세 기준 값</th>
				<td rowspan=2 style="vertical-align:middle;">
					<input type="radio" name="ltvkind" value="1" id="rr21" style="margin:0;"> <label for="rr21" style="margin-right:15px;">일반가</label>
					<input type="radio" name="ltvkind" value="2" id="rr22" style="margin:0;"> <label for="rr22" style="margin-right:15px;">하한가</label>
					<button type="button" class="btn btn-sm btn-default" onclick="cal_ltv();">계산하기</button>
				</td>
				<th>원금기준 LTV(%)</tH>
				<td>
					<input type="text" name="ltvmoney" id="ltvmoney" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/> %
				</td>
			</tr>

			<tr>
				<th>채권최고액기준 LTV(%)</tH>
				<td>
					<input type="text" name="ltvmoney2" id="ltvmoney2" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/> %
				</td>
			</tr>

			<tr>
				<th>가산 금리</th>
				<td>
					<input type="radio" name="add_hellobase" id="rr31" style="margin:0;" value="0" checked> <label for="rr31" style="margin-right:15px;">없음</label>
					<input type="radio" name="add_hellobase" id="rr32" style="margin:0;" value="0.5"> <label for="rr32" style="margin-right:15px;">0.5%</label>
					<input type="radio" name="add_hellobase" id="rr33" style="margin:0;" value="1"> <label for="rr33" style="margin-right:15px;">1%</label>
					<button type="button" class="btn btn-sm btn-default" onclick="fn_calc_eja();">계산하기</button>
				</td>
				<th>금리(%)</th>
				<td>
					<input type="text" name="hellobase" id="hellobase" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/> %
				</td>
			</tr>

			<tr>
				<th scope="row" class="tit">매각가율</th>
				<td>
					<input type="text" class="form-control input-sm" name="sale_per" id="sale_per" value="" style="width:200px; display:inline;"/> %
				</td>
				<th>대출만기일</th>
				<td>
					<input type="text" name="hloan_end_date" id="hloan_end_date" class="form-control input-sm datepicker" style="width:100px; display:inline; text-align:center; "/>
				</td>
			</tr>

			<tr>
				<th>헬로 수수료율(%)</th>
				<td>
					<input type="text" name="fees" id="fees" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/> %
				</td>
				<th rowspan=2>플랫폼이용료(원)</th>
				<td rowspan=2 style="vertical-align:middle;">
					<input type="text" id="hellofee" name="hellofee" class="form-control input-sm" style="width:150px; display:inline; text-align:center;" onkeyup="fn_number_comaX('hellofee',this.value, $(this).index());"/> 원
					<button type="button" class="btn btn-sm btn-default" style="margin-left:3px; margin-bottom:5px;" onclick="cal_hellofee();">계산하기</button>
				</td>
			</tr>

			<tr>
				<th>중개 수수료율(%)</th>
				<td>
					<input type="text" name="hm_fees" id="hm_fees" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/> %
				</td>
			</tr>

			<tr>
				<th>특이사항</th>
				<td colspan=3>
					<textarea name="content" class="text01"></textarea>
				</td>
			</tr>

			<tr>
				<th>원인서류</th>
				<td colspan=3>
					<input type="FILE" name="i_file[]" class="" />
					<input type="FILE" name="i_file[]" class="" style="margin-top:5px;" />
					<input type="FILE" name="i_file[]" class="" style="margin-top:5px;"  />
					<input type="FILE" name="i_file[]" class="" style="margin-top:5px;"  />
					<input type="FILE" name="i_file[]" class="" style="margin-top:5px;"  />
				</td>
			</tr>

			<tr>
				<th>헬로펀딩 상품</th>
				<td>
					<select name="productyn" class="form-control input-sm" style="display:inline;width:150px;">
						<option>헬로상품</option>
						<option value="1">대기</option>
						<option value="2">기표준비</option>
						<option value="3">모집</option>
						<option value="4">상환중</option>
						<option value="5">상환완료</option>
						<option value="6">취소</option>
						<option value="7">보류</option>
						<option value="8">기표완료</option>
					</select>
				</td>
				<th>품번</th>
				<td>
					<input type="text" name="product_idx" id="product_idx" class="form-control input-sm" style="width:100px; display:inline; text-align:center; "/>
				</td>
			</tr>

		</tbody>
	</table>

	<div style="width:1000px;text-align:right; margin:20px auto 30px;">
		<span style="float:left;">
			<button type="button" id="save_button" class="btn btn-default" onclick="go_save();">대출회원등록</button>
		</span>
		<button type="button" id="save_button" class="btn btn-default" onclick="go_save();">저장하기</button>
		&nbsp;&nbsp;
		<button type="button" id="list_button" class="btn btn-default">목록보기</button>
	</div>

</form>

</div>

<script>
function cal_hellofee() {
	var ddmoney = parseInt($("#ddmoney").val().replace(/,/gi,"")) ; // 대출 신청금액
	var fees = parseFloat($("#fees").val()) / 100;
	var hm_fees = parseFloat($("#hm_fees").val()) / 100;

	if (isNaN(ddmoney)) ddmoney = 0;
	if (isNaN(fees)) fees = 0;
	if (isNaN(hm_fees)) hm_fees = 0;

	var hellofee = Math.floor(ddmoney * (fees + hm_fees)); 
	$("#hellofee").val(number_format(hellofee));
	$("#hellofee").select();
}

function fn_calc_eja() {
	
	var hellobase = 0; // 금리
	var loan_for = $(':radio[name="loan_for"]:checked').val();  // 거래 목적
	var add_hellobase = $(':radio[name="add_hellobase"]:checked').val(); // 가산금리
	    add_hellobase = parseFloat(add_hellobase);
	
	if  (loan_for=="매매") {
		hellobase = 9.5;
	} else {
		
		var addr = $("#laddr").val();
		var ltv = parseFloat($("#ltvmoney").val());
		
		if (addr.match(/서울/) || addr.match(/경기/) || addr.match(/인천/)) {
			
			if (ltv<=60) {
				hellobase = 7.5
			} else if ( ltv>60 && ltv<=70) {
				hellobase = 8;
			} else if (ltv>70 && ltv<=80) {
				hellobase = 8.5;
			} else {
				alert("LTV 80% 초과 취급불가");
				return;
			}
			
		} else if (addr.match(/대전/) || addr.match(/대구/) || addr.match(/광주/) || addr.match(/부산/) || addr.match(/울산/) ) {
			
			if (ltv<=60) {
				hellobase = 8.5
			} else if ( ltv>60 && ltv<=70) {
				hellobase = 9;
			} else if (ltv>70 && ltv<=80) {
				hellobase = 9.5;
			} else {
				alert("LTV 80% 초과 취급불가");
				return;
			}
			
		} else {
			alert("서울,경기,인천\n대전,대구,광주,부산,울산\n외 지역 취급불가");
			return;
		}
	}
	
	hellobase = hellobase + add_hellobase;
	
	$("#hellobase").val(hellobase);
	
}
function cal_ltv() {
	
	if (!$("#ddmoney").val()) {
		alert("대출신청금액을 입력해 주세요.");
		return;
	}
	if (!$('input:radio[name=ltvkind]').is(":checked")) {
		alert("기준가를 선택해 주세요.");
		return;
	} 
	
	var new_money = parseInt($("#ddmoney").val().replace(/,/gi,""));
	var old_money = 0; // 기대출금액 합계
	var old_money2= 0; // 채권최고액 합계
	var k_money = 0;   // 기준가 (일반가 또는 하한가)
	var house_deposit = parseInt($("#house_deposit").val().replace(/,/gi,""));  // 소액임차보증금
	
	var temp = $(':radio[name="ltvkind"]:checked').val();
	if (temp=="1") {  // 일반가
		k_money = parseInt($("#kb_il").val().replace(/,/gi,""));
	} else {  // 하한가
		k_money = parseInt($("#kb_low").val().replace(/,/gi,""));
	}
	
	if (!k_money) {
		alert("기준가가 없습니다.");
		return;
	}

	// 기대출금액 합계를 구한다.
	$('input[name^="examount"]').each( function() {
		old_money = old_money + parseInt(this.value.replace(/,/gi,""));
	});
	if (isNaN(old_money)) old_money = 0;
	
	// 채권최고액 합계를 구한다.
	$('input[name^="maxbond"]').each( function() {
		old_money2 = old_money2 + parseInt(this.value.replace(/,/gi,""));
	});
	if (isNaN(old_money2)) old_money2 = 0;
	
	//var ltv = ( (new_money + old_money) / k_money ).toFixed(4) * 100;  // 기대출금액 ltv
	var ltv = (new_money + old_money) / k_money;
	//ltv = Math.floor(ltv * 10000) / 100;
	ltv = Math.round(ltv * 10000) / 100;
	$("#ltvmoney").val(ltv);
	
	//var ltv2 = ( (new_money + old_money2 + house_deposit) / k_money ).toFixed(4) * 100;  // 채권최고액 ltv
	var ltv2 = (new_money + old_money2 + house_deposit) / k_money;  // 채권최고액 ltv
	//ltv2 = Math.floor(ltv2 * 10000) / 100;
	ltv2 = Math.round(ltv2 * 10000) / 100;
	$("#ltvmoney2").val(ltv2);
	
	
}

function set_house_deposit() {
	var addr = $("#laddr2").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_house_deposit.php',
		data : {'addr':addr},
		success : function(data) {
			//console.log(data);
			$("#house_deposit").val(number_format(data.hs_dp));     // 일반가
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});
}
function go_save() {
	
	var f = document.fm;
/*	
	if (!f.recyn.value) {
		alert("상태값을 선택해 주세요.");
		f.recyn.focus();
		return false;
	}
*/	
	var yn = confirm("이대로 저장하시겠습니까");
	
	if (yn) f.submit();
	
}

function srch_sise() {

	var mg_id = $("#mg_id").val();
	var ju_seri = $("#ju_seri").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_sise.php',
		data : {'mg_id':mg_id , 'ju_seri':ju_seri},
		success : function(data) {
			console.log(data);
			//$("#kb_jm").val(data.jm);
			$("#kb_il").val(number_format(data.mm*10000));     // 일반가
			$("#kb_low").val(number_format(data.mm_b*10000));  // 하한가
			$("#kb_tot_house").val(data.tot_house);  // 총세대수
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}

function srch_sise2() {

	var mg_id    = $("#mg_id").val();
	var ju_seri  = $("#ju_seri").val();
	var mg_id2   = $("#mg_id2").val();
	var ju_seri2 = $("#ju_seri2").val();
	var d_code   = $("#d_code").val();

	$.ajax({
		type : 'post',
		dataType : 'json',
		url : 'ajax_kb_sise2.php',
		data : {'d_code':d_code , 'mg_id2':mg_id2 , 'ju_seri2':ju_seri2},
		success : function(data) {
			console.log(data);
			var sdd = data.mm_date;
			sdd = sdd.substring(0,4)+"."+sdd.substring(4,6)+"."+sdd.substring(6,8);
			var kdd = data.kijun;
			//kdd = kdd.substring(0,4)+"."+kdd.substring(4,6)+"."+kdd.substring(6,8);
			//$("#kb_jm").val(data.jm);
			$("#kb_il").val(number_format(data.mm*10000));     // 일반가
			$("#kb_low").val(number_format(data.mm_b*10000));  // 하한가
			//$("#kb_tot_house").val(data.tot_house);  // 총세대수
			$("#kb_tot_house").val(data.sum_tot_house);  // 총세대수
			$("#kijun").text("("+kdd+")");
			$("#kb_sil").val(number_format(data.mm_sil*10000)); // 실거래가
			$("#kb_sil_date").val(data.mm_date); // 실거래가
			//$("#kb_sil_date_disp").text(" ("+data.mm_date+")");
			$("#kb_sil_date_disp").text(sdd);
			$("#kb_date").val(data.kijun); // 실거래가
		},
		error : function(XMLHttpRequest, textStatus, errorThrown){
			alert("처리중 오류가 발생하였습니다.\n다시 시도하여주십시오. ("+XMLHttpRequest.statusText+")");
			return false;
		}
	});

}
</script>

<script>
function fn_additem_examount(kind)
{
	var t1content = "";
	var t2content = "";
	var t1layer = $("#examountarea");
	var t2layer = $("#maxbondarea");

	var intlength = $("input[name='examount[]']").length;

	if(kind == "plus") {
		t1content = "<input type='text' name='examount[]' value='' class='form-control input-sm' style='width:200px; margin-top:5px; text-align:right;' onkeyup='fn_number_comaX(\"examount[]\",this.value, "+intlength+");' /> ";
		t2content = "<input type='text' name='maxbond[]'  value='' class='form-control input-sm' style='width:200px; margin-top:5px; text-align:right;' onkeyup='fn_number_comaX(\"maxbond[]\",this.value, "+intlength+");' /> ";

		t1layer.append(t1content);
		t2layer.append(t2content);
	}
	
}


function go_addr_srch(gbn) {
	alert(gbn);
}

function go_kb_addr_srch2() {
	var ww = 600;
	var wh = 400;

	var top  = ($(window).height()/2)-(wh/2);
	var left = ($(window).width()/2)-(ww/2);

	window.open('kb_srch2.php','kb_srch','top='+top+',left='+left+',width='+ww+',height='+wh+',toolbar=0,menubar=0,status=0,scrollbars=yes,resizable=yes');
}

function go_kb_addr_srch() {
	var ww = 600;
	var wh = 400;

	var top  = ($(window).height()/2)-(wh/2);
	var left = ($(window).width()/2)-(ww/2);

	window.open('kb_srch.php','kb_srch','top='+top+',left='+left+',width='+ww+',height='+wh+',toolbar=0,menubar=0,status=0,scrollbars=yes,resizable=yes');
}
</script>

<script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
<script>
    //본 예제에서는 도로명 주소 표기 방식에 대한 법령에 따라, 내려오는 데이터를 조합하여 올바른 주소를 구성하는 방법을 설명합니다.
    function sample4_execDaumPostcode() {
        new daum.Postcode({
            oncomplete: function(data) {
                // 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

                // 도로명 주소의 노출 규칙에 따라 주소를 표시한다.
                // 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
                var roadAddr = data.roadAddress; // 도로명 주소 변수
                var extraRoadAddr = ''; // 참고 항목 변수

                // 법정동명이 있을 경우 추가한다. (법정리는 제외)
                // 법정동의 경우 마지막 문자가 "동/로/가"로 끝난다.
                if(data.bname !== '' && /[동|로|가]$/g.test(data.bname)){
                    extraRoadAddr += data.bname;
                }
                // 건물명이 있고, 공동주택일 경우 추가한다.
                if(data.buildingName !== '' && data.apartment === 'Y'){
                   extraRoadAddr += (extraRoadAddr !== '' ? ', ' + data.buildingName : data.buildingName);
                }
                // 표시할 참고항목이 있을 경우, 괄호까지 추가한 최종 문자열을 만든다.
                if(extraRoadAddr !== ''){
                    extraRoadAddr = ' (' + extraRoadAddr + ')';
                }

                // 우편번호와 주소 정보를 해당 필드에 넣는다.
                //document.getElementById('sample4_postcode').value = data.zonecode;
                //document.getElementById("sample4_roadAddress").value = roadAddr;
                //document.getElementById("sample4_jibunAddress").value = data.jibunAddress;
                document.getElementById('pcp_comp_addr_post').value = data.zonecode;
                document.getElementById("pcp_comp_addr").value = roadAddr;
                //document.getElementById("sample4_jibunAddress").value = data.jibunAddress;

            }
        }).open();
    }

function fn_number_comaX(target, obj, idx)
{
		obj = obj.replace(/,/gi,"");

		if(!OnlyNumX(obj))
		{
			alert("숫자만 입력이 가능합니다");
			//$("input[name='"+target+"']").val("");
			return false;
		}
		var retval = numberWithCommasX(obj);
		//console.log(target+" "+obj+" "+idx+" "+retval);
		$("input[name='"+target+"']").eq(idx).val(retval);
}

// 숫자만 입력 기입
function OnlyNumX(word)
{
	reOnlyNum = new RegExp("[0-9]", "g");
	var returnValue = true;
	for(i=0;i<word.length;i++)  {
		 if(!(word.substr(i,1).match(reOnlyNum))) {
			returnValue=false;
		}
	}
	return returnValue;
}

function numberWithCommasX(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

$('#ddmoney').focusout(function() {

	var ddmoney = parseInt($("#ddmoney").val().replace(/,/gi,"")) ; // 대출 신청금액

	if (ddmoney) {
		var chtop = 0;
		chtop = ddmoney * 1.2;
		//chtop = Math.floor(chtop);
	} else if (ddmoney==0) {
		chtop = 0;
	} else {
		chtop = 0;
	}

	$('#bsmoney').val(chtop);

});

</script>

<?
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>