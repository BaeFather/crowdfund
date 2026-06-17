<?

include_once("../syndication_config.php");
include_once("inc_request_check.php");
include_once("inc_login_check.php");

header("Content-Type:application/json");

$json = json_encode($_RESULT);
echo $json;

?>