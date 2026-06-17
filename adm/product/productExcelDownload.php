<?php
/**
 * 투자상품목록 다운로드
 * User: 김국현
 * Date: 2018-02-06
 * Time: 오후 3:19
 */

include_once("_common.php");

check_admin_token(); // ajax 이용시

// 값에 따른 변수 생성
while (list($key, $value) = each($_REQUEST)) {
    if ($_FILES) continue;
    if (!is_array($_REQUEST)) ${$key} = clean_xss_tage($value);
}

$params = array();
if (isset($searchParams) && !empty($searchParams)) {
    parse_str($searchParams, $params);
}

$where = "";
$stateYN = false;
if (count($params) > 0) {
    foreach ($params as $key => $value) {
        if (!empty($value)) {

            if(gettype($value) == "string"){
                $value = '\''.$value.'\'';
            }else if(gettype($value) == "array"){

                // state 상태
                $v = "";
                switch($key){
                    case "ST":
                        $stateYN = true;
                        $key = "state";
                        if(count($value) > 0){
                            $tmpValue = [];
                            foreach($value as $v){
                                if(gettype($v) == "string"){
                                    $tmpValue[] = '\''.$v.'\'';
                                }
                            }
                        }

                        $value = implode(',', $tmpValue);
                        break;
                }
                $where .= ' AND pr.' . $key . ' IN (' . $value . ')';
            }else{
                $where .= ' AND pr.' . $key . ' = ' . $value;
            }
        }
    }
}

$mode = (isset($mode) && !empty($mode)) ? $mode : 2;

$todayData = date("Y_m_d");

$excelStartRowIndex = 10; // 상품목록이 들어갈 엑셀 행 번호
$excelLastColIndex = 14; // 상품목록이 들어갈 엑셀 열 번호

$productList = array();
$totalRecruitAmount = 0; // 대출금
$totalAmount = 0; // 투자금액
$totalInvestAmount = 0; // 상환액
$totalInvestAmountPer = 0; // 상환 퍼센트
$totalLeftAmount = 0; // 정상
$totalLeftAmountPer = 0; // 정상 퍼센트
$totalUsefeeAmount = 0; // 플랫폼 이용료 합산
$totalCommissionAmount = 0; // 중계수수료 합산

$sql = "
	SELECT
		pr.idx, pr.title, pr.recruit_amount,  pr.loan_interest_rate, pr.loan_interest_type, pr.invest_usefee_type, IFNULL(pr.loan_usefee, null) AS loan_usefee,
		pr.loan_start_date, pr.loan_end_date, pr.invest_period, pr.invest_days, pr.state,
		pr.open_datetime, pr.end_datetime, pr.invest_end_date, pr.start_datetime,
		prc.broker, prc.receiver, IFNULL(prc.commission_fee, 0) AS commission_fee,
		IFNULL (pri.amount, 0) AS amount,
		IFNULL (prg.principal, 0) AS principal
	FROM
		cf_product pr
	LEFT JOIN
		cf_product_container prc  ON pr.idx=prc.product_idx
	LEFT JOIN
		(SELECT product_idx AS idx, SUM(amount) AS amount FROM cf_product_invest WHERE invest_state = 'Y' GROUP BY product_idx) pri ON (pr.idx = pri.idx)
	LEFT JOIN
		(SELECT product_idx AS idx, SUM(principal) AS principal FROM cf_product_give GROUP BY product_idx) prg ON (pr.idx = prg.idx)
	WHERE 1
		AND display = 'Y'";

if(!$stateYN) $sql.= " AND state IN (1, 2, 5) ";
$sql.= $where;
$sql.= "
	ORDER BY
		pr.start_num DESC
--	,pr.loan_start_date ASC
--	,pr.open_datetime ASC
--	,pr.idx ASC
";
$query = sql_query($sql);
$totalCount = $query->num_rows;

if (!$totalCount) {
	exit(json_encode(array("error" => 1, "message" => "상품정보가 존재하지 않습니다.")));
}

$nLoop = 1;
$list = array();
while ($result = sql_fetch_array($query))
{
    // 순번
    $list["num"] = $nLoop;

    // 상품명
    $list["title"] = html_clean(stripslashes($result["title"]));

    // 대출금
    $recruit_amount = (preg_replace('/[^0-9]/', '', $result["recruit_amount"]) + 0);
    $totalRecruitAmount += ($recruit_amount + 0);
    $list["recruit_amount"] = ($recruit_amount);

    // 이자율
    $list["loan_interest_rate"] = floatRtrim($result["loan_interest_rate"]) . '%';

    // 이자징수방식
    $list["loan_interest_type"] = (!$result["loan_interest_type"]) ? "후취" : ($result["loan_interest_type"] ? "선취" : "부분선취");

    // 플랫폼 이용료 (구분, %, 금액)
    $list["invest_usefee_type"] = ($result["invest_usefee_type"] == 'A') ? "선취" : ($result["invest_usefee_type"] == 'B' ? "후취" : '');

    $list["loan_usefee"] = ($result["loan_usefee"] + 0);

    // 플랫폼 이용료 금액
    if ($result["loan_usefee"] > 0) {
        $list["loan_usefee_amount"] = (($result["loan_usefee"] * $recruit_amount) / 100);
        $totalUsefeeAmount += ($list["loan_usefee_amount"]);
        $list["loan_usefee_amount"] = ($list["loan_usefee_amount"]);
    } else {
        $list["loan_usefee_amount"] = '0';
        $totalUsefeeAmount += 0; // 플랫폼이용료 합산
    }

    // 대출 실행일
    $list["loan_start_date"] = ($result["loan_start_date"] && $result["loan_start_date"] != "0000-00-00") ? date("Y.m.d", strtotime($result["loan_start_date"])) : '';

    // 상환일(종료일)
    $list["loan_end_date"] = ($result["loan_end_date"] && $result["loan_end_date"] != "0000-00-00") ? date("Y.m.d", strtotime($result["loan_end_date"])) : '';

    // 개월(투자기간)
    $list["invest_period"] = ($result["invest_days"] > 0) ? $result["invest_days"]."일" : $result["invest_period"]."개월";

    // 상태
    $list["state"] = getProductState($result["state"], $result["open_datetime"], $result["invest_end_date"], $result["start_datetime"], $result["end_datetime"], $recruit_amount, $result["amount"]);
		if(preg_match("/(중도상환|정상상환)/i", $list["state"])) {
			$list["state"] = preg_replace("/(중도상환|정상상환)/i", "상환", $list["state"]);
		}
		else if(preg_match("/이자상환중/i", $list["state"])) {
			$list["state"] = preg_replace("/이자상환중/i", "상환중", $list["state"]);
		}

		if (!$list["state"]) $list["state"] = '-';

    /*echo
         "open_datetime: ".$result["open_datetime"]."<br/>",
         "invest_end_date: ".$result["invest_end_date"]."<br/>".
         "start_datetime: ".$result["start_datetime"]."<br/>",
         "end_datetime: ".$result["end_datetime"]."<br/>".
                            $recruit_amount, $result["amount"];*/
    // echo "<pre>"; print_r($list); exit;

    // 중개자
    $list["broker"] = html_clean(stripslashes($result["broker"]));

    // 중계수수료
    $list["commission_fee"] = ($result["commission_fee"]) ? round($result["commission_fee"], 2) . '%' : '';

    // 중계수수료금액
    if ((int)$result["commission_fee"] > 0) {
        $list["commission_fee_amount"] = (($recruit_amount * $result["commission_fee"]) / 100);
        $totalCommissionAmount += ($list["commission_fee_amount"]);
        $list["commission_fee_amount"] = ($list["commission_fee_amount"]);
    } else {
        $list["commission_fee_amount"] = '';
    }

    // 접수자
    $list["receiver"] = $result["receiver"];

    /*---------------------------------------------------------------------------------------------------------------*/
    /*----노출 상단---------------------------------------------------------------------------------------------------*/
    /*---------------------------------------------------------------------------------------------------------------*/

    // 상환금액
    $totalInvestAmount += ($result["principal"] + 0);

    // 투자모집금액
    $totalAmount += ($result["amount"] + 0);

    // 결과 저장
    if($mode == 1){
        array_push($productList, $list);
    }else{
        array_push($productList, array_values($list));
    }
    $nLoop++;
}

// 정상(대출잔액)
$totalLeftAmount = $totalRecruitAmount - $totalInvestAmount;

// 상환율
$totalInvestAmountPer = sprintf('%.2f', ($totalInvestAmount / $totalRecruitAmount * 100));

// 대출잔액율
$totalLeftAmountPer = sprintf('%.2f', (100 - $totalInvestAmountPer));

//echo "상환율: ".$totalInvestAmountPer."<br/>";
//echo "정상(대출잔액)율".$totalLeftAmountPer."<br/>";
//echo "상환금액: ".$totalInvestAmount."<br/>";
//echo "정상(대출잔액): ".$totalLeftAmount."<br/>";
//echo "투자모집금액: ".$totalAmount."<br/>";

if (count($productList) > 0) {
    include_once(G5_LIB_PATH . "/PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php");
    $excelFilePath = G5_LIB_PATH . "/Excel/productList.xlsx";
    $excelFileUrl = G5_URL . "/lib/Excel/productList_{$todayData}.xlsx";

    try {

        $objPHPExcel = new PHPExcel();
        $inputFileType = PHPExcel_IOFactory::identify($excelFilePath);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);

        $objPHPExcel = $objReader->load($excelFilePath);

        // $objWorkSheet = $objPHPExcel->createSheet(0);

        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet(); // 활성시트
        // $objWorkSheet->setTitle('productList');

        $highestRow = $sheet->getHighestDataRow(); // 행개수
        $highestColumn = $sheet->getHighestDataColumn();

        // 기존 데이터 삭제
        if ($sheet != null)
        {
            $sheet->setCellValue('H2', ''); // 상환, 정상, 누적액 삭제
            $sheet->setCellValue('H3', '');
            $sheet->setCellValue('H5', '');
            $sheet->setCellValue('J2', '');
            $sheet->setCellValue('J3', '');


            // 대출금, 플랫폼수수료, 중계수수료 합계 입력
            $sheet->setCellValue('C9', ($totalRecruitAmount));
            $sheet->setCellValue('H9', ($totalUsefeeAmount));
            $sheet->setCellValue('O9', ($totalCommissionAmount));

            // 상품목록 삭제
            for ($col = 0; $col <= $excelLastColIndex; $col++) {
                for ($row = $excelStartRowIndex; $row <= $highestRow; $row++) {
                    $sheet->setCellValueByColumnAndRow($col, $row, null);
                }
            }
            $sheet->removeRow(($excelStartRowIndex + 1), $highestRow);
        }

        // 투자상품목록 개수만큼 행 추가
        $sheet->insertNewRowBefore(11, count($productList));

        // 상품목록 값 입력
        $sheet->setCellValue('H2', number_format($totalInvestAmount)); // 상환, 정상, 누적액 입력
        $sheet->setCellValue('H3', number_format($totalLeftAmount));
        $sheet->setCellValue('H5', number_format($totalRecruitAmount));
        $sheet->setCellValue('J2', sprintf("%.2f", $totalInvestAmountPer) . '%');
        $sheet->setCellValue('J3', sprintf("%.2f", $totalLeftAmountPer) . '%');


        /**
         * Array
         * (
         * [title] => [제58호] 일산 대화동 다세대 주택 건축자금 3차
         * [recruit_amount] => 1,600,000,000
         * [loan_interest_rate] => 18.00%
         * [loan_interest_type] => 후취
         * [invest_usefee_type] => 선취
         * [loan_usefee] => 0
         * [loan_usefee_amount] => 0
         * [loan_start_date] => 2017.12.22
         * [loan_end_date] => 2018.08.22
         * [invest_period] => 8
         * [state] => 이자상환중
         * [receiver] =>
         * [commission_fee] =>
         * [commission_fee_amount] =>
         * )
         */

        if($mode == 1) {
            $html = "";
            $html .= '<table style="table-layout: fixed; width: 1500px;" border="1">
                            <colgroup>
                            <col style="width: 400px"/>
                            <col style="width: 100px"/>
                            <col style="width: 64px"/>
                            <col style="width: 150px"/>
                            <col style="width: 70px"/>
                            <col style="width: 70px"/>
                            <col style="width: 150px"/>
                            <col style="width: 100px"/>
                            <col style="width: 100px"/>
                            <col style="width: 57px"/>
                            <col style="width: 80px"/>
                            <col style="width: 100px"/>
                            <col style="width: 40px"/>
                            <col style="width: 150px"/>
                            <col style="width: 100px"/>
                            </colgroup>
                              <tr>
                                <td colspan="2" rowspan="4">헬로펀딩<br></td>
                                <td align="center">상환</td>
                                <td align="right">' . number_format($totalInvestAmount) . '</td>
                                <td align="right">' . (float)$totalInvestAmountPer . '%</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <<td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td align="center">정상</td>
                                <td align="right">' . number_format($totalLeftAmount) . '</td>
                                <td align="right">' . (float)$totalLeftAmountPer . '%</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td align="center">취소<br></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td align="center">누적액</td>
                                <td align="right">' . number_format($totalRecruitAmount) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                              </tr>
                              <tr>
                                <td rowspan="2" align="center">상품명<br></td>
                                <td rowspan="2" align="center">대출금</td>
                                <td rowspan="2" align="center">이자율</td>
                                <td rowspan="2" align="center">이자징수방식</td>
                                <td colspan="3" align="center">플랫폼 수수료<br></td>
                                <td rowspan="2" align="center">대출실행일<br></td>
                                <td rowspan="2" align="center">상환일</td>
                                <td rowspan="2" align="center">개월<br></td>
                                <td rowspan="2" align="center">상태</td>
                                <td colspan="3" align="center">중개수수료<br></td>
                                <td rowspan="2" align="center">접수자<br></td>
                              </tr>
                              <tr>
                                <td align="center">구분<br></td>
                                <td align="center">%<br></td>
                                <td align="center">금액<br></td>
                                <td align="center">중개자</td>
                                <td align="center">%</td>
                                <td align="center">금액</td>
                              </tr>
                              <tr>
                                <td></td>
                                <td>' . number_format($totalRecruitAmount) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>' . number_format($totalUsefeeAmount) . '</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>' . number_format($totalCommissionAmount) . '</td>
                                <td></td>
                              </tr>
                              ';
            foreach ($productList as $row => $product) {
                $html .= '<tr>
                                <td align="left">&nbsp;&nbsp;' . $product["title"] . '</td>
                                <td align="right">' . $product["recruit_amount"] . '</td>
                                <td align="center">' . $product["loan_interest_rate"] . '</td>
                                <td align="center">' . $product["loan_interest_type"] . '</td>
                                <td align="center">' . $product["invest_usefee_type"] . '</td>
                                <td align="center">' . $product["loan_usefee"] . '</td>
                                <td align="right">' . $product["loan_usefee_amount"] . '</td>
                                <td align="center">' . $product["loan_start_date"] . '</td>
                                <td align="center">' . $product["loan_end_date"] . '</td>
                                <td align="center">' . $product["invest_period"] . '</td>
                                <td align="center">' . $product["state"] . '</td>
                                <td align="center">' . $product["broker"] . '</td>
                                <td align="center">' . $product["commission_fee"] . '</td>
                                <td align="right">' . $product["commission_fee_amount"] . '</td>
                                <td align="center">' . $product["receiver"] . '</td>
                              </tr>';
            }
            $html .= '</table>';

            // 출력
            ob_end_clean();
            header("Content-type: application/vnd.ms-excel;");
            header('Content-Disposition: attachment;filename="productList.xls"');
            header('Cache-Control: max-age=0');

            debug_flush($html);

        }else {

            // 엑셀 플러그인 사용
            foreach ($productList as $row => $product) {
                foreach ($product as $col => $value) {
                    $sheet->setCellValueByColumnAndRow($col, ($row + $excelStartRowIndex), $value);
                }
            }

            // 저장
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $inputFileType);

            // 출력
            // ob_end_clean();
            /*header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="productList.xls"');
            header('Cache-Control: max-age=0');*/
            // $objWriter->save('php://output');
            $objWriter->save(G5_LIB_PATH . "/Excel/productList_{$todayData}.xlsx");
        }

        // 연결종료
        $objPHPExcel->disconnectWorksheets();
        $objPHPExcel->garbageCollect();
        unset($objPHPExcel);
        unset($productList);

        if($mode == 1){

        }else{
             echo json_encode(array("success" => 1, "excelFileUrl" => $excelFileUrl));
        }

    }
        catch (Exception $e)
    {
        die('Error !! "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
        echo json_encode(array("error" => 1, "meesage" => "다운로드 받을 수 없습니다. 관리자에게 문의하세요."));
    }
}

sql_close();
exit;