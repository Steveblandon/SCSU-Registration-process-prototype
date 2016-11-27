<?php
require "dbConnect.php";


$sql = "DELETE FROM schedule WHERE CourseNo LIKE '".$_POST['courseNo']."' AND SectionNo LIKE '".$_POST['sectionNo']."'";

$result = $conn->query($sql);
if (!$result){
	die("query failed" . $conn->error);
}
else echo "course has been removed!";
?>