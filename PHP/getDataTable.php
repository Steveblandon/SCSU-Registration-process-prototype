<?php
require "dbConnect.php";
$sql = "";


//add table connections here
if (strtolower($_POST["type"]) == "table-search"){
	$sql = "SELECT Subject, CourseNo, SectionNo, Credits, Title, Days, CONCAT(DATE_FORMAT(startTime,'%h:%i%p'),'-',DATE_FORMAT(endTime,'%h:%i%p')) AS Time, Instructor, Date, Location FROM courses";
}
else if (strtolower($_POST["type"]) == "table-current"){
	$sql = "SELECT Subject, CourseNo, SectionNo, Credits, Title, Days, CONCAT(DATE_FORMAT(startTime,'%h:%i%p'),'-',DATE_FORMAT(endTime,'%h:%i%p')) AS Time, Instructor, Date, Location FROM schedule";
}


//add table connections here
$result = $conn->query($sql);
if (!$result){
	die("query failed" . $conn->error);
}
echo "<thead><tr>";
while($field = $result->fetch_field()){
	echo "<th>" . $field->name . "</th>";
}
echo "</tr></thead>";
echo "<tbody>";
while($row = $result->fetch_row()){
	$i = 0;
	$max = count($row);
	if ($max == 0) echo "nothing";
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
	while($i < $max){
		echo "<td>" . $row[$i] . "</td>";
		$i++;
	}
	echo "</tr>";
}
?>