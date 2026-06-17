<?
$aa = "김철수";
if (mb_substr($aa,0,3)=="(주)") $dis_char=4;
else $dis_char=1;
$ab = mb_substr($aa,0,$dis_char).str_repeat(" *",mb_strlen(mb_substr($aa,$dis_char)));

echo "$aa<br/>";
echo $ab;
echo "<br/><br/>";

$aa = "(주)헬로핀테크";
if (mb_substr($aa,0,3)=="(주)") $dis_char=4;
else $dis_char=1;
$ab = mb_substr($aa,0,$dis_char).str_repeat(" *",mb_strlen(mb_substr($aa,$dis_char)));

echo "$aa<br/>";
echo $ab;
echo "<br/><br/>";
?>