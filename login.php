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

if(isset($_COOKIE["PHPRPG"])) {
  $cookie = explode("||",$_COOKIE["PHPRPG"]);
  $conn = mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $hero = mysqli_fetch_assoc(mysqli_query($conn,"SELECT pw FROM Hero WHERE id = '$cookie[0]'"));
  mysqli_close($conn);
  if($cookie[1] == $hero['pw']) {
    header('Location: index.php');
  }
}

function giveItem($pre, $base, $suf, $itemowner, $isequipped) {

  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $pre=mysqli_real_escape_string($conn, "$pre");
  $base=mysqli_real_escape_string($conn, "$base");
  $suf=mysqli_real_escape_string($conn, "$suf");
  $itempre = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$pre' AND slot = 'prefix'"));
  $itembase = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$base' AND slot != 'prefix' AND slot != 'suffix'"));
  $itemsuf = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM Itemstats WHERE name = '$suf' AND slot = 'suffix'"));

  $item[slot] = $itembase[slot];
  $item[sdam] = $itempre[sdam] + $itembase[sdam] + $itemsuf[sdam];
  $item[pdam] = $itempre[pdam] + $itembase[pdam]+ $itemsuf[pdam];
  $item[bdam] = $itempre[bdam] + $itembase[bdam] + $itemsuf[bdam];
  $item[sarm] = $itempre[sarm] + $itembase[sarm] + $itemsuf[sarm];
  $item[parm] = $itempre[parm] + $itembase[parm]+ $itemsuf[parm];
  $item[barm] = $itempre[barm] + $itembase[barm] + $itemsuf[barm];
  $item[hpreg] = $itempre[hpreg] + $itembase[hpreg] + $itemsuf[hpreg];
  $item[mpreg] = $itempre[mpreg] + $itembase[mpreg] + $itemsuf[mpreg];
  $item[des] = trim($itembase[des] . " " . $itempre[des] . " " . $itemsuf[des]);

  mysqli_query($conn,"INSERT INTO Item (pre, base, suf, des, owner, slot, equip, sdam, pdam, bdam, sarm, parm, barm, hpreg, mpreg) VALUES ('$pre', '$base', '$suf', '$item[des]', '$itemowner', '$item[slot]', '$isequipped', '$item[sdam]', '$item[pdam]', '$item[bdam]', '$item[sarm]', '$item[parm]', '$item[barm]', '$item[hpreg]', '$item[mpreg]')");

  mysqli_close($conn);

}

if(isset($_POST['name'],$_POST['pw'],$_POST['race'],$_POST['prof'])) {
  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd");
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $pw = sha1(mysqli_real_escape_string($conn, $_POST['pw']));
  $race = mysqli_real_escape_string($conn, $_POST['race']);
  $prof = mysqli_real_escape_string($conn, $_POST['prof']);
  if(!is_null(mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hero WHERE name = '$name'")))) {
    echo "Hero name already exists.";
  } else {

    $hpmult = 1;
    $mpmult = 1;
    switch($race) {
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
    switch($prof) {
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
    $maxhp = floor((1*5 + 1*3) * $hpmult);
    $maxmp = floor((1*5 + 1*3) * $mpmult);
    $initiative = 1*2 + 1;

    mysqli_query($conn, "INSERT INTO Hero (name, pw, race, prof, maxhp, hp, maxmp, mp, initiative) VALUES ('$name', '$pw', '$race', '$prof', '$maxhp', '$maxhp', '$maxmp', '$maxmp', '$initiative')");

    mysqli_close($conn);
    switch($prof) {
      case "Barbarian":
        giveItem("Rusty", "Greataxe", "", $name, 1);
        giveItem("", "Leather Gloves", "", $name, 1);
        giveItem("", "Leather Greaves", "", $name, 1);
        giveItem("", "Leather Boots", "", $name, 1);
        break;
      case "Mage":
        giveItem("", "Staff", "", $name, 1);
        giveItem("", "Spellbook", "", $name, 2);
        giveItem("", "Mage Robe", "", $name, 1);
        break;
      case "Archer":
        giveItem("Bent", "Shortbow", "", $name, 1);
        giveItem("", "Leather Armor", "", $name, 1);
        giveItem("", "Leather Gloves", "", $name, 1);
        giveItem("", "Leather Greaves", "", $name, 1);
        giveItem("", "Leather Boots", "", $name, 1);
        break;
      case "Priest":
        giveItem("Frail", "Mace", "", $name, 1);
        giveItem("", "Holy Symbol", "", $name, 2);
        giveItem("", "Priest Robe", "", $name, 1);
        break;
      case "Knight":
        giveItem("Rusty", "Long Sword", "", $name, 1);
        giveItem("Weak", "Wooden Shield", "", $name, 2);
        giveItem("", "Leather Armor", "", $name, 1);
        giveItem("", "Leather Gloves", "", $name, 1);
        giveItem("", "Leather Greaves", "", $name, 1);
        giveItem("", "Leather Boots", "", $name, 1);
      break;
      
    }
    setcookie("PHPRPG", $name."||".$pw, time()+60*60*24*365);
    echo "<META http-equiv='refresh' content='0;URL=index.php'>";
    exit();
  }
}
else if(isset($_POST['name'],$_POST['pw'])) {
  $conn=mysqli_connect("ucfsh.ucfilespace.uc.edu","piattjd","curtis1","piattjd") or die("#2013 - Lost connection to MySQL server at 'reading authorization packet', system error: 0<br>" . mysqli_error($conn));
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $pw = sha1(mysqli_real_escape_string($conn, $_POST['pw']));
  $hero = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM Hero WHERE name = '$name' AND pw = '$pw'"));
  if(is_null($hero)) {
    echo "Incorrect login";
  } else {
    setcookie("PHPRPG", $hero['id'] . "||" . $pw, time()+60*60*24*365);
    echo "<META http-equiv='refresh' content='0;URL=index.php'>";
    mysqli_close($conn);
    exit();
  }
}

echo "<form action='login.php' method='post'>
<fieldset>
<legend>New Account</legend>
Name: <input type='text' name='name' required>
<br>Password: <input type='password' name='pw' required>
<br>Race: <select name='race' required onchange='changestats(this.value)'><option value='Human'>Human</option><option value='Elf'>Elf</option><option value='Orc'>Orc</option><option>Dwarf</option></select>
<span id='racestats'>+10% HP, +10% MP</span>
<br>Profession: <select name='prof' required onchange='changestats(this.value)'><option value='Barbarian'>Barbarian</option><option value='Archer'>Archer</option><option value='Mage'>Mage</option><option>Knight</option><option>Priest</option></select>
<span id='profstats'>+30% HP, -15% MP</span>
<br><input type='submit'>
</fieldset></form>";

echo "<br><br>";

echo "<form action='login.php' method='post'>
<fieldset>
<legend>Login</legend>
Name: <input type='text' name='name' required>
<br>Password: <input type='password' name='pw' required>
<br><input type='submit'>
</fieldset></form>";


?>