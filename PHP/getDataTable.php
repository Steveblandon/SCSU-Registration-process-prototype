<?php
require "dbConnect.php";
$sql = "";


//add table connections here
if ($_POST["type"] == "table-search" && isset($_POST["subject"])){
	$weekdays = ["mon","tue","wed","thu","fri"];
	$dayCount = count($weekdays);
	$days = "";
	$addonSearch = "";
	$addOr = 0;
	for ($i = 0; $i < $dayCount; $i++){
		if (isset($_POST[$weekdays[$i]])){
			if ($addOr == 1) $days = $days." OR ";
			$days = $days."courses.Days LIKE '".strtoupper($_POST[$weekdays[$i]])."%'";
			$addOr = 1;
		}
	}
	if ($days != ""){
		$addonSearch = " AND (".$days.")";
	}
	if (isset($_POST["subject"])){
		if ($_POST["subject"] != "--ALL--"){

			$addonSearch = $addonSearch." AND courses.Subject LIKE '".$_POST["subject"]."'";
		}
	}
	if (isset($_POST["courseno"])){
		if ($_POST["courseno"] != "--ALL--"){
			$addonSearch = $addonSearch." AND courses.CourseNo LIKE '".$_POST["courseno"]."'";
		}
	}
	if (isset($_POST["instructor"])){
		if ($_POST["instructor"] != "--ALL--"){
			//this needs a quick fix because the sql data scrape for some reason has a double space inbetween the instructor names
			$name = strrev($_POST["instructor"]);
			$name = substr_replace($name, "  ", strpos($name, " "), 0);
			$addonSearch = $addonSearch." AND courses.Instructor LIKE '".strrev($name)."'";
		}
	}
	//select only records that are not already in schedule
	$sql = "SELECT courses.Subject, courses.CourseNo, courses.SectionNo, courses.Credits, courses.Title, courses.Days, CONCAT(DATE_FORMAT(courses.startTime,'%h:%i%p'),'-',DATE_FORMAT(courses.endTime,'%h:%i%p')) AS Time, courses.Instructor, courses.Date, courses.Location FROM courses LEFT JOIN schedule ON (courses.CourseNo = schedule.CourseNo) AND (courses.SectionNo = schedule.SectionNo) WHERE schedule.CourseNo IS NULL AND schedule.SectionNo IS NULL".$addonSearch;
}
else if ($_POST["type"] == "table-current"){
	$sql = "SELECT Subject, CourseNo, SectionNo, Credits, Title, Days, CONCAT(DATE_FORMAT(startTime,'%h:%i%p'),'-',DATE_FORMAT(endTime,'%h:%i%p')) AS Time, Instructor, Date, Location FROM schedule";
}

$nr = false;
if ($sql == ""){
	$nr = true;
}
else{
	$result = $conn->query($sql);
	if (!$result){
		die("query failed" . $conn->error);
	}
	if ($result->num_rows == 0){
			$nr = true;
	}
	else{
		echo "<thead><tr>";
		while($field = $result->fetch_field()){
			echo "<th>" . $field->name . "</th>";
		}
		echo "</tr></thead>";
		echo "<tbody>";
		while($row = $result->fetch_row()){
			$useDefaultTag = 1;
			if (strtolower($_POST["type"]) == "table-search"){
				//do a search to see if this record conflicts with a record on schedule
				$sql = "SELECT * FROM courses WHERE CourseNo LIKE '".$row[1]."' AND SectionNo LIKE '".$row[2]."'";
				$result2 = $conn->query($sql);
				$course = $result2->fetch_row();
				//see if there is anything in the schedule within the same time frames
				$sql = "SELECT * FROM schedule WHERE days LIKE '".substr($course[5],0,1)."%' AND ((startTime >= '".$course[6]."' AND startTime <= '".$course[7]."') OR (endTime >= '".$course[6]."' AND endTime <= '".$course[7]."'))";
				$result3 = $conn->query($sql);
				if (count($result3->fetch_row()) > 0){
					echo "<tr class='table-row-unselectable' data-placement='bottom' data-toggle='popover' data-trigger='hover' title='Time Conflict:' data-content='the time period of this course section interferes with your current schedule!'>";
					$useDefaultTag = 0;
				}
			}
			if ($useDefaultTag == 1){
				echo "<tr class='table-row-selectable'>";
			}
			$max = count($row);
			for($i = 0; $i < $max; $i++){
				echo "<td>" . $row[$i] . "</td>";
			}
			echo "</tr>";
		}
	}
}


if ($nr == true){
	echo "<h1 style='text-align:center; margin-top:20px; color:grey;'>no results</h1>";
}
?>