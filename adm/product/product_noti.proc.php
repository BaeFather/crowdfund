<?

include_once('./_common.php');

while( list($k, $v) = each($_POST) ) { if(!is_array($k)) ${$k} = @trim($v); }

if( in_array($mode, array('new','edit')) ) {

	$subject = sql_real_escape_string($subject);
	$CONT = $_REQUEST['cont'];

	$cont_sql = "";
	for($i=0; $i<10; $i++) {
		if( trim($CONT[$i]) ) {
			$CONT[$i] = sql_real_escape_string(trim($CONT[$i]));
			$cont_sql.= ",cont{$i}='".$CONT[$i]."'";
		}
		else {
			$cont_sql.= ",cont{$i}=NULL";
		}
	}

}

if($mode=='new') {

	$sql = "
		INSERT INTO
			cf_sms_noti
		SET
			gubun   = '".$gubun."',
			subject = '".$subject."',
			member_idx = '".$member['mb_no']."',
			rdate = NOW()
			$cont_sql";
	if( sql_query($sql) ) {
		echo 'INSERT_OK';
	}
	else {
		echo 'INSERT_FAIL';
	}

}

else if($mode=='edit') {

	$DATA = sql_fetch("SELECT * FROM cf_sms_noti WHERE idx='".$idx."'");
	if($DATA['idx']) {
		if($gubun!=$DATA['gubun'] || $subject!=$DATA['subject'] ||
			 $cont0!=$CONT[0] || $cont1!=$CONT[1] || $cont2!=$CONT[2] || $cont3!=$CONT[3] || $cont4!=$CONT[4] ||
			 $cont5!=$CONT[5] || $cont6!=$CONT[6] || $cont7!=$CONT[7] || $cont8!=$CONT[8] || $cont9!=$CONT[9])
		{

			$sql = "
				UPDATE
					cf_sms_noti
				SET
					gubun   = '".$gubun."',
					subject = '".$subject."',
					last_editor = '".$member['mb_no']."',
					edate = NOW()
					$cont_sql
				WHERE
					idx='".$idx."'";
				//echo $sql;
			if( sql_query($sql) ) {
				echo 'EDIT_OK';
			}
			else {
				echo 'EDIT_FAIL';
			}

		}
		else {
			echo 'EDIT_NO_CHANGE';
		}
	}
	else {
		echo 'LOSTED_CONTENT';
	}

}

else if($mode=='delete') {

	$DATA = sql_fetch("SELECT * FROM cf_sms_noti WHERE idx='".$idx."'");
	if($DATA['idx']) {
		$sql = "
			UPDATE
				cf_sms_noti
			SET
				isFired = '1',
				last_editor = '".$member['mb_no']."',
				edate = NOW()
			WHERE
				idx='".$idx."'";
		if( sql_query($sql) ) {
			echo "DROP_OK";
		}
		else {
			echo "DROP_FAIL";
		}
	}
	else {
		echo 'LOSTED_CONTENT';
	}

}

sql_close();

?>