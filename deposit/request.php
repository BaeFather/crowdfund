<? 
	//#######################################################################################
	//#####
	//#####	개인실명확인 샘플 소스 (실명확인요청)						나이스평가정보(주)
	//#####
	//#####	================================================================================
	//#####
	//#######################################################################################

	$posts = Array( // 포스트 
			//##################################################
			//###### ▣ 회원사 ID 설정   - 계약시에 발급된 회원사 ID를 설정하십시오. ▣
			//###### ▣ 회원사 PW 설정   - 계약시에 발급된 회원사 PASSWORD를 설정하십시오. ▣
			//###### ▣ 조회사유  설정   - 10:회원가입 20:기존회원가입 30:성인인증 40:비회원확인 90:기타사유 ▣
			//###### ▣ 개인/사업자 설정 - 1:개인 2:사업자 ▣
			//##################################################
			"niceUid"=>"NID100158",					// 나이스평가정보에서 고객사에 부여한 구분 id
			"svcPwd"=>"funny123@",						// 나이스평가정보에서 고객사에 부여한 서비스 이용 패스워드
			"inq_rsn"=>"40",							// 조회사유 - 10:회원가입 20:기존회원가입 30:성인인증 40:비회원확인 90:기타사유
			"strGbn"=>"1",								// 1 : 개인, 2: 사업자
			//##################################################
			//###### 위의 값을 알맞게 수정해 주세요.
			//##################################################
			"strResId"=>$_POST["JUMINNO"],					// 주민등록번호
			"strNm"=>$_POST["USERNM"],							// 이름
			"strBankCode"=>$_POST["strBankCode"],		// 은행코드
			"strAccountNo"=>$_POST["strAccountNo"],	// 계좌번호
			"service"=>$_POST["service"],						// 서비스구분
			"svcGbn"=>$_POST["svcGbn"],							// 업무구분
			"svc_cls"=>$_POST["svc_cls"],						// 내/외국인 구분
			"strOrderNo"=>date("Ymd") . rand(1000000000,9999999999),			//주문번호 : 매 요청마다 중복되지 않도록 유의(수정불필요)
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
		
		if ( ! $socket )
		{
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
		echo "주문번호   : " . $retOrderNo . "<br>";
		echo "응답코드   : E999<br>";
		echo "응답메시지 : 소켓연결에 실패하였습니다.<br>";
	}
	else
	{
		// 결과값 처리
		$NewInfo = explode("\r\n", $retval);			// 헤더정보에서 뉴라인 체크
		$infoValue = explode("|",$NewInfo[8]);		// 9번째 줄의 결과값만 가져온다.
	
		
		//echo $NewInfo[8];
	
		$OrderNum	= $infoValue[0];		// 주문번호
		$ResultCD	= $infoValue[1];		// 결과코드
		$Msg			= $infoValue[2];		// 메세지
		
		echo "주문번호   : " . $OrderNum . "<br>";
		echo "응답코드   : " . $ResultCD . "<br>";
		echo "응답메시지 : " . $Msg . "<br>";
	}

	

?>
