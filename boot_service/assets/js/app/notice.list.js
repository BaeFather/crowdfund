var noticeList = {
    totalList: function(result) {
        var html = '';

        if(result.recordsTotal > 0) {
            for(var list = 0; list < result.data.length; list++) {
                html += "<div class='notice-list' onclick='goDetail(" + result.data[list].wrId + ");'>";
                html += "<p class='tit'>";

                if(result.data[list].wrAnnounce == 'Y') {
                    html += "<span class='tag'>" + result.data[list].caName + "</span>";
                }

                html += result.data[list].wrSubject + "</p>";
                html += "<p class='date'>" + result.data[list].wrLast + "</p>";
                html += "</div>";
            }
        } else {
            html += "<div class='notice-list-none' style='text-align:center;'>";
            html += "<div><img src='/assets/images/img/docu-icon.png'/></div>";
            html += "<div>검색결과가 없습니다.</div>";
            html += "</div>";
        }

       html = $('#all').html(html);
       return html;
    },

    infoUseList: function(result) {
        var html = '';

        if(result.recordsTotal > 0) {
            for(var list = 0; list < result.data.length; list++) {
                html += "<div class='notice-list' onclick='goDetail(" + result.data[list].wrId + ");'>";
                html += "<p class='tit'>";
                html += result.data[list].wrSubject + "</p>";
                html += "<p class='date'>" + result.data[list].wrLast + "</p>";
                html += "</div>";
            }
        } else {
            html += "<div class='notice-list-none' style='text-align:center;'>";
            html += "<div><img src='/assets/images/img/docu-icon.png'/></div>";
            html += "<div>검색결과가 없습니다.</div>";
            html += "</div>";
        }

        html = $('#notice-info').html(html);
        return html;
    },

    productList: function(result) {
        var html = '';

        if(result.recordsTotal > 0) {
            for(var list = 0; list < result.data.length; list++) {
                html += "<div class='notice-list' onclick='goDetail(" + result.data[list].wrId + ");'>";
                html += "<p class='tit'>";
                html += result.data[list].wrSubject + "</p>";
                html += "<p class='date'>" + result.data[list].wrLast + "</p>";
                html += "</div>";
            }
        } else {

            html += "<div class='notice-list-none' style='text-align:center;'>";
            html += "<div><img src='/assets/images/img/docu-icon.png'/></div>";
            html += "<div>검색결과가 없습니다.</div>";
            html += "</div>";
        }

        html = $('#notice-product').html(html);
        return html;
    },

    inspectionList: function(result) {
        var html = '';

        if(result.recordsTotal > 0) {
            for(var list = 0; list < result.data.length; list++) {
                html += "<div class='notice-list' onclick='goDetail(" + result.data[list].wrId + ");'>";
                html += "<p class='tit'>";
                html += result.data[list].wrSubject + "</p>";
                html += "<p class='date'>" + result.data[list].wrLast + "</p>";
                html += "</div>";
            }
        } else {
            html += "<div class='notice-list-none' style='text-align:center;'>";
            html += "<div><img src='/assets/images/img/docu-icon.png'/></div>";
            html += "<div>검색결과가 없습니다.</div>";
            html += "</div>";
        }

        html = $('#notice-inspection').html(html);
        return html;
    }


}