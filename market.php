<script>
function buyItem(buyitemid) {
  document.getElementById('buyitemid').value = buyitemid;
  document.getElementById('buyitemfrm').submit();
}
</script>

<style>
  a{
    //text-decoration: none;
  }
</style>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(isset($_POST['buyitemid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
  $buyitemid = mysqli_real_escape_string($conn, $_POST['buyitemid']);
	$buyitemprice = getItemPrice($buyitemid, 0);
  if($hero['gold'] < $buyitemprice) { echo "Not enough gold"; } else {
    mysqli_query($conn, "UPDATE Inventory SET equip = 0, market = 0, owner = $cookie[0] WHERE inventoryid = '$_POST[buyitemid]' AND market > 0");
    mysqli_query($conn, "UPDATE Hero SET gold = gold - $buyitemprice WHERE id = '$cookie[0]'");
  }
}

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid LEFT JOIN Hero ON Inventory.owner = Hero.id WHERE Inventory.market > 0 ORDER BY market ASC");

$itemlist = [];
while($row = mysqli_fetch_assoc($items)) {
  if($row['market'] <= time()) {
    mysqli_query($conn, "DELETE FROM Inventory WHERE inventoryid = '$row[inventoryid]' AND market > 0");
    continue;
  }
  $row['price'] = getItemPrice($row['inventoryid'], 0);
  $row['sdam'] = getItemStats($row['inventoryid'], "sdam");
  $row['pdam'] = getItemStats($row['inventoryid'], "pdam");
  $row['bdam'] = getItemStats($row['inventoryid'], "bdam");
  $row['sarm'] = getItemStats($row['inventoryid'], "sarm");
  $row['parm'] = getItemStats($row['inventoryid'], "parm");
  $row['barm'] = getItemStats($row['inventoryid'], "barm");
  $row['hpreg'] = getItemStats($row['inventoryid'], "hpreg");
  $row['mpreg'] = getItemStats($row['inventoryid'], "mpreg");
  $row['itemdes'] = getItemDes($row['inventoryid'], 0, 0, 0);
  $itemlist[] = $row;
}

$sort = "time";
$order = "up";

if(isset($_GET['order'])) {
  if($_GET['order'] == "down") {
    usort($itemlist, 'sort_down');
  } else {
    usort($itemlist, 'sort_up');
  }
}

echo "Items for sale:<br>";

echo "<table class='parchment'><tr><th>Prefix<br><a href='?sort=prefixname&order=up'>&#8593;</a> <a href='?sort=prefixname&order=down'>&#8595;</a></th><th>Base<br><a href='?sort=basename&order=up'>&#8593;</a> <a href='?sort=basename&order=down'>&#8595;</a></th><th>Suffix<br><a href='?sort=suffixname&order=up'>&#8593;</a> <a href='?sort=suffixname&order=down'>&#8595;</a></th><th>For Sale Until<br><a href='?sort=market&order=up'>&#8593;</a> <a href='?sort=market&order=down'>&#8595;</a></th><th>Slot<br><a href='?sort=slot&order=up'>&#8593;</a> <a href='?sort=slot&order=down'>&#8595;</a></th><th>S. Dmg<br><a href='?sort=sdam&order=up'>&#8593;</a> <a href='?sort=sdam&order=down'>&#8595;</a></th><th>P. Dmg<br><a href='?sort=pdam&order=up'>&#8593;</a> <a href='?sort=pdam&order=down'>&#8595;</a></th><th>B. Dmg<br><a href='?sort=bdam&order=up'>&#8593;</a> <a href='?sort=bdam&order=down'>&#8595;</a></th><th>S. Arm<br><a href='?sort=sarm&order=up'>&#8593;</a> <a href='?sort=sarm&order=down'>&#8595;</a></th><th>P. Arm<br><a href='?sort=parm&order=up'>&#8593;</a> <a href='?sort=parm&order=down'>&#8595;</a></th><th>B. Arm<br><a href='?sort=barm&order=up'>&#8593;</a> <a href='?sort=barm&order=down'>&#8595;</a></th><th>HP Regen<br><a href='?sort=hpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>MP Regen<br><a href='?sort=mpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>Description</th><th>Buy Item<br><a href='?sort=price&order=up'>&#8593;</a> <a href='?sort=price&order=down'>&#8595;</a></th></tr>";

foreach($itemlist as $item => $row) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . date("m-d-y H:i:s", $row['market']) . "</td><td>" . $row['slot'] . "</td><td>" . $row['sdam'] . "</td><td>" . $row['pdam'] . "</td><td>" . $row['bdam'] . "</td><td>" . $row['sarm'] . "</td><td>" . $row['parm'] . "</td><td>" . $row['barm'] . "</td><td>" . $row['hpreg'] . "</td><td>" . $row['mpreg'] . "</td><td>" . $row['itemdes'] . "</td><td><a href='javascript:buyItem($row[inventoryid]);'>" . $row['price'] . " gold</a></td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='buyitemfrm' id='buyitemfrm' method='POST' action='market.php'><input name='buyitemid' type='hidden' value='' id='buyitemid'></form>";

?>