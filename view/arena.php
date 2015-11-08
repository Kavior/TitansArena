<html>
	<head>
		<title>Arena</title>
		<link rel="icon" href="../resources/images/favicon.png" type="image/gif"> 
		<link href="resources/css/style.css" type="text/css" rel="stylesheet"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script src="resources/js/misc.js" type="text/javascript"></script>
		<script>
			//Loading animation
			showLoader();
			//Main settings
			var settings = {
				CHOSEN_OPPONENT : <?php echo $opponentJSON ?>,
				DIFFICULTY : <?php echo $difficulty ?>,
				CHARACTER_URL : '<?php echo $characterUrl ?>',
				MIN_SHOT_INTERVAL : 10,
				USER_LEVEL : <?php echo $user['user_level'] ?>,
				USER_STRENGTH : <?php echo $user['user_strength'] ?>,
				USER_TOTAL_HP : <?php echo $user['user_totalHP'] ?>,
				USER_MAGIC : <?php echo $user['user_magic'] ?>,
				USER_SPEED : <?php echo $characterSpeed ?>,
				USER_SKIN : "resources/images/skins/<?php echo $user['user_skin']?>" ,
				MIN_ATTACK_DELAY : 1000,
				BEAM_DELAY : 5000,
				MIN_MEELE_DISTANCE : <?php echo $chosenOpponent['opponent_size'] + $chosenOpponent['opponent_size'] * 0.8 + 25 ?>
			}	
			
			preload(
					"resources/images/arena1.jpg",
					"resources/images/Sword_Fire.jpg",
					settings.USER_SKIN
			);
		</script>
		<script src="resources/js/arena.js" type="text/javascript"></script>
		<script src="resources/js/attack.js" type="text/javascript"></script>
		<script src="resources/js/opponent.js" type="text/javascript"></script>
		<script src="resources/js/inits.js" type="text/javascript"></script>
		<script>
			$(window).load(function(){
				hideLoader(1300);
			});
		</script>
	</head>
</html>
<body>
	<div class="beam-load"></div><!-- Preloading beam animation -->
	<div class="right-top-buttons">
		<a href="<?php echo $this->generateUrl('arena_choice') ?>"><button class="button">Change opponent</button></a>
		<a href="<?php echo $this->generateUrl('character_view') ?>"><button class="button">Character</button></a>
		<a href="<?php echo $this->generateUrl('arena_info') ?>"><button class="button">Game info</button></a>
		<a href="<?php echo $this->generateUrl('user_logout') ?>"><button class="button">Log out</button></a>
	</div>
	<div id="arena-container" class="noselect">
		<div id="arena">
			<div id="player" style="background-image: url('resources/images/skins/<?php echo $user['user_skin'] ?>')"><div class="player-health health-bar"><div class="hp-bar-inside"></div></div></div>
		</div>
		<div id="player-menu">
			<div class="menu menu-basic">
				<div class="menu-title">Attack type</div>
				<div class="radios">
					<div class="radio-cell">
						<label>Meele</label><input class="attackType" type="radio" name="attackType" value="0" checked/>
					</div>
					<div class="radio-cell">
						<label>Distance</label><input class="attackType" type="radio" name="attackType" value="1"/>
					</div>
				</div>
			</div>
			<div class="menu menu-special">
				<div class="menu-title">Actions</div>
				<button class="button dsgnmoo" id="beam">Fire Beam</button>
			</div>
		</div>
	</div>
</body>