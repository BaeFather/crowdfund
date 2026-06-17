<?
include_once('./_common.php');

$g5['title'] = '비밀번호 찾기';
$g5['top_bn'] = "/images/member/sub_pw.jpg";
$g5['top_bn_alt'] = "회원가입 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다.";

include_once(HF_PATH.'/hf_head.php');

add_stylesheet('<link rel="stylesheet" href="/css/style.css">', 0);
?>

<? if(G5_IS_MOBILE && false){ ?>
	<!--<img src="<?=G5_THEME_URL?>/img2/member/sub_pw.jpg" alt="비밀번호 찾기 투자자가 작은 금액들을 모아서 함께 투자하는 새로운 투자 방식입니다." /> //-->
<? } ?>

    <div id="content">
        <? if(false) { ?><!-- <div class="location"><span></span><b class="blue">비밀번호 찾기</b></div> //--><? } ?>
        <div class="content">

            <form name="frm" id="frm" method="post">
                <fieldset id="find_pw_form">
                    <legend>안녕하세요. 헬로펀딩입니다.</legend>
                    <span class="title"><strong>비밀번호</strong> 찾기</span>
                    <div class="clearfix"></div>
                    <br/>
                    <br/>
                    <label for="mb_id">아이디</label>
                    <input type="text" name="mb_id" id="mb_id" required="required" class="mb_id required" placeholder="아이디를 입력해주세요."/><br/>
                    <label for="mb_name">이름</label>
                    <input type="text" name="mb_name" id="mb_name" required="required" class="mb_name required" placeholder="이름을 입력해주세요."/><br/>
                    <label for="mb_hp">휴대폰 번호</label>
                    <input type="text" name="mb_hp" id="mb_hp" required="required" class="mb_hp required" placeholder="휴대폰 번호를 입력해주세요."/><br/>

                    <div class="btn_group">
                        <button type="button" name="submit" id="check" class="btn_big_blue">비밀번호 찾기</button>
                    </div>
                </fieldset>
            </form>

        <? if(false) { ?>
            <!-- 비밀번호 찾기-->
            <!--
            <div class="findInfo">
                <img src="<? echo G5_IMAGES_URL;?>/member/icon_find.gif" alt="헬로펀딩 계정으로 가입하기" />
                <div class="title"><span class="blue">비밀번호</span>찾기</div>
                헬로펀딩 비밀번호를 찾아보세요.
                <form name="frm" id="frm" method="post">
                    <div class="inputArea">
                        <div class="id"><label class="id-label" for="mb-id">아이디를 입력하세요</label><input type="text" class="mb-id" id="mb_id" name="mb_id" /></div>
                        <div class="id"><label class="name-label" for="mb_name">이름을 입력하세요</label><input type="text" class="mb-name" id="mb_name"  name="mb_name"/></div>
                        <div class="phone"><label class="p-label" for="mb-phone">핸드폰 번호를 입력하세요</label><input type="text" class="mb-phone" id="mb_hp" name="mb_hp" /></div>
                    </div>
                    <div class="btnArea">
                        <a href="javascript:;" class="btn_big_blue" id="check" style="<? echo (G5_IS_MOBILE) ? 'width:100%' : ''; ?>">확인</a>
                    </div>
                </form>
            </div>
            //-->
            <!-- 비밀번호 찾기 -->
        <? } ?>


        </div>
    </div>

    <div id="complete">
        <img src="/images/btn_close.gif" alt="close" class="close" />
        <div class="title">임시비밀번호 발급완료</div>
        <div class="text">고객님의 핸드폰으로<br><span class="blue">임시비밀번호가 발급되었습니다.</span></div>
        <span id="yes" class="btn_big_blue">확인</span>
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

        // 이름
        $('.mb-name').focus(function() {

            $('.name-label').animate({ opacity: "0" }, "fast");

                if($(this).val() == "mb-name")
                    $(this).val() == "";

            }).blur(function() {

                if($(this).val() == "") {
                    $(this).val() == "mb-name";
                    $('.name-label').animate({ opacity: "1" }, "fast");
                }
        });

        //핸드폰번호
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

            var mb_id   = $("input[name='mb_id']").val();
            var mb_name = $("input[name='mb_name']").val();
            var mb_hp		= $("input[name='mb_hp']").val();

            if(!input_check(mb_id)){ alert('아이디를 입력해 주세요'); $("input[name='mb_id']").focus(); return false; }
            if(!input_check(mb_name)){ alert('이름을 입력해 주세요'); $("input[name='mb_name']").focus(); return false; }
            if(!input_check(mb_hp)){ alert('핸드폰번호를 입력해 주세요');  $("input[name='mb_hp']").focus(); return false; }

            var ajax_data = $("#frm").serialize();
            $.ajax({
                url : "./find_pw_proc.php",
                type: "POST",
                data : ajax_data,
                success: function(data, textStatus, jqXHR){
                    $('#ajax_return_txt').val(data);
                    if(data=="SUCCESS"){
                        $.blockUI({
                        message: $('#complete'),
                        css: { border:0, cursor:'default' } });
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

        });

        $('#complete #yes, #complete .close').click(function() {
            $.unblockUI();
            return false;
        });

    });
    </script>

<!-- 본문내용 E N D -->
<?
include_once(HF_PATH.'/tail.php');
?>