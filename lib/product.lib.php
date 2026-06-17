<?

// 인기상품, 최신상품 추출
// $cache_time 캐시 갱신시간(sec)
function getProductList($type, $limit=0, $cache_time=60)
{
	global $g5;

	$type = (!$type) ? 1 : $type; // (1: 일반 활성상품, 2: 인기상품, 3: 최신상품)

	if( !in_array($type, array(1, 2, 3)) ) {
		return false;
	}

	switch($type) {
		case '1' : $fileType = "active";  break;
		case '2' : $fileType = "popular"; break;
		case '3' : $fileType = "latest";  break;
	}

	$result = [];
	$cache_fwrite = false;

	if( G5_USE_CACHE && preg_match('/(www\.hellofunding\.co|www1\.hellofunding\.co|www2\.hellofunding\.co|www-stg\.hellofunding\.co|dev\.hellofunding\.co|dev2\.hellofunding\.co)/i', G5_URL) ) {		// 헬로펀딩도메인 접속시에만 캐싱파일 생성
		$cache_file = G5_DATA_PATH."/cache/productList-{$fileType}.php";

		if(!file_exists($cache_file)) {
			$cache_fwrite = true;
		}
		else {
			if($cache_time > 0) {
				$filetime = filemtime($cache_file);
				$file_delete_time = $filetime + $cache_time;

				if($filetime && ($file_delete_time <= G5_SERVER_TIME)) {
					//@unlink($cache_file);
					$cache_fwrite = true;
				}
			}

			if(!$cache_fwrite)
				require($cache_file);
		}
	}

	if(!G5_USE_CACHE || $cache_fwrite)
	{
		$activeProductList  = [];
		$eventProductList   = [];
		$popularProductList = [];
		$latestProductList  = [];

		$first_url = G5_URL;
	//$first_url = (preg_match("/crowdfund_dev2/", $_SERVER['DOCUMENT_ROOT'])) ? G5_URL : "https://www.hellofunding.co.kr";

		$where = "";
		$where.= " AND A.display='Y' AND A.isTest='' AND A.only_vip='' ";

		switch($type) {

			default :

			case '1' :

				// 활성 일반 상품추출 ( 모집중(모집율 오름차순) > 투자대기중 > 모집완료(기표전) )
				// ★★★★ 이 조건 이랬다저랬다 백만번 바꿨음. 한번 더 바꿔달라고 하면 [ https://www.youtube.com/watch?v=3jLGmI_BGko ] <== 이거 보여주기!! ★★★★

				// 모집중(모집율 오름차순)
				$sqlA0 = "
					SELECT
						A.idx, A.ai_grp_idx, A.mortgage_guarantees, A.category, A.state, A.title, A.main_image, A.main_image_m,
						A.invest_return, A.invest_period, A.invest_days,
						A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime, A.end_date, A.loan_start_date, A.loan_end_date,
						A.purchase_guarantees, A.advanced_payment,
						A.open_datetime, A.stream_url1, A.stream_url2,
						A.advance_invest, A.advance_invest_ratio,
						A.live_invest_amount AS total_invest_amount
					FROM
						cf_product A
					WHERE 1=1
						$where
						AND A.state='' AND A.invest_end_date='' AND A.start_datetime <= NOW()
					ORDER BY
						ROUND((total_invest_amount/A.recruit_amount)*100) ASC,
						A.start_num DESC,
						A.open_datetime DESC,
						A.idx DESC";
				$res = sql_query($sqlA0);
				while( $row = sql_fetch_array($res) ) {
					$NEW_LIST[] = $row;
				}

				// 투자대기중
				$sqlA2 = "
					SELECT
						A.idx, A.ai_grp_idx, A.mortgage_guarantees, A.category, A.state, A.title, A.main_image, A.main_image_m,
						A.invest_return, A.invest_period, A.invest_days,
						A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime, A.end_date, A.loan_start_date, A.loan_end_date,
						A.purchase_guarantees, A.advanced_payment,
						A.advance_invest, A.advance_invest_ratio,
						A.open_datetime, A.stream_url1, A.stream_url2
					FROM
						cf_product A
					WHERE 1=1
						$where
						AND A.state='' AND A.invest_end_date='' AND A.start_datetime >= NOW() AND open_datetime <= NOW()
					ORDER BY
						A.start_datetime ASC,
						A.start_num ASC,
						A.open_datetime ASC,
						A.idx DESC";
				$res = sql_query($sqlA2);
				while( $row = sql_fetch_array($res) ) {
					$NEW_LIST[] = $row;
				}

				// 모집완료(기표전)
				/*
				$sqlA1 = "
					SELECT
						A.idx, A.ai_grp_idx, A.mortgage_guarantees, A.category, A.state, A.title, A.main_image, A.main_image_m,
						A.invest_return, A.invest_period, A.invest_days,
						A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime, A.end_date, A.loan_start_date, A.loan_end_date,
						A.purchase_guarantees, A.advanced_payment,
						A.advance_invest, A.advance_invest_ratio,
						A.open_datetime, A.stream_url1, A.stream_url2
					FROM
						cf_product A
					WHERE 1=1
						$where
						AND A.state='' AND A.invest_end_date!=''
					ORDER BY
						A.start_num ASC,
						A.open_datetime ASC,
						A.idx DESC";
				$res = sql_query($sqlA1);
				while( $row = sql_fetch_array($res) ) {
					$NEW_LIST[] = $row;
				}
				*/

				$totalCount = count($NEW_LIST);
				if($totalCount > 0) {
					for($i=0; $i<$totalCount; $i++) {

						$titleAndSubject = ($NEW_LIST[$i]['title']) ? extractText($NEW_LIST[$i]['title']) : $NEW_LIST[$i]['title'];
						$NEW_LIST[$i]['number']      = ($titleAndSubject[0]) ? $titleAndSubject[0] : $NEW_LIST[$i]['title'];
						$NEW_LIST[$i]['goods_title'] = ($titleAndSubject[1]) ? $titleAndSubject[1] : $NEW_LIST[$i]['title'];
						$NEW_LIST[$i]['type']        = '연';
						$NEW_LIST[$i]['detail_url']  = $first_url . "/investment/investment.php?prd_idx=" . $NEW_LIST[$i]['idx'];

						// 카테고리 구분
						$category = $NEW_LIST[$i]['category'];
						switch($NEW_LIST[$i]['category']) {
							case '1' : $NEW_LIST[$i]['category_text'] = "동산";	break;
							case '2' :
								$NEW_LIST[$i]['category_text'] = "부동산";
								if($NEW_LIST[$i]['mortgage_guarantees']=='1') {
									$NEW_LIST[$i]['category_text'] = "주택담보대출";
									$category = 4;
								}
							break;
							case '3' : $NEW_LIST[$i]['category_text'] = "확정매출채권";	break;
						}

						if($NEW_LIST[$i]['main_image']) {
							$NEW_LIST[$i]['title_image_url'] = ( file_exists(G5_DATA_PATH . "/product/" . $NEW_LIST[$i]['main_image']) ) ? "/data/product/" . $NEW_LIST[$i]['main_image'] : "";
						}
						$NEW_LIST[$i]['title_image_url_m'] = $NEW_LIST[$i]['title_image_url'];


						// 총 투자금액
						$NEW_LIST[$i]["total_invest_amount"] = (!$NEW_LIST[$i]["total_invest_amount"]) ? 0 : $NEW_LIST[$i]["total_invest_amount"];

						if($NEW_LIST[$i]["recruit_amount"] > 0) { // 일부값 / 전체값 * 100
							$NEW_LIST[$i]['invest_percent'] = ($NEW_LIST[$i]["total_invest_amount"] > 0) ? round((($NEW_LIST[$i]["total_invest_amount"] / $NEW_LIST[$i]["recruit_amount"]) * 100), 2) : 0;
						}
						else {
							$NEW_LIST[$i]['invest_percent'] = 0;
						}

						$NEW_LIST[$i]['repay_count'] = 0;

						// 전체 정산회차수
						$loan_start_date_day = date(d);

						// 투자개월이 1개월 미만인 경우 일수로 표시
						if( $NEW_LIST[$i]['invest_period']==1 && $NEW_LIST[$i]['invest_days']>0 ) {
							$NEW_LIST[$i]['total_repay_count']  = $NEW_LIST[$i]['invest_period'];
							$NEW_LIST[$i]['invest_period']      = preg_replace("/[^0-9]*/s", "", $NEW_LIST[$i]['invest_days']);
							$NEW_LIST[$i]['invest_period_unit'] = "일";
						}
						else {
							$NEW_LIST[$i]['total_repay_count']  = ($loan_start_date_day < 5) ? $NEW_LIST[$i]['invest_period'] : $NEW_LIST[$i]['invest_period'] + 1;
							$NEW_LIST[$i]['invest_period']      = preg_replace("/[^0-9]*/s", "", $NEW_LIST[$i]['invest_period']);
							$NEW_LIST[$i]['invest_period_unit'] = "개월";
						}

						$NEW_LIST[$i]['recruit_period_start_date'] = date("Y년 m월 d일", strtotime($NEW_LIST[$i]['recruit_period_start']));
						$NEW_LIST[$i]['advance_invest_flag']       = ($NEW_LIST[$i]['advance_invest']=='Y' && $NEW_LIST[$i]['advance_invest_ratio'] > 0) ? 'Y' : 'N';
						$NEW_LIST[$i]["auto_invest_flag"]          = ($NEW_LIST[$i]['ai_grp_idx']) ? 'Y' : 'N';
						$NEW_LIST[$i]["new_flag"]                  = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5 day', strtotime($NEW_LIST[$i]['open_datetime']))) && ($NEW_LIST[$i]['recruit_amount'] > $NEW_LIST[$i]['total_invest_amount'])) ? 'Y' : 'N';
						$NEW_LIST[$i]["print_date"]                = ($NEW_LIST[$i]["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($NEW_LIST[$i]["start_datetime"])) : date("Y년 m월 d일", strtotime($NEW_LIST[$i]["recruit_period_start"]));
						$NEW_LIST[$i] = array_merge($NEW_LIST[$i], productStatusCheck($NEW_LIST[$i]['idx']));

						$activeProductList[] = $NEW_LIST[$i];

					}
				}


				// 활성 이벤트 상품 가져오기
				$sql = "
					SELECT
						EV.idx, EV.category, EV.state, EV.title, EV.main_image, EV.main_image_m,
						EV.invest_amount, EV.invest_profit, EV.invest_return, EV.total_return_amount, EV.start_datetime,
						EV.recruit_amount, EV.recruit_period_start, EV.recruit_period_end,
						EV.evaluate_score1, EV.evaluate_score2, EV.evaluate_score3, EV.evaluate_score4,
						(SELECT IFNULL(SUM(amount),0) FROM cf_event_product_invest WHERE product_idx = EV.idx AND invest_state='Y') AS total_invest_amount,
						(SELECT IFNULL(MAX(turn),0) FROM cf_product_success WHERE product_idx=EV.idx AND invest_give_state='Y') AS repay_count
					FROM
						cf_event_product EV
					WHERE 1=1
						AND EV.display='Y'
						AND EV.invest_end_date=''
					ORDER BY
						EV.start_date DESC,
						EV.idx DESC";
				$res = sql_query($sql);
				while( $row = sql_fetch_array($res) ) {
					$NEW_EVENT_LIST[] = $row;
				}

				$eventTotalCount = count($EVENT_LIST);
				if($eventTotalCount > 0) {
					for($i=0,$nLoop=1; $i<$eventTotalCount; $i++,$nLoop++) {

						$EVENT_LIST[$i]['detail_url'] = $first_url . "/event_invest/event_invest.php?prd_idx=" . $EVENT_LIST[$i]['idx'];
						$EVENT_LIST[$i]['type'] = '회';

						if($EVENT_LIST[$i]['main_image']) {
							$EVENT_LIST[$i]['title_image_url'] = ( file_exists(G5_DATA_PATH . "/product_special/" . $EVENT_LIST[$i]['main_image']) ) ? "/data/product_special/" . $EVENT_LIST[$i]['main_image'] : "";
						}

						if($EVENT_LIST[$i]['main_image_m']) {
							$EVENT_LIST[$i]['title_image_url_m'] = (file_exists(G5_DATA_PATH . "/product_special/" . $EVENT_LIST[$i]['main_image_m'])) ? "/data/product_special/" . $EVENT_LIST[$i]['main_image_m'] : "";
						}

						// 총 투자금액
						$EVENT_LIST[$i]["total_invest_amount"] = (!$EVENT_LIST[$i]["total_invest_amount"]) ? 0 : $EVENT_LIST[$i]["total_invest_amount"];

						if($EVENT_LIST[$i]["recruit_amount"] > 0) { // 일부값 / 전체값 * 100
							$EVENT_LIST[$i]['invest_percent'] = ($EVENT_LIST[$i]["total_invest_amount"] > 0) ? round((($EVENT_LIST[$i]["total_invest_amount"] / $EVENT_LIST[$i]["recruit_amount"]) * 100), 2) : 0;
						}
						else {
							$EVENT_LIST[$i]['invest_percent'] = 0;
						}

						$EVENT_LIST[$i]['invest_period'] = ceil(((strtotime($EVENT_LIST[$i]["recruit_period_end"]) - strtotime($EVENT_LIST[$i]["recruit_period_start"])) + 86400) / 86400) . "일";

						// 전체 정산회차수
						$loan_start_date_day = ($EVENT_LIST[$i]['loan_start_date'] > '0000-00-00') ? (int)substr($EVENT_LIST[$i]['loan_start_date'], -2) : date(d);
						$EVENT_LIST[$i]['total_repay_count'] = ($loan_start_date_day < 5) ? $EVENT_LIST[$i]['invest_period'] : $EVENT_LIST[$i]['invest_period'] + 1;

						$EVENT_LIST[$i]['recruit_period_start_date'] = date("Y년 m월 d일", strtotime($EVENT_LIST[$i]['recruit_period_start']));

						$EVENT_LIST[$i]["auto_invest_flag"] = 'N';
						$EVENT_LIST[$i]["new_flag"] = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($EVENT_LIST[$i]['open_datetime']))) && ($EVENT_LIST[$i]['recruit_amount'] > $EVENT_LIST[$i]['total_invest_amount'])) ? 'Y' : 'N';
						$EVENT_LIST[$i]["print_date"] = ($EVENT_LIST[$i]["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($EVENT_LIST[$i]["start_datetime"])) : date("Y년 m월 d일", strtotime($EVENT_LIST[$i]["recruit_period_start"]));
						$EVENT_LIST[$i] = array_merge($EVENT_LIST[$i], productStatusCheck($EVENT_LIST[$i]['idx']));

						if($EVENT_LIST[$i]['invest_percent'] < 100) {
							$eventProductList[] = $EVENT_LIST[$i];
						}

					}
				}

				$list = array_merge($activeProductList, $eventProductList);

				// 요청된 카운트 외의 배열에 속하는 데이터는 배열에서 제외
				$list_count = count($list);
				for($i=0,$j=1; $i<$list_count; $i++,$j++) {
					if($j > $list_count) unset($list_count[$i]);
				}

			break;

			case '2': // 인기상품

				$sql = "
					SELECT
						A.idx, A.ai_grp_idx, A.mortgage_guarantees, A.category, A.state, A.title, A.main_image, A.main_image_m,
						A.invest_return, A.invest_period, A.invest_days,
						A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime, A.end_date, A.loan_start_date, A.loan_end_date,
						A.purchase_guarantees, A.advanced_payment,
						A.advance_invest, A.advance_invest_ratio,
						A.open_datetime, A.stream_url1, A.stream_url2,
						A.live_invest_amount AS total_invest_amount,
						(SELECT IFNULL(MAX(turn),0) FROM cf_product_success WHERE product_idx=A.idx AND invest_give_state='Y' AND overdue_start_date IS NULL) AS repay_count
					FROM
						cf_product A
					WHERE 1=1
						$where
						AND A.state NOT IN(3,4,6,7)
						AND A.popular_goods='Y'
						AND A.idx NOT IN(148,157,171,175,176,225,231,238)
					ORDER BY
						A.open_datetime DESC,
						A.idx DESC";
					if($limit > 0) {
						$sql .= " LIMIT " . $limit;
					}
				$query = sql_query($sql);
				$totalCount = sql_num_rows($query);

				$nLoop = 1;
				if($totalCount > 0) {
					while($row = sql_fetch_array($query)) {
						$titleAndSubject = ($row['title']) ? extractText($row['title']) : $row['title'];
						$row['number']      = ($titleAndSubject[0]) ? $titleAndSubject[0] : $row['title'];
						$row['goods_title'] = ($titleAndSubject[1]) ? $titleAndSubject[1] : $row['title'];
						$row['detail_url']  = $first_url . "/investment/investment.php?prd_idx=" . $row['idx'];

						// 카테고리 구분
						$category = $row['category'];
						switch($row['category']) {
							case '1' : $row['category_text'] = "동산";	break;
							case '2' :
								$row['category_text'] = "부동산";
								if($row['mortgage_guarantees']=='1') {
									$row['category_text'] = "주택담보대출";
									$category = 4;
								}
							break;
							case '3' : $row['category_text'] = "확정매출채권";	break;
						}

						if($row['main_image']) {
							$row['title_image_url'] = ( file_exists(G5_DATA_PATH . "/product/" . $row['main_image']) ) ? "/data/product/" . $row['main_image'] : "";
						}

						$row['title_image_url_m'] = $row['title_image_url'];
						/*
						if($row['main_image_m']) {
							$row['title_image_url_m'] = (file_exists(G5_DATA_PATH . "/product/" . $row['main_image_m'])) ? "/data/product/" . $row['main_image_m'] : "";
						}
						*/

						// 총 투자금액
						$row["total_invest_amount"] = (!$row["total_invest_amount"]) ? 0 : $row["total_invest_amount"];

						if($row["recruit_amount"] > 0) { // 일부값 / 전체값 * 100
							$row['invest_percent'] = ($row["total_invest_amount"] > 0) ? round((($row["total_invest_amount"] / $row["recruit_amount"]) * 100), 2) : 0;
						}
						else {
							$row['invest_percent'] = 0;
						}

						// 전체 정산회차수
						$loan_start_date_day = ($row['loan_start_date'] > '0000-00-00') ? (int)substr($row['loan_start_date'], -2) : date(d);

						// 투자개월이 1개월 미만인 경우 일수로 표시
						if($row['invest_period']==1 && $row['invest_days']>0) {
							$row['total_repay_count'] = $row['invest_period'];
							$row['invest_period'] = preg_replace("/[^0-9]*/s", "", $row['invest_days']);
							$row['invest_period_unit'] = "일";
						}
						else {
							$row['total_repay_count'] = ($loan_start_date_day < 5) ? $row['invest_period'] : $row['invest_period'] + 1;
							$row['invest_period'] = preg_replace("/[^0-9]*/s", "", $row['invest_period']);
							$row['invest_period_unit'] = "개월";
						}

						$row['recruit_period_start_date'] = date("Y년 m월 d일", strtotime($row['recruit_period_start']));

						$row['advance_invest_flag'] = ($row['advance_invest']=='Y' && $row['advance_invest_ratio'] > 0) ? 'Y' : 'N';
						$row["auto_invest_flag"] = ($row['ai_grp_idx']) ? 'Y' : 'N';
						$row["new_flag"] = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($row['open_datetime']))) && ($row['recruit_amount'] > $row['total_invest_amount'])) ? 'Y' : 'N';
						$row["print_date"] = ($row["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($row["start_datetime"])) : date("Y년 m월 d일", strtotime($row["recruit_period_start"]));
						$row = array_merge($row, productStatusCheck($row['idx']));

						$nLoop++;
						$popularProductList[$category][] = $row;
					}
					if(count($popularProductList) > 0) {
						ksort($popularProductList);
					}
					$list = $popularProductList;
				}
			break;

			case '3':		// 최신상품 (각 카테고리별 금일자 오픈상품 1개씩 추출), $limit 무시

				$LIST = array();

				$sql = "
					SELECT
						A.idx, A.ai_grp_idx, A.mortgage_guarantees, A.category, A.state, A.title, A.main_image, A.main_image_m,
						A.invest_return, A.invest_period, A.invest_days,
						A.recruit_amount, A.recruit_period_start, A.recruit_period_end, A.start_datetime, A.end_date, A.loan_start_date, A.loan_end_date,
						A.purchase_guarantees, A.advanced_payment,
						A.advance_invest, A.advance_invest_ratio,
						A.open_datetime, A.stream_url1, A.stream_url2,
						A.live_invest_amount AS total_invest_amount,
						(SELECT IFNULL(MAX(turn),0) FROM cf_product_success WHERE product_idx=A.idx AND invest_give_state='Y' AND overdue_start_date IS NULL) AS repay_count
					FROM
						cf_product A
					WHERE 1=1
						$where
						AND A.state NOT IN(3,4,6,7)
						AND A.idx NOT IN(148,157,171,175,176,225,231,238)
						-- AND (invest_end_date='' OR invest_end_date<=CURRENT_DATE)
						-- AND (open_date=CURRENT_DATE() OR LEFT(A.start_datetime,10)=CURRENT_DATE())
				";

				$sqlOrderLimit = " ORDER BY A.open_datetime DESC, idx DESC LIMIT 1";

				// 부동산
				$sql1 = $sql. " AND A.category='2' AND mortgage_guarantees='' " . $sqlOrderLimit;
				$ROW  = sql_fetch($sql1);
				if($ROW['idx']) array_push($LIST, $ROW);

				// 주택담보대출
				$sql2 = $sql. " AND A.category='2' AND mortgage_guarantees='1' " . $sqlOrderLimit;
				$ROW  = sql_fetch($sql2);
				if($ROW['idx']) array_push($LIST, $ROW);

				// 동산
				$sql3 = $sql. " AND A.category='1' " . $sqlOrderLimit;
				$ROW = sql_fetch($sql3);
				if($ROW['idx']) array_push($LIST, $ROW);

				// 확정매출채권
				$sql4 = $sql. " AND A.category='3' " . $sqlOrderLimit;
				$ROW = sql_fetch($sql4);
				if($ROW['idx']) array_push($LIST, $ROW);

				unset($ROW);

				$totalCount = count($LIST);
				if($totalCount) {
					for($i=0,$nLoop=1; $i<$totalCount; $i++,$nLoop++) {
						$row = $LIST[$i];

						$titleAndSubject = ($row['title']) ? extractText($row['title']) : $row['title'];
						$row['number']      = ($titleAndSubject[0]) ? $titleAndSubject[0] : $row['title'];
						$row['goods_title'] = ($titleAndSubject[1]) ? $titleAndSubject[1] : $row['title'];
						$row['detail_url']  = $first_url . "/investment/investment.php?prd_idx=" . $row['idx'];

						// 카테고리 구분
						$category = $row['category'];
						switch($row['category']) {
							case '1' : $row['category_text'] = "동산";	break;
							case '2' :
								$row['category_text'] = "부동산";
								if($row['mortgage_guarantees']=='1') {
									$row['category_text'] = "주택담보대출";
									$category = 4;
								}
							break;
							case '3' : $row['category_text'] = "확정매출채권";	break;
						}

						if($row['main_image']) {
							$row['title_image_url'] = ( file_exists(G5_DATA_PATH . "/product/" . $row['main_image']) ) ? "/data/product/" . $row['main_image'] : "";
						}

						$row['title_image_url_m'] = $row['title_image_url'];
						/*
						if($row['main_image_m']) {
							$row['title_image_url_m'] = (file_exists(G5_DATA_PATH . "/product/" . $row['main_image_m'])) ? "/data/product/" . $row['main_image_m'] : "";
						}
						*/

						// 총 투자금액
						$row["total_invest_amount"] = (!$row["total_invest_amount"]) ? 0 : $row["total_invest_amount"];

						if($row["recruit_amount"] > 0) { // 일부값 / 전체값 * 100
							$row['invest_percent'] = ($row["total_invest_amount"] > 0) ? round((($row["total_invest_amount"] / $row["recruit_amount"]) * 100), 2) : 0;
						}
						else {
							$row['invest_percent'] = 0;
						}

						// 전체 정산회차수
						$loan_start_date_day = ($row['loan_start_date'] > '0000-00-00') ? (int)substr($row['loan_start_date'], -2) : date(d);

						// 투자개월이 1개월 미만인 경우 일수로 표시
						if($row['invest_period']==1 && $row['invest_days']>0) {
							$row['total_repay_count'] = $row['invest_period'];
							$row['invest_period'] = preg_replace("/[^0-9]*/s", "", $row['invest_days']);
							$row['invest_period_unit'] = "일";
						}
						else {
							$row['total_repay_count'] = ($loan_start_date_day < 5) ? $row['invest_period'] : $row['invest_period'] + 1;
							$row['invest_period'] = preg_replace("/[^0-9]*/s", "", $row['invest_period']);
							$row['invest_period_unit'] = "개월";
						}

						$row['recruit_period_start_date'] = date("Y년 m월 d일", strtotime($row['recruit_period_start']));

						$row['advance_invest_flag'] = ($row['advance_invest']=='Y' && $row['advance_invest_ratio'] > 0) ? 'Y' : 'N';
						$row["auto_invest_flag"] = ($row['ai_grp_idx']) ? 'Y' : 'N';
						$row["new_flag"]         = (G5_TIME_YMD <= date('Y-m-d', strtotime('+5day', strtotime($row['open_datetime']))) && ($row['recruit_amount'] > $row['total_invest_amount'])) ? 'Y' : 'N';
						$row["print_date"]       = ($row["start_datetime"]) ? date("Y년 m월 d일 H:i A", strtotime($row["start_datetime"])) : date("Y년 m월 d일", strtotime($row["recruit_period_start"]));

						$row = array_merge($row, productStatusCheck($row['idx']));

						array_push($latestProductList, $row);

					}

					if( count($latestProductList) > 0 ) {

						// 모집율-ASC, 오픈시간-DESC 로 재정렬
						foreach($latestProductList as $key => $row) {
							$sortA[$key] = $row['invest_percent'];
							$sortB[$key] = $row['start_datetime'];
						}
						array_multisort($sortA, SORT_ASC, $sortB, SORT_DESC, $latestProductList);

					}

					$list = $latestProductList;
				}



			break;
		}

		// 캐싱
		if($cache_fwrite) {
			$handle = fopen($cache_file, 'w');

			$cache_content = "<?\nif(!defined('_GNUBOARD_')) exit;\n\$list=".var_export($list, true)."?>";

			fwrite($handle, $cache_content);
			fclose($handle);
		}
	}
	return $list;
}

?>