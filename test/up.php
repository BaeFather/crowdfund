<form name="fm" method="post" enctype="multipart/form-data">
<input type="FILE" name="fl"><br/>
<input type="submit" value="확인">
</form>

<?
die();
if (is_array($_FILES)) {

	echo "<pre>";
	print_r($_FILES);
	echo "</pre>";

	if (move_uploaded_file($_FILES['fl']['tmp_name'], "/home/crowdfund/public_html/adm/helloloan/afile/kkkkk")) {
		echo "/home/crowdfund/public_html/adm/helloloan/afile/kkkkk";
	} else {
		echo "No";
	}

}

?>