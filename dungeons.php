<script>
function leave(leaveID) {
  document.getElementById('leaveID').value = leaveID;
  document.getElementById('leavefrm').submit();
}
function create(createID) {
  document.getElementById('createID').value = createID;
  document.getElementById('createfrm').submit();
}
function join(joinID) {
  document.getElementById('joinID').value = joinID;
  document.getElementById('joinfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

//$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));

if(trim(str_replace("|", "", $hero['battleplan'])) == "") {
	echo "<center><h1>You need a battleplan to do dungeons.</h1></center>";
	exit;
}

if($hero['cd'] > time()) {
  echo "<center><h1>Dungeon cooldown until: " . date("m-d-y H:i:s", $hero['cd']) . "</h1></center>";
  // exit;
}

if(isset($_POST['leaveid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $leaveid = mysqli_real_escape_string($conn, $_POST['leaveid']);
  $members = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE party = '$hero[party]'"))['members'];
  if($members == 1) { mysqli_query($conn, "DELETE FROM Party WHERE partyid = '$hero[party]'"); }
  mysqli_query($conn, "UPDATE Hero SET party = '0' WHERE id = '$leaveid'");
  $hero['party'] = 0;
  echo "You have left your party!";
}

if(isset($_POST['createid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $createid = mysqli_real_escape_string($conn, $_POST['createid']);
  mysqli_query($conn,"INSERT INTO Party (dungeonid) VALUES ('$createid')") or die(mysqli_error($conn));
  $newparty = mysqli_insert_id($conn);
  mysqli_query($conn, "UPDATE Hero SET party = '$newparty' WHERE id = '$hero[id]'");
  $hero['party'] = $newparty;
  echo "You have created a party!";
  $members = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE party = '$hero[party]'"))['members'];
  $maxpeople = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party, Dungeons WHERE Party.dungeonid = Dungeons.dungeonid AND Party.partyid = '$hero[party]'"));
  echo $members;
  echo $maxpeople['maxpeople'];
  if($members == $maxpeople['maxpeople']) { header('Location: battle.php?partyid=' . $hero['party']); }
}

if(isset($_POST['joinid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $joinid = mysqli_real_escape_string($conn, $_POST['joinid']);
  mysqli_query($conn, "UPDATE Hero SET party = '$joinid' WHERE id = '$hero[id]'");
  $hero['party'] = $joinid;
  echo "You have joined a party!";
  $members = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE party = '$hero[party]'"))['members'];
  $maxpeople = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party, Dungeons WHERE Party.dungeonid = Dungeons.dungeonid AND Party.partyid = '$hero[party]'"));
  if($members == $maxpeople['maxpeople']) { header('Location: battle.php?partyid=' . $hero['party']); }
}

$dungeons = mysqli_query($conn,"SELECT * FROM Dungeons WHERE dungeonid > 0 AND Dungeons.dungeonlevel <= '$hero[dungeonlevel]' ORDER BY dungeonlevel ASC, maxpeople ASC");
$parties = mysqli_query($conn,"SELECT * FROM Party, Dungeons WHERE Party.dungeonid = Dungeons.dungeonid AND Dungeons.dungeonlevel <= '$hero[dungeonlevel]'");

if($hero['party'] == 0) {
  echo "<h1>Dungeons:</h1>";
  echo "<table class='parchment'><tr><th>Dungeon Name</th><th>Type</th><th>Level</th><th>Create Party</th></tr>";
  while($row = mysqli_fetch_assoc($dungeons)) {
  	if($row['maxpeople'] == 1) { $row['maxpeople'] = 'Solo'; }
  	if($row['maxpeople'] == 4) { $row['maxpeople'] = 'Party'; }
  	if($row['maxpeople'] == 6) { $row['maxpeople'] = 'Raid'; }
    echo "<tr><td>" . $row['dungeonname'] . "</a></td><td>" . $row['maxpeople'] . "</td><td>" . $row['dungeonlevel'] . "</td><td><a href='javascript:create($row[dungeonid]);'>";
    if($row['maxpeople'] == 'Solo') {
    echo "Run Dungeon";
    } else {
    echo "Create Party";
    }
    echo "</a></td></tr>";
  }
  echo "</table>";
} else {
  $membernames = "";
  $party = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party, Dungeons WHERE Party.partyid = '$hero[party]' AND Dungeons.dungeonid = Party.dungeonid"));
  $partymembers = mysqli_query($conn,"SELECT * FROM Hero WHERE party = '$hero[party]'");
  while($row = mysqli_fetch_assoc($partymembers)) {
    if($membernames != "") { $membernames .= ", "; }
    $membernames .= "<a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a>";
  }
  $party['members'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE party = '$hero[party]'"))['members'];
  echo "<h1>Your Party:</h1>";
  echo "<table class='parchment'><tr><th>Dungeon Name</th><th>Level</th><th>Members</th><th>Member Names</th><th>Leave Party</th></tr>";
  //if($party['maxpeople'] == 1) { $party['maxpeople'] = 'Solo'; }
  //if($party['maxpeople'] == 4) { $party['maxpeople'] = 'Party'; }
  //if($party['maxpeople'] == 6) { $party['maxpeople'] = 'Raid'; }
  echo "<tr><td>" . $party['dungeonname'] . "</a></td><td>" . $party['dungeonlevel'] . "</td><td>" . $party['members'] . " / " . $party['maxpeople'] . "</td><td>" . $membernames . "</td><td><a href='javascript:leave($hero[id]);'>X</a></td></tr>";
  echo "</table>";  
}

echo "<h1>Parties:</h1>";
echo "<table class='parchment'><tr><th>Dungeon Name</th><th>Level</th><th>Members</th>";
if($hero['party'] == 0) { echo "<th>Join Party</th>"; }
echo "</tr>";
while($row = mysqli_fetch_assoc($parties)) {
	$row['members'] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS members FROM Hero WHERE party = '$row[partyid]'"))['members'];
	//if($row['maxpeople'] == 1) { $row['maxpeople'] = 'Solo'; }
	//if($row['maxpeople'] == 4) { $row['maxpeople'] = 'Party'; }
	//if($row['maxpeople'] == 6) { $row['maxpeople'] = 'Raid'; }
  echo "<tr><td>" . $row['dungeonname'] . "</td><td>" . $row['dungeonlevel'] . "</td><td>" . $row['members'] . " / " . $row['maxpeople'] . "</td>";
  if($hero['party'] == 0) { echo "<td><a href='javascript:join($row[partyid]);'>X</a></td>"; }
  echo"</tr>";
}
echo "</table>";

echo "<form name='leavefrm' id='leavefrm' method='POST' action='dungeons.php'><input name='leaveid' type='hidden' value='' id='leaveID'></form>";
echo "<form name='createfrm' id='createfrm' method='POST' action='dungeons.php'><input name='createid' type='hidden' value='' id='createID'></form>";
echo "<form name='joinfrm' id='joinfrm' method='POST' action='dungeons.php'><input name='joinid' type='hidden' value='' id='joinID'></form>";

mysqli_close($conn);
?>