<?
set_time_limit(0);
ini_set('memory_limit', -1);
header("Content-Type: text/html; charset=utf-8");

function download_file($file_name, $file_micro, $file_dir, $file_type)
{
        if( !$file_name || !$file_micro || !$file_dir )
		{
			return 1;
			exit;
		}
        if( preg_match( "#\\\\|\.\.|/#", $file_micro ) )
		{
			return 2;
			exit;
		}

        if( file_exists($file_dir.$file_micro) )
        {
                $fp = fopen($file_dir.$file_micro,"r");
                if( $file_type )
                {
						header("Content-type: $file_type");
						Header("Content-Length: ".filesize($file_dir.$file_micro));
						Header("Content-Disposition: attachment; filename=$file_name");
						Header("COntent-type:file/unknown");
						Header("Content-Transfer-Encoding: binary");
						Header("Cache-Control: cache,must-revalidate");
						Header("Pragma: cache");
						header("Expires: 0");

                }
                else
                {
                        if(preg_match("#(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0)#", $HTTP_USER_AGENT))
                        {
							    Header("Content-type: application/octet-stream");
								Header("Content-Length: ".filesize($file_dir.$file_micro));
								Header("Content-Disposition: attachment; filename=$file_name");
								Header("Content-Transfer-Encoding: binary");
								Header("Cache-Control: cache,must-revalidate");
								Header("Pragma: cache");
								Header("Expires: 0");
                        }
                        else
                        {
								Header("Content-type: file/unknown");
								Header("Content-Length: ".filesize($file_dir.$file_micro));
								Header("Content-Disposition: attachment; filename=$file_name");
								Header("Content-Description: PHP3 Generated Data");
								Header("Cache-Control: cache,must-revalidate");
								Header("Pragma: cache");
								Header("Expires: 0");
                        }
                }


                ob_clean();
				flush();
				readfile($file_dir.$file_micro);
        }
        else return 1;
}

FUNCTION Error($alert)
{
	ECHO "<script type='text/javascript'>";
	ECHO "	alert('".$alert."');";
	ECHO "</script>";
}

$F				=	$_GET["F"];
$filename		=	$_GET["val"];

IF(!$F || !$filename) { Error("파일이 없습니다"); EXIT;}

IF($F) { $fileUrl = $_SERVER['DOCUMENT_ROOT'].$F."/"; }

if (!preg_match("#".$_SERVER['HTTP_HOST']."#", $_SERVER['HTTP_REFERER']))
{
//	Error("외부에서는 다운로드 받으실수 없습니다.");
}

// 다운로드 방식을 구한다.
$ext = array_pop(explode(".", $filename));
if ($ext=="avi" || $ext=="asf")         $file_type = "video/x-msvideo";
else if ($ext=="mpg" || $ext=="mpeg")   $file_type = "video/mpeg";
else if ($ext=="jpg" || $ext=="jpeg")   $file_type = "image/jpeg";
else if ($ext=="gif")                   $file_type = "image/gif";
else if ($ext=="png")                   $file_type = "image/png";
else if ($ext=="txt")                   $file_type = "text/plain";
else if ($ext=="zip")                   $file_type = "application/x-zip-compressed";
else				                    $file_type = "application/x-zip-compressed";

// 실제로 다운로드 받는다.
$ret = download_file($filename, $filename, $fileUrl, $file_type);


if( $ret == 1 )
{
	Error("지정하신 파일이 없습니다.");
	exit;
}
if( $ret == 2 )
{
	Error("접근불가능 파일입니다. 정상 접근 하시기 바랍니다.");
	exit;
}
?>
