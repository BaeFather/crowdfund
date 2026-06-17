<?php

#!/usr/local/php/bin/php -q

// author: chesskim <chesekim@nate.com>
// blog: chess72.tistory.com
// encode: UTF-8

// Seed128 + CBC mode + PKCS7 + base64 암호화 & 복호화 운용 예제

mb_internal_encoding('UTF-8');

require_once "Seed128Cipher.php";

//$text_Key = md5(uniqid(rand(), true));
$text_Key = "12345678901234567890";
$text_Data = "니미씨팔";

echo "Key \t:: " . $text_Key . "\n";
echo "Data \t:: " . $text_Data . "\n";

$seed = new Seed128Cipher();

$encryptText = $seed->base64_encrypt($text_Data, $text_Key, "UTF-8");
echo "Encrypt Text \t:: " . $encryptText . "\n";

$decryptText = $seed->base64_decrypt($encryptText, $text_Key);
echo "Decrypt Text \t:: " . $decryptText . "\n";

?>