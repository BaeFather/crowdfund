var StatusValue = {
    totalStatus: function(result) {
        var result = JSON.parse(result);

        if(result) {
            $('#totalLoanAmount').text(NumberUtils.numberToKorean(result.loanAmtSum)+'원');
            $('#totalRepayAmount').text(NumberUtils.numberToKorean(result.principalSum)+'원');
            $('#totalLoanRemain').text(NumberUtils.numberToKorean(result.remainAmt)+'원');
            $('#totalOverduePerc').text(result.overduePerc+'%');
            $('#totalOverdueCnt').text(result.overdueCount+'건');
        } else {
            $('#totalLoanAmount').text('-');
            $('#totalRepayAmount').text('-');
            $('#totalLoanRemain').text('-');
            $('#totalOverduePerc').text('-');
            $('#totalOverdueCnt').text('-');
        }
    },

    myselfStatus: function(result) {
        var result = JSON.parse(result);

        if(result) {
            var myselfRemain = result.myselfAmount - result.myselfPrincipal;  // 투자잔액

            $('#myselfInvestAmount').text(NumberUtils.formatComma(result.myselfAmount));
            $('#myselfInvestRemain').text(NumberUtils.formatComma(myselfRemain));
            $('#myselfOverduePerc').text('0');
            $('#myselfOverdueCnt').text('0');
        } else {
            $('#myselfInvestAmount').text('-');
            $('#myselfInvestRemain').text('-');
            $('#myselfOverduePerc').text('-');
            $('#myselfOverdueCnt').text('-');
        }
    },

    employeeStatus: function(result) {
        var result = JSON.parse(result);

        if(result.recordsTotal > 0) {
            $('#empMember').text(result.data[0].empMember);
            $('#empProfessional').text(result.data[0].empProfessional);
            $('#empSimsa').text(result.data[0].empSimsa);
        } else {
            $('#empMember').text('-');
            $('#empProfessional').text('-');
            $('#empSimsa').text('-');
        }

    },

    majorStatus: function(result) {
        var result = JSON.parse(result);

        if(result.recordsTotal > 0) {
            $('#majorShareholder').text(result.data[0].majorShareholder);
        } else {
            $('#majorShareholder').text('-');
        }

    },

    privacyStatus: function(result) {
        var result = JSON.parse(result);

        if(result.recordsTotal > 0) {
            var inspection = result.data[0].inspectionDate;
            var [view_year, view_month] = inspection.split('-');

            $('#inspectionDate').text(view_year + '년 ' + view_month + '월');
            $('#inspectionCorp').text(result.data[0].inspectionCorp);
        } else {
            //$('#inspectionDate').text('-');
            $('#inspectionDate').text($('select[name=privacySelect]').val()+ '년 점검 예정');
            $('#inspectionCorp').text('-');

        }

    },

    financeStatus: function(result) {
        var result = JSON.parse(result);

        if(result.recordsTotal > 0) {
            $('#finContents').text(result.data[0].finContents);
            $('#finUrl').html('<a href='+ result.data[0].finUrl +' target="_blank" alt='+ result.data[0].finContents +'><i class="mdi mdi-magnify-scan"></i></a>');
        } else {
            //$('#finContents').text('-');
            //$('#finUrl').html('<a href="javascript.void(0);" target="_blank" alt=""><i class="mdi mdi-magnify-scan"></i></a>');
            $('#finContents').text($('select[name=financeSelect]').val() + '년 등록 예정');
            $('#finUrl').html('');
        }
    }


}