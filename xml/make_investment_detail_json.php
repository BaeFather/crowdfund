<?php
################################################################################
## 현재 투자진행 상품 json 파일 실시간 생성
## 실행명령 : php -q /홈디렉토리/xml/make_investment_detail_json.php
## 저장경로 : /홈디렉토리/xml/json/invest_상품번호.json
## 쉘스크립트 실행경로 : /home/crowdfund/schedule_work/make_investment_json.sh
## - 위의 쉘스크립트 실행시 본 파일을 매초마다 실행함.
################################################################################

$base_path = "/home/crowdfund/public_html";

include_once($base_path.'/common.php');

// 캐싱 요청 상품 추출
$sql = "
	SELECT
		idx
	FROM
		cf_product
	WHERE 1
		AND state=''
		AND display='N'
		AND caching_start='Y'
		AND ( start_datetime <= NOW() AND end_datetime >= NOW() )
	ORDER BY
		open_datetime DESC";
if($argv[1]=='debug') echo $sql."\n";
$resx = sql_query($sql);
$rcount = sql_num_rows($resx);
if(!$rcount) { exit; }

for($i=0; $i<$rcount; $i++) {
	$r = sql_fetch_array($resx);

	if($r['idx']) {

		$prd_idx = $r['idx'];

		$sql = "
			SELECT
				a.idx, a.state, a.invest_period, a.recruit_amount, a.open_datetime, a.start_datetime, a.start_date, a.loan_start_date,
				(SELECT SUM(amount) FROM cf_product_invest WHERE a.idx = product_idx AND invest_state='Y' ) AS total_invest_amount,
				(SELECT COUNT(product_idx) AS total_invest_count FROM cf_product_invest WHERE a.idx = product_idx AND invest_state='Y') AS total_invest_count
			FROM
				cf_product a
			WHERE
				a.idx = '".$prd_idx."'";
		$PRDT = sql_fetch($sql);

		$sql2 = "
			SELECT
				COUNT(product_idx) AS total_invest_count,
				IFNULL(SUM(amount), 0) AS total_invest_amount
			FROM
				cf_product_invest
			WHERE
					product_idx='".$PRDT['idx']."'";
		if($PRDT['state']=='6') {
			$sql2.= " AND invest_state='R'";  //투자취소 상품의 경우 반환 처리된 투자금 내역을 가져온다.
		}
		else {
			$sql2.= " AND invest_state='Y'";
		}
		$tmpres = sql_fetch($sql2);
		//print_rr($tmpres);
		$PRDT['total_invest_count']  = $tmpres['total_invest_count'];
		$PRDT['total_invest_amount'] = $tmpres['total_invest_amount'];
		unset($sql2);

		if($PRDT["recruit_amount"]>0) {
			$product_invest_percent = ($PRDT["total_invest_amount"]>0) ? round((($PRDT["total_invest_amount"]/$PRDT["recruit_amount"])*100),2) : 0;
		}
		else {
			$product_invest_percent = 0;
		}

		###################################
		## 리턴 상태코드(code)
		###################################
		## A01 : 이자상환중
		## A02 : 투자상환완료 (상품마감)
		## A03 : 투자모집실패
		## A04 : 부실
		## A05 : 중도일시상환
		## B00 : 상품준비중
		## B01 : 투자대기중
		## B02 : 투자모집중
		## B03 : 투자모집완료
		## B04 : 투자모집실패
		###################################
		$PRDT_STATE = getProductStat($prd_idx);

		$invest_finished = false;
		if($PRDT_STATE['code']=='A02') {
			$invest_finished = true;
			$button_class    = 'btn_big_gray';
			$invest_button   = '<a href="javascript:;" onClick="alert(\'본 상품의 투자가 종료 되었습니다.\');" class="'.$button_class.'">투자상환완료</a>';
		}
		else if(preg_match('/(B00|B01)/', $PRDT_STATE['code'])) {
			$button_class  = 'btn_big_green';
			if($PRDT['open_datetime'] > G5_TIME_YMDHIS) {
				$msg = "투자 가능 시간이 아닙니다.";
			}
			else {
				if($PRDT['start_datetime'] > G5_TIME_YMDHIS) {
					$print_day = date("Y년 m월 d일", strtotime($PRDT['start_date']))." ".get_yoil($PRDT['start_date'])."요일";
					$print_hour = ($PRDT['start_hour']<=12) ? '오전' : '오후';
					$print_hour.= date("g", strtotime($PRDT['start_datetime']))."시"; //출력표기 시간
					$msg = $print_day." ".$print_hour." 부터 투자가 가능합니다.";
				}
			}

			if($member['invest_warning_agree']=='Y') {
				$invest_button = '<a href="javascript:;" onClick="alert(\''.$msg.'\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
			}
			else {
				$invest_button = '<a href="javascript:;" onClick="invest_warning_agree_open();"  class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';  //투자위험고지 팝업 : /popup/inc_invest_warning_agree_form.php
			}
		}
		else if($PRDT_STATE['code']=='B02') {
			$button_class  = 'btn_big_blue';
			if($member['mb_id']) {
				if($need_virtual_account) {
					$invest_button = '<a href="javascript:;" onClick="if(confirm(\'발급된 가상계좌 정보가 없습니다.\\n가상계좌 신청 페이지로 이동하시겠습니까?\')){ location.href=\'/deposit/deposit.php\'; }" class="'.$button_class.'">투자하기</a>';
				}
				else {
					if($member['invest_warning_agree']=='Y') {
						$invest_button = '<a href="/investment/detail.php?prd_idx='.$PRDT['idx'].'" class="'.$button_class.'">투자하기</a>';
					}
					else {
						$invest_button = '<a href="javascript:;" onClick="invest_warning_agree_open();"  class="'.$button_class.'">투자하기</a>';  //투자위험고지 팝업 : /popup/inc_invest_warning_agree_form.php
					}
				}
			}
			else {
				$invest_button = '<a href="/bbs/login.php?url='.urlencode($_SERVER['REQUEST_URI']).'" class="'.$button_class.'">투자하기</a>';
			}
		}
		else {
			$invest_finished = true;
			$button_class    = 'btn_big_gray';
			$invest_button   = '<a href="javascript:;" onClick="alert(\'본 상품의 투자가 종료 되었습니다.\');" class="'.$button_class.'">'.$PRDT_STATE['code_str'].'</a>';
		}
		$invest_button2 = $invest_button;  // 하단 투자하기 버튼


		// 대출실행 완료건에 대하여 이자지급 차수 표시
		if($PRDT['loan_start_date'] && $PRDT['loan_start_date']!='0000-00-00') {
			$loan_start_date_day = (int)substr($PRDT['loan_start_date'], -2);
			$total_repay_count = ($loan_start_date_day > 1) ? $PRDT['invest_period'] + 1 : $PRDT['invest_period'];  //총 지급횟수
			$PAIED = sql_fetch("SELECT MAX(turn) as max_turn FROM cf_product_success WHERE product_idx='".$PRDT['idx']."' AND invest_give_state='Y'");
			$repay_count = ($PAIED['max_turn']) ? $PAIED['max_turn'] : 0;
		}

		if( in_array($PRDT_STATE['code'], array('A01','A02','A05')) ) {
			$fcolor = ($repay_count) ? '#FF6633' : '#AAA';
			$area3_title = '지급회차';
			$area3_data = '<span style="color:'.$fcolor.'">'.$repay_count.'</span> / '.$total_repay_count;
		}
		else {
			$area3_title = '목표금액';
			$area3_data = price_cutting($PRDT['recruit_amount']).'원';
		}
		$area4_data = price_cutting($PRDT['total_invest_amount']).'원';  // 모집금액
		$progress = $product_invest_percent.'%';  // 진행률
		$progress_width = ($product_invest_percent) ? $product_invest_percent . '%' : '0.2%';

		$button_area1_data = "";
		$button_area1_data.= ($invest_finished==false) ? '<a href="/investment/simulation.php?prd_idx='.$PRDT['idx'].'" class="btn_big_link">투자시뮬레이션</a>' : '';
		$button_area1_data.= " " . $invest_button;

		$avail_invest_amount = $PRDT['recruit_amount'] - $PRDT['total_invest_amount'];
		$avail_invest_amount_k = price_cutting($avail_invest_amount).'원';

		$ARR['data'] = array(
							'version'         => date('YmdHis'),
							'invest_finished' => $invest_finished,
							'area3_title'     => $area3_title,
							'area3_data'      => $area3_data,
							'area4_data'      => $area4_data,
							'progress'        => $progress,
							'progress_width'  => $progress_width,
							'button_data1'    => $button_area1_data,
							'button_data2'    => $invest_button2,
							'avail_invest_amount' => $avail_invest_amount,
							'avail_invest_amount_k' => $avail_invest_amount_k,
							'referer'         => $_SERVER['HTTP_REFERER']
						);

		if($argv[1]=='debug') {

			print_r($ARR);

		}
		else {

			echo $ARR['data']['version']."\n";
			$file_path = $base_path."/xml/json/invest_".$prd_idx.".json";
			$json = json_encode($ARR);
			file_put_contents($file_path, $json);

		}


	}

}

sql_free_result($resx);

exit;
?>