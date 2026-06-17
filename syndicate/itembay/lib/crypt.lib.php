<?

/*
 *  @desc 공개키로 데이터를 암호화한다.
 *  @param $data : 암호화할 데이터
 *  @param $use_key : 사용할 키
 *  @return base64 encode한 암호화 값
 *  2019-01-2 전체적용
 */
function masterEncrypt($data, $isRSA=0)
{

	// 암호화방식 = ($isRSA==1) ? RSA(비대칭암호화 - 개인키, 공개키 이용) : AES256(대칭암호화 - 개인키 이용);

	if(trim($data)=='') {
		return false;
	}
	else {
		global $syndi_base_path;
		$private_key = file_get_contents(BSC_PATH . '/syndicate/finnq/keys/hello_rsa_pri.20180627.pem');  // 개인키
		$public_key  = file_get_contents(BSC_PATH . '/syndicate/finnq/keys/hello_rsa_pub.20180627.pem');	// 공개키

		if( $isRSA ) {
			$pubkey_decoded = openssl_pkey_get_public($public_key);		// 공개키를 사용하여 암호화한다.
			if($pubkey_decoded === false) return false;

			$ciphertext = false;
			$status = @openssl_public_encrypt($data, $ciphertext, $pubkey_decoded);
			if(!$status || $ciphertext === false) return false;

			return base64_encode($ciphertext);		// 암호문을 base64로 인코딩하여 반환한다.

		}
		else {

			$key = hash('MD5', $private_key, true);
			return base64_encode(openssl_encrypt($data, "aes-256-cbc", $key, true, str_repeat(chr(0), 16)));

		}
	}
}


/*
 *  @desc 개인키로 데이터를 복호화한다.
 *  @param $ciphertext : 암호화값
 *  @return 복호화 값
 */
function masterDecrypt($ciphertext, $isRSA=0)
{

	if(trim($ciphertext)=='') {
		return false;
	}
	else {
		global $syndi_base_path;
		$private_key = file_get_contents(BSC_PATH . '/syndicate/finnq/keys/hello_rsa_pri.20180627.pem');  // 개인키
		$public_key  = file_get_contents(BSC_PATH . '/syndicate/finnq/keys/hello_rsa_pub.20180627.pem');	// 공개키

		if( $isRSA ) {
			$password = NULL;			// $password = 'this is a passphrase';

			$ciphertext = base64_decode($ciphertext, true);	// 암호문을 base64로 디코딩한다.
			if($ciphertext === false) return false;

			$privkey_decoded = openssl_pkey_get_private($private_key, $password); // 개인키를 사용하여 복호화한다.
			if($privkey_decoded === false) return false;

			$plaintext = false;
			$status = openssl_private_decrypt($ciphertext, $plaintext, $privkey_decoded);
			@openssl_pkey_free($privkey_decoded);
			if(!$status || $plaintext === false) return false;

			return $plaintext;		// 이상이 없는 경우 평문을 반환한다.

		}
		else {

			$key = hash('MD5', $private_key, true);
			$plaintext = openssl_decrypt(base64_decode($ciphertext), "aes-256-cbc", $key, true, str_repeat(chr(0), 16));
			return $plaintext;

		}
	}
}

?>