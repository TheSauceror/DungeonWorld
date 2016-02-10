<?php
  $hp = 10;
  $mp = 10;
  if(!empty($_GET['hp'])) $hp = $_GET['hp'];
  if(!empty($_GET['mp'])) $mp = $_GET['mp'];
  $races = array("Elf","Orc","Human");
  $classes = array("Mage","Warrior","Archer");
  foreach($races as $race) {
    foreach($classes as $class) {
      $hpmult = 1;
      $mpmult = 1;
      switch ($race) {
      	case 'Elf':
      	  $hpmult -= 0.15;
      	  $mpmult += 0.3;
          break;
      	case 'Orc':
      	  $hpmult += 0.3;
      	  $mpmult -= 0.15;
          break;
      	case 'Human':
      	  $hpmult += 0.1;
      	  $mpmult += 0.1;
          break;
      }
      switch ($class) {
      	case 'Mage':
      	  $hpmult -= 0.15;
      	  $mpmult += 0.3;
          break;
      	case 'Warrior':
      	  $hpmult += 0.3;
      	  $mpmult -= 0.15;
          break;
      	case 'Archer':
      	  $hpmult += 0.1;
      	  $mpmult += 0.1;
          break;
      }
      echo $race . " " . $class . " -  HP: " . floor($hp*$hpmult) . "   MP: " . floor($mp*$mpmult) . "<br>";
    }
    echo "<br>";
  }
?>