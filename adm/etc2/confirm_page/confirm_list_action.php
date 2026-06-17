<?
include_once('./_common.php');

auth_check($auth[$sub_menu], 'w');
if($is_admin != 'super' && $w == '') alert('최고관리자만 접근 가능합니다.');


switch($mode) {
	case 'delete' :
		
		if($idx) {

			if($type == '1') {  // 완납확인서

				$sql = "
					DELETE 
						A.*, B.*, C.*
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t1 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t1_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);

			} else if($type == '2') {  // 금융거래확인서
				$sql = "
					DELETE 
						A.*, B.*, C.*
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t2 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t2_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);

			} else if($type == '3') {  // 이자납입내역서
				$sql = "
					DELETE 
						A.*, B.*, C.*
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t3 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t3_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);

			}	else if($type == '4') {  // 이자내역서
				$sql = "
					DELETE 
						A.*, B.*, C.*
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t4 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t4_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);
			}

			if($res) {
				msg_replace("삭제되었습니다.", "./confirm_list.php");
			} else {
				msg_replace("삭제하지 못했습니다. 다시 시도해주세요.", "./confirm_list.php");
			}


		}
	
	break;
		
	case 'print' :
	
		header( "Content-type: application/vnd.ms-excel" ); 
		header( "Content-type: application/vnd.ms-excel; charset=utf-8");
		header( "Content-Disposition: attachment; filename = list_print_".$idx.".xls" ); 
		header( "Content-Description: PHP4 Generated Data" );

		if($idx) {

			if($type == '1') {  // 완납확인서

				// 채무자 정보
				$sql = "
					SELECT
						A.creditor_no, B.loan_name, B.loan_birth, B.loan_addr, B.reg_date
					FROM
						cf_paper A
					LEFT JOIN
						cf_paper_t1 B ON A.p_no = B.p_no
					WHERE 
						B.p_no = '$idx'
				";
				$row = sql_fetch($sql);

				if($row['creditor_no']) {
					// 채권자 정보
					$sql2 = "
						SELECT
							A.creditor_no, B.c_no, B.company_name, B.company_addr, B.company_tel
						FROM
							cf_paper A
						LEFT JOIN
							cf_paper_creditor B ON A.creditor_no = B.c_no
						WHERE
							A.creditor_no = ".$row['creditor_no']."
					";
					$row2 = sql_fetch($sql2);
				}
				
				$loan_birth = $row['loan_birth'].'******';

				$reg_date = explode('-', $row['reg_date']);
				$reg_date = $reg_date[0].'년 '.$reg_date[1].'월 '.$reg_date[2].'일';

				// 부채내역
				$sql = "
					SELECT 
						 A.p_no, A.k_idx, A.creditor_no, A.reg_date, B.loan_name, B.loan_addr, 
						 C.loan_amount, C.loan_sdate, C.loan_edate, C.bank_name, C.bank_acc
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t1 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t1_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);
				$cnt = $res->num_rows;
				

				$excel_str = "
					<h2 style='text-align: center; font-family: 바탕; font-size: 24pt;'>완 납 확 인 서</h2>
					<p style='font-family: 바탕;'>◎ 채무자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th colspan='3'>고객명</th>
								<td colspan='7'>".$row['loan_name']."</td>
							</tr>";
				if($row['loan_birth']) {
				$excel_str.= "
							<tr>
								<th colspan='3'>주민등록번호</th>
								<td colspan='7' style='text-align: left'>".$loan_birth."</td>
							</tr>";
				}	
				$excel_str.= "<tr>
								<th colspan='3'>주소</th>
								<td colspan='7'>".$row['loan_addr']."</td>
							</tr> 
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 채권자</p>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<tbody>
							<tr>
								<th colspan='3'>회사명</th>
								<td colspan='7'>".$row2['company_name']."</td>
							</tr>
							<tr>
								<th colspan='3'>주소</th>
								<td colspan='7'>".$row2['company_addr']."</td>
							</tr> 
							<tr>
								<th colspan='3'>연락처</th>
								<td colspan='7'>".$row2['company_tel']."</td>
							</tr>
						</tbody>
					</table>
					<br/>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<p>◎ 부채내역</p>
						<p colspan='10' style='font-size: 12px; text-align: right;'>단위(원)</p>
						<thead>
							<tr>
								<th></th>
								<th colspan='2'>대출금액</th>
								<th colspan='2'>대출실행일</th>
								<th colspan='2'>만료일</th>
								<th>은행</th>
								<th colspan='2'>납입계좌</th>
							</tr>
						</thead>
						<tbody>
				";
				
				for($i=0,$num=1; $i<$cnt; $i++,$num++) {
					$EX_LIST[$i] = sql_fetch_array($res);

					if($EX_LIST[$i]['loan_amount']) {
						$EX_LIST[$i]['loan_amount'] = number_format($EX_LIST[$i]['loan_amount']);
					} else {
						$EX_LIST[$i]['loan_amount'] = '0';
					}
				
					$excel_str.= "
					<tr style='text-align: center;'>
						<td>".$num."</td>
						<td colspan='2'>￦&nbsp;".$EX_LIST[$i]['loan_amount']."</td>
						<td colspan='2'>".$EX_LIST[$i]['loan_sdate']."</td>
						<td colspan='2'>".$EX_LIST[$i]['loan_edate']."</td>
						<td>".$EX_LIST[$i]['bank_name']."</td>
						<td colspan='2' style='mso-number-format:'\@';'>".$EX_LIST[$i]['bank_acc']."&nbsp;</td>
					</tr>
					";
				}

				$excel_str.= "
						</tbody>
					</table>
					<p width='100%' style='font-family: 바탕;'>◎ ".$row['loan_name']."님은 위 내용의 채무를 전액 상환하였음을 알려드립니다.</p>
					<br />
					<table border='0' width='100%' style='font-family: 바탕;'>
						<tr>
							<td colspan='10' style='font-size: 16pt; text-align: center;'>".$reg_date."</td>
						</tr>
						<tr><td></td></tr>
						<tr>
							<td colspan='10' style='font-size: 22pt; text-align: center;'>".$row2['company_name']." <span>(인)</span></td>
						</tr>
					</table>
				";
				
			} else if($type == '2') {  // 금융거래확인서

				$sql = "
					SELECT
						A.creditor_no, B.loan_name, B.loan_birth, B.loan_addr, B.reg_date, 
						B.dambo_kinds, B.dambo_price, B.dambo_date, B.dambo_note, B.is_overdue
					FROM
						cf_paper A
					LEFT JOIN
						cf_paper_t2 B ON A.p_no = B.p_no
					WHERE
						B.p_no = '$idx'
				";
				$row = sql_fetch($sql);

				if($row['creditor_no']) {
					// 채권자 정보
					$sql2 = "
						SELECT
							A.creditor_no, B.c_no, B.company_name, B.company_addr, B.company_tel
						FROM
							cf_paper A
						LEFT JOIN
							cf_paper_creditor B ON A.creditor_no = B.c_no
						WHERE
							A.creditor_no = ".$row['creditor_no']."
					";
					$row2 = sql_fetch($sql2);
				}

				$loan_birth = $row['loan_birth'].'******';

				if($row['dambo_note']=='1') {
					$dambo_note = '선순위';
				} else if($row['dambo_note']=='2') {
					$dambo_note = '후순위';
				}

				if($row['is_overdue']=='Y') {
					$is_overdue = '有';
				} else if($row['is_overdue']=='N') {
					$is_overdue = '無';
				}

				if($row['dambo_price']) {
					$row['dambo_price'] = number_format($row['dambo_price']);
				} else {
					$row['dambo_price'] = '0';
				}

				$reg_date = explode('-', $row['reg_date']);
				$reg_date = $reg_date[0].'년 '.$reg_date[1].'월 '.$reg_date[2].'일';
				

				// 대출금 거래상황
				$sql = "
					SELECT 
						 A.p_no, A.k_idx, A.creditor_no, A.reg_date, B.loan_name, B.loan_addr, 
						 C.loan_kinds, C.loan_sdate, C.loan_edate, C.loan_price, C.loan_remain, C.loan_note
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t2 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t2_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);
				$cnt = $res->num_rows;

			
				$excel_str = "
					<h2 style='text-align: center; font-family: 바탕; font-size: 24pt;'>금 융 거 래 확 인 서</h2>
					<p style='font-family: 바탕;'>◎ 채무자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th>고객명</th>
								<td colspan='9'>".$row['loan_name']."</td>
							</tr>";
				if($row['loan_birth']) {
				$excel_str.= "
							<tr>
								<th>주민등록번호</th>
								<td colspan='9' style='text-align: left'>".$loan_birth."</td>
							</tr>";
				}	
				$excel_str.= "
							<tr>
								<th>주소</th>
								<td colspan='9'>".$row['loan_addr']."</td>
							</tr> 
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 채권자</p>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<tbody>
							<tr>
								<th>회사명</th>
								<td colspan='9'>".$row2['company_name']."</td>
							</tr>
							<tr>
								<th>주소</th>
								<td colspan='9'>".$row2['company_addr']."</td>
							</tr> 
							<tr>
								<th>연락처</th>
								<td colspan='9'>".$row2['company_tel']."</td>
							</tr>
						</tbody>
					</table>
					<br/>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<p style='font-family: 바탕;'>◎ 대출금 거래상황</p>
						<p colspan='10' style='font-size: 12px; text-align: right;'>단위(원)</p>
						<thead>
							<tr>
								<th>종별</th>
								<th colspan='2'>계약일자</th>
								<th colspan='2'>대출기한</th>
								<th colspan='2'>대출금액</th>
								<th colspan='2'>잔액</th>
								<th>비고</th>
							</tr>
						</thead>
						<tbody>
				";

				for($i=0; $i<$cnt; $i++) {
					$EX_LIST[$i] = sql_fetch_array($res);

					if($EX_LIST[$i]['loan_price']) {
						$total_loan_price += $EX_LIST[$i]['loan_price'];  // 합계

						$EX_LIST[$i]['loan_price'] = number_format($EX_LIST[$i]['loan_price']);
					} else {
						$EX_LIST[$i]['loan_price'] = '0';
					}
					
					if($EX_LIST[$i]['loan_remain']) {
						$total_loan_remain += $EX_LIST[$i]['loan_remain'];  // 합계

						$EX_LIST[$i]['loan_remain'] = number_format($EX_LIST[$i]['loan_remain']);
					} else {
						$EX_LIST[$i]['loan_remain'] = '0';
					}


					$excel_str.= "
						<tr style='text-align: center;'>
							<td>".$EX_LIST[$i]['loan_kinds']."</td>
							<td colspan='2'>".$EX_LIST[$i]['loan_sdate']."</td>
							<td colspan='2'>".$EX_LIST[$i]['loan_edate']."</td>
							<td colspan='2'>￦&nbsp;".$EX_LIST[$i]['loan_price']."</td>
							<td colspan='2'>￦&nbsp;".$EX_LIST[$i]['loan_remain']."</td>
							<td>".$EX_LIST[$i]['loan_note']."</td>
						</tr>
					";
				}

				$excel_str.= "
					<tr style='text-align: center;'>
						<td>합계</td>
						<td colspan='2'>".$cnt."건</td>
						<td colspan='2'></td>
						<td colspan='2'>￦&nbsp;".number_format($total_loan_price)."</td>
						<td colspan='2'>￦&nbsp;".number_format($total_loan_remain)."</td>
						<td></td>
					</tr>
				";

				$excel_str.= "
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 담보내용</p>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<thead>
							<tr>
								<th colspan='2'>소재지</th>
								<th>소유자</th>
								<th>관계</th>
								<th colspan='2'>종류</th>
								<th>감정가격</th>
								<th>감정일자</th>
								<th>설정내용</th>
								<th>비고</th>
							</tr>
						</thead>
						<tbody>
							<tr style='text-align: center;'>
								<td colspan='2'>".$row['loan_addr']."</td>
								<td>".$row['loan_name']."</td>
								<td>본인</td>
								<td colspan='2'>".$row['dambo_kinds']."</td>
								<td>￦&nbsp;".$row['dambo_price']."</td>
								<td>".$row['dambo_date']."</td>
								<td>근저당권</td>
								<td>".$dambo_note."</td>
							</tr>
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 기준일 현재 연체(연체대출금 및 지급보증대지급금 보유 또는 이자 분할상환금, 분할상환원리금지체 포함) 여부 : ".$is_overdue."</p>
				";

				$excel_str.= "
					<p style='font-family: 바탕;'>◎ 최근 3개월 이내 10일 이상 계속된 연체 명세</p>
					<table border='1' width='100%' style='font-family: 바탕;'>
						<thead>
							<tr>
								<th rowspan='2'>종별</th>
								<th rowspan='2'>연체발생일</th>
								<th colspan='5'>연체금액</th>
								<th rowspan='2'>연체정리일</th>
								<th rowspan='2'>연체일수</th>
								<th rowspan='2'>비고</th>
							</tr>
							<tr>
								<th colspan='3'>원금</th>
								<th colspan='2'>이자</th>
							</tr>
						</thead>
						<tbody>
					";
					
					if($row['is_overdue']=='N') {  // 연체내역이 없을 때
						$excel_str.= "
								<tr style='text-align: center;'>
									<td colspan='10'>해당 사항 없음</td>
								</tr>
						";
					} else if($row['is_overdue']=='Y') {  // 연체내역이 있을 때
						$excel_str.= "
								<tr>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
						";
					}

					$excel_str.= " 
						</tbody>
					</table>
					<p width='100%' style='text-align: center; font-family: 바탕;'>위와 같이 이상 없음을 확인함.</p>
					<br />
					<table border='0' width='100%' style='font-family: 바탕;'>
						<tr>
							<td colspan='10' style='font-size: 16pt; text-align: center;'>".$reg_date."</td>
						</tr>
						<tr><td></td></tr>
						<tr>
							<td colspan='10' style='font-size: 22pt; text-align: center;'>".$row2['company_name']." <span>(인)</span></td>
						</tr>
					</table>
				";

			} else if($type == '3') {  // 이자납입내역서
				
				$sql = "
					SELECT
						A.creditor_no, B.loan_name, B.loan_birth, B.loan_addr, B.loan_kinds, 
						B.loan_sdate, B.loan_edate, B.loan_price, B.loan_remain, 
						B.basic_date, B.reg_date, B.use_text, B.is_overdue, 
						B.price_field1, B.price_field2 
					FROM
						cf_paper A
					LEFT JOIN
						cf_paper_t3 B ON A.p_no = B.p_no
					WHERE
						B.p_no = '$idx'
				";
				$row = sql_fetch($sql);

				if($row['creditor_no']) {
					// 채권자 정보
					$sql2 = "
						SELECT
							A.creditor_no, B.c_no, B.company_name, B.company_addr, B.company_tel
						FROM
							cf_paper A
						LEFT JOIN
							cf_paper_creditor B ON A.creditor_no = B.c_no
						WHERE
							A.creditor_no = ".$row['creditor_no']."
					";
					$row2 = sql_fetch($sql2);
				}

				
				// 주민등록번호
				$loan_birth = $row['loan_birth'].'******';

				// 등록 날짜
				$reg_date = explode('-', $row['reg_date']);
				$reg_date = $reg_date[0].'년 '.$reg_date[1].'월 '.$reg_date[2].'일';

				// 대출종별(주택담보대출, PF, ABL, 브릿지, 기타)
				if($row['loan_kinds']=='1') {
					$loan_kinds = '주택담보대출';
				} else if($row['loan_kinds']=='2') {
					$loan_kinds = 'PF';
				} else if($row['loan_kinds']=='3') {
					$loan_kinds = 'ABL';
				} else if($row['loan_kinds']=='4') {
					$loan_kinds = '브릿지';
				} else if($row['loan_kinds']=='9') {
					$loan_kinds = '기타';
				}
				
				// 대출금액, 대출잔액
				if($row['loan_price']) {
					$row['loan_price'] = number_format($row['loan_price']);
				} else {
					$row['loan_price'] = '0';
				}

				if($row['loan_remain']) {
					$row['loan_remain'] = number_format($row['loan_remain']);
				} else {
					$row['loan_remain'] = '0';
				}

				// 연체 유무
				if($row['is_overdue']=='Y') {
					$is_overdue = '有';
				} else if($row['is_overdue']=='N') {
					$is_overdue = '無';
				}

				
				// 원리금 및 비용 납입내역
				$sql = "
					SELECT 
						 C.ins_date, C.ins_principal, C.ins_eja, C.field1_price, C.field2_price
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t3 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t3_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);
				$cnt = $res->num_rows;
				
				$excel_str = "
					<h2 style='text-align: center; font-family: 바탕; font-size: 24pt;'>이 자 납 입 내 역 서</h2>
					<p style='font-family: 바탕;'>◎ 채무자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th colspan='2'>고객명</th>
								<td colspan='8'>".$row['loan_name']."</td>
							</tr>";
				if($row['loan_birth']) {
				$excel_str.= "
							<tr>
								<th colspan='2'>주민등록번호</th>
								<td colspan='8' style='text-align: left'>".$loan_birth."</td>
							</tr>";
				}
				$excel_str.= "<tr>
								<th colspan='2'>주소</th>
								<td colspan='8'>".$row['loan_addr']."</td>
							</tr> 
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 채권자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th colspan='2'>회사명</th>
								<td colspan='8'>".$row2['company_name']."</td>
							</tr>
							<tr>
								<th colspan='2'>주소</th>
								<td colspan='8'>".$row2['company_addr']."</td>
							</tr> 
							<tr>
								<th colspan='2'>연락처</th>
								<td colspan='8'>".$row2['company_tel']."</td>
							</tr>
						</tbody>
					</table>
					<br/>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<p>◎ 대출정보</p>
						<p colspan='10' style='font-size: 12px; text-align: right;'>단위(원)</p>
						<thead>
							<tr>
								<th colspan='2'>종별</th>
								<th colspan='2'>대출실행일</th>
								<th colspan='2'>대출종료일</th>
								<th colspan='2'>대출금</th>
								<th colspan='2'>대출잔액</th>
							</tr>
						</thead>
						<tbody>
				";

				$excel_str.= "
							<tr style='text-align: center;'>
								<td colspan='2'>".$loan_kinds."</td>
								<td colspan='2'>".$row['loan_sdate']."</td>
								<td colspan='2'>".$row['loan_edate']."</td>
								<td colspan='2'>￦&nbsp;".$row['loan_price']."</td>
								<td colspan='2'>￦&nbsp;".$row['loan_remain']."</td>
							</tr>
				";

				$excel_str.= "
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 원리금 및 비용 납입내역 (기준일 : ".$row['basic_date'].")</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
					<thead>
						<tr>
							<th rowspan='2'>순번</th>
							<th rowspan='2'>납입일자</th>
							<th colspan='2'>원금</th>
							<th colspan='2'>이자</th>
							<th colspan='4'>비용</th>
						</tr>
						<tr>
							<th colspan='2'>납입금액</th>
							<th colspan='2'>납입금액</th>
							<th colspan='2'>".$row['price_field1']."</th>
							<th colspan='2'>".$row['price_field2']."</th>
						</tr>
					</thead>
					<tbody>
				";

				for($i=0,$num=1; $i<$cnt; $i++,$num++) {
					$LIST[$i] = sql_fetch_array($res);
					
					// 합계
					$total_principal += $LIST[$i]['ins_principal'];
					$total_eja += $LIST[$i]['ins_eja'];
					$total_price1 += $LIST[$i]['field1_price'];
					$total_price2 += $LIST[$i]['field2_price'];
					
					// 금액 number_format 처리
					if($LIST[$i]['ins_principal'] > 0) {
						$LIST[$i]['ins_principal'] = '￦&nbsp;'.number_format($LIST[$i]['ins_principal']);
					} else {
						$LIST[$i]['ins_principal'] = '';
					}
					if($LIST[$i]['ins_eja'] > 0) {
						$LIST[$i]['ins_eja'] = '￦&nbsp;'.number_format($LIST[$i]['ins_eja']);
					} else {
						$LIST[$i]['ins_eja'] = '';
					}
					if($LIST[$i]['field1_price'] > 0) {
						$LIST[$i]['field1_price'] = '￦&nbsp;'.number_format($LIST[$i]['field1_price']);
					} else {
						$LIST[$i]['field1_price'] = '';
					}
					if($LIST[$i]['field2_price'] > 0) {
						$LIST[$i]['field2_price'] = '￦&nbsp;'.number_format($LIST[$i]['field2_price']);
					} else {
						$LIST[$i]['field2_price'] = '';
					}


					$excel_str.= "
						<tr style='text-align: center;'>
							<td>".$num."</td>
							<td>".$LIST[$i]['ins_date']."</td>
							<td colspan='2'>".$LIST[$i]['ins_principal']."</td>
							<td colspan='2'>".$LIST[$i]['ins_eja']."</td>
							<td colspan='2'>".$LIST[$i]['field1_price']."</td>
							<td colspan='2'>".$LIST[$i]['field2_price']."</td>
						</tr>
					";

				}
				
				// 합계 number_format 처리
				if($total_principal > 0) {
					$total_principal = '￦&nbsp;'.number_format($total_principal);
				} else {
					$total_principal = '';
				}
				if($total_eja > 0) {
					$total_eja = '￦&nbsp;'.number_format($total_eja);
				} else {
					$total_eja = '';
				}
				if($total_price1 > 0) {
					$total_price1 = '￦&nbsp;'.number_format($total_price1);
				} else {
					$total_price1 = '';
				}
				if($total_price2 > 0) {
					$total_price2 = '￦&nbsp;'.number_format($total_price2);
				} else {
					$total_price2 = '';
				}

				$excel_str.= "
					<tr style='text-align: center;'>
						<td colspan='2'>합계</td>
						<td colspan='2'>".$total_principal."</td>
						<td colspan='2'>".$total_eja."</td>
						<td colspan='2'>".$total_price1."</td>
						<td colspan='2'>".$total_price2."</td>
					</tr>
				";

				$excel_str.= "
					</tbody>
				</table>
				<ul style='list-style: none; padding: 0; font-family: 바탕; font-size: 11pt;'>
					<li>◎ 사용목적 : ".$row['use_text']."</li>
					<li>◎ 담보내역 : ".$row['loan_addr']."</li>
					<li>◎ 최근 3개월 이내 10일 이상 연체 사실 : ".$is_overdue."</li>
				</ul>
				<br />
				<p width='100%' style='text-align: center; font-family: 바탕;'>위와 같이 원리금 및 부대 비용을 납입하였음을 증명합니다.</p>
				<br />
				<table border='0' width='100%' style='font-family: 바탕;'>
					<tr>
						<td colspan='10' style='font-size: 16pt; text-align: center;'>".$reg_date."</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td colspan='10' style='font-size: 22pt; text-align: center;'>".$row2['company_name']." <span>(인)</span></td>
					</tr>
				</table>
				";


			}	else if($type == '4') {  // 이자내역서
				
				$sql = "
					SELECT
						A.creditor_no, B.loan_name, B.loan_addr, B.reg_date, 
						B.loan_kinds, B.loan_sdate, B.loan_edate, B.loan_price, B.loan_eja_perc
					FROM
						cf_paper A
					LEFT JOIN
						cf_paper_t4 B ON A.p_no = B.p_no
					WHERE
						B.p_no = '$idx'
				";
				$row = sql_fetch($sql);

				if($row['creditor_no']) {
					// 채권자 정보
					$sql2 = "
						SELECT
							A.creditor_no, B.c_no, B.company_name, B.company_addr, B.company_tel
						FROM
							cf_paper A
						LEFT JOIN
							cf_paper_creditor B ON A.creditor_no = B.c_no
						WHERE
							A.creditor_no = ".$row['creditor_no']."
					";
					$row2 = sql_fetch($sql2);
				}
				
				// 등록 날짜
				$reg_date = explode('-', $row['reg_date']);
				$reg_date = $reg_date[0].'년 '.$reg_date[1].'월 '.$reg_date[2].'일';
				
				// 등록된 날짜 년, 월, 일 자르기
				$date_y = substr($row['reg_date'], 0, 4);
				$date_m = substr($row['reg_date'], 5, 2);
				$date_d = substr($row['reg_date'], 8, 2);
				
				// 등록 일자 월초, 월말 구하기
				$start_date = date("Y-m-d", mktime(0, 0, 0, $date_m , 1, $date_y));
				$end_date		= date("Y-m-d", mktime(0, 0, 0, $date_m+1 , 0, $date_y));
				$interval   = strtotime($end_date) - strtotime($start_date);
				$days				= floor($interval/86400) + 1;

				// 대출실행금액
				if($row['loan_price']) {
					$row['loan_price'] = number_format($row['loan_price']);
				} else {
					$row['loan_price'] = '0';
				}

				// 종별
				if($row['loan_kinds']=='1') {
					$loan_kinds = 'PF';
				} else if($row['loan_kinds']=='2') {
					$loan_kinds = 'ABL';
				} else if($row['loan_kinds']=='3') {
					$loan_kinds = '브릿지';
				} else if($row['loan_kinds']=='4') {
					$loan_kinds = '기타담보대출';
				}

				// 납부 계좌 및 금액
				$sql = "
					SELECT 
						 A.p_no, A.k_idx, A.creditor_no, A.reg_date, 
						 B.loan_name, B.loan_addr, 
						 C.price, C.bank_name, C.bank_acc, C.note
					FROM 
						cf_paper A
					LEFT JOIN
						cf_paper_t4 B ON A.p_no = B.p_no
					LEFT JOIN 
						cf_paper_t4_detail C ON B.idx = C.type_idx
					WHERE 
						A.p_no = '$idx'
				";
				$res = sql_query($sql);
				$cnt = $res->num_rows;

				$excel_str = "
					<h2 style='text-align: center; font-family: 바탕; font-size: 24pt;'>이 자 내 역 서</h2>
					<p style='font-family: 바탕;'>◎ 채무자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th colspan='2'>고객명</th>
								<td colspan='8'>".$row['loan_name']."</td>
							</tr>
							<tr>
								<th colspan='2'>주소</th>
								<td colspan='8'>".$row['loan_addr']."</td>
							</tr> 
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 채권자</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<tbody>
							<tr>
								<th colspan='2'>회사명</th>
								<td colspan='8'>".$row2['company_name']."</td>
							</tr>
							<tr>
								<th colspan='2'>주소</th>
								<td colspan='8'>".$row2['company_addr']."</td>
							</tr> 
							<tr>
								<th colspan='2'>연락처</th>
								<td colspan='8'>".$row2['company_tel']."</td>
							</tr>
						</tbody>
					</table>
					<p>◎ 대출정보</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
						<thead>
							<tr>
								<th colspan='2'>종별</th>
								<th colspan='2'>대출실행일</th>
								<th colspan='2'>대출만기일</th>
								<th colspan='2'>대출실행금액</th>
								<th colspan='2'>이자율</th>
							</tr>
						</thead>
						<tbody>
				";

				$excel_str.= "
							<tr style='text-align: center;'>
								<td colspan='2'>".$loan_kinds."</td>
								<td colspan='2'>".$row['loan_sdate']."</td>
								<td colspan='2'>".$row['loan_edate']."</td>
								<td colspan='2'>￦&nbsp;".$row['loan_price']."</td>
								<td colspan='2'>".$row['loan_eja_perc']."%</td>
							</tr>
				";

				$excel_str.= "
						</tbody>
					</table>
					<p style='font-family: 바탕;'>◎ 납부 계좌 및 금액</p>
					<table border='1' width='100%' style='font-family: 바탕; font-size: 11pt;'>
					<thead>
						<tr>
							<th colspan='2'>순번</th>
							<th colspan='2'>금액</th>
							<th colspan='2'>예금주</th>
							<th colspan='2'>납부계좌</th>
							<th colspan='2'>비고</th>
						</tr>
					</thead>
					<tbody>
				";
				
				for($i=0,$num=1; $i<$cnt; $i++,$num++) {
					$EX_LIST[$i] = sql_fetch_array($res);

					if($EX_LIST[$i]['price']) {
						$total_price += $EX_LIST[$i]['price'];  // 합계

						$EX_LIST[$i]['price'] = number_format($EX_LIST[$i]['price']);
					} else {
						$EX_LIST[$i]['price'] = '0';
					}
	
					$excel_str.= "
						<tr style='text-align: center;'>
							<td colspan='2'>".$num."</td>
							<td colspan='2'>￦&nbsp;".$EX_LIST[$i]['price']."</td>
							<td colspan='2'>".$EX_LIST[$i]['bank_name']."</td>
							<td colspan='2'>".$EX_LIST[$i]['bank_acc']."</td>
							<td colspan='2'>".$EX_LIST[$i]['note']."</td>
						</tr>
					";
				}

				$excel_str.= "
					<tr style='text-align: center;'>
						<td colspan='2'>합계</td>
						<td colspan='2'>￦&nbsp;".number_format($total_price)."</td>
						<td colspan='2'></td>
						<td colspan='2'></td>
						<td colspan='2'></td>
					</tr>
				";

				$excel_str.= "
					</tbody>
				</table>
				<p width='100%' style='text-align: center; font-family: 바탕;'>위 내용과 같이 이자금액 ".number_format($total_price)."원(".$start_date."~".$end_date." (".$days."일치))를<br />납부해야함을 알려드립니다.</p>
				<br />
				<table border='0' width='100%' style='font-family: 바탕; font-size: 11pt;'>
					<tr>
						<td colspan='10' style='font-size: 16pt; text-align: center;'>".$reg_date."</td>
					</tr>
					<tr><td></td></tr>
					<tr>
						<td colspan='10' style='font-size: 22pt; text-align: center;'>".$row2['company_name']." <span>(인)</span></td>
					</tr>
				</table>
				";
			
			}
		}
			
		echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'> ";
		echo $excel_str;
		
		break;
}


?>