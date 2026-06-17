<?
if ($g4['is_cheditor5']) 
{
    $g4['cheditor4']      = "cheditor5";
    $g4['cheditor4_path'] = $g4['path'] . "/" . $g4['cheditor4'];

    function cheditor1($id, $width='100%', $height='400')
    {
        global $g4;

        return "
        <script type='text/javascript'>
        var ed_{$id} = new cheditor('ed_{$id}');
        ed_{$id}.config.editorHeight = '{$height}';
        ed_{$id}.config.editorWidth = '{$width}';
        ed_{$id}.inputForm = 'tx_{$id}';
        </script>";
    }
}
else 
{
    function cheditor1($id, $width='100%', $height='400')
    {
        global $g4;

        return "
        <script type='text/javascript'>
        var ed_{$id} = new cheditor('ed_{$id}');
        ed_{$id}.config.editorHeight = '{$height}';
        ed_{$id}.config.editorWidth = '{$width}';
        ed_{$id}.config.imgReSize = false;
        ed_{$id}.config.fullHTMLSource = false;
        ed_{$id}.config.editorPath = '{$g4[cheditor4_path]}';
        ed_{$id}.inputForm = 'tx_{$id}';
        </script>";
    }
}

function cheditor2($id, $content='')
{
    global $g4;

    return "
    <textarea name='{$id}' id='tx_{$id}' style='display:none;'>{$content}</textarea>
    <script type='text/javascript'>
    ed_{$id}.run();
    </script>";
}
 
function cheditor3($id)
{
    return "document.getElementById('tx_{$id}').value = ed_{$id}.outputBodyHTML();";
}

echo "<script src='/cheditor5/cheditor.js'></script>";
echo cheditor1('wr_content', '100%', '400px');

echo cheditor1('wr_summary', '100%', '200px');

echo cheditor1('wcontent', '100%', '100px');

echo cheditor1('wcontent1', '100%', '150px');
echo cheditor1('wcontent2', '100%', '150px');
echo cheditor1('wcontent3', '100%', '150px');
echo cheditor1('wcontent4', '100%', '150px');
?>
