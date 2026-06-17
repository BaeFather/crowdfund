<?
include_once('./_common.php');

auth_check($auth[$sub_menu], "w");

$html_title = "HYPHEN log";
$g5['title'] = $html_title;

include_once (G5_ADMIN_PATH.'/admin.head.nomenu.php');

// GET 받은 데이터를 변수화
foreach($_GET as $k=>$v) { ${$_GET[$k]} = $v; }

error_reporting(E_ALL^ E_WARNING ^ E_NOTICE); 

?>
<link href="jquery.json-viewer.css" rel="stylesheet">
<script src="jquery.json-viewer.js"></script>

<?
$sql = "SELECT * FROM hyphen_request_log WHERE idx='$idx'";
$row = sql_fetch($sql);


$row["res_body"] = strip_tags($row["res_body"]);
$row["res_body"] = str_replace(array("r\n","\n", "\r","\r\n","<br/>"), "", $row["res_body"]);


$res_body = json_decode($row["res_body"] , true);




?>

<!-- https://www.jqueryscript.net/other/jQuery-Plugin-For-Easily-Readable-JSON-Data-Viewer.html -->

<div style="width:95%; margin:20px auto;">

	<textarea id="json-input" autocomplete="off" style="display:none;"><?=$row["res_body"]?></textarea>

	<pre id="json-renderer"></pre>

</div>

<script>
$(function() {
  function renderJson() {
    try {
      var input = eval('(' + $('#json-input').val() + ')');

    }
    catch (error) {
      return alert("Cannot eval JSON: " + error);
    }
    var options = {
      collapsed: $('#collapsed').is(':checked'),
      rootCollapsable: $('#root-collapsable').is(':checked'),
      withQuotes: $('#with-quotes').is(':checked'),
      withLinks: $('#with-links').is(':checked')
    };
    $('#json-renderer').jsonViewer(input, options);
  }

  // Generate on click
  $('#btn-json-viewer').click(renderJson);

  // Generate on option change
  $('p.options input[type=checkbox]').click(renderJson);

  // Display JSON sample on page load
  renderJson();
});
</script>
