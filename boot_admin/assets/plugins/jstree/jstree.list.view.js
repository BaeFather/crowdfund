
//########################################################################################
//# View Setting Configuration Variable									     
//########################################################################################

// View Setting
var viewSetting = {
		
	// All Data Realated to grid
	grid : {
		
		// Grid selector
		el: null, 
		
		// This where on selected row will get the id needed for sending
		// multiple Update of Details or Delete
		id : "seq",
		
		// Java List Field variable Name
		collectionName: 'seqCollection',
	},
	// All Data Related to Search
	search : {
		// Search form
		el : "#searchForm",
		// param
		param : function(param) {
			
		}, 
		
		// Url where request will be submitted 
		// For now this can only support non-jg-autosearch forms
		url : null,
		// On Search Success Listner can on be use by
		// non-jg-autosearch forms
		onSearchSuccess : function(response) {
			
		}
	},
	// All Data Related to Multiple Update
	multipleTransaction: {
		// Generalize all URL but can be override by adding a manual to 
		// Element Attribute
		url: null,
		// Override Parameter that will be send to URL
		// Must return boolean to determine if needed to continue on
		// validation of data
		//
		//
		// RETURN Meaning
		// TRUE 	: Continue
		// FALSE 	: Do not Continue
		//
		// Parameter Meaning 
		// param 	: parameter container
		// type 	: return transaction type ("UPDATE", "DELETE")
		// category : get attribute request category
		param : function (param, type, category){
			
			return true;
		}
	},
};


//########################################################################################
//# Autosearch class for form									     
//########################################################################################


// When auto search is submitted
$(".autosearch").on('submit', function(e){
	// don't reload page
	e.preventDefault();
	
	// Get grid
	var grid = $(viewSetting.grid.el);
	
	// clear grid
	grid.jqGrid("clearGridData", true);
	
	var postData = $(this).serializeSearchForm();
	
	if(viewSetting && viewSetting.search && viewSetting.search && viewSetting.search.param){
		if(viewSetting.search.param instanceof Function) 
			postData = viewSetting.search.param(postData);
	}
	
	
	var rowNum = $("#grid").jqGrid('getGridParam', 'rowNum');
	
	grid.jqGrid('setGridParam', {
			url : define_jqgrid.url, 
			mtype : 'GET', 
			datatype : 'json',
			rowNum : postData.pageSize || rowNum || 20,
			postData : postData,
	}).trigger('reloadGrid');
	
});

//When auto search is submitted
$('.non-jg-autosearch').on('submit', function(e){
	// don't reload page
	e.preventDefault();
	// get data form this form
	var postData = $(this).serializeSearchForm();
	
	if(viewSetting && viewSetting.search && viewSetting.search && viewSetting.search.param){
		if(viewSetting.search.param instanceof Function) 
			postData = viewSetting.search.param(postData);
	}
	
	$.ajax({
		url : define_jqgrid.url,
		method: "GET",
		dataType: "JSON",
		data: postData,
		success: viewSetting.search.onSearchSuccess
	})
	
});

//########################################################################################
//# Multiple Process									     
//########################################################################################


// Multiple Batch
$(".process")

	// initialize needed data
	.on('click','.update,.delete', function(e){
		
		// get current button
		this._currentBtn = $(this);
		
		// check if there's an available url
		if(!this._currentBtn.data("url") && !viewSetting.multipleTransaction.url)
			throw("No URL Found")
			
			
		// check if el has value
		if (!viewSetting.grid.el)
			throw("Cannot find grid Element");
		// Check if key has value
//		if (!viewSetting.grid.id)
//			throw("Cannot find grid row id key");
		
		// initialize grid
		this._grid = $(viewSetting.grid.el);
		
		// get id key
		this._idKey = viewSetting.grid.id;
		// get collection name
		this._collectionName = viewSetting.grid.collectionName;
		// set param
		this._param = {};
		// set get category
		this._category 		= this._currentBtn.data("category");
		this._param['batch']= this._currentBtn.data("batch");

		// get id array
		this._idArr = this._grid.getGridSelectedRow(this._idKey);
		
		// check if there's a selected row
		if (this._idArr.length == 0 ) {
			alert("�묒뾽�� 泥섎━�� ��ぉ�� 1媛� �댁긽 �좏깮�섏꽭��.")
			e.stopImmediatePropagation();
			return;
		}
		
		// get url
		this._url = this._currentBtn.data("url") || viewSetting.multipleTransaction.url;
			
		// convert idArr to java list readable format
		addArrayAsJavaList(this._param, this._idArr, this._collectionName );
		
		
	})
	// when update button is clicked
	.on('click', '.update', function(){
		
		
		var toContinue = true;
		
		if (viewSetting.multipleTransaction && viewSetting.multipleTransaction.param )
			toContinue = viewSetting.multipleTransaction.param(this._param, "UPDATE", this._category);
		
		if(toContinue){
			// check if there is target attribute
			if(!this._currentBtn.data('target'))
				throw('Cannot find target attribute');
			
			
			var _tV = this._currentBtn.data('target');
			
			if(!_tV.length) {
				console.error('no target found');
				return;
			}
			
			_tV = _tV.split(",");
			
			for (var i = 0 ; i < _tV.length; i++) {
				
				var target = $('.process '+ _tV[i]);
				// check if target has a value
				if(!target.val() || target.val() == "0" || target.val() < 1) {
					alert('�쇨큵 蹂�寃쏀븷 媛믪쓣 �좏깮�섏꽭��.');
					return;
				}
				// add select value to param
				
				var _tN = target.attr('name');
				if (!_tN) {
					console.error('No name attriute has been found')
					return;
				}
				this._param[_tN] = target.val(); 
				
			}
		}
		
		// check if it will proceed to update
		if(!confirm('�좏깮�� ��ぉ�� �낅뜲�댄듃 �섏떆寃좎뒿�덇퉴?')) return;
		
	
		
		// send put request
		$.put(this._url, this._param, function(response){
			if (response.status == "SUCCESS") {
				$(viewSetting.search.el).trigger('submit');
			}
			alert(response.message);
		});
		
		
	})
	// when delete button is clicked
	.on('click', '.delete', function(){
		
		// check if it will proceed to delete
		if (!confirm('�좏깮�� ��ぉ�� ��젣 �섏떆寃좎뒿�덇퉴?')) return;
		// send put request
		if(viewSetting.multipleTransaction && viewSetting.multipleTransaction.param)
			viewSetting.multipleTransaction.param(this._param, "DELETE", this._category);
		
		$.delete(this._url, this._param, function(response){
			if (response.status == "SUCCESS") {
				$(viewSetting.search.el).trigger('submit');
			}
			alert(response.message);
		});
	});


//########################################################################################
//# Date Formatter Template 									     
//########################################################################################

function dateFormatter(value, row, rowData) {	
	if(value==null){
		return "-";
	}
	var date = null;
	try{
		var timeStamp = Number(value);
		date = new Date(timeStamp);
	}catch(e)
	{
		value = value.split('+')[0];
		value = value + "Z";
		date = new Date(value);
	}
	return date.getFullYear() + "-" + ('0' + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2) + " " + ('0' + date.getHours()).slice(-2) + ":" + ('0' + date.getMinutes()).slice(-2) + ":" + ('0' + date.getSeconds()).slice(-2);

}

function dateFormatter2(value, row, rowData) {	
	if(value==null){
		return "-";
	}
	try{
		var timeStamp = Number(value);
		date = new Date(timeStamp);
	}catch(e)
	{
		value = value.split('+')[0];
		value = value + "Z";
		date = new Date(value);
	}
	return date.getFullYear() + "-" + ('0' + (date.getMonth() + 1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2);
	
}

//grid �덉쓽 �대�吏� mouse over �� �뺣�蹂닿린 
// TBL PROD LIST
$('table').on("mouseenter", ".imgPrev",function(){ // On Mouse Hover
	var pos = $(this).offset();
	var div = $("#DivPreviewImageProd");
	div.html("<img src='" + $(this).attr('src') + "' style='width:200px; height:200px;' onerror=\"this.src ='/images/common/bg/prod_no_75x75_1.gif'\" />");
	div.css({ top: pos.top + "px", left: (pos.left + 30) + "px" }).show();
	
}).on("mouseleave", '.imgPrev', function(){ // On Mouse Out
	$("#DivPreviewImageProd").hide();
});

