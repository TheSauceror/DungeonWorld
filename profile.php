<script>
function updateAttribute(aID) {
  document.getElementById('attributeID').value = aID;
  document.getElementById('attributefrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

//echo "<form name='herosearch' action='profile.php' method='GET'><input type='text' name='id'><input type='submit' value='Submit'></form>";

$id = $cookie[0];

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_POST['mainhand'])) {
  //protect these inputs  from injection
  //protect other people items from f12ing!!!!!!!!!!!!!!!!
  mysqli_query($conn, "UPDATE Inventory SET equip = 0 WHERE owner = '$id'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[mainhand]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 2 WHERE inventoryid = '$_POST[offhand]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[head]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[torso]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[arms]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[legs]'");
  mysqli_query($conn, "UPDATE Inventory SET equip = 1 WHERE inventoryid = '$_POST[feet]'");
}

if(isset($_GET['id'])) {
  $id = mysqli_real_escape_string($conn, $_GET['id']);
}

$hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero LEFT JOIN Guilds ON Hero.guild = Guilds.guildid WHERE Hero.id = '$id'"));

if(strpos($hero['tutorial'], 'profileintro') === false) {
  echo "<div class='alert'>Welcome, hero! A life of adventuring is one fraught with danger, but also a rewarding one! As long as you can survive, that is...<br>Your character stats are listed here on your <span class='red'>Profile</span>. You can train them, but nothing is free. Luckily for you, gold is easy to come by nowadays. I see you're already equipped for battle. You can also <span class='red'>change your gear</span> on this page, once you get some better items. Before you set off, you'd best plan a <a href='battleplan.php'><span class='red'>strategy</span></a>.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'profileintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

echo "<div class='parchment center'><h1>$hero[name] the $hero[race] $hero[prof]";
if($hero['guild'] != 0) { echo " of <a href='guild.php?id=$hero[guild]'>$hero[guildname]</a>"; }
echo "</div>";

if(isset($_POST['attribute'])) {
  $aID = mysqli_real_escape_string($conn, $_POST['attribute']);
  $cost = $hero[$aID] * 100;
  if($hero['gold'] < $cost) { echo "Not enough gold"; } else {
    mysqli_query($conn,"UPDATE Hero SET `$aID` = ( `$aID` + 1 ) WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
    mysqli_query($conn,"UPDATE Hero SET gold = gold - $cost WHERE id = '$cookie[0]'") or die(mysqli_error($conn));

    calculateHPMPInit($cookie[0]);

    $hero['gold'] -= $cost;
    $hero[$aID]++;
  }
}

if($id != $cookie[0]) {
  echo "<div class='parchment center'><a href='sendmessage.php?to=$id' target='_blank'><h3>Send a message</h3></a></div>";
}

echo "<div class='parchment left'>";
if($id == $cookie[0]) { echo "<h3>Available gold: $hero[gold]</h3><hr>"; }
echo "<h3>Attributes:</h3>";
echo "<table><tr><th>Attribute</th><th>Level</th>";
if($id == $cookie[0]) { echo "<th>Training Cost</th></tr>"; }
echo "<tr><td>Strength <sub class='help' title='Increases melee damage and health'>?</sub></td><td>$hero[str]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"str\");'>" . $hero['str']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Vitality <sub class='help' title='Greatly increases health'>?</sub></td><td>$hero[vit]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"vit\");'>" . $hero['vit']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Dexterity <sub class='help' title='Increases ranged damage and initiative'>?</sub></td><td>$hero[dex]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"dex\");'>" . $hero['dex']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Intelligence <sub class='help' title='Increases spell damage, MP, and initiative'>?</sub></td><td>$hero[nce]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"nce\");'>" . $hero['nce']*100 . " gold</a>"; }
echo "</td></tr>";
echo "<tr><td>Piety <sub class='help' title='Increases healing power. Greatly increases MP'>?</sub></td><td>$hero[pie]</td>";
if($id == $cookie[0]) { echo "<td><a href='javascript:updateAttribute(\"pie\");'>" . $hero['pie']*100 . " gold</a>"; }
echo "</td></tr>";
echo "</table>";
echo "</div>";

echo "<div class='parchment left'><h3>Stats:</h3>";
echo "HP: " . $hero['maxhp'];
echo "<br>";
echo "MP: " . $hero['maxmp'];
echo "<br>";
echo "Initiative: " . $hero['initiative'];
echo "<br><br>";
echo "HP regen: " . getAllItemStats($id, "hpreg");
echo "<br>";
echo "MP regen: " . getAllItemStats($id, "mpreg");
echo "<br><br>";

//echo ": " . array_sum(array_map(function($temp){return $temp[];},$gear)) . "<br>";
echo "Slashing power: " . getAllItemStats($id, "sdam") . "<br>";
echo "Piercing power: " . getAllItemStats($id, "pdam") . "<br>";
echo "Bludgeoning power: " . getAllItemStats($id, "bdam") . "<br>";
echo "Arcane power: " . getAllItemStats($id, "adam") . "<br>";
echo "Divine power: " . getAllItemStats($id, "ddam") . "<br>";
echo "<br>";
echo "Slashing defense: " . getAllItemStats($id, "sarm") . "<br>";
echo "Piercing defense: " . getAllItemStats($id, "parm") . "<br>";
echo "Bludgeoning defense: " . getAllItemStats($id, "barm") . "<br>";
echo "Arcane defense: " . getAllItemStats($id, "aarm") . "<br>";
echo "Divine defense: " . getAllItemStats($id, "darm") . "<br>";
echo "</div>";

echo "<div class='parchment left'><h3>Items:</h3>Dungeon Level: $hero[dungeonlevel] <sub class='help' title='You can only equip items of your highest completed dungeon level or lower'>?</sub>";
if($id == $cookie[0]) {
  echo "<form name='equipfrm' id='equipfrm' method='POST' action='profile.php'>";
  echo "<table>";
  echo "<tr><td>Main Hand:</td><td><select name='mainhand' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Hand', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Hand', 1) . "</td></tr>";
  echo "<tr><td>Off Hand:</td><td><select name='offhand' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Hand', 2); echo "</select></td><td>" . getItemDes(0, $id, 'Hand', 2) . "</td></tr>";
  echo "<tr><td>Head:</td><td><select name='head' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Head', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Head', 1) . "</td></tr>";
  echo "<tr><td>Torso:</td><td><select name='torso' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Torso', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Torso', 1) . "</td></tr>";
  echo "<tr><td>Arms:</td><td><select name='arms' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Arms', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Arms', 1) . "</td></tr>";
  echo "<tr><td>Legs:</td><td><select name='legs' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Legs', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Legs', 1) . "</td></tr>";
  echo "<tr><td>Feet:</td><td><select name='feet' onChange='this.form.submit();'>"; getAllSlot($id, $hero['dungeonlevel'], 'Feet', 1); echo "</select></td><td>" . getItemDes(0, $id, 'Feet', 1) . "</td></tr>";
  echo "</table>";
  echo "</form>";
} else {
  echo "<br><table>";
  echo "<tr><td>Main Hand:</td><td>" . getItemName($id, 'Hand', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Hand', 1) . "</td></tr>";
  echo "<tr><td>Off Hand:</td><td>" . getItemName($id, 'Hand', 2, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Hand', 2) . "</td></tr>";
  echo "<tr><td>Head:</td><td>" . getItemName($id, 'Head', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Head', 1) . "</td></tr>";
  echo "<tr><td>Torso:</td><td>" . getItemName($id, 'Torso', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Torso', 1) . "</td></tr>";
  echo "<tr><td>Arms:</td><td>" . getItemName($id, 'Arms', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Arms', 1) . "</td></tr>";
  echo "<tr><td>Legs:</td><td>" . getItemName($id, 'Legs', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Legs', 1) . "</td></tr>";
  echo "<tr><td>Feet:</td><td>" . getItemName($id, 'Feet', 1, 0, 0, 0, 0, 0, 0) . "</td><td>" . getItemDes(0, $id, 'Feet', 1) . "</td></tr>";
}
echo "</table></div>";

echo "<form name='attributefrm' id='attributefrm' method='POST' action='profile.php'><input name='attribute' type='hidden' value='' id='attributeID'></form>";

mysqli_close($conn);

function getAllSlot($hero, $level, $slot, $equip) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd"); 
  $slotitems = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot = '$slot' AND Inventory.time = '0' AND Inventory.baselevel <= '$level'");
  echo "<option value=''>----------</option>";
  while($row = mysqli_fetch_assoc($slotitems)) {
    if($row['equip'] != $equip && $row['equip'] != 0) { continue; }
    echo "<option value='" . $row['inventoryid'] . "'";
    if($row['equip'] == $equip) { echo " selected"; }
    echo ">";
    if($row['prefixlevel'] > 0) { echo $row['prefixname'] . "(" . $row['prefixlevel'] . ") "; }
    echo $row['basename'] . "(" . $row['baselevel'] . ")";
    if($row['suffixlevel'] > 0) { echo " " . $row['suffixname'] . "(" . $row['suffixlevel'] . ")"; }
    echo "</option>";
  }
  mysqli_close($conn);
  return;
}

?>