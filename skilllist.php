<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

echo "Skills:<br>";

$skills = mysqli_query($conn,"SELECT * FROM SkillList");
echo "<table><tr><th>ID</th><th>Name</th><th>Profession</th><th>Cost</th><th>Effect</th><th>Type</th><th>Range</th><th>Duration</th><th>Targets</th></tr>";

while($row = mysqli_fetch_assoc($skills)) {
  echo "<tr><td>" . $row['skillid'] . "</td><td>" . $row['skillname'] . "</td><td>" . $row['skillprof'] . "</td><td>" . $row['cost'] . "</td><td>" . $row['effect'] . "</td><td>" . $row['type'] . "</td><td>" . $row['range'] . "</td><td>" . $row['duration'] . "</td><td>" . $row['targets'] . "</td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>