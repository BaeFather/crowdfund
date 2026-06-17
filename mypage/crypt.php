<?php
/**
* 암호화 함수
* $value : 암호화 시킬 문자열,  $key : 암,복호화 시 사용할 키값
*/
function encrypt($value, $key) {
	$result = "";
	for($i=0; $i<strlen($value); $i++) {
		$val1 = chr(ord(substr($value, $i, 1))+strlen($value));
		$key1 = substr($key, ($i % strlen($key)), 1);
		$val1 = $val1 ^ $key1;
		$result .= $val1;
	}
	return base64_encode($result);
}




/**
* 복호화 함수
* $value : 복호화 시킬 문자열,  $key : 암,복호화 시 사용할 키값
*/
function decrypt($value, $key) {
	$result = "";
	$value = base64_decode($value);
	for($i=strlen($value)-1; $i>=0; $i--) {
		$val2 = substr($value, $i, 1);
		$key2 = substr($key, ($i % strlen($key)), 1);

		$val2 = $val2 ^ $key2;
		$val2 = chr(ord($val2)-strlen($value));
		$result = $val2.$result;
	}
	return $result;
}
?>