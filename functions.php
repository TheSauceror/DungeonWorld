<?php

function getItemPrice($itemid, $mult) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.inventoryid = '$itemid'"));
  mysqli_close($conn);
  if($mult == 0) { $mult = (.5+($item['market']-time())/60/60/24/7/2); }
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
  	$item = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid  WHERE Inventory.owner = '$hero' AND ItemBase.slot = '$slot' AND Inventory.market = '0' AND Inventory.equip = '$equip'"));
  }
  $itemdes = "";
  mysqli_close($conn);
  if($item['basesdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixsdam'] * $item['prefixlevel']) + ($item['basesdam'] * $item['baselevel']) + ($item['suffixsdam'] * $item['suffixlevel'])) . " slashing damage";
  }
  if($item['basepdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixpdam'] * $item['prefixlevel']) + ($item['basepdam'] * $item['baselevel']) + ($item['suffixpdam'] * $item['suffixlevel'])) . " piercing damage";
  }
  if($item['basebdam'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixbdam'] * $item['prefixlevel']) + ($item['basebdam'] * $item['baselevel']) + ($item['suffixbdam'] * $item['suffixlevel'])) . " bludgeoning damage";
  }
  if($item['basesarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixsarm'] * $item['prefixlevel']) + ($item['basesarm'] * $item['baselevel']) + ($item['suffixsarm'] * $item['suffixlevel'])) . " slashing armor";
  }
  if($item['baseparm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixparm'] * $item['prefixlevel']) + ($item['baseparm'] * $item['baselevel']) + ($item['suffixparm'] * $item['suffixlevel'])) . " piercing armor";
  }
  if($item['basebarm'] > 0) {
    if($itemdes != "") { $itemdes .= ", "; }
    $itemdes .= max(0, ($item['prefixbarm'] * $item['prefixlevel']) + ($item['basebarm'] * $item['baselevel']) + ($item['suffixbarm'] * $item['suffixlevel'])) . " bludgeoning armor";
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

?>