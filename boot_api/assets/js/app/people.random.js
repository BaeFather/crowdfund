function peopleArray() {
    var peopleArray = new Array(
        Array("0","","",""),
        Array("1", "남기중", "01.jpg", "팀원들의 행복과 재미를 챙기는 것이, 헬로펀딩을 정직하게 성장시키는 최선의 방법임을 잊지 말자."),
        Array("2", "최수석", "02.jpg", "투자자의 안전! 영원히 변치않는 헬로펀딩의 미션입니다."),
        Array("3", "김인", "05.jpg", "펀딩 상품 하나하나가 제 얼굴입니다."),
        Array("4", "배재수", "09.jpg", "투자자의 행복은 안전한 시스템 위에 지어져야 무너지지 않습니다!"),
        Array("5", "전승찬", "10.jpg", "정확한 자료를 신속하고 안전하게 제공하겠습니다."),
        Array("6", "류재영", "12.jpg", "어떠한 상황에서도 생각, 말, 행동, 마음을 거짓 없이 바르게 표현하여, 고객을 가족으로 만들겠습니다."),
        Array("7", "이기륜", "15.jpg", "선승구전의 자세로 새로운 금융 플랫폼을 만들겠습니다."),
        Array("8", "이상규", "16.jpg", "투자자를 위한 서비스를 제공하겠습니다."),
        Array("10", "김동일", "27.jpg", "우리 모두가 행복해지기 위한 투자를 하겠습니다."),
        Array("11", "김영신", "37.jpg", "고객 분들의 투자가 최고의 투자가 될 수 있도록 노력하겠습니다."),
        Array("12", "최선희", "38.jpg", "헬로펀딩만의 장점과 매력을 바르고 정직하게 전달하고 고객의 소리에 귀 기울이는 소통을 하겠습니다."),
        Array("13", "김정은", "39.jpg", "후회하지 않을 만큼 최선을 다하겠습니다."),
        Array("14", "이철규", "41.jpg", "투자자의 입장에서 생각하며 행동하겠습니다."),
        Array("15", "김단비", "42.jpg", "초심 잃지않는 헬로의 정직과 신뢰의 일원이 되겠습니다."),
        Array("16", "김미선", "45.jpg", "언제나 즐거운 디자인을 보여드리겠습니다."),
        Array("19", "이지혜", "56.jpg", "책임감을 가지고 최선을 다하겠습니다."),
        Array("20", "서동환", "57.jpg", "누구나 신뢰 할 수 있는 최고의 금융 회사로 만들어 가겠습니다."),
        Array("21", "박건호", "58.jpg", "유지경성[有志竟成]"),
        Array("23", "윤선미", "61.jpg", "쾌적한 투자환경은 편한 서비스에서 비롯됩니다."),
        Array("25", "박기범", "63.jpg", "쾌적하고 안전한 투자를 위해 노력하겠습니다."),
        Array("26", "정성호", "64.jpg", "敬天尊地愛人"),
        Array("27", "김수빈", "65.jpg", "처음 입사 할 때의 마음 그대로 늘 발전하는 직원이 될 수 있도록 노력하겠습니다."),
    );



    var html = "";
    var peopleDiv = $('.member-wrap');

    const peoples = Array(peopleArray.length - 1).fill().map((item, index) => index + 1);
    const peopleRandomArr = [];

    while(peoples.length > 0) {
        const randomMath = Math.floor(Math.random() * peoples.length);
        const newArr = peoples.splice(randomMath, 1);
        const peopleValue = newArr[0];
        peopleRandomArr.push(peopleValue);

        var random = peopleRandomArr.pop(peopleValue);

        peopleDiv.append(
            "<div>" +
            "<img src='../../assets/images/company/profile/"+peopleArray[random][2]+"' alt='"+peopleArray[random][1]+"' />" +
            "<div class='people_text'><span>"+peopleArray[random][1]+"</span><br />"+peopleArray[random][3]+"</div>" +
            "</div>"
        );
    }
}

