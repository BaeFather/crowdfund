<?
// 탈퇴자 g5_withdrawal 에  mb_no 넣기  ---- 나중에 다시 작업할것
include_once("../../common.cli.php");

$sql = "
	SELECT
		A.mb_id, LEFT(regdate, 10) AS regdate
	FROM
		g5_withdrawal A
	WHERE
		A.mb_no = 0
	GROUP BY
		A.mb_id
	ORDER BY
		A.mb_id";
$res = sql_query($sql);
$rcnt = $res->num_rows;

for($i=0; $i<$rcnt; $i++) {

	$R = sql_fetch_array($res);

	$DMB = sql_fetch("
		SELECT
			AA.mb_no,
			(SELECT COUNT(mb_no) FROM g5_member_drop WHERE mb_id=AA.mb_id) AS cnt
		FROM
			g5_member_drop AA
		WHERE 1
			AND AA.mb_id='".$R['mb_id']."'
		GROUP BY
			AA.mb_id");

	if($DMB['cnt']==1) {
		$sql = "UPDATE g5_withdrawal SET mb_no='".$DMB['mb_no']."' WHERE mb_id='".$R['mb_id']."' AND mb_no='0'";
		sql_query($sql);
		echo $sql."<br>\n";
	}

}


?>