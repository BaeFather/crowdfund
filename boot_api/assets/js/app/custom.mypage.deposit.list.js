/* 입출금내역 */
function mypageDepositList(result) {

    var result = JSON.parse(result);

    /* 변수 정의 */
    var html = '';
    var mobile_html = '';
    var division = '';
    var text_color = '';
    var list = $('#depositList > tbody');
    var mobile_list = $('#depositListMobile');

    /* 해당 영역 비우기 */
    list.empty();
    mobile_list.empty();

    /* 리스트(PC) */
    for(var i=0; i<result.data.length; i++) {

        var division_value = result.data[i].poRelTable;
        var insert_date = result.data[i].poDatetime.substring(0, 10);
        var insert_time = result.data[i].poDatetime.substring(11, 16);

        if(division_value == '@deposit') { division = '입금'; }
        else if(division_value == '@withdrawal') { division = '출금'; }
        else if(division_value == '@invest') { division = '투자'; }
        else if(division_value == '@charge') { division = '지급'; }
        else if(division_value == '@discharge') { division = '차감'; }
        else if(division_value == '@cancel' || division_value == '@return') { division = '취소'; }
        else if(division_value == '@repay' || division_value == '@overdue_repay') { division = '상환'; }
        else { division = ''; }

        if(division_value == '@deposit' || division_value == '@charge' || division_value == '@cancel' || division_value == '@repay' || division_value == '@overdue_repay' || division_value == '@return') {
            text_color = 'red';
        } else if(division_value == '@withdrawal' || division_value == '@invest' || division_value == '@discharge') {
            text_color = 'blue';
        }


        html += '<tr class="d-flex">';
        html += '<td class="responsive-width-15 text-center">'+insert_date+' <span>'+insert_time+'</span></td>';
        html += '<td class="responsive-width-10 text-left ' +text_color+ '">'+division+'</td>';
        html += '<td class="responsive-width-35 text-left">'+result.data[i].poContent+'</td>';
        html += '<td class="responsive-width-20 text-right ' +text_color+ '">'+numberWithCommas(result.data[i].poPoint)+'원</td>';
        html += '<td class="responsive-width-20 text-right">'+numberWithCommas(result.data[i].poMbPoint)+'원</td>';
        html += '</tr>';
    }

    list.html(html);


    /* 리스트(Mobile) */
    for(var j=0; j<result.data.length; j++) {
        var division_value = result.data[j].poRelTable;
        var insert_date = result.data[j].poDatetime.substring(0, 10);

        if(division_value == '@deposit') { division = '입금'; }
        else if(division_value == '@withdrawal') { division = '출금'; }
        else if(division_value == '@invest') { division = '투자'; }
        else if(division_value == '@charge') { division = '지급'; }
        else if(division_value == '@discharge') { division = '차감'; }
        else if(division_value == '@cancel' || division_value == '@return') { division = '취소'; }
        else if(division_value == '@repay' || division_value == '@overdue_repay') { division = '상환'; }
        else { division = ''; }

        if(division_value == '@deposit' || division_value == '@charge' || division_value == '@cancel' || division_value == '@repay' || division_value == '@overdue_repay' || division_value == '@return') {
            text_color = 'red';
        } else if(division_value == '@withdrawal' || division_value == '@invest' || division_value == '@discharge') {
            text_color = 'blue';
        }

        mobile_html += '<div class="deposit-withdrawal-list">';
        mobile_html += '<ul>';
        mobile_html += '<li>';
        mobile_html += '<p class="date">'+result.data[j].poDatetime+'</p>';
        mobile_html += '<p class="tit ' +text_color+ '">'+division+'</p>';
        mobile_html += '</li><li>';
        mobile_html += '<p class="money ' +text_color+ '">'+numberWithCommas(result.data[j].poPoint)+'원</p>';
        mobile_html += '<p class="balance">잔액 '+numberWithCommas(result.data[j].poMbPoint)+'원</p>';
        mobile_html += '</li>';
        mobile_html += '</ul>';
        mobile_html += '<p class="txt">'+result.data[j].poContent+'</p>';
        mobile_html += '</div>';
    }
    mobile_html += '<div id="moreDivMobile" class="list-add-bt"><button type="button" class="btn btn-secondary" onclick="moreAction();">더보기</button></div>';

    mobile_list.html(mobile_html);

}



