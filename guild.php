<script>
function accept(acceptid) {
  document.getElementById('acceptid').value = acceptid;
  document.getElementById('acceptfrm').submit();
}
function deny(denyid) {
  document.getElementById('denyid').value = denyid;
  document.getElementById('denyfrm').submit();
}
function kick(kickid) {
  document.getElementById('kickid').value = kickid;
  document.getElementById('kickfrm').submit();
}
function give(giveid) {
  document.getElementById('giveid').value = giveid;
  document.getElementById('givefrm').submit();
}
function leave(leaveid) {
  document.getElementById('leaveid').value = leaveid;
  document.getElementById('leavefrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

$id = $hero['guild'];

if(isset($_GET['id'])) {
  $id = mysqli_real_escape_string($conn, $_GET['id']);
}

if(isset($_POST['guildname']) && $hero['guild'] == 0) {
  $name = mysqli_real_escape_string($conn, $_POST['guildname']);
  mysqli_query($conn,"INSERT INTO Guilds (guildname, owner) VALUES ('$name', '$hero[id]')") or die(mysqli_error($conn));
  $guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE owner = '$hero[id]'")) or die(mysqli_error($conn));
  mysqli_query($conn, "UPDATE Hero SET guild = $guild[guildid] WHERE id = '$hero[id]'");
  $hero['guild'] = $guild['guildid'];
  echo "You have created $guild[guildname]!";
}

if($id == 0) {
	header('Location: guilds.php');
}

$guild = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Guilds WHERE guildid = '$id'"));

if(isset($_POST['acceptid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
	$acceptid = mysqli_real_escape_string($conn, $_POST['acceptid']);
	mysqli_query($conn, "DELETE FROM GuildApplicants WHERE heroid = '$acceptid'");
	mysqli_query($conn, "UPDATE Hero SET guild = $guild[guildid] WHERE id = '$acceptid'");
  $accepthero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$acceptid'")) or die(mysqli_error($conn));
	echo "$accepthero[name] has joined your guild!";
}

if(isset($_POST['denyid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
	$denyid = mysqli_real_escape_string($conn, $_POST['denyid']);
	mysqli_query($conn, "DELETE FROM GuildApplicants WHERE heroid = '$denyid' AND guildid = $guild[guildid]");
  $denyhero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$denyid'")) or die(mysqli_error($conn));
	echo "$denyhero[name] has been rejected!";
}

if(isset($_POST['kickid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $kickid = mysqli_real_escape_string($conn, $_POST['kickid']);
  mysqli_query($conn, "UPDATE Hero SET guild = 0 WHERE id = '$kickid'");
  $kickhero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$kickid'")) or die(mysqli_error($conn));
  echo "$kickhero[name] has been kicked!";
}

if(isset($_POST['giveid'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $giveid = mysqli_real_escape_string($conn, $_POST['giveid']);
  mysqli_query($conn, "UPDATE Guilds SET owner = $giveid WHERE guildid = '$hero[guild]'");
  $guild['owner'] = $giveid;
  $givehero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$giveid'")) or die(mysqli_error($conn));
  echo "Ownership has been given to $givehero[name]!";
}

if(isset($_POST['guilddes'])) {
  //protect these inputs from injection
  //protect other people items from f12ing
  $guilddes = mysqli_real_escape_string($conn, $_POST['guilddes']);
  echo $guilddes;
  mysqli_query($conn, "UPDATE Guilds SET guilddes = '$guilddes' WHERE guildid = '$hero[guild]'");
  $guild['guilddes'] = $guilddes;
  echo $guild['guilddes'];
  echo "The guild description has been changed!";
}

$members = mysqli_query($conn,"SELECT * FROM Hero WHERE guild = '$id'");
$applicants = mysqli_query($conn,"SELECT * FROM GuildApplicants LEFT JOIN Hero ON GuildApplicants.heroid = Hero.id WHERE guildid = '$id'");

echo "<h1>$guild[guildname]</h1>";

if($guild['owner'] == $hero['id']) {
  echo "<form action='guild.php' method='post'><fieldset class='parchment'><legend>Edit guild description</legend>Description: <input type='text' name='guilddes' required value='$guild[guilddes]'><input type='submit' value='Save'></fieldset></form>";
} else {
  echo "<h3>$guild[guilddes]</h3>";
}

echo "<table class='parchment'><tr><th>Name</th><th>Race</th><th>Profession</th><th>Party</th>";
if($guild['owner'] == $hero['id']) { echo "<th>Member Options</th>"; }
echo "</tr>";
while($row = mysqli_fetch_assoc($members)) {
  echo "<tr><td><a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "<td></td></td>";
	if($guild['owner'] == $hero['id'] && $row['id'] != $hero['id']) { echo "<td><a href='javascript:kick($row[id]);'>Kick</a> / <a href='javascript:give($row[id]);'>Give ownership</a></td>"; }
  echo "</tr>";
}
echo "</table>";

if($guild['owner'] != $hero['id'] && $guild['guildid'] == $hero['guild']) { echo "<h3><a href='javascript:leave($hero[id]);'>Leave Guild</a></h3>"; }

echo "<h3>Applicants</h3>";
echo "<table class='parchment'><tr><th>Name</th><th>Race</th><th>Profession</th>";
if($guild['owner'] == $hero['id']) { echo "<th colspan='2'>Respond</th>"; }
echo "</tr>";
while($row = mysqli_fetch_assoc($applicants)) {
  echo "<tr><td><a href='profile.php?id=" . $row['id'] . "'>" . $row['name'] . "</a></td><td>" . $row['race'] . "</td><td>" . $row['prof'] . "</td>";
	if($guild['owner'] == $hero['id']) { echo "<td><a href='javascript:accept($row[id]);'>Accept</a></td><td><a href='javascript:deny($row[id]);'>Deny</a></td>"; }
  echo "</tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='acceptfrm' id='acceptfrm' method='POST' action='guild.php'><input name='acceptid' type='hidden' value='' id='acceptid'></form>";
echo "<form name='denyfrm' id='denyfrm' method='POST' action='guild.php'><input name='denyid' type='hidden' value='' id='denyid'></form>";
echo "<form name='kickfrm' id='kickfrm' method='POST' action='guild.php'><input name='kickid' type='hidden' value='' id='kickid'></form>";
echo "<form name='givefrm' id='givefrm' method='POST' action='guild.php'><input name='giveid' type='hidden' value='' id='giveid'></form>";
echo "<form name='leavefrm' id='leavefrm' method='POST' action='guild.php'><input name='leaveid' type='hidden' value='' id='leaveid'></form>";

?>