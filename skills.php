<head><title>Adventures Of Eld - Skills</title></head>

<script>
function updateSkill(sID) {
  document.getElementById('skillID').value = sID;
  document.getElementById('skillfrm').submit();
}
</script>

<?php

include "checklogin.php";
include "menu.php";
include "functions.php";

ini_set("display_errors", 1);

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(strpos($hero['tutorial'], 'skillsintro') === false) {
  echo "<div class='alert'>Here's where you can train your repertoire of <span class='red'>skills</span>. Remember to update your <a href='battleplan.php'><span class='red'>Strategy</span></a> with your newfound power!</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'skillsintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

echo "<div class='parchment'><h3>Skills</h3>";

if(isset($_POST['skill'])) {
  $sID = mysqli_real_escape_string($conn, $_POST['skill']);
  //these should be combined
  $heroskill = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM HeroSkills WHERE heroid = '$cookie[0]' AND abilityid = '$sID'"));
  $skillinfo = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM SkillList WHERE skillid = '$sID'"));
  if(is_null($heroskill)) {
    $cost = pow($skillinfo['tier'] - 1, 3) * 50;
    $query = "INSERT INTO HeroSkills (heroid, abilityid, skilllevel) VALUES ('$cookie[0]', '$sID', '1')";
  } else {
    $cost = pow($heroskill['skilllevel'], 2) * 50 * $skillinfo['tier'];
    $query = "UPDATE HeroSkills SET skilllevel = (skilllevel + 1 ) WHERE abilityid = '$sID' AND heroid = '$cookie[0]'";
  }
  if($hero['gold'] < $cost) {
    echo "<div class='alert'>Not enough gold!</div>";
  } else {
    mysqli_query($conn,"$query") or die(mysqli_error($conn));
    mysqli_query($conn,"UPDATE Hero SET gold = gold - $cost WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
    
    $hero['gold'] -= $cost;
  }
}

$skilllist = mysqli_query($conn,"SELECT * FROM SkillList WHERE skillprof LIKE '%$hero[prof]%' ORDER BY tier ASC");
//$skilllist = mysqli_query($conn,"SELECT * FROM SkillList LEFT JOIN HeroSkills ON SkillList.skillid = HeroSkills.abilityid WHERE HeroSkills.heroid = '$hero[id]' OR (SkillList.skillprof LIKE '%$hero[prof]%' AND HeroSkills.heroid IS NULL) ORDER BY SkillList.tier ASC");

$skills = [];
$maxtier = 0;
while($row = mysqli_fetch_assoc($skilllist)) {
  $heroskill = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM HeroSkills WHERE heroid = '$cookie[0]' AND abilityid = '$row[skillid]'"));
  $row['skilllevel'] = $heroskill['skilllevel'];
  if($row['tier'] > $maxtier && $row['skilllevel'] != NULL) { $maxtier = $row['tier']; }
  $skills[] = $row;
}

mysqli_close($conn);

echo "<h3>Available gold: " . $hero['gold'] . "</h3>";
echo "<table><tr><th>Skill</th><th>Tier</th><th>Level</th><th>MP Cost</th><th>Effect</th><th>Training Cost</th></tr>";
foreach ($skills as $skill) {
  if($skill['tier'] > $maxtier + 1) { continue; }
  $cost = pow($skill['skilllevel'], 2) * 50 * $skill['tier'];
  if($cost == 0) { $cost = pow($skill['tier'] - 1, 3) * 50; }
  if($skill['skilllevel'] == NULL) { $skill['skilllevel'] = 0; }
  $skilldes = "";
  /*if($skill['category'] == "heal") {
    $skilldes .= "Heals for ";
  } else if($skill['category'] == "buff") {
    $skilldes .= "Buffs an ally for ";
  } else {
    $skilldes .= "Does ";
  }*/

  $skilleffect = str_replace("{skill level}", $skill['skilllevel'], $skill['effect']);
  $skilleffect = eval("return ($skilleffect);");
  $skillcat = null;
  switch ($skill['category']) {
    case 'melee':
      $skillcat = 'str';
      $skilleffect = max(1, floor(log($hero[$skillcat]+1)*$skilleffect));
      $skilldes .= "Does " . $skilleffect . " " . $skill['type'] . " ";
      break;
    case 'ranged':
      $skillcat = 'dex';
      $skilleffect = max(1, floor(log($hero[$skillcat]+1)*$skilleffect));
      $skilldes .= "Does " . $skilleffect . " " . $skill['type'] . " ";
      break;
    case 'magic':
      $skillcat = 'nce';
      $skilleffect = max(1, floor(log($hero[$skillcat]+1)*$skilleffect));
      $skilldes .= "Does " . $skilleffect . " " . $skill['type'] . " ";
      break;
    case 'heal':
      $skillcat = 'pie';
      $skilleffect = max(1, floor(log($hero[$skillcat]+1)*$skilleffect));
      $skilldes .= "Heals for  " . $skilleffect;
      break;
    case 'buff':
      $skilldes .= "Buffs ";
      break;
    default:
      break;
  }

  switch ($skill['skillstatus']) {
    case '':
      break;
    case 'dot':
      $skilldur = str_replace("{skill level}", $skill['skilllevel'], $skill['duration']);
      $skilldur = eval("return ($skilldur);");
      $skilldur = max(1, floor(log($hero[$skillcat]+1)*$skilldur));
      $skilldes .= " and " . $skilldur . " damage over time";
      break;
    case 'root':
      $skilldur = $skill['duration'];
      $skilldes .= "also roots the target for " . $skilldur . " turns";
      break;
    case 'stun':
      $skilldur = $skill['duration'];
      $skilldes .= "also stuns the target for " . $skilldur . " turns";
      break;
    case 'silence':
      $skilldur = $skill['duration'];
      $skilldes .= "also silences the target for " . $skilldur . " turns";
      break;
    default:
      $skilldur = str_replace("{skill level}", $skill['skilllevel'], $skill['duration']);
      $skilldur = eval("return ($skilldur);");
      $skilldes .= $skill['skillstatus'] . " by " . $skilldur;
  }

  //$skilltargets = str_replace("{skill level}", $skill['skilllevel'], $skill['targets']);
  //$skilldes .= " to " . $skilltargets . " target";
  //if($skill['targets'] > 1) { $skilldes .= "s"; }
  //if($skill['skillstatus'] != "") {
  //  $skilldes .= " and " . $skill['skillstatus'];
  //}

  echo "<tr><td>$skill[skillname]</td><td>$skill[tier]</td><td>$skill[skilllevel]</td><td>$skill[cost]</td><td>$skilldes</td><td><a href='javascript:updateSkill(\"$skill[skillid]\");'>$cost gold</a></td></tr>";
}
echo "</table></div>";
echo "<form name='skillfrm' id='skillfrm' method='POST' action='skills.php'><input name='skill' type='hidden' value='' id='skillID'></form>";

?>