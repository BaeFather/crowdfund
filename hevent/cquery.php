<?php
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
//아이디 처리
?>
<?php
include_once('./_common.php');
include_once('../lib/function_prc.php');
?>
<?php
	$strPost = ARRAY(ARRAY("section","","Y"),ARRAY("page","","Y"));

	FOR($i=0;$i<COUNT($strPost);$i++)
	{
		IF($strPost[$i][1] > 0)
		{
			$strPostTarget = "";
			FOR($j=0;$j<COUNT($_POST[$strPost[$i][0]]);$j++)
			{
				$strPostVal = "";
				IF($j > 0)
				{
					$strPostTarget .=  ":";
					//${$strPost[$i][0]} .=  ",";
				}
				$strPostVal		 =& $_POST[$strPost[$i][0]][$j];
				$strPostTarget	.= replace_integer($strPostVal);
				//${$strPost[$i][0]} .= $_POST[$strPost[$i][0]][$j];
			}
			${$strPost[$i][0]} = $strPostTarget;

		} ELSE {
			IF($strPost[$i][2] == "Y")
			{
				IF($_POST[$strPost[$i][0]]<>"")
				{
					${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
				} ELSE {
					$objval = ARRAY("retcode"=>"X","retalert"=>STR_REPLACE("+"," ",urlencode("값이 올바르지 않습니다. 다시 시도하여 주십시오")),"retval"=>"");
					ECHO json_encode($objval);
					EXIT;
				}
			} ELSE {
				${$strPost[$i][0]} = $_POST[$strPost[$i][0]];
			}
		}
	}

	$strEventClass	=	new Event_Board();

	$strColumn	=	ARRAY(
								"idx", "title","sdate","edate","ifile","linkurl","target"
						);

	$strSearch = ARRAY("SC"=>"N");

	$page++;

	$rowList2 = $strEventClass->FnListFront($strSearch, $page, 6, $strColumn);
	$strContent = "";

	FOR($i=0;$i<COUNT($rowList2[2]);$i++)
	{
		unset($RowLink);

		FOR($j=0;$j<COUNT($strColumn);$j++)
		{
			${$strColumn[$j]} = $rowList2[2][$i][$j];
		}
		IF($linkurl)
		{
			$RowLink = $linkurl;
			$RowTarget = $target;
		} ELSE {
			$RowLink = "?RD=2&page=".$page."&SE=".$idx;
			$RowTarget = "_self";
		}

		IF($i > 0 && (($i+1) % 3 == 0))
		{
			$strClass ="evli evli2 last";
		} ELSE {
			$strClass ="evli evli2";
		}

		IF(G5_IS_MOBILE == true)
		{
			$strImg = $strEventClass->FnRepimg($ifile,2,"/data/fevent");
		} ELSE {
			$strImg = $strEventClass->FnRepimg($ifile,0,"/data/fevent");
		}

		$strContent .= "<li class='".$strClass."'>
				<a href='".$RowLink."' target='".$RowTarget."'>
				<ul class='ev_area'>
				<li class='evli_pic bgcl'>
				<img src='".$strImg."' class='listrepimg2' /></li>
				<li class='evli_txt2'>
					<ul class='ev_sarea'>
						<li class='evlis_txt1'>".$title."</li>
					</ul>
				</li>
			</ul>
		</li>";

	}

	$objval = ARRAY("retcode"=>"OK","page"=>$page,"rettotal"=>$rowList2[2],"retval"=>$strContent);
	ECHO json_encode($objval);
	sql_close($connect);
	EXIT;
?>