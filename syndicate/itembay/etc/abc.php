<?
include_once('./_common.php');


echo BSC_PATH;echo "<br/>";
$aaa = masterEncrypt("aaa", false);
echo $aaa."<br/>";

$bbb = masterDecrypt("ASdTWwlWjuu1JmJZyHlffg==",false);
echo $bbb."<br/><br/>";

echo getJumin_new(6412);
?>