<?php
include "checklogin.php";
?>

<?php
ini_set("display_errors", 1);

if(isset($_POST['name'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $level = mysqli_real_escape_string($conn, $_POST['level']);
  $type = mysqli_real_escape_string($conn, $_POST['type']);
  $rooms = mysqli_real_escape_string($conn, $_POST['rooms']);
  $des = mysqli_real_escape_string($conn, $_POST['des']);

  mysqli_query($conn, "INSERT INTO Dungeons (dungeonname, dungeonlevel, dungeontype, rooms, des, loot) VALUES 
    ('$name', '$level', '$type', '$rooms', '$des', '')") or die (mysqli_error($conn));
  mysqli_close($conn);

  echo "Dungeon Added!";
}
?>

<h1>Add Dungeon</h1>
<form name='adddungeonfrm' id='dungeonfrm' method='POST' action='adddungeon.php'>
	<table>
	<tr><td>Name</td><td><input type='text' name='name' size='40' required></td></tr>
	<tr><td>Rooms (separate with '|')</td><td><input type='text' name='rooms' required></td></tr>
  <tr><td>Dungeon Level </td><td><input type='text' name='level' required></td></tr>
  <tr><td>Dungeon Type (solo, party, raid)</td><td><input type='text' name='type' required></td></tr>
	<tr><td>Description (separate with '|')</td><td><textarea name='des' cols='40' rows='6' required></textarea></td></tr>
	</table>
	<br>
	<input type='submit' value='Add Dungeon'>
</form>