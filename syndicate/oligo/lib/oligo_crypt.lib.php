<?

Class CryptoCBC
{
	public $linkurl;

	Public  function __construct()
	{
		$this->linkurl= "/home/crowdfund/public_html/syndicate/oligo/lib";
	}

	Public  function __destruct()
	{
	}

	function enCrypt($str)
	{
		$retval = EXEC("java -jar ".$this->linkurl."/crypt.jdk_1.7.jar -e ".$str);		// 상용서버용
	//$retval = EXEC("java -jar ".$this->linkurl."/crypt.jdk_1.8.jar -e ".$str);		// 테스트서버용
		$retval = EXPLODE(":",$retval);
		return $retval[1];
	}

	function deCrypt($str)
	{
		$retval = EXEC("java -jar ".$this->linkurl."/crypt.jdk_1.7.jar -d ".$str);		// 상용서버용
	//$retval = EXEC("java -jar ".$this->linkurl."/crypt.jdk_1.8.jar -d ".$str);		// 테스트서버용
		$retval = EXPLODE(":",$retval);
		return $retval[1];
	}
}

/*
// example
$crypto = new CryptoCBC();
echo $crypto->enCrypt("7402151702315");
echo "<BR><BR>";
echo $crypto->deCrypt("4F3600241F657F06B8954B73452598AA");
exit;
*/
?>