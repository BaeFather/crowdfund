<?php
include_once('./_common.php');
$sub_menu = "300780";
auth_check($auth[$sub_menu], 'w');

// 기간
$sdate = date_create($reg_date);
$edate = date_create($end_date);
$diff = date_diff($sdate, $edate);
$gigan = $diff -> format("%d");
$title = addslashes($title);

if($mode=='modify') {  // 수정일 경우
	
	if(isset($content) || $_FILES['content']['name']) {  // 파일이 있을 경우

		$imgFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  // 파일 확장자
		$target_dir = G5_PATH."/data/popup_img/";  // 경로 설정
		$target_file = $target_dir.basename($_FILES['content']['tmp_name']);  // 파일 경로

		$row = sql_fetch("SELECT content, origin_content FROM $g5[site_popup_table] WHERE no = $no");
		$pre_content = $row['content'];
		$ori_content = $row['origin_content'];


		// 스크립트 파일 업로드 제한
		if($imgFileType == html || $imgFileType == php || $imgFileType == htm) {
			
			alert("스크립트 파일은 등록할 수 없습니다.");

		} else if($_FILES['content']['size'] > 2000000) {  // 파일 사이즈 체크 
			
			alert("파일 사이즈가 너무 큽니다.");

		} else {
			// 기존 파일이 있을 때
			if($content) {
				$content = $pre_content;
				$origin_content = $ori_content;
			}
			
			// 파일 업로드 된게 있을 때
			if($_FILES['content']['name']) {
				if(move_uploaded_file($_FILES['content']['tmp_name'], $target_file)) { 
					$content = basename($_FILES['content']['tmp_name']);
					$origin_content = $_FILES['content']['name'];

				} 
			}
		}
			
	} else {
		alert('파일을 업로드해 주세요.');
	}

		
	$sql="
		update
			$g5[site_popup_table]
		set
			level=$level,
			title='$title',
			type='$type',
			content='$content',
			origin_content='$origin_content',
			gigan='$gigan',
			check_use='$check_use',
			reg_date=DATE_FORMAT('$reg_date','%Y-%m-%d'),
			end_date=DATE_FORMAT('$end_date','%Y-%m-%d')
		 where 
			no=$no
		";


	$result = sql_query($sql,$connect_db);


	if(!$result) {
		alert("수정되지 않았습니다.");
	}

	alert("수정되었습니다.","./site_popup_list.php");


	} else {  // 새로 추가인 경우

		$imgFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));  // 파일 확장자
		$target_dir = G5_PATH."/data/popup_img/";  // 경로 설정
		$target_file = $target_dir.basename($_FILES['content']['tmp_name']);  // 파일 경로

		// 스크립트 파일 업로드 제한
		if($imgFileType == html || $imgFileType == php || $imgFileType == htm) {
			
			alert("스크립트 파일은 등록할 수 없습니다.");

		} else if($_FILES['content']['size'] > 2000000) {  // 파일 사이즈 체크 
			
			alert("파일 사이즈가 너무 큽니다.");

		} else {
			// 파일 업로드 된게 있을 때
			if($_FILES['content']['name']) {
				if(move_uploaded_file($_FILES['content']['tmp_name'], $target_file)) { 
					$content = basename($_FILES['content']['tmp_name']);
					$origin_content = $_FILES['content']['name'];

				} 
			} else {
				alert("파일을 업로드해 주시기 바랍니다.");
			}
		}


		if(!$reg_date){
			$date_text="now()";
		} else{
			$date_text="DATE_FORMAT('$reg_date','%Y-%m-%d')";
		}

		if(!$end_date){
			$y = substr($reg_date, 0, 4);  
			$m = substr($reg_date, 4, 2);  
			$d = substr($reg_date, 6, 2);  
			$ymd = $y.'-'.$m.'-'.$d;
			$end_date_text = date('Y-m-d', strtotime($ymd . ' 1 day'));
			$end_date_text = "'".$end_date_text."'";
		} else {
			$end_date_text="DATE_FORMAT('$end_date','%Y-%m-%d')";
		}


		$sql = "
			INSERT 
			INTO 
				$g5[site_popup_table]
					(`user_id`,     `type`, 
					 `level`,       `title`,       `content`,    `origin_content`, 
					 `gigan`,		`check_use`,   `reg_date`,    `end_date`)
			 VALUES ('$user_id',    '$type',
					 '$level',      '$title',      '$content',    '$origin_content',
					 '$gigan',		'$check_use',   $date_text,    $end_date_text)
			 ";

		$result = sql_query($sql,$connect_db);

		if(!$result) {
			alert("등록되지 않았습니다.");
		}

		alert("등록되었습니다.","./site_popup_list.php");

	}
?>
