<?
include_once('./_common.php');
?>
<?
$evnt_idx=$_REQUEST["evnt_idx"];
$idxcnt = count($evnt_idx);

$cnt=0;
if ($idxcnt) {
	//for ($i=0; $i<$idxcnt; $i++) {
	foreach($evnt_idx as $key => $values) {
		//echo "$key -> $values"."<br/>";
		//echo "$evnt_idx[$key]<br/><br/>";
		$up_sql = "update cf_event_nhCMA set jigup='Y' where idx='$evnt_idx[$key]'";
		sql_query($up_sql);
		$cnt++;
	}
} else {
	echo "선택항목이 없습니다.";
	exit;
}

echo number_format($cnt)." 건 처리완료";
?>