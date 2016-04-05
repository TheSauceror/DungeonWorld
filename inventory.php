<head><title>Adventures Of Eld - Inventory</title></head>

<script>
function sellItem(sellitemid) {
  document.getElementById('sellitemid').value = sellitemid;
  document.getElementById('sellitemfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(strpos($hero['tutorial'], 'inventoryintro') === false) {
  echo "<div class='alert'>Here's where you'll find your hoard of <span class='red'>items</span> you pick up from adventuring. If you don't want them you can sell them to the <a href='market.php'><span class='red'>Market</span></a>, or equip them on your <a href='profile.php'><span class='red'>Profile</span></a>.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'inventoryintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

if(isset($_POST['sellitemid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
  $sellitemid = mysqli_real_escape_string($conn, $_POST['sellitemid']);
	$sellitemprice = getItemPrice($sellitemid, .4);
	$markettime = time() + (3 * 24 * 60 * 60);
	mysqli_query($conn, "UPDATE Inventory SET equip = 0, time = $markettime WHERE inventoryid = '$_POST[sellitemid]'");
	mysqli_query($conn, "UPDATE Hero SET gold = gold + $sellitemprice WHERE id = '$cookie[0]'");
}

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = $cookie[0] AND Inventory.time = '0' AND Inventory.equip = '0' ORDER BY inventoryid DESC");

$itemlist = [];
while($row = mysqli_fetch_assoc($items)) {
  $row['value'] = getItemPrice($row['inventoryid'], .4);
  $row['sdam'] = getItemStats($row['inventoryid'], "sdam");
  $row['pdam'] = getItemStats($row['inventoryid'], "pdam");
  $row['bdam'] = getItemStats($row['inventoryid'], "bdam");
  $row['adam'] = getItemStats($row['inventoryid'], "adam");
  $row['ddam'] = getItemStats($row['inventoryid'], "ddam");
  $row['sarm'] = getItemStats($row['inventoryid'], "sarm");
  $row['parm'] = getItemStats($row['inventoryid'], "parm");
  $row['barm'] = getItemStats($row['inventoryid'], "barm");
  $row['aarm'] = getItemStats($row['inventoryid'], "aarm");
  $row['darm'] = getItemStats($row['inventoryid'], "darm");
  $row['hpreg'] = getItemStats($row['inventoryid'], "hpreg");
  $row['mpreg'] = getItemStats($row['inventoryid'], "mpreg");
  $row['itemdes'] = getItemDes($row['inventoryid'], 0, 0, 0);
  $itemlist[] = $row;
}

if(isset($_GET['order'])) {
  if($_GET['order'] == "des") {
    usort($itemlist, 'sort_down');
  } else {
    usort($itemlist, 'sort_up');
  }
}

echo "<div class='parchment'><h3>Inventory:</h3>";
//echo "<table><tr><th>Prefix<br><a href='?sort=prefixname&order=up'>&#8593;</a> <a href='?sort=prefixname&order=down'>&#8595;</a></th><th>Base<br><a href='?sort=basename&order=up'>&#8593;</a> <a href='?sort=basename&order=down'>&#8595;</a></th><th>Suffix<br><a href='?sort=suffixname&order=up'>&#8593;</a> <a href='?sort=suffixname&order=down'>&#8595;</a></th><th>Slot<br><a href='?sort=slot&order=up'>&#8593;</a> <a href='?sort=slot&order=down'>&#8595;</a></th><th>S. Dmg<br><a href='?sort=sdam&order=up'>&#8593;</a> <a href='?sort=sdam&order=down'>&#8595;</a></th><th>P. Dmg<br><a href='?sort=pdam&order=up'>&#8593;</a> <a href='?sort=pdam&order=down'>&#8595;</a></th><th>B. Dmg<br><a href='?sort=bdam&order=up'>&#8593;</a> <a href='?sort=bdam&order=down'>&#8595;</a></th><th>S. Arm<br><a href='?sort=sarm&order=up'>&#8593;</a> <a href='?sort=sarm&order=down'>&#8595;</a></th><th>P. Arm<br><a href='?sort=parm&order=up'>&#8593;</a> <a href='?sort=parm&order=down'>&#8595;</a></th><th>B. Arm<br><a href='?sort=barm&order=up'>&#8593;</a> <a href='?sort=barm&order=down'>&#8595;</a></th><th>HP Regen<br><a href='?sort=hpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>MP Regen<br><a href='?sort=mpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>Description</th><th>Sell Item<br><a href='?sort=price&order=up'>&#8593;</a> <a href='?sort=price&order=down'>&#8595;</a></th></tr>";
echo "<form name='sortfrm' id='sortfrm' method='GET' action='inventory.php'>Sort by: <select name='sort' id='sort'><option value='value'>Value</option><option value='slot'>Slot</option><option value='prefixname'>Prefix Name</option><option value='basename'>Base Name</option><option value='suffixname'>Suffix Name</option><option value='sdam'>Slashing Power</option><option value='pdam'>Piercing Power</option><option value='bdam'>Bludgeoning Power</option><option value='adam'>Arcane Power</option><option value='ddam'>Divine Power</option><option value='sarm'>Slashing Defense</option><option value='parm'>Piercing Defense</option><option value='barm'>Bludgeoning Defense</option><option value='aarm'>Arcane Defense</option><option value='darm'>Divine Defense</option><option value='hpreg'>HP Regen</option><option value='mpreg'>MP Regen</option></select><select name='order' id='order'><option value='des'>Descending</option><option value='asc'>Ascending</option></select><input type='submit' value='Sort'></form>";
echo "<table><tr><th>Prefix</th><th>Base</th><th>Suffix</th><th>Slot</th><th>Description</th><th>Sell Item</th></tr>";
foreach($itemlist as $item => $row) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . $row['slot'] . "</td><td>" . $row['itemdes'] . "</td><td><a href='javascript:sellItem($row[inventoryid]);'>" . $row['value'] . " gold</td></tr>";
}
echo "</table><div>";

mysqli_close($conn);

echo "<form name='sellitemfrm' id='sellitemfrm' method='POST' action='inventory.php'><input name='sellitemid' type='hidden' value='' id='sellitemid'></form>";

?>