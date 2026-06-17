<?

include_once("_common.php");

$event_no = trim($_REQUEST['event_no']);

$EVENT_CONF = sql_fetch("SELECT * FROM recommend_event_config WHERE event_no='".$event_no."'");

echo json_encode($EVENT_CONF, JSON_UNESCAPED_SLASHES+JSON_UNESCAPED_UNICODE+JSON_PRETTY_PRINT); sql_close(); exit;

sql_close();
exit;

?>