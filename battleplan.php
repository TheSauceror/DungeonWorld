<?php
include "checklogin.php";
include "menu.php";

$conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");

if(strpos($hero['tutorial'], 'battleplanintro') === false) {
  echo "<div class='alert'>Here's where you set your strategy for adventures. Choose what <a href='skills.php'><span class='red'>Skills</span></a> you want to use and when to use them. Once you do you can go on <a href='dungeons.php'><span class='red'>Adventures</span></a>.</div>";
  mysqli_query($conn,"UPDATE Hero SET tutorial = CONCAT(tutorial, 'battleplanintro|') WHERE id = '$cookie[0]'") or die(mysqli_error($conn));
}

if(isset($_POST['save'])) {
  foreach($_POST['plans'] as $plans) {
    if (array_key_exists('0', $plans) && array_key_exists('1', $plans))
    {
      if ($plans[1] == 'attack')
      {
        $attacks = [];
        foreach ($_POST['attack'] as $attack)
        {
          $attacks[] = $attack;
        }
        $plans[1] = implode(',', $attacks);
      }
      $battleplans[] = implode('|', $plans);
    }
  }
  if (isset($battleplans))
  {
    $battleplan = implode("||",$battleplans);
    mysqli_query($conn,"UPDATE Hero SET battleplan = '$battleplan' WHERE id = '$cookie[0]'");
    echo "<div class='alert'>Strategy saved!</div>";
  }
}

$currentplans = mysqli_fetch_assoc(mysqli_query($conn,"SELECT battleplan FROM Hero WHERE id = '$cookie[0]'"));

$currentplan = explode("||",$currentplans['battleplan']);
foreach($currentplan as $currents) {
  $current[] = explode('|',$currents);
}

$allskills = null;
$skillresults = mysqli_query($conn,"SELECT skillid, skillname FROM SkillList, HeroSkills WHERE heroID = '$cookie[0]' AND abilityid =skillid AND category!='heal'") or die(mysqli_error($conn));
while ($row = mysqli_fetch_assoc($skillresults))
{
  $allskills[] = $row;
}

$healskills = null;
$healresults = mysqli_query($conn,"SELECT skillid, skillname FROM SkillList, HeroSkills WHERE heroID = '$cookie[0]' AND abilityid =skillid AND category='heal'") or die(mysqli_error($conn));
while($row = mysqli_fetch_assoc($healresults))
{
  $healskills[] = $row;
}

mysqli_close($conn);

?>


<script>
window.onload=init;

function init()
{
  var heals = <?php echo json_encode($healskills); ?>;
  if (heals != null)
    {
      for (i=0; i<heals.length; i++)
      {
        addOption(document.getElementById('plans[0][1]'), heals[i]["skillname"], heals[i]["skillid"]);
        addOption(document.getElementById('plans[1][1]'), heals[i]["skillname"], heals[i]["skillid"]);
        addOption(document.getElementById('plans[2][1]'), heals[i]["skillname"], heals[i]["skillid"]);
        addOption(document.getElementById('plans[3][1]'), heals[i]["skillname"], heals[i]["skillid"]);
      }
    }

  var dbPlan = <?php echo json_encode($current); ?>;
  var attack_arr = new Array();
  for (i=0; i<dbPlan.length; i++)
  {
    switch(dbPlan[i][0])
    {
      case 'yourhpbelow33':
        var checkbox = document.getElementById('plans[0][0]');
        checkbox.checked = true;
        var dropdown = document.getElementById('plans[0][1]');
        dropdown.value = dbPlan[i][1];
        break;
      case 'yourhpbelow66':
        var checkbox = document.getElementById('plans[1][0]');
        checkbox.checked = true;
        var dropdown = document.getElementById('plans[1][1]');
        dropdown.value = dbPlan[i][1];
        break;
      case 'allyhpbelow33':
        var checkbox = document.getElementById('plans[2][0]');
        checkbox.checked = true;
        var dropdown = document.getElementById('plans[2][1]');
        dropdown.value = dbPlan[i][1];
        break;
      case 'allyhpbelow66':
        var checkbox = document.getElementById('plans[3][0]');
        checkbox.checked = true;
        var dropdown = document.getElementById('plans[3][1]');
        dropdown.value = dbPlan[i][1];
        break;
      case 'notnexttoenemy':
        var checkbox = document.getElementById('plans[4][0]');
        checkbox.checked = true;
        if (dbPlan[i][1] != 'moveclosertoenemy')
        {
          var dropdown = document.getElementById('plans[4][1]');
          dropdown.value = 'attack';
          attack_arr.length = 0;
          attack_arr = dbPlan[i][1].split(',');
        }
        break;
      case 'nexttoenemy':
        var checkbox = document.getElementById('plans[5][0]');
        checkbox.checked = true;
        if (dbPlan[i][1] != 'moveawayfromenemy')
        {
          var dropdown = document.getElementById('plans[5][1]');
          dropdown.value = 'attack';
          attack_arr.length = 0;
          attack_arr = dbPlan[i][1].split(',');
        }
        break;
    }
  }

  if (attack_arr.length > 0)
  {
    for (j=0; j<attack_arr.length; j++)
    {
      add();
      var attackSelect = document.getElementById("attack[" + j + "]");
      attackSelect.value = attack_arr[j];
    }
  }
  else
  {
    add();
  }
}

var num = 0;
function add() {
  var bform = document.getElementById('battleplanfrm');
  var newSkillList = document.createElement("select");
  newSkillList.name = "attack[" + num + "]";
  newSkillList.id = "attack[" + num + "]";
  bform.insertBefore(newSkillList, document.getElementById("buttonAdd"));

  var skills = <?php echo json_encode($allskills); ?>;
  if (skills != null)
  {
    for (i=0; i<skills.length; i++)
      { addOption(newSkillList, skills[i]["skillname"], skills[i]["skillid"]); }
  }
  num++;
}

function addOption(id, text, value)
{
  var option = document.createElement("option");
  option.text = text;
  option.value = value;
  id.add(option);
}
</script>


<form name='battleplan' id='battleplanfrm' action='battleplan.php' method='POST' class='parchment'>
  Attack Rotation:<br>
  <button id="buttonAdd" type='button' onclick='add()'>Add</button><br>

  <br><table>
    <tr>
    <tr>
      <td><input type='checkbox' name='plans[0][0]' id='plans[0][0]' value='yourhpbelow33'>Your HP is below 33%</td>
      <td>
        <select name='plans[0][1]' id='plans[0][1]'>
          <option value='moveawayfromenemy'>Move away from closest enemy</option>
        </select>
      </td>
    </tr>
      <td><input type='checkbox' name='plans[1][0]' id='plans[1][0]' value='yourhpbelow66'>Your HP is between 66% and 33%</td>
      <td>
        <select name='plans[1][1]' id='plans[1][1]'>
        <option value='moveawayfromenemy'>Move away from closest enemy</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><input type='checkbox' name='plans[2][0]' id='plans[2][0]' value='allyhpbelow33'>An ally's HP is below 33%</td>
      <td><select name='plans[2][1]' id='plans[2][1]'></select></td>
    </tr>
    <tr>
    <tr>
      <td><input type='checkbox' name='plans[3][0]' id='plans[3][0]' value='allyhpbelow66'>An ally's HP is between 66% and 33%</td>
      <td><select name='plans[3][1]' id='plans[3][1]'></select></td>
    </tr>
      <td><input type='checkbox' name='plans[4][0]' id='plans[4][0]' value='notnexttoenemy'>You are not next to enemy</td>
      <td><select name='plans[4][1]' id='plans[4][1]'>
          <option value='moveclosertoenemy'>Move towards closest enemy</option>
          <option value='attack'>Attack</option>
        </select>
      </td>
    </tr>
    <tr>
      <td><input type='checkbox' name='plans[5][0]' id='plans[5][0]' value='nexttoenemy'>You are next to enemy</td>
      <td><select name='plans[5][1]' id='plans[5][1]'>
          <option value='moveawayfromenemy'>Move away from closest enemy</option>
          <option value='attack'>Attack</option>
        </select></td>
    </tr>
  </table><br>

  <input type='submit' name='save' value='Save'></form>
</form>