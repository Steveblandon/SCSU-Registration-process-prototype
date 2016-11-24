<?php
require "dbConnect.php";
$sql = "";


//add table connections here
if (strtolower($_POST["type"]) == "table-search"){
	$sql = "SELECT * FROM courses";
}
else if (strtolower($_POST["type"]) == "table-current"){
	$sql = "SELECT * FROM schedule";
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
	echo "<tr>";
	while($i < $max){
		echo "<td>" . $row[$i] . "</td>";
		$i++;
	}
	echo "</tr>";
}
?>