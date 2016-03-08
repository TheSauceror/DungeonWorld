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
	$sellitemprice = getItemPrice($_POST['sellitemid']);
	$markettime = time() + (7 * 24 * 60 * 60);
	mysqli_query($conn, "UPDATE Inventory SET equip = 0, market = $markettime WHERE inventoryid = '$_POST[sellitemid]'");
	mysqli_query($conn, "UPDATE Hero SET gold = gold + $sellitemprice WHERE id = '$cookie[0]'");
}

echo "Inventory:<br>";

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = $cookie[0] AND Inventory.market = '0' AND Inventory.equip = '0'");
echo "<table><tr><th>ID</th><th>Prefix</th><th>Base</th><th>Suffix</th><th>Slot</th><th>S. Dmg</th><th>P. Dmg</th><th>B. Dmg</th><th>S. Arm</th><th>P. Arm</th><th>B. Arm</th><th>HP Regen</th><th>MP Regen</th><th>Description</th><th>Sell Item</th></tr>";

while($row = mysqli_fetch_assoc($items)) {
  echo "<tr><td>" . $row['inventoryid'] . "</td><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . $row['slot'] . "</td><td>" . getItemStats($row['inventoryid'], "sdam") . "</td><td>" . getItemStats($row['inventoryid'], "pdam") . "</td><td>" . getItemStats($row['inventoryid'], "bdam") . "</td><td>" . getItemStats($row['inventoryid'], "sarm") . "</td><td>" . getItemStats($row['inventoryid'], "parm") . "</td><td>" . getItemStats($row['inventoryid'], "barm") . "</td><td>" . getItemStats($row['inventoryid'], "hpreg") . "</td><td>" . getItemStats($row['inventoryid'], "mpreg") . "</td><td>" . getItemDes($row['inventoryid'], 0, 0, 0) . "</td><td><a href='javascript:sellItem($row[inventoryid]);'>" . getItemPrice($row['inventoryid'], .4) . " gold</td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='sellitemfrm' id='sellitemfrm' method='POST' action='inventory.php'><input name='sellitemid' type='hidden' value='' id='sellitemid'></form>";

?>