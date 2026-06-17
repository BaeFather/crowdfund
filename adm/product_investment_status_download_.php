<?

set_time_limit(300);

include_once('./_common.php');
include_once(G5_LIB_PATH."/PHPExcel_1.8.0/Classes/PHPExcel.php");

foreach($_GET as $k=>$v) { ${$_GET[$k]} = trim($v); }

$prd_idx = $_REQUEST['idx'];

$PRDT = sql_fetch("SELECT start_num, title, recruit_amount, invest_return, recruit_period_start, loan_start_date, loan_end_date, start_datetime FROM cf_product WHERE idx='".$prd_idx."'");

// 투자소요시간 측정
$LAST_INVEST = sql_fetch("SELECT insert_date, insert_time FROM cf_product_invest WHERE product_idx='".$prd_idx."' AND invest_state='Y' ORDER BY idx DESC LIMIT 1");
$last_invest_datetime = $LAST_INVEST['insert_date']." ".$LAST_INVEST['insert_time'];
$interval = getDateInterval($PRDT['start_datetime'], $last_invest_datetime);

$sql = "
	SELECT
		B.mb_id, B.mb_name, B.mb_co_name, B.member_type, B.member_investor_type,
		A.member_idx, A.amount, A.is_advance_invest, A.syndi_id AS flatform_id,
		(SELECT COUNT(idx) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_count,
		(SELECT SUM(amount) FROM cf_product_invest WHERE member_idx=A.member_idx AND invest_state='Y') AS total_invest_amount
	FROM
		cf_product_invest A
	LEFT JOIN
		g5_member B
	ON
		A.member_idx = B.mb_no
	WHERE (1)
		AND A.product_idx='".$prd_idx."'
		AND A.invest_state='Y'
		$where_plus
	ORDER BY
		A.amount DESC";
//echo $sql;
$res  = sql_query($sql);
$rows = sql_num_rows($res);

$TOTAL = array(
					'COUNT'      => 0,
					'AMOUNT'     => 0,
					'M1_COUNT'   => 0,
					'M1_AMOUNT'  => 0,
					'M11_COUNT'  => 0,
					'M11_AMOUNT' => 0,
					'M12_COUNT'  => 0,
					'M12_AMOUNT' => 0,
					'M13_COUNT'  => 0,
					'M13_AMOUNT' => 0,
					'M2_COUNT'   => 0,
					'M2_AMOUNT'  => 0,
					'M3_COUNT'   => 0,
					'M3_AMOUNT'  => 0,
					'M32_COUNT'   => 0,
					'M32_AMOUNT'  => 0,
					'M33_COUNT'   => 0,
					'M33_AMOUNT'  => 0,
				);

$TOTAL_A = array(
						'COUNT'      => 0,
						'AMOUNT'     => 0,
						'M1_COUNT'   => 0,
						'M1_AMOUNT'  => 0,
						'M11_COUNT'  => 0,
						'M11_AMOUNT' => 0,
						'M12_COUNT'  => 0,
						'M12_AMOUNT' => 0,
						'M13_COUNT'  => 0,
						'M13_AMOUNT' => 0,
						'M2_COUNT'   => 0,
						'M2_AMOUNT'  => 0,
						'M3_COUNT'   => 0,
						'M3_AMOUNT'  => 0,
						'M32_COUNT'   => 0,
						'M32_AMOUNT'  => 0,
						'M33_COUNT'   => 0,
						'M33_AMOUNT'  => 0,
					);

$TOTAL_B = array(
						'COUNT'      => 0,
						'AMOUNT'     => 0,
						'M1_COUNT'   => 0,
						'M1_AMOUNT'  => 0,
						'M11_COUNT'  => 0,
						'M11_AMOUNT' => 0,
						'M12_COUNT'  => 0,
						'M12_AMOUNT' => 0,
						'M13_COUNT'  => 0,
						'M13_AMOUNT' => 0,
						'M2_COUNT'   => 0,
						'M2_AMOUNT'  => 0,
						'M3_COUNT'   => 0,
						'M3_AMOUNT'  => 0,
						'M32_COUNT'   => 0,
						'M32_AMOUNT'  => 0,
						'M33_COUNT'   => 0,
						'M33_AMOUNT'  => 0,
					);


for($i=0; $i<$rows; $i++) {
	$LIST[$i] = sql_fetch_array($res);

	////////////////////////////////////
	// 전체 현황
	////////////////////////////////////
	$TOTAL['COUNT'] += 1;
	$TOTAL['AMOUNT'] += $LIST[$i]['amount'];

	if($LIST[$i]['member_type']=='2') {
		$TOTAL['M2_COUNT'] += 1;
		$TOTAL['M2_AMOUNT'] += $LIST[$i]['amount'];
	}
	else {
		$TOTAL['M1_COUNT'] += 1;
		$TOTAL['M1_AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_investor_type']=='2') {
			$TOTAL['M12_COUNT'] += 1;
			$TOTAL['M12_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['member_investor_type']=='3') {
			$TOTAL['M13_COUNT'] += 1;
			$TOTAL['M13_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL['M11_COUNT'] += 1;
			$TOTAL['M11_AMOUNT'] += $LIST[$i]['amount'];
		}
	}

	if($LIST[$i]['flatform_id']=='finnq') {
		$TOTAL['M3_COUNT'] += 1;
		$TOTAL['M3_AMOUNT'] += $LIST[$i]['amount'];
	}
	else if($LIST[$i]['flatform_id']=='hktvwowstar') {
		$TOTAL['M32_COUNT'] += 1;
		$TOTAL['M32_AMOUNT'] += $LIST[$i]['amount'];
	}
	else if($LIST[$i]['flatform_id']=='chosun') {
		$TOTAL['M33_COUNT'] += 1;
		$TOTAL['M33_AMOUNT'] += $LIST[$i]['amount'];
	}

	////////////////////////////////////
	// 최초 투자자 현황 데이터
	////////////////////////////////////
	if($LIST[$i]['total_invest_count']==1) {

		$TOTAL_A['COUNT'] += 1;
		$TOTAL_A['AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_type']=='2') {
			$TOTAL_A['M2_COUNT'] += 1;
			$TOTAL_A['M2_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL_A['M1_COUNT'] += 1;
			$TOTAL_A['M1_AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_investor_type']=='2') {
				$TOTAL_A['M12_COUNT'] += 1;
				$TOTAL_A['M12_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['member_investor_type']=='3') {
				$TOTAL_A['M13_COUNT'] += 1;
				$TOTAL_A['M13_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_A['M11_COUNT'] += 1;
				$TOTAL_A['M11_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

		if($LIST[$i]['flatform_id']=='finnq') {
			$TOTAL_A['M3_COUNT'] += 1;
			$TOTAL_A['M3_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='hktvwowstar') {
			$TOTAL_A['M32_COUNT'] += 1;
			$TOTAL_A['M32_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='chosun') {
			$TOTAL_A['M33_COUNT'] += 1;
			$TOTAL_A['M33_AMOUNT'] += $LIST[$i]['amount'];
		}

	}

	////////////////////////////////////
	// 기존 투자자 현황 데이터
	////////////////////////////////////
	else {

		$TOTAL_B['COUNT'] += 1;
		$TOTAL_B['AMOUNT'] += $LIST[$i]['amount'];

		if($LIST[$i]['member_type']=='2') {
			$TOTAL_B['M2_COUNT'] += 1;
			$TOTAL_B['M2_AMOUNT'] += $LIST[$i]['amount'];
		}
		else {
			$TOTAL_B['M1_COUNT'] += 1;
			$TOTAL_B['M1_AMOUNT'] += $LIST[$i]['amount'];

			if($LIST[$i]['member_investor_type']=='2') {
				$TOTAL_B['M12_COUNT'] += 1;
				$TOTAL_B['M12_AMOUNT'] += $LIST[$i]['amount'];
			}
			else if($LIST[$i]['member_investor_type']=='3') {
				$TOTAL_B['M13_COUNT'] += 1;
				$TOTAL_B['M13_AMOUNT'] += $LIST[$i]['amount'];
			}
			else {
				$TOTAL_B['M11_COUNT'] += 1;
				$TOTAL_B['M11_AMOUNT'] += $LIST[$i]['amount'];
			}
		}

		if($LIST[$i]['flatform_id']=='finnq') {
			$TOTAL_B['M3_COUNT'] += 1;
			$TOTAL_B['M3_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='hktvwowstar') {
			$TOTAL_B['M32_COUNT'] += 1;
			$TOTAL_B['M32_AMOUNT'] += $LIST[$i]['amount'];
		}
		else if($LIST[$i]['flatform_id']=='chosun') {
			$TOTAL_B['M33_COUNT'] += 1;
			$TOTAL_B['M33_AMOUNT'] += $LIST[$i]['amount'];
		}

	}

}


$title0 = "헬로펀딩 제{$PRDT['start_num']}호 상품 투자 요약보고";

$objPHPExcel = new PHPExcel();

// Excel문서 속성 지정
$objPHPExcel->getProperties()->setCreator("헬로펀딩 정산시스템")
                             ->setLastModifiedBy("헬로펀딩 정산시스템")
                             ->setTitle($title0)
                             ->setSubject($title0)
                             ->setDescription($title0)
                             ->setKeywords($title0)
                             ->setCategory("(주)헬로핀테크");


// ▼ 제목줄 -------------------------------------------------------------------
// 셀병합(Col) 및 제목삽입
$objPHPExcel->setActiveSheetIndex(0)->mergeCells("B2:F2")->setCellValue("B2", $title0);
//가운데 정렬
$objPHPExcel->getActiveSheet()->getStyle("B2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

//보더
$objPHPExcel->getActiveSheet()->getStyle("B2:F2")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
// ▲ 제목줄 -------------------------------------------------------------------


// ▼ Chapter1 -----------------------------------------------------------------
$objPHPExcel->setActiveSheetIndex()->mergeCells("B4:F4")->setCellValue("B4", $PRDT['title']);

//배경색 및 글자색 변경
$objPHPExcel->getActiveSheet()->getStyle("B4:F4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FF808080");
$objPHPExcel->getActiveSheet()->getStyle("B4:F4")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');

// Excel 파일의 각 셀의 타이틀을 정해준다.
$objPHPExcel->setActiveSheetIndex()->mergeCells("B5:D5")->mergeCells("E5:F5")->setCellValue("B5", "모집금액")->setCellValue("E5", price_cutting($PRDT['recruit_amount'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B6:D6")->mergeCells("E6:F6")->setCellValue("B6", "투자소요시간")->setCellValue("E6", $interval);
$objPHPExcel->setActiveSheetIndex()->mergeCells("B7:D7")->setCellValue("B7", "전체투자현황")->setCellValue("E7", number_format($TOTAL['COUNT'])."건")->setCellValue("F7", price_cutting($TOTAL['AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B8:D8")->setCellValue("B8", "법인투자")->setCellValue("E8", number_format($TOTAL['M2_COUNT'])."건")->setCellValue("F8", price_cutting($TOTAL['M2_AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B9:D9")->setCellValue("B9", "개인투자")->setCellValue("E9", number_format($TOTAL['M1_COUNT'])."건")->setCellValue("F9", price_cutting($TOTAL['M1_AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B10:D10")->setCellValue("B10", "최초투자자")->setCellValue("E10", number_format($TOTAL_A['COUNT'])."건")->setCellValue("F10", price_cutting($TOTAL_A['AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B11:D11")->setCellValue("B11", "기존투자자")->setCellValue("E11", number_format($TOTAL_B['COUNT'])."건")->setCellValue("F11", price_cutting($TOTAL_B['AMOUNT'])."원");



$objPHPExcel->getActiveSheet()->getStyle("B5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

$objPHPExcel->getActiveSheet()->getStyle("E5")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
						->getActiveSheet()->getStyle("E6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
						->getActiveSheet()->getStyle("E7:F7")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
						->getActiveSheet()->getStyle("E8:F8")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
						->getActiveSheet()->getStyle("E9:F9")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
						->getActiveSheet()->getStyle("E10:F10")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
						->getActiveSheet()->getStyle("E11:F11")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle("B5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B8")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B9")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1")
						->getActiveSheet()->getStyle("B11")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");

$objPHPExcel->getActiveSheet()->getStyle("E5:F5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFF6F6F6")
						->getActiveSheet()->getStyle("E6:F6")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFF6F6F6");

$objPHPExcel->getActiveSheet()->getStyle("B4:F11")->getFont()->setName("맑은 고딕");
$objPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->getStyle("B5:F11")->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle("B5:F11")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// ▲ Chapter1 -----------------------------------------------------------------


// ▼ Chapter2 -----------------------------------------------------------------
$objPHPExcel->setActiveSheetIndex()
						->mergeCells("B13:F13")->setCellValue("B13", "신디케이션 업체별 투자 발생내역")
						->mergeCells("B14:D14")->setCellValue("B14", "신디케이션 업체")
						->setCellValue("E14", "투자건수")
						->setCellValue("F14", "투자금액");

$objPHPExcel->setActiveSheetIndex()->mergeCells("B15:D15")->setCellValue("B15", "핀크")->setCellValue("E15", number_format($TOTAL['M3_COUNT'])."건")->setCellValue("F15", price_cutting($TOTAL['M3_AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B16:D16")->setCellValue("B16", "와우스타(한경TV)")->setCellValue("E16", number_format($TOTAL['M32_COUNT'])."건")->setCellValue("F16", price_cutting($TOTAL['M32_AMOUNT'])."원");
$objPHPExcel->setActiveSheetIndex()->mergeCells("B17:D17")->setCellValue("B17", "땅집고(조선일보)")->setCellValue("E17", number_format($TOTAL['M33_COUNT'])."건")->setCellValue("F17", price_cutting($TOTAL['M33_AMOUNT'])."원");

$objPHPExcel->getActiveSheet()->getStyle("B14:F14")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B16")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("B17")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
            ->getActiveSheet()->getStyle("E15:F15")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->getActiveSheet()->getStyle("E16:F16")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT)
            ->getActiveSheet()->getStyle("E17:F17")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle("B13:F17")->getFont()->setName("맑은 고딕");
$objPHPExcel->getActiveSheet()->getStyle("B13")->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->getStyle("B14:F17")->getFont()->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle("B14:F17")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$objPHPExcel->getActiveSheet()->getStyle("B14:F14")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");
// ▲ Chapter2 -----------------------------------------------------------------


// ▼ Chapter3 -----------------------------------------------------------------
$objPHPExcel->setActiveSheetIndex()->mergeCells("B19:F19")->setCellValue("B19", "투자 상세내역");

$objPHPExcel->setActiveSheetIndex()
						->setCellValue("B20", "NO")
						->setCellValue("C20", "업체명/성명")
						->setCellValue("D20", "투자금액")
						->setCellValue("E20", "누적투자수")
						->setCellValue("F20", "투적투자금액")
						->getStyle("B20:F20")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle("B20:F20")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFDCE6F1");

for($i=0,$j=1,$lnum=21; $i<$rows; $i++,$j++,$lnum++) {

	$name = ($LIST[$i]['member_type']=='2') ? $LIST[$i]['mb_co_name'] : $LIST[$i]['mb_name'];

	$objPHPExcel->setActiveSheetIndex()
							->setCellValue("B{$lnum}", $j)
							->setCellValue("C{$lnum}", $name)
							->setCellValue("D{$lnum}", price_cutting($LIST[$i]['amount'])."원")
							->setCellValue("E{$lnum}", number_format($LIST[$i]['total_invest_count'])."건")
							->setCellValue("F{$lnum}", price_cutting($LIST[$i]['total_invest_amount'])."원");

}

$lnum -= 1;

$objPHPExcel->getActiveSheet()->getStyle("B21:C{$lnum}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
						->getActiveSheet()->getStyle("D21:F{$lnum}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

$objPHPExcel->getActiveSheet()->getStyle("B19")->getFont()->setSize(11);
$objPHPExcel->getActiveSheet()->getStyle("B20:F{$lnum}")->getFont()->setName("맑은 고딕")->setSize(10);
$objPHPExcel->getActiveSheet()->getStyle("B20:F{$lnum}")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);

//echo $lnum;

// ▲ Chapter3 -----------------------------------------------------------------


//셀너비
$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(2.25);
$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(6.88);
$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(16.43);
$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(13.13);
$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(11.88);
$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(14.38);

$objPHPExcel->getActiveSheet()->getStyle("B4:F11")->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);


$objPHPExcel->getActiveSheet()->getStyle("B2:F2")->getFont()->setName("맑은 고딕")->setSize(16);
$objPHPExcel->getActiveSheet()->getStyle("B2:F2")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


// 활성 시트 색인을 첫 번째 시트로 설정하면 Excel이 이를 첫 번째 시트로 엽니다.
$objPHPExcel->setActiveSheetIndex(0);


$file_subject = $title0;
// 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
$filename = iconv("UTF-8", "EUC-KR", $file_subject);

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

exit;

?>