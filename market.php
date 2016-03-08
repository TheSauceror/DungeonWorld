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
	$buyitemprice = getItemPrice($_POST['buyitemid'], 0);
	mysqli_query($conn, "UPDATE Inventory SET equip = 0, market = 0, owner = $cookie[0] WHERE inventoryid = '$_POST[buyitemid]' AND market > 0");
	mysqli_query($conn, "UPDATE Hero SET gold = gold - $buyitemprice WHERE id = '$cookie[0]'");
}

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid LEFT JOIN Hero ON Inventory.owner = Hero.id WHERE Inventory.market > 0 ORDER BY market ASC");

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

function sort_up($a, $b) { //move these to functions? if used for sorting elsewhere
  return $a[$_GET['sort']] - $b[$_GET['sort']];
}

function sort_down($a, $b) { //move these to functions? if used for sorting elsewhere
  return $b[$_GET['sort']] - $a[$_GET['sort']];
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

/*$sort = "market ASC";

if(isset($_GET['sort'])) {
  switch($_GET['sort']) {
    case "timeup":
      $sort = "market ASC";
      break;
    case "timedown":
      $sort = "market DESC";
      break;
    case "slotup":
      $sort = "slot ASC";
      break;
    case "slotdown":
      $sort = "slot DESC";
      break;
    case "sdamup":
      $sort = "sdam ASC";
      break;
    case "sdamdown":
      $sort = "sdam DESC";
      break;
    case "pdamup":
      $sort = "pdam ASC";
      break;
    case "pdamdown":
      $sort = "pdam DESC";
      break;
    case "bdamup":
      $sort = "bdam ASC";
      break;
    case "bdamdown":
      $sort = "bdam DESC";
      break;
    case "sarmup":
      $sort = "sarm ASC";
      break;
    case "sarmdown":
      $sort = "sarm DESC";
      break;
    case "parmup":
      $sort = "parm ASC";
      break;
    case "parmdown":
      $sort = "parm DESC";
      break;
    case "barmup":
      $sort = "barm ASC";
      break;
    case "barmdown":
      $sort = "barm DESC";
      break;
    case "hpregup":
      $sort = "hpreg ASC";
      break;
    case "hpregdown":
      $sort = "hpreg DESC";
      break;
    case "mpregup":
      $sort = "mpreg ASC";
      break;
    case "mpregdown":
      $sort = "mpreg DESC";
      break;
    case "priceup":
      $sort = "price ASC";
      break;
    case "pricedown":
      $sort = "price DESC";
      break;
  }
}

echo "Items for sale:<br>";

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid LEFT JOIN Hero ON Inventory.owner = Hero.id WHERE Inventory.market > 0 ORDER BY " . $sort);

echo "<table><tr><th>Prefix</th><th>Base</th><th>Suffix</th><th><a href='?sort=timeup'>&#8593;</a> For Sale Until <a href='?sort=timedown'>&#8595;</a></th><th><a href='?sort=slotup'>&#8593;</a> Slot <a href='?sort=slotdown'>&#8595;</a></th><th><a href='?sort=sdamup'>&#8593;</a> S. Dmg <a href='?sort=sdamdown'>&#8595;</a></th><th><a href='?sort=pdamup'>&#8593;</a> P. Dmg <a href='?sort=pdamdown'>&#8595;</a></th><th><a href='?sort=bdamup'>&#8593;</a> B. Dmg <a href='?sort=bdamup'>&#8595;</a></th><th><a href='?sort=sarmup'>&#8593;</a> S. Arm <a href='?sort=sarmdown'>&#8595;</a></th><th><a href='?sort=parmup'>&#8593;</a> P. Arm <a href='?sort=parmdown'>&#8595;</a></th><th><a href='?sort=barmup'>&#8593;</a> B. Arm <a href='?sort=barmdown'>&#8595;</a></th><th><a href='?sort=hpregup'>&#8593;</a> HP Regen <a href='?sort=hpregdown'>&#8595;</a></th><th><a href='?sort=mpregup'>&#8593;</a> MP Regen <a href='?sort=hpregdown'>&#8595;</a></th><th>Description</th><th><a href='?sort=priceup'>&#8593;</a> Price <a href='?sort=pricedown'>&#8595;</a></th><th>Buy</th></tr>";

while($row = mysqli_fetch_assoc($items)) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . date("m-d-y H:i:s", $row['market']) . "</td><td>" . $row['slot'] . "</td><td>" . getItemStats($row['inventoryid'], "sdam") . "</td><td>" . getItemStats($row['inventoryid'], "pdam") . "</td><td>" . getItemStats($row['inventoryid'], "bdam") . "</td><td>" . getItemStats($row['inventoryid'], "sarm") . "</td><td>" . getItemStats($row['inventoryid'], "parm") . "</td><td>" . getItemStats($row['inventoryid'], "barm") . "</td><td>" . getItemStats($row['inventoryid'], "hpreg") . "</td><td>" . getItemStats($row['inventoryid'], "mpreg") . "</td><td>" . getItemDes($row['inventoryid'], 0, 0, 0) . "</td><td>" . getItemPrice($row['inventoryid'], 0) . "</td><td><a href='javascript:buyItem($row[inventoryid]);'>Buy</a></td></tr>";
}
echo "</table>";
*/

echo "Items for sale:<br>";

echo "<table><tr><th>Prefix</th><th>Base</th><th>Suffix</th><th>For Sale Until<br><a href='?sort=market&order=up'>&#8593;</a> <a href='?sort=market&order=down'>&#8595;</a></th><th>Slot<br><a href='?sort=slot&order=up'>&#8593;</a> <a href='?sort=slot&order=down'>&#8595;</a></th><th>S. Dmg<br><a href='?sort=sdam&order=up'>&#8593;</a> <a href='?sort=sdam&order=down'>&#8595;</a></th><th>P. Dmg<br><a href='?sort=pdam&order=up'>&#8593;</a> <a href='?sort=pdam&order=down'>&#8595;</a></th><th>B. Dmg<br><a href='?sort=bdam&order=up'>&#8593;</a> <a href='?sort=bdam&order=down'>&#8595;</a></th><th>S. Arm<br><a href='?sort=sarm&order=up'>&#8593;</a> <a href='?sort=sarm&order=down'>&#8595;</a></th><th>P. Arm<br><a href='?sort=parm&order=up'>&#8593;</a> <a href='?sort=parm&order=down'>&#8595;</a></th><th>B. Arm<br><a href='?sort=barm&order=up'>&#8593;</a> <a href='?sort=barm&order=down'>&#8595;</a></th><th>HP Regen<br><a href='?sort=hpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>MP Regen<br><a href='?sort=mpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>Description</th><th>Price<br><a href='?sort=price&order=up'>&#8593;</a> <a href='?sort=price&order=down'>&#8595;</a></th><th>Buy</th></tr>";

foreach($itemlist as $item => $row) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . date("m-d-y H:i:s", $row['market']) . "</td><td>" . $row['slot'] . "</td><td>" . $row['sdam'] . "</td><td>" . $row['pdam'] . "</td><td>" . $row['bdam'] . "</td><td>" . $row['sarm'] . "</td><td>" . $row['parm'] . "</td><td>" . $row['barm'] . "</td><td>" . $row['hpreg'] . "</td><td>" . $row['mpreg'] . "</td><td>" . $row['itemdes'] . "</td><td>" . $row['price'] . "</td><td><a href='javascript:buyItem($row[inventoryid]);'>Buy</a></td></tr>";
}
echo "</table>";

mysqli_close($conn);

echo "<form name='buyitemfrm' id='buyitemfrm' method='POST' action='market.php'><input name='buyitemid' type='hidden' value='' id='buyitemid'></form>";

?>