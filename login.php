<head><link href="main.css" rel="stylesheet" type="text/css" /></head>
<head><title>Adventures Of Eld - Login</title></head>

<script>
function changestats(change) {
  if(change == "Elf") { document.getElementById("racestats").innerHTML = "-15% HP, +30% MP"; }
  if(change == "Orc") { document.getElementById("racestats").innerHTML = "+20% HP, -10% MP"; }
  if(change == "Human") { document.getElementById("racestats").innerHTML = "+10% HP, +10% MP"; }
  if(change == "Dwarf") { document.getElementById("racestats").innerHTML = "+30% HP, -15% MP"; }
  if(change == "Mage") { document.getElementById("profstats").innerHTML = "-15% HP, +30% MP"; }
  if(change == "Barbarian") { document.getElementById("profstats").innerHTML = "+30% HP, -15% MP"; }
  if(change == "Archer") { document.getElementById("profstats").innerHTML = "+10% HP, +10% MP"; }
  if(change == "Knight") { document.getElementById("profstats").innerHTML = "+20% HP, -5% MP"; }
  if(change == "Priest") { document.getElementById("profstats").innerHTML = "-5% HP, +20% MP"; }
}
</script>

<?php

ini_set("display_errors", 1);

include "functions.php";

//echo "<div class='left'><img src='AdventuresOfEld.png' height='50%' width='50%'></div>";

if(isset($_COOKIE["DungeonsOfEld"])) {
  $cookie = explode("||",$_COOKIE["DungeonsOfEld"]);
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT pw FROM Hero WHERE id = '$cookie[0]'"));
  mysqli_close($conn);
  if($cookie[1] == $hero['pw']) {
    header('Location: profile.php');
  }
}

if(isset($_POST['name'], $_POST['pw'], $_POST['race'], $_POST['prof'])) {
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $pw = sha1(mysqli_real_escape_string($conn, $_POST['pw']));
  $race = mysqli_real_escape_string($conn, $_POST['race']);
  $prof = mysqli_real_escape_string($conn, $_POST['prof']);
  if(!is_null(mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hero WHERE name = '$name'")))) {
    echo "<div class='alert'>Hero name already exists!</div>";
  } else {
    mysqli_query($conn, "INSERT INTO Hero (name, pw, race, prof, gold, battleplan, tutorial) VALUES ('$name', '$pw', '$race', '$prof', '250', 'notnexttoenemy|7||nexttoenemy|7', '')");
    $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Hero WHERE name = '$name' AND pw = '$pw'"));
    calculateHPMPInit($hero['id']);
    switch($prof) {
      case "Barbarian":
        giveItem($hero['id'],1,16,1,15,2,0,0);
        giveItem($hero['id'],1,3,1,0,0,0,0);
        giveItem($hero['id'],1,4,1,0,0,0,0);
        giveItem($hero['id'],1,5,1,0,0,0,0);
        break;
      case "Mage":
        giveItem($hero['id'],1,7,1,0,0,0,0);
        giveItem($hero['id'],2,9,1,0,0,0,0);
        giveItem($hero['id'],1,6,1,0,0,0,0);
        break;
      case "Archer":
        giveItem($hero['id'],1,15,1,17,1,0,0);
        giveItem($hero['id'],1,4,1,0,0,0,0);
        break;
      case "Priest":
        giveItem($hero['id'],1,21,1,22,1,0,0);
        giveItem($hero['id'],2,20,1,0,0,0,0);
        giveItem($hero['id'],1,6,1,0,0,0,0);
        break;
      case "Knight":
        giveItem($hero['id'],1,11,1,15,1,0,0);
        giveItem($hero['id'],2,14,1,0,0,0,0);
        giveItem($hero['id'],1,2,1,0,0,0,0);
        break;      
    }
    mysqli_query($conn,"INSERT INTO HeroSkills (heroid, abilityid, skilllevel) VALUES ('$hero[id]', '7', '1')") or die(mysqli_error($conn));
    setcookie("DungeonsOfEld", $hero['id'] . "||" . $pw, time()+60*60*24*365);
    echo "<META http-equiv='refresh' content='0;URL=profile.php'>";
    mysqli_close($conn);
    exit();
  }
} else if(isset($_POST['name'], $_POST['pw'])) {
  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd") or die("#2013 - Lost connection to MySQL server at 'reading authorization packet', system error: 0<br>" . mysqli_error($conn));
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $pw = sha1(mysqli_real_escape_string($conn, $_POST['pw']));
  $hero = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hero WHERE name = '$name' AND pw = '$pw'"));
  if(is_null($hero)) {
    echo "<div class='alert'>Incorrect login!</div>";
  } else {
    setcookie("DungeonsOfEld", $hero['id'] . "||" . $pw, time()+60*60*24*365);
    echo "<META http-equiv='refresh' content='0;URL=profile.php'>";
  }
  mysqli_close($conn);
}

echo "<table class='width100 height100'><tr><td rowspan='2' style='vertical-align:middle;' class='width50'><img src='AdventuresOfEld.png'></td><td style='height:50%;vertical-align:middle;'>";
//echo "<table><tr><td colspan='2' style='background-color:red;'><img src='AdventuresOfEld.png'></td></tr><tr><td style='background-color:blue;'>";
//echo "<table><tr><td style='vertical-align:middle;'>";

echo "<div class='parchment'><h3>New Account</h3>
<form action='login.php' method='post'>
Name: <input type='text' name='name' required>
<br>Password: <input type='password' name='pw' required>
<br>Race: <select name='race' required onchange='changestats(this.value)'><option value='Human'>Human</option><option value='Elf'>Elf</option><option value='Orc'>Orc</option><option>Dwarf</option></select>
<br><span id='racestats'>+10% HP, +10% MP</span>
<br>Profession: <select name='prof' required onchange='changestats(this.value)'><option value='Barbarian'>Barbarian</option><option value='Archer'>Archer</option><option value='Mage'>Mage</option><option>Knight</option><option>Priest</option></select>
<br><span id='profstats'>+30% HP, -15% MP</span>
<br><input type='submit' value='Create Account'>
</form></div>";

echo "</td></tr><tr><td style='height:50%;vertical-align:middle;'>";
//echo "</td><td style='background-color:green;'>";
//echo "</td><td style='vertical-align:middle;'><img src='AdventuresOfEld.png'></td><td style='vertical-align:middle;'>";

echo "<div class='parchment'><h3>Login</h3>
<form action='login.php' method='post'>
Name: <input type='text' name='name' required>
<br>Password: <input type='password' name='pw' required>
<br><input type='submit' value='Login'>
</form></div>";

echo "</td></tr></table>";

?>