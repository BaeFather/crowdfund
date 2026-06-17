<?php
function alert($alert)
{
	echo"<script>alert('".$alert."');</script>";
	return;
}

function alert_close($alert)
{
	if($alert)
	{
		echo"<script>alert('".$alert."');self.close();</script>";
		return;
	} else {
		echo"<script>self.close();</script>";
		return;
	}
}

function alert_topclose($alert)
{
	if($alert)
	{
		echo"<script>alert('".$alert."');top.self.close();</script>";
		return;
	} else {
		echo"<script>top.self.close();</script>";
		return;
	}
}


function alert_confirm($alert,$url)
{
	echo "<Script>";
	echo " if(confirm('".$alert."')) { ";
	echo " window.location='".$url."'; }  ";
	echo "</Script>";
	return;
}

function alert_confirm_go($alert,$url,$url2)
{
	echo "<Script>";
	echo " if(confirm('".$alert."')) { ";
	echo " window.location='".$url."'; } else { window.location='".$url2."'; } ";
	echo "</Script>";
	return;
}

function alert_confirm_back($alert,$url,$goCnt)
{
	echo "<Script>";
	echo " if(confirm('".$alert."')) { ";
	echo " window.location='".$url."'; } else { history.go('".$goCnt."'); } ";
	echo "</Script>";
	return;
}

function alert_confirm_parent($alert,$url)
{
	echo"<script language='JavaScript'>";

	echo " if(confirm('".$alert."')) { ";
	echo " parent.location.href='".$url."'; }  ";
	echo "</Script>";
	return;
}

function alert_confirm_parent__($alert,$url)
{
	echo"<script language='JavaScript'>";
	IF($alert)
	{
		echo"alert('".$alert."');";
	}
	echo " parent.location.href='".$url."';";
	echo "</Script>";
	return;
}

function alert_confirm_parent_go($alert,$url,$url2)
{
	echo"<script language='JavaScript'>";

	echo " if(confirm('".$alert."')) { ";
	echo " parent.location.href='".$url."'; } else {  parent.location.href='".$url2."'; } ";
	echo "</Script>";
	return;
}

function alert_back($alert,$go)
{
	echo "<script>";
	if($alert)  { echo " alert('".$alert."');"; }
	if($go)		{ echo " history.go('".$go."');"; }
	echo "</script>";
	return;
}

function alert_opener($alert)
{
	echo"<script language='JavaScript'>";

	echo"alert('".$alert."');";
	echo"opener.document.location.reload();";
	echo"self.close();";
	echo"</script>";
	exit;
	return;
}

function alert_parent($alert)
{
	echo"<script language='JavaScript'>";

	echo"alert('".$alert."');";
	echo"parent.document.location.reload();";
	echo"</script>";
	exit;
	return;
}

function alert_delay_go($alert,$time,$url)
{
	echo"<script language='JavaScript'>";

	if($alert) {
	echo"alert('".$alert."');";
	}
	ECHO "</script>";
	ECHO "<meta http-equiv='refresh' content='".$time.";url=".$url."'>";
}


function alert_parent_reload_alert_no($alert)
{
	echo"<script language='JavaScript'>";

	echo"alert('".$alert."');";
	echo"window.parent.document.location.href = window.parent.document.URL;";
	echo"</script>";
	exit;
	return;
}

function alert_opener_reload_alert_no($alert)
{
	echo"<script language='JavaScript'>";

	echo"alert('".$alert."');";
	echo"window.opener.document.location.href = window.opener.document.URL;";
	echo"</script>";
	exit;
	return;
}

function alert_opener_go($alert,$url)
{
	echo"<script language='JavaScript'>";

	if($alert) {
	echo"alert('".$alert."');";
	}
	if($url) {
		echo "opener.window.location='".$url."';";
	} else { 
		echo"opener.document.location.reload();";
	}
	echo"self.close();";
	echo"</script>";
	exit;
	return;
}


function alert_pop_confirm($alert,$url)
{
	echo"<script language='JavaScript'>";

	echo"if(confirm('".$alert."')) {";
	echo"opener.window.location='".$url."'";
	echo"} else {";
	echo"opener.document.location.reload();";
	echo"}";
	echo"self.close();";
	echo"</script>";
	exit;
	return;
}

function alert_pop_confirm_noreplace($alert,$url)
{
	echo"<script language='JavaScript'>";

	echo"if(confirm('".$alert."')) {";
	echo"opener.window.location='".$url."'";
	echo"} ";
	echo"self.close();";
	echo"</script>";
	exit;
	return;
}


function alert_pop_confirm_go($alert,$url,$url2)
{
	echo"<script language='JavaScript'>";

	echo"if(confirm('".$alert."')) {";
	echo"opener.window.location='".$url."'";
	echo"} else {";
	echo"opener.window.location='".$url2."'";
	echo"}";
	echo"self.close();";
	echo"</script>";
	exit;
	return;
}

function gourl($alert,$gourl)
{
	if($alert)
	{
		echo "<Script>alert('".$alert."');window.location='".$gourl."';</Script>";
		return;
	} else {
		echo "<Script>window.location='".$gourl."';</Script>";
		return;
	}
}

function parent_gourl($alert,$gourl)
{
	echo"<script language='JavaScript'>";

	if($alert)
	{
		echo "alert('".$alert."');";
	} 
	echo "parent.location.href='".$gourl."';";
	echo"</Script>";
}

function top_gourl($alert,$gourl)
{
	echo"<script language='JavaScript'>";

	if($alert)
	{
		echo "alert('".$alert."');";
	} 
	echo "top.location.href='".$gourl."';";
	echo"</Script>";
}

function pagging_home($page,$total_page,$num_per_page,$urlVal)
{
	global $gstrPHPSELF;
	global $page_per_list;

	$total_block = ceil($total_page/$page_per_list);

	$block = ceil($page/$page_per_list);

	$first_page = ($block-1)*$page_per_list;
	$last_page = $block*$page_per_list;

	if($total_block <= $block) {
	   $last_page = $total_page;
	}

	$page_val = "<ul class='page'>";

	if($block > 1) {
		$page_first = $page-$page_per_list;

		if($page_first == 1) { $page_first = 1; }
		$page_val .= "<li class='page_arrow_l'><a href='$gstrPHPSELF?page=".$page_first.$urlVal."'>-</a></li>";
	} else {
		$page_val .= "<li class='page_arrow_l'><a href='javascript:;' OnClick=\"alert('이전블록이 없습니다.');\">-</a></li>";
	}

	for($i=$first_page+1;$i<$last_page+1;$i++)
	{

		if($i == $page) {
			$page_val .= "<li class='page_num p".sprintf("%02d",$i)." on'><a href='$gstrPHPSELF?page=".$i.$urlVal."'>".$i."</a></li>";
		} else {
			$page_val .= "<li class='page_num p".sprintf("%02d",$i)."'><a href='$gstrPHPSELF?page=".$i.$urlVal."'>".$i."</a></li>";
		}

	}

	if($block < $total_block) {
		$last_page = $page+$page_per_list;

		if($last_page > $total_page) { $last_page = $total_page; }

		$page_val.= "<li class='page_arrow_r'><a href='$gstrPHPSELF?page=".$last_page.$urlVal."'>-</a></li>";
	} else {
		$page_val .= "<li class='page_arrow_r'><a href='javascript:;'  OnClick=\"alert('다음블럭이 없습니다..');\">-</a></li>";
	}
	$page_val .= "</ul>";
	return $page_val;
}


function pagging_list__($page,$total_page,$num_per_page,$urlVal,$urlVal3_)
{
	global $PHP_SELF;
	global $img_url;

	$total_block = ceil($total_page/$num_per_page);

	$block = ceil($page/$num_per_page);

	$first_page = ($block-1)*$num_per_page;
	$last_page = $block*$num_per_page;

	if($total_block <= $block) {
	   $last_page = $total_page;
	}

	$img_url = "/images/page";

	$page_val = "<table class='pagelist1_sub'><tr><td><ul>";

	if($block > 1) {
		$page_first = $page-$num_per_page;

		if($page_first == 1) { $page_first = 1; }
		$page_val .= "<li><a href='$PHP_SELF?page=".$page_first.$urlVal."'><img src='".$img_url."/llbtn.jpg'></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/llbtn.jpg' style='cursor:hand' OnClick=\"alert('이전블록이 없습니다.');\"></li>";
	}

	if($page > 1) {
		$page_first = $page-1;
		$page_val .= "<li><a href='$PHP_SELF?page=".$page_first.$urlVal."'><img src='".$img_url."/llbtn1.jpg'></a></li>";

	} else {
		$page_val .= "<li><img src='".$img_url."/llbtn1.jpg' style='cursor:hand' OnClick=\"alert('이전페이지가 없습니다.');\"></li>";
	}

	for($i=$first_page+1;$i<$last_page+1;$i++)
	{
		$page_val.= "<li><a href='$PHP_SELF?page=".$i.$urlVal."'>";

		if($i == $page) {
			$page_val .= "<span class='list_this'>".$i."</span>";
		} else {
			$page_val .= $i;
		}
		$page_val .= "</a></li>";

		if ($i < $total_page)
			$page_val .= " <li><img src='".$img_url."/listbar.jpg'></li>";
		else 
			break;
	}

	if($page < $total_page) {
		$page_next = $page+1;

		$page_val.= "<li><a href='$PHP_SELF?page=".$page_next.$urlVal."'><img src='".$img_url."/rrbtn1.jpg'></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/rrbtn1.jpg' style='cursor:hand' OnClick=\"alert('다음페이지가 없습니다.');\"></li>";
	}

	if($block < $total_block) {
		$last_page = $page+$num_per_page;

		if($last_page > $total_page) { $last_page = $total_page; }

		$page_val.= "<li><a href='$PHP_SELF?page=".$last_page.$urlVal."'><img src='".$img_url."/rrbtn.jpg' ></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/rrbtn.jpg' style='cursor:hand' OnClick=\"alert('다음블럭이 없습니다..');\"></li>";
	}


	$page_val .= "</ul></td></tr></table>";

	return $page_val;
}


function pagging_list_s($page,$total_page,$num_per_page,$urlVal,$urlVal3_)
{
	global $PHP_SELF;
	global $img_url;

	$total_block = ceil($total_page/$num_per_page);

	$block = ceil($page/$num_per_page);

	$first_page = ($block-1)*$num_per_page;
	$last_page = $block*$num_per_page;

	if($total_block <= $block) {
	   $last_page = $total_page;
	}

	$img_url = "/images/page";

	$page_val = "<table class='pagelist1_sub'><tr><td><ul>";

	if($block > 1) {
		$page_first = $page-$num_per_page;

		if($page_first == 1) { $page_first = 1; }
		$page_val .= "<li><a href='$PHP_SELF?page_s=".$page_first.$urlVal."'><img src='".$img_url."/llbtn.jpg'></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/llbtn.jpg' style='cursor:hand' OnClick=\"alert('이전블록이 없습니다.');\"></li>";
	}

	if($page > 1) {
		$page_first = $page-1;
		$page_val .= "<li><a href='$PHP_SELF?page_s=".$page_first.$urlVal."'><img src='".$img_url."/llbtn1.jpg'></a></li>";

	} else {
		$page_val .= "<li><img src='".$img_url."/llbtn1.jpg' style='cursor:hand' OnClick=\"alert('이전페이지가 없습니다.');\"></li>";
	}

	for($i=$first_page+1;$i<$last_page+1;$i++)
	{
		$page_val.= "<li><a href='$PHP_SELF?page_s=".$i.$urlVal."'>";

		if($i == $page) {
			$page_val .= "<span class='list_this'>".$i."</span>";
		} else {
			$page_val .= $i;
		}
		$page_val .= "</a></li>";

		if ($i < $total_page)
			$page_val .= " <li><img src='".$img_url."/listbar.jpg'></li>";
		else 
			break;
	}

	if($page < $total_page) {
		$page_next = $page+1;

		$page_val.= "<li><a href='$PHP_SELF?page_s=".$page_next.$urlVal."'><img src='".$img_url."/rrbtn1.jpg'></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/rrbtn1.jpg' style='cursor:hand' OnClick=\"alert('다음페이지가 없습니다.');\"></li>";
	}

	if($block < $total_block) {
		$last_page = $page+$num_per_page;

		if($last_page > $total_page) { $last_page = $total_page; }

		$page_val.= "<li><a href='$PHP_SELF?page_s=".$last_page.$urlVal."'><img src='".$img_url."/rrbtn.jpg' ></a></li>";
	} else {
		$page_val .= "<li><img src='".$img_url."/rrbtn.jpg' style='cursor:hand' OnClick=\"alert('다음블럭이 없습니다..');\"></li>";
	}


	$page_val .= "</ul></td></tr></table>";

	return $page_val;
}



function money_trans($money)
{
	$money_len = strlen($money);

	if($money_len > 4) {
		for($i=0;$i<$money_len;$i++)
		{
			$money_val = substr($money,($money_len-1-$i),1);
	
			switch($i)
			{
				case "4" ; $money_sum = $money_val."억 ".$money_sum; break;
				default ; $money_sum = $money_val.$money_sum; break;
			}				
		}
	} else {
		$money_sum = $money;
	}

	$money_sum = $money_sum."만원";

	$money_sum = str_replace("억 0000만원","억",$money_sum);

	return $money_sum;
}

function ksubstr($str, $start, $end=1)
{
        $lenth = strlen($str);
        
        //$str을 $str_a에 한글자씩 저장
        $j=0;
        for($i=0;$i < $lenth;$i++){
                $tmp = substr($str,$i,1);
                if(ord($tmp) > 127){ //2바이트 문자라면
                        $str_a[$j] = substr($str,$i,2); //2바이트를 먹고
                        ++$i; //i값을 증가시킨다.
                }
                else{ $str_a[$j] = $tmp; }
                ++$j;
        }
        
        //시작에 따른 끝을 설정
        if($end > 0) $end += $start;
        else $end = sizeof($str_a) + $end;
        
        //$str_r에 $str_a[시작] 부터 끝까지 하나씩 붙여서 리턴
        for($i=$start;$i < $end;$i++){
                $str_r .= $str_a[$i];
        }
        
        return $str_r;
}


function date_diff2($start,$last)
{
	$_endDate = mktime(substr($last,11,2),substr($last,14,2),substr($last,17,2),substr($last,5,2),substr($last,8,2),substr($last,0,4));
	$_beginDate =  mktime(substr($start,11,2),substr($start,14,2),substr($start,17,2),substr($start,5,2),substr($start,8,2),substr($start,0,4));

	$timestamp_diff= $_endDate-$_beginDate + 1 ;
	$days_diff = $timestamp_diff/(86400);

	return floor($days_diff);
}

function date_ntime($year,$month, $day)
{
	$retval = mktime(0,0,0,$month,$day,$year);
	return $retval;
}

function date_ntime2($year,$month, $day, $hour, $min, $sec)
{
	$retval = mktime($hour,$min,$sec,$month,$day,$year);
	return $retval;
}

function number_cut($str,$len)
{
	if($len < 10) { 
		$len = "%0".$len."d"; 
	} else { 
		$len = "%".$len."d";
	}

	$str_val = sprintf("$len",$str);

	return $str_val;
}


function week_hangul($dtmDay)
{
	switch($dtmDay) 
	{
		case "0" : $dtmVal = "일"; break;
		case "1" : $dtmVal = "월"; break;
		case "2" : $dtmVal = "화"; break;
		case "3" : $dtmVal = "수"; break;
		case "4" : $dtmVal = "목"; break;
		case "5" : $dtmVal = "금"; break;
		case "6" : $dtmVal = "토"; break;
//		default : $dtmVal = ""; break;
	}
	return $dtmVal;
}


FUNCTION replace_si($val)
{
	SWITCH ($val)
	{

		CASE "서울특별시" : $val_return = "서울"; break;
		CASE "인천광역시" : $val_return = "인천"; break;
		CASE "광주광역시" : $val_return = "광주"; break;
		CASE "대전광역시" : $val_return = "대전"; break;
		CASE "대구광역시" : $val_return = "대구"; break;
		CASE "울산광역시" : $val_return = "울산"; break;
		CASE "부산광역시" : $val_return = "부산"; break;
		CASE "세종특별자치시" : $val_return = "세종"; break;
		CASE "경기도" : $val_return = "경기"; break;
		CASE "강원도" : $val_return = "강원"; break;
		CASE "충청북도" : $val_return = "충북"; break;
		CASE "충청남도" : $val_return = "충남"; break;
		CASE "전라북도" : $val_return = "전북"; break;
		CASE "전라남도" : $val_return = "전남"; break;
		CASE "경상북도" : $val_return = "경북"; break;
		CASE "경상남도" : $val_return = "경남"; break;
		CASE "제주특별자치도" : $val_return = "제주"; break;
		DEFAULT : $val_return = $val; break;
	}
	return $val_return;
}

FUNCTION check_si($val)
{
	SWITCH ($val)
	{
		CASE "서울" : $val_return = "A"; break;
		CASE "인천" : $val_return = "B"; break;
		CASE "광주" : $val_return = "C"; break;
		CASE "대전" : $val_return = "D"; break;
		CASE "대구" : $val_return = "E"; break;
		CASE "울산" : $val_return = "F"; break;
		CASE "부산" : $val_return = "G"; break;
		CASE "경기도" : $val_return = "H"; break;
		CASE "강원도" : $val_return = "I"; break;
		CASE "충청북도" : $val_return = "J"; break;
		CASE "충청남도" : $val_return = "K"; break;
		CASE "전라북도" : $val_return = "L"; break;
		CASE "전라남도" : $val_return = "M"; break;
		CASE "경상북도" : $val_return = "N"; break;
		CASE "경상남도" : $val_return = "O"; break;
		CASE "제주도" : $val_return = "P"; break;
		DEFAULT : $val_return = "X"; break;
	}
	return $val_return;
}

FUNCTION SDATE_FORM($strKind,$strName,$dtmDate)
{
	ECHO "<Select name='".$strName."'>";
	ECHO "	<Option value=\"\">-선택-</Option>";
	IF($strKind == "Y")
	{
		FOR($i=1930;$i<=DATE("Y");$i++)
		{
			ECHO "<Option value='".$i."' ";
			IF($dtmDate == $i) {
				ECHO " selected";
			} 
			ECHO ">".$i."</Option>";
		}
	} ELSEIF ($strKind == "M") {
		FOR($i=1;$i<13;$i++)
		{
			ECHO "<Option value='".$i."' ";
			IF($dtmDate == $i) {
				ECHO " selected";
			} 
			ECHO ">".$i."</Option>";
		}
	}
	EcHO "</Select>";
}


FUNCTION INPUT_FORM($strKind,$strName,$strClass,$strScript,$strVaribables,$strVal)
{
	SWITCH($strKind)
	{
		CASE "txt1" : 
			ECHO $strVal;
		BREAK;

		CASE "txt2" : 
			ECHO nl2br($strVal);
		BREAK;

		CASE "txt3" : 
			IF($strVal == $strScript)	{ ECHO $strVaribables; }
		BREAK;

		CASE "text" :
			ECHO "<INPUT TYPE='TEXT' name='".$strName."' ";
			IF($strVal) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
		BREAK;

		CASE "password" :
			ECHO "<INPUT TYPE='password' name='".$strName."' ";
			IF($strVal) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
		BREAK;

		CASE "checkbox" :
			ECHO "<INPUT TYPE='CHECKBOX' name='".$strName."' ";
			IF($strVal) { ECHO " VALUE='".$strVal."' "; }
			IF($strVal == $strScript)	{ ECHO " checked"; }
			IF($strClass) { ECHO " Class='".$strClass."'"; }
			ECHO ">";
			IF($strVaribables) { ECHO $strVaribables; }
		BREAK;

		CASE "radio" :
			ECHO "<INPUT TYPE='RADIO' name='".$strName."' ";
			IF($strVal) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
		BREAK;

		CASE "textarea" :
			ECHO "<TEXTAREA NAME='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO $strVal; }
			ECHO "</TEXTAREA>";
		BREAK;

		CASE "file_n" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='".$strName."_check' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"".$strName."_or\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "fileimg_n" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' width='150'></a> 파일삭제 : <input type='checkbox' name='".$strName."_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"".$strName."_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "file" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "file2" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> <input type='hidden' name='i_file_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "sfile" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' class='srepimg'></a> 파일삭제 : <input type='checkbox' name='s_file_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"s_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "pfile" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' class='srepimg'></a> 파일삭제 : <input type='checkbox' name='p_file_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"p_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "pmfile" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='pm_file_check[]' value=\"".$strVal."\">";  
			ECHO "<input type=\"hidden\" name=\"pm_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;


		CASE "fileImgM" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ID='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$gstrMainUrl."/".$strScript."' target='_blank'><img src='".$gstrMainUrl."/".$strVal."' border='0'></a>"; }
		BREAK;

		CASE "fileImg" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ID='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$gstrMainUrl."/".$strScript."' target='_blank'><img src='".$gstrMainUrl."/".$strVal."' border='0'></a> 이미지삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal.":".$strScript."\">"; 
			ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal.":".$strScript."\">";
			}
		BREAK;


		CASE "fileImgNew" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { 
				$strValArr = EXPLODE(".",$strVal);
				IF(strtolower($strValArr[1]) == "jpg" || strtolower($strValArr[1]) == "gif" || strtolower($strValArr[1]) == "png")
				{
				ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' border='0' width='200'></a> 이미지삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal."\">"; 
				ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
				} ELSE {
				ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal."\">";  
				ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
				}
			}
		BREAK;
	}
}

FUNCTION OTHER_FORM($strKind,$strKind2,$strVal,$strVal2,$strVal3,$strUrl)
{
	$connect_other = sql_conn();
	if($strKind == "addr")
	{
		if($strKind2 == "si")
		{
			$result = @mysql_query("SELECT SI FROM old_zipcode GROUP BY SI ORDER BY SI");
			echo "<SELECT name='si' OnChange=\"CheckHandle('si',this.value,'');\" style='width:70px'>";
			echo "	<option value=''>::시선택::</option>";
			WHILE($row = @mysql_fetch_array($result))
			{
				$SI		=	$row["SI"];
				echo "<option value='".urlencode($SI)."' ";
				if($SI == $strVal) { echo " SELECTED "; }
				echo ">".$SI."</option>";
			}
			echo "</SELECT>";
		}
		if($strKind2 == "gu")
		{
			echo "<SELECT name='gu' OnChange=\"CheckHandle('gu',this.value,document.all.si.value);\" style='width:110px'>";
			echo "	<option value=''>::구선택::</option>";
				$result = @mysql_query("SELECT GU FROM old_zipcode WHERE SI='".$strVal2."' GROUP BY GU ORDER BY GU");			
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["GU"];
					echo "<option value='".urlencode($GU)."' ";
					if($GU == $strVal) { echo " SELECTED "; }
					echo ">".$GU."</option>";

			}
			echo "</SELECT>"; 
		}
		if($strKind2 == "dong")
		{
			echo "<SELECT name='dong' style='width:150px'>";
			echo "	<option value=''>::동선택::</option>";
				$result = @mysql_query("select DONGSEQ from ZIPCODE where SIDO='".$strVal2."' AND SIGUN = '".$strVal3."' group by DONGSEQ");
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONG		=	$row["DONGSEQ"];
					echo "<option value='".$DONG."' ";
					if($DONG == $strVal) { echo " SELECTED "; }
					echo ">".$DONG."</option>";
			}
			echo "</SELECT>";
		}
	} elseif ($strKind == "subway") {

		if($strKind2 == "sub_area")
		{
			$result = @mysql_query("SELECT skind1 FROM ec_subway GROUP BY skind1 ORDER BY uid ASC");
			echo "<SELECT name='sub_area' OnChange=\"CheckHandle('sub_area',this.value,'');\">";
			echo "	<option value=''>::지 역::</option>";
			WHILE($row = @mysql_fetch_array($result))
			{
				$skind1		=	$row["skind1"];
				echo "<option value='".urlencode($skind1)."' ";
				if($skind1 == $strVal) { echo " SELECTED "; }
				echo ">".$skind1."</option>";
			}
			echo "</SELECT>";
		}
		if($strKind2 == "sub_num")
		{
			echo "<SELECT name='sub_num' OnChange=\"CheckHandle('sub_num',this.value,document.all.sub_area.value);\">";
			echo "	<option value=''>::호 선::</option>";
			if($strVal) {
				$result = @mysql_query("SELECT skind2 FROM ec_subway WHERE skind1='".$strVal2."' GROUP BY skind2 ORDER BY skind2");
				WHILE($row = @mysql_fetch_array($result))
				{
					$skind2		=	$row["skind2"];
					echo "<option value='".urlencode($skind2)."' ";
					if($skind2 == $strVal) { echo " SELECTED "; }
					echo ">".$skind2."</option>";
				}
			}
			echo "</SELECT>";
		}
		if($strKind2 == "sub_name")
		{
			echo "<SELECT name='sub_name'>";
			echo "	<option value=''>::역이름::</option>";
			if($strVal) {
				$result = @mysql_query("SELECT sval FROM ec_subway WHERE skind1='".$strVal2."' AND skind2='".$strVal3."' GROUP BY sval ORDER BY sval");
				WHILE($row = @mysql_fetch_array($result))
				{
					$sval		=	$row["sval"];
					echo "<option value='".$sval."' ";
					if($sval == $strVal) { echo " SELECTED "; }
					echo ">".$sval."</option>";
				}
			}
			echo "</SELECT>";
		}
	}
	sql_close($connect_other);
}

FUNCTION OTHER_FORM_MAIN($strKind,$strKind2,$strVal,$strVal2,$strVal3,$strUrl)
{
	$connect_other = sql_conn();
	if($strKind == "addr")
	{
		if($strKind2 == "si")
		{
			$result = @mysql_query("SELECT SI FROM zipcode GROUP BY SI ORDER BY SI");
			echo "<SELECT name='si' OnChange=\"CheckHandleMain('si',this.value,'');\" style='width:70px'>";
			echo "	<option value=''>::시선택::</option>";
			WHILE($row = @mysql_fetch_array($result))
			{
				$SI		=	$row["SI"];
				echo "<option value='".urlencode($SI)."' ";
				if($SI == $strVal) { echo " SELECTED "; }
				echo ">".$SI."</option>";
			}
			echo "</SELECT>";
		}
		if($strKind2 == "gu")
		{
			echo "<SELECT name='gu' OnChange=\"CheckHandleMain('gu',this.value,document.all.si.value);\" style='width:110px'>";
			echo "	<option value=''>::구선택::</option>";
				$result = @mysql_query("SELECT GU FROM zipcode WHERE SI='".$strVal2."' GROUP BY GU ORDER BY GU");			
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["GU"];
					echo "<option value='".urlencode($GU)."' ";
					if($GU == $strVal) { echo " SELECTED "; }
					echo ">".$GU."</option>";

			}
			echo "</SELECT>"; 
		}
		if($strKind2 == "dong")
		{
			echo "<SELECT name='dong' style='width:70px'>";
			echo "	<option value=''>::동선택::</option>";
				$result = @mysql_query("SELECT DONG FROM zipcode WHERE SI='".$strVal2."' AND GU='".$strVal3."' GROUP BY DONG ORDER BY DONG");
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONG		=	$row["DONG"];
					echo "<option value='".$DONG."' ";
					if($DONG == $strVal) { echo " SELECTED "; }
					echo ">".$DONG."</option>";
			}
			echo "</SELECT>";
		}
	}
	sql_close($connect_other);
}

FUNCTION INPUT_CHECK_FORM($strKind,$strName,$strClass,$strVaribables,$strVal)
{
	ECHO "<INPUT TYPE='TEXT' name='".$strName."' ";
			IF($strVal) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
	ECHO " <INPUT TYPE='BUTTON' NAME='".$strName."btn' VALUE='선택' OnClick=\"pop_button('".$strName."');\">";
}

FUNCTION FLASH($strWidth,$strHeight,$strUrl)
{
	ECHO "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"".$strWidth."\" height=\"".$strHeight."\"> 
					  <param name=\"wmode\" VALUE=\"transparent\">	
					  <param name=\"movie\" value=\"".$strUrl."\"> 
					  <param name=\"quality\" value=\"high\"> 
					  <embed src=\"".$strUrl."\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"".$strWidth."\" height=\"".$strHeight."\" name=\"wmode\" VALUE=\"transparent\"> 
					  </embed> 
			</object>";
}

FUNCTION FLASH2($strWidth,$strHeight,$strUrl)
{
	ECHO "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"".$strWidth."\" height=\"".$strHeight."\"> 
					  <param name=\"movie\" value=\"".$strUrl."\"> 
					  <param name=\"quality\" value=\"high\"> 
					  <embed src=\"".$strUrl."\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"".$strWidth."\" height=\"".$strHeight."\" name=\"wmode\" VALUE=\"transparent\"> 
					  </embed> 
			</object>";
}

FUNCTION FLASH_NEW($strWidth,$strHeight,$strFileName1,$strFileName2)
{
	$strUrl = $strFileName1.".".$strFileName2;

	ECHO "<script type=\"text/javascript\">
			AC_FL_RunContent( 'codebase','http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0','width','".$strWidth."','height','".$strHeight."','src','".$strFileName1."','quality','high','pluginspage','http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash','movie','".$strFileName1."' ); //end AC code
          </script>
		    <noscript>
		      <object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0\" width=\"".$strWidth."\" height=\"".$strHeight."\">
			  <param name=\"wmode\" VALUE=\"transparent\">	
              <param name=\"movie\" value=\"".$strUrl."\" />
              <param name=\"quality\" value=\"high\" />
              <embed src=\"".$strUrl."\" quality=\"high\" pluginspage=\"http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"".$strWidth."\" height=\"".$strHeight."\" name=\"wmode\" VALUE=\"transparent\"></embed>
            </object>
            </noscript>";
}

function Input_jumin($name){
	$print_string = "
					<input name='".$name."1' type='text' size='9' maxlength='6' style='ime-mode:disabled;border:1px;height:15px;margin:0 0 1px 0;border-color:#d8d8d8'  onkeyup='javascript:MovePointJumin(this);MakeFullJuminNumber(this);'>&nbsp;-&nbsp;<input name='".$name."2' type='password' size='10' maxlength='7' onkeyup='javascript:CheckJumin(this, \"write\");MakeFullJuminNumber(this);' style='ime-mode:disabled;border:1px;height:15px;margin:0 0 1px 0;font-size:11px;letter-spacing:-2px;border-color:#d8d8d8' >
					<input type='hidden' name='".$name."' value=''>
					";
	echo $print_string;
}

function Input_juminTo($name,$val1,$val2){
	$print_string = "
					<input name='".$name."1' type='text' size='8' maxlength='6' style='ime-mode:disabled;border:1px;border-color:#9a9a9a;height:15px;margin:0 0 1px 0;'  onkeyup='javascript:MovePointJumin(this);MakeFullJuminNumber(this);' value='".$val1."'>&nbsp;-&nbsp;<input name='".$name."2' type='password' size='10' maxlength='7' value='".$val2."' onkeyup='javascript:CheckJumin(this, \"write\");MakeFullJuminNumber(this);' style='ime-mode:disabled;border:1px;border-color:#9a9a9a;height:15px;margin:0 0 1px 0;font-size:11px;letter-spacing:-2px' >
					<input type='hidden' name='".$name."' value=''>
					";
	echo $print_string;
}

// 법인등록번호 부분
function Input_bubin($name){
	$print_string = "
					<input name='".$name."1' type='text' size='6' maxlength='6' style='ime-mode:disabled' onkeyup='javascript:MovePointBubin(this);MakeFullBubinNumber(this)'>&nbsp;-&nbsp;<input name='".$name."2' type='text' size='7' maxlength='7' onkeyup='javascript:CheckBubin(this, \"write\");MakeFullBubinNumber(this);'>
					<input type='hidden' name='".$name."' value=''>
					";
	echo $print_string;
}

// 사업자번호 부분
function Input_com($name){
	$print_string = "
					<input name='".$name."1' type='text' size='4' maxlength='3' style='ime-mode:disabled;border:1px;border-color:d8d8d8' onkeyup='javascript:MovePointCom1(this);MakeFullComNumber(this)'>&nbsp;-&nbsp;<input name='".$name."2' type='text' size='3' maxlength='2' style='ime-mode:disabled;border:1px;border-color:d8d8d8' onkeyup='javascript:MovePointCom2(this);MakeFullComNumber(this);'>&nbsp;-&nbsp;<input name='".$name."3' type='password' size='9' style='ime-mode:disabled;border:1px' style='font-size:10px;margin:0 0 2px 0;border-color:d8d8d8' maxlength='5' onkeyup='javascript:CheckCom(this, \"write\");MakeFullComNumber(this);'>
					<input type='hidden' name='".$name."' value=''>
					";
	echo $print_string;
}

function file_upload_default($strFileName,$strFileNameTemp,$strLand,$strWidth,$strHeight,$SaveDir,$dtmDate,$strOrFileName,$strKindDel,$filesizelen)
{
	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

	$SaveDir = $_SERVER["DOCUMENT_ROOT"].$SaveDir."/";

	IF(filesize($strFileNameTemp) > $filesizelen)
	{
		$strNewName = "file__";
	} ELSE {

		IF($strFileName)
		{
			if(filesize($strFileNameTemp) > $filesizelen)
			{
				
			}

			$strName = substr(strrchr($strFileName,"."),1);

			if($strOrFileName)
			{
				 //file_del($SaveDir."/".$strOrFileName);
				 $strNamenew = EXPLODE(".",$strOrFileName);
				 $strNewName = $strNamenew[0].".".strtolower($strName);
			} else {
				$strNumber = $dtmDate1_1.$dtmDate1_2.$strLand;
				$strNewName = $strNumber.".".strtolower($strName);
			}

			move_uploaded_file($strFileNameTemp, $SaveDir.$strNewName);

			IF(strtolower($strName) == "jpg" || strtolower($strName) == "gif" || strtolower($strName) == "png")
			{
				IF($strWidth && $strHeight)
				{
					$strNewName = new thumbnailImgOr($SaveDir.$strNewName,$strWidth,$strHeight,$SaveDir,$strNewName);
				}
			} 

		} else {
			if($strKindDel)
			{
				file_del($SaveDir."/".$strOrFileName);
				$strNewName = "";
			} else {
				if($strOrFileName)
				{
					 $strNewName = $strOrFileName;
				} else {
					 $strNewName = "";
				}
			}
		}
	}
	return $strNewName;
} 


function add_date($orgDate,$mth,$mdh){ 
	$cd = strtotime($orgDate); 
	$retDAY = date('Y-m-d H:i:s', mktime(date('H',$cd),date('i',$cd),date('s',$cd),date('m',$cd)+$mth,date('d',$cd)+$mdh,date('Y',$cd))); 
	return $retDAY; 
} 

function add_date_w($orgDate,$mth,$mdh){ 
	$cd = strtotime($orgDate); 
	$retDAY = date('w', mktime(date('H',$cd),date('i',$cd),date('s',$cd),date('m',$cd)+$mth,date('d',$cd)+$mdh,date('Y',$cd))); 
	return $retDAY; 
} 

function add_date_ymd($orgDate,$mth,$mdh){ 
	$cd = strtotime($orgDate); 
	$retDAY = date('Y-m-d', mktime(date('H',$cd),date('i',$cd),date('s',$cd),date('m',$cd)+$mth,date('d',$cd)+$mdh,date('Y',$cd))); 
	return $retDAY; 
} 


function Cha_date($startDate,$endDate)
{
	$date1 = strtotime($startDate); 
	$date2 = strtotime($endDate); 

	$dateSum = $date2 - $date1;
	return $dateSum;
}


function Check_ksubstr($length,$strVal)
{
	$length_to = $length * 2;

	if(strlen($strVal) > $length_to)
	{
		$strTitle = ksubstr($strVal,0,$length)."..";
	} else {
		$strTitle = $strVal;
	}
	return $strTitle;
}


FUNCTION NameEng($orName)
{
	$first_name_two = ARRAY("선우","서문","장곡","제갈","독고","남궁","사공","강전","동방","황보","소봉","망절");

	$first_name = Array(
	ARRAY("김","KIM"),ARRAY("이","LEE"),ARRAY("박","PARK"),ARRAY("최","CHOI"),ARRAY("정","JEONG"),
	ARRAY("강","KANG"),ARRAY("조","CHO"),ARRAY("윤","YOON"),ARRAY("장","JANG"),ARRAY("임","LIM"),
	ARRAY("한","HAN"),ARRAY("신","SHIN"),ARRAY("오","OH"),ARRAY("서","SEO"),ARRAY("권","KWON"),
	ARRAY("황","HWANG"),ARRAY("송","SONG"),ARRAY("안","AHN"),ARRAY("유","YOO"),ARRAY("류","RYOO"),
	ARRAY("홍","HONG"),ARRAY("전","JUN"),ARRAY("고","GOH"),ARRAY("문","MOON"),ARRAY("손","SHON"),
	ARRAY("양","YANG"),ARRAY("배","BAE"),ARRAY("백","BAEK"),ARRAY("허","HEO"),ARRAY("남","NAM"),
	ARRAY("심","SHIM"),ARRAY("노","NOH"),ARRAY("하","HA"),ARRAY("성","SUNG"),ARRAY("곽","KWAK"),
	ARRAY("차","CHA"),ARRAY("구","GOO"),ARRAY("우","WOO"),ARRAY("주","JOO"),ARRAY("나","NA"),
	ARRAY("민","MIN"),ARRAY("지","JI"),ARRAY("진","JIN"),ARRAY("엄","UHM"),ARRAY("원","WON"),
	ARRAY("채","CHAE"),ARRAY("천","CHEON"),ARRAY("방","BANG"),ARRAY("공","GONG"),ARRAY("현","HYEON"),
	ARRAY("함","HAM"),ARRAY("변","BYEON"),ARRAY("염","YEOM"),ARRAY("여","YEO"),ARRAY("연","YEON"),
	ARRAY("추","CHOO"),ARRAY("도","DO"),ARRAY("석","SEOK"),ARRAY("소","SOH"),ARRAY("설","SEOL"),
	ARRAY("선","SUN"),ARRAY("주","JOO"),ARRAY("길","GIL"),ARRAY("마","MA"),ARRAY("표","PYO"),
	ARRAY("위","WI"),ARRAY("명","MYUNG"),ARRAY("기","GI"),ARRAY("반","BAN"),ARRAY("왕","WANG"),
	ARRAY("금","GEUM"),ARRAY("옥","OAK"),ARRAY("육","YOOK"),ARRAY("인","IN"),ARRAY("맹","MAENG"),
	ARRAY("제","JE"),ARRAY("탁","TAK"),ARRAY("남궁","NAMGGOONG"),ARRAY("모","MO"),ARRAY("국","KOOK"),
	ARRAY("어","EO"),ARRAY("은","EUN"),ARRAY("편","PYEON"),ARRAY("용","YONG"),ARRAY("예","YE"),
	ARRAY("봉","BONG"),ARRAY("경","KYEONG"),ARRAY("사","SA"),ARRAY("부","BOO"),ARRAY("황보","HWANGBO"),
	ARRAY("가","GA"),ARRAY("태","TAE"),ARRAY("복","BOK"),ARRAY("목","MOK"),ARRAY("계","KYE"),ARRAY("피","PI"),
	ARRAY("형","HYEONG"),ARRAY("두","DOO"),ARRAY("감","GAM"),ARRAY("동","DONG"),ARRAY("음","EUM"),
	ARRAY("온","OHN"),ARRAY("제갈","JEGAL"),ARRAY("사공","SAGONG"),ARRAY("호","HO"),ARRAY("좌","JWA"),
	ARRAY("선우","SEONWOO"),ARRAY("갈","GAL"),ARRAY("범","BEOM"),ARRAY("빈","BIN"),ARRAY("팽","PAENG"),
	ARRAY("서문","SEOMOON"),ARRAY("승","SEUNG"),ARRAY("시","SI"),ARRAY("상","SANG"),ARRAY("간","GAN"),
	ARRAY("화","HWA"),ARRAY("단","DAN"),ARRAY("견","KYEON"),ARRAY("순","SOON"),ARRAY("당","DANG"),
	ARRAY("창","CHANG"),ARRAY("독고","DOKGO"),ARRAY("옹","ONG"),ARRAY("평","PYEONG"),ARRAY("종","JONG"),
	ARRAY("섭","SEOB"),ARRAY("묵","MOOK"),ARRAY("궁","GOONG"),ARRAY("대","DAE"),ARRAY("빙","BING"),
	ARRAY("근","KEUN"),ARRAY("풍","POONG"),ARRAY("영","YEONG"),ARRAY("낭","NANG"),ARRAY("아","AH"),
	ARRAY("내","NAE"),ARRAY("만","MAN"),ARRAY("해","HAE"),ARRAY("궉","KWOK"),ARRAY("포","PO"),
	ARRAY("판","PAN"),ARRAY("초","CHO"),ARRAY("매","MAE"),ARRAY("군","GOON"),ARRAY("요","YO"),
	ARRAY("필","PIL"),ARRAY("점","JEOM"),ARRAY("곡","GOK"),ARRAY("동방","DONGBANG"),ARRAY("개","GAE"),
	ARRAY("미","MI"),ARRAY("준","JOON"),ARRAY("수","SOO"),ARRAY("야","YA"),ARRAY("자","JA"),
	ARRAY("운","WOON"),ARRAY("뇌","NOI"),ARRAY("돈","DON"),ARRAY("탄","TAN"),ARRAY("삼","SAM"),
	ARRAY("애","AE"),ARRAY("후","HOO"),ARRAY("비","BI"),ARRAY("난","NAN"),ARRAY("묘","MYO"),
	ARRAY("교","KYO"),ARRAY("학","HAK"),ARRAY("망절","MANGJEOL"),ARRAY("십","SIB"),ARRAY("흥","HEUNG"),
	ARRAY("춘","CHUN"),ARRAY("누","NOO"),ARRAY("저","JEO"),ARRAY("강전","KANGJEON"),ARRAY("소봉","SOBONG"),
	ARRAY("장곡","JANGGOK"),ARRAY("즙","JEUP"));

	$last_name = Array(
	array("가","ga"),array("각","gak"),array("간","gan"),array("갈","gal"),array("감","gam"),
	array("갑","gap"),array("갓","gat"),array("강","gang"),array("개","gae"),array("객","gaek"),
	array("거","geo"),array("건","geon"),array("걸","geol"),array("검","gum"),array("겁","gup"),
	array("게","ge"),array("겨","gyeo"),array("격","gyeok"),array("견","gyeon"),array("결","gyeol"),
	array("겸","gyeom"),array("겹","gyeop"),array("경","gyung"),array("계","gye"),array("고","go"),
	array("곡","gok"),array("곤","gon"),array("골","gol"),array("곳","got"),array("공","gong"),
	array("곶","got"),array("과","gwa"),array("곽","gwak"),array("관","gwan"),array("괄","gwal"),
	array("광","kwang"),array("괘","gwae"),array("괴","goe"),array("굉","goeng"),array("교","gyo"),
	array("구","goo"),array("국","guk"),array("군","gun"),array("굴","gul"),array("굿","gut"),
	array("궁","gung"),array("권","gwon"),array("궐","gwol"),array("궤","gwe"),array("귀","gwi"),
	array("규","gyoo"),array("균","gyoon"),array("귤","gyool"),array("그","geu"),array("극","geuk"),
	array("근","geun"),array("글","geul"),array("금","geum"),array("급","geup"),array("긍","geung"),
	array("기","ki"),array("긴","gin"),array("길","gil"),array("김","gim"),array("까","kka"),
	array("깨","kkae"),array("꼬","kko"),array("꼭","kkok"),array("꽃","kkot"),array("꾀","kkoe"),
	array("꾸","kku"),array("꿈","kkum"),array("끝","kkeut"),array("끼","kki"),array("나","na"),
	array("낙","nak"),array("난","nan"),array("날","nal"),array("남","nam"),array("납","nap"),
	array("낭","nang"),array("내","nae"),array("냉","naeng"),array("너","neo"),array("널","neol"),
	array("네","ne"),array("녀","nyeo"),array("녁","nyeok"),array("년","nyeon"),array("념","nyeom"),
	array("녕","nyung"),array("노","no"),array("녹","nok"),array("논","non"),array("놀","nol"),
	array("농","nong"),array("뇌","noe"),array("누","nu"),array("눈","nun"),array("눌","nul"),
	array("느","neu"),array("늑","neuk"),array("늠","neum"),array("능","neung"),array("늬","nui"),
	array("니","nee"),array("닉","nik"),array("닌","nin"),array("닐","nil"),array("님","nim"),
	array("다","da"),array("단","dan"),array("달","dal"),array("담","dam"),array("답","dap"),
	array("당","dang"),array("대","dae"),array("댁","daek"),array("더","deo"),array("덕","deok"),
	array("도","do"),array("독","dok"),array("돈","don"),array("돌","dol"),array("동","dong"),
	array("돼","dwae"),array("되","doe"),array("된","doen"),array("두","doo"),array("둑","dook"),
	array("둔","doon"),array("뒤","dwi"),array("드","deu"),array("득","deuk"),array("들","deul"),
	array("등","deung"),array("듸","dui"),array("디","di"),array("따","tta"),array("땅","ttang"),
	array("때","ttae"),array("또","tto"),array("뚜","ttu"),array("뚝","ttuk"),array("뜨","tteu"),
	array("띠","tti"),array("라","ra"),array("락","rak"),array("란","ran"),array("람","ram"),
	array("랑","rang"),array("래","rae"),array("랭","raeng"),array("량","ryang"),array("렁","reong"),
	array("레","re"),array("려","ryeo"),array("력","ryeok"),array("련","ryun"),array("렬","ryul"),
	array("렴","ryeom"),array("렵","ryeop"),array("령","ryung"),array("례","rye"),array("로","ro"),
	array("록","rok"),array("론","ron"),array("롱","rong"),array("뢰","roe"),array("료","ryo"),
	array("룡","ryong"),array("루","ru"),array("류","ryu"),array("륙","ryuk"),array("륜","ryun"),
	array("률","ryul"),array("륭","ryung"),array("르","reu"),array("륵","reuk"),array("른","reun"),
	array("름","reum"),array("릉","reung"),array("리","ree"),array("린","rin"),array("림","rim"),
	array("립","rip"),array("마","ma"),array("막","mak"),array("만","man"),array("말","mal"),
	array("망","mang"),array("매","mae"),array("맥","maek"),array("맨","maen"),array("맹","maeng"),
	array("머","meo"),array("먹","meok"),array("메","me"),array("며","myeo"),array("멱","myeok"),
	array("면","myeon"),array("멸","myeol"),array("명","myung"),array("모","mo"),array("목","mok"),
	array("몰","mol"),array("못","mot"),array("몽","mong"),array("뫼","moe"),array("묘","myo"),
	array("무","moo"),array("묵","mook"),array("문","moon"),array("물","mool"),array("므","meu"),
	array("미","mi"),array("민","min"),array("밀","mil"),array("바","ba"),array("박","bak"),
	array("반","ban"),array("발","bal"),array("밥","bap"),array("방","bang"),array("배","bae"),
	array("백","baek"),array("뱀","baem"),array("버","beo"),array("번","beon"),array("벌","beol"),
	array("범","beom"),array("법","beop"),array("벼","byeo"),array("벽","byeok"),array("변","byeon"),
	array("별","byul"),array("병","byung"),array("보","bo"),array("복","bok"),array("본","bon"),
	array("봉","bong"),array("부","bu"),array("북","buk"),array("분","bun"),array("불","bul"),
	array("붕","boong"),array("비","bee"),array("빈","bin"),array("빌","bil"),array("빔","bim"),
	array("빙","bing"),array("빠","ppa"),array("빼","ppae"),array("뻐","ppeo"),array("뽀","ppo"),
	array("뿌","ppu"),array("쁘","ppeu"),array("삐","ppi"),array("사","sa"),array("삭","sak"),
	array("산","san"),array("살","sal"),array("삼","sam"),array("삽","sap"),array("상","sang"),
	array("샅","sat"),array("새","sae"),array("색","saek"),array("생","saeng"),array("서","seo"),
	array("석","seok"),array("선","seon"),array("설","seol"),array("섬","seom"),array("섭","seop"),
	array("성","seong"),array("세","se"),array("셔","syeo"),array("소","so"),array("속","sok"),
	array("손","son"),array("솔","sol"),array("솟","sot"),array("송","song"),array("쇄","swae"),
	array("쇠","soe"),array("수","soo"),array("숙","sook"),array("순","soon"),array("술","sool"),
	array("숨","soom"),array("숭","soong"),array("쉬","swi"),array("스","seu"),array("슬","seul"),
	array("슴","seum"),array("습","seup"),array("승","seung"),array("시","see"),array("식","sik"),
	array("신","sin"),array("실","sil"),array("심","sim"),array("십","sip"),array("싱","sing"),
	array("싸","ssa"),array("쌍","ssang"),array("쌔","ssae"),array("쏘","sso"),array("쑥","ssook"),
	array("씨","ssi"),array("아","a"),array("악","ak"),array("안","an"),array("알","al"),
	array("암","am"),array("압","ap"),array("앙","ang"),array("앞","ap"),array("애","ae"),
	array("액","aek"),array("앵","aeng"),array("야","ya"),array("약","yak"),array("얀","yan"),
	array("양","yang"),array("어","eo"),array("억","eok"),array("언","eon"),array("얼","eol"),
	array("엄","eom"),array("업","eop"),array("에","e"),array("여","yeo"),array("역","yeok"),
	array("연","yun"),array("열","yeol"),array("염","yeom"),array("엽","yeop"),array("영","young"),
	array("예","ye"),array("오","oh"),array("옥","ock"),array("온","on"),array("올","ol"),
	array("옴","om"),array("옹","ong"),array("와","wa"),array("완","wan"),array("왈","wal"),
	array("왕","wang"),array("왜","wae"),array("외","oe"),array("왼","oen"),array("요","yo"),
	array("욕","yok"),array("용","yong"),array("우","woo"),array("욱","wook"),array("운","woon"),
	array("울","wool"),array("움","woom"),array("웅","woong"),array("워","wo"),array("원","won"),
	array("월","wol"),array("위","wi"),array("유","yoo"),array("육","yook"),array("윤","yoon"),
	array("율","yool"),array("융","yoong"),array("윷","yoot"),array("으","eu"),array("은","eun"),
	array("을","eul"),array("음","eum"),array("읍","eup"),array("응","eung"),array("의","ui"),
	array("이","ee"),array("익","ik"),array("인","in"),array("일","il"),array("임","im"),
	array("입","ip"),array("잉","ing"),array("자","ja"),array("작","jak"),array("잔","jan"),
	array("잠","jam"),array("잡","jap"),array("장","jang"),array("재","jae"),array("쟁","jaeng"),
	array("저","jeo"),array("적","juk"),array("전","jun"),array("절","jul"),array("점","jum"),
	array("접","jup"),array("정","jung"),array("제","je"),array("조","jo"),array("족","jok"),
	array("존","jon"),array("졸","jol"),array("종","jong"),array("좌","jwa"),array("죄","joe"),
	array("주","ju"),array("죽","jook"),array("준","joon"),array("줄","jool"),array("중","joong"),
	array("쥐","jwi"),array("즈","jeu"),array("즉","jeuk"),array("즐","jeul"),array("즘","jeum"),
	array("즙","jeup"),array("증","jeung"),array("지","ji"),array("직","jik"),array("진","jin"),
	array("질","jil"),array("짐","jim"),array("집","jip"),array("징","jing"),array("짜","jja"),
	array("째","jjae"),array("쪼","jjo"),array("찌","jji"),array("차","cha"),array("착","chak"),
	array("찬","chan"),array("찰","chal"),array("참","cham"),array("창","chang"),array("채","chae"),
	array("책","chaek"),array("처","cheo"),array("척","cheok"),array("천","chun"),array("철","chul"),
	array("첨","cheom"),array("첩","cheop"),array("청","chung"),array("체","che"),array("초","cho"),
	array("촉","chok"),array("촌","chon"),array("총","chong"),array("최","choe"),array("추","choo"),
	array("축","chook"),array("춘","choon"),array("출","chool"),array("춤","choom"),array("충","choong"),
	array("측","cheuk"),array("층","cheung"),array("치","chee"),array("칙","chik"),array("친","chin"),
	array("칠","chil"),array("침","chim"),array("칩","chip"),array("칭","ching"),array("칩","chip"),
	array("칭","ching"),array("코","ko"),array("쾌","kwae"),array("크","keu"),array("큰","keun"),
	array("키","kee"),array("타","ta"),array("탁","tak"),array("탄","tan"),array("탈","tal"),
	array("탐","tam"),array("탑","tap"),array("탕","tang"),array("태","tae"),array("택","taek"),
	array("탱","taeng"),array("터","teo"),array("테","te"),array("토","to"),array("톤","ton"),
	array("톨","tol"),array("통","tong"),array("퇴","toe"),array("투","too"),array("퉁","toong"),
	array("튀","twi"),array("트","teu"),array("특","teuk"),array("틈","teum"),array("티","tee"),
	array("파","pa"),array("판","pan"),array("팔","pal"),array("패","pae"),array("팽","paeng"),
	array("퍼","peo"), array("페","pe"),array("펴","pyeo"),array("편","pyun"),array("폄","pyum"),
	array("평","pyung"),array("폐","pye"),array("포","po"),array("폭","pok"),array("표","pyo"),
	array("푸","poo"),array("품","poom"),array("풍","poong"),array("프","peu"),array("피","pee"),
	array("픽","pik"),array("필","pil"),array("핍","pip"),array("하","ha"),array("학","hak"),
	array("한","han"),array("할","hal"),array("함","ham"),array("합","hap"),array("항","hang"),
	array("해","hae"),array("핵","haek"),array("행","haeng"),array("향","hyang"),array("허","heo"),
	array("헌","hun"),array("험","hum"),array("헤","he"),array("혀","hyeo"),array("혁","hyuk"),
	array("현","hyun"),array("혈","hyul"),array("혐","hyum"),array("협","hyup"),array("형","hyung"),
	array("혜","hye"),array("호","ho"),array("혹","hok"),array("혼","hon"),array("홀","hol"),
	array("홉","hop"),array("홍","hong"),array("화","hwa"),array("확","hwak"),array("환","hwan"),
	array("활","hwal"),array("황","hwang"),array("홰","hwae"),array("횃","hwaet"),array("회","hoe"),
	array("획","hoek"),array("횡","hoeng"),array("효","hyo"),array("후","hoo"),array("훈","hoon"),
	array("훤","hwon"),array("훼","hwe"),array("휘","hwi"),array("휴","hyoo"),array("휼","hyul"),
	array("흉","hyung"),array("흐","heu"),array("흑","heuk"),array("흔","heun"),array("흘","heul"),
	array("흠","heum"),array("흡","heup"),array("흥","heung"),array("희","hee"),array("흰","huin"),
	array("히","hi"),array("힘","him"));

	$nameTrue = false;

	FOR($i=0;$i<COUNT($first_name_two);$i++)
	{
		IF(ksubstr($orName,0,2) == $first_name_two[$i])
		{
			$nameTrue = true;
		}
	}

	IF($nameTrue == true)
	{
		$FristName = ksubstr($orName,0,2);
		$LastName = ksubstr($orName,2,10);
	} else {
		$FristName = ksubstr($orName,0,1);
		$LastName = ksubstr($orName,1,10);
	}

	$LastNamelength = strlen($LastName)/2;


	FOR($i=0;$i<COUNT($first_name);$i++)
	{
		IF($first_name[$i][0] == $FristName)
		{
			$OrFirstName = $first_name[$i][1];
			break;
		}
	}

	FOR($j=0;$j<$LastNamelength;$j++)
	{
		if($j > 0)
		{
			$OrLastName .= "-";
		}
		FOR($i=0; $i<COUNT($last_name);$i++)
		{
			IF($last_name[$i][0] == ksubstr($LastName,$j,1))
			{
				$OrLastName = $OrLastName.$last_name[$i][1];
				break;
			}
		}
	}

	return $OrFirstName." ".$OrLastName;
}

function address_sell_si($obj)
{
	FOR($i=0;$i<$obj;$i++)
	{
		$strVal .= "<div style='margin:0 0 5px 0;'><SELECT name='si[]' OnChange=\"CheckHandleAddr('1','si[]',this.value,'','".$i."')\">
					<option value=''>::시선택::</option>
					<option value='서울' >서울</option>
						<option value='경기'>경기</option>
						<option value='인천'>인천</option>
						<option value='강원'>강원</option>
						<option value='광주'>광주</option>
						<option value='대전'>대전</option>
						<option value='대구'>대구</option>
						<option value='부산'>부산</option>
						<option value='울산'>울산</option>
						<option value='충븍'>충븍</option>
						<option value='충남'>충남</option>
						<option value='전북'>전북</option>
						<option value='전남'>전남</option>
						<option value='경북'>경북</option>
						<option value='경남'>경남</option>
						<option value='제주'>제주</option>
				   </SELECT> 
				   <SELECT name='gu[]' id='gu[]' OnChange=\"CheckHandleAddr('1','gu[]',this.value,document.getElementsByName('si[]')[".$i."].value,'".$i."');\">	<option value=''>::구선택::</option></SELECT>
				    <SELECT name='dong[]' id='dong[]'><option value=''>::동선택::</option></SELECT>";

		$strVal .= " <input type='text' name='area_content[]' style='width:300px'>";					
		$strVal .= "</div>";
	}
	return $strVal;
}

function address_sell_si_member($obj)
{
	FOR($i=0;$i<$obj;$i++)
	{
		$strVal .= "<SELECT name='si[]' style='border-width:1px; border-color:#9a9a9a;' OnChange=\"CheckHandleAddr('1','si[]',this.value,'','".$i."')\">
					<option value=''>::시선택::</option>
					<option value='서울' >서울</option>
						<option value='경기'>경기</option>
						<option value='인천'>인천</option>
						<option value='강원'>강원</option>
						<option value='광주'>광주</option>
						<option value='대전'>대전</option>
						<option value='대구'>대구</option>
						<option value='부산'>부산</option>
						<option value='울산'>울산</option>
						<option value='충븍'>충븍</option>
						<option value='충남'>충남</option>
						<option value='전북'>전북</option>
						<option value='전남'>전남</option>
						<option value='경북'>경북</option>
						<option value='경남'>경남</option>
						<option value='제주'>제주</option>
				   </SELECT> 
				   <SELECT name='gu[]' id='gu[]' style='border-width:1px; border-color:#9a9a9a;' OnChange=\"CheckHandleAddr('1','gu[]',this.value,document.getElementsByName('si[]')[".$i."].value,'".$i."');\">	<option value=''>::구선택::</option></SELECT>
				    <SELECT name='dong[]' id='dong[]' style='border-width:1px; border-color:#9a9a9a;'><option value=''>::동선택::</option></SELECT>";

		$strVal .= " <input type='text' name='area_content[]' style='width:300px;border-width:1px; border-color:#9a9a9a;'>";					
	}
	return $strVal;
}


function address_sell_si_member2($obj)
{
	FOR($i=0;$i<$obj;$i++)
	{
		$strVal .= "<SELECT name='si2[]' style='border-width:1px; border-color:#9a9a9a;' OnChange=\"CheckHandleAddr('2','si2[]',this.value,'','".$i."')\">
					<option value=''>::시선택::</option>
					<option value='서울' >서울</option>
						<option value='경기'>경기</option>
						<option value='인천'>인천</option>
						<option value='강원'>강원</option>
						<option value='광주'>광주</option>
						<option value='대전'>대전</option>
						<option value='대구'>대구</option>
						<option value='부산'>부산</option>
						<option value='울산'>울산</option>
						<option value='충븍'>충븍</option>
						<option value='충남'>충남</option>
						<option value='전북'>전북</option>
						<option value='전남'>전남</option>
						<option value='경북'>경북</option>
						<option value='경남'>경남</option>
						<option value='제주'>제주</option>
				   </SELECT> 
				   <SELECT name='gu2[]' id='gu2[]' style='border-width:1px; border-color:#9a9a9a;' OnChange=\"CheckHandleAddr('2','gu2[]',this.value,document.getElementsByName('si2[]')[".$i."].value,'".$i."');\">	<option value=''>::구선택::</option></SELECT>
				    <SELECT name='dong2[]' id='dong2[]' style='border-width:1px; border-color:#9a9a9a;'><option value=''>::동선택::</option></SELECT>";

		$strVal .= " <input type='text' name='area_content2[]' style='width:300px;border-width:1px; border-color:#9a9a9a;'>";					
	}
	return $strVal;
}


function address_buy_si($obj,$add_si,$add_gu,$add_dg,$connect)
{
	$add_siVal = EXPLODE("^",$add_si);
	$add_guVal = EXPLODE("^",$add_gu);
	$add_dgVal = EXPLODE("^",$add_dg);

	FOR($i=0;$i<$obj;$i++)
	{
		$strVal .= "<div style='margin:0 0 5px 0;'>
					의뢰".($i+1)."지역 : <SELECT name='si2[]' OnChange=\"CheckHandleAddr('2','si2[]',this.value,'','".$i."')\">
						<option value=''>::시선택::</option>
						<option value='서울' ";
		IF($add_siVal[$i] == "서울") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">서울</option>";
		$strVal  .= "<option value='경기' ";
		IF($add_siVal[$i] == "경기") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경기</option>";
		$strVal  .= "<option value='인천' ";
		IF($add_siVal[$i] == "인천") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">인천</option>";
		$strVal  .= "<option value='강원' ";
		IF($add_siVal[$i] == "강원") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">강원</option>";
		$strVal  .= "<option value='광주' ";
		IF($add_siVal[$i] == "광주") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">광주</option>";
		$strVal  .= "<option value='대전' ";
		IF($add_siVal[$i] == "대전") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">대전</option>";
		$strVal  .= "<option value='대구' ";
		IF($add_siVal[$i] == "대구") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">대구</option>";
		$strVal  .= "<option value='부산' ";
		IF($add_siVal[$i] == "부산") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">부산</option>";
		$strVal  .= "<option value='울산' ";
		IF($add_siVal[$i] == "울산") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">울산</option>";
		$strVal  .= "<option value='충븍' ";
		IF($add_siVal[$i] == "충븍") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">충븍</option>";
		$strVal  .= "<option value='충남' ";
		IF($add_siVal[$i] == "충남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">충남</option>";
		$strVal  .= "<option value='전북' ";
		IF($add_siVal[$i] == "전북") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">전북</option>";
		$strVal  .= "<option value='전남' ";
		IF($add_siVal[$i] == "전남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">전남</option>";
		$strVal  .= "<option value='경북' ";
		IF($add_siVal[$i] == "경북") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경북</option>";
		$strVal  .= "<option value='경남' ";
		IF($add_siVal[$i] == "경남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경남</option>";
		$strVal  .= "<option value='제주' ";
		IF($add_siVal[$i] == "제주") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">제주</option>
				   </SELECT> ";

		$strVal	 .= "<SELECT name='gu2[]' OnChange=\"CheckHandleAddr('2','gu2[]',this.value,document.getElementsByName('si2[]')[".$i."].value,'".$i."');\">";
		$strVal	 .= " <option value=''>::구선택::</option>";
			if($add_siVal[$i]) {
				$result = @mysql_query("SELECT GU FROM zipcode WHERE SI='".$add_siVal[$i]."' GROUP BY GU ORDER BY GU",$connect);	
			
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["GU"];
					$strVal	 .= "<option value='".$GU."' ";
					if($GU == $add_guVal[$i]) { $strVal	 .= " SELECTED "; }
					$strVal	 .= ">".$GU."</option>";
				}
			}
			$strVal	 .= "</SELECT>"; 

			$strVal .=" <SELECT name='dong2[]' id='dong2[]'><option value=''>::동선택::</option>";
			if($add_siVal[$i]) {
				$result = @mysql_query("SELECT DONG FROM zipcode WHERE SI='".$add_siVal[$i]."' AND GU='".$add_guVal[$i]."' GROUP BY DONG ORDER BY DONG",$connect);	
			
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONG		=	$row["DONG"];
					$strVal	 .= "<option value='".$DONG."' ";
					if($DONG == $add_dgVal[$i]) { $strVal	 .= " SELECTED "; }
					$strVal	 .= ">".$DONG."</option>";
				}
			}
			$strVal .= "</SELECT>";
		$strVal .= "</div>";
	}
	return $strVal;
}

function address_buy_si_member($obj,$add_si,$add_gu,$add_dg,$add_other,$connect)
{
	$add_siVal = EXPLODE("^",$add_si);
	$add_guVal = EXPLODE("^",$add_gu);
	$add_dgVal = EXPLODE("^",$add_dg);

	FOR($i=0;$i<$obj;$i++)
	{
		$strVal .= "<SELECT name='si2[]' OnChange=\"CheckHandleAddr('2','si2[]',this.value,'','".$i."')\">
						<option value=''>::시선택::</option>
						<option value='서울' ";
		IF($add_siVal[$i] == "서울") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">서울</option>";
		$strVal  .= "<option value='경기' ";
		IF($add_siVal[$i] == "경기") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경기</option>";
		$strVal  .= "<option value='인천' ";
		IF($add_siVal[$i] == "인천") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">인천</option>";
		$strVal  .= "<option value='강원' ";
		IF($add_siVal[$i] == "강원") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">강원</option>";
		$strVal  .= "<option value='광주' ";
		IF($add_siVal[$i] == "광주") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">광주</option>";
		$strVal  .= "<option value='대전' ";
		IF($add_siVal[$i] == "대전") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">대전</option>";
		$strVal  .= "<option value='대구' ";
		IF($add_siVal[$i] == "대구") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">대구</option>";
		$strVal  .= "<option value='부산' ";
		IF($add_siVal[$i] == "부산") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">부산</option>";
		$strVal  .= "<option value='울산' ";
		IF($add_siVal[$i] == "울산") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">울산</option>";
		$strVal  .= "<option value='충븍' ";
		IF($add_siVal[$i] == "충븍") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">충븍</option>";
		$strVal  .= "<option value='충남' ";
		IF($add_siVal[$i] == "충남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">충남</option>";
		$strVal  .= "<option value='전북' ";
		IF($add_siVal[$i] == "전북") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">전북</option>";
		$strVal  .= "<option value='전남' ";
		IF($add_siVal[$i] == "전남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">전남</option>";
		$strVal  .= "<option value='경북' ";
		IF($add_siVal[$i] == "경북") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경북</option>";
		$strVal  .= "<option value='경남' ";
		IF($add_siVal[$i] == "경남") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">경남</option>";
		$strVal  .= "<option value='제주' ";
		IF($add_siVal[$i] == "제주") { $strVal .= " SELECTED"; }
		$strVal	 .=  ">제주</option>
				   </SELECT> ";

		$strVal	 .= "<SELECT name='gu2[]' OnChange=\"CheckHandleAddr('2','gu2[]',this.value,document.getElementsByName('si2[]')[".$i."].value,'".$i."');\">";
		$strVal	 .= " <option value=''>::구선택::</option>";
			if($add_siVal[$i]) {
				$result = @mysql_query("SELECT GU FROM zipcode WHERE SI='".$add_siVal[$i]."' GROUP BY GU ORDER BY GU",$connect);	
			
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["GU"];
					$strVal	 .= "<option value='".$GU."' ";
					if($GU == $add_guVal[$i]) { $strVal	 .= " SELECTED "; }
					$strVal	 .= ">".$GU."</option>";
				}
			}
			$strVal	 .= "</SELECT>"; 

			$strVal .=" <SELECT name='dong2[]' id='dong2[]'><option value=''>::동선택::</option>";
			if($add_siVal[$i]) {
				$result = @mysql_query("SELECT DONG FROM zipcode WHERE SI='".$add_siVal[$i]."' AND GU='".$add_guVal[$i]."' GROUP BY DONG ORDER BY DONG",$connect);	
			
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONG		=	$row["DONG"];
					$strVal	 .= "<option value='".$DONG."' ";
					if($DONG == $add_dgVal[$i]) { $strVal	 .= " SELECTED "; }
					$strVal	 .= ">".$DONG."</option>";
				}
			}
			$strVal .= "</SELECT>";
			$strVal .= " <input type='text' name='area_content2[]' value='".$add_other."' style='width:300px;border-width:1px; border-color:#9a9a9a;'>";
	}
	return $strVal;
}



FUNCTION ADDR_FORM($strKind,$strKind2,$strKind3,$strVal,$strVal2,$strVal3,$strArrJ,$strArrI)
{
	$connect_other = sql_conn2();
	if($strKind == "addr")
	{
		if(SUBSTR($strKind2,0,2) == "si")
		{
			$arrAddr	=	array("서울","경기","인천","강원","광주","대전","대구","부산","울산","충북","충남","전북","전남","경북","경남","제주");
			echo "<SELECT name='".$strKind2."' OnChange=\"CheckHandleAddr('".$strArrJ."','".$strKind2."',this.value,'','".$strArrI."')\">";
			echo "	<option value=''>::시선택::</option>";

			FOR($i=0;$i<COUNT($arrAddr);$i++)
			{
				$SI		=	$arrAddr[$i];
				echo "<option value='".$SI."' ";
				if($SI == $strVal) { echo " SELECTED "; }
				echo ">".$SI."</option>";
			}
			echo "</SELECT>";
		}
		if(SUBSTR($strKind2,0,2) == "gu")
		{
			echo "<SELECT name='".$strKind2."' OnChange=\"CheckHandleAddr('".$strArrJ."','".$strKind2."',this.value,document.getElementsByName('".$strKind3."')[".$strArrI."].value,'".$strArrI."');\">";
			echo "	<option value=''>::구선택::</option>";
			if($strVal2) {
				$result = @mysql_query("SELECT GU FROM zipcode WHERE SI='".$strVal2."' GROUP BY GU ORDER BY GU");	
			
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["GU"];
					echo "<option value='".$GU."' ";
					if($GU == $strVal) { echo " SELECTED "; }
					echo ">".$GU."</option>";
				}
			}
			echo "</SELECT>"; 
		}
		if(substr($strKind2,0,4) == "dong")
		{
			echo "<SELECT name='".$strKind2."'>";
			echo "	<option value=''>::동선택::</option>";
			if($strVal2 && $strVal3) {
				$result = @mysql_query("SELECT DONG FROM zipcode WHERE SI='".$strVal2."' AND GU='".$strVal3."' GROUP BY DONG ORDER BY DONG");
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONG		=	$row["DONG"];
					echo "<option value='".$DONG."' ";
					if($DONG == $strVal) { echo " SELECTED "; }
					echo ">".$DONG."</option>";
				} 
			}
			echo "</SELECT>";
		}
	} ELSEIF($strKind == "section") {
		echo "<SELECT name='".$strKind2."' OnChange=\"CheckHandleSection('".$strKind3."',this.value,'".$strArrI."');\">";
			echo "	<option value=''>::물건선택::</option>";
//			if($strVal) {
				$result = @mysql_query("SELECT csseq,code_name FROM cd_code_second ORDER BY csseq ASC");
				WHILE($row = @mysql_fetch_array($result))
				{
					$csseq			=	$row["csseq"];
					$code_name		=	$row["code_name"];
					echo "<option value='".$csseq."' ";
					if($csseq == $strVal) { echo " SELECTED "; }
					echo ">".$code_name."</option>";
				} 
//			}
			echo "</SELECT>";
			echo "<SELECT name='".$strKind3."'>";
			echo "	<option value=''>:: 소분류 ::</option>";
//			if($strVal) {
				$result = @mysql_query("SELECT seq,code_name FROM cd_code_detail WHERE csseq='".$strVal."' ORDER BY csseq ASC");
				WHILE($row = @mysql_fetch_array($result))
				{
					$seq			=	$row["seq"];
					$code_name		=	$row["code_name"];
					echo "<option value='".$seq."' ";
					if($seq == $strVal2) { echo " SELECTED "; }
					echo ">".$code_name."</option>";
				} 
//			}
			echo "</SELECT>";
	}
}


FUNCTION ADDR_FORM_SEARCH($strKind,$strVal,$strVal2,$strVal3,$connect1)
{
	if($strKind == "search_si")
	{
		$arrAddr	=	array("서울","경기","인천","강원","광주","대전","대구","부산","울산","충북","충남","전북","전남","경북","경남","제주");
		echo "<SELECT name='".$strKind."' OnChange=\"CheckHandleAddrSearch('".$strKind."',this.value,'')\"  style=\"width:113px;height:18px;\">";
		echo "	<option value=''>::시선택::</option>";

		FOR($i=0;$i<COUNT($arrAddr);$i++)
		{
			$SI		=	$arrAddr[$i];
			echo "<option value='".urlencode($SI)."'";
			if($SI == $strVal) { echo " SELECTED"; }
			echo ">".$SI."</option>";
		}
		echo "</SELECT>";
	}
	if($strKind == "search_gu")
	{
		echo "<SELECT name='".$strKind."' OnChange=\"CheckHandleAddrSearch('".$strKind."',this.value,document.getElementsByName('search_si')[0].value);\"  style=\"width:98px;height:18px;font-size:11px;\">";
		echo "	<option value=''>::구선택::</option>";
		if($strVal2) {
			$result = @mysql_query("SELECT GU FROM zipcode WHERE SI='".$strVal2."' GROUP BY GU ORDER BY GU",$connect1);	
		
			WHILE($row = @mysql_fetch_array($result))
			{
				$GU		=	$row["GU"];
				echo "<option value='".urlencode($GU)."' ";
				if($GU == $strVal) { echo " SELECTED "; }
				echo ">".$GU."</option>";
			}
		}
		echo "</SELECT>"; 
	}
	if($strKind == "search_dg")
	{
		echo "<SELECT name='".$strKind."' style=\"width:105px;height:18px;font-size:11px;\">";
		echo "	<option value=''>::동선택::</option>";
		if($strVal2 && $strVal3) {
			$result = @mysql_query("SELECT DONG FROM zipcode WHERE SI='".$strVal2."' AND GU='".$strVal3."' GROUP BY DONG ORDER BY DONG",$connect1);
			WHILE($row = @mysql_fetch_array($result))
			{
				$DONG		=	$row["DONG"];
				echo "<option value='".$DONG."' ";
				if($DONG == $strVal) { echo " SELECTED "; }
				echo ">".$DONG."</option>";
			} 
		}
		echo "</SELECT>";
	}
}

function mailer_old($fname, $fmail, $to, $subject, $content, $type=0, $file="", $cc="", $bcc="", $f_option="-f help@nemweb.kr") {
	global $js, $config;

	$fname = "=?EUC-KR?B?" . base64_encode($fname) . "?=";
	$subject = "=?EUC-KR?B?" . base64_encode($subject) . "?=";

	$header  = "Return-Path: <$fmail>\n";
	$header .= "From: $fname <$fmail>\n";
	$header .= "Reply-To:$fname <$fmail>\n";

	if ($cc)  $header .= "Cc: $cc\n";
	if ($bcc) $header .= "Bcc: $bcc\n";
	$header .= "MIME-Version: 1.0\n";
	$header .= "X-Mailer: POPSMAIL : $_SERVER[SERVER_ADDR] : $_SERVER[REMOTE_ADDR] \n";

	if ($file != "") {
		$boundary = uniqid("http://muan.ne.kr");

		$header .= "Content-type: MULTIPART/MIXED; BOUNDARY=\"$boundary\"\n\n";
		$header .= "--$boundary\n";
	}

	if ($type) {
		$header .= "Content-Type: TEXT/HTML; charset=EUC-KR\n";
		if ($type == 2)
			$content = nl2br($content);
	} else {
		$header .= "Content-Type: TEXT/PLAIN; charset=EUC-KR\n";
		$content = stripslashes($content);
	}
	$header .= "Content-Transfer-Encoding: BASE64\n\n";
	$header .= chunk_split(base64_encode($content)) . "\n";

	if ($file != "") {
		foreach ($file as $f) {
			$header .= "\n--$boundary\n";
			$header .= "Content-Type: APPLICATION/OCTET-STREAM; name=\"$f[name]\"\n";
			$header .= "Content-Transfer-Encoding: BASE64\n";
			$header .= "Content-Disposition: inline; filename=\"$f[name]\"\n";

			$header .= "\n";
			$header .= chunk_split(base64_encode($f[data]));
			$header .= "\n";
		}
		$header .= "--$boundary--\n";
	}

	@mail($to, $subject, base64_encode($content), $header, $f_option);
}

function mailer($fname, $fmail, $to, $subject, $content, $homeurl, $type=0, $file="", $cc="", $bcc="") 
{
    global $config;
    global $g4;

    // 메일발송 사용을 하지 않는다면

    $fname   = "=?utf-8?B?" . base64_encode($fname) . "?=";
    $subject = "=?utf-8?B?" . base64_encode($subject) . "?=";
    //$g4[charset] = ($g4[charset] != "") ? "charset=$g4[charset]" : "";

    $header  = "Return-Path: <$fmail>\n";
    $header .= "From: $fname <$fmail>\n";
    $header .= "Reply-To: <$fmail>\n";
    if ($cc)  $header .= "Cc: $cc\n";
    if ($bcc) $header .= "Bcc: $bcc\n";
    $header .= "MIME-Version: 1.0\n";
    //$header .= "X-Mailer: SIR Mailer 0.91 (sir.co.kr) : $_SERVER[SERVER_ADDR] : $_SERVER[REMOTE_ADDR] : $g4[url] : $_SERVER[PHP_SELF] : $_SERVER[HTTP_REFERER] \n";
    // UTF-8 관련 수정
    $header .= "X-Mailer: SIR Mailer 0.92 (vitalparachute.com) : $_SERVER[SERVER_ADDR] : $_SERVER[REMOTE_ADDR] : $_SERVER[PHP_SELF] : $_SERVER[HTTP_REFERER] \n";

    if ($file != "") {
        $boundary = uniqid($homeurl);

        $header .= "Content-type: MULTIPART/MIXED; BOUNDARY=\"$boundary\"\n\n";
        $header .= "--$boundary\n";
    }

    if ($type) {
        $header .= "Content-Type: TEXT/HTML; charset=utf-8\n";
        if ($type == 2)
            $content = nl2br($content);
    } else {
        $header .= "Content-Type: TEXT/PLAIN; charset=utf-8\n";
        $content = stripslashes($content);
    }
    $header .= "Content-Transfer-Encoding: BASE64\n\n";
    $header .= chunk_split(base64_encode($content)) . "\n";

    if ($file != "") {
        foreach ($file as $f) {
            $header .= "\n--$boundary\n";
            $header .= "Content-Type: APPLICATION/OCTET-STREAM; name=\"$f[name]\"\n";
            $header .= "Content-Transfer-Encoding: BASE64\n";
            $header .= "Content-Disposition: inline; filename=\"$f[name]\"\n";

            $header .= "\n";
            $header .= chunk_split(base64_encode($f[data]));
            $header .= "\n";
        }
        $header .= "--$boundary--\n";
    }

    @mail($to, $subject, "", $header);
}

function e_code() {

		$date_s = rand(0,9);
		$rand_s = substr(rand(10000,99999) * rand(10000,99999),-5);
		$rand_e = substr(rand(1000,9999) * rand(1000,9999),-2);
		
		if($date_s  == 0) {
			$a = "A";
			$b = "Z";
			$c = e_code_to();
		} elseif($date_s  == 1) {
			$a = "B";
			$b = "Y";
			$c = e_code_to();
		} elseif($date_s  == 2) {
			$a = "C";
			$b = "X";
			$c = e_code_to();
		} elseif($date_s  == 3) {
			$a = "D";
			$b = "W";
			$c = e_code_to();
		} elseif($date_s  == 4) {
			$a = "E";
			$b = "V";
			$c = e_code_to();
		} elseif($date_s  == 5) {
			$a = "F";
			$b = "U";
			$c = e_code_to();
		} elseif($date_s  == 6) {
			$a = "G";
			$b = "T";
			$c = e_code_to();
		} elseif($date_s  == 7) {
			$a = "H";
			$b = "S";
			$c =e_code_to();
		} elseif($date_s  == 8) {
			$a = "K";
			$b = "R";
			$c = e_code_to();
		} elseif($date_s  == 9) {
			$a = "M";
			$b = "P";
			$c = e_code_to();
		}

		return date("YmdH").$a.$rand_s.$b.$c.$rand_e;
}

function  e_code_to() {
		$ret = substr(rand(100,999) * rand(0,9),-1);
		return $ret;
}

function Check_payGb($obj)
{
	SWITCH($obj)
	{
		CASE "M" : $retVal = "모바일"; break;
		CASE "A" : $retVal = "ARS결제"; break;
		CASE "C" : $retVal = "카드결제"; break;
		CASE "B" : $retVal = "실계좌이체"; break;
	}
	return $retVal;
}

function Check_ymd($obj)
{
	if(!$obj)
	{
		$obj = DATE("Y-m-d");
	}
	$objArr = EXPLODE("-",$obj);

	$retval = $objArr[0]."년 ".$objArr[1]."월 ".$objArr[2]."일";

	return $retval;
}


Class strImaGing 
{ 
    // Variables 
    private $img_input; 
    private $img_output; 
    private $img_src; 
    private $format; 
    private $quality = 100; 
    private $x_input; 
    private $y_input; 
    private $x_output; 
    private $y_output; 
    private $resize; 

    // Set image 
    public function set_img($img) 
    { 
        // Find format 
		$imgTrue = false;
        $ext = strtoupper(pathinfo($img, PATHINFO_EXTENSION)); 
		// JPEG image 

		if(is_file($img) && ($ext == "JPG" OR $ext == "JPEG")) 
        { 
            $this->format = $ext; 
            $this->img_input = ImageCreateFromJPEG($img); 
            $this->img_src = $img; 
			$imgTrue = True;
        } 
        // PNG image 
        elseif(is_file($img) && $ext == "PNG") 
        { 
            $this->format = $ext; 
            $this->img_input = ImageCreateFromPNG($img); 
            $this->img_src = $img; 
			$imgTrue = True;
        } 
        // GIF image 
        elseif(is_file($img) && $ext == "GIF") 
        { 
            $this->format = $ext; 
            $this->img_input = ImageCreateFromGIF($img); 
            $this->img_src = $img; 
			$imgTrue = True;
        } 
        // Get dimensions 
		IF($imgTrue == True)
		{
			$this->x_input = imageSX($this->img_input); 
			$this->y_input = imageSY($this->img_input); 
		}
    } 

    // Set maximum image size (pixels) 
    public function set_size($max_x = 100,$max_y = 100) 
    { 
/*
		 $this->x_output = $max_x; 
		 $this->y_output = $max_y; 
		 $this->resize = TRUE; 
*/
//		echo $this->x_input."--".$max_x."<BR>";
//		echo $this->y_input."--".$max_y."<BR>";
        // Resize 
        if($this->x_input > $max_x || $this->y_input > $max_y) 
        { 
            $a= $max_x / $max_y; 
            $b= $this->x_input / $this->y_input; 
            if ($a<$b) 
            { 
                $this->x_output = ($max_y / $this->y_input) * $this->x_input;
                $this->y_output = $max_y;
            } 
            else 
            { 
                $this->y_output = ($max_x / $this->x_input) * $this->y_input; 
                //$this->x_output = ($max_y / $this->y_input) * $this->x_input; 
				$this->x_output = $max_x;
            } 
            // Ready 
            $this->resize = TRUE; 
		// Don't resize       
		} else { $this->resize = FALSE; } 

//		echo "$max_x------$this->x_input---$this->x_output<BR>";
//		echo "$max_y-------$this->y_input---$this->y_output<BR>";
    } 

    // Set image quality (JPEG only) 
    public function set_quality($quality) 
    { 
        if(is_int($quality)) 
        { 
            $this->quality = $quality; 
        } 
    } 
    // Save image 
    public function save_img($path,$max_x = 100,$max_y = 100, $thumkind) 
    { 
		$intXcoo = 0;
        // Resize 
        if($this->resize) 
        { 
			// $thumkind가  A:면 이미지 비율에따라 잘름 (이미지 잘림), "" 라면 이미지 왜곡하여 정비레 잘림
			// 이미지의 세로길이나 세로길이를 0이나 값을 안주면 이미지는 정비레로 축소된다.

			if(!$this->x_output || !$this->y_output)
			{
				$classHeightSize = round(($this->y_input * $this->x_output) / $this->x_input); //세로길이
				$classWidthSize = round(($this->y_output * $this->y_input) / $this->y_input);	// 가로길이

				if($classHeightSize) { $this->y_output = $classHeightSize; }
				if($classWidthSize) { $this->x_output = $classWidthSize; }

				$classWidthSizeInt  = 0;
				$classHeightSizeInt = 0;

	            $this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output); 
			} else {

				if($thumkind == "A")	// 두개가 크다면 같은 비율로 줄인다. (이미지 비율유지))
				{
					IF($this->x_input < $this->y_input)
					{
						$classHeightSize = round($this->y_input - ($this->y_output * $this->x_input) / $this->x_output); //세로길이
						$classWidthSize =  round($max_x - $this->x_output); //가로길이
					} ELSE {
						$classHeightSize = round($max_x - $this->x_output); //세로길이
						$classWidthSize = round($this->y_input - ($this->y_output * $this->x_input) / $this->x_output);	// 가로길이

						$intXcoo = round(($max_x - $this->x_output)/2);
					}
			
					/*						
					echo $this->x_input."<BR>";
					echo $this->y_input."<BR>";
					echo $this->x_output."<BR>";
					echo $this->y_output."<BR>";
					echo $classHeightSize."<BR>";
					echo $classWidthSize;
					exit;
					*/

					if($classWidthSize < 0) $classWidthSize = 0;
					if($classHeightSize < 0) $classHeightSize = 0;

					if($classWidthSize > 0)
					{
						$classWidthSizeInt   = 0;
						$classHeightSizeInt = round((100 * ($this->y_input-$classHeightSize)) / $this->y_output);
					} elseif($classHeightSize > 0) {
						$classWidthSizeInt   =  0;
						$classHeightSizeInt = round((100 * ($this->x_input-$classWidthSize)) / $this->x_output);
					} 
					$this->img_output = ImageCreateTrueColor($max_x, $max_y); 

				} else {	// 아니라면 이미지를 왜곡하여 잘림없이 줄인다.
					$classWidthSizeInt  = 0;
					$classHeightSizeInt = 0;

		            $this->img_output = ImageCreateTrueColor($this->x_output, $this->y_output); 
				}
			}
			//echo $classWidthSize."---".$classHeightSize."<BR>";
			//echo round($this->x_output + $classWidthSizeInt)."--".round($this->y_output + $classHeightSizeInt);

            ImageCopyResampled($this->img_output, $this->img_input, 
							   $intXcoo,
				               0,
				               0, 
				               0, 
							   round($this->x_output + $classWidthSizeInt),
                               round($this->y_output + $classHeightSizeInt),
				               $this->x_input, 
				               $this->y_input); 
        } 
        // Save JPEG 
        if($this->format == "JPG" OR $this->format == "JPEG") 
        { 
            if($this->resize) { imageJPEG($this->img_output, $path, $this->quality); } 
            else { copy($this->img_src, $path); } 
        } 
        // Save PNG 
        elseif($this->format == "PNG") 
        { 
            if($this->resize) { imagePNG($this->img_output, $path); } 
            else { copy($this->img_src, $path); } 
        } 
        // Save GIF 
        elseif($this->format == "GIF") 
        { 
            if($this->resize) { imageGIF($this->img_output, $path); } 
            else { copy($this->img_src, $path); } 
        } 
    } 

    // Get width 
    public function get_width() 
    { 
        return $this->x_input; 
    } 
    // Get height 
    public function get_height() 
    { 
        return $this->y_input; 
    } 
    // Clear image cache 
    public function clear_cache() 
    { 
        @ImageDestroy($this->img_input); 
        @ImageDestroy($this->img_output); 
    } 
} 

Class thumbnailImgOr extends strImaGing { 

    private $image; 
    private $width; 
    private $height; 
    
    function __construct($image,$width,$height,$path,$strfilename,$thumkind) { 

		if($image)
		{
			parent::set_img($image); 
			parent::set_quality(100); 
			parent::set_size($width,$height); 

			$this->thumbnail= $strfilename; 

			parent::save_img($path."/".$this->thumbnail,$width,$height,$thumkind); 
			parent::clear_cache(); 
		}
    } 
	
    function __toString() { 
            return $this->thumbnail; 
    } 
} 

Class thumbnailImg extends strImaGing { 

    private $image; 
    private $width; 
    private $height; 
    
    function __construct($image,$width,$height,$kind,$intGSeq,$dtmDate,$intRand,$OrImage,$gstrFileOrPath) { 

		if($image)
		{
			parent::set_img($image); 
			parent::set_quality(100); 
			parent::set_size($width,$height); 

			$dtmDate1   = EXPLODE(" ",$dtmDate);
			$dtmDate1_1 = EXPLODE("-",$dtmDate1[0]);
			$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

			$StrFileName = $kind."_".$intGSeq."_".$dtmDate1_2.$intRand.".".strtolower(pathinfo($image, PATHINFO_EXTENSION));

			$StrPathInfoArrSum = "";

			$StrPathInfo = $gstrFileOrPath.$dtmDate1_1[0]."/".$dtmDate1_1[1]."/".$dtmDate1_1[2];

			$StrPathInfoArr = EXPLODE("/",$StrPathInfo);

			for($i=1;$i<COUNT($StrPathInfoArr);$i++)
			{
				$StrPathInfoArrSum = $StrPathInfoArrSum."/".$StrPathInfoArr[$i];

				if(!is_dir($StrPathInfoArrSum))
				{
					mkdir($StrPathInfoArrSum, 0777, true);
				}
			}

			$this->thumbnail= $StrPathInfo."/".$StrFileName; 

			parent::save_img($this->thumbnail); 
			parent::clear_cache(); 
		} 

		if($OrImage)
		{
			file_del("../".$OrImage);
		}
    } 

    function __toString() { 
            return str_replace($_SERVER["DOCUMENT_ROOT"]."/imgSale/","imgSale/",$this->thumbnail); 
    } 
} 

// 사진 파일 업로드 (날짜별)
function file_upload2($strKind,$intGSeq,$strFileName,$strFileNameTemp,$dtmDate,$strOrFileName,$gstrFileOrPath)
{
	if($strFileName)
	{
		$dtmDate1   = EXPLODE(" ",$dtmDate);
		$dtmDate1_1 = EXPLODE("-",$dtmDate1[0]);
		$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

		$StrPathInfoArrSum = "";

		$StrPathInfo = $gstrFileOrPath.$dtmDate1_1[0]."/".$dtmDate1_1[1]."/".$dtmDate1_1[2];

		$StrPathInfoArr = EXPLODE("/",$StrPathInfo);

		for($i=1;$i<COUNT($StrPathInfoArr);$i++)
		{
			$StrPathInfoArrSum = $StrPathInfoArrSum."/".$StrPathInfoArr[$i];

//			echo $StrPathInfoArrSum."<BR>";

			if(!is_dir($StrPathInfoArrSum))
			{
				mkdir($StrPathInfoArrSum, 0777, true);
			}
		}
		
		$strName = substr(strrchr($strFileName,"."),1);

		$strNewName = $strKind."_".$intGSeq."_".$dtmDate1_2.rand(10,99).".".strtolower($strName);
		move_uploaded_file($strFileNameTemp, $StrPathInfo."/".$strNewName);

		
		IF($strOrFileName)
		{
			 file_del($strOrFileName);
		}

		return "imgSale/".$dtmDate1_1[0]."/".$dtmDate1_1[1]."/".$dtmDate1_1[2]."/".$strNewName;

	} ELSE {

		IF($strOrFileName)
		{
			 file_del($StrPathInfo."/".$strOrFileName);
		}
	}
}


function file_upload($strFileKind,$strWidth,$strHeight,$strImgThumKind,$strNumber,$strKind,$strFileName,$strFileNameTemp,$dtmDate,$strOrFileName,$strKindDel)
{
	$SaveDir = $_SERVER["DOCUMENT_ROOT"].$strKind;

	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

	if($strFileName)
	{
		$strName = substr(strrchr($strFileName,"."),1);
		if($strOrFileName)
		{
			 file_del($SaveDir."/".$strOrFileName);
			 $strNamenew = EXPLODE(".",$strOrFileName);
			 $strNewName = $strNamenew[0].".".strtolower($strName);
		} else {

			$strNumberName = $dtmDate1_1.$dtmDate1_2.$strNumber;

			$strNewName = $strNumberName.".".strtolower($strName);
		}
		move_uploaded_file($strFileNameTemp, $SaveDir."/".$strNewName);

		if($strFileKind == "IMG")
		{
			$strNewName = new thumbnailImgOr($SaveDir."/".$strNewName,$strWidth,$strHeight,$SaveDir,$strNewName,$strImgThumKind);
		}

	} else {
		if($strOrFileName)
		{
			$strNewName = $strOrFileName;
		} else {
			$strNewName = "";
		}
	}

	if($strKindDel)
	{
		if(!$strFileName)
		{
			file_del($SaveDir."/".$strOrFileName);
			$strNewName = "";
		}
	}

	return $strNewName;
}

function file_upload_double($strImgThumKind,$intsWidth,$intsHeight,$intLWidth,$intLHeight,$strNumber,$strKind,$strFileName,$strFileNameTemp,$dtmDate,$strOrFileName,$strKindDel)
{
	$SaveDir = $_SERVER["DOCUMENT_ROOT"].$strKind;

	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

	if($strFileName)
	{
		$strName = substr(strrchr($strFileName,"."),1);
		if($strOrFileName)
		{
			
			 $strNamenew = STR_REPLACE(".".strtolower($strName),"",$strOrFileName);
			 file_del($SaveDir."/".$strOrFileName);
			 file_del($SaveDir."/".$strNamenew."S.".strtolower($strName));
			 file_del($SaveDir."/".$strNamenew."L.".strtolower($strName));

			 $strNewName = $strNamenew.".".strtolower($strName);
			 $strNewName1 = $strNamenew."S.".strtolower($strName);
			 $strNewName2 = $strNamenew."L.".strtolower($strName);
		} else {

			$strNumberName = $dtmDate1_1.$dtmDate1_2.$strNumber;
			$strNewName = $strNumberName.".".strtolower($strName);
			$strNewName1 = $strNumberName."S.".strtolower($strName);
			$strNewName2 = $strNumberName."L.".strtolower($strName);
		}
		move_uploaded_file($strFileNameTemp, $SaveDir."/".$strNewName);

		$strNewName1 = new thumbnailImgOr($SaveDir."/".$strNewName,$intsWidth,$intsHeight,$SaveDir,$strNewName1,$strImgThumKind);
		$strNewName2 = new thumbnailImgOr($SaveDir."/".$strNewName,$intLWidth,$intLHeight,$SaveDir,$strNewName2,$strImgThumKind);

	} else {
		if($strOrFileName)
		{
			$strName = substr(strrchr($strOrFileName,"."),1);
			$strNamenew = STR_REPLACE(".".strtolower($strName),"",$strOrFileName);
			$strNewName = $strOrFileName;
			$strNewName1 = $strNamenew."S.".strtolower($strName);
			$strNewName2 = $strNamenew."L.".strtolower($strName);
		} else {
			$strNewName = "";
			$strNewName1 = "";
			$strNewName2 = "";
		}
	}

	if($strKindDel)
	{
		if(!$strFileName)
		{
			$strName = substr(strrchr($strOrFileName,"."),1);
			$strNamenew = STR_REPLACE(".".strtolower($strName),"",$strOrFileName);
			file_del($SaveDir."/".$strOrFileName);
			file_del($SaveDir."/".$strNamenew."S.".strtolower($strName));
			file_del($SaveDir."/".$strNamenew."L.".strtolower($strName));
			$strNewName = "";
			$strNewName1 = "";
			$strNewName2 = "";
		}
	}

	return $strNewName."^".$strNewName1."^".$strNewName2;
}

function file_del($file_name)
{
	If(is_file($file_name))
	{
		@unlink($file_name);
	} 
}


FUNCTION addParamRule($obj,$rule)
{
	$chk = 1;
	
	$obj = TRIM($obj);

	IF($obj)
	{
		IF(!eregi("kr",trim($rule))) //한글체크
		{
			IF(preg_match("/[\xA1-\xFE\xA1-\xFE]/",$obj)) $chk = 0;
		}
		IF(!eregi("en",TRIM($rule))) // 영문체크
		{
			IF(preg_match("/[a-zA-Z]/",$obj)) $chk = 0;
		}
		IF(!eregi("int",trim($rule))) // 숫자체크
		{
			if(preg_match("/[0-9]/",$obj)) $chk = 0;
		}
		IF(!eregi("special",trim($rule))) // 특수문자체크
		{
			if(preg_match("/[!#$%^&*()?+=\/]/",$obj)) $chk = 0;
		}

		// True 1 / 0 false
		return $chk;
	}
}

function get_remotefile($url){ 

    $mdate = date("Y-m-d", time()); 
    $url_stuff = parse_url($url); 

    if (!$fp = @fsockopen ($url_stuff['host'], (($url_stuff['port'])?($url_stuff['port']):("80")), $errno, $errstr, 2)) return false; 
    else { 

        if ($url_stuff['query']) $url_stuff['path'] .= "?"; 
        $header = "GET ".$url_stuff['path'].$url_stuff['query']." HTTP/1.0"; 
        $header .= "\r\nHost: ".$url_stuff['host']; 
        $header .= "\r\nIf-Modified-Since: $mdate"; 
        $header .= "\r\n\r\n"; 

        fputs ($fp, $header); 

        unset($header, $body, $lmdate); 
        $act = false; 
    //    $cnt = 0; 

        socket_set_timeout($fp, 4); 

        while ((!feof($fp))) { 

            //if ($cnt == 16) break; 
            $line = fgets ($fp,1024); 
            $ss = socket_get_status($fp); 
            if ($ss[timed_out]) return false; 

            if (!$act) { 
                if (strpos($line, "\r\n", 0) == 0) $act = true; 
                if (($n1 = strpos($line, "Last-Modified:")) !== false) $lmdate = trim(substr($line, $n1 + 14)); 
                if (($n2 = strpos($line, "Location:")) !== false) { $loc = trim(substr($line, $n2 + 9)); break; } 
                $header .= $line; 
            } else { 
                $body .= $line; 
            //    if (strpos($line, "</item>") !== false) $cnt++; 
            } 
    //        echo "<xmp>" . $line . "</xmp>"; 
        } 
        fclose ($fp); 
    } 

    if ($loc) list($header, $body, $lmdate) = get_remotefile($loc, $mdate); 

    //return array($header, $body, $lmdate); 
    return $body; 
}

function add_str($strVal)
{
	$strVal = addslashes(trim($strVal));

	return $strVal;
}

FUNCTION strip_str($strVal)
{
	$strVal = stripslashes($strVal);

	return $strVal;
}

FUNCTION strip_str_br($strVal)
{
	$strVal = nl2br(stripslashes($strVal));

	return $strVal;
}

// 세션변수 생성
function set_session($session_name, $value)
{
    if (PHP_VERSION < '5.3.0')
        session_register($session_name);
    // PHP 버전별 차이를 없애기 위한 방법
    $session_name = $_SESSION["$session_name"] = $value;
}

// 세션변수값 얻음
function get_session($session_name)
{
    return base64_decode($_SESSION[$session_name]);
}

function get_session2($session_name)
{
    return $_SESSION[$session_name];
}

function Check_Hphone($strVal)
{
	$strPhoneVal = trim(str_replace("-","",$strVal));
	$strPhoneVal2 = "01023334749";
	$intPhoneLen = strlen($strPhoneVal);

	IF($intPhoneLen < 10)
	{
		$strPhoneOr = "000-0000-0000";
	} ELSE {
		$strPhoneT = substr($strPhoneVal,0,2);

		IF($strPhoneT == "01")
		{
			SWITCH($intPhoneLen)
			{
				CASE "11" : 
					$intPhone1 = substr($strPhoneVal,0,3);
					$intPhone2 = substr($strPhoneVal,3,4);
					$intPhone3 = substr($strPhoneVal,7,4);
				BREAK;
				CASE "10" : 
					$intPhone1 = substr($strPhoneVal,0,3);
					$intPhone2 = substr($strPhoneVal,3,3);
					$intPhone3 = substr($strPhoneVal,6,4);					
				BREAK;
			}

			$strPhoneOr = $intPhone1."-".$intPhone2."-".$intPhone3;
		} ELSE {
			$strPhoneOr = "000-0000-0000";
		}
	}

	return $strPhoneOr;
}

FUNCTION OTHER_FORM2($strKind,$strKind2,$strVal,$strVal2,$strVal3,$strUrl)
{
	global $connect;

	if($strKind == "addr")
	{
		if($strKind2 == "si")
		{
			$result = sql_query("SELECT SIDO FROM ZIPCODE_N GROUP BY SIDO ORDER BY SIDO",$connect);
			echo "<SELECT name='si' OnChange=\"CheckHandle('si',this.value,'');\" class='selectbd_1'>";
			echo "	<option value=''>::시선택::</option>";
			WHILE($row = @mysql_fetch_array($result))
			{
				$SI		=	$row["SIDO"];
				echo "<option value='".urlencode($SI)."' ";
				if($SI == $strVal) { echo " SELECTED "; }
				echo ">".$SI."</option>";
			}
			echo "</SELECT>";
		}

		if($strKind2 == "gu")
		{
			echo "<SELECT name='gu' OnChange=\"CheckHandle('gu',this.value,document.all.si.value);\" style='width:110px'  class='selectbd_1'>";
			echo "	<option value=''>::구선택::</option>";
				
				$result = sql_query("SELECT SIGUN FROM ZIPCODE_N WHERE SIDO='".$strVal2."' GROUP BY SIGUN ORDER BY SIGUN",$connect);	
				$i =0;
				WHILE($row = @mysql_fetch_array($result))
				{
					$GU		=	$row["SIGUN"];
					IF($GU)
					{
						echo "<option value='".urlencode($GU)."' ";
						if($GU == $strVal) { echo " SELECTED "; }
						echo ">".$GU."</option>";
						$i++;
					}
				}
			
				IF($i == 0)
				{

					$result = sql_query("SELECT DONGSEQ FROM ZIPCODE_N where SIDO = '".$strVal2."' group by DONGSEQ ORDER BY DONGSEQ",$connect);

					WHILE($row = @mysql_fetch_array($result))
					{
						$DONGSEQ		=	$row["DONGSEQ"];
						IF($DONGSEQ)
						{
							echo "<option value='".urlencode($DONGSEQ)."' ";
							if($DONGSEQ == $strVal) { echo " SELECTED "; }
							echo ">".$DONGSEQ."</option>";
							$i++;
						}
					}
				}


			echo "</SELECT>"; 
		}
		if($strKind2 == "dong")
		{
			echo "<SELECT name='dong'  class='selectbd_1'>";
			echo "	<option value=''>::동/기타선택::</option>";
				$result = sql_query("SELECT DONGSEQ FROM ZIPCODE_N where SIDO = '".$strVal2."' AND SIGUN='".$strVal3."'  group by DONGSEQ ORDER BY DONGSEQ",$connect);
				WHILE($row = @mysql_fetch_array($result))
				{
					$DONGSEQ		=	$row["DONGSEQ"];
					echo "<option value='".$DONGSEQ."' ";
					if($DONGSEQ == $strVal) { echo " SELECTED "; }
					echo ">".$DONGSEQ."</option>";
			}
			echo "</SELECT>";
		}
	} 
	sql_close($connect_other);
}


function jk_image_rollover($obj, $objor)
{
	if($obj == $objor)
	{
		$retVal = " class='active'";
	} else {
		$retVal = " class='rollover'";
	}
	return $retVal;
}

function check_recyn($recyn)
{
	SWITCH($recyn)
	{
		CASE "Y" : $retVal = "노출"; BREAK;
		CASE "N" : $retVal = "비노출"; BREAK;
	}
	return $retVal;
}


FUNCTION check_popup_img($obj)
{
	IF($obj)
	{
		$objval = "<img src='/iFile/popup/".$obj."' class='banner_coulsel_img'>";
	}
	return $objval;
}

FUNCTION check_admin_popyn($obj)
{
	SWITCH($obj)
	{
		CASE "Y" : $objval = "노출"; BREAK;
		CASE "N" : $objval = "비노출"; BREAK;
	}
	return $objval;
}

FUNCTION check_popup_linktype($obj)
{
	SWITCH($obj)
	{
		CASE "_new" : $objval = "새창"; BREAK;
		CASE "opener" : $objval = "부모창"; BREAK;
	}
	return $objval;
}


FUNCTION check_admin_auth($obj)
{
	SWITCH($obj)
	{
		CASE "9" : $objval = "모든메뉴"; BREAK;
		CASE "1" : $objval = "채용페이지 글쓰기 및 관리"; BREAK;
		CASE "2" : $objval = "악세서리 페이지 글쓰기 및 관리"; BREAK;
		CASE "3" : $objval = "PR 게시판 글쓰기 및 관리"; BREAK;
	}
	return $objval;
}

FUNCTION check_admin_login($obj)
{
	SWITCH($obj)
	{
		CASE "Y" : $objval = "로그인가능"; BREAK;
		CASE "N" : $objval = "로그인불가"; BREAK;
	}
	return $objval;
}


/* utf-8 힌글 자르기 */
function strcut_utf8($str, $len, $checkmb=false, $tail='') 
{
	preg_match_all('/[\xE0-\xFF][\x80-\xFF]{2}|./', $str, $match); // target for BMP
	$m = $match[0];
	$slen = strlen($str); // length of source string
	$tlen = strlen($tail); // length of tail string
	$mlen = count($m); // length of matched characters

	if ($slen <= $len) return $str;
	if (!$checkmb && $mlen <= $len) return $str;
	$ret = array();
	$count = 0;

	for ($i=0; $i < $len; $i++) {
		$count += ($checkmb && strlen($m[$i]) > 1)?2:1;
		if ($count + $tlen > $len) break;
		$ret[] = $m[$i];
	}
	return join('', $ret).$tail;
}


FUNCTION fn_emailyn($obj)
{
	$strArr = ARRAY("Y^수신","N^수신하지않음");

	ECHO "<span class='forange'>(메일링 서비스를 받으시겠습니까?) </span>";

	FOR($i=0;$i<COUNT($strArr);$i++)
	{
		IF($i > 0)
		{
			ECHO "&nbsp;";
		}
		$strArrVal = EXPLODE("^",$strArr[$i]);

		ECHO "<input type='radio' name='emailyn[]' value='".$strArrVal[0]."'";
		IF($strArrVal[0] == $obj)
		{
			ECHO " checked";
		}
		ECHO "> ".$strArrVal[1];
	}
}



FUNCTION fn_branch_rold($obj,$kind)
{
	$selectname = "rold";

	IF($kind == "search")
	{
		$selectname = "S".$selectname;
	}

	$retval = "<select name='".$selectname."'>";
	$retval .=  "<option value=''>::나이::</option>";
	FOR($i=(DATE("Y")-35);$i<=(DATE("Y")-18);$i++)
	{
		$retval .=  "<option value='".$i."'";
		IF($i == $obj)
		{
			$retval .=  " selected";
		}
		$retval .=  ">".$i."</option>";
	}
	$retval .=  "</select>";

	return $retval;
}


FUNCTION fr_main_board($section,$frorder,$frasc,$frlimit1,$frlimit2)
{
	global $connect;

	$Query = "SELECT seq,title,left(reg_date,10) as reg_date FROM board_other WHERE section='".$section."' ORDER BY ".$frorder." ".$frasc." LIMIT ".$frlimit1.",".$frlimit2;

	$Result = sql_query($Query,$connect);
	$retVal = "";
	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		IF($i > 0)
		{
			$retVal .= "^";
		}
		$FRseq		= $Row["seq"];
		$FRTitle	= strcut_utf8(strip_str($Row["title"]),15,"","..");
		$FRRegDate	= $Row["reg_date"];

		$retVal .= $FRTitle.":".$FRRegDate.":".$FRseq;
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	} ELSE {
		$retVal = "";
	}

	return $retVal;
}


FUNCTION fr_board_list($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen)
{
	global $page;
	global $num_per_page;
	global $strlen2;
	global $connect;

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

FUNCTION fr_board_list_menu($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen)
{
	global $num_per_page;
	global $strlen2;
	global $connect;

	$page = 1;

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

			if($frFieldArr[1] == "title" || $frFieldArr[1] == "content")
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
				$FR[$i][$fri] = $Row[$frFieldArr[1]];
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


function array2js($array,$show_keys)
{
	$dimensoes = array();
	$valores = array();
   
	$total = count ($array)-1;
	$i=0;
	foreach($array as $key=>$value){
		if (is_array($value)) {
			$dimensoes[$i] = array2js($value,$show_keys);
			if ($show_keys) $dimensoes[$i] = '"'.$key.'":'.$dimensoes[$i];
		} else {
			$dimensoes[$i] = '"'.addslashes($value).'"';
			if ($show_keys) $dimensoes[$i] = '"'.$key.'":'.$dimensoes[$i];
		}
		if ($i==0) $dimensoes[$i] = '{'.$dimensoes[$i];
		if ($i==$total) $dimensoes[$i].= '}';
		$i++;
	}
	return implode(',',$dimensoes);
} 


FUNCTION fr_board_view($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strLen)
{
	global $connect;
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


function fn_board_qna_recyn($obj)
{
	SWITCH($obj)
	{
		CASE "R" : $retVal = "답변대기"; BREAK;
		CASE "Y" : $retVal = "답변완료"; BREAK;
	}
	return $retVal;
}

FUNCTION fn_date_format($obj)
{
	$retval = sprintf("%02d",$obj);
	return $retval;
}

FUNCTION check_paykind($obj)
{
	SWITCH($obj)
	{
		CASE "C" : $retval = "카드"; BREAK;
		CASE "M" : $retval = "현금"; BREAK;
	}
	return $retval;
}

FUNCTION fn_smssend($sphone,$cphone,$t_msg,$connect)
{
	/* 파트너 고유키 호출 */
	$Query= "SELECT admin_phone,pid FROM cm_company";
	$Result = sql_query($Query,$connect);
	IF($Row=sql_fetch_array($Result))
	{
		$RowRepPhone	=	$Row["admin_phone"];
		$pid			=	$Row["pid"];
		sql_free_result($Result);
	}

	IF(!$pid)
	{
		return "PX";
		exit;
	}
	IF(!$sphone)
	{
		$sphone = $RowRepPhone;	// 받는사람
	}

	$objLength = check_utf8char_length($t_msg);

	IF($objLength > 90)
	{
		$url = "http://ader.co.kr/mms_general.php";
	} ELSE {
		$url = "http://ader.co.kr/sms_general.php";
	}

	$t_msg = ICONV("UTF-8","EUC-KR",urldecode($t_msg));

	$data = array('pid' => $pid, 'sphone'=>$sphone, 'cphone'=>$cphone, 't_msg'=> $t_msg);
	$data = http_build_query($data);

	$url = parse_url($url);

	$host = $url['host'];     
	$path = $url['path'];

	$fp = fsockopen($host, 80, $errno, $errstr, 30);

	if (!$fp) {
		echo "$errstr ($errno)<br />\n";
	} else {
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ".strlen($data)."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data . "\r\n\r\n");

		while (!feof($fp)) {
			$response .= fgets($fp, 4096);
		}
		fclose($fp);

		$response=explode("\r\n\r\n",$response);
	
		return $response[1];
	}
}

FUNCTION check_utf8char_length($obj)
{
	$objHan =  preg_replace("/[a-zA-Z0-9!#$%^&*(){}<>\[\]:;\"\'?+=@`.,-_\/[:space:]]/","",$obj);
	$objLength = strlen($obj);
	$objHanLength = strlen($objHan);

	/*
	echo "objHan : ".$objHan."<BR>";
	echo "objLength : ".$objLength."<BR>";
	echo "objHanLength : ".$objHanLength."<BR>";
	*/
	return (($objLength-$objHanLength)+(($objHanLength/3)*2));
}

FUNCTION fn_general_select($obj,$strkind,$strArr,$strTxt,$searchName,$strClass,$strJs)
{
	IF($strkind == "search")
	{
		$searchName = "S".$searchName;
	}
	IF($strkind == "search" || !$strkind)
	{
		$retval = "<select name='".$searchName."' id='".$searchName."'  ".$strClass." ".$strJs.">";
		$retval .= "<option value=''>".$strTxt."</option>";

		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			$strArrto = EXPLODE("^",$strArr[$i]);
			$retval .= "<option value='".$strArrto[0]."'";
			IF($strArrto[0] == $obj)
			{
				$retval .= " selected ";
			}	
			$retval .= ">".$strArrto[1]."</option>";
		}
		$retval .= "</select>";

	} ELSEIF($strkind == "radio") {
		
		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			$strArrto = EXPLODE("^",$strArr[$i]);
			$retval .= "<input type='radio' name='".$searchName."' ".$strClass." value='".$strArrto[0]."'";
			IF($strArrto[0] == $obj)
			{
				$retval .= " checked ";
			}	
			$retval .= " ".$strJs."> ".$strArrto[1]."&nbsp;";
		}

	} ELSEIF($strkind == "checkbox") {
		
		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			$strArrto = EXPLODE("^",$strArr[$i]);
			$retval .= "<input type='checkbox' name='".$searchName."' value='".$strArrto[0]."'";
			IF($strArrto[0] == $obj)
			{
				$retval .= " checked ";
			}	
			$retval .= " ".$strJs.">".$strArrto[1]."&nbsp;";
		}
		
	} ELSEIF($strkind == "txt") {

		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			$strArrto = EXPLODE("^",$strArr[$i]);
			IF($strArrto[0] == $obj)
			{
				$retval = $strArrto[1];
				break;
			}	
		}
	}
	return $retval;
}

FUNCTION fn_general_txt($obj,$strArr)
{
	FOR($i=0;$i<COUNT($strArr);$i++)
	{
		$strArrto = EXPLODE("^",$strArr[$i]);
		IF($strArrto[0] == $obj)
		{
			$retval = $strArrto[1];
			break;
		}	
	}
	return $retval;
}


FUNCTION fn_reffer_general_insert($gstrPHPSELF,$gsterREFFER,$gsterUserAgent,$gstrHttpHost,$gstrRequestURI,$gstrRemoteaddr,$connect)
{
	$strDate = DATE("Y-m-d");
	$strTime = DATE("H:i:s");

	$refferQuery = "INSERT INTO st_reffer (self_url,self_query,reffer_url,brower,reg_date,reg_time,ipaddr)
	VALUES
	('".$gstrPHPSELF."','".$gstrRequestURI."','".$gsterREFFER."','".$gsterUserAgent."','".$strDate."','".$strTime."','".$gstrRemoteaddr."');";
	sql_query($refferQuery,$connect);

	$refferInsertId = MYSQL_INSERT_ID();

	return $refferInsertId;
}

function check_content_pattern($obj)
{
	$PATTERN	=	"/[^0-9a-zA-Z가-힣\ \ㅇ\-\.\,\?\!]/";  // 영문,숫자,한글, -,.공백 ㅇ 만 출력
	$retval	=	preg_replace($PATTERN,"",$obj);

	return $retval;
}

function replace_title($strTitle)
{
	$strTitleVal = preg_replace("/[A-Za-z-0-9-[:punct:]]/", "", $strTitle); 
	$strTitleVal = TRIM($strTitleVal); 

	return $strTitleVal;
}

function replace_integer($strTitle)
{
	$strTitleVal = preg_replace("/[^0-9]/", "", $strTitle); 
	$strTitleVal = TRIM($strTitleVal); 

	return $strTitleVal;
}

function fwrite_stream($fp, $string) {
    for ($written = 0; $written < strlen($string); $written += $fwrite) {
        //$fwrite = fwrite($fp, iconv("EUCKR","UTF8",substr($string, $written)));
		$fwrite = fwrite($fp, substr($string, $written));
        if ($fwrite === false) {
            return $written;
        }
    }
    return $written;
}

function unique_replace($strVal)
{
	$strVal = str_replace("&","&amp;",$strVal);
	$strVal = str_replace("\"","&quot;",$strVal);
	$strVal = str_replace("'","&apos;",$strVal);
	$strVal = str_replace("<","&lt;",$strVal);
	$strVal = str_replace(">","&gt;",$strVal);

	return $strVal;
}

function fn_siteurl($obj)
{
	$obj = "http://".str_replace("http://","",$obj);
	return $obj;
}

FUNCTION fn_board_count($tbname,$rowcom,$Where,$connect)
{
	$Query= "UPDATE ".$tbname." SET ".$rowcom."=".$rowcom."+1 ".$Where;
	sql_query($Query,$connect);

}

FUNCTION fn_rep_img($obj,$strlink,$strclass,$strTxt)
{
	IF($obj)
	{
		$retval = "<img src='".$strlink."/".$obj."'";
		IF($strclass) { $retval .= " class='".$strclass."'"; }
		IF($strTxt) { $retval .= $strTxt; }
		$retval .= ">";
	} ELSE {
		/*
		$retval = "<img src='/images/noImageBig.jpg'";
		IF($strclass) { $retval .= " class='".$strclass."'"; }
		IF($strTxt) { $retval .= $strTxt; }
		$retval .= ">";
		*/
	}
	return $retval;
}

FUNCTION fn_board_date($obj)
{
	$retval = DATE("D",strtotime($obj))."., ".DATE("d/m/Y",strtotime($obj));
	return $retval;
}

FUNCTION fn_board_prev($colname,$SE,$tbname,$Where,$connect)
{
	$Query = "SELECT seq, title FROM ".$tbname." WHERE seq IN (SELECT MAX(".$colname.") as seq FROM ".$tbname." WHERE ".$colname." < ".$SE." ".$Where.")";

	$Result = sql_query($Query,$connect);

	IF($Row=sql_fetch_array($Result))
	{
		$seq = $Row["seq"];
		$title = $Row["title"];
		sql_free_result($Result);
	}
	IF(!$seq)
	{
		$seq = "";
		$title = "이전글이 없습니다";
	}
	return ARRAY($seq,$title);
}

FUNCTION fn_board_next($colname,$SE,$tbname,$Where,$connect)
{
	$Query = "SELECT seq, title FROM ".$tbname." WHERE seq IN (SELECT MIN(".$colname.") as seq  FROM ".$tbname." WHERE ".$colname." > ".$SE." ".$Where.")";

	$Result = sql_query($Query,$connect);

	IF($Row=sql_fetch_array($Result))
	{
		$seq	= $Row["seq"];
		$title	= $Row["title"];
		sql_free_result($Result);
	}

	IF(!$seq)
	{
		$seq = "";
		$title = "다음글이 없습니다";
	}

	return ARRAY($seq,$title);
}

FUNCTION fn_pid($kind,$connect)
{
	IF($kind)
	{
		$Query = "SELECT pid FROM cm_company";
		$Result = sql_query($Query,$connect);
		IF($Row=sql_fetch_array($Result))
		{
			$pid	 = $Row["pid"];
			sql_free_result($Result);
		}
		$retval = $pid;
	} ELSE {
		$retval = "";
	}
	return $retval;
}

FUNCTION fn_icon_new($gstrNdate,$reg_date,$strDay,$gstrKind)
{
	$_endDate = mktime(substr($gstrNdate,11,2),substr($gstrNdate,14,2),substr($gstrNdate,17,2),substr($gstrNdate,5,2),substr($gstrNdate,8,2),substr($gstrNdate,0,4));
	$_beginDate =  mktime(substr($reg_date,11,2),substr($reg_date,14,2),substr($reg_date,17,2),substr($reg_date,5,2),substr($reg_date,8,2),substr($reg_date,0,4));

	$timestamp_diff= $_endDate-$_beginDate + 1 ;
	$days_diff = floor($timestamp_diff/86400);

	IF($days_diff < $strDay)
	{
		SWITCH($gstrKind)
		{
			CASE "F" : $retval = "<img src='/images/f_new.gif'>"; BREAK;
			CASE "A" : $retval = "<img src='/images/b_new.gif'>"; BREAK;
		}
	}
	return $retval;
}

FUNCTION fn_file_link($strUrl,$strFile)
{
	$retval = "";
	IF($strUrl && $strFile)
	{
		$retval = "<a href='/inc/download.php?F=".$strUrl."&val=".$strFile."'>[첨부파일]</a>";
	}
	return $retval;
}

FUNCTION fn_file_download($strUrl,$filename,$strTXT)
{
	if(!$strTXT)
	{
		$strTXT = "[첨부파일]";
	}
	IF($filename)
	{
		$retval = "<a href='/inc/download.php?F=".$strUrl."&val=".$filename."'>".$strTXT."</a>";
	} ELSE {
		$retval = "";
	}
	return $retval;
}

function alert_confirm_form($target,$alert,$url,$SE,$SD)
{
    global $RD;

	ECHO "<form method='POST' name='regfm' action='".$url."' target='".$target."'>";
	ECHO "<input type='hidden' name='SE' value='".$SE."'>";
	ECHO "<input type='hidden' name='SD' value='".$SD."'>";
	ECHO "<input type='hidden' name='RD' value='".$RD."'>";
	ECHO "<input type='hidden' name='kdauth' value='1'>";
	ECHO "</form>";
	ECHO "<script language='javascript'>";
	IF($alert)
	{
		ECHO"alert('".$alert."');";
	}
	ECHO "document.getElementsByName('regfm')[0].submit();";
	ECHO "</script>";
	return;
}


FUNCTION fn_board_count_select($tbname,$rowcom,$obj,$connect)
{
	$Query= "SELECT COUNT(*) as CNT FROM ".$tbname." WHERE ".$rowcom."='".$obj."' AND Pnum <> '0' ";
	$Result = sql_query($Query,$connect);

	IF($Row=sql_fetch_array($Result))
	{
		$intCnt = $Row["CNT"];
		sql_free_result($Result);
	}
	return $intCnt;
}

function ICONV_UTF8($obj)
{
	$retVal = ICONV("UTF-8","EUC-KR",$obj);
	return $retVal;
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


FUNCTION fn_cm_company_info($connect)
{
	$Query = "SELECT company,com_num,rep_name,addr1,addr2,mo_num,rep_mail,rep_phone,cus_phone,rep_fax,bankname,banknumber,cacaoid,other1,other2,other3,zipcode,other4,other5,admin_phone,pid FROM cm_company ";

	$Result = sql_query($Query,$connect);

	IF($Row=sql_fetch_row($Result))
	{
		$RowVal[]	=	strip_str($Row[0]);	//company
		$RowVal[]	=	strip_str($Row[1]);	//com_num
		$RowVal[]	=	strip_str($Row[2]);	//rep_name
		$RowVal[]	=	strip_str($Row[3]);	//addr1
		$RowVal[]	=	strip_str($Row[4]);	//addr2
		$RowVal[]	=	strip_str($Row[5]);	//mo_num
		$RowVal[]	=	strip_str($Row[6]);	//rep_mail
		$RowVal[]	=	strip_str($Row[7]);	//rep_phone
		$RowVal[]	=	strip_str($Row[8]);	//cus_phone
		$RowVal[]	=	strip_str($Row[9]);	//rep_fax
		$RowVal[]	=	strip_str($Row[10]);	//bankname
		$RowVal[]	=	strip_str($Row[11]);	//banknumber
		$RowVal[]	=	strip_str($Row[12]);	//cacaoid
		$RowVal[]	=	EXPLODE("^",strip_str($Row[13]));	//other1
		$RowVal[]	=	strip_str($Row[14]);	//other2
		$RowVal[]	=	strip_str($Row[15]);	//other3
		$RowVal[]	=	strip_str($Row[16]);	//zipcode
		$RowVal[]	=	strip_str($Row[17]);	//other4
		$RowVal[]	=	strip_str($Row[18]);	//other5
		$RowVal[]	=	strip_str($Row[19]);	//admin_phone
		$RowVal[]	=	strip_str($Row[20]);	//pid

		sql_free_result($Result);
	}
	return $RowVal;
}


FUNCTION fn_img_view($kind, $strimgurl,$strimg,$strclass)
{
	IF($kind == "IMG")
	{
		IF($strimg)
		{
			$retval .= "<img src='".$strimgurl."/".$strimg."'";
			IF($strclass)
			{
				$retval .= "  class='".$strclass."'";
			}
			$retval .= ">";
		}
	} ELSE {
		$retval = "<a href='/inc/download.php?F=".$strimgurl."&val=".$strimg."'>";
		$retval .= "[".$kind."]";
		$retval .= "</a>";
	}

	return $retval;
}
FUNCTION check_reffer($reffer_url)
{
	IF(preg_match('#m.search.naver.com#',$reffer_url))
	{
		$retVal = "네이버 모바일";
	} ELSEIF(preg_match('#ad.search.naver.com#',$reffer_url))
	{
		$retVal = "네이버 검색광고";
	} ELSEIF(preg_match('#web.search.naver.com#',$reffer_url))
	{
		$retVal = "네이버";
	} ELSEIF(preg_match('#blog.naver.com#',$reffer_url))
	{
		$retVal = "네이버블로그";
	} ELSEIF(preg_match('#search.naver.com#',$reffer_url))
	{
		$retVal = "네이버";
	} ELSEIF(preg_match('#kin.naver.com#',$reffer_url))
	{
		$retVal = "네이버지식인";
	} ELSEIF(preg_match('#store.naver.com#',$reffer_url))
	{
		$retVal = "네이버쇼핑";
	} ELSEIF(preg_match('#kakao.com#',$reffer_url))
	{
		$retVal = "카카오";
	} ELSEIF(preg_match('#shopping.naver.com#',$reffer_url))
	{
		$retVal = "네이버쇼핑";
	} ELSEIF(preg_match('#cafe.naver.com#',$reffer_url)) {
		$retVal = "네이버 까페";
	} ELSEIF(preg_match('#mail.naver.com#',$reffer_url)) {
		$retVal = "네이버 메일";
	} ELSEIF(preg_match('#google.com#',$reffer_url)) {
		$retVal = "구글";
	} ELSEIF(preg_match('#google.co.kr#',$reffer_url)) {
		$retVal = "구글";
	} ELSEIF(preg_match('#maillink.co.kr#',$reffer_url)) {
		$retVal = "메일링크";
	} ELSEIF(preg_match('#coupang.com#',$reffer_url)) {
		$retVal = "쿠팡";
	} ELSEIF(preg_match('#zum.com#',$reffer_url)) {
		$retVal = "줌";
	} ELSEIF(preg_match('#daum.net#',$reffer_url)) {
		$retVal = "다음";
	} ELSEIF(preg_match('#tour.jeonju.go.kr#',$reffer_url)) {
		$retVal = "전주시청";
	} ELSEIF(preg_match('#ticketmonster.co.kr#',$reffer_url)) {
		$retVal = "티켓몬스터";
	} ELSEIF(preg_match('#bing.com#',$reffer_url)) {
		$retVal = "bing";
	} ELSEIF(preg_match('#auction.co.kr#',$reffer_url)) {
		$retVal = "옥션";
	} ELSEIF(preg_match('#facebook#',$reffer_url)) {
		$retVal = "페이스북";
	} ELSE {
		IF($reffer_url)
		{
			$retValarr	=	EXPLODE("/",STR_REPLACE("http://","",$reffer_url));
			$retVal = $retValarr[0];
		} ELSE { 
			$retVal = "기타";
		}
	}
	return $retVal;
}

FUNCTION check_reffer_abs($reffer_url, $agent)
{
	IF($reffer_url)
	{
		IF(preg_match('#m.search.naver.com#',$reffer_url))
		{
			$retVal = "네이버 모바일";
		} ELSEIF(preg_match('#ad.search.naver.com#',$reffer_url))
		{
			$retVal = "네이버 검색광고";
		} ELSEIF(preg_match('#web.search.naver.com#',$reffer_url))
		{
			$retVal = "네이버";
		} ELSEIF(preg_match('#blog.naver.com#',$reffer_url))
		{
			$retVal = "네이버블로그";
		} ELSEIF(preg_match('#search.naver.com#',$reffer_url))
		{
			$retVal = "네이버";
		} ELSEIF(preg_match('#kin.naver.com#',$reffer_url))
		{
			$retVal = "네이버지식인";
		} ELSEIF(preg_match('#store.naver.com#',$reffer_url))
		{
			$retVal = "네이버쇼핑";
		} ELSEIF(preg_match('#kakao.com#',$reffer_url))
		{
			$retVal = "카카오";
		} ELSEIF(preg_match('#shopping.naver.com#',$reffer_url))
		{
			$retVal = "네이버쇼핑";
		} ELSEIF(preg_match('#cafe.naver.com#',$reffer_url)) {
			$retVal = "네이버 까페";
		} ELSEIF(preg_match('#mail.naver.com#',$reffer_url)) {
			$retVal = "네이버 메일";
		} ELSEIF(preg_match('#google.com#',$reffer_url)) {
			$retVal = "구글";
		} ELSEIF(preg_match('#google.co.kr#',$reffer_url)) {
			$retVal = "구글";
		} ELSEIF(preg_match('#maillink.co.kr#',$reffer_url)) {
			$retVal = "메일링크";
		} ELSEIF(preg_match('#coupang.com#',$reffer_url)) {
			$retVal = "쿠팡";
		} ELSEIF(preg_match('#zum.com#',$reffer_url)) {
			$retVal = "줌";
		} ELSEIF(preg_match('#daum.net#',$reffer_url)) {
			$retVal = "다음";
		} ELSEIF(preg_match('#tour.jeonju.go.kr#',$reffer_url)) {
			$retVal = "전주시청";
		} ELSEIF(preg_match('#ticketmonster.co.kr#',$reffer_url)) {
			$retVal = "티켓몬스터";
		} ELSEIF(preg_match('#bing.com#',$reffer_url)) {
			$retVal = "bing";
		} ELSEIF(preg_match('#auction.co.kr#',$reffer_url)) {
			$retVal = "옥션";
		} ELSEIF(preg_match('#facebook#',$reffer_url)) {
			$retVal = "페이스북";
		} ELSEIF(preg_match('#instagram#',$reffer_url)) {
			$retVal = "인스타그램";
		} ELSE {
			IF($reffer_url)
			{
				$retValarr	=	EXPLODE("/",STR_REPLACE("https://","",STR_REPLACE("http://","",$reffer_url)));
				$retVal = $retValarr[0];

				IF(preg_match('#NAVER#',$agent))
				{
					$retVal .= " [앱] 네이버";
				} ELSEIF(preg_match('#KAKAOTALK#',$agent)) {
					$retVal .= " [앱] 카카오톡";
				} ELSEIF(preg_match('#FACEBOOK#',$agent)) {
					$retVal .= " [앱] 페이스북";
				} ELSEIF(preg_match('#KAKAOSTORY#',$agent)) {
					$retVal .= " [앱] 카카오스토리";
				} ELSEIF(preg_match('#Band#',$agent)) {
					$retVal .= " [앱] 밴드";
				} ELSEIF(preg_match('#Instagram#',$agent)) {
					$retVal .= " [앱] 인스타그램";
				} ELSEIF(preg_match('#BAND#',$agent)) {
					$retVal .= " [앱] 밴드";
				} ELSE {
					$retVal .= " [앱] 기타";
				}
			} ELSE { 
				$retVal = "기타";
			}
		}
	} ELSEIF($agent && !$reffer_url) {

		IF(preg_match('#NAVER#',$agent))
		{
			$retVal = "[앱] 네이버";
		} ELSEIF(preg_match('#KAKAOTALK#',$agent)) {
			$retVal = "[앱] 카카오톡";
		} ELSEIF(preg_match('#FACEBOOK#',$agent)) {
			$retVal = "[앱] 페이스북";
		} ELSEIF(preg_match('#KAKAOSTORY#',$agent)) {
			$retVal = "[앱] 카카오스토리";
		} ELSEIF(preg_match('#Band#',$agent)) {
			$retVal = "[앱] 밴드";
		} ELSEIF(preg_match('#Instagram#',$agent)) {
			$retVal = "[앱] 인스타그램";
		} ELSEIF(preg_match('#BAND#',$agent)) {
			$retVal = "[앱] 밴드";
		} ELSEIF(preg_match('#bot#',$agent)) {
			$retVal = "검색 봇";
		} ELSE {
			$retVal = "기타";
		}
	} ELSE {
		$retVal = "";
	}
	return $retVal;
}

FUNCTION check_reffer_query($reffer_url)
{
	$retVal1 = EXPLODE("query",$reffer_url);
	$retVal2 = EXPLODE("&",$retVal1[1]);
	$retVal = EXPLODE("=",$retVal2[0]);

	IF(preg_match('#blog.naver.com#',$reffer_url))
	{
		$reffer_url = STR_REPLACE("https://","",$reffer_url);

		IF(preg_match('#PostView.nhn#',$reffer_url))
		{
			$reffer_url = STR_REPLACE("blog.naver.com/PostView.nhn?","",$reffer_url);
			$retValex = EXPLODE("&",$reffer_url);

			FOR($i=0;$i<COUNT($retValex);$i++)
			{
				$retValArr = EXPLODE("=",$retValex[$i]);
				IF($retValArr[0] == "blogId")
				{
					$retval = $retValArr[1];
					break;
				}
			}
			return "blog`".urldecode($retval."post");
		} ELSE {
			$retVal = EXPLODE("/",$reffer_url);
			return "blog`".urldecode($retVal[1]);
		}

	} ELSEIF(preg_match('#ad.search.naver.com#',$reffer_url))
	{
		//return iconv("EUC-KR", "UTF-8", urldecode($retVal[1]));
		return "`".urldecode($retVal[1]);
	} ELSEIF(preg_match('#http://search.naver.com#',$reffer_url))
	{
		IF(preg_match('#ie=utf8#',$reffer_url))
		{
			return "`".urldecode($retVal[1]);
		} ELSE {
			return "`".iconv("EUC-KR", "UTF-8",urldecode($retVal[1]));
		}

	} ELSE {
		return "`".urldecode($retVal[1]);
	}
}

FUNCTION fn_check_phone($obj)
{
	$objval = EXPLODE("-",$obj);
	IF($objval[1] && $objval[2])
	{
		$retval = $obj;
	} ELSE { 
		$retval = "";
	}
	return $retval;
}

FUNCTION fn_shoporder_submit($ordernumber,$action,$SD,$alert)
{
	ECHO "<form name='regfm' action='".$action."' target='_parent' method='POST'>
			<input type='hidden' name='ordernumber' value='".$ordernumber."'>
			<input type='hidden' name='SD' value='".$SD."'>
		  </form>
		  <script type='text/javascript'>			
			  alert('".$alert."');
			  document.getElementsByName('regfm')[0].submit();
		  </script>
	";
}

FUNCTION fn_bank_code($obj)
{
	$strArr = ARRAY(
				ARRAY("03","기업은행"),
				ARRAY("04","국민은행"),
				ARRAY("05","외환은행"),
				ARRAY("07","수협중앙회"),
				ARRAY("11","농협중앙회"),
				ARRAY("20","우리은행"),
				ARRAY("23","SC제일은행"),
				ARRAY("31","대구은행"),
				ARRAY("32","부산은행"),
				ARRAY("34","광주은행"),
				ARRAY("37","전북은행"),
				ARRAY("39","경남은행"),
				ARRAY("53","한국씨티은행"),
				ARRAY("71","우체국"),
				ARRAY("81","하나은행"),
				ARRAY("88","통합신한은행(신한,조흥은행)"),
				ARRAY("D1","유안타증권(구 동양증권)"),
				ARRAY("D2","현대증권"),
				ARRAY("D3","미래에셋증권"),
				ARRAY("D4","한국투자증권"),
				ARRAY("D5","우리투자증권"),
				ARRAY("D6","하이투자증권"),
				ARRAY("D7","HMC투자증권"),
				ARRAY("D8","SK증권"),
				ARRAY("D9","대신증권"),
				ARRAY("DA","하나대투증권"),
				ARRAY("DB","굿모닝신한증권"),
				ARRAY("DC","동부증권"),
				ARRAY("DD","유진투자증권"),
				ARRAY("DE","메리츠증권"),
				ARRAY("DF","신영증권"),
				ARRAY("27","한국씨티은행 (한미은행)")
			);

	FOR($i=0;$i<COUNT($strArr);$i++)
	{
		IF($obj == $strArr[$i][0])
		{
			$retval = $strArr[$i][1];
			break;
		}
	}
	return $retval;
}

FUNCTION fn_general_process_link($kind, $retkind, $retaddlink)
{
	global $INSERT_ID;

	SWITCH($retkind)
	{
		CASE "1" : $strRetval = "&SD=1".$retaddlink; BREAK;
		CASE "2" : $strRetval = "&SD=2&SE=".$INSERT_ID.$retaddlink; BREAK;
		CASE "DEFAULT" : $strRetval = "&SD=2&SE=".$INSERT_ID.$retaddlink; BREAK;
	}

	SWITCH($kind)
	{
		CASE "save" : $strTxt = "등록"; BREAK;
		CASE "update" : $strTxt = "수정"; BREAK;
		CASE "del" : $strTxt = "삭제"; BREAK;
	}
	return ARRAY($strTxt,$strRetval);
}

FUNCTION fn_file_upload($fname,$strFileFolder,$strImgArr,$kind)
{
	global $_FILES;
	global $_POST;
	global $gstrNdate;

	$strIFilename		=	$_FILES[$fname]["name"];
	$strIFilenameTmp	=	$_FILES[$fname]["tmp_name"];

	$strIfileCheck		=	$_POST[$fname."_check"]; // 이미지 삭제
	$strIFilenameOr		=	$_POST[$fname."_or"]; // 원본파일 이미지 

	/* 다중 파일 업로드 */
	$intRand = RAND(10,99);
	$k = 0;
	for($i=0; $i<sizeof($strIFilename); $i++)
	{
		UNSET($strFileDelname);
		$intRand = $intRand + $i;

		FOR($j=0;$j<COUNT($strIfileCheck);$j++)
		{	
			if($strIFilenameOr[$i] == $strIfileCheck[$j]) 
			{
				$strFileDelname = $strIfileCheck[$j];
				break;
			}
		}
		IF($kind == "single")
		{
			$strIFileNameUpload[$i] = file_upload($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strFileDelname);
		} ELSEIF($kind == "multi") { 

			$strIFileName__ = file_upload($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strFileDelname);

			IF($strIFileName__)
			{
				IF($k > 0) { $strIFileNameUpload .= "^"; }
				$strIFileNameUpload .= $strIFileName__;
				$k++;
			}
		}
	}		

	return $strIFileNameUpload;
}

FUNCTION fn_file_upload_re($fname,$strFileFolder,$strImgArr,$i)
{
	global $_FILES;
	global $_POST;
	global $gstrNdate;

	$strIFilename		=	$_FILES[$fname]["name"];
	$strIFilenameTmp	=	$_FILES[$fname]["tmp_name"];

	$strIfileCheck		=	$_POST[$fname."_check"]; // 이미지 삭제
	$strIFilenameOr		=	$_POST[$fname."_or"]; // 원본파일 이미지 

	/* 다중 파일 업로드 */
	$intRand = RAND(10,99);

	$intRand = $intRand + $i;

	FOR($j=0;$j<COUNT($strIfileCheck);$j++)
	{	
		if($strIFilenameOr[$i] == $strIfileCheck[$j]) 
		{
			$strFileDelname = $strIfileCheck[$j];
			break;
		}
	}
	
	$strIFileNameUpload = file_upload($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strFileDelname);
		

	return $strIFileNameUpload;
}


function fn_st_cate_slave($scmseq,$connect)
{
	$Query = "SELECT scsseq,title FROM st_cate_slave WHERE recyn='Y'";
	IF($scmseq)
	{
		$Query .= " AND scmseq='".$scmseq."'";
	}
	$Query .=" ORDER BY sort_id ASC, scsseq";

	$Result = sql_query($Query,$connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{	
		$scsseq =	$Row["scsseq"];
		$title	=	add_str($Row["title"]);

		$retval[] = $scsseq."^".$title;
		$i++;
	}

	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}

function check_password($obj)
{
	$strTarget = ARRAY(
						ARRAY("영문","/[a-zA-Z]/"),
						ARRAY("숫자","/[0-9]/"),
						ARRAY("특수문자","/[#\&\+\-%@=\/\\:;,\.'\"\^`~\_|\!\?\*$#<>()\[\]\{\}]/")
					  );

	$intCnt = 0;
	FOR($i=0;$i<COUNT($strTarget);$i++)
	{
		IF (preg_match($strTarget[$i][1],$obj)) { 
			$intCnt++;
		}
	}
	return $intCnt;
}

FUNCTION fn_check_admin_login($strLevel, $connect)
{
	global $gstrRemoteaddr;
	global $gstrAdminLoginKind;

	IF($strLevel == 8 || $strLevel == 1)
	{
		$gstrCompanyInfo = fn_cm_company_info($connect);

		$strAdminKind = false;
		$gstrAdminIp = EXPLODE("^",$gstrCompanyInfo[18]);
		
		IF($gstrAdminIp[0])
		{
			FOR($i=0;$i<COUNT($gstrAdminIp);$i++)
			{
				IF($gstrAdminIp[$i] == $gstrRemoteaddr)
				{
					$strAdminKind = true;
				}
			}
		} ELSE {
			$strAdminKind = true;
		}

		IF($strAdminKind == false && $gstrAdminLoginKind == true)
		{
			$_SESSION["admin_mseq"]		= "";
			$_SESSION["admin_id"]		= "";
			$_SESSION["admin_name"]		= "";
			$_SESSION["admin_level"]	= "";
		}
	} ELSEIF($strLevel == 9) {
		$strAdminKind = true;
	}
	return $strAdminKind;
}

FUNCTION _json_encode($val)
{
	 if (is_string($val)) return '"'.addslashes($val).'"';
	 if (is_numeric($val)) return $val;
	 if ($val === null) return 'null';
	 if ($val === true) return 'true';
	 if ($val === false) return 'false';

	 $assoc = false;
	 $i = 0;
	 foreach ($val as $k=>$v){
		 if ($k !== $i++){
			 $assoc = true;
			 break;
		 }
	 }
	 $res = array();
	 foreach ($val as $k=>$v){
		 $v = _json_encode($v);
		 if ($assoc){
			 $k = '"'.addslashes($k).'"';
			 $v = $k.':'.$v;
		 }
		 $res[] = $v;
	 }
	 $res = implode(',', $res);
	 return ($assoc)? '{'.$res.'}' : '['.$res.']';
}

FUNCTION _json_decode($json) { 

    $json=preg_replace('/.+?({.+}).+/','$1',$json); 

	$json = json_decode($json);

    return $json; 
} 

FUNCTION fn_prifile_mv_view($strimgurl,$strimg,$strMovie,$strclass,$gstrMobileCheck,$autoplay)
{
	IF(!$emseq) { $emseq = "other"; }
	IF($gstrMobileCheck == false)
	{
		$strWidth = "640px;";
		$strHeigth = "480px;";
	} ELSE {
		$strWidth = "100%;";
		$strHeigth = "360px;";
	}

	IF($strMovie)
	{
		$retval = "<video id='movie-area' class='video-js vjs-default-skin' controls preload='none'  autoplay='".$autoplay."'  width='".$strWidth."' height='".$strHeigth."' poster='".$strimg."' data-setup='{}'>
					<source src='".$strimgurl."/".$strMovie."' type='video/mp4' />
				 </video>";
	}
	return $retval;
}
?>