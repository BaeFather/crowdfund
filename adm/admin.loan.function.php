<?php
FUNCTION fn_Search_S1($phmseq)
{
	global $connect;

	$Query = "SELECT hmseq, cname FROM hloan_member GROUP BY hmseq";
	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$hmseq	=	$Row["hmseq"];
		$cname	=	strip_str($Row["cname"]);
		$retval[] = ARRAY($hmseq, $cname);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}


function pagging_list($page,$total_page,$num_per_page,$urlVal,$urlVal3_)
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
		$page_val .= "<li class='p1'><a href='$PHP_SELF?page=".$page_first.$urlVal."'><img src='".$img_url."/llbtn.jpg'></a></li>";
	} else {
		$page_val .= "<li class='p1'><img src='".$img_url."/llbtn.jpg' style='cursor:hand' OnClick=\"alert('이전블록이 없습니다.');\"></li>";
	}

	if($page > 1) {
		$page_first = $page-1;
		$page_val .= "<li class='p1'><a href='$PHP_SELF?page=".$page_first.$urlVal."'><img src='".$img_url."/llbtn1.jpg'></a></li>";

	} else {
		$page_val .= "<li class='p1'><img src='".$img_url."/llbtn1.jpg' style='cursor:hand' OnClick=\"alert('이전페이지가 없습니다.');\"></li>";
	}

	for($i=$first_page+1;$i<$last_page+1;$i++)
	{
		$page_val.= "<li class='p2'><a href='$PHP_SELF?page=".$i.$urlVal."'>";

		if($i == $page) {
			$page_val .= "<span class='list_this'>".$i."</span>";
		} else {
			$page_val .= $i;
		}
		$page_val .= "</a></li>";

		if ($i < $total_page)
			$page_val .= " <li class='p_'><img src='".$img_url."/listbar.jpg'></li>";
		else
			break;
	}

	if($page < $total_page) {
		$page_next = $page+1;

		$page_val.= "<li class='p1'><a href='$PHP_SELF?page=".$page_next.$urlVal."'><img src='".$img_url."/rrbtn1.jpg'></a></li>";
	} else {
		$page_val .= "<li class='p1'><img src='".$img_url."/rrbtn1.jpg' style='cursor:hand' OnClick=\"alert('다음페이지가 없습니다.');\"></li>";
	}

	if($block < $total_block) {
		$last_page = $page+$num_per_page;

		if($last_page > $total_page) { $last_page = $total_page; }

		$page_val.= "<li class='p1'><a href='$PHP_SELF?page=".$last_page.$urlVal."'><img src='".$img_url."/rrbtn.jpg' ></a></li>";
	} else {
		$page_val .= "<li class='p1'><img src='".$img_url."/rrbtn.jpg' style='cursor:hand' OnClick=\"alert('다음블럭이 없습니다..');\"></li>";
	}


	$page_val .= "</ul></td></tr></table>";

	return $page_val;
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

		CASE "text" :
			ECHO "<INPUT TYPE='TEXT' name='".$strName."' id='".$strName."' ";
			IF($strVal || $strVal == 0) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."' style='display:inline;'>";
		BREAK;

		CASE "password" :
			ECHO "<INPUT TYPE='password' name='".$strName."' ";
			IF($strVal || $strVal == 0) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
		BREAK;

		CASE "checkbox" :
			ECHO "<INPUT TYPE='CHECKBOX' name='".$strName."' ";
			IF($strVal || $strVal == 0) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			IF($strClass) { ECHO " Class='".$strClass."'"; }
			ECHO ">";
		BREAK;

		CASE "radio" :
			ECHO "<INPUT TYPE='RADIO' name='".$strName."' ";
			IF($strVal || $strVal == 0) { ECHO " VALUE='".$strVal."' "; }
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
		BREAK;

		CASE "textarea" :
			ECHO "<TEXTAREA NAME='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			IF($strScript)	{ ECHO " ".$strScript; }
			ECHO " Class='".$strClass."'>";
			IF($strVal || $strVal == 0) { ECHO $strVal; }
			ECHO "</TEXTAREA>";
		BREAK;

		CASE "file_n" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal || $strVal == 0) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='".$strName."_check' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"".$strName."_or\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "fileimg_n" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal || $strVal == 0) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' width='150'></a> 파일삭제 : <input type='checkbox' name='".$strName."_check[]' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"".$strName."_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "file" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal || $strVal == 0) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'>".$strVal."</a> 파일삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "filetxt" :
			ECHO fn_file_link($strScript,$strVal);
		BREAK;

		CASE "sfile" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO fn_file_link($strScript,$strVal)." 파일삭제 : <input type='checkbox' name='s_file_check[]' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"s_file_or[]\" value=\"".$strVal."\">";
			} ELSE {
			ECHO "<input type=\"hidden\" name=\"s_file_check[]\" value=\"\">";
			ECHO "<input type=\"hidden\" name=\"s_file_or[]\" value=\"\">";
			}
		BREAK;

		CASE "sfileForm" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO fn_file_link($strScript,$strVal)." 파일삭제 : <input type='checkbox' name=\"".$strName."_check\" value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"".$strName."_or\" value=\"".$strVal."\">";
			} ELSE {
			ECHO "<input type=\"hidden\" name=\"".$strName."_check\" value=\"\">";
			ECHO "<input type=\"hidden\" name=\"".$strName."_or\" value=\"\">";
			}
		BREAK;

		CASE "sfileImg" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' class='srepimg NO-CACHE'></a> 파일삭제 : <input type='checkbox' name='s_file_check[]' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"s_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "fileImg" :
			ECHO "<INPUT TYPE='FILE' name='".$strName."' ";
			IF($strVaribables) { ECHO $strVaribables; }
			ECHO " Class='".$strClass."'>";
			IF($strVal) { ECHO " <BR><a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' class='srepimg NO-CACHE' border='0'></a> 이미지삭제 : <input type='checkbox' name='i_file_check[]' value=\"".$strVal."\">";
			ECHO "<input type=\"hidden\" name=\"i_file_or[]\" value=\"".$strVal."\">";
			}
		BREAK;

		CASE "fileImgatt" :
			IF($strVal)
			{
				ECHO "<a href='".$strScript."/".$strVal."' target='_blank'><img src='".$strScript."/".$strVal."' class='srepimg' border='0'></a>";
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
	$intRand = RAND(100,999);
	$k = 0;
	IF(sizeof($strIFilename) > 0)
	{
		FOR($i=0; $i<sizeof($strIFilename); $i++)
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
					IF($k > 0) { $strIFileNameUpload[0] .= "^"; }
					$strIFileNameUpload[0] .= $strIFileName__;
					$k++;
				}
			}
		}
	} ELSE {
		FOR($i=0; $i<sizeof($strIFilenameOr); $i++)
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
					IF($k > 0) { $strIFileNameUpload[0] .= "^"; }
					$strIFileNameUpload[0] .= $strIFileName__;
					$k++;
				}
			}
		}
	}

	return $strIFileNameUpload;
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

FUNCTION file_del($obj)
{
	@unlink($obj);
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

function add_date($orgDate,$mth,$mdh){
	$cd = strtotime($orgDate);
	$retDAY = date('Y-m-d H:i:s', mktime(date('H',$cd),date('i',$cd),date('s',$cd),date('m',$cd)+$mth,date('d',$cd)+$mdh,date('Y',$cd)));
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

function alert_back($alert,$go)
{
	echo "<script>";
	if($alert)  { echo " alert('".$alert."');"; }
	if($go)		{ echo " history.go('".$go."');"; }
	echo "</script>";
	return;
}

FUNCTION fr_board_list_re($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen,$connect)
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

FUNCTION fr_board_list2($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen)
{
	global $connect;
	global $num_per_page;
	global $strlen2;

	$page = 1;

	IF(!$strlen)
	{
		$strlen = 25;
	}

	$frlimit1 = $num_per_page*($page-1);	//시작페이지

	$frFieldVal = "";

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
		return $FR;
	}
}

FUNCTION fr_board_view($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strLen)
{
	global $connect;
	global $page;

	$frFieldVal		=	"";

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
	// echo $Query;
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
				$FR[$i][$frField[$fri]] = strip_str($Row[$frField[$fri]]);
				//$FR[$i][$frField[$fri]] = SUBSTR($Row[$frField[$fri]],0,10);
			} ELSE {
				$FR[$i][$frField[$fri]] = strip_str($Row[$frField[$fri]]);
			}

		}
		$i++;
	}

	IF($i > 0)
	{
		sql_free_result($Result);
	} ELSE {
		$FR = ARRAY();
	}

	return $FR;
}

FUNCTION fn_general_select($obj,$strkind,$strArr,$strTxt,$searchName,$strClass,$strJs)
{
	$retval = "";
	IF($strkind == "search")
	{
		$searchName = "S".$searchName;
	}
	IF($strkind == "search" || !$strkind)
	{
		$retval = "<select name='".$searchName."' ".$strClass." ".$strJs." style='display:inline-block; width:auto;'>";
		$retval .= "<option value=''>".$strTxt."</option>";

		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			//$strArrto = EXPLODE("^",$strArr[$i]);
			$retval .= "<option value='".$strArr[$i][0]."'";
			IF($strArr[$i][0] == $obj)
			{
				$retval .= " selected ";
			}
			$retval .= ">".$strArr[$i][1]."</option>";
		}
		$retval .= "</select>";

	} ELSEIF($strkind == "radio") {

		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			//$strArrto = EXPLODE("^",$strArr[$i]);
			$retval .= "<input type='radio' name='".$searchName."' value='".$strArr[$i][0]."' ";
			IF($strClass)
			{
				$retval .= $strClass;
			}
			IF($strArr[$i][0] == $obj)
			{
				$retval .= " checked ";
			}
			$retval .= " ".$strJs."> <label>".$strArr[$i][1]."</label>&nbsp;";
		}

	} ELSEIF($strkind == "checkbox") {

		$strObjArr = EXPLODE("^",$obj);

		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			$retval .= "<input type='checkbox' name='".$searchName."' value='".$strArr[$i][0]."'";

			IF($strObjArr > 1)
			{
				FOR($j=0;$j<COUNT($strObjArr);$j++)
				{
					IF($strArr[$i][0] == $strObjArr[$j])
					{
						$retval .= " checked ";
					}
				}
			} ELSE {
				IF($strArr[$i][0] == $obj)
				{
					$retval .= " checked ";
				}
			}
			IF($strClass)
			{
				$retval .= $strClass;
			}
			$retval .= " ".$strJs."> <label>".$strArr[$i][1]."</label>&nbsp;";
		}

	} ELSEIF($strkind == "txt") {

		$strObjArr = EXPLODE("^",$obj);
		$l = 0;
		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			IF($strObjArr > 1)
			{
				FOR($j=0;$j<COUNT($strObjArr);$j++)
				{
					IF($strArr[$i][0] == $strObjArr[$j])
					{
						IF($l > 0)
						{
							$retval .= ",";
						}
						$retval .= $strArr[$i][1];
						$l++;
					}
				}
			} ELSE {
				IF($strArr[$i][0] == $obj)
				{
					$retval = $strArr[$i][1];
					break;
				}
			}
		}
	} ELSEIF($strkind == "label") {

		$strObjArr = EXPLODE("^",$obj);
		$l = 0;
		FOR($i=0;$i<COUNT($strArr);$i++)
		{
			IF($strObjArr > 1)
			{
				FOR($j=0;$j<COUNT($strObjArr);$j++)
				{
					IF($strArr[$i][0] == $strObjArr[$j])
					{
						IF($l > 0)
						{
							$retval .= ",";
						}
						IF($strClass) { $retval .= "<span ".$strClass.">"; }
						$retval .= $strArr[$i][1];
						IF($strClass) { $retval .= "</span>"; }
						$l++;
					}
				}
			} ELSE {
				IF($strArr[$i][0] == $obj)
				{
					IF($strClass) { $retval .= "<span ".$strClass.">"; }
					$retval = $strArr[$i][1];
					IF($strClass) { $retval .= "</span>"; }
					break;
				}
			}
		}
	}
	return $retval;
}

FUNCTION fn_general_txt($obj,$strArr)
{
	FOR($i=0;$i<COUNT($strArr);$i++)
	{
		//$strArrto = EXPLODE("^",$strArr[$i]);
		IF($strArr[$i][0] == $obj)
		{
			$retval = $strArr[$i][1];
			break;
		}
	}
	return $retval;
}

FUNCTION fn_general_query_update($kind,$column,$cvalues,$tablename,$SEcolumn,$SE,$strWhere,$connect_db)
{
	$strColumnVal	= "";
	$strCvaluesVal	= "";

	IF($kind == "save")
	{
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

		sql_query($Query);
		$INSERT_ID = sql_insert_id();

		return $INSERT_ID;

	} ELSEIF($kind == "update") {

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
		sql_query($Query);

		return $SE;
	} ELSEIF($kind == "del") {

		IF(!$strWhere)
		{
			if ($tablename=="hloan_content") $Query = "UPDATE ".$tablename." SET del='Y' WHERE ".$SEcolumn."='".$SE."'";
			else $Query = "DELETE FROM ".$tablename." WHERE ".$SEcolumn."='".$SE."'";
		} ELSE {
			$Query = "DELETE FROM ".$tablename." ".$strWhere;
		}
		//echo $Query."\n";
		sql_query($Query);

		return $SE;
	}
}

FUNCTION fn_board_count($tbname,$rowcom,$Where,$connect)
{
	$Query= "UPDATE ".$tbname." SET ".$rowcom."=".$rowcom."+1 ".$Where;
	sql_query($Query,$connect);

}

FUNCTION fn_file_link($strUrl,$strFile)
{
	$retval = "";
	IF($strUrl && $strFile)
	{
		$retval = "<a href='/adm/helloloan_renew/download.php?F=".$strUrl."&val=".$strFile."'>".$strFile."</a>";
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


function ICONV_UTF8__($obj)
{
	$retVal = ICONV("UTF-8","EUC-KR",$obj);
	return $retVal;
}

function check_password__($obj)
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

FUNCTION f_number($obj)
{
	$retval = "";
	IF($obj)
	{
		$retval = NUMBER_FORMAT($obj);
	} ELSEIF(!$obj) {
		$retval = 0;
	}
	return $retval;
}

FUNCTION fn_date_replace($kind, $target, $obj)
{
	IF($kind == "1")
	{
		$obj = SUBSTR($obj, 0, 10);
	}
	IF($target)
	{
		$obj = STR_REPLACE("-",$target, $obj);
	}

	return $obj;
}

FUNCTION fn_banner_target()
{
	$retval = ARRAY(
				ARRAY("_blank","새창"),
				ARRAY("_self","본창"),
				ARRAY("_parent","부모창")
			  );
	return $retval;
}

FUNCTION fn_epilogue_section()
{
	$retval = ARRAY(
				ARRAY("1","인터뷰"),
				ARRAY("2","SNS리뷰"),
				ARRAY("3","추천평")
			  );
	return $retval;
}

FUNCTION fn_recommend_display_yn()
{
	$retval = ARRAY(
				ARRAY("R","대기"),
				ARRAY("N","비노출"),
				ARRAY("Y","노출")
			  );
	return $retval;
}

FUNCTION fn_epilogue_snskind()
{
	$retval = ARRAY(
				ARRAY("B","블로그"),
				ARRAY("F","페이스북"),
				ARRAY("C","까페"),
				ARRAY("T","티스토리")
			  );
	return $retval;
}


FUNCTION fn_mbirthday($obj)
{
	SWITCH($obj)
	{
		CASE "1" :
			FOR($i=(DATE("Y")-80);$i<=DATE("Y");$i++)
			{
				$retval[] = ARRAY($i, $i);
			}
		BREAK;
		CASE "2" :
			FOR($i=1;$i<=12;$i++)
			{
				$retval[] = ARRAY($i, sprintf("%02d",$i));
			}
		BREAK;
		CASE "3" :
			FOR($i=1;$i<=31;$i++)
			{
				$retval[] = ARRAY($i, sprintf("%02d",$i));
			}
		BREAK;
	}
	return $retval;
}

FUNCTION replace_title($strTitle)
{
	$strTitleVal = preg_replace("/[A-Za-z-0-9-[:punct:]]/", "", $strTitle);
	$strTitleVal = TRIM($strTitleVal);

	return $strTitleVal;
}

FUNCTION fn_general_process_link($kind, $retkind, $retaddlink)
{
	global $INSERT_ID;

	SWITCH($retkind)
	{
		CASE "1" : $strRetval = $retaddlink; BREAK;
		CASE "2" : $strRetval = "&RD=2&idx=".$INSERT_ID.$retaddlink; BREAK;
		CASE "DEFAULT" : $strRetval = "&RD=2&idx=".$INSERT_ID.$retaddlink; BREAK;
	}

	SWITCH($kind)
	{
		CASE "save" : $strTxt = "등록"; BREAK;
		CASE "update" : $strTxt = "수정"; BREAK;
		CASE "del" : $strTxt = "삭제"; BREAK;
	}
	return ARRAY($strTxt,$strRetval);
}

FUNCTION fn_general_process_link2($kind, $retkind, $retaddlink)
{
	global $INSERT_ID;

	SWITCH($retkind)
	{
		CASE "1" : $strRetval = $retaddlink; BREAK;
		CASE "2" : $strRetval = "&RD=2&SE=".$INSERT_ID.$retaddlink; BREAK;
		CASE "DEFAULT" : $strRetval = "&RD=2&SE=".$INSERT_ID.$retaddlink; BREAK;
	}

	SWITCH($kind)
	{
		CASE "save" : $strTxt = "등록"; BREAK;
		CASE "update" : $strTxt = "수정"; BREAK;
		CASE "del" : $strTxt = "삭제"; BREAK;
	}
	return ARRAY($strTxt,$strRetval);
}


FUNCTION fn_member_yn()
{
	$retval = ARRAY(
					ARRAY("Y","정상회원"),
					ARRAY("N","탈퇴회원")
				);
	return $retval;
}

FUNCTION fn_recyn()
{
	$retval = ARRAY(
					ARRAY("N","대기"),
					ARRAY("Y","승인"),
					ARRAY("M","심사중"),
					ARRAY("B","반려")
				);
	return $retval;
}

FUNCTION fn_recyn_report()
{
	$retval = ARRAY(
					ARRAY("N","비대상"),
					ARRAY("Y","대상")
				);
	return $retval;
}

FUNCTION fn_recm_kind()
{
	$retval = ARRAY(
					ARRAY("1","예치금"),
					ARRAY("2","포인트"),
					ARRAY("3","상품권/쿠폰")
				);
	return $retval;
}

FUNCTION fn_rep_img_list($strimgurl,$strimg,$strclass)
{
	$retval = "<img src='".$strimgurl."/".$strimg."' class='".$strclass."'>";

	return $retval;
}

FUNCTION fn_replace_product($obj)
{
	$obj = str_replace("<div>&nbsp;</div>","",$obj);
	$obj = str_replace("width=","alt=",$obj);
	$obj = str_replace("height=","alt=",$obj);
	return $obj;
}

FUNCTION fn_replace_iframe($obj, $mobilekind)
{
	IF($mobilekind)
	{
		$obj = str_replace("<div>&nbsp;</div>","",$obj);
		$obj = str_replace("width=","alt=",$obj);
		$obj = str_replace("height=","alt=",$obj);
		$obj = str_replace("<iframe ","<iframe width='100%' height='220' ",$obj);
	}
	return $obj;
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


FUNCTION fn_re_name($obj)
{
	$intLength = strlen($obj);

	IF($intLength >= 9)
	{
		$strObj1   = SUBSTR($obj,0,3);
		$strObj2   = "*";
		$strObj3   = SUBSTR($obj,6,3);
	} ELSE {
		$strObj1   = SUBSTR($obj,0,3);
		$strObj2   = "*";
		$strObj3   = "";
	}
	$strObj = $strObj1.$strObj2.$strObj3;
	return $strObj;
}

FUNCTION fn_hello_content_master_cnt($hmseq, $connect)
{
	global $strPartRecyn;

	$Query = "SELECT recyn , COUNT(*) as CNT FROM hloan_content WHERE hmseq='".add_str($hmseq)."' GROUP BY  recyn ORDER BY hmseq ASC";

	$Result = sql_query($Query, $connect);

	$i = 0;
	$intTotal = 0;
	$intCnt = 0;
	$strRecyn = "";

	$recyn = ARRAY();
	$CNT   = ARRAY();

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$recyn[]	=	strip_str($Row["recyn"]);
		$CNT[]		=	strip_str($Row["CNT"]);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	FOR($i=0;$i<COUNT($strPartRecyn);$i++)
	{
		$intCnt = 0;
		FOR($j=0;$j<COUNT($recyn);$j++)
		{
			IF($strPartRecyn[$i][0] == $recyn[$j])
			{
				$intCnt = $CNT[$j];
				break;
			}
		}
		$retval[$strPartRecyn[$i][0]] = $intCnt;
		$intTotal += $intCnt;
	}

	// 총등록
	$retval["T"] = $intTotal;

	return $retval;
}

FUNCTION fn_hello_content_slave_cnt($hmseq, $connect)
{
	global $strPartRecyn;

	$Query = "
	 SELECT IFNULL(t2.recyn,'') as recyn , SUM(IFNULL(t2.CNT,0)) as CNT FROM
	(SELECT hmseq FROM hloan_member WHERE phmseq='".add_str($hmseq)."' AND level='1') t1 LEFT JOIN
	(SELECT hmseq, recyn , COUNT(*) as CNT FROM hloan_content GROUP BY hmseq, recyn) t2 ON t1.hmseq=t2.hmseq
    GROUP BY recyn
	ORDER BY t1.hmseq ASC,recyn
	";

	$Result = sql_query($Query, $connect);

	$i = 0;
	$intTotal = 0;
	$intCnt = 0;
	$strRecyn = "";

	$recyn = ARRAY();
	$CNT   = ARRAY();

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$recyn[]	=	strip_str($Row["recyn"]);
		$CNT[]		=	strip_str($Row["CNT"]);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	FOR($i=0;$i<COUNT($strPartRecyn);$i++)
	{
		$intCnt = 0;
		FOR($j=0;$j<COUNT($recyn);$j++)
		{
			IF($strPartRecyn[$i][0] == $recyn[$j])
			{
				$intCnt = $CNT[$j];
				break;
			}
		}
		$retval[$strPartRecyn[$i][0]] = $intCnt;
		$intTotal += $intCnt;
	}

	// 총등록
	$retval["T"] = $intTotal;

	return $retval;
}

FUNCTION fn_hellloan_kind()
{
	$retval = ARRAY(
					ARRAY("T","등록"),
					ARRAY("N","대기"),
					ARRAY("M","심사중"),
					ARRAY("Y","승인"),
					ARRAY("B","반려")
				);
	return $retval;
}

FUNCTION fn_hellloan_kind_renew()
{
	$retval = ARRAY(
					ARRAY("T","등록"),
					ARRAY("M","심사중"),
					ARRAY("B","반려/취소"),
					ARRAY("Y","최종승인")
				);
	return $retval;
}

FUNCTION fn_hellloan_search_kind_back()
{
	$retval = ARRAY(
					ARRAY("A","전체"),
					ARRAY("N","대기"),
					ARRAY("M","심사중"),
					ARRAY("Y","승인"),
					ARRAY("B","반려")
				);
	return $retval;
}

FUNCTION fn_smsyn()
{
	$retval = ARRAY(
					ARRAY("Y","예"),
					ARRAY("N","아니오")
				);
	return $retval;
}

FUNCTION fn_hellloan_search_kind()
{
	/*
	$retval	=	ARRAY(
					ARRAY("A","전체"),
					ARRAY("1","1차 심사중"),
					ARRAY("2","1차 승인"),
					ARRAY("3","2차 심사중"),
					ARRAY("4","최종승인"),
					ARRAY("5","부재"),
					ARRAY("6","대출취소"),
					ARRAY("7","협의지연"),
					ARRAY("8","차주통화완료"),
					ARRAY("9","고객자서예정(캘린더-예정일)"),
					ARRAY("10","고객자서완료"),
					ARRAY("11","펀딩중"),
					ARRAY("12","기표완료"),
					ARRAY("13","반려")
				);
	*/

	$retval	=	ARRAY(
					ARRAY("A","전체"),
					ARRAY("1","심사중"),
					ARRAY("2","승인"),
					ARRAY("4","최종승인"),
					ARRAY("5","부재"),
					ARRAY("6","대출취소"),
					ARRAY("7","협의지연"),
					ARRAY("8","차주통화완료"),
					ARRAY("9","고객자서예정(캘린더-예정일)"),
					ARRAY("10","고객자서완료"),
					//ARRAY("11","펀딩중"),
					ARRAY("11","심의요청"),
					ARRAY("12","기표완료"),
					ARRAY("13","반려"),
					ARRAY("14","감액요청")
				);
	return $retval;
}

FUNCTION fn_loan_arecyn()
{
	$retval	=	ARRAY(
					ARRAY("4","1차 심사중"),
					ARRAY("5","1차 승인"),
					ARRAY("6","2차 심사중"),
					ARRAY("7","최종승인"),
					ARRAY("1","협력사 반려"),
					ARRAY("2","헬로펀딩 반려"),
					ARRAY("3","감액요청")
				);
	return $retval;
}

FUNCTION fn_hellloan_search_kind_renew()
{
	$retval	=	ARRAY(
					ARRAY("7","최종승인"),
					ARRAY("1","대출취소"),
					ARRAY("8","협의지연"),
					ARRAY("9","차주통화완료"),
					ARRAY("10","자서예정"),
					ARRAY("11","자서완료"),
					ARRAY("12","펀딩중"),
					ARRAY("13","기표완료")
				);
	return $retval;
}

Function hloan_voteyn_renew()
{
	$retval = ARRAY(
					ARRAY("5","1차 승인"),
					ARRAY("2","헬로펀딩 반려"),
					ARRAY("3","감액요청")
			  );
	return $retval;
}

function  fn_hellloan_member($connect,$level)
{
	$Query = "SELECT hmseq, cname FROM hloan_member_renew WHERE level='".$level."'";
	$Result = sql_query($Query);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$retval[]	=	ARRAY($Row["hmseq"], $Row["cname"]);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}


FUNCTION fn_loan_name_replace($obj,$objtarget)
{
	$retval = EXPLODE($objtarget,$obj);
	return $retval[1];
}

FUNCTION fn_hellloan_search_kind_2_renew()
{
	$retval	=	ARRAY(
					ARRAY("7","최종승인"),
					ARRAY("2","반려")
				);
	return $retval;
}


FUNCTION fn_cratring()
{
	$retval = ARRAY(
					ARRAY("1","1등급"),
					ARRAY("2","2등급"),
					ARRAY("3","3등급"),
					ARRAY("4","4등급"),
					ARRAY("5","5등급"),
					ARRAY("6","6등급"),
					ARRAY("7","7등급"),
					ARRAY("8","8등급"),
					ARRAY("9","9등급"),
					ARRAY("A","미확인")
			);
	return $retval;
}

FUNCTION fn_ltvkind()
{
	$retval	=	ARRAY(
					ARRAY("1","일반가"),
					ARRAY("2","하한가")
				);
	return $retval;
}

FUNCTION fn_rowner()
{
	$retval	=	ARRAY(
					ARRAY("1","본인"),
					ARRAY("2","가족"),
					ARRAY("3","중개인")
				);
	return $retval;
}

FUNCTION fn_tenant()
{
	$retval	=	ARRAY(
					ARRAY("Y","있음"),
					ARRAY("N","없음")
				);
	return $retval;
}

FUNCTION fn_hideyn()
{
	$retval	=	ARRAY(
					ARRAY("Y","등록(전송)"),
					ARRAY("N","임시(본인만)")
				);
	return $retval;
}

FUNCTION fn_Search_S2()
{
	$retval	=	ARRAY(
					ARRAY("laddr","담보물주소"),
					ARRAY("pname","원차주명")
				);
	return $retval;
}

FUNCTION fn_Search_S2_new()
{
	$retval	=	ARRAY(
					ARRAY("laddr","담보물주소"),
					ARRAY("lenmember","원차주명"),
					ARRAY("hnum","접수번호")
				);
	return $retval;
}


FUNCTION fn_Search_withholding_S1()
{
	$retval	=	ARRAY(
					ARRAY("mb_name","상호(성함)"),
					ARRAY("mb_jumin","사업자번호(주민등록번호)"),
					ARRAY("mb_jumin","이메일"),
					ARRAY("recyn^N","상태(미확인)"),
					ARRAY("recyn^R","상태(요청)"),
					ARRAY("recyn^Y","상태(완료)")
				);
	return $retval;
}


FUNCTION fn_Search_hmember()
{
	$retval	=	ARRAY(
					ARRAY("cname","여신회사명"),
					ARRAY("hname","담당자")
				);
	return $retval;
}

FUNCTION fn_Search_hmember_new()
{
	$retval	=	ARRAY(
					ARRAY("cname","중개법인")
				);
	return $retval;
}


FUNCTION fn_hmember_level()
{
	$retval	=	ARRAY(
					ARRAY("1","여신회사"),
					ARRAY("2","총괄")
				);
	return $retval;
}

FUNCTION fn_hmember_recyn()
{
	$retval	=	ARRAY(
					ARRAY("Y","로그인가능"),
					ARRAY("N","로그인불가")
				);
	return $retval;
}

FUNCTION fn_widhholding_member_type()
{
	$retval	=	ARRAY(
					ARRAY("1","개인"),
					ARRAY("2","사업자")
				);
	return $retval;
}

FUNCTION fn_widhholding_rkind()
{
	$retval	=	ARRAY(
					ARRAY("1","귀속"),
					ARRAY("2","지급")
				);
	return $retval;
}

FUNCTION fn_widhholding_recyn()
{
	$retval	=	ARRAY(
					ARRAY("N","미확인"),
					ARRAY("R","요청"),
					ARRAY("Y","완료")
				);
	return $retval;
}

FUNCTION fn_widhholding_prcess_1()
{
	$retval	=	ARRAY(
					ARRAY("","선택한회원"),
					ARRAY("A","모든회원")
				);
	return $retval;
}

FUNCTION fn_product_manager($connect)
{
	$Query = "SELECT mb_no, mb_name FROM g5_member WHERE mb_level='9' AND mb_name LIKE '%상품관리-%' ORDER BY mb_name, mb_no ASC";

	$Result = sql_query($Query, $connect);

	$i = 0;
	$retval = ARRAY();
	WHILE($Row=sql_fetch_array($Result))
	{
		$mb_no		=	strip_str($Row["mb_no"]);
		$mb_name	=	strip_str($Row["mb_name"]);

		$retval[]	= ARRAY($mb_no,$mb_name);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}

	return $retval;
}

FUNCTION fn_g5_member($mb_id)
{
	IF($mb_id)
	{
		$Query = "SELECT mb_no, mb_name FROM g5_member WHERE mb_id='".add_str($mb_id)."'";

		$Result = sql_query($Query, $connect);

		$i = 0;
		$retval = ARRAY();
		WHILE($Row=sql_fetch_array($Result))
		{
			$mb_no		=	strip_str($Row["mb_no"]);
			$mb_name	=	strip_str($Row["mb_name"]);

			$retval	= ARRAY($mb_no,$mb_name);
			$i++;
		}
		IF($i > 0)
		{
			sql_free_result($Result);
		}
	} ELSE {
		$retval = ARRAY("","");
	}
	return $retval;
}

FUNCTION fn_product_hello()
{
	$retval	=	ARRAY(
					ARRAY("1","대기"),
					ARRAY("2","기표준비"),
					ARRAY("3","모집"),
					ARRAY("4","상환중"),
					ARRAY("5","상환완료"),
					ARRAY("6","취소"),
					ARRAY("7","보류"),
					ARRAY("8","기표완료")
				);
	return $retval;
}


FUNCTION fn_product_list($connect)
{
	$Query = "SELECT idx, title FROM cf_product ORDER BY idx DESC";

	$Result = sql_query($Query, $connect);

	$i = 0;
	$retval = ARRAY();
	WHILE($Row=sql_fetch_array($Result))
	{
		$idx		=	strip_str($Row["idx"]);
		$title		=	strip_str($Row["title"]);

		$retval[]	= ARRAY($idx,$title);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}

	return $retval;
}

Function fn_hloan_member($level, $seq, $connect)
{
	$Query = "SELECT hmseq, cname FROM hloan_member WHERE recyn='Y'";
	IF($level)
	{
		$Query .= " AND level='".add_str($level)."'";
	}
	IF($seq)
	{
		$Query .= " AND hmseq='".add_str($seq)."'";
	}
	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$hmseq	=	$Row["hmseq"];
		$cname	=	strip_str($Row["cname"]);
		$retval[] = ARRAY($hmseq, $cname);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}

Function fn_recommend_event_config($SE, $connect)
{
	$Query = "
		SELECT
			event_no, event_title, sdate, edate, recmdee_reward_type, recmdee_reward_goods_name, recmdee_reward_point, recmder_reward_type, recmder_reward_goods_name, recmder_reward_point, use_point
		FROM
			recommend_event_config";
	IF($SE)
	{
		$Query .= " WHERE event_no='".add_str($SE)."'";
	} ELSE {
		$Query .= " ORDER BY idx DESC";
	}
	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$event_no                  = $Row["event_no"];
		$event_title               = strip_str($Row["event_title"]);
		$sdate                     = strip_str($Row["sdate"]);
		$edate                     = strip_str($Row["edate"]);
		$recmdee_reward_type       = strip_str($Row["recmdee_reward_type"]);
		$recmdee_reward_goods_name = strip_str($Row["recmdee_reward_goods_name"]);
		$recmdee_reward_point      = strip_str($Row["recmdee_reward_point"]);
		$recmder_reward_type       = strip_str($Row["recmder_reward_type"]);
		$recmder_reward_goods_name = strip_str($Row["recmder_reward_goods_name"]);
		$recmder_reward_point      = strip_str($Row["recmder_reward_point"]);
		$use_point                 = strip_str($Row["use_point"]);
		$retval[] =	ARRAY($event_no, $event_title, $sdate, $edate, $recmdee_reward_type, $recmdee_reward_goods_name, $recmdee_reward_point, $recmder_reward_type, $recmder_reward_goods_name, $recmder_reward_point, $use_point);
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}

FUNCTION fn_loankind()
{
	$retval = ARRAY(
					ARRAY("1","선순위"),
					ARRAY("2","후순위")
			  );
	return $retval;
}

FUNCTION fn_skind()
{
	$retval = ARRAY(
					ARRAY("1","아파트")
			  );
	/*
	$retval = ARRAY(
					ARRAY("1","아파트"),
					ARRAY("2","오피스텔"),
					ARRAY("3","빌라")
			  );
	*/
	return $retval;
}

FUNCTION fn_check_ltv($obj)
{
	IF($obj > 83)
	{
		$retval = "<span class='fred f12'>".$obj."</span>";
	} ELSE {
		$retval = $obj;
	}
	return $retval;
}


FUNCTION fn_mkind()
{
	FOR($i=0;$i<=24;$i++)
	{
		IF($i == 0)
		{
			$retval[] = ARRAY("A","1개월 미만");
		} ELSE {
			$retval[] = ARRAY($i,$i."개월");
		}
	}
	return $retval;
}

FUNCTION fn_mdate_pro($mkind, $mdate)
{
	IF($mkind == "A")
	{
		$retval = $mdate."일";
	} ELSE {
		$retval = $mkind."개월";
	}
	return $retval;
}


Function hloan_voteyn($mblevel)
{
	SWITCH($mblevel)
	{
		CASE "2" :
			$retval = ARRAY(
							ARRAY("9","승인"),
							ARRAY("8","승인-감액"),
							ARRAY("4","반려")
					  );
		BREAK;
		DEFAULT :
			$retval = ARRAY(
							ARRAY("2","부결"),
							ARRAY("3","가결"),
							ARRAY("1","감액")
					  );
		BREAK;

	}
	return $retval;
}

Function hloan_admin_member_vote($SE, $connect)
{
	global $strSelectBox2;

	$strTable	=	"hloan_admin_member_vote t1 LEFT JOIN hloan_admin_member t2 ON t1.midx=t2.midx";
	$strWhere	=	" WHERE t1.hcseq='".add_str($SE)."' AND t2.mb_level='2'";
	$strOrder	=	"t1.midx";
	$strColumn	=	"";
	$intLimit1	=	0;
	$intLimit2	=	1;
	$intStrlen	=	100;

	$rowView = fr_board_view(ARRAY("votyn"),$strTable,"",$strWhere,$strOrder,$intLimit1,$intLimit2,$intStrlen);

	$strDisabled = "";
	IF($rowView[0]["votyn"] > 0)
	{
		$strDisabled = "disabled";
	}

	$Query = "SELECT t1.midx, t1.mb_no, t1.mb_id, t1.mb_name, t1.mb_level,
					 IFNULL(t2.idx,0) as idx, IFNULL(t2.votyn,'')as votyn
			  FROM
			  hloan_admin_member t1
			  LEFT JOIN
			  (SELECT idx,midx, votyn FROM hloan_admin_member_vote WHERE hcseq='".add_str($SE)."') t2
			  ON t1.midx=t2.midx
			  ORDER BY t1.sort_id ASC";
	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$midx					=	$Row["midx"];
		$mb_no					=	$Row["mb_no"];
		$mb_id					=	strip_str($Row["mb_id"]);
		$mb_name				=	strip_str($Row["mb_name"]);
		$mb_level				=	$Row["mb_level"];
		$idx					=	$Row["idx"];
		$votyn					=	$Row["votyn"];

		IF($mb_level == "2") { $strDisabled = ""; }

		$retval[]				=	"<div class='votarea'>".$mb_name."&nbsp;&nbsp;".fn_general_select($votyn,$strSelectBox2,hloan_voteyn($mb_level),"::심사구분::","votyn[]","OnChange=\"check_admin_member_vote('".$midx."','".$mb_level."',this.value,'".$i."','".$SE."',event);\" ".$strDisabled,"")."</div>";
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}

	return $retval;
}

Function hloan_admin_member($connect)
{
	$Query = "SELECT midx, mb_no, mb_name FROM hloan_admin_member WHERE recyn='Y' ORDER BY sort_id";
	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$midx	=	$Row["midx"];
		$mname	=	strip_str($Row["mb_name"]);
		$retval[] = ARRAY($midx, $mname);
		$i++;
	}

	IF($i > 0)
	{
		sql_free_result($Result);
	}

	return $retval;
}

FUNCTION fn_check_addr($obj)
{
	$objArr	=	EXPLODE(" ",$obj);
	IF($objArr[1] == "시")
	{
		$strAddr1 = $objArr[0];
		$strAddr2 = $objArr[1]." ".$objArr[2];
		FOR($i=3;$i<COUNT($objArr);$i++)
		{
			IF($i > 3) { $strAddr3 .= " "; }
			$strAddr3 .= $objArr[$i];
		}
	} ELSE {
		$strAddr1 = $objArr[0];
		$strAddr2 = $objArr[1];
		FOR($i=2;$i<COUNT($objArr);$i++)
		{
			IF($i > 2) { $strAddr3 .= " "; }
			$strAddr3 .= $objArr[$i];
		}
	}

	return ARRAY($strAddr1, $strAddr2, $strAddr3);
}


Function fn_hloan_member_cnt($connect)
{
	$Query = "SELECT hmseq,recyn, count(*) as CNT, SUM(ddmoney) as TSUM FROM hloan_content  GROUP BY hmseq,recyn";

	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$hmseq					=	$Row["hmseq"];
		$recyn					=	strip_str($Row["recyn"]);
		$CNT					=	strip_str($Row["CNT"]);
		$TSUM					=	strip_str($Row["TSUM"]);

		$retval[0][$hmseq][$recyn]	=	$CNT;
		$retval[1][$hmseq][$recyn]	=	$TSUM;
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
}

Function fn_hloan_member_cnt_renew($connect)
{
	$Query = "SELECT hmseq,recyn, count(*) as CNT, SUM(ddmoney) as TSUM FROM hloan_content_renew  GROUP BY hmseq,recyn";

	$Result = sql_query($Query, $connect);

	$i = 0;
	WHILE($Row=sql_fetch_array($Result))
	{
		$hmseq					=	$Row["hmseq"];
		$recyn					=	strip_str($Row["recyn"]);
		$CNT					=	strip_str($Row["CNT"]);
		$TSUM					=	strip_str($Row["TSUM"]);

		$retval[0][$hmseq][$recyn]	=	$CNT;
		$retval[1][$hmseq][$recyn]	=	$TSUM;
		$i++;
	}
	IF($i > 0)
	{
		sql_free_result($Result);
	}
	return $retval;
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

FUNCTION fr_board_list($frField,$frTable,$frQuery,$frWhere,$frorder,$frlimit1,$frlimit2,$strlen)
{
	global $connect;
	global $page;
	global $num_per_page;
	global $strlen2;

	$frTotal		= 0;
	$Frtotalpage	= 0;

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

	$frFieldVal = "";

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
				//$FR[$i][$fri] = SUBSTR($Row[$frFieldArr[1]],0,10);
				//$FR[$i][$fri] = strip_str($Row[$frFieldArr[1]]);
				$FR[$i][$fri] = $Row[$frFieldArr[1]];
			} ELSE {
				//$FR[$i][$fri] = strip_str($Row[$frFieldArr[1]]);
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

/** 관리자 **/

Class Product_Calculate
{
	public $DB_RESULT;
	public $ndate;

	Public  function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public  function __destruct()
	{
	}

	Public Function RsCount()
	{
		$row =	sql_num_rows($this->DB_RESULT);
		return $row;
	}

	function add_date($givendate,$day=0,$mth=0,$yr=0)
	{
		$cd = strtotime($givendate);
		$newdate = date('Y-m-d h:i:s', mktime(date('h',$cd),
		date('i',$cd), date('s',$cd), date('m',$cd)+$mth,
		date('d',$cd)+$day, date('Y',$cd)+$yr));
		return $newdate;
    }

	Public Function Product_view($prd_idx)
	{
		//   /lib/repay_calculation.php' 펑션
		global $BANK;

		$INV_ARR   = repayCalculation($prd_idx);

		$INI       = $INV_ARR['INI'];
		$PRDT      = $INV_ARR['PRDT'];
		$REPAY_SUM = $INV_ARR['REPAY_SUM'];
		$LOANER    = $INV_ARR['LOANER'];


		$title							=	$PRDT['title']; // 상품명
		$recruit_amount					=	$PRDT['recruit_amount']; // 대출금액
		$loan_interest_rate				=	sprintf('%.2f', $PRDT['loan_interest_rate']); // 대출이자(금리)
		$loan_date_range				=	($PRDT['state']=='') ? $PRDT['invest_period'].'개월' : preg_replace('/-/', '.', $PRDT['loan_start_date']).' ~ '.preg_replace('/-/', '.', $INI['loan_end_date']); // 대출기간

		$invest_usefee					=	$REPAY_SUM['invest_usefee']; // 플랫폼 이용료
		$invest_bank					=	"우리은행 1005-203-046258 ㈜헬로핀테크";

		IF($PRDT['state'] <> "" && $PRDT["loan_start_date"])	// 기표가 있고, 시작일이 있으면
		{
			$loan_date					=	$PRDT["loan_start_date"];
		} ELSE {
			$loan_date					=	$this->ndate;		// 대출실행일
		}

		$repay_acct_no					=	$PRDT['repay_acct_no']; // 이자 입금 계좌

		$loaner = ($LOANER['member_type']=='2') ? $LOANER['mb_co_name'] : $LOANER['mb_name'];

		// 대출금 입금계좌
		$j = 0;
		FOR($i=1;$i<6;$i++)
		{
			$BANK_KEYS = array_keys($BANK);
			for($x=0; $x<count($BANK); $x++) {
				IF($PRDT['loan_dep_bank_cd'.$i]==$BANK_KEYS[$x])
				{
					$loan_dep_bank_cd = $BANK[$BANK_KEYS[$x]];
					break;
				}
			}
			$loan_dep_acct_nb = $PRDT["loan_dep_acct_nb".$i];
			$loan_dep_amt	  = $PRDT["loan_dep_amt".$i];
			$loan_dep_acct_memo = $PRDT['loan_dep_acct_memo'.$i];

			IF($loan_dep_acct_nb)
			{
				IF($j > 0)
				{
					$loan_dep		  .= "<br />";
				}
				$loan_dep .= $loan_dep_bank_cd."&nbsp;|&nbsp;".$loan_dep_acct_nb."(".$loan_dep_acct_memo.")&nbsp;|&nbsp".NUMBER_FORMAT($loan_dep_amt)." 원";
				$j++;
			}

		}

		IF(DATE("L") == 1) { $intDate = 366; } ELSE { $intDate = 365; }

		$intNtime = strtotime($loan_date);

		IF(DATE("d",$intNtime) > 22)	// 22일 초과
		{
			$intNowDay  = DATE("t",$intNtime)-DATE("d",$intNtime);
			$intNextday = DATE("t",strtotime($this->add_date($loan_date,0,1,0)));

			$intDay = ($intNowDay + $intNextday) + 1; //(오늘포함)
		} ELSE {
			$intDay  = (DATE("t",$intNtime)-DATE("d",$intNtime)) + 1; //(오늘포함)
		}

		$deposit_interest = (($recruit_amount * ($loan_interest_rate / 100) / $intDate) * $intDay);  		// 입금이자
		// 입금 이자 수식 (모집금액 * 대출이율 / 366일 * 이자 수취일)

		$invest_usefee = ($PRDT['loan_usefee'] * ($recruit_amount/ 100)) * 1.1;

		return ARRAY(
					"title"=>$title,
					"recruit_amount"=>$recruit_amount,
					"loan_interest_rate"=>$loan_interest_rate,
					"loan_date_range"=>$loan_date_range,
					"invest_usefee"=>$invest_usefee,
					"invest_bank"=>$invest_bank,
					"loan_date"=>$loan_date,
					"repay_acct_no"=>$repay_acct_no,
					"loan_dep"=>$loan_dep,
					"deposit_interest"=>$deposit_interest,
					"intday"=>$intDay,
					"loaner"=>$loaner
			   );
	}
}

Class Limit_Select
{
	public $ndate;
	public $intSeq;

	Public  function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public  function __destruct()
	{
	}

	Public FUNCTION fn_kb_limit($kind, $d_code, $mg_id, $ju_seri)
	{
		// d_code 법정동 코드
		// mg_id 물건식별자
		// ju_seri 주택형일련번호
		$strTable = "hello_apt_kb";

		SWITCH($kind)
		{
			CASE "1" :
				$strColumn = ARRAY("mg_id","dj_name");
				$strWhere = " WHERE d_code='".add_str($d_code)."' AND jong_code='01' GROUP BY mg_id";
				$strOrder =	"binary(dj_name) ASC";
				$num_per_page = 100;
			BREAK;
			CASE "2" :
				$strColumn = ARRAY("jm","jmp","ju_seri");	// 전용면적 , 전용면적 평
				$strWhere = " WHERE mg_id='".add_str($mg_id)."' GROUP BY jm, ju_seri";
				$strOrder =	"ju_seri ASC";
				$num_per_page = 100;
			BREAK;
			CASE "3" :
				$strColumn = ARRAY("mm","mm_b","mm_t");	// 매매 일반가
				$strWhere = " WHERE mg_id='".add_str($mg_id)."'  and ju_seri='".add_str($ju_seri)."'";
				$strOrder =	"idx desc";
				$num_per_page = 1;
			BREAK;
		}
		$page = 1;

		$rowList = fr_board_list($strColumn,$strTable,"",$strWhere,$strOrder,"",$num_per_page,"2000",$connect);

		return ARRAY($strColumn, $rowList);
	}

	FUNCTION fn_limit_select($ddmoney, $maxbond, $fees, $loankind, $mm, $auctionyn, $hmseq, $si, $gu)
	{
		// ddmoney 희망대출금액(예상대출금) maxbond 채권채고액   fees 플랫폼수수료  loankind 물건순위 (1,선순위, 2,후순위) mm KB시세
		// 예상대출금 = [kb 평균가 * LTV기준(69.9%, 79.9%, 83%)] - 선순위 채권최고액
		// 플랫폼수수료 = 수수료율 * 예상대출금

		IF($this->intSeq)
		{
			$strTable = "
						(
							SELECT t1.seq as seq, ltvs, ltvl, CASE WHEN ltvl='100' THEN '1' ELSE ROUND(ltvl/100,4) END as ltv, ms, ml,period FROM hloan_content_setting_history t1 JOIN hloan_content_setting_slave_history t2 ON t1.seq=t2.seq
							WHERE t1.seq='".$this->intSeq."'
						) t1";

		} ELSE {
			$strTable = "
						(
							SELECT t1.seq, ltvs, ltvl, CASE WHEN ltvl='100' THEN '1' ELSE ROUND(ltvl/100,4) END as ltv, ms, ml, period FROM
							(
								SELECT st1.seq , st2.hcsseq, st2.period FROM
								(
								SELECT MAX(seq) as seq FROM hloan_content_setting_history WHERE hmseq='".$hmseq."' AND  rec_date <= LEFT(now(),10) AND recyn='Y' AND addr_si='".add_str($si)."' AND addr_gu LIKE '%".add_str($gu)."%'
								) st1 JOIN hloan_content_setting_history st2 ON st1.seq=st2.seq
							) t1 JOIN hloan_content_setting_slave_history t2 ON t1.seq=t2.seq
						) t1";

		}

		$strColumn = ARRAY("ltv","ms","ml","ltvs","ltvl","period","seq");
		$strWhere = "";
		$strOrder =	"ltv ASC";
		$num_per_page = 100;

		$rowList = fr_board_list2($strColumn,$strTable,"",$strWhere,$strOrder,"",$num_per_page,"2000","1");

		FOR($i=0;$i<COUNT($rowList);$i++)
		{
			IF($i == 0) { $retval[] = ARRAY("","","","","",""); }
			$retval[] = $rowList[$i];
		}

		//$ddmoney = roanlmoney(replace_integer($ddmoney));	// 희망대출금액
		//$maxbond = roanlmoney(replace_integer($maxbond));	// 선순위채권최고액
		$ddmoney = replace_integer($ddmoney);	// 희망대출금액
		$maxbond = replace_integer($maxbond);	// 선순위채권최고액
		$mm		=  replace_integer($mm)*10000;			// kb시세

		FOR($i=0;$i<COUNT($retval);$i++)
		{
			UNSET($returnHellomoney);
			IF($i == 0)
			{
				$intNowEaMount  =  	ROUND(($ddmoney+$maxbond) / $mm,4); //현재LTV

				$eamount[$i]		= $ddmoney;			// 예상대출금
				IF($ddmoney > $mm)
				{
					$eamount[$i]		=	0;
					$pfees[$i]			=	1;
					$hellomoney[$i]		=	0;
					$ltv[$i]			= $this->fn_percent($intNowEaMount);

				} ELSE {
					$pfees[$i]			= ($fees/100) * $eamount[$i];		// 플랫폼수수료

					FOR($j=1;$j<<COUNT($retval);$j++)
					{
						IF($retval[$j][3] <= ($intNowEaMount*100) && $retval[$j][4] >= ($intNowEaMount*100))
						{
							$hellomoney[$i] = $retval[$j][$loankind];
							break;
						}
					}

					//$hellomoney[$i]		= $retval[$i][$loankind];		// 헬로금리
					IF($auctionyn == "Y") { $hellomoney[$i] += 1; }

					$ltv[$i]			= $this->fn_percent($intNowEaMount);
				}

			} ELSE {
				$intNowEaMount = $retval[$i][0];

				//$eamount[$i]		= ($mm * $intNowEaMount); // 예상대출금
				$eamount[$i]		= roanlmoney($mm * $intNowEaMount)- $maxbond; // 예상대출금
				$pfees[$i]			= ($fees/100) * $eamount[$i];		// 플랫폼수수료

				$hellomoney[$i]		= $retval[$i][$loankind];		// 헬로금리
				IF($auctionyn == "Y") { $hellomoney[$i] += 1; }

				$ltv[$i]			= $this->fn_percent($intNowEaMount);

			}
		}

		// 가능 한도금액 계산
		//$intLoanMoney = $eamount[COUNT($this->intEaMount)-1]- $maxbond;
		$intLoanMoney = $eamount[0];
		IF($intLoanMoney < 0)
		{
			$intLoanMoney = 0;
		}

		return ARRAY($eamount, $pfees, $hellomoney, $ltv, $retval, COUNT($rowList), $intLoanMoney);
	}

	FUNCTION fn_percent($obj)
	{
		$retval = $obj * 100;
		$retval .= "%";
		return $retval;
	}

	FUNCTION fn_maxlimit()
	{
		$retval = 1500000000;
		return $retval;
	}

	FUNCTION fn_setting_list($hmseq, $si, $gu)
	{
		$strTable = "hloan_content_setting_history";
		$strColumn = ARRAY("seq");
		$strWhere = "WHERE hmseq='".$hmseq."' AND rec_date <= LEFT(now(),10) AND recyn='Y' AND addr_si='".add_str($si)."' AND addr_gu LIKE '%".add_str($gu)."%'";
		$strOrder =	"seq DESC";
		$num_per_page = 1;

		$rowView = fr_board_view($strColumn,$strTable,"",$strWhere,$strOrder,"0","1","2000");

		return $rowView[0]["seq"];
	}
}


FUNCTION fn_file_upload_new($fname,$strFileFolder,$strImgArr,$intFileCnt)
{
	global $_FILES;
	global $_POST;
	global $gstrNdate;

	$intRand = RAND(100,980);
	FOR($i=0;$i<$intFileCnt;$i++)
	{
		$strIFilename[$i]		=	$_FILES[$fname.$i]["name"];
		$strIFilenameTmp[$i]	=	$_FILES[$fname.$i]["tmp_name"];

		$strIfileCheck[$i]		=	$_POST[$fname.$i."_check"]; // 이미지 삭제
		$strIFilenameOr[$i]		=	$_POST[$fname.$i."_or"]; // 원본파일 이미지

		$intRand = $intRand + $i;

		$strIFileNameUpload = file_upload_new($strImgArr[0],$strImgArr[1],$strImgArr[2],$strImgArr[3],$intRand,$strFileFolder,$strIFilename[$i],$strIFilenameTmp[$i],$gstrNdate,$strIFilenameOr[$i],$strIfileCheck[$i]);

		IF($i > 0)
		{
			$strIFileNameUploadVal .= "^";
		}
		$strIFileNameUploadVal .= $strIFileNameUpload;
	}

	return $strIFileNameUploadVal;
}

function file_upload_new($strFileKind,$strWidth,$strHeight,$strImgThumKind,$strNumber,$strKind,$strFileName,$strFileNameTemp,$dtmDate,$strOrFileName,$strKindDel)
{
	$SaveDir = $_SERVER["DOCUMENT_ROOT"].$strKind;

	$dtmDate1   = EXPLODE(" ",$dtmDate);
	$dtmDate1_1 = str_replace("-","",$dtmDate1[0]);
	$dtmDate1_2 = str_replace(":","",$dtmDate1[1]);

	if($strFileName)
	{
		if($strOrFileName)
		{
			 file_del($SaveDir."/".$strOrFileName);
		}
		$strName = substr(strrchr($strFileName,"."),1);
		$strNumberName = STR_REPLACE(".".$strName,"",$strFileName).$strNumber;

		$strNewName = $strNumberName.".".strtolower($strName);

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
			file_del($SaveDir."/".STR_REPLACE("+"," ",$strOrFileName));
			$strNewName = "";
		}
	}

	return $strNewName;
}

// 아파트 시세 조회
Class strAptPrice
{
	public $ndate;
	public $strLinkUrl;

	Public Function __construct()
	{
		$this->ndate = DATE("Y-m-d");
	}

	Public Function __destruct()
	{
	}

	Public Function addr_si()
	{
		$retval = ARRAY(
							"서울특별시",
							"경기도",
							"인천광역시",
							"대구광역시",
							"대전광역시",
							"광주광역시",
							"부산광역시",
							"울산광역시",
							"세종특별자치시"
					);
		return $retval;
	}

	Public Function addr_gu($strSi)
	{
		global $connect;

		$Query = "SELECT gu FROM add_code where si='".addslashes($strSi)."' AND gu <>''  GROUP BY gu ORDER BY binary(gu) ASC ";
		$Result = sql_query($Query,$connect);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	$Row["gu"];
		}

		return $retval;
	}

	Public Function addr_dong($strSi, $strGu)
	{
		global $connect;

		//$Query = "SELECT code, dong FROM add_code where si='".addslashes($strSi)."' AND gu='".addslashes($strGu)."' AND dong <>'' GROUP BY dong ORDER BY binary(dong) ASC ";
		$Query = "SELECT code, CASE WHEN ri ='' THEN dong ELSE CONCAT(dong,' ',ri) END as dong FROM add_code where si='".addslashes($strSi)."' AND gu='".addslashes($strGu)."' AND dong <>'' GROUP BY dong,ri ORDER BY binary(dong) ASC ";

		$Result = sql_query($Query,$connect);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	ARRAY($Row["code"], $Row["dong"]);
		}

		return $retval;
	}

	Public Function Apt_name($d_code)
	{
		global $connect;
		$d_code = SUBSTR($d_code,0,8)."00";

		$Query = "SELECT mg_id, dj_name FROM hello_apt_kb WHERE d_code='".addslashes($d_code)."' GROUP BY mg_id, dj_name ORDER BY  binary(dj_name) ASC ";

		$Result = sql_query($Query,$connect);

		WHILE($Row=sql_fetch_array($Result))
		{
			$retval[]	=	ARRAY($Row["mg_id"],$Row["dj_name"]);
		}
		return $retval;
	}

	Public Function Apt_area($strMgid)
	{
		global $connect;

		$Query = "SELECT ju_seri, jm, cr_date, tot_house  FROM hello_apt_kb WHERE mg_id='".addslashes($strMgid)."' GROUP BY ju_seri,jm ORDER BY jm ASC";
		$Result = sql_query($Query,$connect);


		WHILE($Row=sql_fetch_array($Result))
		{
			$ju_txt = "";
			IF($jmOr == $Row["jm"])
			{
				$ju_txt = " (탑층)";
			}
			$retval[]	=	ARRAY($Row["ju_seri"],$Row["jm"].$ju_txt,$Row["cr_date"],$Row["tot_house"]);
			$jmOr = $Row["jm"];
		}
		return $retval;
	}

	Public Function Apt_select($mg_id, $ju_seri)
	{
		$strWhere = " WHERE mg_id='".addslashes($mg_id)."' AND ju_seri='".addslashes($ju_seri)."'";

		$Query = "SELECT mm FROM hello_apt_kb ".$strWhere." ORDER BY idx ASC";

		$Result = sql_query($Query, $connect);

		IF($Row=sql_fetch_array($Result))
		{
			$RowPrice	=	$Row["mm"];
			sql_free_result($Result);
		}

		return $RowPrice;
	}

	Public Function Sale_percent($si)
	{
		SWITCH($si)
		{
			CASE "서울특별시" : $retval = 0.83; BREAK;
			CASE "경기도" : $retval = 0.80; BREAK;
			CASE "인천광역시" : $retval = 0.80; BREAK;
			DEFAULT : $retval = 0.75; BREAK;
		}
		return $retval;
	}
}

function roanlmoney($obj)
{
	$objlen = STRLEN($obj);
	$objval	= SUBSTR($obj, 0, ($objlen-6));

	$retval = $objval."000000";
	return $retval;
}


FUNCTION fn_hmseq()
{
	$strColumn = ARRAY("hmseq","cname");
	$strTable  = "hloan_member_renew";
	$strQuery  = "";
	$strWhere  = " WHERE section='2' AND recyn='Y'";
	$strorder  = " binary(cname) ASC";
	$frlimit1  = 0;
	$frlimit2  = 100;

	$rowList = fr_board_list2($strColumn,$strTable,$strQuery,$strWhere,$strorder,$frlimit1,$frlimit2,$strlen);

	return $rowList;
}
?>
