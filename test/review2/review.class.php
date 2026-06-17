<?php
Class strReviewClass
{
	// G5_COOKIE_DOMAIN  /config.php

	public $ndate;
	public $strLinkUrl;

	Public Function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public Function __destruct()
	{
	}

	Public Function fn_list($section, $strlimit2)
	{
		global $strColumn;
		global $page;

		$intSeqName = "id";
		$strTable = "epilogue_list";
		$strQuery = "";
		$strWhere = " WHERE section='".$section."' AND display_yn = 'Y'";

		$strOrder = "";
		IF( in_array($section, array('1','3','2')) )
		{
			$strOrder.= "best_review DESC, regdate DESC, sort ASC, id DESC";
		}
		else {
			$strOrder.= "id DESC";
		}

		//IF($_SERVER["REMOTE_ADDR"] == "220.117.134.164") echo "(".$strOrder.")";


		$rowList = fr_board_list($strColumn,$strTable,$frQuery,$strWhere,$strOrder,"",$strlimit2,"2000",$connect_for);

		return ARRAY(
						"tpage" =>	$rowList[0],
						"tcnt"	=>	$rowList[1],
						"val"	=>	$rowList[2]
				    );
	}

	Public Function fn_recommend_view($SE)
	{
		$strColumn = ARRAY("id","subject","mem_name","contents","content2txt","mb_sex","CNT");

		$strTable = "( SELECT t1.id,t1.subject,CONCAT(LEFT(mem_name,1),'**') as mem_name,t1.contents,t1.content2txt,t2.mb_sex
		,(SELECT COUNT(*) as CNT FROM cf_product_invest WHERE invest_state='Y' AND member_idx=t2.mb_no) as CNT
		FROM
		epilogue_list t1
		LEFT JOIN g5_member t2
		ON t1.mem_id = t2.mb_id
		WHERE t1.id='".add_str($SE)."') t1";

		$strQuery = "";
		$strWhere = " ";
		$strOrder = " t1.id ASC";
		$strlimit1 = 0;
		$strlimit2 = 1;

		$rowView = fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$strlimit1,$strlimit2,"2000",$connect_for);

		return $rowView[0];
	}

	Public Function fn_invest_cnt($mb_no)
	{
		$strColumn = ARRAY("CNT");

		$strTable = "cf_product_invest";
		$strQuery = "COUNT(*) as CNT";
		$strWhere = " WHERE invest_state='Y' AND member_idx='".add_str($mb_no)."'";
		$strOrder = " idx ASC";
		$strlimit1 = 0;
		$strlimit2 = 1;

		$rowView = fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$strlimit1,$strlimit2,"2000",$connect_for);

		return $rowView[0];
	}

	Public Function fn_registed_cnt($mb_id)
	{
		$strColumn = ARRAY("CNT");

		$strTable = "epilogue_list";
		$strQuery = "COUNT(id) AS CNT";
		$strWhere = " WHERE display_yn='Y' AND mem_id='".add_str($mb_id)."'";
		$strOrder = " CNT DESC";
		$strlimit1 = 0;
		$strlimit2 = 1;

		$rowView = fr_board_view($strColumn,$strTable,$strQuery,$strWhere,$strOrder,$strlimit1,$strlimit2,"2000",$connect_for);

		return $rowView[0];
	}

	Public Function fn_view($section,$SE)
	{
		global $strColumn;

		$intSeqName = "id";
		$strTable = "epilogue_list";
		$strQuery = "";
		$strWhere = " WHERE section='".$section."' AND id='".add_str($SE)."'";
		$strOrder = "sort ASC";
		$strlimit1 = 0;
		$strlimit2 = 1;

		$rowView = fr_board_view($strColumn,$strTable,$frQuery,$strWhere,$strOrder,$strlimit1,$strlimit2,"2000",$connect_for);

		return ARRAY(
						"val"	=>	$rowView[0]
				    );
	}

	Public Function fn_review_section()
	{
		$strVal = ARRAY(
						ARRAY("1","인터뷰"),
						ARRAY("2","SNS리뷰"),
						ARRAY("3","추천평")
					   );
		return $strVal;
	}

	Public FUNCTION fn_epilogue_snskind()
	{
		$retval = ARRAY(
					ARRAY("B","블로그","img/blog.png"),
					ARRAY("F","페이스북","img/facebook.png"),
					ARRAY("C","까페","img/insta.png"),
					ARRAY("T","티스토리","img/tstory.png")
				  );
		return $retval;
	}

	Public FUNCTION fn_product()
	{
		$retval = ARRAY(
					ARRAY("부동산","부동산"),
					ARRAY("주택담보","주택담보"),
					ARRAY("동산","동산"),
					ARRAY("헬로페이-면세점","헬로페이-면세점"),
					ARRAY("헬로페이-소상공인","헬로페이-소상공인")
				  );
		return $retval;
	}

	Public FUNCTION fn_rep_img($obj)
	{
		$intCnt = $this->fn_epilogue_snskind();

		FOR($i=0;$i<COUNT($intCnt);$i++)
		{
			IF($intCnt[$i][0] == $obj)
			{
				$retval = $intCnt[$i][2];
				break;
			}
		}

		return $retval;
	}

	Public FUNCTION fn_mb_sex()
	{
		$strVal = ARRAY(
						ARRAY("m","남"),
						ARRAY("w","여")
					   );
		return $strVal;
	}

	Public FUNCTION fn_general_txt($obj,$strArr)
	{
		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			//$strArrto = EXPLODE("^",$strArr[$i]);
			IF($strArr[$i][0] == $obj)
			{
				$retval = $strArr[$i][1];
				break;
			}
		}
		return $retval;
	}

}
?>
