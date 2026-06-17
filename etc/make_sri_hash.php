<?

function str_f6($val, $ss, $ee){
	$temp_arr = explode($ss, $val);
	$temp_arr2 = explode($ee, $temp_arr[1]);
	$value = trim($temp_arr2[0]);
	$temp_arr = $temp_arr2 = NULL;
	return $value;
}

$ARR = array(
	'<script src="https://www.hellofunding.co.kr/theme/2018/js/jquery-ui-1.12.1/jquery-ui.min.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/theme/2018/js/swiper.min.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/jquery.blockUI.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/jquery.menu.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/common.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/wrest.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/theme/2018/js/jquery.webui-popover.min.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/theme/2018/js/iscroll.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/jquery.validation-1.19.0/dist/jquery.validate.min.js" crossorigin="anonymous"></script>',
	'<script src="https://www.hellofunding.co.kr/js/modernizr.custom.70111.js" crossorigin="anonymous"></script>'
);


for($i=0; $i<count($ARR); $i++) {
	$script_path = str_f6($ARR[$i], "<script src=\"", "\"");
	$script_path = preg_replace("/https:\/\/www\.hellofunding\.co\.kr/", "/home/crowdfund/public_html", $script_path);
	//echo $script_path."<br>\n";

	$script_hash = exec("openssl dgst -sha384 -binary $script_path | openssl base64 -A");

	$last_script = preg_replace("/crossorigin=\"anonymous\"/", " integrity=\"sha384-".$script_hash."\" crossorigin=\"anonymous\"", $ARR[$i]);
	echo $last_script . "\n";
}

?>

