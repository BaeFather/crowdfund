<?php
include_once('./_common.php');

$dtmDate = DATE("Y-m-d H:i:s");
$intRand = RAND(10,99);

$nifile = array();
$nifile_ori = array();

for ($i=0 ; $i<count($_FILES["i_file"]["name"]) ; $i++) {

	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);
	$strNumber  = $intRand+$i;
	$strName = substr(strrchr($_FILES['i_file']['name'][$i],"."),1);


	$strNumberName = $dtmDate1_1.$dtmDate1_2.$strNumber;
	
	$strNewName = $strNumberName.".".strtolower($strName);

	//$uploadFile = "./afile/" . basename($strNewName);
	$uploadFile = "./afile/". basename($strNewName);

	if (move_uploaded_file($_FILES['i_file']['tmp_name'][$i], $uploadFile)) {
/*
        // set array to send data to remote server
        $remoteData = array(
            'fileName' => $strNewName,
            'fileData' => base64_encode($uploadFile)
        );
*/

  $filename = $uploadFile; 
  $handle = fopen($filename, "r"); 
  //$data = base64_encode(fread($handle, filesize($filename))); 
  $data = fread($handle, filesize($filename)); 
        $remoteData = array(
            'fileName' => $strNewName,
            'fileData' => base64_encode($data)
        );

        // start curl set up for remote file upload
		$headers = array("Content-Type:multipart/form-data");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'http://admin.hellofunding.kr/etc/remote_upload.php');
		//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		//curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $remoteData);
        $response = curl_exec($curl);
        curl_close($curl);
		//echo $response;   // set response to server.php file 

		$nifile[$i] = $strNewName;
		$nifile_ori[$i] = $_FILES['i_file']['name'][$i];

	}


}


$examt = implode( ":" , $examount);
$examt = str_replace(",","", $examt);
$mxbnd = implode( ":" , $maxbond);
$mxbnd = str_replace(",","", $mxbnd);

$ddmoney = replace_integer($ddmoney);  // 대출신청금액
//$kb_jm = replace_integer($kb_jm);   // 전용면적
$kb_il = replace_integer($kb_il);   // KB시세 일반가
$kb_low = replace_integer($kb_low);   // KB시세 일반가
$kb_sil = replace_integer($kb_sil);   // KB시세 최근 실거래가
$house_deposit = replace_integer($house_deposit);   // KB시세 일반가
$bsmoney = replace_integer($bsmoney);   // KB시세 일반가
$hellofee = replace_integer($hellofee);   // KB시세 일반가
$ifile = implode("^" , $nifile);  // 첨부파일 
$ifile_ori = implode("^" , $nifile_ori);  // 첨부파일 


$jumin = replace_integer($jumin);
$encJumin = masterEncrypt($jumin, false);
/*
$ins_sql = "INSERT INTO hloan_content
					SET recyn = '$recyn',
						hmseq =  '$hmseq',
						laddr = '".$laddr."',
						kb_mg_id = '$mg_id',
						kb_ju_seri = '$ju_seri',
						kbquote = '".$kbquote."',
						pname = '$pname',
						regist_number = '".$encJumin."',
						pname_E_first='".$pname_E_first."',
						pname_E_last='".$pname_E_last."',
						sale_per = '$sale_per',
						dambo_pname = '$dambo_pname',
						dambo_pphone = '$dambo_pphone',
						pcp_income = '".$pcp_income."',
						crating = '$crating',
						mkind = '$mkind',
						loan_for = '$loan_for',
						pcp_job_group = '$pcp_job_group',
						pcp_company = '$pcp_company',
						pcp_comp_addr_post = '$pcp_comp_addr_post',
						pcp_comp_addr = '".$pcp_comp_addr."',
						pcp_comp_addr2 = '".$pcp_comp_addr2."',
						jm = '$kb_jm',
						kb_mm = '$kb_il',
						kb_mm_b = '$kb_low',
						hholds = '$kb_tot_house',
						land_yn = '$land_yn',
						house_deposit = '$house_deposit',
						ddmoney = '$ddmoney',
						bsmoney = '$bsmoney',
						examount = '$examt',
						maxbond = '$mxbnd',
						ltvkind = '$ltvkind',
						ltvmoney = '$ltvmoney',
						ltvmoney2 = '$ltvmoney2',
						add_hellobase = '$add_hellobase',
						hellobase = '$hellobase',
						hloan_end_date = '$hloan_end_date',
						fees = '$fees',
						hm_fees = '$hm_fees',
						hellofee = '$hellofee',
						content = '".$content."',
						productyn = '$productyn',
						product_idx = '$product_idx', 
						ifile = '$ifile',
						ifile_ori = '$ifile_ori',
						reg_date = NOW()
";
*/
$ins_sql = "INSERT INTO hloan_content
					SET recyn = '$recyn',
						hmseq =  '$hmseq',
						laddr = '".$laddr."',
						kb_mg_id = '$mg_id',
						kb_mg_id2 = '$mg_id2',
						kb_ju_seri = '$ju_seri',
						kb_ju_seri2 = '$ju_seri2',
						kbquote = '".$kbquote."',
						pname = '$pname',
						pphone1 = '$pphone1',
						regist_number = '".$encJumin."',
						pname_E_first='".$pname_E_first."',
						pname_E_last='".$pname_E_last."',
						sale_per = '$sale_per',
						dambo_pname = '$dambo_pname',
						dambo_pphone = '$dambo_pphone',
						pcp_income = '".$pcp_income."',
						crating = '$crating',
						mkind = '$mkind',
						loan_for = '$loan_for',
						pcp_job_group = '$pcp_job_group',
						pcp_company = '$pcp_company',
						pcp_comp_addr_post = '$pcp_comp_addr_post',
						pcp_comp_addr = '".$pcp_comp_addr."',
						pcp_comp_addr2 = '".$pcp_comp_addr2."',
						d_code = '$d_code',
						kb_date = '$kb_date',
						kbarea = '$kb_jm',
						kbprice = '$kb_il',
						kbllimit = '$kb_low',
						kb_mm_sil = '$kb_sil',
						kb_mm_sil_date = '$kb_sil_date',
						hholds = '$kb_tot_house',
						land_yn = '$land_yn',
						house_deposit = '$house_deposit',
						ddmoney = '$ddmoney',
						bsmoney = '$bsmoney',
						examount = '$examt',
						maxbond = '$mxbnd',
						ltvkind = '$ltvkind',
						ltvmoney = '$ltvmoney',
						ltvmoney2 = '$ltvmoney2',
						add_hellobase = '$add_hellobase',
						hellobase = '$hellobase',
						hloan_end_date = '$hloan_end_date',
						fees = '$fees',
						hm_fees = '$hm_fees',
						hellofee = '$hellofee',
						content = '".$content."',
						productyn = '$productyn',
						product_idx = '$product_idx', 
						ifile = '$ifile',
						ifile_ori = '$ifile_ori',
						reg_date = NOW()
";

$res = sql_query($ins_sql);

if ($res) {
	?>
	<script>
	alert("저장이 완료되었습니다.");
	self.location.replace("/adm/helloloan/");
	</script>
	<?
} else {
	?>
	<script>
	alert("저장 실패");
	history.back();
	</script>
	<?
}
?>
<?
FUNCTION fn_file_upload($fname,$strFileFolder,$strImgArr,$kind)
{
	global $_FILES;
	global $_POST;
	global $gstrNdate;
	global $strRepImgName; // 변경되지 않은 이름 정의시

	$strIFilename		=	$_FILES[$fname]["name"];
	$strIFilenameTmp	=	$_FILES[$fname]["tmp_name"];
echo $strIFilename." ".$strIFilenameTmp."<br/>";
	$strIfileCheck		=	$_POST[$fname."_check"]; // 이미지 삭제

	IF($strRepImgName)
	{
		$strIFilenameOr[0]	=	$strRepImgName; // 원본파일 이미지
	} ELSE {
		$strIFilenameOr		=	$_POST[$fname."_or"]; // 원본파일 이미지
	}

	/* 다중 파일 업로드 */
	$intRand = RAND(10,99);
	$j = 0;
	for($i=0; $i<sizeof($_POST[$fname]); $i++)
	{
		UNSET($strFileDelname);
		$intRand = $intRand + $i;

		FOR($j=0;$j<COUNT($strIfileCheck);$j++)
		{
			if($strIFilenameOr[$i] == $strIfileCheck[$j])
			{
				$strFileDelname = $strIfileCheck[$j];
				break;
			}
		}
		IF($kind == "single")
		{
			$strIFileNameUpload[$i] = file_upload($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strFileDelname);
		} ELSEIF($kind == "multi") {
			$strIFileName__ = file_upload($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strFileDelname);

			IF($strIFileName__)
			{
				IF($k > 0) { $strIFileNameUpload .= "^"; }
				$strIFileNameUpload .= $strIFileName__;
				$k++;
			}
		}
	}

	return $strIFileNameUpload;
}

function file_upload($strFileKind,$strWidth,$strHeight,$strImgThumKind,$strNumber,$strKind,$strFileName,$strFileNameTemp,$dtmDate,$strOrFileName,$strKindDel)
{
	$SaveDir = $_SERVER["DOCUMENT_ROOT"].$strKind;

	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

	if($strFileName)
	{
		$strName = substr(strrchr($strFileName,"."),1);
		if($strOrFileName)
		{
			 file_del($SaveDir."/".$strOrFileName);
			 $strNamenew = EXPLODE(".",$strOrFileName);
			 $strNewName = $strNamenew[0].".".strtolower($strName);
		} else {

			$strNumberName = $dtmDate1_1.$dtmDate1_2.$strNumber;

			$strNewName = $strNumberName.".".strtolower($strName);
		}
		move_uploaded_file($strFileNameTemp, $SaveDir."/".$strNewName);

		if($strFileKind == "IMG")
		{
			$strNewName = new thumbnailImgOr($SaveDir."/".$strNewName,$strWidth,$strHeight,$SaveDir,$strNewName,$strImgThumKind);
		}

	} else {
		if($strOrFileName)
		{
			$strNewName = $strOrFileName;
		} else {
			$strNewName = "";
		}
	}

	if($strKindDel)
	{
		if(!$strFileName)
		{
			file_del($SaveDir."/".$strOrFileName);
			$strNewName = "";
		}
	}

	return $strNewName;
}
?>