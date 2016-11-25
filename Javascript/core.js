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
		$("#" + id).html(data);
		$('[data-toggle="popover"]').popover();
	});
}