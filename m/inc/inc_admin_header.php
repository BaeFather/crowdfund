<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>어드민</title>
<meta http-equiv="cache-control" CONTENT="no-cache">
<meta HTTP-EQUIV="Pragma"  CONTENT="no-cache">
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="/inc/calendar/jquery-ui.css" type="text/css">
<script type="text/javascript" src="/js/config.js"></script>
<script type="text/javascript" src="/js/common.js"></script>
<script type="text/javascript" src="/js/member.js"></script>
<script type="text/javascript" src="/js/ajax.js"></script>
<script type="text/javascript" src="/inc/calendar/jquery.min.js"></script>
<script type="text/javascript" src="/inc/calendar/jquery-1.8.2.js"></script>
<script type="text/javascript" src="/inc/calendar/jquery-ui.js"></script>
</head>
<script type="text/javascript">
$(function() {
	$("#startDate").datepicker({ dateFormat:'yy-mm-dd'  });
	$("#endDate").datepicker({ dateFormat:'yy-mm-dd'  });
	$(".sDate").datepicker({ dateFormat:'yy-mm-dd'  });
});
</script>
<!-- 대기중 이미지 시작 //-->
<script type="text/javascript">
<!--window.addEventListener('load', function(){setTimeout(scrollTo, 0, 0, 1);}, false);//-->
</script>
<div id="loading"><img id="loading-image" src="/images/load/loading.gif" alt="Loading..." /></div>
<script language="javascript" type="text/javascript">   
$(window).load(function() {     
 $('#loading').hide();   
}); 
</script>
<!-- 대기중 이미지 종료 //-->