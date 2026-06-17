<?
/*
2017-04-24 : 개인회원 상품별 금액 제한 관련 내용 추가
*/
include_once('./_common.php');

while( list($k, $v) = each($_REQUEST) ) { if(!is_array($k) ) ${$k} = addslashes(clean_xss_tags(trim($v))); }

$g5['title'] = '회원가입';
$g5['top_bn'] = "/images/member/sub_join.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

include_once(HF_PATH.'/hf_head.php');


while(list($k, $v) = each($_REQUEST)) { ${$k} = @trim($v); }

if (!$tab) $tab="p";
if(!in_array($tab, array('p','c'))) { header('Location: /', true, 302); exit; }

$tab = ($tab) ? $tab : 'p';



$ID_LIMIT = array(
              'easy'=>array('min_length'=>6, 'max_length'=>15, 'str_type'=>'', 'describe'=>'영문 또는 영문과 숫자의 조합, 6-15자까지 등록 가능합니다.'),
              'hard'=>array('min_length'=>8, 'max_length'=>15, 'str_type'=>'alpha_num', 'describe'=>'영문 또는 영문과 숫자의 조합, 8-15자까지 등록 가능합니다.')
            );
$PW_LIMIT = array(
              'easy'=>array('min_length'=>4, 'max_length'=>15, 'str_type'=>'alpha_num', 'describe'=>'영문, 숫자 조합, 4-15자까지 등록 가능합니다.'),
              'hard'=>array('min_length'=>8, 'max_length'=>15, 'str_type'=>'alpha_num_special', 'describe'=>'영문, 숫자, 특수문자의 조합, 8-15자까지 등록 가능합니다.')
            );

$idpw_type = (preg_match('/mirror\.hello/i', $_SERVER['HTTP_HOST'])) ? 'hard' : 'easy';
//$idpw_type = "easy";

?>

<? if(G5_IS_MOBILE) { ?>
<link rel="stylesheet" href="/member/join_style.css">
<? } else { ?>
<link rel="stylesheet" href="<?=HF_CSS_URL?>/member.css">
<? } ?>


<div id="content">
    <? if(false) { ?>
        <!--
        <div class="location">
            <span></span>
            <b class="blue"><? echo $g5['title'];?></b>
        </div>
        //-->
    <? } ?>

	<div class="content">
        <div class="register_form">
            <span class="title">헬로펀딩 <strong>회원가입</strong></span>
            <div class="clearfix"></div>
            <br/>
            <div class="register_tabs">
                <ul>
                    <li <? echo (empty($tab) || $tab=='p') ? 'class="on"' : '';?> onClick="location.href='?tab=p';"><a href="#">개인 회원가입</a></li>
                    <li <? echo ($tab=='c') ? 'class="on"' : '';?> onClick="location.href='?tab=c';"><a href="#">법인 회원가입</a></li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <? require_once "join_info_form_{$tab}.php"; ?>
        </div>
    </div>
</div>



<!-- 본문내용 E N D -->
<?
include_once(HF_PATH.'/_tail.php');
?>