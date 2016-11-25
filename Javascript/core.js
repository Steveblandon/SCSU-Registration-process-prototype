$(document).ready(function(){
	//load current schedule table by default
	tableId = "table-current";
	loadData(tableId);

	$("a[data-toggle='tab']").click(function(){
		tableId = "";
		if ($(this).attr("id") == "tab1"){
			tableId = "table-current";
			enableButton("#button-addto-schedule");
			$("#button-addto-schedule").text("Submit");
			$("#button-addto-schedule").attr("id","button-submit-schedule");
		}
		else{
			tableId = "table-search";
			$("#button-submit-schedule").text("Add to Schedule");
			$("#button-submit-schedule").attr("id","button-addto-schedule");
			disableButton("#button-addto-schedule");
		}
		loadData(tableId);
	});

	$("table").on("click","tr.table-row-selectable", function(){
		$("tr.table-row-selected").addClass("table-row-selectable");
		$("tr.table-row-selected").removeClass("table-row-selected");
		toggleSelection(this);
		enableButton("#button-addto-schedule");
	});
	
	$("table").on("click","tr.table-row-selected", function(){
		toggleSelection(this);
		disableButton("#button-addto-schedule");
	});

	$("div.btn-group").on("click","#button-addto-schedule", function(){
		if($(this).attr("disabled") != "disabled"){
			fields = $(($("tr.table-row-selected"))[0]).children();
			cNo = $(fields[1]).text();
			sNo = $(fields[2]).text();
			$.post("PHP/addToSchedule.php", {courseNo:cNo,sectionNo:sNo}, function(data, status){
				bootbox.alert(data);
				loadData("table-search");
			});
		}
	});
});



function toggleSelection(element){
	$(element).toggleClass("table-row-selected");
	$(element).toggleClass("table-row-selectable");
}

function disableButton(id){
	$(id).attr('disabled','true');
	$(id).addClass('btn-default');
	$(id).removeClass('btn-primary');
}

function enableButton(id){
	$(id).removeAttr('disabled');
	$(id).removeClass('btn-default');
	$(id).addClass('btn-primary');	
}


function loadData(id){
	$.post("PHP/getDataTable.php", {type:id}, function(data, status){
		$("#" + id).html(data);
		$('[data-toggle="popover"]').popover();
	});
}