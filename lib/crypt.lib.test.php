<?

// 암호화
function masterEncrypt($data, $isRSA=0)
{

	global $CONF;

	// 암호화방식 = ($isRSA==1) ? RSA(비대칭암호화) : AES256(대칭암호화);

	if(trim($data)=='') {
		return false;
	}
	else {

		$pvkey = LoadKey("pv", $CONF['LoadKeyPwd']);
		$pbkey = LoadKey("pb", $CONF['LoadKeyPwd']);

		if( $isRSA ) {
			$pubkey_decoded = openssl_pkey_get_public($pbkey);
			if($pubkey_decoded === false) return false;

			$ciphertext = false;
			$status = @openssl_public_encrypt($data, $ciphertext, $pubkey_decoded);
			if(!$status || $ciphertext === false) return false;

			return base64_encode($ciphertext);		// 암호문을 base64로 인코딩하여 반환한다.

		}
		else {

			$key = hash('MD5', $pvkey, true);
			return base64_encode(openssl_encrypt($data, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));

		}
	}
}


// 복호화
function masterDecrypt($ciphertext, $isRSA=0)
{

	global $CONF;

	if(trim($ciphertext)=='') {
		return false;
	}
	else {

		$pvkey = LoadKey("pv", $CONF['LoadKeyPwd']);
		$pbkey = LoadKey("pb", $CONF['LoadKeyPwd']);

		if( $isRSA ) {
			$password = NULL;			// $password = 'this is a passphrase';

			$ciphertext = base64_decode($ciphertext, true);
			if($ciphertext === false) return false;

			$privkey_decoded = openssl_pkey_get_private($pvkey, $password);
			if($privkey_decoded === false) return false;

			$plaintext = false;
			$status = openssl_private_decrypt($ciphertext, $plaintext, $privkey_decoded);
			@openssl_pkey_free($privkey_decoded);
			if(!$status || $plaintext === false) return false;

			return $plaintext;		// 이상이 없는 경우 평문을 반환한다.

		}
		else {

			$key = hash('MD5', $pvkey, true);
			$plaintext = openssl_decrypt(base64_decode($ciphertext), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
			return $plaintext;

		}
	}
}

function LoadKey($key_type, $password) {
	if($key_type && in_array($key_type, array('pv','pb'))) {
		if( defined('G5_MYSQL_HOST2') && defined('G5_MYSQL_USER2') && defined('G5_MYSQL_PASSWORD2') && defined('G5_MYSQL_DB2') ) {

			$enc_password = get_encrypt_string2($password);

			//echo "(".$password.":".$enc_password.")\n";

			$linkX = sql_connect(G5_MYSQL_HOST2, G5_MYSQL_USER2, G5_MYSQL_PASSWORD2, G5_MYSQL_DB2) or die('DB2 Connect Error!!!');

			$sql = "
				SELECT
					AES_DECRYPT(UNHEX(".$key_type."),'".$enc_password."') AS ".$key_type."
				FROM
					secure_key
				ORDER BY
					rdate DESC LIMIT 1";
			$SECURE_KEY = sql_fetch($sql, "", $linkX);
			//print_r($SECURE_KEY);
			return $SECURE_KEY[$key_type];

		}
		else {
			return false;
		}
	}
	else {
		return false;
	}
}

?>