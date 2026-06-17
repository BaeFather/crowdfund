<?
include_once('./_common.php');

$g5['title'] = '회원가입';
$g5['top_bn'] = "/images/member/sub_id.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

include_once(HF_PATH.'/hf_head.php');
?>

<? 
if(G5_IS_MOBILE) { 
	add_stylesheet('<link rel="stylesheet" href="/css/find_id_m.css">', 0);
} else { 
	add_stylesheet('<link rel="stylesheet" href="/css/find_id.css?11">', 0);
} 
?>

<!-- 본문내용 START -->
<? if(G5_IS_MOBILE && false){ ?>
	<!--<img src="<? echo G5_THEME_URL;?>/img2/member/sub_id.jpg" alt="아이디 찾기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." /> //-->
<? } ?>

    <div id="content">
        <? if(false) { ?><!-- <div class="location"><span></span><b class="blue">아이디 찾기</b></div> //--> <? } ?>
        <div class="content">

            <form name="frm" id="frm" method="post">
                <fieldset id="find_id_form">
                    <legend>안녕하세요. 헬로펀딩입니다.</legend>
                    <span class="title"><strong>아이디</strong> 찾기</span>
                    <div class="clearfix"></div>
                    <br/>
                    <br/>
                    <label for="mb_name">이름</label>
                    <input type="text" name="mb_name" id="mb_name" required="required" class="mb_name required" placeholder="이름을 입력해주세요."/><br/>
                    <label for="mb_hp">휴대폰 번호</label>
                    <input type="text" name="mb_hp" id="mb_hp" required="required" class="mb_hp required" placeholder="휴대폰 번호를 입력해주세요."/><br/>

                    <div class="btn_group">
                        <button type="button" name="submit" id="check" class="btn_big_blue">아이디 찾기</button>
                    </div>
                </fieldset>
            </form>

        </div>
    </div>

    <script type="text/javascript">
        function input_check(msg){
            if(msg.replace(/(^\s*)|(\s*$)/g,"") == ''){ return false; }else{ return true; }
        }

        $(document).ready(function(){
            // 인풋박스 텍스트 페이드
            $('.id-label').animate({ opacity: "1" })
                .click(function() {
                    var thisFor	= $(this).attr('for');
                    $('.'+thisFor).focus();
            });

            $('.p-label').animate({ opacity: "1" })
                .click(function() {
                    var thisFor	= $(this).attr('for');
                    $('.'+thisFor).focus();
            });

            // 아이디
            $('.mb-id').focus(function() {

                $('.id-label').animate({ opacity: "0" }, "fast");

                    if($(this).val() == "mb-id")
                        $(this).val() == "";

                }).blur(function() {

                    if($(this).val() == "") {
                        $(this).val() == "mb-id";
                        $('.id-label').animate({ opacity: "1" }, "fast");
                    }
            });
            // 핸드폰번호
            $('.mb-phone').focus(function() {

                $('.p-label').animate({ opacity: "0" }, "fast");

                    if($(this).val() == "mb-phone")
                        $(this).val() == "";

                }).blur(function() {

                    if($(this).val() == "") {
                        $(this).val() == "mb-phone";
                        $('.p-label').animate({ opacity: "1" }, "fast");
                    }
            });

            $('#check').click(function() {

                var mb_name  = $("input[name='mb_name']").val();
                var mb_hp		= $("input[name='mb_hp']").val();

                if(!input_check(mb_name)){ alert('이름을 입력해 주세요');  $("input[name='mb_name']").focus(); return false; }
                if(!input_check(mb_hp)){ alert('핸드폰번호를 입력해 주세요');  $("input[name='mb_name']").focus(); return false; }

                var ajax_data = $("#frm").serialize();
                $.ajax({
                    url : "./find_id_proc.php",
                    type: "POST",
                    data : ajax_data,
                    success: function(data, textStatus, jqXHR){
                        if(data=="SUCCESS"){
                        //	alert("회원 아이디가 가입된 핸드폰 번호로 전송 되었습니다.");
                        //	return;

                            $.blockUI({
                                message: $('#complete'),
                                <? if(G5_IS_MOBILE) { ?>
                                css: { top:'5%',left:'1%',width:'98%', border:0, cursor:'default' }
                                <? } else { ?>
                                css: { border:0, cursor:'default' }
                                <? } ?>
                            });

                        }
                        else if(data=="ERROR-NO-DATA"){
                            alert("일치하는 정보가 없습니다.");
                            return;
                        }
                        else{
                            alert("시스템 에러입니다. 관리자에 문의해주세요.");
                            return;
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown)	{

                    }
                });

                /*
                $.post(url,{'mb_name':nm, 'mb_hp':hp},

                    function(result) {

                        if(result == 1){



                        }else if(result == 2){

                            alert('비회원이거나 입력하신 정보가 올바르지 않습니다\n\n비회원일 경우 회원가입을 해주시고\n\n기존 회원이시라면 정확한 정보로 다시 입력해 주세요');

                        }

                    }

                );
                $.blockUI({
                    message: $('#complete'),
                    css: { border:0, cursor:'default' } });

                */

                /*
                $.ajax({
                    url: 'wait.php',
                    cache: false,
                    complete: function() {
                        // unblock when remote call returns
                        $.unblockUI();
                    }
                });
                */

            });

            $('#complete #yes, #complete .close').click(function() {
                $.unblockUI();
                return false;
            });

        });
    </script>

<div id="complete">
	<img src="/images/btn_close.gif" alt="close" class="close" />
	<div class="title">아이디 전송 완료</div>
	<div class="text">고객님의 핸드폰으로<br/><span class="blue">아이디가 전송 되었습니다</span></div>
	<span id="yes" class="btn_big_blue">확인</span>
</div>

<!-- 본문내용 E N D -->
<?
include_once(HF_PATH.'/tail.php');
?>