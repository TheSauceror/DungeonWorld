<?php

include "checklogin.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$enemies = mysqli_query($conn,"SELECT * FROM Enemies");

echo "Enemies:<br>";

echo "<table><tr><th>Name</th><th>Race</th><th>Experience</th><th>Max HP</th><th>Max MP</th><th>Initiative</th><th>Strength</th><th>Vitality</th><th>Dexterity</th><th>Intelligence</th><th>Piety</th></tr>";
while($row = mysqli_fetch_assoc($enemies)) {
  echo "<tr><td><a href='loadenemy.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['xp'] . "</td><td>" . $row['maxhp'] . "</td><td>" . $row['maxmp'] . "</td><td>" . $row['initiative'] . "</td><td>" . $row['str'] . "</td><td>" . $row['vit'] . "</td><td>" . $row['dex'] . "</td><td>" . $row['nce'] . "</td><td>" . $row['pie'] . "</td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>