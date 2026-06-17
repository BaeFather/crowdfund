$(document).ready(function() {
    // 스크롤 시
    var rollHeader = 0;  // 값 조정

    if($('header').offset().top > 0) {
        $('header').addClass('roll');
    } else {
        $('header').removeClass('roll');
    }

	$(window).scroll(function() {
	var scroll = getCurrentScroll();
		if( scroll >= rollHeader ) {
			$('header').addClass('roll');
		}
		else {
			$('header').removeClass('roll');
		}
	});

	function getCurrentScroll() {
		return window.pageYOffset || document.documentElement.scrollTop;
	}

    // 모바일 메뉴 클릭 시
    $(".m_header_wrap .menu_bar").on("click", function(e) {
        if ( $('.menu_list_container').hasClass('on') ){
            // close
            $('.menu_list_container').removeClass('on');
            $('.dimmed').removeClass('dimmed');        
        } else {
            // open
            $('.menu_list_container').addClass('on');
            // dim
            $('.m_header_wrap').prepend('<div class="dimmed" style="display: block; opacity: .5;"></div>');
            $('.dimmed').siblings().removeClass('dimmed');
        }
    });

    // dim 클릭 시 모바일 메뉴 닫히기

    // 모바일 메뉴 depth1 클릭 시 depth2 메뉴 노출
    $('.m_header_wrap .depth1 > li').on('click', function(e) {
        if ( $(this).hasClass('active') ) {
            $(this).find(' > ul').stop().slideUp(300);
            $(this).removeClass('active');
            $(this).find('span').css('transform','rotate(0deg)');
        }
        else {
            $(this).find(' > ul').stop().slideDown(300);
            $(this).addClass('active');
            $(this).find('span').css('transform','rotate(90deg)');
        }
        e.preventDefault();
    });

    // 마우스 오버 시 depth2 보이기
    $('.header_wrap .depth1 > li').hover(function() {
        $(this).find('.depth2').css('display', 'block');
    }, function() {
        $(this).find('.depth2').css('display', 'none');
    });

});

// aside
function infoDisplay() {
    var info_target = $('.user_nick');
    var info_content = info_target.next();

    if( info_content.is(':visible') == true ) {
        info_content.fadeOut(200);
    } else {
        info_content.fadeIn(200);
    }
    
}
