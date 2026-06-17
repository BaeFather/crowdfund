function mypageInvestList(result) {

    //var result = JSON.parse(result);

    /* 초기화 */
    var list_cnt = result.data.length;
    var html = '';
    var invest_state = '';
    var invest_state_className = '';
    var print_invest_period = '';

    /* 현재 날짜 및 시간 */
    var year = today.getFullYear();
    var month = ('0' + (today.getMonth() + 1)).slice(-2);
    var date = ('0' + today.getDate()).slice(-2);
    var hours = ('0' + today.getHours()).slice(-2);
    var minutes = ('0' + today.getMinutes()).slice(-2);
    var seconds = ('0' + today.getSeconds()).slice(-2);

    var day = today.getDay();
    var today_date = year + '-' + month + '-' + date;
    var today_time = hours + ':' + minutes  + ':' + seconds;
    var full_today = year + '-' + month + '-' + date + ' ' + hours + ':' + minutes  + ':' + seconds;

    $("#investList").empty();

    for(var j=0; j<result.data.length; j++) {
        if(result.data[j].investState == 'Y' && result.data[j].state == '1') {
            invest_state = '상환중';
            invest_state_className = 'state_1';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '2' || result.data[j].investState == 'Y' && result.data[j].state == '5') {
            invest_state = '상환완료';
            invest_state_className = 'state_0';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '8') {
            invest_state = '지연/연체';
            invest_state_className = 'state_2';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '4') {
            invest_state = '부실';
            invest_state_className = 'state_2';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '9') {
            invest_state = '매각';
            invest_state_className = 'state_2';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '' && result.data[j].investEndDate != '') {
            invest_state = '모집완료';
            invest_state_className = 'state_3';
        } else if(result.data[j].investState == 'N') {
            invest_state = '투자취소';
            invest_state_className = 'state_4';
        } else if(result.data[j].investState == 'Y' && result.data[j].state == '' && result.data[j].investEndDate == '') {
            invest_state = '모집중';
            invest_state_className = 'state_5';
        }

        if(result.data[j].investPeriod == 1 && result.data[j].investDays > 0) {
            print_invest_period = result.data[j].investDays + '일';
        } else {
            print_invest_period = result.data[j].investPeriod + '개월';
        }

        html += "<div class='col-md-6 col-xl-3'>";
        html += "<div class='card d-block'>";
        html += "<div class='card-body'>";
        if(result.data[j].investState == 'Y') {
            if(result.data[j].state == 0) {
                html += "<a onclick='goAlert();' class='myInvest_list_box'>";
            } else {
                html += "<a href='javascript:goDetailList("+ result.data[j].productIdx +");' class='myInvest_list_box'>";
            }
        html += "<input type='hidden' name='productIdx' value="+ result.data[j].productIdx +">"
        }
        html += "<h4><p class='text-title'>" + result.data[j].title + "</p></h4>";
        if(result.data[j].investState == 'Y') {
        html += "</a>";
        }
        html += "<div class='table'>";
        html += "<table class='table'>";
        html += "<tbody>";
        html += "<tr><td>연 수익률</td><td>" + result.data[j].investReturn + "% </td></tr>";
        html += "<tr><td>투자금액</td><td>" + NumberUtils.numberToKorean(result.data[j].amount, 0) + "원</td></tr>";
        html += "<tr><td>투자기간</td><td>" + print_invest_period + "</td></tr>";
        html += "<tr><td>투자상태</td><td class='"+ invest_state_className + "'>" + invest_state + "</td></tr>";

        if(result.data[j].investState == 'Y' && result.data[j].state == '' && result.data[j].investEndDate == '') {
            if(result.data[j].investEndDate=="" && result.data[j].state=="") {
                html += "<tr><td>투자취소</td><td><p onclick='myInvestCancel("+ result.data[j].idx +");'  class='invest_cancel_button'>취소하기</p></td></tr>";
            }
        } else {
            if(!result.data[j].loanEndDate) {
                result.data[j].loanEndDate = '-';
            }
            html += "<tr><td>상환예정일</td><td>" + result.data[j].loanEndDate + "</td></tr> ";
        }

        html += "</tbody></table></div></div>";
        html += "</div></div>";
    }
    $('#investList').html(html);

}

