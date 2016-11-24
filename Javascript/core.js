$(document).ready(function(){
	tableId = "table-current";
	loadData(tableId);
	$("a[data-toggle='tab']").on("click",function(){
		tableId = "";
		if ($(this).attr("id") == "tab1"){
			tableId = "table-current";
		}
		else{
			tableId = "table-search";
		}
		loadData(tableId);
	});
});

function loadData(id){
	$.post("PHP/getDataTable.php", {type:id}, function(data, status){
			$("#" + tableId).html(data);
			children = $("#" + tableId + " > tbody").children();
			for(var i=0; i < children.length; i++){
				//check for issues now
			}
		});
}