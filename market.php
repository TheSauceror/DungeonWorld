<?php

include "checklogin.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

echo "Items for sale:<br>";

//$items = mysqli_query($conn,"SELECT * FROM Inventory WHERE market > 0");
$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid LEFT JOIN Hero ON Inventory.owner = Hero.id WHERE Inventory.market > 0");
echo "<table><tr><th>ID</th><th>Prefix</th><th>Base</th><th>Suffix</th><th>For Sale Until</th><th>Slot</th><th>S. Dmg</th><th>P. Dmg</th><th>B. Dmg</th><th>S. Arm</th><th>P. Arm</th><th>B. Arm</th><th>Description</th><th>Price</th><th>Buy</th></tr>";

while($row = mysqli_fetch_assoc($items)) {
  echo "<tr><td>" . $row['inventoryid'] . "</td><td>" . $row['prefixname'] . "</td><td>" . $row['basename'] . "</td><td>" . $row['suffixname'] . "</td><td>" . date("m-d-y H:i:s", $row['market']) . "</td><td>" . $row['slot'] . "</td><td>" . getItemStats($row['inventoryid'], "sdam") . "</td><td>" . getItemStats($row['inventoryid'], "pdam") . "</td><td>" . getItemStats($row['inventoryid'], "bdam") . "</td><td>" . getItemStats($row['inventoryid'], "sarm") . "</td><td>" . getItemStats($row['inventoryid'], "parm") . "</td><td>" . getItemStats($row['inventoryid'], "barm") . "</td><td>" . $row['basedes'] . " " . $row['prefixdes'] . " " . $row['suffixdes'] . "</td><td>" . getItemPrice($row['inventoryid']) . "</td><td><a href=''>Buy</a></td></tr>";
}
echo "</table>";

mysqli_close($conn);

function getItemPrice($itemid) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  mysqli_close($conn);
  return max(1, ($item['prefixvalue'] * $item['prefixlevel']) + ($item['basevalue'] * $item['baselevel']) + ($item['suffixvalue'] * $item['suffixlevel']));
}

function getItemStats($itemid, $stat) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  mysqli_close($conn);
  return max(0, ($item["prefix".$stat] * $item['prefixlevel']) + ($item["base".$stat] * $item['baselevel']) + ($item["suffix".$stat] * $item['suffixlevel']));
}

?>