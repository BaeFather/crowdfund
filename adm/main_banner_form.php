<?php
$sub_menu = '100600';
include_once('./_common.php');
include_once(G5_EDITOR_LIB);

auth_check($auth[$sub_menu], "w");

$html_title = "메인배너";
$g5['title'] = $html_title.' 설정';

// 메인 배너이미지 정보
$mb_sql = " select * from g5_main_banner where idx = '1' ";
$mb_r = sql_fetch($mb_sql);

$mb_img1 = $mb_r['bn_img1'];
$mb_img2 = $mb_r['bn_img2'];
$mb_img3 = $mb_r['bn_img3'];


include_once (G5_ADMIN_PATH.'/admin.head.php');

$mb_dir = str_replace('','', G5_DATA_PATH."/main_banner/");


?>

<form name="frmcontentform" action="./main_banner_update.php" method="post" enctype="MULTIPART/FORM-DATA" >

	<input type="hidden" name="bn_img1_pre" value="<?php echo $mb_img1;?>" />
	<input type="hidden" name="bn_img2_pre" value="<?php echo $mb_img2;?>" />
	<input type="hidden" name="bn_img3_pre" value="<?php echo $mb_img3;?>" />

<div class="tbl_frm01 tbl_wrap">
    <table>
    <caption><?php echo $g5['title']; ?></caption>
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
	<tr>
		<td colspan="2">이미지를 교체하시려면 파일선택을 하시고 <input type="button" value="확인" class="btn_submit">을 클릭하세요</td>
	</tr>
    <tr>
        <th scope="row"><label for="bn_img1">첫번째 배너이미지</label></th>
        <td>
            <input type="file" name="bn_img1" id="bn_img1">
			(업로드 이미지 사이즈: 978px X 685px)
            <?php
            $bimg1 = $mb_dir.$mb_img1;
			if (file_exists($bimg1)) {
                $size = @getimagesize($bimg1);
                if($size[0] && $size[0] > 300)
                    $width = 300;
                else
                    $width = $size[0];

                $bimg1_str = '<img src="'.G5_DATA_URL.'/main_banner/'.$mb_img1.'" width="'.$width.'" alt="">';
            }
            if ($bimg1_str) {
                echo '<div class="banner_or_img">';
                echo $bimg1_str;
                echo '</div>';
            }
            ?>
        </td>
    </tr>
    <tr>
        <th scope="row"><label for="bn_img2">두번째 배너이미지</label></th>
        <td>
            <input type="file" name="bn_img2" id="bn_img2">
			(업로드 이미지 사이즈: 978px X 685px)
            <?php
            $bimg2 = $mb_dir.$mb_img2;
			if (file_exists($bimg2)) {
                $size = @getimagesize($bimg2);
                if($size[0] && $size[0] > 300)
                    $width = 300;
                else
                    $width = $size[0];

                $bimg2_str = '<img src="'.G5_DATA_URL.'/main_banner/'.$mb_img2.'" width="'.$width.'" alt="">';
            }
            if ($bimg2_str) {
                echo '<div class="banner_or_img">';
                echo $bimg2_str;
                echo '</div>';
            }
            ?>
        </td>
    </tr>
	<tr>
        <th scope="row"><label for="bn_img3">세번째 배너이미지</label></th>
        <td>
            <input type="file" name="bn_img3" id="bn_img3">
			(업로드 이미지 사이즈: 978px X 685px)
            <?php
            $bimg3 = $mb_dir.$mb_img3;
			if (file_exists($bimg3)) {
                $size = @getimagesize($bimg3);
                if($size[0] && $size[0] > 300)
                    $width = 300;
                else
                    $width = $size[0];

                $bimg3_str = '<img src="'.G5_DATA_URL.'/main_banner/'.$mb_img3.'" width="'.$width.'" alt="">';
            }
            if ($bimg3_str) {
                echo '<div class="banner_or_img">';
                echo $bimg3_str;
                echo '</div>';
            }
            ?>
        </td>
    </tr>
    </tbody>
    </table>
</div>

<div class="btn_confirm01 btn_confirm">
    <input type="submit" value="확인" class="btn_submit" accesskey="s">
</div>

</form>

<?php
include_once (G5_ADMIN_PATH.'/admin.tail.php');
?>
