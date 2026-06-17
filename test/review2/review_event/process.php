<?php
include_once('./_common.php');
include_once('../../lib/function_prc.php');
include_once('../review.class.php');

	$kind = $_POST["kind"];

	IF($kind == "s1")
	{
		$strPost = ARRAY(
							ARRAY("sns","","Y"),ARRAY("content","","Y"),ARRAY("check01","","Y")
					);

		FOR($i=0;$i<COUNT($strPost);$i++)
		{
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
				} ELSE {

					IF($strPost[$i][0] == "sns") { $stralt = "선호상품이 없습니다. 선호상품을 선택하여 주십시오."; }
					IF($strPost[$i][0] == "content") { $stralt = "추천평이 없습니다. 추천평을 입력하여 주십시오."; }
					IF($strPost[$i][0] == "check01") { $stralt = "개인정보 활용 및 마케팅에 동의해야 합니다."; }

					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode($stralt)),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
			}

		}

		$mbContent = mb_strlen($content,"UTF-8");

		$stralt = "";
		IF($mbContent < 50)
		{
			$stralt = "추천평은 50자 이상이어야 합니다.";
		}
		IF($mbContent > 500)
		{
			$stralt = "추천평은 500자 이하까지 작성이 가능합니다.";
		}

		IF(!$_FILES["thumbnail"]["name"])
		{
			$stralt = "이미지를 선택하여 주십시오.";
		}

		IF(!$member["mb_no"])
		{
			$stralt = "추천평 작성은 로그인 후 이용이 가능합니다.";
		}

		IF($stralt)
		{
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode($stralt)),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		$img_path = G5_IMG_PATH.'/review/';

		 // 대표이미지 이름, 업로드
		if(isset($_FILES["thumbnail"]["name"]) && !empty($_FILES["thumbnail"]["name"])){
			$thumbnail = md5(uniqid(mt_rand(), true)).'.'.pathinfo($_FILES["thumbnail"]["name"], PATHINFO_EXTENSION);
			$thumbnail_origin = $_FILES["thumbnail"]["name"];

			UploadFile($img_path, '10', "thumbnail", null, $thumbnail, 'Y');
		//	if(!UploadFile($img_path, '10', "thumbnail", null, $thumbnail, 'Y')){
		//		throw new Exception("이미지를 저장할 수 없습니다.");
		//	}
		}
		$subject = "헬로펀딩 추천평 작성";
		$ndate = DATE("Y-m-d H:i:s");

		$strTable = "epilogue_list";
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

		fn_general_query_update("save",$strColumn,$strValues,$strTable,"","","",$connect);

		$strMsg = "추천평이 정상 등록 되었습니다";


	} ELSEIF($kind == "s2") {

		$strPost = ARRAY(
							ARRAY("mno","","Y"),ARRAY("check01","","Y"),ARRAY("check02","","Y")
					);

		FOR($i=0;$i<COUNT($strPost);$i++)
		{
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
				} ELSE {

					IF($strPost[$i][0] == "mno") { $stralt = "인터뷰 신청은 로그인 후 이용이 가능합니다."; }
					IF($strPost[$i][0] == "check01") { $stralt = "개인정보 활용 및 마케팅 활용에 동의 해야 합니다."; }
					IF($strPost[$i][0] == "check02") { $stralt = "동영상 및 사진촬영에 동의 해야 합니다."; }
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode($stralt)),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = urldecode($_POST[$strPost[$i][0]]);
			}

		}

		IF($member["mb_no"] <> $mno || !$mno || !$member["mb_no"])
		{
			$stralt = "추천평 작성은 로그인 후 이용이 가능합니다.";
		}

		IF($stralt)
		{
			$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode($stralt)),"retval"=>"");
			ECHO json_encode($objval);
			EXIT;
		}

		$ndate = DATE("Y-m-d H:i:s");

		$mb_hp = "";
		IF($member["mb_hp"])
		{
		$mb_hp = masterEncrypt($member["mb_hp"], false);
		}

		$strTable = "hello_event_request";
		$strColumn	=	ARRAY(
								"mb_no","mb_id","mb_name","mb_phone","reg_date","admin_comment",
								"recyn","yak1","yak2"
							);
		$strValues	=	ARRAY(
								$member["mb_no"], $member["mb_id"], $member["mb_name"],$mb_hp, $ndate,"",
								"R",$check01, $check02
							);

		fn_general_query_update("save",$strColumn,$strValues,$strTable,"","","",$connect);

		$strMsg = "인터뷰 신청이 정상 등록 되었습니다";
	}



	sql_close($connect_for);

	$objval = ARRAY("retcode"=>"OK","retalert"=>STR_REPLACE("+"," ",urlencode($strMsg)),"retval"=>"/review/review_event/");
	ECHO json_encode($objval);
?>