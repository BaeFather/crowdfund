/* 상품 리스트(상단) - 모집대기중, 모집중, endDate = NOW() */
function productList(result) {

    var result = JSON.parse(result);

    /* 변수 정의 */
    var html = '';
    var division = '';
    var text_color = '';
    var list = $('#productList');

    /* 해당 영역 비우기 */
    list.empty();
    /* 리스트(PC) */
    if(result.length == 0){
        list.html('<p class="list-none">현재 모집중인 상품이 없습니다.</p>');
    }else{
        for(var i=0; i<result.length; i++) {

            var category = result[i].category;
            var p_division = '';
            var main_image_tag = '';
            var coverCaption = '';
            var investAbleAmountStr = '';
            var backgroundImg = '';
            var invest_info = '';
            var startDatetime = '';
            var tag = '';
            var onclickDiv = '';

            switch(category){
                case '1' : p_division += '<p class="category">동산</p>'; break;
                case '2' : result[i].mortgageGuarantees == '1' ? p_division += '<p class="category">주택담보</p>' : p_division +=  '<p class="category">부동산</p>'; break;
                case '3' :
                    if(result[i].category2 == '5'){
                        p_division += '<p class="category">온라인</p>';
                    }else if(result[i].category2 == '6'){
                        p_division += '<p class="category">정산금</p>';
                    }else{
                        p_division += '<p class="category">SCF</p>';
                    }
                    break;
            }

            var nameTagArr = result[i].nameTag.split('|');
            for(var j=0; j<nameTagArr.length; j++) {
                if(nameTagArr[j] != ''){
                    p_division += '<p class="category">' + nameTagArr[j] + '</p>';
                }
            }


            if(result[i].printInvestAbleAmount == 0){
                investAbleAmountStr = '0원';
            }else{
                investAbleAmountStr = result[i].printInvestAbleAmount + result[i].printInvestAbleAmountUnit + '원';
            }

            switch(result[i].productStatus){
                case 'standBy' : //모집 대기중
                    if(category == '1'){
                        backgroundImg += '<div class="img-box before-pf1">';
                    }else if(category == '2'){
                        backgroundImg += '<div class="img-box before-ju3">';
                    }else if(category == '3'){
                        if(result[i].category2 == '5'){
                            backgroundImg += '<div class="img-box before-online1">';
                        }else if(result[i].category2 == '6'){
                            backgroundImg += '<div class="img-box before-settlement1">';
                        }else{
                            backgroundImg += '<div class="img-box before-scf1">';
                        }
                    }

                    backgroundImg += '<div class="tag">';
                    backgroundImg += p_division;
                    backgroundImg += '</div>';
                    backgroundImg += '</div>';

                    invest_info += '<div class="before-invest-info">';
                    invest_info += '<p>';
                    invest_info += '<span>' + result[i].startDatetime + '</span> 모집 예정';
                    invest_info += '</p>';
                    invest_info += '</div>';

                break;

                case 'ing' : //모집 중
                    if(category == '1'){
                        backgroundImg += '<div class="img-box ing-pf1">';
                    }else if(category == '2'){
                        backgroundImg += '<div class="img-box ing-ju3">';
                    }else if(category == '3'){
                        if(result[i].category2 == '5'){
                            backgroundImg += '<div class="img-box ing-online1">';
                        }else if(result[i].category2 == '6'){
                            backgroundImg += '<div class="img-box ing-settlement1">';
                        }else{
                            backgroundImg += '<div class="img-box ing-scf1">';
                        }
                    }

                    backgroundImg += '<div class="tag">';
                    backgroundImg += p_division;
                    backgroundImg += '</div>';
                    backgroundImg += '<p class="date">' + result[i].startDatetime + '</p>';
                    backgroundImg += '</div>';

                    invest_info += '<div class="invest-info">';
                    invest_info += '<p>투자가능금액 : <span>' + investAbleAmountStr + '</span></p>';
                    invest_info += '<p>모집률 : <span>' + result[i].investPercent + '%</span></p>';
                    invest_info += '</div>';

                break;

                case 'end' : //모집 완료
                    if(category == '1'){
                        backgroundImg += '<div class="img-box after-pf1">';
                    }else if(category == '2'){
                        backgroundImg += '<div class="img-box after-ju3">';
                    }else if(category == '3'){
                        if(result[i].category2 == '5'){
                            backgroundImg += '<div class="img-box after-online1">';
                        }else if(result[i].category2 == '6'){
                             backgroundImg += '<div class="img-box after-settlement1">';
                        }else{
                           backgroundImg += '<div class="img-box after-scf1">';
                        }
                    }

                    backgroundImg += '<div class="tag">';
                    backgroundImg += p_division;
                    backgroundImg += '</div>';
                    backgroundImg += '<p class="date">' + result[i].startDatetime + '</p>';
                    backgroundImg += '<p class="end">모집마감</p>';
                    backgroundImg += '</div>';

                    invest_info += '<div class="invest-info">';
                    invest_info += '<p>투자가능금액 : <span>' + investAbleAmountStr + '</span></p>';
                    invest_info += '<p>모집률 : <span>' + result[i].investPercent + '%</span></p>';
                    invest_info += '</div>';

                break;
            }

            html += '<div class="box" onclick="location.href=\'detail/'+ result[i].idx +'\'">';
            html += backgroundImg;
            html += '<div class="pro-info">';
            html += '<p class="name">' + result[i].title + '</p>'; // 상품명
            html += '<div>';
            html += '<p>연<span>' + result[i].investReturn + '</span>%</p>'; // 연 수익률
            html += '<p><span>' + result[i].printInvestPeriod + '</span>' + result[i].printInvestPeriodUnit + '</p>'; // 투자 기간
            html += '<p><span>' + result[i].printRecruitAmount + '</span>' + result[i].printRecruitAmountUnit + '원</p>'; // 모집 금액
            html += '</div>';
            html += '</div>';
            html += '<div class="progress">';
            html += '<div class="progress-bar" role="progressbar" style="width: ' + result[i].investPercent + '%;" aria-valuenow="47" aria-valuemin="0" aria-valuemax="100"></div>'; // 모집률
            html += '</div>';
            html += invest_info;
            html += '</div>';
        }
        list.html(html);
    }
}

function recruitCompleteProductList(result, isStart) {

    var result = JSON.parse(result);

    /* 변수 정의 */
    var html = '';
    var division = '';
    var text_color = '';
    var list = $('#recruitCompleteProductList');
    var onclickDiv = '';

    // 지정 투자 상품
    var designate_invest_product = [148,157,171,644];

    /* 해당 영역 비우기 */
    if(isStart){
        list.empty();
    }
    /* 리스트(PC) */
    if(result.length == 0){
            list.html('<p class="list-none">검색 결과가 없습니다.</p>');
    }else{
        var length = result.length;
        if(length == 10){
            length = 9;
        }
        for(var i=0; i<length; i++) {
            onclickDiv = '';
            switch(result[i].stateCode){
                case 'A01' : onclickDiv += '<div class="box" onclick="location.href=\'detail/'+ result[i].idx +'\'">'; break;
                case 'A02' : onclickDiv += isBefore30Days(result[i]); break;
                case 'A05' : onclickDiv += isBefore30Days(result[i]); break;
                case 'A03' :
                case 'A04' :
                case 'A06' :
                case 'A07' : onclickDiv += '<div class="box" onclick="MsgBox.Alert(\'error\', \'상품모집이 취소되어 상세내용을 제공하지 않습니다.\')">'; break;
                case 'A08' :
                case 'A08S' :
                case 'A09' :
                case 'B03' : onclickDiv += '<div class="box" onclick="location.href=\'detail/'+ result[i].idx +'\'">'; break;
                case 'B04' : onclickDiv += '<div class="box" onclick="MsgBox.Alert(\'error\', \'상품모집이 취소되어 상세내용을 제공하지 않습니다.\')">'; break;
                default : onclickDiv += '<div class="box">'; break
            }

            var category = result[i].category;
            var stateCode = result[i].stateCode;
            var t_category = '';
            var t_stateCodeStr = '';

            switch(category){
                case '1' : t_category += '동산'; break;
                case '2' : result[i].mortgageGuarantees == '1' ? t_category += '주택담보' : t_category +=  '부동산'; break;
                case '3' :
                    if(result[i].category2 == '5'){
                        t_category += '온라인';
                    }else if(result[i].category2 == '6'){
                        t_category += '정산금';
                    }else{
                        t_category += 'SCF';
                    }
                    break;
                default : t_category += ''; break;
            }

            switch(stateCode){
                case 'A01' : t_stateCodeStr += '<p class="situation type-01">' + result[i].stateCodeStr + '</p>'; break;
                case 'A02' : t_stateCodeStr += '<p class="situation type-02">' + result[i].stateCodeStr + '</p>'; break;
                case 'A03' : t_stateCodeStr += '<p class="situation type-03">' + result[i].stateCodeStr + '</p>'; break;
                case 'A04' : t_stateCodeStr += '<p class="situation type-04">' + result[i].stateCodeStr + '</p>'; break;
                case 'A05' : t_stateCodeStr += '<p class="situation type-02">' + result[i].stateCodeStr + '</p>'; break;
                case 'A06' : t_stateCodeStr += '<p class="situation type-06">' + result[i].stateCodeStr + '</p>'; break;
                case 'A07' : t_stateCodeStr += '<p class="situation type-07">' + result[i].stateCodeStr + '</p>'; break;
                case 'A08' : t_stateCodeStr += '<p class="situation type-08">' + result[i].stateCodeStr + '</p>'; break;
                case 'A08S' : t_stateCodeStr += '<p class="situation type-09">' + result[i].stateCodeStr + '</p>'; break;
                case 'A09' : t_stateCodeStr += '<p class="situation type-10">' + result[i].stateCodeStr + '</p>'; break;
                case 'B00' : t_stateCodeStr += '<p class="situation type-11">' + result[i].stateCodeStr + '</p>'; break;
                case 'B01' : t_stateCodeStr += '<p class="situation type-12">' + result[i].stateCodeStr + '</p>'; break;
                case 'B02' : t_stateCodeStr += '<p class="situation type-13">' + result[i].stateCodeStr + '</p>'; break;
                case 'B03' : t_stateCodeStr += '<p class="situation type-14">' + result[i].stateCodeStr + '</p>'; break;
                case 'B04' : t_stateCodeStr += '<p class="situation type-15">' + result[i].stateCodeStr + '</p>'; break;
                default : t_stateCodeStr += ''; break;
            }

            html += onclickDiv;
            html += '<div class="tag">';
            html += '<p class="category">' + t_category;
            html += t_stateCodeStr;
            html += '</div>';
            html += '<div class="pro-info">';
            html += '<p class="name">' + result[i].title + '</p>'; // 상품명
            html += '<div>';
            html += '<p>연<span>' + result[i].investReturn + '</span>%</p>'; // 연 수익률
            html += '<p><span>' + result[i].printInvestPeriod + '</span>' + result[i].printInvestPeriodUnit + '</p>'; // 투자 기간
            html += '<p><span>' + result[i].printRecruitAmount + '</span>' + result[i].printRecruitAmountUnit + '원</p>'; // 모집 금액
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }
        if(isStart){
            list.html(html);
        }else{
            list.append(html);
        }
    }
}

function isBefore30Days(result){
    var returnStr = '';

    var investEndDate = new Date(result.investEndDate);
    var today = new Date();
    var beforeDay = ( today.getTime() - investEndDate.getTime() ) / (1000*60*60*24); // 오늘날짜 -  모집완료일
    if(beforeDay <= 30){
        returnStr += '<div class="box" onclick="location.href=\'detail/'+ result.idx +'\'">';
    }else{
        returnStr += '<div class="box" onclick="MsgBox.Alert(\'error\', \'해당 상품은 상환이 완료된 상품으로 상세내용을 제공하지 않습니다.<br>(상환완료 후 30일 이내 확인 가능)\')">';
    }

    return returnStr;
}


function randomTinyNum(min, max){
    var maxPlusOne = max + 1;
    var result = (Math.floor(Math.random() * (maxPlusOne - min)) + min);

    if(result != maxPlusOne){
        return result;
    }else{
        return max;
    }
}