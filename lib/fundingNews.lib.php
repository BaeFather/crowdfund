<?
if(!defined('_GNUBOARD_')) exit;

// 언론보도 최신글 추출
// $cache_time 캐시 갱신시간
function fundingNews($skin_dir='', $rows=10, $subject_len=40, $cache_time=1, $options='') {

	global $g5;

	if (!$skin_dir) $skin_dir = 'basic';

	if( preg_match('#^theme/(.+)$#', $skin_dir, $match) ) {
		if (G5_IS_MOBILE) {
			$latest_skin_path = G5_THEME_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
			if(!is_dir($latest_skin_path))
				$latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
			$latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
		}
		else {
			$latest_skin_path = G5_THEME_PATH.'/'.G5_SKIN_DIR.'/latest/'.$match[1];
			$latest_skin_url = str_replace(G5_PATH, G5_URL, $latest_skin_path);
		}
		$skin_dir = $match[1];
	}
	else {
		if(G5_IS_MOBILE) {
			$latest_skin_path = G5_MOBILE_PATH.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
			$latest_skin_url  = G5_MOBILE_URL.'/'.G5_SKIN_DIR.'/latest/'.$skin_dir;
		}
		else {
			$latest_skin_path = G5_SKIN_PATH.'/latest/'.$skin_dir;
			$latest_skin_url  = G5_SKIN_URL.'/latest/'.$skin_dir;
		}
	}

	$cache_fwrite = false;
	if( G5_USE_CACHE  && preg_match('/(www\.hellofunding\.co\.kr|dev\.hellofunding\.co\.kr)/i', G5_URL) ) {		// 헬로펀딩도메인 접속시에만 캐싱파일 생성
		$cache_file = G5_DATA_PATH."/cache/latest-fundingNews-{$skin_dir}-{$rows}-{$subject_len}.php";
		if(!file_exists($cache_file)) {
			$cache_fwrite = true;
		} else {
			if($cache_time > 0) {
				$filetime = filemtime($cache_file);
				if($filetime && $filetime < (G5_SERVER_TIME - 3600 * $cache_time)) {
					@unlink($cache_file);
					$cache_fwrite = true;
				}
			}

			if(!$cache_fwrite)
				include($cache_file);
		}
	}

	if(!G5_USE_CACHE || $cache_fwrite) {
		$list = array();

		$sql = " SELECT * FROM funding_news_list WHERE 1=1 ORDER BY regdate DESC LIMIT 0, {$rows} ";
		$result = sql_query($sql);
		for ($i=0; $row = sql_fetch_array($result); $i++) {

			$list[$i]['news_link'] = $row["news_link"];
			if ($subject_len)
				$list[$i]['subject'] = conv_subject($row['subject'], $subject_len, '…');
			else
				$list[$i]['subject'] = conv_subject($row['subject'], 100, '…');

			$list[$i]['thumbnail'] = "";
			if($row['thumbnail'] && file_exists(G5_PATH.$row['thumbnail'])) {
				$list[$i]['thumbnail'] = $row['thumbnail'];
			//$list[$i]['thumbnail'] = preg_replace("/(https\:\/\/www\.hellofunding\.co\.kr\:443|https\:\/\/www\.hellofunding\.co\.kr|https\:\/\/dev\.hellofunding\.co\.kr\:4443|https\:\/\/dev\.hellofunding\.co\.kr)/i", "", $list[$i]['thumbnail']);		// 도메인 제거
			}

			$list[$i]['news_logo'] = "";
			if($row['news_logo'] && file_exists(G5_PATH.$row['news_logo'])) {
				$list[$i]['news_logo'] = $row['news_logo'];
			//$list[$i]['news_logo'] = preg_replace("/(https\:\/\/www\.hellofunding\.co\.kr\:443|https\:\/\/www\.hellofunding\.co\.kr|https\:\/\/dev\.hellofunding\.co\.kr\:4443|https\:\/\/dev\.hellofunding\.co\.kr)/i", "", $list[$i]['news_logo']);		// 도메인 제거
			}
			$list[$i]['show_date'] = str_replace('-','.',substr($row['show_date'],0,10));

			$list[$i]['icon_new'] = '';
			if ($row['regdate'] >= date("Y-m-d H:i:s", G5_SERVER_TIME - (24 * 3600))){
				$list[$i]['icon_new'] = '<img src="'.G5_THEME_IMG_URL.'/main/icon_img01.png" alt="N" />';
			}
		}

		if($cache_fwrite) {
			$handle = fopen($cache_file, 'w');

			$cache_content = "<?\nif(!defined('_GNUBOARD_')) exit;\n\$list=".var_export($list, true)."?>";

			fwrite($handle, $cache_content);
			fclose($handle);
		}
	}



	ob_start();
	include $latest_skin_path.'/fundingNews.skin.php';
	$content = ob_get_contents();
	ob_end_clean();

	return $content;

}

?>