<?php
include_once('./_common.php');
include_once('../admin.loan.function.php');
?>
<?
//echo "<pre>"; print_r($_FILES); echo "<pre>";

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
	//$uploadFile = "./afile/". basename($strNewName);
	$uploadFile = "../../data/afile/". basename($strNewName);

	if (move_uploaded_file($_FILES['i_file']['tmp_name'][$i], $uploadFile)) {

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
       // $response = curl_exec($curl);
        curl_close($curl);
		//echo $response;   // set response to server.php file 

		$nifile[$i] = $strNewName;
		$nifile_ori[$i] = $_FILES['i_file']['name'][$i];

	}


}


$ifile = implode("^" , $nifile);  // 첨부파일 
$ifile_ori = implode("^" , $nifile_ori);  // 첨부파일 

if ($old_file) $ifile = $old_file."^".$ifile;
if ($old_file_ori) $ifile_ori = $old_file_ori."^".$ifile_ori;

$up_sql = "UPDATE hloan_content SET ifile = '$ifile', ifile_ori = '$ifile_ori' WHERE hcseq='$idx'";
sql_query($up_sql);

//echo $up_sql."<br/>";
//echo "$ifile<br/>";
//echo "$ifile_ori<br/>";
?>
<script>
history.back();
</script>