<?php

include_once('hf_common.php');

echo HF_SESSION_PATH;
echo "<BR><BR>";

echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

echo "<BR><BR>";

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

?>