"use strict";
var Pagination = /** @class */ (function () {
    function Pagination(el, length) {
        var _this = this;
        var _page;
        var _start = 0;
        var _length = length;
        var _currentPage = 1;
        var _totalPage = 5;
        var _maxNumberOfPage = 5;

        this.onPageSelect = function (startNumber) {
            console.log(pageNumber);
        };

        this.getInitPage = function () {
            return {
                start: 0,
                length: _length
            };
        };

        // Generate Pagination
        this.getPageFromAjax = function (startNumber) {
            return {
                start: startNumber,
                length: _page.length,
                recordsTotal: _page.recordsTotal,
            };
        };

        // Generate Pagination
        this.draw = function (page) {
            if (page === void 0) { page = { start: 1, pageTotal: 0, recordsTotal: 0, length: 5}; }

            // Get Parent Div Element
            var parentPage = $(_this.el);
            // check if there is a total number of page to be show
            if (page.recordsTotal == 0) {
                parentPage.html('');
                return;
            }

            _page = page;
            _start = page.start + 1;
            _length = page.length;
            _currentPage = Math.ceil(_start/_length);
            _totalPage = Math.floor((page.recordsTotal)/_length) == 0 ? 1 : Math.floor((page.recordsTotal)/_length) + 1;

            var startPage = (Math.ceil(_currentPage/_maxNumberOfPage) * _maxNumberOfPage) - (_maxNumberOfPage-1);

            var endPage = (startPage + _maxNumberOfPage - 1) < _totalPage ? (startPage + _maxNumberOfPage - 1) : _totalPage;

            var hasStartPage = (startPage == 1) ? true : false;

            var hasLastPage = (endPage == _totalPage) ? true : false;

            var hasNextPage = (_currentPage < _totalPage) ? true : false;

            var nextPageNumber = hasNextPage ? (_currentPage + 1) : _totalPage;

            var hasPrevPage = (_currentPage > 1) ? true : false;

            var prevPageNumber = hasPrevPage ? (_currentPage - 1) : 1;

            // Generate redirect First Page and Next Page set
            var ulStr = "<ul class=\"pagination pagination-rounded mb-0\">\n";

            var aStr="";
            var pageFromAjax ="";
            /*
             * start page html
             */
            if(hasStartPage) {
                aStr = "<a class=\"page-link link_default\" href=\"javascript: void(0);\">&laquo;</a>";
            }
            else {
                var startNumber = 0;
                aStr = "<a class=\"page-link\" href=\"javascript: pagination.onPageSelect(" + startNumber + ");\">&laquo;</a>";
            }
            ulStr += "<li class=\"page-item\">" + aStr +"</li>\n";

            /*
             * prev page html
             */
            if(hasPrevPage) {
                var startNumber = (prevPageNumber - 1) * _length;
                aStr = "<a class=\"page-link\" href=\"javascript: pagination.onPageSelect(" + startNumber  + ");\">&lsaquo;</a>";
            }
            else {
                aStr = "<a class=\"page-link link_default\" href=\"javascript: void(0);\">&lsaquo;</a>";
            }
            ulStr += "<li class=\"page-item\">" + aStr +"</li>\n";

            /*
             * page html
             */
            for (var i = startPage; i <= endPage; i++) {
                if(_currentPage == i ) {
                    aStr = "<a class=\"page-link\" href=\"javascript: void(0);\">" + i + "</a>";
                    ulStr += "<li class=\"page-item active\" >" + aStr + "</li>";
                }
                else {
                    var startNumber = (i-1) * _length;
                    aStr = "<a class=\"page-link\" href=\"javascript: pagination.onPageSelect(" + startNumber + ");\">" + i + "</a>";
                    ulStr += "<li class=\"page-item\">" + aStr +"</li>\n";
                }
            }

            /*
             * next page html
             */
            if(hasNextPage) {
                var startNumber = (nextPageNumber - 1) * _length;
                aStr = "<a class=\"page-link\" href=\"javascript: pagination.onPageSelect(" + startNumber + ");\">&rsaquo;</a>";
            }
            else {
                aStr = "<a class=\"page-link link_default\" href=\"javascript: void(0);\">&rsaquo;</a>";
            }

            ulStr += "<li class=\"page-item\">" + aStr +"</li>\n";

            /*
             * last page html
             */
            if(hasLastPage) {
                aStr = "<a class=\"page-link link_default\" href=\"javascript: void(0);\">&raquo;</a>";
            }
            else {
                var startNumber =(_totalPage -1) * _length;
                aStr = "<a class=\"page-link\" href=\"javascript: pagination.onPageSelect(" + startNumber + ");\">&raquo;</a>";
            }
            ulStr += "<li class=\"page-item\">" + aStr +"</li>\n";

            ulStr += "</ul>";

            parentPage.html("<nav>" + ulStr + "</nav>");
        };
        this.el = el;
    }
    return Pagination;
}());
