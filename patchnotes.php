<head><title>Adventures Of Eld - Patch Notes</title></head>

<?php
	include "checklogin.php";
	include "menu.php";
?>

<div class="parchment">
<h2>Planned Updates</h2>
<ul>
	<li>Social Patch - Expand on Guild system, continue to improve party finder</li>
	<li>Items Expansion - Add primary stats to items, unique and powerful passives</li>
	<li>PvP Patch - New PvP mode, Elo and level based matchmaking, introduce PvP rewards</li>
	<li>Skills Patch - Create skill tree and specialization, passive skills, more depth to existing skills</li>
</ul>
</div>
<div class="parchment">
<h2>For Beta Testing</h2>
<ul>
	<li>Your characters might be messed with a little so we can test features</li>
	<li>Adventures don't redirect to Reports</li>
	<li>Parties aren't disbanded after Adventures</li>
	<li>Adventure cooldown is removed</li>
</ul>
</div>
<div class="parchment">
<h2>Known Bugs</h2>
<ul>
	<li>Adventures don't end on failure correctly</li>
	<li>Messages only work by sending to Hero ID, not by Hero name</li>
	<li>All Reports show up as red, even when they didn't fail</li>
	<li>Adventures unlock the next adventure level on failure</li>
</ul>
</div>
<div class="parchment">
<h2>April 4, 2016</h2>
<ul>
	<li>Two-handed items now use both hands</li>
	<li>Max Gold increments</li>
</ul>
<h2>March 26, 2016</h2>
<ul>
	<li>Added feedback system</li>
	<li>Added "Defend Against the Rat Invasion" Adventure</li>
</ul>
<h2>March 24, 2016</h2>
<ul>
	<li>Added tutorial</li>
	<li>Added "Clear Out The Rat Nest Adventure"</li>
</ul>
<h2>March 21, 2016</h2>
<ul>
	<li>Added party system</li>
</ul>
<h2>March 17, 2016</h2>
<ul>
	<li>Added guilds</li>
	<li>Implemented skills</li>
	<li>Added status effects</li>
</ul>
<h2>March 13, 2016</h2>
<ul>
	<li>Added skills</li>
	<li>Updated profile</li>
	<li>Increased attribute significance</li>
</ul>
<h2>March 9, 2016</h2>
<ul>
	<li>Updated UI</li>
	<li>Added adventures levels/types/tiers</li>
</ul>
<h2>March 7, 2016</h2>
<ul>
	<li>Added sorting to market</li>
</ul>
<h2>March 5, 2016</h2>
<ul>
	<li>Fix item creation</li>
	<li>Fixed hero creation</li>
</ul>
<h2>February 29, 2016</h2>
<ul>
	<li>Removed function redundancy</li>
	<li>Isolated login check</li>
	<li>Show adventure currently being explored</li>
	<li>Improved item descriptions</li>
</ul>
<h2>February 29, 2016</h2>
<ul>
	<li>Changed rooms to list enemies by id</li>
	<li>Reorganized pages</li>
	<li>Made items un/equippable </li>
	<li>Set up adventure levels</li>
	<li>Implemented item component levels</li>
	<li>Disabled changing other hero's equipment</li>
	<li>Disabled seeing other hero's gold</li>
	<li>Disabled increasing other hero's attributes</li>
</ul>
<h2>February 27, 2016</h2>
<ul>
	<li>Randomized enemy gold drops</li>
	<li>Changed minimum damage to 1</li>
	<li>Added landing page</li>
</ul>
<h2>February 24, 2016</h2>
<ul>
	<li>Changed reports to show up after the adventure is completed</li>
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
	<li>Adventures shouldn't reach the max text size and freeze anymore</li>
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
	<li>Changed adventure cooldown to 10 minutes/room from 1 hour/room</li>
	<li>Added distinct floor options for rooms</li>
	<li>Fixed room counting in adventures</li>
	<li>Added the "Whiteshire Abbey" adventure</li>
</ul>
<h2>January 29, 2016</h2>
<ul>
	<li>Started recording patch notes</li>
	<li>Moved home.php to index.php</li>
	<li>Moved images to images folder</li>
	<li>Fixed image urls</li>
	<li>Fixed unit image settings to pull from profession instead of name</li>
</ul>
</div>