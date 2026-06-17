var plusCount = Number(5);

$(document).ready(function(){
    $.ajax({
        url: magazineUrl,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify({
            'startNum' : 0,
            'endNum' : 6,
            'topDisplayYn' : 'Y'
        }),
        success: function(response) {
            topListSetHtml(response);
        },
        error: function(e) {
            alert('error');
        },
        complete: function(e){
            var swiper = new Swiper(".mySwiper", {
                slidesPerView: 1,  //초기값 설정 모바일값이 먼저!!
                spaceBetween: 10,
                loop: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                    type: "fraction",
                },
                breakpoints: {
                    650: {
                        slidesPerView: 2,  //브라우저가 768보다 클 때
                        spaceBetween: 20,
                    },
                    900: {
                        slidesPerView: 3,  //브라우저가 1024보다 클 때
                        spaceBetween: 10,
                    },
                },
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                },
            });
        }
    });

    $.ajax({
        url: magazineUrl,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify({
            'startNum' : $('#startNum').val(),
            'endNum' : Number($('#endNum').val()) + 1
        }),
        success: function(response) {
            bottomListSetHtml(response);
            $('#startNum').val(Number($('#startNum').val()) + plusCount);
        },
        error: function(e) {
            alert('error');
        }
    });
});

$('#moreCountBtn').on('click', function(){
    $.ajax({
        url: magazineUrl,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify({
            'startNum' : $('#startNum').val(),
            'endNum' : Number($('#endNum').val()) + 1,
            'tab' : $('#magazine_tap').val()
        }),
        success: function(response) {
            bottomListSetHtml(response);
            $('#startNum').val(Number($('#startNum').val()) + plusCount);
        },
        error: function(e) {
            alert('error');
        }
    });
});

$('.magazine_tab_btn').on('click', function(){
    var tab = $(this).data('tab');
    if(tab == 'all'){
        tab = '';
    }
    $('#magazine_tap').val(tab);

    $('#startNum').val(0);
    $('#endNum').val(5);
    $('.magazine_tab_btn').removeClass('active');
    $(this).addClass('active');
    $('#moreCountBtn').css('display', 'inline-block');

    $.ajax({
        url: magazineUrl,
        type: 'POST',
        dataType: 'json',
        contentType: 'application/json',
        traditional: true,
        data: JSON.stringify({
            'startNum' : $('#startNum').val(),
            'endNum' : Number($('#endNum').val()) + 1,
            'tab' : tab
        }),
        success: function(response) {
            $('#bottom_magazine_list').empty();
            bottomListSetHtml(response);
            $('#startNum').val(Number($('#startNum').val()) + plusCount);
        },
        error: function(e) {
            alert('error');
        }
    });
})

function topListSetHtml(response){
    var html = '';

    for(var i=0; i<response.length; i++) {
        var name = '';
        var image = '';
        var linkUrl = '';
        switch(response[i].tab){
            case 'news' : cate = '<p class="cate-news">보도자료</p>';
                    name = response[i].press;
                    image = response[i].thumbnail;
                    linkUrl = 'onclick="window.open(\''+ response[i].newsLink +'\')"';
            break;
            case 'story' : cate = '<p class="cate-video">헬로영상</p>';
                    name = response[i].subheading;
                    image = response[i].thumbnail;
                    linkUrl = 'onclick="window.open(\''+ response[i].targetLink +'\')"';
            break;
            case 'talk' : cate =  '<p class="cate-interview">직원인터뷰</p> ';
                    name = response[i].memberDepartment + ' ' + response[i].memberName;
                    image = response[i].listImage;
                    linkUrl = 'onclick="location.href=\''+ response[i].linkUrl +'\'"';
            break;
        }

        html += '<div class="swiper-slide" ' + linkUrl + '>';
        html += '<div class="img">';
        html += '<img src="' + image + '" alt="' + response[i].subject + '">';
        html += '</div>';
        html += '<div class="desc">';
        html += cate;
        html += '<p class="tit">' + response[i].subject + '</p>';
        html += '<p class="by">by. <span class="name">' + name + '</span></p>';
        html += '</div>';
        html += '</div>';
    }
    $('#top_magazine_list').append(html);
}

function bottomListSetHtml(response){
    var length = 0;
    if(response.length <= 5){
        $('#moreCountBtn').css('display', 'none');
        length = response.length;
    }else{
        length = response.length - 1;
    }

    var html = '';

    for(var i=0; i<length; i++) {
        var cate = '';
        var name = '';
        var image = '';
        var date = '';
        var linkUrl = '';

        switch(response[i].tab){
            case 'news' : cate = '<p class="cate-news">보도자료</p>';
                    name = response[i].press;
                    image = response[i].thumbnail;
                    date = response[i].regdate;
                    linkUrl = 'onclick="window.open(\''+ response[i].newsLink +'\')"';
            break;
            case 'story' : cate = '<p class="cate-video">헬로영상</p>';
                    name = response[i].subheading;
                    image = response[i].thumbnail;
                    linkUrl = 'onclick="window.open(\''+ response[i].targetLink +'\')"';
            break;
            case 'talk' : cate =  '<p class="cate-interview">직원인터뷰</p> ';
                    name = response[i].memberDepartment + ' ' + response[i].memberName;
                    image = response[i].listImage;
                    linkUrl = 'onclick="location.href=\''+ response[i].linkUrl +'\'"';
            break;
        }

        html += '<div class="box" ' + linkUrl +'>';
        html += '<div class="img">';
        html += '<img src="' + image + '" alt="' + response[i].subject + '">';
        html += '</div>';
        html += '<div class="text">';
        html += '<div class="info">';
        html += cate;
        html += '<p class="by"><span class="date">' + date + '</span> <span class="name">' + name + '</span></p>';
        html += '</div>';
        html += '<div class="desc">';
        html += '<p class="tit">' + response[i].subject + '</p>';
        html += '<p class="txt">' + response[i].contents + '</p>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
    };

    $('#bottom_magazine_list').append(html);
}
