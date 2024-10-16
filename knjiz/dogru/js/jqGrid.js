queryOptions = {};
function JqGrid(tableId, jSon, ajaxResource)
{
	var pagerId = tableId + '_pager';
	$('<div></div>').appendTo(document.body).attr('id', pagerId);
	dt = JSON.parse(jSon);
	var colNames = [];
	var colModels= [];
	var v = 0;
	for(var i in dt.DataTable.Columns)
	{
		var c = dt.DataTable.Columns[i];
		colNames.push(c.DisplayName);
		colModels.push({
			name: c.Name,
			index: c.Name,
			sorttype:c.Type
		});
	}

	var ajaxOptions = {
		url:Page.GetCurrentUrl() + '&grid=1',
		datatype: "json",
		mtype: 'POST',
		viewrecords : true,
		pager: pagerId,
		postData: {
			table_id : function(){
				return 'tableTest2'
			}
		}
	};
	var options = {
		datatype: "local",
		autowidth: true,
		height: 250,
		colNames: colNames,
		colModel: colModels,
		rowNum:10,
		rowList:[10,20,30],
	};
	if (ajaxResource)
		for(var i in ajaxOptions)
			options[i] = ajaxOptions[i];
	$('#' + tableId).css('width', '100%').jqGrid( options );
}


