<?php
	include "checklogin.php";
?>
<h2>Planned Updates</h2>
<ul>
	<li>Make an enemy editor form</li>
	<li>Make a room generator form</li>
	<li>Make a room editor form</li>
	<li>Make a dungeon generator form</li>
</ul>
<hr width='33%'>
<h2>Known Bugs</h2>
<ul>
	<li>Hero list only shows heroes that are in a party</li>
	<li>Hero database has 0's for maxhp/hp/maxmp/mp/initiative</li>
	<li>Enemy professions aren't showing up</li>
	<li>Right now, race/prof attribute %'s are all over the places and static. Should consolidate them so we can update them if we need to balance later</li>
	<li>Fix hero battleplan</li>
	<li>Having an 'int' column is messing everything up. Need to change it to 'nce'</li>
	<li>Fix items. Then market</li>
	<li>Need to move messages from GET to POST</li>
	<li>Need to fix sendmessage and replying</li>
</ul>
<hr width='33%'>
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