<?php
include "checklogin.php";

// Display hero's skills avaiable for BattleScript
$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$listSkills = mysqli_query($conn,"SELECT skillname FROM HeroSkills, SkillList WHERE heroid = '$cookie[0]' AND abilityid = skillid") 
  or die(mysqli_error($conn));
while($row = mysqli_fetch_assoc($listSkills)) {
  print_r($row['skillname']);
  echo "<br>";
}
mysqli_close($conn);

echo "<br>";

// Display current BattleScript for hero
$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$currentscript = mysqli_query($conn,"SELECT name, content FROM BattleScript WHERE owner = '$cookie[0]'")
  or die(mysqli_error($conn));
while($row = mysqli_fetch_assoc($currentscript))
{
  print_r($row['name']);
  print_r(": ");
  $currentplan = explode("|",$row['content']);
  foreach ($currentplan as $curr)
  {
    $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
    $display = mysqli_fetch_assoc(mysqli_query($conn,"SELECT skillname FROM SkillList WHERE skillid = '$curr'"));
    mysqli_close($conn);
    print_r($display['skillname']);
    print_r(" | ");
  }
  echo "<br>";
}
?>

<h1>Battle Script</h1>
<form name='battlescriptfrm' id='bsfrm' method='POST' action='battlescript.php'>
  <!--
  Table listing all skills, effects, damages, costs, combos, etc available to hero
  Similar to battle plan with drop select options filled with all skills
  Add button to add more selects
  Option text is skill name
  Option value is skill id
  Save gets selected options skill id's, implodes using |, saves to BattleScript table under heroID using name
  -->
  <br>
  <input type='submit' value='Add Battle Script'>
</form>