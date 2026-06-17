#!/usr/local/php/bin/php -q
<?

set_time_limit(0);

$base_path = "/home/crowdfund/public_html";

include_once($base_path . '/common.cli.php');

$PRDT['idx'] = "7087";
//shell_exec("/usr/local/php/bin/php -q /home/crowdfund/public_html/adm/repayment/make_turn_member.php {$PRDT['idx']} > /dev/null &");
$aaa = shell_exec("/usr/local/php/bin/php -q /home/crowdfund/public_html/adm/repayment/make_turn_member.php {$PRDT['idx']}");
var_dump($aaa);
?>