$(document).ready(function(){
	//load current schedule table by default
	searchCriteria = "";
	tableId = "table-current";
	loadData(tableId);


	$("a[data-toggle='tab']").on("click", function(){
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


	$("div.btn-group").on("click","#button-addto-schedule", function(){
		if($(this).attr("disabled") != "disabled"){
			fields = $(($("tr.table-row-selected"))[0]).children();
			cNo = $(fields[1]).text();
			sNo = $(fields[2]).text();
			$.post("PHP/addToSchedule.php", {courseNo:cNo,sectionNo:sNo}, function(data, status){
				bootbox.alert("<b>"+data+"</b>");
				loadData("table-search");
			});
		}
	});


	$("div.btn-group").on("click","#button-remove-course", function(){
		bootbox.confirm("<b>Are you sure you want to remove this course from your schedule?</b>", function(result){
			if (result === false) {
		        $("tr.table-row-selected").addClass("table-row-selectable");
				$("tr.table-row-selected").removeClass("table-row-selected");
		    } 
		    else {
		        // result has a value
		        fields = $(($("tr.table-row-selected"))[0]).children();
				cNo = $(fields[1]).text();
				sNo = $(fields[2]).text();
				$.post("PHP/removeCourse.php", {courseNo:cNo,sectionNo:sNo}, function(data, status){
					bootbox.alert("<b>"+data+"</b>");
					loadData("table-current");
				});
		    }
		    $("#button-remove-course").text("Submit");
			$("#button-remove-course").removeClass("btn-danger");
			$("#button-remove-course").addClass("btn-primary");
			$("#button-remove-course").attr("id","button-submit-schedule");
		});
	});


	$("table").on("click","tr.table-row-selectable", function(){
		$("tr.table-row-selected").addClass("table-row-selectable");
		$("tr.table-row-selected").removeClass("table-row-selected");
		toggleSelection(this);
		enableButton("#button-addto-schedule");
		if (tableId == "table-current"){
			$("#button-submit-schedule").text("Remove");
			$("#button-submit-schedule").removeClass("btn-primary");
			$("#button-submit-schedule").addClass("btn-danger");
			$("#button-submit-schedule").attr("id","button-remove-course");

		}
	});

	
	$("table").on("click","tr.table-row-selected", function(){
		toggleSelection(this);
		disableButton("#button-addto-schedule");
	});

	$("#button-search").on("click", function(){
		loadSearchCriteria("", "--ALL--");
	});


	$("select").on("change", function(){
		//dynamic updating of remaining options
		elementName = $(this).attr("name");
		id = $(this).attr("id");
		val = $("#"+id+" option:selected").text();
		loadSearchCriteria(elementName, val);
	});


	$("form").on("submit",function(){
		event.preventDefault();
		tab = $(".nav-tabs").children();
		$(tab[0]).removeClass("active");
		$(tab[1]).addClass("active");
		pane = $(".tab-content").children();
		$(pane[0]).removeClass("active");
		$(pane[1]).addClass("active");
		searchCriteria = $(this).serialize();
		loadData("table-search");
		$("#modal-search-container").modal("hide");
	});
});



function loadSearchCriteria(elementName, val){
	$.post("PHP/getSearchOptions.php",{field:elementName, value:val}, function(data, status){
		selections = $("#form-search").find("select");
		sCount = selections.length;
		fields = [];
		for (i=0; i < sCount; i++){
			name = ($(selections[i]).attr("name"));
			if (name != "undefined"){
				fields.push(name.toLowerCase());
			}
		}
		options = $.parseHTML(data);
		optionCount = options.length;
		selection = "";
		for (i=0; i < optionCount; i++){
			item = ($(options[i]).text());
			if (fields.indexOf(item.toLowerCase()) != -1){
				selOpt = $("#form-input-" + item.toLowerCase()+" option:selected").text();
				if(selOpt == "--ALL--" || selOpt == ""){
					selection = "#form-input-" + item.toLowerCase();
					$(selection).html("<option selected>--ALL--</option>");
				}
			}
			else{
				$(selection).html($(selection).html() + "<option>"+item+"</option>");
			}
		}
	});
}

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
	searchCriteria = searchCriteria + "&type="+id;
	$.post("PHP/getDataTable.php", searchCriteria, function(data, status){
		$("#" + id).html(data);
		$('[data-toggle="popover"]').popover();
	});
}