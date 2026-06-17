<?php
$sub_menu = '800100';
include_once('./_common.php');

$data = $_POST;
/* sql 조합 */

FOR($i=0; $i<count($data['idx']); $i++)
{
   $recynOr = "";
   UNSET($recynArr);
	FOR($j=0;$j<COUNT($recyn);$j++)
  {
    $recynArr = EXPLODE("^",$recyn[$j]);

    IF($recynArr[0] == $idx[$i])
    {
      $recynOr = "Y";
      break;
    }
  }

	$sql = "
			UPDATE g5_kakao_userinfo SET
				`recyn` = '".$recynOr."' ,
				`content` = '".$data['content'][$i]."',
        `turl`  = '".$data['turl'][$i]."'
			WHERE
				`idx` = '".$data['idx'][$i]."' ;
			";

	//echo $sql."<br />";
	sql_query($sql);
}

alert("수정되었습니다.","./kakao_user_form.php");
?>
