<?php

include "checklogin.php";

function giveItem($pre, $base, $suf, $itemowner, $isequipped) {

  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $pre=mysqli_real_escape_string($conn, "$pre");
  $base=mysqli_real_escape_string($conn, "$base");
  $suf=mysqli_real_escape_string($conn, "$suf");
  $itempre = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$pre' AND slot = 'prefix'"));
  $itembase = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$base' AND slot != 'prefix' AND slot != 'suffix'"));
  $itemsuf = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$suf' AND slot = 'suffix'"));

  $item[slot] = $itembase[slot];
  $item[sdam] = $itempre[sdam] + $itembase[sdam] + $itemsuf[sdam];
  $item[pdam] = $itempre[pdam] + $itembase[pdam]+ $itemsuf[pdam];
  $item[bdam] = $itempre[bdam] + $itembase[bdam] + $itemsuf[bdam];
  $item[sarm] = $itempre[sarm] + $itembase[sarm] + $itemsuf[sarm];
  $item[parm] = $itempre[parm] + $itembase[parm]+ $itemsuf[parm];
  $item[barm] = $itempre[barm] + $itembase[barm] + $itemsuf[barm];
  $item[hpreg] = $itempre[hpreg] + $itembase[hpreg] + $itemsuf[hpreg];
  $item[mpreg] = $itempre[mpreg] + $itembase[mpreg] + $itemsuf[mpreg];
  $item[des] = trim($itembase[des] . " " . $itempre[des] . " " . $itemsuf[des]);

  mysqli_query($conn,"INSERT INTO Item (pre, base, suf, des, owner, slot, equip, sdam, pdam, bdam, sarm, parm, barm, hpreg, mpreg) VALUES ('$pre', '$base', '$suf', '$item[des]', '$itemowner', '$item[slot]', '$isequipped', '$item[sdam]', '$item[pdam]', '$item[bdam]', '$item[sarm]', '$item[parm]', '$item[barm]', '$item[hpreg]', '$item[mpreg]')");

  echo "Item given!";

  mysqli_close($conn);

}
giveItem("Rusty", "Long Sword", "", "Darksage", 1);
giveItem("Weak", "Wooden Shield", "", "Darksage", 2);
giveItem("", "Leather Armor", "", "Darksage", 1);
giveItem("", "Leather Gloves", "", "Darksage", 1);
giveItem("", "Leather Greaves", "", "Darksage", 1);
giveItem("", "Leather Boots", "", "Darksage", 1);


?>