<?php
require "dbConnect.php";

if ($_POST["value"] == "--ALL--"){
	$sql = "SELECT subject, courseNo, instructor FROM courses";
}
else $sql = "SELECT subject, courseNo, instructor FROM courses WHERE ".$_POST["field"]." LIKE '".$_POST["value"]."'";

$result = $conn->query($sql);
if (!$result){
	die("query failed" . $conn->error);
}

$fields = [];
$data = [];
while($field = $result->fetch_field()){
	$fields[] = $field->name;
	$data[] = ["<option>".$field->name."</option>"];
}

$colCount = count($fields);
while($row = $result->fetch_row()){
	for ($c = 0; $c < $colCount; $c++){
		if (strtolower($fields[$c]) != strtolower($_POST["field"])){
			$data[$c][] = "<option>".$row[$c]."</option>";
		}
	}
}

for ($c = 0; $c < $colCount; $c++){
	if (strtolower($fields[$c]) != strtolower($_POST["field"])){
		$dat = array_values(array_unique($data[$c]));
		$recordCount = count($dat);
		for ($i = 0; $i < $recordCount; $i++){
			echo $dat[$i];
		}
	}
}

?>