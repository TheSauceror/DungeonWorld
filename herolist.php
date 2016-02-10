<?php

include "checklogin.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//$heroes = mysqli_query($conn,"SELECT * FROM Hero");
$heroes = mysqli_query($conn,"SELECT * FROM Hero, Party WHERE Hero.heroparty = Party.partyid");

echo "Heroes:<br>";

echo "<table><tr><th>Name</th><th>Password</th><th>Race</th><th>Profession</th><th>Experience</th><th>Party</th><th>Max HP</th><th>Max MP</th><th>Initiative</th><th>Strength</th><th>Intelligence</th><th>Dexterity</th><th>Agility</th><th>Wisdom</th><th>Perception</th><th>Action</th><th>Constitution</th><th>Charisma</th></tr>";
while($row = mysqli_fetch_assoc($heroes)) {
  echo "<tr><td><a href='loadhero.php?heroid=" . $row['heroid'] . "'>" . $row['heroname'] . "</a></td><td>" . $row['pw'] . "</td><td>" . $row['herorace'] . "</td><td>" . $row['heroprof'] . "</td><td>" . $row['xp'] . "</td><td><a href='loadparty.php?partyid=" . $row['partyid'] . "'>" . $row['partyname'] . "</a></td><td>" . $row['maxhp'] . "</td><td>" . $row['maxmp'] . "</td><td>" . $row['initiative'] . "</td><td>" . $row['str'] . "</td><td>" . $row['int'] . "</td><td>" . $row['dex'] . "</td><td>" . $row['agi'] . "</td><td>" . $row['wis'] . "</td><td>" . $row['per'] . "</td><td>" . $row['act'] . "</td><td>" . $row['con'] . "</td><td>" .  $row['cha'] . "</td></tr>";
}
echo "</table>";

mysqli_close($conn);
?>