<?php //상세 페이지?>
<?php
	$SE				=	$_POST["SE"];
	$RD				=	$_POST["RD"];
	$page			=	$_POST["page"];
	$section	=	$_POST["section"];
	$viewy		=	$_POST["viewy"];

	IF(!$section) { $section = clean_xss_tags($_GET["S"]); }
	IF(!$SE) { $SE = clean_xss_tags($_GET["SE"]); }
	IF(!$RD) { $RD = clean_xss_tags($_GET["RD"]); }

	IF(!$section) { $section = 1; }
	IF(!$page) { $page= 2; }

	IF(!$SE || !$RD)
	{
		alert('접근이 올바르지 않습니다..', G5_URL);
		exit;
	}

	$strColumn = ARRAY("id","subject","content2","content2m","content2txt");

	$strVal = new strReviewClass();
	$strView = $strVal->fn_view($section, $SE);

	IF(!$strView["val"]["id"])
	{
		alert('접근이 올바르지 않습니다..', G5_URL);
		exit;
	}
?>
<style>
#content {background: none;}
.bt{margin:50px auto 100px; width:180px; height: 40px; border: 1px solid #33a5ed; border-radius: 50px; padding-top:6px; box-sizing: border-box;text-align:center;}
.bt a {color:#33a5ed; font-size:16px;}

.contents {width:1150px;margin:0 auto;overflow:hidden;}

@media all and (max-width: 900px){
.contents {width:98%;margin:0 1%;}
.contents > img {width:100%;}
.contents > p >  img {width:100%;}
.contents > div >   img {width:100%;}
}

</style>

<!-- 본문내용 START -->
<div id="content">
	<div class="contents">
<?php
		IF(G5_IS_MOBILE == false)
		{
			ECHO $strView["val"]["content2"];
		}  ELSE {
			ECHO $strView["val"]["content2m"];
		}
?>
	</div>

<p class="bt"><a href="#none" OnClick="check_list();">+  리스트 보기</a></p>
</div>

<form name="reviewfmview" id="reviewfmview">
	<input type="hidden" name="page" value="<?php ECHO $page-1;?>">
	<input type="hidden" name="section" value="<?php ECHO $section;?>">
	<input type="hidden" name="viewy" value="<?php ECHO $viewy;?>">
	<input type="hidden" name="pkd" value="1">
</form>

<script type="text/javascript">
<!--
if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }

	function check_list()
	{
		$("#reviewfmview").attr("method","POST");
		$("#reviewfmview").attr("action","/review/index.php");
		$("#reviewfmview").submit();
	}
//-->
</script>
