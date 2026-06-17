<?php
// IP정보 설정일: 2022-06-03

$CONF['office_priv_ip']  = "183.98.101.114";		// 사무실 내부망
$CONF['office_pub_ip']   = "183.98.101.115";		// 사무실 외부망
$CONF['gjtec_vpn_ip']    = "211.248.149.48";

$CONF['office_ip'] = array(
	$CONF['office_priv_ip'],
	$CONF['office_pub_ip'],
	$CONF['gjtec_vpn_ip']
);

if( @$_SERVER['HTTP_X_FORWARDED_FOR'] ) {
	array_push($CONF['office_ip'], $_SERVER['HTTP_X_FORWARDED_FOR']);
}

// www1. www2. 도메인으로 접속한 경우 접속자 IP (회사 내외부망) $CONF['office_ip'] 배열에 추가
if( @$_SERVER['HTTP_USER_AGENT'] ) {
	if( @preg_match("/(172\.17\.3\.|172\.19\.3\.|172\.31\.3)/", @$_SERVER['REMOTE_ADDR']) ) {
		array_push($CONF['office_ip'], $_SERVER['REMOTE_ADDR']);
	}
}

?>