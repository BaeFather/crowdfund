<?
@error_reporting(E_ALL ^ E_NOTICE);  // 에러 무시코드 (임의 삽입함)
	//#######################################################################################
	//#####
	//##### 계좌 예금주 실명확인요청						나이스평가정보(주)
	//#####
	//#####	================================================================================
	//#####
	//#######################################################################################

	/*
	if($_SERVER['REMOTE_ADDR']=='220.76.118.61') {
		print_r($_POST); exit;
	}
	*/

	//$strGbn = '1';
	$strGbn = ($_POST['strGbn']) ? $_POST['strGbn'] : '1';

	$bank_private_name = trim($_POST["USERNM"]);
	$bank_private_name.= (trim($_POST["bank_private_name_sub"])) ? "(".trim($_POST["bank_private_name_sub"]).")" : "";  // 2017-07-10 부기명 처리 추가

	$posts = Array( // 포스트
			//##################################################
			//###### ▣ 회원사 ID 설정   - 계약시에 발급된 회원사 ID를 설정하십시오. ▣
			//###### ▣ 회원사 PW 설정   - 계약시에 발급된 회원사 PASSWORD를 설정하십시오. ▣
			//###### ▣ 조회사유  설정   - 10:회원가입 20:기존회원가입 30:성인인증 40:비회원확인 90:기타사유 ▣
			//###### ▣ 개인/사업자 설정 - 1:개인 2:사업자 ▣
			//##################################################
			"niceUid" => "NID100158",				// 나이스평가정보에서 고객사에 부여한 구분 id
			"svcPwd"  => "funny123@",				// 나이스평가정보에서 고객사에 부여한 서비스 이용 패스워드
			"inq_rsn" => "10",							// 조회사유 - 10:회원가입 20:기존회원가입 30:성인인증 40:비회원확인 90:기타사유
			"strGbn"  => $strGbn,						// 1: 개인, 2: 사업자
			//##################################################
			//###### 위의 값을 알맞게 수정해 주세요.
			//##################################################

			"strResId"     => preg_replace('/-/', '', $_POST["JUMINNO"]),			// 주민등록번호
			"strNm"        => $bank_private_name,															// 이름(예금주명)		2017-07-10 부기명 처리 추가
			"strBankCode"  => $_POST["strBankCode"],													// 은행코드
			"strAccountNo" => $_POST["strAccountNo"],													// 계좌번호
			"service"      => $_POST["service"],															// 서비스구분
			"svcGbn"       => $_POST["svcGbn"],																// 업무구분
			"svc_cls"      => $_POST["svc_cls"],															// 내/외국인 구분
			"strOrderNo"   => date("Ymd") . rand(1000000000,9999999999),			//주문번호 : 매 요청마다 중복되지 않도록 유의(수정불필요)
	);

	$cookies = array();		// 쿠키
	$referer = "";				// 리퍼러

	$host = "secure.nuguya.com";																														// 호스트
//$page = "https://secure.nuguya.com/nuguya/service/realname/sprealnameactconfirm.do";		// URL
	$page = "https://secure.nuguya.com/nuguya2/service/realname/sprealnameactconfirm.do";		// UTF-8 URL

	$retval = sock_post($host,$page,$posts,$cookies,$referer);

	################################################################################################
	// sock_post() 함수 시작
	################################################################################################
	function sock_post($host,$target,$posts,$cookies,$referer='',$port=443) {
		if(is_array($posts)) {
			foreach($posts AS $name=>$value) $postValues .= urlencode($name) . "=" . urlencode($value) . '&';
			$postValues = substr($postValues, 0, -1);
		}


		$postLength = strlen($postValues);

		if(is_array($cookies)) {
			foreach($cookies AS $name=>$value) $cookieValues .= urlencode($name) . "=" . urlencode($value) . ';';
			$cookieValues = substr($cookieValues, 0, -1);
		}

		$request  = "POST $target HTTP/1.1\r\n";
		$request .= "Host: $host\r\n";
		$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$request .= "Content-Length: " . $postLength . "\r\n";
		$request .= "Connection: close\r\n";
		$request .= "\r\n";
		$request .= $postValues;

		$ret = '';
		$socket  = fsockopen("ssl://".$host, $port, $errno, $errstr, 10); // 소켓 타임아웃 10초

		if(!$socket) {
			return false;
		}

		fputs($socket, $request);
		while(!feof($socket)) $ret .= fgets($socket, 1024);
		fclose($socket);

		return $ret;
	}
	//################################################################################################

	if( $retval == false) {
		$retOrderNo = $posts["strOrderNo"];
		/*
		echo "주문번호   : " . $retOrderNo . "<br>";
		echo "응답코드   : E999<br>";
		echo "응답메시지 : 소켓연결에 실패하였습니다.<br>";
		*/

		echo('E999*:소켓연결에 실패하였습니다*:'.$retOrderNo);
	}
	else {

		// 결과값 처리
		$NewInfo = explode("\r\n", $retval);			// 헤더정보에서 뉴라인 체크
		$infoValue = explode("|",$NewInfo[8]);		// 9번째 줄의 결과값만 가져온다.


		//echo $NewInfo[8];

		$OrderNum	= $infoValue[0];		// 주문번호
		$ResultCD	= $infoValue[1];		// 결과코드
		//$Msg			= $infoValue[2];		// 메세지
		//$Msg      = iconv("EUC-KR", "UTF-8", $Msg);

		switch($ResultCD) {
			case 'DB01' :	$Msg = '해당 데이터가 존재하지 않음';			break;
			case 'DB02' :	$Msg = '실명조회 DB 에러';								break;
			case 'D100' :	$Msg = 'ID에 할당된 사업자번호 오류';			break;
			case 'D200' :	$Msg = '주민번호오류';										break;
			case 'D300' :	$Msg = '사업자번호오류';									break;
			case 'D400' :	$Msg = '주민번호,사업자번호구분 오류';		break;
			case 'D500' :	$Msg = '서비스 구분 오류(테스트,Real)';		break;
			case 'D600' :	$Msg = 'Key 오류';												break;
			case 'D700' :	$Msg = '거래일자오류';										break;
			case 'D800' :	$Msg = '거래시간오류';										break;
			case 'D900' :	$Msg = '조회은행코드오류';								break;
			case 'D101' :	$Msg = '조회 주민등록번호 오류';					break;
			case 'D102' :	$Msg = '조회 사업자번호 오류';						break;
			case 'D103' :	$Msg = '조회 계좌번호 오류';							break;
			case 'D104' :	$Msg = 'Flag오류';												break;
			case 'D105' :	$Msg = '구분 오류';												break;
			case 'TIME' :	$Msg = 'TIMEOUT(응답지연)';								break;
			case 'DSYS' :	$Msg = '시스템장애';											break;
			case 'OVER' :	$Msg = '동시 접속자수 초과';							break;
			case 'D888' :	$Msg = '당행서비스가 불가능함';						break;
			case 'D999' :	$Msg = '서비스 시간 아님.';								break;
			case 'B001' :	$Msg = '생년월일-계좌일치, 계좌성명-계좌불일치';		break;
			case 'B002' :	$Msg = '생년월일-계좌불일치, 계좌성명-계좌일치';		break;
			case 'B003' :	$Msg = '생년월일-계좌불일치, 계좌성명-계좌불일치';	break;
			case 'B004' :	$Msg = '계좌성명-계좌불일치';							break;
			case 'B005' :	$Msg = '미등록코드(응답메시지확인필요)';	break;
			case 'B101' :	$Msg = '타행(공동망) or 은행시스템 오류';	break;
			case 'B102' :	$Msg = '계좌오류';												break;
			case 'B103' :	$Msg = '생년월일 또는사업자번호상이';			break;
			case 'B104' :	$Msg = '입력성명과 계좌성명이 다름';			break;
			case 'B199' :	$Msg = '은행 기타 오류';									break;
			case 'C001' :	$Msg = 'Connection Fail';									break;
			case 'C002' :	$Msg = 'Data Write Fail';									break;
			case 'C003' :	$Msg = 'Data Read Fail';									break;
			case 'S606' :	$Msg = '계좌소유주명 오류';								break;
			case 'S606' :	$Msg = '계좌소유주명 오류';								break;
		}

		/*
		echo "주문번호   : " . $OrderNum . "<br>";
		echo "응답코드   : " . $ResultCD . "<br>";
		echo "응답메시지 : " . $Msg . "<br>";
		*/

		echo $ResultCD.'*:'.$Msg.'*:'.$OrderNum;

	}

?>