<head><title>Adventures Of Eld - Enemy List</title></head>

<?php

include "checklogin.php";
include "menu.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$enemies = mysqli_query($conn,"SELECT * FROM Enemies");

echo "<div class='parchment'><h3>Enemies:</h3>";
echo "<table><tr><th>Name</th><th>Race</th><th>Prof</th><th>Gold</th><th>HP</th><th>MP</th><th>Init</th><th>Str</th><th>Vit</th><th>Dex</th><th>Int</th><th>Pie</th><th>S. Pwr</th><th>P. Pwr</th><th>B. Pwr</th><th>A. Pwr</th><th>D. Pwr</th><th>S. Def</th><th>P. Def</th><th>B. Def</th><th>A. Def</th><th>D. Def</th></tr>";
while($row = mysqli_fetch_assoc($enemies)) {
  echo "<tr><td><a href='loadenemy.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "</td><td>" . $row['gold'] . "</td><td>" . $row['maxhp'] . "</td><td>" . $row['maxmp'] . "</td><td>" . $row['initiative'] . "</td><td>" . $row['str'] . "</td><td>" . $row['vit'] . "</td><td>" . $row['dex'] . "</td><td>" . $row['nce'] . "</td><td>" . $row['pie'] . "</td><td>" . $row['sdam'] . "</td><td>" . $row['pdam'] . "</td><td>" . $row['bdam'] . "</td><td>" . $row['adam'] . "</td><td>" . $row['ddam'] . "</td><td>" . $row['sarm'] . "</td><td>" . $row['parm'] . "</td><td>" . $row['barm'] . "</td><td>" . $row['aarm'] . "</td><td>" . $row['darm'] . "</td></tr>";
}
echo "</table></div>";

mysqli_close($conn);
?>