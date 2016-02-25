<?php
	include "checklogin.php";
?>
<h2>Planned Updates</h2>
<ul>
	<li>Make an enemy editor dev tool</li>
	<li>Make a room generator dev tool</li>
	<li>Make a room editor dev tool</li>
	<li>Make a dungeon generator dev tool</li>
	<li>Make a item generator dev tool (will probably need 4: base, prefix, suffix, and one to put them all together) - <strong>use the last for hero creation and battle</strong></li>
	<li>Make a item editor dev tool</li>
	<li>Make a party finder</li>
</ul>
<hr width='33%'>
<h2>Known Bugs</h2>
<ul>
	<li>Dungeons don't end on failure correctly</li>
	<li>Fix hero battleplan</li>
	<li>Fix market</li>
	<li>Need to move messages from GET to POST</li>
	<li>Need to fix sendmessage and replying</li>
	<li>Apostrophes break dungeon names</li>
	<li>Hero creation doesn't work because item giving is broken</li>
	<li>Need to change rooms to list enemies by id</li>
</ul>
<hr width='33%'>
<h2>February 24, 2016</h2>
<ul>
	<li>Changed reports to show up after the dungeon is completed</li>
	<li>Started setting up the market</li>
	<li>Changed itemlist to show your inventory</li>
	<li>Organized database better</li>
	<li>Changed 'experience' to 'gold'</li>
</ul>
<h2>February 17, 2016</h2>
<ul>
	<li>Having no equipped items now correctly displays item stats as 0</li>
	<li>Stats from items can no longer be negative</li>
	<li>Gave item stats to enemies</li>
	<li>Updated enemylist and loadenemy to show item stats</li>
</ul>
<h2>February 15, 2016</h2>
<ul>
	<li>Items show up again and add to stats correctly</li>
	<li>Herolist shows all heroes now</li>
</ul>
<h2>February 13, 2016</h2>
<ul>
	<li>Dungeons shouldn't  reach the max text size and freeze anymore</li>
	<li>Fixed intelligence</li>
	<li>Race/prof attribute %'s are precalculated before battle</li>
</ul>
<h2>February 11, 2016</h2>
<ul>
	<li>Started improving report UI</li>
	<li>Seperated battle report into divs</li>
	<li>Fixed report image titles</li>
</ul>
<h2>February 10, 2016</h2>
<ul>
	<li>Started changing database to get everything by ID instead of name</li>
	<li>Started crying 'cause I have to redo the entire database :(</li>
	<li>Ragequit 'cause I didn't realize I messed up halfway through redoing it all :(</li>
	<li>Changed attributes</li>
</ul>
<h2>February 9, 2016</h2>
<ul>
	<li>Fixed the turn order to read initiative from database</li>
	<li>***FIXED THE PARTY FROM REGAINING MAXHP EVERY ROOM***</li>
	<li>Made an enemy generator form</li>
</ul>
<h2>February 7, 2016</h2>
<ul>
	<li>Put maxhp/hp/maxmp/mp/initiative in enemy database</li>
	<li>Updated enemy search to pull maxhp/maxmp/initiative from database</li>
	<li>Updated enemy list with maxhp/maxmp/initiative</li>
	<li>Updated enemy database with maxhp/hp/maxmp/mp/initiative</li>
</ul>
<h2>February 5, 2016</h2>
<ul>
	<li>Put maxhp/hp/maxmp/mp/initiative in hero database</li>
	<li>Probably added them to character generation also. Don't think we can test it until items are fixed</li>
	<li>Updated hero search to pull maxhp/maxmp/initiative from database</li>
	<li>Updated hero list with maxhp/maxmp/initiative</li>
</ul>
<h2>February 2, 2016</h2>
<ul>
	<li>Changed dungeon cooldown to 10 minutes/room from 1 hour/room</li>
	<li>Added distinct floor options for rooms</li>
	<li>Fixed room counting in dungeons</li>
	<li>Added the Whiteshire Abbey dungeon</li>
</ul>
<h2>January 29, 2016</h2>
<ul>
	<li>Started recording patch notes</li>
	<li>Moved home.php to index.php</li>
	<li>Moved images to images folder</li>
	<li>Fixed image urls</li>
	<li>Fixed unit image settings to pull from profession instead of name</li>
</ul>