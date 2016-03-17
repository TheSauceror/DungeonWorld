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

if(isset($_POST['sellitemid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
  $sellitemid = mysqli_real_escape_string($conn, $_POST['sellitemid']);
	$sellitemprice = getItemPrice($sellitemid, .4);
	$markettime = time() + (3 * 24 * 60 * 60);
	mysqli_query($conn, "UPDATE Inventory SET equip = 0, market = $markettime WHERE inventoryid = '$_POST[sellitemid]'");
	mysqli_query($conn, "UPDATE Hero SET gold = gold + $sellitemprice WHERE id = '$cookie[0]'");
}

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = $cookie[0] AND Inventory.market = '0' AND Inventory.equip = '0' ORDER BY inventoryid DESC");

/*$itemlist = [];
while($row = mysqli_fetch_assoc($items)) {
  $itemlist[] = $row;
}

$sort = "";
$order = "up";

if(isset($_GET['order'])) {
  if($_GET['order'] == "down") {
    usort($itemlist, 'sort_down');
  } else {
    usort($itemlist, 'sort_up');
  }
}*/

echo "Inventory:<br>";

echo "<table class='parchment'><tr><th>Prefix<br><a href='?sort=prefixname&order=up'>&#8593;</a> <a href='?sort=prefixname&order=down'>&#8595;</a></th><th>Base<br><a href='?sort=basename&order=up'>&#8593;</a> <a href='?sort=basename&order=down'>&#8595;</a></th><th>Suffix<br><a href='?sort=suffixname&order=up'>&#8593;</a> <a href='?sort=suffixname&order=down'>&#8595;</a></th><th>Slot<br><a href='?sort=slot&order=up'>&#8593;</a> <a href='?sort=slot&order=down'>&#8595;</a></th><th>S. Dmg<br><a href='?sort=sdam&order=up'>&#8593;</a> <a href='?sort=sdam&order=down'>&#8595;</a></th><th>P. Dmg<br><a href='?sort=pdam&order=up'>&#8593;</a> <a href='?sort=pdam&order=down'>&#8595;</a></th><th>B. Dmg<br><a href='?sort=bdam&order=up'>&#8593;</a> <a href='?sort=bdam&order=down'>&#8595;</a></th><th>S. Arm<br><a href='?sort=sarm&order=up'>&#8593;</a> <a href='?sort=sarm&order=down'>&#8595;</a></th><th>P. Arm<br><a href='?sort=parm&order=up'>&#8593;</a> <a href='?sort=parm&order=down'>&#8595;</a></th><th>B. Arm<br><a href='?sort=barm&order=up'>&#8593;</a> <a href='?sort=barm&order=down'>&#8595;</a></th><th>HP Regen<br><a href='?sort=hpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>MP Regen<br><a href='?sort=mpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>Description</th><th>Sell Item<br><a href='?sort=price&order=up'>&#8593;</a> <a href='?sort=price&order=down'>&#8595;</a></th></tr>";

while($row = mysqli_fetch_assoc($items)) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . $row['slot'] . "</td><td>" . getItemStats($row['inventoryid'], "sdam") . "</td><td>" . getItemStats($row['inventoryid'], "pdam") . "</td><td>" . getItemStats($row['inventoryid'], "bdam") . "</td><td>" . getItemStats($row['inventoryid'], "sarm") . "</td><td>" . getItemStats($row['inventoryid'], "parm") . "</td><td>" . getItemStats($row['inventoryid'], "barm") . "</td><td>" . getItemStats($row['inventoryid'], "hpreg") . "</td><td>" . getItemStats($row['inventoryid'], "mpreg") . "</td><td>" . getItemDes($row['inventoryid'], 0, 0, 0) . "</td><td><a href='javascript:sellItem($row[inventoryid]);'>" . getItemPrice($row['inventoryid'], .4) . " gold</td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='sellitemfrm' id='sellitemfrm' method='POST' action='inventory.php'><input name='sellitemid' type='hidden' value='' id='sellitemid'></form>";

?>