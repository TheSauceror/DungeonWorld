<style>
table, th, tr, td {
  /*border: 1  solid black;*/
  border-collapse: collapse;
  padding: 5px;
}
</style>

<?php

include "checklogin.php";

ini_set("display_errors", 1);

echo "<center><h1>Running the dungeon...</h1></center>";

$partyname = "";
$dungeonid = $_POST['dungeon'];
if($dungeonid == "") { header('Location: dungeons.php'); }
$dungeonname = "";
$dungeonrooms = "";
$roomdescriptions = "";
$roomstats = [];
$room = 0;
$partyfighters = [];
$fighters = [];
$turnorder = [];
$map = [];
$totalgold = 0;
$reportintro = "";
$reportinitiative = "";
$reportmap = "";
$reporttext = "";
//$maxturns = 20;
$cd = 0;

function init() {
  global $cookie, $partyname, $dungeonid, $dungeonname, $dungeonrooms, $roomdescriptions, $partyfighters, $cd;
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
  $party = mysqli_query($conn,"SELECT * FROM Hero WHERE party = '$hero[party]'");

  /*if(mysqli_fetch_assoc($party)['cd'] > time()) {
    echo "<center><h1>Dungeon cooldown until: " . date("m-d-y H:i:s", mysqli_fetch_assoc($party)['cd']) . "</h1></center>";
    // exit;
  }*/

  while($row = mysqli_fetch_assoc($party)) {
    $partyfighters[] = $row;
  }
  foreach ($partyfighters as $heronum => $hero) {
    echo "<span style='color:green;'>loading " . $hero['name'] . "'s items</span><br>";
    $partyfighters[$heronum]['sdam'] = getAllItemStats($hero['id'], "sdam");
    $partyfighters[$heronum]['pdam'] = getAllItemStats($hero['id'], "pdam");
    $partyfighters[$heronum]['bdam'] = getAllItemStats($hero['id'], "bdam");
    $partyfighters[$heronum]['sarm'] = getAllItemStats($hero['id'], "sarm");
    $partyfighters[$heronum]['parm'] = getAllItemStats($hero['id'], "parm");
    $partyfighters[$heronum]['barm'] = getAllItemStats($hero['id'], "barm");

  }
  $dungeonid = mysqli_real_escape_string($conn, "$dungeonid");
  $dungeonstats = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Dungeons WHERE dungeonid = '$dungeonid'"));
  $dungeonname = $dungeonstats['dungeonname'];
  $dungeonrooms = explode("|", $dungeonstats['rooms']);
  $cd = time() + (6 * count($dungeonrooms));//600 = 10 minutes
  mysqli_query($conn,"UPDATE Party SET cd = '$cd' WHERE partyid = '$hero[party]'");
  $roomdescriptions = explode("|", $dungeonstats['des']);
  mysqli_close($conn);
  findMaxHpAndMp();
}

init();

foreach($dungeonrooms as $key => $room) {
  // global $dungeonrooms, $partyfighters, $roomstats, $fighters, $turnorder;
  echo "<br>";
  echo "<span style='color:red;'>fighters before: </span>";
  print_r($fighters);
  echo "<br>";
  $fighters = $partyfighters;
  $roomstats = [];
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  foreach($dungeonrooms as $dungeonroom) {
    $roomstats[] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Rooms WHERE roomid = '$dungeonroom'"));
  }
  foreach(explode("|", $roomstats[$key]['enemies']) as $enemyid) {
    $fighters[] = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Enemies WHERE id = '$enemyid'"));
  }
  mysqli_close($conn);
  echo "<span style='color:red;'>fighters after: </span>";
  print_r($fighters);
  echo "<br>";
  //findMaxHpAndMp();
  $reportintro .= "<strong>$dungeonname: Room " . ($key + 1) . "</strong> - " . $roomdescriptions[$key];
  startRoom($key);
  updateMap($roomstats[$key]['length'], $roomstats[$key]['width'], $roomstats[$key]['floor']);
  $maxturns = 20;
  while(!onlyOneTeam()) {
    $maxturns--;
    echo "Turns left: " . $maxturns . "<br>";
    getTurnOrder();
    foreach ($turnorder[0] as $currentturn) {
      for($acts = 0; $acts < $fighters[$currentturn]['act']; $acts++) {
        if($fighters[$currentturn]['hp'] < 1) { break; }
        $j = -1;
        do{
          $j++;
        }while(!testSwitch1($currentturn,$j));
        testSwitch2($currentturn,$j);
        //if(onlyOneTeam() || $maxturns == 0) {
        //  break 2;
        //}
        if(onlyOneTeam() == $fighters[0]['party'] && $maxturns > 0) {
          $reportintro .= "Victory!";
          break 2;
        }
      }
    }
    updateMap($roomstats[$key]['length'], $roomstats[$key]['width'], $roomstats[$key]['floor']);
  }

  // getTurnOrder();

  /*if(onlyOneTeam() == $fighters[0]['party'] && $maxturns > 0) {
    $reportintro .= "Victory!";
  } else {
    $reportintro .= "Defeat!";
    exit;
  }
  $reportintro .= "|";*/

  foreach($fighters as $ind => $fighter) {
    if($fighter['party'] != $hero['party']) {
      echo "Unsetting: " . $fighter['name'] . "<br>";
      unset($fighters[$ind]);
    }
  }
  $partyfighters = $fighters;
}

function getDamage($attacker, $defender) {
  $damage = max(1, max(0, $attacker['sdam'] - $defender['sarm']) + max(0, $attacker['pdam'] - $defender['parm']) + max(0, $attacker['bdam'] - $defender['barm']));
  echo "<span style='color:blue;'>damage: </span>", $damage, " from ", $attacker['name'], " to ", $defender['name'], "<br>";
  return $damage;
}

function getAllItemStats($hero, $stat) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  //$equippeditems = mysqli_fetch_assoc(mysqli_query($conn,"SELECT SUM(ItemBase.$stat) + SUM(ItemPrefix.$stat) + SUM(ItemSuffix.$stat) AS 'allequippedstats' FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.equip > 0"));
  $equippeditems = mysqli_query($conn,"SELECT * FROM Inventory LEFT JOIN ItemBase ON Inventory.base = ItemBase.baseid Left JOIN ItemPrefix ON Inventory.prefix = ItemPrefix.prefixid LEFT JOIN ItemSuffix ON Inventory.suffix = ItemSuffix.suffixid WHERE Inventory.owner = '$hero' AND Inventory.equip > 0");
  $equippedstats = 0;
  while($row = mysqli_fetch_assoc($equippeditems)) {
    $equippedstats += max(0, ($row["prefix".$stat] * $row['prefixlevel']) + ($row["base".$stat] * $row['baselevel']) + ($row["suffix".$stat] * $row['suffixlevel']));
  }
  mysqli_close($conn);
  return $equippedstats;
}

function giveGold() {
  global $partyfighters, $totalgold, $reportintro;
  $totalgold *= (1 + rand(-15, 15) / 100);
  $eachgold = floor($totalgold/count($partyfighters));
  foreach($partyfighters as $fighter) {
    $id = $fighter['id'];
    $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
    mysqli_query($conn, "UPDATE Hero SET gold = gold + $eachgold WHERE id = '$id'");
    mysqli_close($conn);
    $reportintro .= $fighter['name'] . ": " . $eachgold . " gold<br>";
  }
}

function testSwitch1($i, $j) {
  global $fighters, $reporttext;
  $testplan = explode("||",$fighters[$i]['battleplan']);
  // $reportmap .= count($testplan) . "x" . count($testplan);
  if($testplan[0] == "" || $j > count($testplan)) {
    $reporttext .= $fighters[$i]['name'] . " does nothing.<br>";
    return true;
  }
  $testplan = explode("|", $testplan[$j])[0];
  switch($testplan) {
    case "nexttoenemy":
      $closest = findNearestEnemy($i);
      $dist = (abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y']));
      return $dist < 2;
      break;
    case "notnexttoenemy":
      $closest = findNearestEnemy($i);
      $dist = (abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y']));
      return $dist > 1;
      break;
    case "yourhpbelow100":
      return $fighters[$i]['hp'] < $fighters[$i]['maxhp'];
      break;
    default:
      $reporttext .= $fighters[$i]['name'] . " does nothing.<br>";
      return true;
      break;
  }
}

function testSwitch2($i ,$j) {
  global $fighters, $map, $totalgold, $reporttext;
  $testplan = explode("||",$fighters[$i]['battleplan']);
  if($testplan[0] == "") { return; }
  $testplan = explode("|", $testplan[$j])[1];
  switch($testplan) {
    case "moveclosertoenemy":
      $closest = findNearestEnemy($i);
      $reporttext .= $fighters[$i]['name'] . " moved from (" . $fighters[$i]['x'] . ", " . $fighters[$i]['y'] . ") to (";
      for ($k = 0; $k < $fighters[$i]['move']; $k++) {
      //$move = $fighters[$i]['agi'];
      //while($move > 0 ) {
        if($fighters[$i]['x'] < $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']+1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']+1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']++;
          //$move--;
        } else
        if($fighters[$i]['y'] < $fighters[$closest]['y'] && $map[$fighters[$i]['y']+1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']+1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']++;
          //$move--;
        } else
        if($fighters[$i]['x'] > $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']-1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']-1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']--;
          //$move--;
        } else
        if($fighters[$i]['y'] > $fighters[$closest]['y'] && $map[$fighters[$i]['y']-1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']-1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']--;
          //$move--;
        }
      }
      $reporttext .= $fighters[$i]['x'] . ", " .+ $fighters[$i]['y'] . ").<br>";
      return;
      break;
    case "meleeattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 2) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        $reporttext .= $fighters[$i]['name'] . " hits " . $fighters[$closest]['name'] . " for " . $damage . " damage.<br>";
        $fighters[$closest]['hp'] -= $damage;
        if($fighters[$closest]['hp'] < 1) {
          $fighters[$closest]['hp'] = 0;
          $map[$fighters[$closest]['y']][$fighters[$closest]['x']] = -1;
          $reporttext .= $fighters[$closest]['name'] . " dies.<br>";
          if($fighters[$closest]['party'] == "Enemy") { $totalgold += $fighters[$closest]['gold']; }
        }
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;
    case "rangedattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 9) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        $reporttext .= $fighters[$i]['name'] . " shoots " . $fighters[$closest]['name'] . " for " . $damage . " damage.<br>";
        $fighters[$closest]['hp'] -= $damage;
        if($fighters[$closest]['hp'] < 1) {
          $fighters[$closest]['hp'] = 0;
          $map[$fighters[$closest]['y']][$fighters[$closest]['x']] = -1;
          $reporttext .= $fighters[$closest]['name'] . " dies.<br>";
          if($fighters[$closest]['party'] == "Enemy") { $totalgold += $fighters[$closest]['gold']; }
        }
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;
    case "magicattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 6) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        $reporttext .= $fighters[$i]['name'] . " zaps " . $fighters[$closest]['name'] . " for " . $damage . " damage.<br>";
        $fighters[$closest]['hp'] -= $damage;
        if($fighters[$closest]['hp'] < 1) {
          $fighters[$closest]['hp'] = 0;
          $map[$fighters[$closest]['y']][$fighters[$closest]['x']] = -1;
          $reporttext .= $fighters[$closest]['name'] . " dies.<br>";
          if($fighters[$closest]['party'] == "Enemy") { $totalgold += $fighters[$closest]['gold']; }
        }
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;
  }
}

function onlyOneTeam() {
  global $fighters;
  $team = "";
  foreach ($fighters as $fighter) {
    if($fighter['hp'] > 0) {
      if($team == "") { $team = $fighter['party']; }
      if($team != $fighter['party']) { return false; }
    }
  }
  return $team;
}

function findNearestEnemy($index) {
  global $fighters;
  $closest = -1;
  for ($i = 0; $i < count($fighters); $i++) {
    if($fighters[$i]['hp'] > 0 && $fighters[$i]['party'] != $fighters[$index]['party']) {
      if($closest == -1) {
        $closest = $i;
      } else {
        if((abs($fighters[$index]['x']-$fighters[$i]['x']) + abs($fighters[$index]['y']-$fighters[$i]['y'])) < (abs($fighters[$index]['x']-$fighters[$closest]['x']) + abs($fighters[$index]['y']-$fighters[$closest]['y']))) {
          $closest = $i;
        }
      }
    }
  }
  return $closest;
}

function getTurnOrder() {
  global $fighters, $turnorder, $reportinitiative;
  $turnorder = array(null,null);
  foreach($fighters as $key => $f) {
    $turnorder[0][] = $key;
    $turnorder[1][] = $f['initiative'];
  }
  array_multisort($turnorder[1], SORT_DESC, SORT_NUMERIC, $turnorder[0]);
  $reportinitiative .= "<center><table><tr><th>Initiative</th><th>Name</th><th>Health</th><th>Mana</th></tr>";
  foreach($turnorder[0] as $key => $turn) {
    $reportinitiative .= "<tr><td>" . $turnorder[1][$key] . "</td><td>" . $fighters[$turn]['name'] . "</td><td>" . $fighters[$turn]['hp'] . "</td><td>" . $fighters[$turn]['mp'] . "</td></tr>";
  }
  $reportinitiative .= "</table></center>";
}

function findMaxHpAndMp() {
  global $partyfighters;
  foreach($partyfighters as &$partyfighters) {
    $hpmult = 1;
    $mpmult = 1;
    switch($partyfighters['race']) {
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
    switch($partyfighters['prof']) {
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
    $partyfighters['maxhp'] = floor(($partyfighters['vit']*5 + $partyfighters['str']*3) * $hpmult);
    $partyfighters['hp'] = $partyfighters['maxhp'];
    $partyfighters['maxmp'] = floor(($partyfighters['pie']*4 + $partyfighters['nce']) * $mpmult);
    $partyfighters['mp'] = $partyfighters['maxmp'];
    //TODO: this calculates party hp/mp correctly, but not enemies. adding it to the database should fix this, or another function
  }
}

function startRoom($num) {
  global $roomstats, $fighters, $map;
  // getTurnOrder();
  $map = [];
  $l = $roomstats[$num]['length'];
  $w = $roomstats[$num]['width'];
  for ($i=0; $i < $w; $i++) {
    for ($j=0; $j < $l; $j++) {
      $map[$i][] = -1;
    }
  }
  foreach($fighters as $key => &$fighter) {
    do {
      $x = rand(0,$l-1);
      $y = rand(0,2);
      if($fighter['party'] != "Enemy") { $y += $w-3; }
    } while ($map[$y][$x] != -1);
    $fighter['x'] = $x;
    $fighter['y'] = $y;
    $map[$y][$x] = $key;
  }
  // updateMap($l, $w);
}

function updateMap($l, $w, $floor) {
  global $fighters, $map, $reportmap, $dungeonfloor, $reportintro, $reportinitiative, $reporttext;
  $mapstring = "";
  for ($i=0; $i < $w; $i++) {
    for ($j=0; $j < $l; $j++) {
      $key = $map[$i][$j];
      if($key > -1 && $fighters[$key]['hp'] > 0) {
        $mapstring .= "<img src='images/" . $fighters[$key]['prof'] . ".gif' title='" . $fighters[$key]['name'] . "'>";
      }
      else {
        $mapstring .= "<img src='images/" . $floor . ".gif'>";
      }
    }
    $mapstring .= "<br>";
  }
  $reportintro .= "|";
  if($reportmap != "") {
    $reportinitiative .= "|";
    $reporttext .= "|";
  }
  $reportmap .= $mapstring . "|";
}

$reportinitiative .= "|";
$reporttext .= "|";

giveGold();
$reportintro = addslashes($reportintro);
$reportinitiative = addslashes($reportinitiative);
$reportmap = addslashes($reportmap);
$reporttext = addslashes($reporttext);
//$date = date("m-d-y H:i:s");
$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
mysqli_query($conn,"INSERT INTO Reports (party, timestamp, dungeon, reportintro, reportinitiative, reportmap, reporttext) VALUES ('$hero[party]', '$cd', '$dungeonname', '$reportintro', '$reportinitiative', '$reportmap', '$reporttext')") or die(mysqli_error($conn));
mysqli_close($conn);

//echo "<META http-equiv='refresh' content='0;URL=reports.php'>";

?>