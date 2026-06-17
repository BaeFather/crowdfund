PHPExcel 클래스를 이용해 Excel 2007~2010 의 xlsx 파일 읽기 (100만 행 까지)


기존 엑셀 2003까지 버전은 확장자가 xls 로 생성되며 한시트에 최대 65,535 라인까지 처리할 수 있다.
그 이상의 행을 처리하려면 엑셀 2007 이상의 버전에서 xlsx 파일로 생성해야 한다. (100만 라인까지 가능)
기본 65,535라인을 넘는 데이터를 처리해야 할 경우 PHPExcel 클래스를 이용해 간단히 처리할 수 있다.
참고로 서버에 zip 라이브러리가 설치되어 있어야 한다.

먼저 PHPExcel 클래스를 다운로드 받자

http://www.codeplex.com/PHPExcel 에 방문하여 최신 버전을 내려받는다.



현재 기준으로 1.7.9 버전이 최신이다.
파일을 내려 받으면 PHPExcel_1.7.9_doc.zip 라는 파일이 받아진다.
압축을 풀어 서버에 업로드 한다.

나의 경우에는 서버 계정의 _lib 디렉토리에 업로드 하고 디렉토리명을 PHPExcel 로 설정했다.

POST로 엑셀파일을 업로드 하는 폼 페이지는 생략 하겠다.
아래는 폼 페이지에서 파일을 업로드 하면 실행하는 파일이다.
(POST 로 날라오는 파일 폼 이름이  "upfile" 이라 정하면)


<?php

include $_SERVER["DOCUMENT_ROOT"]."/_lib/PHPExcel/Classes/PHPExcel.php";


$UpFile	= $_FILES["upfile"];
$UpFileName = $UpFile["name"];
$UpFilePathInfo = pathinfo($UpFileName);

$UpFileExt		= strtolower($UpFilePathInfo["extension"]);

if($UpFileExt != "xls" && $UpFileExt != "xlsx") {
	echo "엑셀파일만 업로드 가능합니다. (xls, xlsx 확장자의 파일포멧)";
	exit;
}


//-- 읽을 범위 필터 설정 (아래는 A열만 읽어오도록 설정함  => 속도를 중가시키기 위해)


class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
	public function readCell($column, $row, $worksheetName = '') {

		// Read rows 1 to 7 and columns A to E only
		if (in_array($column,range('A','A'))) {
			return true;
		}

		return false;
	}
}

$filterSubset = new MyReadFilter();


//업로드된 엑셀파일을 서버의 지정된 곳에 옮기기 위해 경로 적절히 설정
$upload_path = $_SERVER["DOCUMENT_ROOT"]."/Uploads/Excel_".date("Ymd");
$upfile_path = $upload_path."/".date("Ymd_His")."_".$UpFileName;

if(is_uploaded_file($UpFile["tmp_name"])) {
	if(!move_uploaded_file($UpFile["tmp_name"],$upfile_path)) {
		echo "업로드된 파일을 옮기는 중 에러가 발생했습니다.";
		exit;
	}

	//파일 타입 설정 (확자자에 따른 구분)
	$inputFileType = 'Excel2007';
	if($UpFileExt == "xls") {
		$inputFileType = 'Excel5';
	}

	//엑셀리더 초기화
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);

	//데이터만 읽기(서식을 모두 무시해서 속도 증가 시킴)
	$objReader->setReadDataOnly(true);


	//범위 지정(위에 작성한 범위필터 적용)
	$objReader->setReadFilter($filterSubset);

	//업로드된 엑셀 파일 읽기
	$objPHPExcel = $objReader->load($upfile_path);

	//첫번째 시트로 고정
	$objPHPExcel->setActiveSheetIndex(0);

	//고정된 시트 로드
	$objWorksheet = $objPHPExcel->getActiveSheet();

  //시트의 지정된 범위 데이터를 모두 읽어 배열로 저장
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
	$total_rows = count($sheetData);

	foreach($sheetData as $rows) {

		$fieldData = $rows["A"];				//A열값을 가져온다.

    /* 데이터 처리 */

	}

}

?>


위 데이터 처리 부분에 원하는 처리를 하면 되겠다.

데이터를 100만건까지 처리 하다보니 서버의 부담이 커서 데이터 시트 범위를 정하고, 서식을 모두 버리고 데이터만 처리하도록 세팅했다.

위 내용은 PHPExcel 라이브러리를 받아 내부에 문서를 보면 다 나와있는 내용이긴 하지만, 사용하려는 분들이 좀 더 쉽게 사용하시라고 예제를 만들어봤다. (실제 사용해 보니 잘 돌아간다.)
끝으로 PHPExcel 라이브러리를 만들어준 분께 감사드린다.
