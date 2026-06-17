<?php
Class Hello_Member
{
  public $ndate;
  public $ntime;
  public $remoteIp;

  Public Function __construct()
  {
      $this->ndate 	= DATE("Y-m-d H:i:s");
      $this->ntime  = TIME();


  }

  Public Function __destruct()
  {
  }

  Function check_member_drop($mb_ci)
  {
    global $HelloUtil;

    $SeqName = "mb_no";

    $strColumn	=	ARRAY("cnt");
    $strTable   =   "g5_member_drop";

    FOR($i=0;$i<COUNT($strColumn);$i++)
    {
      ${$strColumn[$i]} = "";
    }

    $strQuery = "COUNT(mb_no) AS cnt";

    $strWhere	=	" WHERE mb_ci='".$HelloUtil->add_str($mb_ci)."' AND kakaopay_userid != ''";
    $strOrder	=	$SeqName." DESC";
    $intLimit1	=	0;
    $intLimit2	=	1;
    $intStrlen	=	100;

    $rowView = $HelloUtil->fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

    IF($rowView[0][$intSeqName])
    {
      FOR($i=0;$i<COUNT($strColumn);$i++)
      {
        ${$strColumn[$i]} = $rowView[0][$strColumn[$i]];
      }
    }

    return $cnt;
  }

  // 회원정보 검색
  Function check_member_select($mb_ci, $mb_name, $mb_hp_enc)
  {
    global $HelloUtil;

    $SeqName = "mb_no";

    $strColumn	=	ARRAY(
                        $SeqName,"mb_ci","mb_id","mb_name","mb_hp","mb_email",
                        "bank_name","bank_code","bank_private_name","account_num","va_bank_code2",
                        "virtual_account2","va_private_name2","kakaopay_userid"
                      );

    $strTable   =   "g5_member";

    FOR($i=0;$i<COUNT($strColumn);$i++)
    {
      ${$strColumn[$i]} = "";
    }

    $strQuery = "";

    $strWhere	=	" WHERE mb_level='1' AND member_group='F' AND (mb_ci='".$HelloUtil->add_str($mb_ci)."' OR (mb_name='".$HelloUtil->add_str($mb_name)."' AND mb_hp='".$HelloUtil->add_str($mb_hp_enc)."'))";
    $strOrder	=	$SeqName." DESC";
    $intLimit1	=	0;
    $intLimit2	=	1;
    $intStrlen	=	100;

    $rowList = $HelloUtil->fr_board_list_menu($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

    IF($rowView[0][$intSeqName])
    {
      FOR($i=0;$i<COUNT($strColumn);$i++)
      {
        ${$strColumn[$i]} = $rowList[2][$strColumn[$i]];
      }
    }

    // 카운트 와 value값 리턴
    $retval =  ARRAY(
              "cnt" => $rowList[1],
              "val" => ARRAY(
                        "mb_no"               => $mb_no,
                        "mb_ci"               => $mb_ci,
                        "mb_id"               => $mb_id,
                        "mb_name"             => $mb_name,
                        "mb_hp"               => $mb_hp,
                        "mb_email"            => $mb_email,
                        "bank_name"           => $bank_name,
                        "bank_code"           => $bank_code,
                        "bank_private_name"   => $bank_private_name,
                        "account_num"         => $account_num,
                        "va_bank_code2"       => $va_bank_code2,
                        "virtual_account2"    => $virtual_account2,
                        "va_private_name2"    => $va_private_name2,
                        "kakaopay_userid"     => $kakaopay_userid
                      )
      );

      return $retval;
  }

  Function fn_member_select($app_user_id)
  {
    global $HelloUtil;

    IF($app_user_id)
    {

      $SeqName = "mb_no";
      $strColumn	=	ARRAY(
                          $SeqName,"mb_name","va_bank_code2","virtual_account2","va_private_name2"
                        );

      $strTable   =   "g5_member";

      FOR($i=0;$i<COUNT($strColumn);$i++)
      {
        ${$strColumn[$i]} = "";
      }
      $strQuery = "";
      $strWhere	=	" WHERE mb_level='1' AND kakaopay_userid='".$HelloUtil->add_str($app_user_id)."'";
      $strOrder	=	$SeqName." DESC";
      $intLimit1	=	0;
      $intLimit2	=	1;
      $intStrlen	=	100;

      $rowView = $HelloUtil->fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen,$connect_for);

      IF(@$rowView[0][$SeqName])
      {
        FOR($i=0;$i<COUNT($strColumn);$i++)
        {
          ${$strColumn[$i]} = @$rowView[0][$strColumn[$i]];
        }
      }
    } ELSE {
      $mb_no            =   "";
      $mb_name          =   "";
      $va_bank_code2    =   "";
      $virtual_account2 =   "";
      $va_private_name2 =   "";
    }

    // 카운트 와 value값 리턴
    $retval =  ARRAY(
                        "mb_no"               => $mb_no,
                        "mb_name"             => $mb_name,
                        "va_bank_code2"       => $va_bank_code2,
                        "virtual_account2"    => $virtual_account2,
                        "va_private_name2"    => $va_private_name2
                      );

      return $retval;
  }


  FUNCTION fn_member_save($app_user_id,$mb_name,$mb_jumin, $mb_email, $mb_hp)
  {
      global $HelloUtil;

    	$mb_password          = preg_replace('/(-|:| )/', '', $this->ndate);
      $receive_method       = '2';  // 예치금(가상계좌)

      $JUMIN     = getBirthGender($mb_jumin);
    	$birthdate = $JUMIN[0];
    	$gender    = $JUMIN[1];

      $mb_hp_key    = substr($mb_hp, -4);
      $mb_hp_enc    = masterEncrypt($mb_hp, false);

      $mb_jumin_enc = masterEncrypt($mb_jumin, true);

      // 마케팅동의에 관한 파라미터가 없으므로 일단 모두 블로킹한다.
    	$mb_mailling  = "N";
    	$mb_push      = "N";
    	$mb_sms       = "N";
      //$mb_level     = "3"; // 임시로 저장 후 삭제나 회원 전환 level이 1
      $mb_level     = "1";

      $strTablename   = "g5_member";

      $strColumns = ARRAY(
                          "mb_id","mb_level","member_type","member_investor_type","mb_name",
                          "mb_email","mb_hp","mb_hp_key","mb_birth","mb_sex",
                          "zip_num","mb_addr1","mb_addr2","mb_mailling","mb_sms",
                          "receive_method","bank_name","bank_code","account_num","account_num_key",
                          "bank_private_name","kakaopay_userid","kakaopay_rdate","mb_ip","mb_datetime",
                          "mb_password","mb_ci"
                        );
      $strValues = ARRAY(
                          $mb_email, $mb_level, '1', '1', $mb_name,
                          $mb_email,$mb_hp_enc,$mb_hp_key,$birthdate,$gender,
                          '','','',$mb_mailling,$mb_sms,
                          $receive_method,'','','','',
                          '',$app_user_id,$this->ndate,$this->remoteIp,$this->ndate,
                          get_encrypt_string2($mb_password),$mb_ci
                        );

      $INSERT_ID = $HelloUtil->fn_general_query_update("save",$strColumns,$strValues,$strTablename,"","","",$connect_for);

      $link2 = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2);

      UNSET($strTablename);
      UNSET($strColumns);
      UNSET($strValues);

      $strTablename   = "member_private";

      $strColumns = ARRAY(
                          "mb_no","regist_number","5dm"
                        );
      $strValues = ARRAY(
                          $INSERT_ID, $mb_jumin_enc ,strtoupper(md5($mb_jumin))
                        );

      fn_general_query_update("save",$strColumns,$strValues,$strTablename,"","","",$link2);
      sql_close($link2);

      return $INSERT_ID;
  }

  FUNCTION fn_member_update($mb_no, $app_user_id)
  {
    global $HelloUtil;
    $strTablename   = "g5_member";

    $strColumns = ARRAY(
                        "kakaopay_userid","kakaopay_rdate","edit_datetime"
                      );
    $strValues = ARRAY(
                        $app_user_id, $this->ndate, $this->ndate
                      );

    $INSERT_ID = $HelloUtil->fn_general_query_update("update",$strColumns,$strValues,$strTablename,"mb_no",$mb_no,"",$connect_for);

    return $INSERT_ID;
  }

  FUNCTION fn_member_bank_update($bank_code, $account_number,$mb_name,$app_user_id)
  {
      global $HelloUtil;

      $mb_hp_enc = masterEncrypt($mb_hp, false);
      $acctNum      = preg_replace('/(-| )/', '', $account_number);
      $acctNum_enc  = masterEncrypt($acctNum, false);
      $acctNum_key  = substr($acctNum, -4);

      $acctName     = $this->fn_bank_code($bank_code);

      $strTablename  = "g5_member";

      $strColumns   = ARRAY("bank_name", "bank_code","account_num", "account_num_key","bank_private_name");
      $strValues    = ARRAY($acctName, $bank_code, $acctNum_enc, $acctNum_key, $mb_name);

      $HelloUtil->fn_general_query_update("update",$strColumns,$strValues,$strTablename,"kakaopay_userid",$app_user_id,"",$connect_for);
  }

  FUNCTION fn_check_member($app_user_id, $mb_ci, $mb_name, $mb_jumin, $mb_email, $mb_hp)
  {
      $mb_hp_enc = masterEncrypt($mb_hp, false);

      $strMemberCheck = $this->check_member_select($mb_ci, $mb_name, $mb_hp_enc);

      IF($strMemberCheck["cnt"] == 0) // 가입 내역이 없다면.
      {
          $retval = $this->fn_member_save($app_user_id,$mb_name,$mb_jumin, $mb_email, $mb_hp);
      } ELSE {
          $retval = $this->fn_member_update($strMemberCheck["val"]["mb_no"], $strMemberCheck["val"]["kakaopay_userid"]);
      }
      return $retval;
  }

  FUNCTION fn_bank_code($obj)
  {
  	$strArr = ARRAY(
  				ARRAY("02","한국산업은행"),
  				ARRAY("03","기업은행"),
  				ARRAY("04","국민은행"),
  				ARRAY("05","하나은행(구 외환)"),
  				ARRAY("06","국민은행(구 주택)"),
  				ARRAY("07","수협중앙회"),
  				ARRAY("11","농협중앙회"),
  				ARRAY("12","단위농협"),
  				ARRAY("16","축협중앙회"),
  				ARRAY("20","우리은행"),
  				ARRAY("21","구)조흥은행"),
  				ARRAY("22","상업으행"),
  				ARRAY("23","SC제일은행"),
  				ARRAY("24","한일은행"),
  				ARRAY("25","서울은행"),
  				ARRAY("26","구)신한은행"),
  				ARRAY("27","한국씨티은행(구한미)"),
  				ARRAY("31","대구은행"),
  				ARRAY("32","부산은행"),
  				ARRAY("34","광주은행"),
  				ARRAY("35","제주은행"),
  				ARRAY("37","전북은행"),
  				ARRAY("38","강원은행"),
  				ARRAY("39","경남은행"),
  				ARRAY("41","비씨카드"),
  				ARRAY("45","새마을금고"),
  				ARRAY("48","신용협동조합중앙회"),
  				ARRAY("50","상호저축은행"),
  				ARRAY("53","한국씨티은행"),
  				ARRAY("54","홍콩상하이은행"),
  				ARRAY("55","도이치은행"),
  				ARRAY("56","ABN암로"),
  				ARRAY("57","JP모건"),
  				ARRAY("59","미쓰비시도쿄은행"),
  				ARRAY("60","BOA(bank of America)"),
  				ARRAY("64","산림조합"),
  				ARRAY("70","신안상호저축은행"),
  				ARRAY("71","우체국"),
  				ARRAY("81","하나은행"),
  				ARRAY("83","평화은행"),
  				ARRAY("87","신세계"),
  				ARRAY("88","신한(통합)은행"),
  				ARRAY("89","케이뱅크"),
  				ARRAY("90","카카오뱅크"),
  				ARRAY("93","토스머니"),
  				ARRAY("94","SSG머니"),
  				ARRAY("96","엘포인트"),
  				ARRAY("97","카카오머니"),
  				ARRAY("98","페이코"),
  				ARRAY("D1","유안타증권(구 동양증권)"),
  				ARRAY("D2","현대증권"),
  				ARRAY("D3","미래에셋증권"),
  				ARRAY("D4","한국투자증권"),
  				ARRAY("D5","우리투자증권"),
  				ARRAY("D6","하이투자증권"),
  				ARRAY("D7","HMC투자증권"),
  				ARRAY("D8","SK증권"),
  				ARRAY("D9","대신증권"),
  				ARRAY("DA","하나대투증권"),
  				ARRAY("DB","굿모닝신한증권"),
  				ARRAY("DC","동부증권"),
  				ARRAY("DD","유진투자증권"),
  				ARRAY("DE","메리츠증권"),
  				ARRAY("DF","신영증권"),
  				ARRAY("DG","대우증권"),
  				ARRAY("DH","삼성증권"),
  				ARRAY("DI","교보증권"),
  				ARRAY("DJ","키움증권"),
  				ARRAY("DK","이트레이드"),
  				ARRAY("DL","솔로몬증권"),
  				ARRAY("DM","한화증권"),
  				ARRAY("DN","NH증권"),
  				ARRAY("DO","부국증권"),
  				ARRAY("DP","LIG증권"),
  				ARRAY("BW","뱅크월렛")
  			);

  	FOR($i=0;$i<COUNT($strArr);$i++)
  	{
  		IF($obj == $strArr[$i][0])
  		{
  			$retval = $strArr[$i][1];
  			break;
  		}
  	}
  	return $retval;
  }
}
?>
