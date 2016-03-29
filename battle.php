<style>
table, th, tr, td {
  /*border: 1  solid black;*/
  border-collapse: collapse;
  padding: 5px;
}
</style>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

ini_set("display_errors", 1);

echo "<center><h1>Running the dungeon...</h1></center>";

$partyname = "";
$partyid = $_GET['partyid']; //protect this from injection
if($partyid == "") { header('Location: dungeons.php'); }
//$dungeonid = 
//if($dungeonid == "") { header('Location: dungeons.php'); }
$dungeonname = "";
$dungeonlevel = 0;
$dungeonrooms = "";
$roomdescriptions = "";
$roomstats = [];
$room = 0;
$partyfighters = [];
$fighters = [];
$status = [];
$turnorder = [];
$map = [];
$totalgold = 0;
$totalloot = [];
$reportintro = "";
$reportinitiative = "";
$reportmap = "";
$reporttext = "";
$maxTurns = 20;
$cd = 0;

function init() {
  global $cookie, $partyname, $partyid, $dungeonname, $dungeonrooms, $dungeonlevel, $roomdescriptions, $partyfighters, $cd;
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE id = '$cookie[0]'"));
  $party = mysqli_query($conn,"SELECT * FROM Hero WHERE party = '$hero[party]'");

  /*if(mysqli_fetch_assoc($party)['cd'] > time()) {
    echo "<center><h1>Dungeon cooldown until: " . date("m-d-y H:i:s", mysqli_fetch_assoc($party)['cd']) . "</h1></center>";
    // exit;
  }*/

  $partyid = mysqli_real_escape_string($conn, "$partyid");
  $dungeonstats = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Party, Dungeons WHERE Party.dungeonid = Dungeons.dungeonid AND Party.partyid = '$partyid'"));
  $dungeonname = $dungeonstats['dungeonname'];
  $dungeonrooms = explode("|", $dungeonstats['rooms']);
  $dungeonlevel = $dungeonstats['dungeonlevel'];
  $cd = time() + 1;//(6 * count($dungeonrooms));//600 = 10 minutes
  $roomdescriptions = explode("|", $dungeonstats['des']);

  while($row = mysqli_fetch_assoc($party)) {
    $partyfighters[] = $row;
  }

  foreach ($partyfighters as $heronum => $hero) {
    echo "<span style='color:green;'>loading " . $hero['name'] . "'s items</span><br>";
    $partyfighters[$heronum]['sdam'] = getAllItemStats($hero['id'], "sdam");
    $partyfighters[$heronum]['pdam'] = getAllItemStats($hero['id'], "pdam");
    $partyfighters[$heronum]['bdam'] = getAllItemStats($hero['id'], "bdam");
    $partyfighters[$heronum]['adam'] = getAllItemStats($hero['id'], "adam");
    $partyfighters[$heronum]['ddam'] = getAllItemStats($hero['id'], "ddam");
    $partyfighters[$heronum]['sarm'] = getAllItemStats($hero['id'], "sarm");
    $partyfighters[$heronum]['parm'] = getAllItemStats($hero['id'], "parm");
    $partyfighters[$heronum]['barm'] = getAllItemStats($hero['id'], "barm");
    $partyfighters[$heronum]['aarm'] = getAllItemStats($hero['id'], "aarm");
    $partyfighters[$heronum]['darm'] = getAllItemStats($hero['id'], "darm");
    $partyfighters[$heronum]['hpreg'] = getAllItemStats($hero['id'], "hpreg");
    $partyfighters[$heronum]['mpreg'] = getAllItemStats($hero['id'], "mpreg");
    $partyfighters[$heronum]['maxhp'] = $partyfighters[$heronum]['hp'];
    $partyfighters[$heronum]['maxmp'] = $partyfighters[$heronum]['mp'];
  }
  mysqli_close($conn);
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
  $enemynames = [];
  foreach(explode("|", $roomstats[$key]['enemies']) as $enemyid) {
    $enemyfighter = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Enemies WHERE id = '$enemyid'"));
    $enemynames[$enemyfighter['name']]++;
    $enemyfighter['name'] .= " " . $enemynames[$enemyfighter['name']];
    $fighters[] = $enemyfighter;
  }
  mysqli_close($conn);
  echo "<span style='color:red;'>fighters after: </span>";
  print_r($fighters);
  echo "<br>";

  foreach ($fighters as $heronum => $x)
  {
    $status[$heronum]['id'] = $fighters[$heronum]['id'];
    $status[$heronum]['name'] = $fighters[$heronum]['name'];
    $status[$heronum]['str'] = 0;
    $status[$heronum]['vit'] = 0;
    $status[$heronum]['dex'] = 0;
    $status[$heronum]['nce'] = 0;
    $status[$heronum]['pie'] = 0;
    $status[$heronum]['sdam'] = 0;
    $status[$heronum]['pdam'] = 0;
    $status[$heronum]['bdam'] = 0;
    $status[$heronum]['adam'] = 0;
    $status[$heronum]['ddam'] = 0;
    $status[$heronum]['sarm'] = 0;
    $status[$heronum]['parm'] = 0;
    $status[$heronum]['barm'] = 0;
    $status[$heronum]['aarm'] = 0;
    $status[$heronum]['darm'] = 0;
    $status[$heronum]['maxhp'] = 0;
    $status[$heronum]['hpreg'] = 0;
    $status[$heronum]['maxmp'] = 0;
    $status[$heronum]['mpreg'] = 0;
    $status[$heronum]['root'] = 0;
    $status[$heronum]['silence'] = 0; 
    $status[$heronum]['stun'] = 0;
    $status[$heronum]['initiative'] = 0;
  }

  $reportintro .= "<strong>$dungeonname: Room " . ($key + 1) . "</strong> - " . $roomdescriptions[$key];
  startRoom($key);
  updateMap($roomstats[$key]['length'], $roomstats[$key]['width'], $roomstats[$key]['floor']);
  $currTurn = 1;
  while(!onlyOneTeam()) {
    if ($currTurn > $maxTurns)
    {
      foreach ($fighters as $heronum => $z)
      {
        if ($fighters[$heronum]['party'] != 'Enemy')
        {
          $fighters[$heronum]['hp'] = 0;
        }
      }
      $reporttext .= "Max turns exceeded, the party is forced to flee <br>";
      break;
    }

    echo "Turns left: " . ($maxTurns-$currTurn) . "<br>";
    getTurnOrder();
    foreach ($turnorder[0] as $currentturn) {
      if($fighters[$currentturn]['hp'] < 1)
      { continue; }

      if ($fighters[$currentturn]['party'] == 'Enemy')
      {
        $fighters[$currentturn]['hp'] = min($fighters[$currentturn]['maxhp'], $fighters[$currentturn]['hp'] + $status[$currentturn]['hpreg']);
        $fighters[$currentturn]['mp'] = min($fighters[$currentturn]['maxmp'], $fighters[$currentturn]['mp'] + $status[$currentturn]['mpreg']);
      }
      else
      {
        $fighters[$currentturn]['hp'] = min($fighters[$currentturn]['maxhp'],
          $fighters[$currentturn]['hp'] + $fighters[$currentturn]['hpreg'] + $status[$currentturn]['hpreg']);
        $fighters[$currentturn]['mp'] = min($fighters[$currentturn]['maxmp'],
          $fighters[$currentturn]['mp'] + $fighters[$currentturn]['mpreg'] + $status[$currentturn]['mpreg']);
      }

      if($fighters[$currentturn]['hp'] < 1)
      { 
        KillFighter($currentturn);
        continue;
      }

      for($acts = 0; $acts < $fighters[$currentturn]['act']; $acts++) {
        $j = -1;
        do{
          $j++;
        }while(!condition($currentturn,$j));
        action($currentturn,$j);
        //if(onlyOneTeam() || $currTurn >= $maxTurns) {
        //  break 2;
        //}
        if(onlyOneTeam() == $fighters[0]['party'] && $currTurn < $maxTurns) {
          $reportintro .= "Victory!";
          break 2;
        }
      }
    }
    updateMap($roomstats[$key]['length'], $roomstats[$key]['width'], $roomstats[$key]['floor']);
    DevalueStatus();
   	$currTurn++;
  }

  // getTurnOrder();

  /*if(onlyOneTeam() == $fighters[0]['party'] && $currTurn < $maxTurns) {
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

function giveLoot() { //rename to be more accurate
  global $partyfighters, $totalgold, $totalloot, $reportintro;
  $totalgold *= (1 + rand(-15, 15) / 100);
  $eachgold = floor($totalgold / count($partyfighters));
  foreach($partyfighters as $fighter) {
    $id = $fighter['id'];
    $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
    mysqli_query($conn, "UPDATE Hero SET gold = gold + $eachgold WHERE id = '$id'");
    mysqli_close($conn);
    $reportintro .= $fighter['name'] . " gets " . $eachgold . " gold<br>";
  }
  $reportintro .= "<br>";
  foreach($totalloot as $lootdrop) {
    $loottaker = rand(1, count($partyfighters)) - 1;
    $lootstats = explode(",", $lootdrop);
    giveItem($partyfighters[$loottaker]['id'], 0, $lootstats[0], $lootstats[1], $lootstats[2], $lootstats[3], $lootstats[4], $lootstats[5]);
    $reportintro .= $partyfighters[$loottaker]['name'] . " picks up " . getItemName(0, 0, 0, $lootstats[0], $lootstats[1], $lootstats[2], $lootstats[3], $lootstats[4], $lootstats[5]) . "<br>";
  }
}


function condition($i, $j) {
  global $fighters, $reporttext;
  $testplan = explode("||",$fighters[$i]['battleplan']);
  // $reportmap .= count($testplan) . "x" . count($testplan);   
  /*if($testplan[0] == "" || $j > count($testplan)) { //COMMENT OUT
    $reporttext .= $fighters[$i]['name'] . " has no plan.<br>";
    return true;
  }*/
  if (!isset($testplan[$j]))
  {
    $reporttext .= $fighters[$i]['name'] . " has nothing to do.<br>";
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
    case "yourhpbelow33":
      return $fighters[$i]['hp'] <= $fighters[$i]['maxhp']*(1/3);
      break;
    case "yourhpbelow66":
      return $fighters[$i]['hp'] <= $fighters[$i]['maxhp']*(2/3);
      break;
    case "allyhpbelow33":
      for($z=0; $z<count($fighters); $z++)
      {
        if ($fighters[$i]['party'] == $fighters[$z]['party'] && $fighters[$i] != $fighters[$z])
        {
          if ($fighters[$z]['hp'] < $fighters[$z]['maxhp']*(1/3))
            { return true; }
        }
      }
      break;
    case "allyhpbelow66":
      for($z=0; $z<count($fighters); $z++)
      {
        if ($fighters[$i]['party'] == $fighters[$z]['party'] && $fighters[$i] != $fighters[$z])
        {
          if ($fighters[$z]['hp'] < $fighters[$z]['maxhp']*(2/3))
            { return true; }
        }
      }
      break;
    default:
      $reporttext .= $fighters[$i]['name'] . " has nothing to do.<br>";
      return true;
      break;
  }
}

function action($i ,$j) {
  global $fighters, $status, $map, $totalgold, $reporttext, $currTurn;
  $testplan = explode("||",$fighters[$i]['battleplan']);
  if($testplan[0] == "") { return; }
  if(!isset($testplan[$j])) { return; }
  $condition = explode("|", $testplan[$j])[0];
  $testplan = explode("|", $testplan[$j])[1];
  switch($testplan) {
    case "moveclosertoenemy":
      if ($status[$i]['root'] > 0 || $status[$i]['stun'] > 0)
      {
        $reporttext .= $fighters[$i]['name'] . " is crowd controlled and unable move.<br>";
        return;
      }

      $closest = findNearestEnemy($i);
      $reporttext .= $fighters[$i]['name'] . " moved toward " . $fighters[$closest]['name'] . "<br>";
      //$reporttext .= $fighters[$i]['name'] . " moved from (" . $fighters[$i]['x'] . ", " . $fighters[$i]['y'] . ") to (";
      for ($k = 0; $k < $fighters[$i]['move']; $k++) {
      //$move = $fighters[$i]['agi'];
      //while($move > 0 ) {
        if($fighters[$i]['y'] > $fighters[$closest]['y'] && $map[$fighters[$i]['y']-1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']-1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']--;
          //$move--;
        } else
        if($fighters[$i]['y'] < $fighters[$closest]['y'] && $map[$fighters[$i]['y']+1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']+1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']++;
          //$move--;
        } else
        if($fighters[$i]['x'] < $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']+1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']+1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']++;
          //$move--;
        } else
        if($fighters[$i]['x'] > $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']-1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']-1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']--;
          //$move--;
        }
      }
      //$reporttext .= $fighters[$i]['x'] . ", " .+ $fighters[$i]['y'] . ").<br>";
      return;
      break;
    case "moveawayfromenemy":
      if ($status[$i]['root'] > 0 || $status[$i]['stun'] > 0)
      {
        $reporttext .= $fighters[$i]['name'] . " is crowd controlled and unable move.<br>";
        return;
      }

      $closest = findNearestEnemy($i);
      $reporttext .= $fighters[$i]['name'] . " moved away from " . $fighters[$closest]['name'] . "<br>";
      //$reporttext .= $fighters[$i]['name'] . " moved from (" . $fighters[$i]['x'] . ", " . $fighters[$i]['y'] . ") to (";
      for ($k = 0; $k < $fighters[$i]['move']; $k++) {
      //$move = $fighters[$i]['agi'];
      //while($move > 0 ) {
        if($fighters[$i]['y'] > $fighters[$closest]['y'] && $map[$fighters[$i]['y']+1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']+1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']++;
          //$move--;
        } else
        if($fighters[$i]['y'] < $fighters[$closest]['y'] && $map[$fighters[$i]['y']-1][$fighters[$i]['x']] == -1) {
          $map[$fighters[$i]['y']-1][$fighters[$i]['x']] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['y']--;
          //$move--;
        } else
        if($fighters[$i]['x'] < $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']-1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']-1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']--;
          //$move--;
        } else
        if($fighters[$i]['x'] > $fighters[$closest]['x'] && $map[$fighters[$i]['y']][$fighters[$i]['x']+1] == -1) {
          $map[$fighters[$i]['y']][$fighters[$i]['x']+1] = $map[$fighters[$i]['y']][$fighters[$i]['x']];
          $map[$fighters[$i]['y']][$fighters[$i]['x']] = -1;
          $fighters[$i]['x']++;
          //$move--;
        }
      }
      //$reporttext .= $fighters[$i]['x'] . ", " .+ $fighters[$i]['y'] . ").<br>";
      return;
      break;
    /*case "meleeattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 2) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        doDamage($i, "hits", $closest, $damage);
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;
    case "rangedattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 9) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        doDamage($i, "shoots", $closest, $damage);
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;
    case "magicattack":
      $closest = findNearestEnemy($i);
      if((abs($fighters[$i]['x']-$fighters[$closest]['x']) + abs($fighters[$i]['y']-$fighters[$closest]['y'])) < 6) {
        $damage = getDamage($fighters[$i], $fighters[$closest]);
        doDamage($i, "zaps", $closest, $damage);
      } else { $reporttext .= $fighters[$i]['name'] . " is out of range.<br>";}
      return;
      break;*/
    default:
      if ($status[$i]['silence'] > 0 || $status[$i]['stun'] > 0)
      {
        $reporttext .= $fighters[$i]['name'] . " is crowd controlled and unable act.<br>";
        return;
      }

      $rotation = explode(",", $testplan);
      if (count($rotation) > 0)
      {
        $skill = $rotation[($currTurn-1) % count($rotation)];
        $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
        $skillInfo = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM SkillList WHERE skillid = '$skill'"));
        mysqli_close($conn);

        if ($fighters[$i]['mp'] - $skillInfo['cost'] < 0)
        {
            $reporttext .= $fighters[$i]['name'] . " does not have enough MP.<br>";
            return;
        }
        $fighters[$i]['mp'] -= $skillInfo['cost'];
        switch ($skillInfo['category'])
        {
          case 'heal':
            if ($condition == 'yourhpbelow33' || $condition == 'yourhpbelow66')
            {
              DoHeal($i, $i, $skillInfo, $skill);
              break;
            }
            else if ($condition == 'allyhpbelow33')
            {
              for($z=0; $z<count($fighters); $z++)
              {
                if ($fighters[$i]['party'] == $fighters[$z]['party'] && $fighters[$i] != $fighters[$z])
                {
                  if ($fighters[$z]['hp'] < $fighters[$z]['maxhp']*(1/3))
                  { 
                    DoHeal($i, $z, $skillInfo, $skill);
                    break;
                  }
                }
              }
            }
            else if ($condition == 'allyhpbelow66')
            {
              for($z=0; $z<count($fighters); $z++)
              {
                if ($fighters[$i]['party'] == $fighters[$z]['party'] && $fighters[$i] != $fighters[$z])
                {
                  if ($fighters[$z]['hp'] < $fighters[$z]['maxhp']*(2/3))
                  {
                    DoHeal($i, $z, $skillInfo, $skill);
                    break;
                  }
                }
              }
            }
            break;
          case 'buff':
            DoBuff($i, $skillInfo, $skill);
            break;
          default:
            DoDamage($i, $skillInfo, $skill);
            break;
        }
      }
      else
        { $reporttext .= $fighters[$i]['name'] . " does not know what to do.<br>"; }
      return;
      break;
  }
}

function DoHeal($attacker, $reciever, $skillInfo, $skill)
{
  global $fighters, $status, $totalgold, $totalloot, $reporttext, $map, $dungeonlevel;
  $hid = $fighters[$attacker]['id'];
    $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
    $skillLevel = mysqli_fetch_assoc(mysqli_query($conn,"SELECT skilllevel FROM HeroSkills WHERE abilityid = '$skill' AND heroid = '$hid'"));
    mysqli_close($conn);
    if ($skillLevel == null && $fighters[$attacker]['party'] != 'Enemy')
    {
      $reporttext .= $fighters[$attacker]['name'] . " cannot use " . $skillInfo['skillname'] . ".<br>";
      return;
    }

    $healStat = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['effect']);
    $heal = eval("return ($healStat);");
    $heal = ScaleDamage($heal, $fighters[$attacker]['pie'], 100, 1);
    $reporttext .= $fighters[$attacker]['name'] . " heals " . $fighters[$reciever]['name'] . " for " . $heal . " damage " . " with " . $skillInfo['skillname'] . ".<br>";
    $fighters[$reciever]['hp'] = min($fighters[$reciever]['maxhp'], $fighters[$reciever]['hp'] + $heal);
}

function DoBuff($attacker, $skillInfo, $skill)
{
  global $fighters, $status, $totalgold, $totalloot, $reporttext, $map, $dungeonlevel;
  $buffs = explode('|', $skillInfo['skillstatus']);
  foreach ($buffs as $buff)
  {
    $hid = $fighters[$attacker]['id'];
    $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
    $skillLevel = mysqli_fetch_assoc(mysqli_query($conn,"SELECT skilllevel FROM HeroSkills WHERE abilityid = '$skill' AND heroid = '$hid'"));
    mysqli_close($conn);
    if ($skillLevel == null && $fighters[$attacker]['party'] != 'Enemy')
    {
      $reporttext .= $fighters[$attacker]['name'] . " cannot use " . $skillInfo['skillname'] . ".<br>";
      return;
    }

    $buffStats = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
    $stats = eval("return ($buffStats);");
    if ($status[$attacker][$buff] < $stats)
      { $status[$attacker][$buff] = $stats; }
  }

  $reporttext .= $fighters[$attacker]['name'] . " uses " . $skillInfo['skillname'] . " and gains +" .
    $stats . " " . $skillInfo['type'] . "<br>";
}

function DoDamage($attacker, $skillInfo, $skill)
{
  global $fighters, $status, $totalgold, $totalloot, $reporttext, $map, $dungeonlevel;
  $defender = findNearestEnemy($attacker);
  if ($skillInfo['category'] == 'melee')
  {
    if((abs($fighters[$attacker]['x']-$fighters[$defender]['x']) + abs($fighters[$attacker]['y']-$fighters[$defender]['y'])) > 1)
    {
      $reporttext .= $fighters[$attacker]['name'] . " is out of range.<br>";
      return;
    }
  }

  $hid = $fighters[$attacker]['id'];
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $skillLevel = mysqli_fetch_assoc(mysqli_query($conn,"SELECT skilllevel FROM HeroSkills WHERE abilityid = '$skill' AND heroid = '$hid'"));
  mysqli_close($conn);
  if ($skillLevel == null && $fighters[$attacker]['party'] != 'Enemy')
  {
    $reporttext .= $fighters[$attacker]['name'] . " cannot use " . $skillInfo['skillname'] . ".<br>";
    return;
  }

  $damType = null;
  $armType = null;
  switch ($skillInfo['type'])
  {
    case "piercing damage":
      $damType = "pdam";
      $armType = "parm";
      break;
    case "slashing damage":
      $damType = "sdam";
      $armType = "sarm";
      break;
    case "bludgeoning damage":
      $damType = "bdam";
      $armType = "barm";
      break;
    case "arcane damage":
      $damType = "adam";
      $armType = "aarm";
      break;
    case "divine damage":
      $damType = "ddam";
      $armType = "darm";
      break;
    case "pure damage":
      break;
    case "{weapon type}":
      //@JAH  GET WEAPON TYPE
      break;
    default:
      //echo "Unhandled damage type " . $skillInfo['type'] . "<br>"; @JAH
      //return;
      break;
  }

  $damCat = null;
  switch ($skillInfo['category']) {
    case "melee":
      $damCat = "str";
      break;
    case "ranged":
      $damCat = "dex";
      break;
    case "magic":
      $damCat = "nce";
      break;
    case "heal":
      $damCat = "pie";
      break;
  }

  $dbDamage = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['effect']);
  $damage = eval("return ($dbDamage);");
  $totalPower = 100;
  $totalArmor = 1;
  $abilityScale = $fighters[$attacker][$damCat]+$status[$attacker][$damCat];
  if ($damType != null)
  {
    $totalPower = $fighters[$attacker][$damType]+$status[$attacker][$damType]+1;
    $totalArmor = $fighters[$defender][$armType]+$status[$defender][$armType]+1;
    $damage = ScaleDamage($damage, $abilityScale, $totalPower, $totalArmor);
  }
  $damage = ScaleDamage($damage, $abilityScale, $totalPower, $totalArmor);


  echo "<span style='color:blue;'>damage: </span>", $damage, " from ", $fighters[$attacker]['name'], " to ",
    $fighters[$defender]['name'], "<br>";

  $reporttext .= $fighters[$attacker]['name'] . " uses " . $skillInfo['skillname'] . " on " . $fighters[$defender]['name'] . " for " . $damage . " damage.<br>";
  $fighters[$defender]['hp'] -= $damage;

  if ($skillInfo['skillstatus'] != null)
  {
    switch ($skillInfo['skillstatus']) {
      case 'dot':
        $dbDot = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
        $dot = eval("return ($dbDot);");
        $dot = ScaleDamage($dot, $abilityScale, $totalPower, $totalArmor);
        if ($status[$defender]['hpreg'] < $dot)
        {
          echo "<span style='color:blue;'>damage: </span>", $dot, " over time from ", $fighters[$attacker]['name'], " to ", $status[$defender]['name'], "<br>";
          $reporttext .= $skillInfo['skillname'] . " causes " . $dot ." damage to " . $status[$defender]['name'] ."<br>";
          $status[$defender]['hpreg'] = -$dot;
        }
        break;
      case 'root':
        $root = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
        $rootDur = max(1, floor(eval("return ($root);")));
        if ($status[$defender]['root'] < $rootDur)
        {
          $reporttext .= $skillInfo['skillname'] . " roots " . $status[$defender]['name'] . "<br>";
          $status[$defender]['root'] = $rootDur;
        }
        break;
      case 'silence':
        $silence = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
        $silenceDur = max(1, floor(eval("return ($silence);")));
        if ($status[$defender]['silence'] < $silenceDur)
        {
          $reporttext .= $skillInfo['skillname'] . " silences " . $status[$defender]['name'] . "<br>";
          $status[$defender]['silence'] = $silenceDur;
        }
        break;
      case 'stun':
        $stun = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
        $stunDur = max(1, floor(eval("return ($stun);")));
        if ($status[$defender]['stun'] < $stunDur)
        {
          $reporttext .= $skillInfo['skillname'] . " stuns " . $status[$defender]['name'] . "<br>";
          $status[$defender]['stun'] = $stunDur;
        }
        break;
      default:
        $debuffs = explode('|', $skillInfo['skillstatus']);
        foreach ($debuffs as $debuff)
        {
          $debuffStats = str_replace("{skill level}", $skillLevel['skilllevel'], $skillInfo['duration']);
          $dstats = max(1, floor(eval("return ($debuffStats);")));
          if ($status[$defender][$debuff] < $dstats)
            { $status[$defender][$debuff] = $dstats; }
          }
        break;
    }
  }

  if($fighters[$defender]['hp'] < 1)
    { KillFighter($defender); }
}

function DevalueStatus()
{
  global $status;
  foreach ($status as $id => $a)
  {
    foreach ($status[$id] as $stat => $b)
    {
      if ($stat != 'id' && $stat != 'name')
      {
        if ($b >= 1)
          { $status[$id][$stat]--; }
        else if ($b <= -1)
          { $status[$id][$stat]++; }
      }
    }
  }
}

function ScaleDamage($baseDamage, $abilityScale, $totalPow, $totalArm)
{
  $damage = $baseDamage;
  if ($abilityScale != null)
  {
    $damage = log($abilityScale+1)*$damage;
    if ($totalPow/max(1, $totalArm) < 1)
    {
      $damage = $totalPow/max(1, $totalArm) * $damage;
    }
  }
  return max(1, floor($damage));
}

function KillFighter($dead)
{
  global $map, $fighters, $totalgold, $reporttext, $dungeonlevel;
  $fighters[$dead]['hp'] = 0;
  $map[$fighters[$dead]['y']][$fighters[$dead]['x']] = -1;
  $reporttext .= $fighters[$dead]['name'] . " dies.<br>";
  if($fighters[$dead]['party'] == "Enemy") {
    $totalgold += $fighters[$dead]['gold'];
    if(rand(1, 100) <= $fighters[$dead]['loot']) {
      $droppeditem = createItem($dungeonlevel);
      $dropstats = explode(",", $droppeditem);
      $totalloot[] = $droppeditem;
      $reporttext .= "<span style='color:yellow;'>" . $fighters[$dead]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "!</span><br>";
      echo "<span style='color:yellow;'>" . $fighters[$dead]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "</span><br>";
    }
  }
}

/*function doDamage($attacker, $verb, $defender, $damage) {
  global $fighters, $totalgold, $totalloot, $reporttext, $map, $dungeonlevel;
  $reporttext .= $fighters[$attacker]['name'] . " " . $verb . " " . $fighters[$defender]['name'] . " for " . $damage . " damage.<br>";
  $fighters[$defender]['hp'] -= $damage;
  if($fighters[$defender]['hp'] < 1) {
    $fighters[$defender]['hp'] = 0;
    $map[$fighters[$defender]['y']][$fighters[$defender]['x']] = -1;
    $reporttext .= $fighters[$defender]['name'] . " dies.<br>";
    if($fighters[$defender]['party'] == "Enemy") {
      $totalgold += $fighters[$defender]['gold'];
      if(rand(1, 100) <= 5) {
        $droppeditem = createItem($dungeonlevel);
        $dropstats = explode(",", $droppeditem);
        $totalloot[] = $droppeditem;
        $reporttext .= "<span style='color:yellow;'>" . $fighters[$defender]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "!</span><br>";
        echo "<span style='color:yellow;'>" . $fighters[$defender]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "</span><br>";
      }
/*      foreach(explode("||", $fighters[$defender]['loot']) as $loottable) {
        $possibledrop = explode("|", $loottable);
        if(rand(1,100) < $possibledrop[0]) {
          $dropstats = explode(",", $possibledrop[1]);
          $reporttext .= "<span style='color:yellow;'>" . $fighters[$defender]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "!</span><br>";
          echo "<span style='color:yellow;'>" . $fighters[$defender]['name'] . " drops " . getItemName(0, 0, 0, $dropstats[0], $dropstats[1], $dropstats[2], $dropstats[3], $dropstats[4], $dropstats[5]) . "</span><br>";
          $totalloot[] = $possibledrop[1];
        }
      }&/
    }
  }
}*/

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
    $color = "green";
    if($fighters[$turn]['hp'] / $fighters[$turn]['maxhp'] <= .67) { $color = "yellow"; }
    if($fighters[$turn]['hp'] / $fighters[$turn]['maxhp'] <= .33) { $color = "red"; }
    if($fighters[$turn]['hp'] / $fighters[$turn]['maxhp'] == 0) { $color = "white"; }
    $reportinitiative .= "<tr><td>" . $turnorder[1][$key] . "</td><td><span style='color:" . $color . "'>" . $fighters[$turn]['name'] . "</span></td><td>" . $fighters[$turn]['hp'] . "</td><td>" . $fighters[$turn]['mp'] . "</td></tr>";
  }
  $reportinitiative .= "</table></center>";
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

giveLoot();
$reportintro = addslashes($reportintro);
$reportinitiative = addslashes($reportinitiative);
$reportmap = addslashes($reportmap);
$reporttext = addslashes($reporttext);
echo "<span style='color:yellow;'>";
print_r($totalloot);
echo "</span>";
$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
//mysqli_query($conn, "DELETE FROM Party WHERE partyid = '$hero[party]'");
foreach ($partyfighters as $hero) {
  mysqli_query($conn, "INSERT INTO Reports (heroid, timestamp, dungeon, reportintro, reportinitiative, reportmap, reporttext) VALUES ('$hero[id]', '$cd', '$dungeonname', '$reportintro', '$reportinitiative', '$reportmap', '$reporttext')") or die(mysqli_error($conn));
  if ($hero['dungeonlevel'] <= $dungeonlevel)
  {
    mysqli_query($conn, "UPDATE Hero SET dungeonlevel = '$dungeonlevel' + 1 WHERE id = '$hero[id]'");
    //mysqli_query($conn, "UPDATE Hero SET cd = '$cd' WHERE id = '$hero[id]'");
    //mysqli_query($conn, "UPDATE Hero SET party = '0' WHERE id = '$hero[id]'");
  }
}
mysqli_close($conn);

//echo "<META http-equiv='refresh' content='0;URL=reports.php'>";

?>