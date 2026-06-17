<?php

if(@$_SERVER['HTTP_USER_AGENT']) {
	$path = "../../";
}
else {
	$path = "/home/crowdfund/public_html";
}

include_once($path . '/common.php');

?>