<?php

include_once('../common.php');
include_once('../lib/common.lib.php');
include_once('../lib/sms.lib.php');
include_once(G5_PATH . '/mypage/crypt.php');

error_reporting(E_ALL ^ E_NOTICE);

	/**********************************************************************************************
	 NICE평가정보 휴대폰 인증 서비스
	 서버 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	 IP : 121.131.196.200 / Port : 3700 ~ 3715    
	
	보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
    **********************************************************************************************/

	$key = "jumin";

	$sSiteCode	 = "AB917";			                  // 사이트 코드 (NICE평가정보에서 발급한 사이트코드)
	$sSitePw	 = "8vJBrEtmUvdb";			          // 사이트 패스워드 (NICE평가정보에서 발급한 사이트패스워드)
	
	$iBirth		 = $_POST["Mbirth"];  		          // 생년월일 19880609
	$iSex        = $_POST["Msex"];                    // 성별 1 남자 2 여자
	$sBirth      = substr($iBirth,2,7);         // 주민번호 앞 7자리

	$oName       = $_POST["Mname"];
	$sName 		 = iconv("utf-8","euc-kr",$oName);			// 사용자 성명

	$sHP 		 = $_POST["Mhp"];           // 휴대폰번호
	$sHPComp  	 = $_POST["Mhpcomp"];		// 이통사
	$sRequestSeq = date("YmdHis")."-".rand(100,999);				// 요청SEQ_식별값

	$already = "";

	$chk_sql = "select * from cf_auth_nice 
					where auth_phone_num='$sHP' 
					and auth_name='$oName'
					and auth_birth='".encrypt($_POST[Mbirth], $key)."'
					and auth_sex='$iSex'
					and rescode='0000' 
					and insert_date>now() - interval 10 day 
					order by insert_date desc limit 1";

	$chk_res = sql_query($chk_sql);
	$chk_cnt = sql_num_rows($chk_res);

	if ($chk_cnt) {

		$chk_row = sql_fetch_array($chk_res);
		$sResultData = $chk_row['rescode']."|".$chk_row['reqseq']."|".$chk_row['resseq'];
		$already = "Y";

	} else {

		$mcheckplus_path = "/home/crowdfund/NICE/CheckPlusSafe_SOCK_PHP/64bit/MCheckPlus";
		//echo "$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $sName $sHPComp $sHP $sRequestSeq";
		
		//인자값 : /MCheckPlus AUTH 사이트코드 사이트비밀번호 주민등록번호 이름 이통사구분(1/2/3) 휴대전화번호 요청고유번호(option)
		$nice_send_data = "";
		$nice_send_data = "$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $oName $sHPComp $sHP $sRequestSeq";
		
		$sResultData    = "";
		//$sResultData    = `$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $sName $sHPComp $sHP $sRequestSeq`;  
	}

	//결과 : 응답코드|요청SEQ|응답SEQ
	//echo "결과 : $sResultData";

	$res_tmp = explode("|",$sResultData);

	$insert_idx = 0;
	
	$sql = "insert into cf_auth_nice set
		auth_name = '$oName',
		auth_birth = '". encrypt($_POST[Mbirth], $key)."',
		auth_sex       = '$iSex',
		auth_telecom = '$sHPComp',
		auth_phone_num = '$sHP',
		reqseq = '$res_tmp[1]',
		rescode = '$res_tmp[0]',
		resseq = '$res_tmp[2]',
		insert_date = NOW()
	";
	if ($chk_cnt) {
		$insert_idx = $chk_row["idx"];
	} else {
		sql_query($sql);
		$insert_idx = sql_insert_id();
	}
	
	$res = array(
		"src" => $nice_send_data,
		"res" => $sResultData,
		"getReturnCode"=>$res_tmp[0],
		"req_seq"=>$res_tmp[1],
		"res_seq"=>$res_tmp[2],
		"already"=>$already,
		"sql_id"=>$insert_idx
	);
	if ($chk_cnt) {$res["checked"]="Y";}
	echo json_encode($res,JSON_UNESCAPED_UNICODE);

	/*
	연동 결과 코드
	0 : 정상
	-1 ~ -6 : 암/복호화 오류
	-7 ~ -8 : 통신 오류
	-9, 12 : 입력값 오류
	
	응답 코드(getReturnCode)    
	0000 : 명의자 인증 성공
	0001 : 명의자 인증 불일치
	0006 : 법인 사용자
	0007 : 선불폰 사용자
	0008 : 중지 사용자
	0009 : 무선인터넷 차단 사용자
	*/    
	

?>