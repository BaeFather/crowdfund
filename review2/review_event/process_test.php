<?php
include_once('./_common.php');
include_once('../../lib/function_prc.php');
include_once('../review.class.php');

	$kind = $_POST["kind"];

	IF($kind == "s1")
	{

	} ELSEIF($kind == "s2") {

	} ELSEIF($kind == "s3") {

		/*
		$strColumn	=	ARRAY(
								"thumbnail","thumbnail_origin","mem_id","mem_name","subject",
								"contents","target_link","display_yn","regdate","sort",
								"best_review","target_att","section","snskind","content2",
								"content2m","content2txt","reg_date"
							);
		$strValues	=	ARRAY(
								$thumbnail, $thumbnail_origin, $member["mb_id"], $member["mb_name"],$subject,
								$content,"","R",$ndate,0,
								"N","","3","","",
								"",$sns,DATE("Y-m-d")
							);
		*/
		$ndate = DATE("Y-m-d H:i:s");
		$ins_sql = "INSERT INTO epilogue_list
							SET mem_id = '".$member["mb_id"]."',
								mem_name = '".$member["mb_name"]."',
								subject = '".$snsrvw_title."',
								subject_m = '".$snsrvw_title."',
								contents = '',
								target_link = '".$snsrvw_url."',
								display_yn = 'R',
								regdate = '$ndate',
								sort = 0,
								best_review = 'N',
								target_att = '',
								section = '2',
								content2 = '',
								content2m = '',
								content2txt = '',
								reg_date = '".DATE("Y-m-d")."'
					";
		sql_query($ins_sql);

		$strMsg = "SNS 후기 이벤트 신청이 정상 등록 되었습니다.";

	} 



	sql_close($connect_for);

	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode($strMsg)),"retval"=>"/review/review_event/", "sql"=>$ins_sql);
	ECHO json_encode($objval);
?>