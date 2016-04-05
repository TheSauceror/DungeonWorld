<head><title>Adventures Of Eld - Market</title></head>

<script>
function buyItem(buyitemid) {
  document.getElementById('buyitemid').value = buyitemid;
  document.getElementById('buyitemfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(strpos($hero['tutorial'], 'marketintro') === false) {
  echo "<div class='alert'>If you're looking for a place to spend your hard earned gold, then you've come to the right place! Here at the <span class='red'>Market</span>, you'll see stuff that other adventurers found and didn't want, for one reason or another. Well, one's trash is another's treasure, or so the saying goes. Look through your <a href='inventory.php'><span class='red'>Inventory</span></a> to sell your own items, or go to your <a href='profile.php'><span class='red'>Profile</span></a> to equip any new purchases.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'marketintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

if(isset($_POST['buyitemid'])) {
	//protect these inputs from injection
  //protect other people items from f12ing
  $buyitemid = mysqli_real_escape_string($conn, $_POST['buyitemid']);
	$buyitemprice = getItemPrice($buyitemid, 0);
  if($hero['gold'] < $buyitemprice) { echo "<div class='alert'>Not enough gold!</div>"; } else {
    mysqli_query($conn, "UPDATE Inventory SET equip = 0, time = 0, owner = $cookie[0] WHERE inventoryid = '$_POST[buyitemid]' AND time > 0");
    mysqli_query($conn, "UPDATE Hero SET gold = gold - $buyitemprice WHERE id = '$cookie[0]'");
  }
}

$items = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid LEFT JOIN Hero ON Inventory.owner = Hero.id WHERE Inventory.time > 0 ORDER BY time ASC");

$itemlist = [];
while($row = mysqli_fetch_assoc($items)) {
  if($row['time'] <= time()) {
    mysqli_query($conn, "DELETE FROM Inventory WHERE inventoryid = '$row[inventoryid]' AND time > 0");
    continue;
  }
  $row['price'] = getItemPrice($row['inventoryid'], 0);
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

/*echo "<div class='parchment'><h3>Items for sale:</h3>";
echo "<table><tr><th>Prefix<br><a href='?sort=prefixname&order=up'>&#8593;</a> <a href='?sort=prefixname&order=down'>&#8595;</a></th><th>Base<br><a href='?sort=basename&order=up'>&#8593;</a> <a href='?sort=basename&order=down'>&#8595;</a></th><th>Suffix<br><a href='?sort=suffixname&order=up'>&#8593;</a> <a href='?sort=suffixname&order=down'>&#8595;</a></th><th>For Sale Until<br><a href='?sort=time&order=up'>&#8593;</a> <a href='?sort=time&order=down'>&#8595;</a></th><th>Slot<br><a href='?sort=slot&order=up'>&#8593;</a> <a href='?sort=slot&order=down'>&#8595;</a></th><th>S. Dmg<br><a href='?sort=sdam&order=up'>&#8593;</a> <a href='?sort=sdam&order=down'>&#8595;</a></th><th>P. Dmg<br><a href='?sort=pdam&order=up'>&#8593;</a> <a href='?sort=pdam&order=down'>&#8595;</a></th><th>B. Dmg<br><a href='?sort=bdam&order=up'>&#8593;</a> <a href='?sort=bdam&order=down'>&#8595;</a></th><th>S. Arm<br><a href='?sort=sarm&order=up'>&#8593;</a> <a href='?sort=sarm&order=down'>&#8595;</a></th><th>P. Arm<br><a href='?sort=parm&order=up'>&#8593;</a> <a href='?sort=parm&order=down'>&#8595;</a></th><th>B. Arm<br><a href='?sort=barm&order=up'>&#8593;</a> <a href='?sort=barm&order=down'>&#8595;</a></th><th>HP Regen<br><a href='?sort=hpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>MP Regen<br><a href='?sort=mpreg&order=up'>&#8593;</a> <a href='?sort=hpreg&order=down'>&#8595;</a></th><th>Description</th><th>Buy Item<br><a href='?sort=price&order=up'>&#8593;</a> <a href='?sort=price&order=down'>&#8595;</a></th></tr>";
foreach($itemlist as $item => $row) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . date("m-d-y H:i:s", $row['time']) . "</td><td>" . $row['slot'] . "</td><td>" . $row['sdam'] . "</td><td>" . $row['pdam'] . "</td><td>" . $row['bdam'] . "</td><td>" . $row['sarm'] . "</td><td>" . $row['parm'] . "</td><td>" . $row['barm'] . "</td><td>" . $row['hpreg'] . "</td><td>" . $row['mpreg'] . "</td><td>" . $row['itemdes'] . "</td><td><a href='javascript:buyItem($row[inventoryid]);'>" . $row['price'] . " gold</a></td></tr>";
}
echo "</table>";
echo "</div>";*/

echo "<div class='parchment'><h3>Items for sale:</h3>";
echo "<form name='sortfrm' id='sortfrm' method='GET' action='market.php'>Sort by: <select name='sort' id='sort'><option value='time'>Time</option><option value='price'>Price</option><option value='slot'>Slot</option><option value='prefixname'>Prefix Name</option><option value='basename'>Base Name</option><option value='suffixname'>Suffix Name</option><option value='sdam'>Slashing Power</option><option value='pdam'>Piercing Power</option><option value='bdam'>Bludgeoning Power</option><option value='adam'>Arcane Power</option><option value='ddam'>Divine Power</option><option value='sarm'>Slashing Defense</option><option value='parm'>Piercing Defense</option><option value='barm'>Bludgeoning Defense</option><option value='aarm'>Arcane Defense</option><option value='darm'>Divine Defense</option><option value='hpreg'>HP Regen</option><option value='mpreg'>MP Regen</option></select><select name='order' id='order'><option value='des'>Descending</option><option value='asc'>Ascending</option></select><input type='submit' value='Sort'></form>";
echo "<table><tr><th>Prefix</th><th>Base</th><th>Suffix</th><th>For Sale Until</th><th>Slot</th><th>Description</th><th>Buy Item</th></tr>";
foreach($itemlist as $item => $row) {
  echo "<tr><td>" . $row['prefixname'];
  if($row['prefixlevel'] > 0) { echo "(" . $row['prefixlevel'] . ")"; }
  echo "</td><td>" . $row['basename'] . "(" . $row['baselevel'] . ")</td><td>" . $row['suffixname'];
  if($row['suffixlevel'] > 0) { echo "(" . $row['suffixlevel'] . ")"; }
  echo "</td><td>" . date("m-d-y H:i:s", $row['time']) . "</td><td>" . $row['slot'] . "</td><td>" . $row['itemdes'] . "</td><td><a href='javascript:buyItem($row[inventoryid]);'>" . $row['price'] . " gold</a></td></tr>";
}
echo "</table>";
echo "</div>";

mysqli_close($conn);

echo "<form name='buyitemfrm' id='buyitemfrm' method='POST' action='market.php'><input name='buyitemid' type='hidden' value='' id='buyitemid'></form>";

?>