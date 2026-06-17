<?php
die();
	/**********************************************************************************************
	 NICE평가정보 휴대폰 인증 서비스
	 서버 네트웍크 및 방확벽 관련하여 아래 IP와 Port를 오픈해 주셔야 이용 가능합니다.
	 IP : 121.131.196.200 / Port : 3700 ~ 3715    
	
	보안을 위해 제공해드리는 샘플페이지는 서비스 적용 후 서버에서 삭제해 주시기 바랍니다. 
    **********************************************************************************************/
	
	$sSiteCode	= "AB917";			// 사이트 코드 (NICE평가정보에서 발급한 사이트코드)
	$sSitePw		= "8vJBrEtmUvdb";			// 사이트 패스워드 (NICE평가정보에서 발급한 사이트패스워드)
	
	$hp_num1 = "010";
	$hp_num2 = "8624";
	$hp_num3 = "6176";
	$hpComp = "1";
	
	$sBirth		= "7207311";						// 주민번호 앞 7자리
	$sName 			= iconv("utf-8","euc-kr","전승찬");						// 사용자 성명
	$sHP 				= $hp_num1.$hp_num2.$hp_num3; // 휴대폰번호
	$sHPComp  	= $hpComp;										// 이통사
	$sRequestSeq = "SEQ00001";								// 요청SEQ_식별값
  
  $mcheckplus_path = "/home/crowdfund/NICE/CheckPlusSafe_SOCK_PHP/64bit/MCheckPlus";
  	
  //인자값 : /MCheckPlus AUTH 사이트코드 사이트비밀번호 주민등록번호 이름 이통사구분(1/2/3) 휴대전화번호 요청고유번호(option)
  $sResultData = `$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $sName $sHPComp $sHP $sRequestSeq`;  
	
	
	
	echo "$mcheckplus_path AUTH $sSiteCode $sSitePw $sBirth $sName $sHPComp $sHP $sRequestSeq<br/>";
	//결과 : 응답코드|요청SEQ|응답SEQ
	echo "결과 : $sResultData";
	
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