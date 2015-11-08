<html>
	<head>
		<title>Info</title>
		<link href="../resources/css/style.css" type="text/css" rel="stylesheet"/>
	</head>
	<body>
		<div class="right-top-buttons">
			<a href="<?php echo $this->generateUrl('character_view') ?>"><button class="button">Character</button></a>
			<a href="<?php echo $this->generateUrl('user_logout') ?>"><button class="button">Log out</button></a>
		</div>
		<a href="<?php echo $this->generateUrl('arena_choice') ?>"><button class="round-big">Fight!</button></a>
		<div class="arena-info dark-bcg container">
			<h1>Titans arena</h1>
			<p>
				Fight with the most frightening monsters and heroes! You can choose opponent and difficulty of the fight. With every defeated opponent, 
				you will gain experience points depending on the opponent strength, difficulty and your level. 
				With every new level, you will gain 10 learning points. 
				You can use those points to increase your setrength, health points or magic.
			</p>
			<p>
				<h3>Controls:</h3>
				<ul>
					<li>Movement : arrows</li>
					<li>Healing : CTRL</li>
					<li>Shooting / Meele attack : LMB</li>
				</ul>
			</p>
		</div>
	</body>
</html>