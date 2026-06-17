<?php
include_once('./crypt.php');


$value = '123123123';



$a1 = encrypt($value, 'jumin');

$a2 = decrypt($a1, 'jumin');


echo($value.' 암호화 : '.$a1.'<br><br>');
echo($a1.' 복호화 : '.$a2);




/*
$mb_id = $_SESSION['ss_mb_id'];


echo('mb_id = '.$mb_id);


session_unset(); 

// destroy the session 
session_destroy(); 
*/
?>