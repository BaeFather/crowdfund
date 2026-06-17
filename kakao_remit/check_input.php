<?
//$jsonStr = file_get_contents("php://input"); //read the HTTP body.
//$kakao_res = json_decode($jsonStr, true);

include_once('../common.php');
include_once('../lib/common.lib.php');
include_once('../lib/sms.lib.php');

if (!$_GET['token']) die();
?>
<?
$chk_sql = "select count(*) chk_cnt from cf_kakao_remit where oid='$_GET[oid]'";
$chk_res = sql_query($chk_sql);
$chk_row = sql_fetch_array($chk_res);

if ($chk_row["chk_cnt"]==0) {

	$sql = "insert into cf_kakao_remit set
				mb_no           = '$member[mb_no]',
				mb_id           = '$member[mb_id]',
				token           = '$_GET[token]',
				insert_datetime = NOW()";

} else {

	$sql = "update cf_kakao_remit set 
				token = '$_GET[token]',
				modify_datetime = now()
				where oid='$_GET[oid]'";
	
}
//echo "$sql";
sql_query($sql);
?>
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
$.ajax({
	url : "remit_check.php",
	type: "POST",
	data : {'token':'<?=$_GET[token]?>','oid':'<?=$_GET[oid]?>'},

	success: function(res) {
		//alert(res);
		//console.log(res);
		self.location.href = "https://hellofunding.co.kr";
	},
	error: function () {
		//alert('네트워크 오류 입니다. 잠시 후 다시 시도하십시요.');
		self.location.href = "https://hellofunding.co.kr";
	}
});
</script>

<script>
<? if($member['mb_id']<>"romrom22222222222") {?>
//self.location.href = "https://hellofunding.co.kr";
<? } ?>
</script>

