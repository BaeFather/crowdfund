<?php

echo "<pre>"; print_r($_SERVER); echo "</pre>"; exit;

include_once("/home/crowdfund/public_html/syndicate/oligo/lib/oligo_crypt.lib.php");

$plainText = trim($_REQUEST['plainText']);

$crypto = new CryptoCBC();
$encrypt = $crypto->enCrypt($plainText);
$decrypt = $crypto->deCrypt($encrypt);

echo "plainText : " . $plainText ."<br/>\n";
echo "encrypt : " . $encrypt ." ". strlen($encrypt) ."byte<br/>\n";
echo "decrypt : " . $decrypt ."<br/>\n";

echo strlen("INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==") ."<br/>\n";
echo urlencode("INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ+kQcWygzhjFs0yFku7gLWgkGA==") ."<br/>\n";		// INyVTTfK1vsLDA598G6B2NRiusDTQfNW5awDL3vBlnOmS7VsqtQ7iQNM5mbhZ%2BkQcWygzhjFs0yFku7gLWgkGA%3D%3D

exit;


/*
$gstrHelloBanner = NEW Hello_Banner();

$gstrHelloBanner->CODE = "0001";

$strVal = $gstrHelloBanner->RsContent();

FOR($i=0;$i<COUNT($strVal);$i++)
{
	ECHO $strVal[$i]["repimg"]."<BR>";
	ECHO $strVal[$i]["targeturl"]."<BR>";
	ECHO $strVal[$i]["targetlink"]."<BR>";
}
*/

?>