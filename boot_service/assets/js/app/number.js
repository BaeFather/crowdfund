var NumberUtils = {
    formatComma: function(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    },

    minimumFormatComma: function(value, minimum) {
        if(value >= minimum){
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }else{
            return 0;
        }
    },

    formatRangeAmountString: function(value, fixed) {
        var amt = 0;
        if(value >= 100000000) {
            amt = value / 100000000;
            return amt.toFixed(fixed).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '억원';
        }
        else if (value >= 10000) {
            amt = value / 10000;
            return amt.toFixed(fixed) + '만원';
        }
        else {
            if(value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') == ''){
                return '0원';
            }
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + '원';
        }
    },

    formatDetailAmountString: function(value, fixed) {
        var amt = 0;
        if(value >= 100000000) {
            amt = value / 100000000;
            return amt.toFixed(fixed).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' 억원';
        }
        else if (value >= 10000000) {
            amt = value / 10000000;
            return amt.toFixed(fixed) + ' 천만원';
        }
        else if (value >= 1000000) {
            amt = value / 1000000;
            return amt.toFixed(fixed) + ' 백만원';
        }
        else if (value >= 100000) {
            amt = value / 100000;
            return amt.toFixed(fixed) + ' 십만원';
        }
        else if (value >= 10000) {
            amt = value / 10000;
            return amt.toFixed(fixed) + ' 만원';
        }
        else {
            return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') + ' 원';
        }
    },

    numberToKorean: function(value, fixed) {
        var inputNumber  = value < 0 ? false : value;
        var unitWords    = ['', '만', '억', '조', '경'];
        var splitUnit    = 10000;
        var splitCount   = unitWords.length;
        var resultArray  = [];
        var resultString = '';

        for (var i = 0; i < splitCount; i++){
            var unitResult = (inputNumber % Math.pow(splitUnit, i + 1)) / Math.pow(splitUnit, i);
            unitResult = Math.floor(unitResult);
            if (unitResult > 0){
                resultArray[i] = unitResult;
            }
        }
        for (var i = 0; i < resultArray.length; i++){
            if(!resultArray[i]) continue;
            resultString = " " + String(numberWithCommas(resultArray[i])) + unitWords[i] + resultString;
        }

        return resultString;
    },

    numberToKoreanNotBlank: function(value, fixed) {
            var inputNumber  = value < 0 ? false : value;
            var unitWords    = ['', '만', '억', '조', '경'];
            var splitUnit    = 10000;
            var splitCount   = unitWords.length;
            var resultArray  = [];
            var resultString = '';

            for (var i = 0; i < splitCount; i++){
                var unitResult = (inputNumber % Math.pow(splitUnit, i + 1)) / Math.pow(splitUnit, i);
                unitResult = Math.floor(unitResult);
                if (unitResult > 0){
                    resultArray[i] = unitResult;
                }
            }
            for (var i = 0; i < resultArray.length; i++){
                if(!resultArray[i]) continue;
                resultString = String(numberWithCommas(resultArray[i])) + unitWords[i] + resultString;
            }

            return resultString;
        }
};