<?php
	// settlng 클래스
	Class Hello_setting
	{
		public $ndate;

		Public  function __construct()
		{
			$this->ndate = DATE("Y-m-d");
		}

		Public  function __destruct()
		{

		}

		Public function fn_addr_yn()
		{
			$strArr	=	ARRAY(
								ARRAY("A","전체 시/구"),
								ARRAY("B","시/구 선택")
						);
			return $strArr;
		}

		Public function fn_setting_recyn()
		{
			$strArr	=	ARRAY(
								ARRAY("N","미적용"),
								ARRAY("Y","적용")
						);
			return $strArr;
		}

		Public function fn_hloan_member()
		{
			$strColumn = ARRAY("hmseq","cname");
			$strTable  = "hloan_member_renew";
			$strQuery  = "";
			$strWhere  = " WHERE section='2' AND recyn='Y'";
			$strorder  = " binary(cname) ASC";
			$frlimit1  = 0;
			$frlimit2  = $num_per_page;

		    $rowList = fr_board_list_re($strColumn,$strTable,$strQuery,$strWhere,$strorder,$frlimit1,$frlimit2,$strlen,$connect_for);

			return $rowList[2];
		}

		Public function fn_addr_si()
		{
			$strColumn = ARRAY(
								ARRAY("서울특별시","서울"),
								ARRAY("경기도","경기"),
								ARRAY("인천광역시","인천"),
								ARRAY("대구광역시","대구"),
								ARRAY("대전광역시","대전"),
								ARRAY("광주광역시","광주"),
								ARRAY("부산광역시","부산"),
								ARRAY("울산광역시","울산"),
								ARRAY("세종특별자치시","세종")
						);

			return $strColumn;
		}

		Public function fn_addr_gu($addr_si)
		{
			$strColumn = ARRAY("gu");
			$strTable  = "add_code";
			$strQuery  = "";
			$strWhere  = " WHERE si='".$addr_si."' AND gu <>'' GROUP BY gu";
			$strorder  = " binary(gu) ASC";
			$frlimit1  = 0;

		    $rowList = fr_board_list_re($strColumn,$strTable,$strQuery,$strWhere,$strorder,$frlimit1,$frlimit2,$strlen,$connect_for);

			return $rowList[2];
		}

		Public Function fn_setting_history($SE)
		{
			$strColumn = ARRAY("seq","mb_id","update_date");
			$strTable  = "hloan_content_setting_history";
			$strQuery  = "";
			$strWhere  = " WHERE hcsseq='".$SE."'";
			$strorder  = " seq ASC";
			$frlimit1  = 0;

		    $rowList = fr_board_list_re($strColumn,$strTable,$strQuery,$strWhere,$strorder,$frlimit1,$frlimit2,$strlen,$connect_for);

			return $rowList[2];
		}

		Public FUNCTION fn_g5_member($mb_id)
		{
			$mb_name = "";
			IF($mb_id)
			{
				$Query = "SELECT mb_no, mb_name FROM g5_member WHERE mb_id='".add_str($mb_id)."'";

				$Result = sql_query($Query, $connect);

				IF($Row=sql_fetch_array($Result))
				{
					$mb_name	=	strip_str($Row["mb_name"]);
					sql_free_result($Result);
				}
			}
			return $mb_name;
		}

	}

	$ClassHelloSetting	=	new Hello_setting();
?>