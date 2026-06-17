<?
// 처리
FUNCTION add_str($strVal)
{
	$strVal = addslashes(trim($strVal));
	return $strVal;
}

FUNCTION strip_str($strVal)
{
	$strVal = stripslashes($strVal);
	return $strVal;
}

FUNCTION fn_query_result($kind, $strColumn, $sql)
{
	global $connect;

	// 쿼리분기
	$result = sql_query($sql);

	IF($kind == "Count")
	{
		IF($row    = mysqli_fetch_array($result))
		{
			IF(COUNT($strColumn) == 1)
			{
				$retval = $row[$strColumn[0]];
			} ELSE {
				FOR($i=0;$i<COUNT($strColumn);$i++)
				{
					$retval[] =	$row[$strColumn[$i]];
				}
			}
			mysqli_free_result($result);
		}
	} ELSEIF($kind == "CountTxt")
	{
		// 문자열로 리턴
		IF($row    = mysqli_fetch_array($result))
		{
			IF(COUNT($strColumn) == 1)
			{
				$retval = $row[$strColumn[0]];
			} ELSE {
				FOR($i=0;$i<COUNT($strColumn);$i++)
				{
					$retval[$strColumn[$i]] =	$row[$strColumn[$i]];
				}
			}
			mysqli_free_result($result);
		}
	} ELSEIF($kind == "List") {

		$j = 0;
		WHILE($row    = mysqli_fetch_array($result))
		{
			FOR($i=0;$i<COUNT($strColumn);$i++)
			{
				$retval[$j][$strColumn[$i]] =	$row[$strColumn[$i]];
			}
			$j++;
		}
		IF($j > 0)
		{
			mysqli_free_result($result);
		}
	}

	return $retval;
}

FUNCTION fn_general_query_update($kind,$column,$cvalues,$tablename,$SEcolumn,$SE,$strWhere,$connect)
{
	IF($kind == "save")
	{
		UNSET($strColumnVal);
		UNSET($strCvaluesVal);

		IF(COUNT($column) <> COUNT($cvalues))
		{
			return "X";
		} ELSE {

			FOR($i=0;$i<COUNT($column);$i++)
			{
				IF($i == 0)
				{
					$strColumnVal = "(";
					$strCvaluesVal = "(";
				}
				IF($i > 0)
				{
					$strColumnVal .= ",";
					$strCvaluesVal .= ",";
				}
				$strColumnVal .= $column[$i];
				$strCvaluesVal .= "'".add_str($cvalues[$i])."'";

				IF($i == (COUNT($column)-1))
				{
					$strColumnVal .= ")";
					$strCvaluesVal .= ")";
				}
			}
		}
		$Query = "INSERT INTO ".$tablename." ".$strColumnVal." VALUES ".$strCvaluesVal;

		sql_query($Query,$connect);
		$INSERT_ID = mysql_insert_id();

		return $INSERT_ID;

	} ELSEIF($kind == "update") {

		UNSET($strColumnVal);
		UNSET($strCvaluesVal);

		IF(COUNT($column) <> COUNT($cvalues))
		{
			return "X";
		} ELSE {

			FOR($i=0;$i<COUNT($column);$i++)
			{
				IF($i > 0)
				{
					$strColumnVal .= ",";
				}
				//echo $column[$i]."---".$cvalues[$i]."----".COUNT($cvalues[$i])."<BR>";
				IF(COUNT($cvalues[$i]) <= 1)
				{
					$strColumnVal .= $column[$i]."='".add_str($cvalues[$i])."'";
				} ELSEIF(COUNT($cvalues[$i]) == 2) {
					$strColumnVal .= $column[$i]."=".$cvalues[$i][0]."+".$cvalues[$i][1]."";
				}
			}
		}

		IF(!$strWhere)
		{
		$Query = "UPDATE ".$tablename." SET ".$strColumnVal." WHERE ".$SEcolumn."='".$SE."'";
		} ELSE {
		$Query = "UPDATE ".$tablename." SET ".$strColumnVal." ".$strWhere;
		}

		sql_query($Query,$connect);

		return $SE;
	} ELSEIF($kind == "del") {

		IF(!$strWhere)
		{
			$Query = "DELETE FROM ".$tablename." WHERE ".$SEcolumn."='".$SE."'";
		} ELSE {
			$Query = "DELETE FROM ".$tablename." ".$strWhere;
		}
		sql_query($Query,$connect);

		return $SE;
	}
}

FUNCTION fr_board_view($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strLen,$connect)
{
	global $page;

	IF($frQuery)
	{
		$Query = "SELECT ".$frQuery." FROM ".$frTable." ".$frWhere." ORDER BY ".$frorder." LIMIT ".$frlimit1.",".$frlimit2;

	} ELSE {

		FOR($fri=0;$fri<COUNT($frField);$fri++)
		{
			IF($fri > 0)
			{
				$frFieldVal .= ",";
			}
			$frFieldVal .= $frField[$fri];
		}

		$Query = "SELECT ".$frFieldVal." FROM ".$frTable." ".$frWhere." ORDER BY ".$frorder." LIMIT ".$frlimit1.",".$frlimit2;
	}
	 echo $Query;
	$Result = sql_query($Query,$connect);
	$retVal = "";

	$i = 0;
	$FR			=	ARRAY();
	WHILE($Row=sql_fetch_array($Result))
	{
		FOR($fri=0;$fri<COUNT($frField);$fri++)
		{
			if($frField[$fri] == "title")
			{
				IF($strLen)
				{
					$FR[$i][$frField[$fri]] = strcut_utf8(strip_str($Row[$frField[$fri]]),$strLen,"","..");
				} ELSE {
					$FR[$i][$frField[$fri]] = strip_str($Row[$frField[$fri]]);
				}

			} elseif($frField[$fri] == "reg_date") {
				$FR[$i][$frField[$fri]] = SUBSTR($Row[$frField[$fri]],0,10);
			} ELSE {
				$FR[$i][$frField[$fri]] = $Row[$frField[$fri]];
			}

		}
		$i++;
	}


	IF($i > 0)
	{
		sql_free_result($Result);
	} ELSE {
		$FR = ARRAY("","");
	}

	return $FR;
}

FUNCTION fr_board_list($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen,$connect)
{
	global $page;
	global $num_per_page;
	global $strlen2;

	IF(!$strlen)
	{
		$strlen = 25;
	}


	$tQuery = "SELECT COUNT(*) as CNT FROM ".$frTable." ".$frWhere;
	$tResult = sql_query($tQuery,$connect);
	IF($Row=sql_fetch_array($tResult))
	{
		$frTotal = $Row["CNT"];
		sql_free_result($tResult);
	}

	$Frtotalpage = ceil($frTotal/$num_per_page);	//토탈페이지
	$frlimit1 = $num_per_page*($page-1);	//시작페이지

	IF(!$frlimit2) { $frlimit2 = $num_per_page; }

	IF($frQuery)
	{
		$Query = "SELECT ".$frQuery." FROM ".$frTable." ".$frWhere." ORDER BY ".$frorder." LIMIT ".$frlimit1.",".$frlimit2;

	} ELSE {

		FOR($fri=0;$fri<COUNT($frField);$fri++)
		{
			IF($fri > 0)
			{
				$frFieldVal .= ",";
			}
			$frFieldVal .= $frField[$fri];
		}

		$Query = "SELECT ".$frFieldVal." FROM ".$frTable." ".$frWhere." ORDER BY ".$frorder." LIMIT ".$frlimit1.",".$frlimit2;
	}
	//echo $Query;

	$Result = sql_query($Query,$connect);

	$i = 0;

	$FR			 = ARRAY();

	WHILE($Row=sql_fetch_array($Result))
	{
		FOR($fri=0;$fri<COUNT($frField);$fri++)
		{
			UNSET($frFieldArr);

			$frFieldArr	=	EXPLODE(".",$frField[$fri]);

			if(COUNT($frFieldArr) == 1)
			{
				$frFieldArr[1] = $frField[$fri];
			}

			if($frFieldArr[1] == "title")
			{
				IF($Row[$frFieldArr[1]])
				{
					if($strlen2)
					{
						$FR[$i][$fri] = strcut_utf8(strip_tags(strip_str($Row[$frFieldArr[1]])),($strlen2),"","");
					} else {
						$FR[$i][$fri] = strcut_utf8(strip_str($Row[$frFieldArr[1]]),$strlen,"","");
					}
				} ELSE {
					$FR[$i][$fri] = $Row[$frFieldArr[1]];
				}

			} elseif($frField[$fri] == "reg_date") {
				$FR[$i][$fri] = SUBSTR($Row[$frFieldArr[1]],0,10);
			} ELSE {
				$FR[$i][$fri] = strip_str($Row[$frFieldArr[1]]);
			}
		}
		$i++;
	}

	IF($i > 0)
	{
		sql_free_result($Result);
	} ELSE {
		$FR = "";
	}
	return ARRAY($Frtotalpage,$frTotal,$FR);
}
?>