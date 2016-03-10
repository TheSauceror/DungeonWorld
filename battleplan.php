<?php
include "checklogin.php";

if(isset($_GET['plans'])) {
  foreach($_GET['plans'] as $plans) {
    $battleplans[] = implode('|',$plans);
  }
  $battleplan = implode("||",$battleplans);
  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  mysqli_query($conn,"UPDATE Hero SET battleplan = '$battleplan' WHERE id = '$cookie[0]'");
  mysqli_close($conn);
}

$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$currentplans = mysqli_fetch_assoc(mysqli_query($conn,"SELECT battleplan FROM Hero WHERE id = '$cookie[0]'"));
mysqli_close($conn);

$currentplan = explode("||",$currentplans['battleplan']);
foreach($currentplan as $currents) {
  $current[] = explode('|',$currents);
}

print_r($current);
echo "<br>";

// Get Battle Scripts
$battlecsripts = null;
$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$battleresults = mysqli_query($conn,"SELECT scriptID, name FROM BattleScript WHERE owner = '$cookie[0]'")
  or die(mysqli_error($conn));
while($row = mysqli_fetch_assoc($battleresults))
{
  $battlecsripts[] = $row;
}
mysqli_close($conn);
print_r($battlecsripts);

// Get heal skills
$healskills = null;
$conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
$healresults = mysqli_query($conn,"SELECT skillid, skillname FROM SkillList, HeroSkills 
  WHERE heroID = '$cookie[0]' AND abilityid=skillid AND category='heal'") or die(mysqli_error($conn));
while($row = mysqli_fetch_assoc($healresults))
{
  $healskills[] = $row;
}
mysqli_close($conn);
print_r($healskills);
?>

<script>

var num = 1;
function add() {
  var bform = document.getElementById('battleplanfrm');

  var selectFirst = document.createElement("select");
  selectFirst.name = "plans[" + num + "][0]";
  selectFirst.id = "plans[" + num + "][0]";
  selectFirst.innerHTML = "  <option value=''>Select an option</option><option value='yourhpbelow66'>When your HP is between 66% and 33%</option><option value='yourhpbelow33'>When your HP is between 33% and 0%</option><option value='allyhpbelow66'>When an ally's HP is between 66% and 33%</option><option value='allyhpbelow33'>When an ally's HP is between 33% and 0%</option><option value='notnexttoenemy'>When not next to an enemy</option><option value='nexttoenemy'>When next to an enemy</option>";
  selectFirst.setAttribute('onchange', 'change(this, ' + num + ', 1)');
  bform.insertBefore(selectFirst, document.getElementById("buttons"));

  var selectSecond = document.createElement("select");
  selectSecond.name = "plans[" + num + "][1]";
  selectSecond.id = "plans[" + num + "][1]"; 
  selectSecond.innerHTML = "<select name='plans[0][1]' id='plans[0][1]'></select><br />";
  bform.insertBefore(selectSecond, document.getElementById("buttons"));  
  bform.insertBefore(document.createElement("br"), document.getElementById("buttons"));
  num++;
}

function addOption(id,text,value) {
  var option = document.createElement("option");
  option.text = text
  option.value = value;
  id.add(option);
}

function change(from, row, col) {
  var next = document.getElementById('plans[' + row + '][' + col + ']');
  while (next.options.length > 0) {
    next.remove(0);
  }
  switch(from.value) {
    case "yourhpbelow66":
      addOption(next, "Move closer to an enemy", "moveclosertoenemy");
      addOption(next, "Move away from the enemy", "moveaway");
      var heals = <?php echo json_encode($healskills); ?>;
      if (heals != null)
      {
        for (i=0; i<heals.length; i++)
        {
         addOption(next, heals[i]["skillname"], heals[i]["skillid"]);
        }
      }
      break;
  	case "yourhpbelow33":
      addOption(next, "Move closer to an enemy", "moveclosertoenemy");
  	  addOption(next, "Move away from the enemy", "moveaway");
      var heals = <?php echo json_encode($healskills); ?>;
      if (heals != null)
      {
        for (i=0; i<heals.length; i++)
        {
         addOption(next, heals[i]["skillname"], heals[i]["skillid"]);
        }
      }
  	  break;
    case "allyhpbelow66":
      var heals = <?php echo json_encode($healskills); ?>;
      if (heals != null)
      {
        for (i=0; i<heals.length; i++)
        {
         addOption(next, heals[i]["skillname"], heals[i]["skillid"]);
        }
      }
      break;    
  	case "allyhpbelow33":
      var heals = <?php echo json_encode($healskills); ?>;
      if (heals != null)
      {
        for (i=0; i<heals.length; i++)
        {
         addOption(next, heals[i]["skillname"], heals[i]["skillid"]);
        }
      }
  	  break;
  	case "notnexttoenemy":
  	  addOption(next, "Move closer to an enemy", "moveclosertoenemy");
      var bscripts = <?php echo json_encode($battlecsripts); ?>;
      if (bscripts != null)
      {
        for (i=0; i<bscripts.length; i++)
        {
          addOption(next, bscripts[i]["name"], bscripts[i]["number"]);
        }
      }
  	  break;
  	case "nexttoenemy":
      addOption(next, "Move away from the enemy", "moveaway");
      var bscripts = <?php echo json_encode($battlecsripts); ?>;
      if (bscripts != null)
      {
        for (i=0; i<bscripts.length; i++)
        {
          addOption(next, bscripts[i]["name"], bscripts[i]["number"]);
        }
      }
  	  break;
    default:
      var bform = document.getElementById("battleplanfrm");
      bform.removeChild(document.getElementById('plans[' + row + '][0]'));
      bform.removeChild(document.getElementById('plans[' + row + '][1]'));
      break;
  }
}
</script>

<form name='battleplan' id='battleplanfrm' action='battleplan.php' method='POST'>
  <select name='plans[0][0]' id='plans[0][0]' onchange='change(this,0,1);'>
  <option value=''>Select an option</option>
  <option value='yourhpbelow66'>When your HP is between 66% and 33%</option>
  <option value='yourhpbelow33'>When your HP is between 33% and 0%</option>
  <option value='allyhpbelow66'>When an ally's HP is between 66% and 33%</option>
  <option value='allyhpbelow33'>When an ally's HP is between 33% and 0%</option>
  <option value='notnexttoenemy'>When not next to an enemy</option>
  <option value='nexttoenemy'>When next to an enemy</option>
  </select>
  <select name='plans[0][1]' id='plans[0][1]'></select>
  <br>
  <button id="buttons" type='button' onclick='add()'>Add</button><input type='submit' value='Save'></form>
</form>
