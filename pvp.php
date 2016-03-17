<?php
  include "functions.php";

  $skill['skilllevel'] = 6;
  $skill['effect'] = "{skill level}/2";
  echo $skill['skilllevel'];
  echo "<br>";
  echo $skill['effect'];
  echo "<br>";
  //$skilleffect = eval("return (str_replace('{skill level}', $skill[skilllevel], $skill[effect]));");
  //echo $skilleffect;
  //echo "<br>";
  $skilleffect = str_replace('{skill level}', $skill['skilllevel'], $skill['effect']);
  echo $skilleffect;
  echo "<br>";
  $skilleffect = eval("return ($skilleffect);");
  echo $skilleffect;
?>