<?php

include "checklogin.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

echo "Items:<br>";

$items = mysqli_query($conn,"SELECT * FROM Item WHERE market>0");
echo "<table><tr><th>ID</th><th>Prefix</th><th>Base</th><th>Suffix</th><th>Cost</th><th>Owner</th><th>Slot</th><th>Equip</th><th>S. Dmg</th><th>P. Dmg</th><th>B. Dmg</th><th>S. Arm</th><th>P. Arm</th><th>B. Arm</th><th>Buy</th></tr>";

while($row = mysqli_fetch_assoc($items)) {
  echo "<tr><td>" . $row['id'] . "</td><td>" . $row['pre'] . "</td><td>" . $row['base'] . "</td><td>" . $row['suf'] . "</td><td>" . $row['market'] . "</td><td><a href='loadhero.php?searchname=" . $row['owner'] . "'>" . $row['owner'] . "</a></td><td>" . $row['slot'] . "</td><td>" . $row['equip'] . "</td><td>" . $row['sdam'] . "</td><td>" . $row['pdam'] . "</td><td>" . $row['bdam'] . "</td><td>" . $row['sarm'] . "</td><td>" . $row['parm'] . "</td><td>" . $row['barm'] . "</td><td><a>Buy</a></td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>