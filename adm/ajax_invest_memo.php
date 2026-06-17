<?

set_time_limit(0);

include_once("_common.php");

while(list($key, $value)=each($_REQUEST)) { ${$key} = trim($value); }

if($mode=="new") {

	$sql = "
		INSERT INTO
			cf_invest_memo
		SET
			product_idx = '".$product_idx."',
			comment = '".addSlashes($memo)."',
			mb_id = '".$member['mb_id']."',
			ip = '".$_SERVER['REMOTE_ADDR']."',
			rdate = NOW()";
	$sql = sql_query($sql);

}

if($mode=="delete") {
	sql_query("DELETE FROM cf_invest_memo WHERE idx='$idx'");
}

//print_r($_SESSION);

?>
<table>
<?
$sql  = "
	SELECT
		A.*,
		B.mb_name
	FROM
		cf_invest_memo A
	LEFT JOIN
		g5_member B  ON A.mb_id = B.mb_id
	WHERE
		A.product_idx='$product_idx'
	ORDER
		BY A.idx";
$res  = sql_query($sql);
$rows = $res->num_rows;
if($rows) {
	for($i=0; $i<$rows; $i++) {
		$LIST = sql_fetch_array($res);
		$comment = stripSlashes($LIST['comment']);

		$print_delete_button = ($LIST['mb_id']==$_SESSION['ss_mb_id']) ? '<span id="delete_btn" data-idx="'.$LIST['idx'].'" onClick="delMemo(\''.$LIST['idx'].'\');" style="margin-left:10px;font-size:12px;color:#ff6633;cursor:pointer">×</span>' : '';

?>
	<tr style="border-bottom:1px dotted #aaa">
		<td style="border:0; padding:10px 10px 0 10px;">
			<?=$LIST['mb_name']?>
			<span style='margin-left:30px;font-size:11px;color:#B2B2B2'><?=preg_replace("/-/", ".", substr($LIST['rdate'],0,16))?></span> <?=$print_delete_button?>
			<br />
			<pre style="border:0;background-color:#fff;font-size:12px;line-height:18px;"><xmp><?=$comment?></xmp></pre>
		</td>
	</tr>
<?
	}
}
else {
	//echo "<tr><td colspan='4' style='border:0; padding:10px 10px 0 10px;'>등록된 메모 없음.</td></tr>";
}
?>
</table>

<?
sql_close();
exit;
?>