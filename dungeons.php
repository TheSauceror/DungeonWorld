<script>
function updateDungeon(dID) {
  document.getElementById('dungeonID').value = dID;
  document.getElementById('dungeonfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$dungeons = mysqli_query($conn,"SELECT * FROM Dungeons WHERE dungeonid > 0 ORDER BY dungeonlevel ASC, maxpeople ASC");

$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero, Party WHERE id = '$cookie[0]' AND Hero.party = Party.partyid"));

if(trim(str_replace("|", "", $hero['battleplan'])) == "") {
	echo "<center><h1>You need a battleplan to do dungeons.</h1></center>";
	exit;
}

if($hero['party'] == 0) {
	echo "<center><h1>You need a party to do dungeons.</h1></center>";
	exit;
}

if($hero['cd'] > time()) {
  echo "<center><h1>Dungeon cooldown until: " . date("m-d-y H:i:s", $hero['cd']) . "</h1></center>";
  // exit;
}

echo "Dungeons:<br>";

echo "<table class='parchment'><tr><th>Name</th><th>Rooms</th><th>Type</th><th>Level</th></tr>";
while($row = mysqli_fetch_assoc($dungeons)) {
	if($row['maxpeople'] == 1) { $row['maxpeople'] = 'Solo'; }
	if($row['maxpeople'] == 4) { $row['maxpeople'] = 'Party'; }
	if($row['maxpeople'] == 6) { $row['maxpeople'] = 'Raid'; }
  echo "<tr><td><a href='javascript:updateDungeon(" . $row['dungeonid'] . ");'>" . $row['dungeonname'] . "</a></td><td>" . sizeof(explode("|",$row['rooms'])) . "</td><td>" . $row['maxpeople'] . "</td><td>" . $row['dungeonlevel'] . "</td></tr>";
}
echo "</table>";

echo "<form name='dungeonfrm' id='dungeonfrm' method='POST' action='battle.php'><input name='dungeon' type='hidden' value='' id='dungeonID'></form>";

mysqli_close($conn);
?>