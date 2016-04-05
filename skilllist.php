<head><title>Adventures Of Eld - Skill List</title></head>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

$skills = mysqli_query($conn,"SELECT * FROM SkillList");

echo "<div class='parchment'><h3>Skills:</h3>";
echo "<table><tr><th>ID</th><th>Name</th><th>Profession</th><th>Tier</th><th>MP Cost</th><th>Effect</th></tr>";

while($row = mysqli_fetch_assoc($skills)) {
	$rowdes = "";
  if($row['category'] == "heal") {
    $rowdes .= "Heals for ";
  } else if($row['category'] == "buff") {
    $rowdes .= "Buffs an ally for ";
  } else {
    $rowdes .= "Does ";
  }
  $rowdes .= $row['effect'] . " " . $row['type'];
  $rowdes .= " to " . $row['targets'] . " target";
  if($row['targets'] > 1) { $rowdes .= "s"; }
  if($row['skillstatus'] != "") { $rowdes .= " and " . $row['skillstatus']; }
  echo "<tr><td>" . $row['skillid'] . "</td><td>" . $row['skillname'] . "</td><td>" . $row['skillprof'] . "</td><td>" . $row['tier'] . "</td><td>" . $row['cost'] . "</td><td>" . $rowdes . "</td></tr>";
}
echo "</table></div>";

mysqli_close($conn);
?>