// �ㅼ젙
var define_jqgrid =
{
	    mtype:"GET",
      	datatype: "json",
        colNames:[],
        colModel:[
	        ],
        regional : 'kr', // �몄뼱 �뚯씪 �ㅼ젙
        height: '100%',
        //width: '100%',
        autowidth: true, // �먮룞留욎땄
     	pager: '',
        viewrecords: true, // �꾩껜 �덉퐫�� �� �쒖떆 �좊Т
        recordpos:'right',       //�곗륫醫뚯륫 湲곗�蹂�寃� records�� �꾩튂 �ㅼ젙
        caption:"",
        sortable: true, // �뺣젹 湲곕뒫
        sortname: '',
        sortorder: '',
        // toppager: true, // �곷떒 �섏씠吏� 酉�
        rowNum: 20, // �쒖떆 �� ��
        //rowList : [ 10,20,30,40, 50, 100], // �됱닔 �좏깮
        page: 1, // �꾩옱 �섏씠吏�
        loadonce : true, // �곗씠�� 媛��몄삤�� 諛⑹떇 true: 理쒖큹 �쒕쾲 ,
        multiSort: false, // 硫��� �뚰듃
        searching: {
        	
        },
        jsonReader: {
            repeatitems: false,
            id: "",
            root: "data",
            cell: "",
            page: function (obj){return obj.currentPage;},
            total: function (obj){return obj.totalPage;},
            records: function (obj){return obj.totalRecords;} 
          },
          loadError: function(xhr, status, error){
    	  		alert(xhr.responseText);
    	  	},
    	  	postData: {
//    	  		select_no: function(){
//    	  			return selectRow;
//    	  		}
    	  	},
        onSelectRow: function(id){ // row �좏깮��
      	},
          ondblClickRow: function(id){  // row �붾툝�대┃��
        	  //alert("You double click row with id: "+id);
        	  //$('#grid').jqGrid('editRow',id,true);
	  	},
	  	onRightClickRow: function(rowid, iRow, iCol, e){
	  		console.log("onRightClickRow:function = " + rowid);
	  	},
	  	onCellSelect: function(rowid, index, contents, event) {
	  		//console.log("onCellSelect:function = " + rowid);
	  	},
	  	gridComplete: function(){
//	  		if(selectRow != -1){
//	  			$("#grid").jqGrid("setSelection", selectRow); //濡쒖슦 媛뺤젣 �좏깮
//	  			selectRow = -1;
//	  		}
		}
    }


//jqgrid 寃��� �꾨뱶 x 踰꾪듉 �④�
//jQuery.extend(jQuery.jgrid.defaults, {
//    cmTemplate: {
//        searchoptions: {
//            clearSearch: false
//        }
//    }
//});



// base set
function jqgridBaseSet(grid, page){
	 
    //$(grid).jqGrid('filterToolbar',{searchOperators : true, stringResult: true, searchOnEnter : true, autosearch:false }); // autosearch: false �먮룞 寃��� 留됯린
    $(grid).jqGrid('navGrid',page,{cloneToTop: true, edit:false,add:false,del:false,search: false,refresh: true});
    
//	    $("#grid").jqGrid('navGrid','#gridPager',{edit:false,add:false,del:false});
    $(grid).jqGrid('gridResize',{minWidth:600,maxWidth:1000,minHeight:200, maxHeight:800});
    
	 // Bind the navigation and set the onEnter event
    $(grid).jqGrid('bindKeys', {"onEnter":function( rowid ) { 
    	//alert("You enter a row with id:"+rowid)
    	} 
    });
    
    //grid title �④�泥섎━
    $('.ui-jqgrid-titlebar').hide();

    //�덈퉬瑜� 100%(醫뚯슦 �⑤뵫媛� 40 �쒖쇅)濡� 留욎땄
    //var pageWidth = $("#box-grid").width();
   // var listWidth = pageWidth - 40;
    //$(grid).setGridWidth(listWidth);
    
    
    // 寃��� �꾨뱶 �대깽�� ��젣
    $("#gs_USE_AT").unbind("change");
}

function jqgridBaseSetMain(grid, page, wObj){
	 
    //$(grid).jqGrid('filterToolbar',{searchOperators : false, stringResult: true, searchOnEnter : true,  }); // autosearch: false �먮룞 寃��� 留됯린
    $(grid).jqGrid('navGrid',page,{cloneToTop: true, edit:false,add:false,del:false,search: false,refresh: true});
    
//	    $("#grid").jqGrid('navGrid','#gridPager',{edit:false,add:false,del:false});
    $(grid).jqGrid('gridResize',{minWidth:600,maxWidth:1000,minHeight:200, maxHeight:800});
    
	 // Bind the navigation and set the onEnter event
    $(grid).jqGrid('bindKeys', {"onEnter":function( rowid ) { 
    	//alert("You enter a row with id:"+rowid)
    	} 
    });
    
    //grid title �④�泥섎━
    $('.ui-jqgrid-titlebar').hide();

    //�덈퉬瑜� 100%(醫뚯슦 �⑤뵫媛� 40 �쒖쇅)濡� 留욎땄
    var pageWidth = $(wObj).width();
    var listWidth = pageWidth - 40;
    $(grid).setGridWidth(listWidth);
    
    
    // 寃��� �꾨뱶 �대깽�� ��젣
    $("#gs_USE_AT").unbind("change");
}

function jqgridBaseSetWidth(){		//grid �덈퉬瑜�  100%(醫뚯슦 �⑤뵫媛� 40 �쒖쇅)濡� 留욎땄
	var pageWidth=$("#DivContents").width();
	var listWidth = pageWidth - 40;
	 $(grid).setGridWidth(listWidth);
}


//formater ==================================================================
function dateFomater(cellValue, options, rowObject){
	if(cellValue == null) return '';
	var date = cellValue.replace(' ','T') + 'Z' //ie 吏��� �좎쭨 �щĸ
	var d = new Date(date);
    var month = '' + (d.getUTCMonth() + 1);
    var day = '' + d.getUTCDate();
    var year = '' + d.getUTCFullYear();
	var hour = '' + d.getUTCHours();
	var min = '' + d.getUTCMinutes();
	var sec = '' + d.getUTCSeconds();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    if (hour.length < 2) hour = '0' + hour;
    if (min.length < 2) min = '0' + min;
//    if (sec.length < 2) sec = '0' + sec;
	 	return [year, month, day].join('-') + ' ' +[hour, min].join(':');
}


function useFomater(cellValue, options, rowObject){
 	return cellValue == true ? "�ъ슜" : "�ъ슜�덊븿";
}




