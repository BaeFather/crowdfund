<?php

include_once("/home/crowdfund/public_html/syndicate/oligo/lib/oligo_crypt.lib.php");

$encText = trim($_REQUEST['encText']);

$crypto = new CryptoCBC();
$decrypt = $crypto->deCrypt($encText);
$encrypt = $crypto->enCrypt($decrypt);


echo "encText : " . $encText ."<br/>\n";
echo "decrypt : " . $decrypt ."<br/>\n";
echo "encrypt : " . $encrypt ." ". strlen($encrypt) ."byte<br/>\n";

exit;


?>