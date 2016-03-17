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

echo "<h1>$hero[name]'s Skills</h1>";

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
    echo "Not enough gold";
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
echo "<table class='parchment'><tr><th>Skill</th><th>Tier</th><th>Level</th><th>MP Cost</th><th>Effect</th><th>Training Cost</th></tr>";
foreach ($skills as $skill) {
  if($skill['tier'] > $maxtier + 1) { continue; }
  $cost = pow($skill['skilllevel'], 2) * 50 * $skill['tier'];
  if($cost == 0) { $cost = pow($skill['tier'] - 1, 3) * 50; }
  if($skill['skilllevel'] == NULL) { $skill['skilllevel'] = 0; }
  $skilldes = "";
  if($skill['category'] == "heal") {
    $skilldes .= "Heals for ";
  } else if($skill['category'] == "buff") {
    $skilldes .= "Buffs an ally for ";
  } else {
    $skilldes .= "Does ";
  }
  $skilleffect = str_replace("{skill level}", $skill['skilllevel'], $skill['effect']);
  $skilleffect = eval("return ($skilleffect);");
  $skilldes .= $skilleffect . " " . $skill['type'];
  $skilldes .= " to " . $skill['targets'] . " target";
  if($skill['targets'] > 1) { $skilldes .= "s"; }
  if($skill['skillstatus'] != "") { $skilldes .= " and " . $skill['skillstatus']; }

  echo "<tr><td>$skill[skillname]</td><td>$skill[tier]</td><td>$skill[skilllevel]</td><td>$skill[cost]</td><td>$skilldes</td><td><a href='javascript:updateSkill(\"$skill[skillid]\");'>$cost gold</a></td></tr>";
}
echo "</table>";
echo "<form name='skillfrm' id='skillfrm' method='POST' action='skills.php'><input name='skill' type='hidden' value='' id='skillID'></form>";

?>