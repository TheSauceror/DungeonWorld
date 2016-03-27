<?php

function getItemName($hero, $slot, $equip, $baseid, $baselevel, $prefixid, $prefixlevel, $suffixid, $suffixlevel) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  if($hero == 0) {
    $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM ItemBase, ItemPrefix, ItemSuffix WHERE ItemBase.baseid = '$baseid' AND ItemPrefix.prefixid = '$prefixid' AND ItemSuffix.suffixid = '$suffixid'"));
    $itemname['prefixlevel'] = $prefixlevel;
    $itemname['baselevel'] = $baselevel;
    $itemname['suffixlevel'] = $suffixlevel;
  } else {
    $itemname = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND ItemBase.slot='$slot' AND Inventory.equip = '$equip'"));
  }
  mysqli_close($conn);
  $fullitemname = "";
  if($itemname['prefixlevel'] > 0) { $fullitemname .= $itemname['prefixname'] . "(" . $itemname['prefixlevel'] . ") "; }
  if($itemname['baselevel'] > 0)  { $fullitemname .= $itemname['basename'] . "(" . $itemname['baselevel'] . ")"; }
  if($itemname['suffixlevel'] > 0) { $fullitemname .= " " . $itemname['suffixname'] . "(" . $itemname['suffixlevel'] . ")"; }
  return trim($fullitemname);
}

function getItemPrice($itemid, $mult) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  mysqli_close($conn);
  if($mult == 0) { $mult = (.5+($item['time']-time())/60/60/24/7/2); }
  return max(1, floor($mult * (($item['prefixvalue'] * $item['prefixlevel']) + ($item['basevalue'] * $item['baselevel']) + ($item['suffixvalue'] * $item['suffixlevel']))));
}

function getItemStats($itemid, $stat) { //get stats of a single item
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  mysqli_close($conn);
  return max(0, ($item["prefix".$stat] * $item['prefixlevel']) + ($item["base".$stat] * $item['baselevel']) + ($item["suffix".$stat] * $item['suffixlevel']));
}

function getAllItemStats($hero, $stat) { //get total stats of all items on a hero
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $equippeditems = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.equip > 0");
  $equippedstats = 0;
  while($row = mysqli_fetch_assoc($equippeditems)) {
    $equippedstats += max(0, ($row["prefix".$stat] * $row['prefixlevel']) + ($row["base".$stat] * $row['baselevel']) + ($row["suffix".$stat] * $row['suffixlevel']));
  }
  mysqli_close($conn);
  return $equippedstats;
}

function createItem($level) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd"); 
  $baseitemcomponents = mysqli_query($conn,"SELECT * FROM ItemBase");
  $prefixitemcomponents = mysqli_query($conn,"SELECT * FROM ItemPrefix");
  $suffixitemcomponents = mysqli_query($conn,"SELECT suffixid, suffixname FROM ItemSuffix");
  $baseitem = [];
  $prefixitem = [];
  $suffixitem = [];
  $prefixid = 0;
  $prefixlevel = 0;
  $suffixid = 0;
  $suffixlevel = 0;
  while($row = mysqli_fetch_assoc($baseitemcomponents)) {
    $baseitem[] = $row;
  }
  $basenum = rand(0, count($baseitem) - 1);
  $baseid = $baseitem[$basenum]['baseid'];
  $baselevel = rand(1, $level);
  if($baselevel > 1 && rand(1,100) > 50) {
    while($row = mysqli_fetch_assoc($prefixitemcomponents)) {
      $prefixitem[] = $row;
    }
    $prefixnum = rand(1, count($prefixitem) - 1);
    $prefixid = $prefixitem[$prefixnum]['prefixid'];
    $prefixlevel = rand(1, $baselevel - 1);
  }
  if($baselevel > 1 && rand(1,100) > 50) {
    while($row = mysqli_fetch_assoc($suffixitemcomponents)) {
      $suffixitem[] = $row;
    }
    $suffixnum = rand(1, count($suffixitem) - 1);
    $suffixid = $suffixitem[$suffixnum]['suffixid'];
    $suffixlevel = rand(1, $baselevel - 1);
  }
  return $baseid . "," . $baselevel . "," . $prefixid . "," . $prefixlevel . "," . $suffixid . "," . $suffixlevel;
}

function giveItem($heroid, $equip, $baseid, $baselevel, $prefixid, $prefixlevel, $suffixid, $suffixlevel) { //give an item to a hero
	$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	mysqli_query($conn,"INSERT INTO Inventory (owner, equip, base, baselevel, prefix, prefixlevel, suffix, suffixlevel) VALUES ($heroid, $equip, $baseid, $baselevel, $prefixid, $prefixlevel, $suffixid, $suffixlevel)") or die(mysqli_error($conn));
	mysqli_close($conn);
}

function getItemDes($itemid, $hero, $slot, $equip) { //calculate description for an item from all of its components. needs updated to keep up with database
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  if($itemid != 0) {
  	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  } else {
  	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid  WHERE Inventory.owner = '$hero' AND ItemBase.slot = '$slot' AND Inventory.time = '0' AND Inventory.equip = '$equip'"));
  }
  $itemdes = "";
  mysqli_close($conn);
  if($item['basesdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixsdam'] * $item['prefixlevel']) + ($item['basesdam'] * $item['baselevel']) + ($item['suffixsdam'] * $item['suffixlevel'])) . " slashing power";
  }
  if($item['basepdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixpdam'] * $item['prefixlevel']) + ($item['basepdam'] * $item['baselevel']) + ($item['suffixpdam'] * $item['suffixlevel'])) . " piercing power";
  }
  if($item['basebdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixbdam'] * $item['prefixlevel']) + ($item['basebdam'] * $item['baselevel']) + ($item['suffixbdam'] * $item['suffixlevel'])) . " bludgeoning power";
  }
  if($item['baseadam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixadam'] * $item['prefixlevel']) + ($item['baseadam'] * $item['baselevel']) + ($item['suffixadam'] * $item['suffixlevel'])) . " arcane power";
  }
  if($item['baseddam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixddam'] * $item['prefixlevel']) + ($item['baseddam'] * $item['baselevel']) + ($item['suffixddam'] * $item['suffixlevel'])) . " divine power";
  }
  if($item['basesarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixsarm'] * $item['prefixlevel']) + ($item['basesarm'] * $item['baselevel']) + ($item['suffixsarm'] * $item['suffixlevel'])) . " slashing defense";
  }
  if($item['baseparm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixparm'] * $item['prefixlevel']) + ($item['baseparm'] * $item['baselevel']) + ($item['suffixparm'] * $item['suffixlevel'])) . " piercing defense";
  }
  if($item['basebarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixbarm'] * $item['prefixlevel']) + ($item['basebarm'] * $item['baselevel']) + ($item['suffixbarm'] * $item['suffixlevel'])) . " bludgeoning defense";
  }
  if($item['baseaarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixaarm'] * $item['prefixlevel']) + ($item['baseaarm'] * $item['baselevel']) + ($item['suffixaarm'] * $item['suffixlevel'])) . " arcane defense";
  }
  if($item['basedarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixdarm'] * $item['prefixlevel']) + ($item['basedarm'] * $item['baselevel']) + ($item['suffixdarm'] * $item['suffixlevel'])) . " divine defense";
  }
  if($item['basehpreg'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixhpreg'] * $item['prefixlevel']) + ($item['basehpreg'] * $item['baselevel']) + ($item['suffixhpreg'] * $item['suffixlevel'])) . " HP regen";
  }
  if($item['basempreg'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixmpreg'] * $item['prefixlevel']) + ($item['basempreg'] * $item['baselevel']) + ($item['suffixmpreg'] * $item['suffixlevel'])) . " MP regen";
  }
  return $itemdes;
}

//put hp/mp/init calculations here. from attribute level up in profile and hero creation in login
function calculateHPMPInit($heroid) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$heroid'"));

  $hpmult = 1;
  $mpmult = 1;
  switch($hero['race']) {
    case "Elf":
      $hpmult = $hpmult - 0.15;
      $mpmult = $mpmult + 0.3;
      break;
    case "Orc":
      $hpmult = $hpmult + 0.2;
      $mpmult = $mpmult - 0.1;
      break;
    case "Human":
      $hpmult = $hpmult + 0.1;
      $mpmult = $mpmult + 0.1;
      break;
    case "Dwarf":
      $hpmult = $hpmult + 0.30;
      $mpmult = $mpmult + 0.15;
      break;
  }
  switch($hero['prof']) {
    case "Mage":
      $hpmult = $hpmult - 0.15;
      $mpmult = $mpmult + 0.3;
      break;
    case "Barbarian":
      $hpmult = $hpmult + 0.3;
      $mpmult = $mpmult - 0.15;
      break;
    case "Archer":
      $hpmult = $hpmult + 0.1;
      $mpmult = $mpmult + 0.1;
      break;
    case "Knight":
      $hpmult = $hpmult + 0.20;
      $mpmult = $mpmult - 0.05;
      break;
    case "Priest":
      $hpmult = $hpmult - 0.05;
      $mpmult = $mpmult + 0.2;
      break;
  }
  $maxhp = floor(($hero['vit']*5 + $hero['str']*3) * $hpmult);
  $maxmp = floor(($hero['pie']*4 + $hero['nce']) * $mpmult);
  $initiative = $hero['dex']*2 + $hero['nce'];

  mysqli_query($conn,"UPDATE Hero SET maxhp = '$maxhp' WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  mysqli_query($conn,"UPDATE Hero SET hp = '$maxhp' WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  mysqli_query($conn,"UPDATE Hero SET maxmp = '$maxmp' WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  mysqli_query($conn,"UPDATE Hero SET mp = '$maxmp' WHERE id = '$hero[id]'") or die(mysqli_error($conn));
  mysqli_query($conn,"UPDATE Hero SET initiative = '$initiative' WHERE id = '$hero[id]'") or die(mysqli_error($conn));

  mysqli_close($conn);
}

function sort_up($a, $b) { //move these to functions? if used for sorting elsewhere
  if(gettype($a[$_GET['sort']]) == "string") {
    return strnatcasecmp($a[$_GET['sort']], $b[$_GET['sort']]);
  } else {
    return $a[$_GET['sort']] - $b[$_GET['sort']];
  }
}

function sort_down($a, $b) { //move these to functions? if used for sorting elsewhere
  if(gettype($a[$_GET['sort']]) == "string") {
    return strnatcasecmp($b[$_GET['sort']], $a[$_GET['sort']]);
  } else {
    return $b[$_GET['sort']] - $a[$_GET['sort']];
  }
}

?>