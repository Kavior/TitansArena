<html>
	<head>
		<title>Character</title>
		<link rel="icon" href="../resources/images/favicon.png" type="image/gif">
		<link href="../resources/css/style.css" type="text/css" rel="stylesheet"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script src="../resources/js/misc.js" type="text/javascript"></script>
		<script src="../resources/js/character.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="right-top-buttons">
			<a href="<?php echo $this->generateUrl('arena_info') ?>"><button class="button">Game info</button></a>
			<a href="<?php echo $this->generateUrl('user_logout') ?>"><button class="button">Log out</button></a>
		</div>
		<a href="<?php echo $arenaChoiceUrl ?>"><button class="round-big">Fight!</button></a>
		<div class="character-stats dark-bcg container">
			<table class="character-table">
				<tr class="ability-row">
					<td>Name</td><td class="ability-value"><?php echo $user['user_nickname'] ?></td><td></td>
				</tr>
				<tr class="ability-row">
					<td>Experience</td><td class="ability-value"><?php echo $user['user_experience'] ?></td><td></td>
				</tr>
				<tr class="ability-row">
					<td>Next level</td><td class="ability-value"><?php echo $nextLevelXp ?></td><td></td>
				</tr>
				<tr class="ability-row">
					<td>Level</td><td class="ability-value"><?php echo $user['user_level'] ?></td><td></td>
				</tr>
				<tr class="ability-row ability-healthPoints" data-ability="healthPoints">
					<td>Health points</td><td class="ability-value"><?php echo $user['user_totalHP'] ?></td><td class="lp-options noselect"></td>
				</tr>
				<tr class="ability-row ability-strength" data-ability="strength">
					<td>Strength</td><td class="ability-value"><?php echo $user['user_strength'] ?></td>
					<td class="lp-options noselect"></td>
				</tr>
				<tr class="ability-row ability-magic" data-ability="magic">
					<td>Magic</td><td class="ability-value"><?php echo $user['user_magic'] ?></td><td class="lp-options noselect"></td>
				</tr>
			</table>
			<div class="character-skin noselect">
				<div class="change-skin">Change</div>
				<span>Your skin</span>
				<div class="skin-name"><?php echo $skinName ?></div>
				<div class="skin-container">
					<div class="skin-img" style="background-image : url('../resources/images/skins/<?php echo $userSkinFile ?>') ;"></div>
				</div>
			</div>
			<div class="clear"></div>
			<div class="bottom-info">
				Learning points : <span class="lp-count"><?php echo $userLearningPoints ?></span>
				<button id="abilities-save" class="button">Save</button>
			</div>
		</div>

		<script>
			var anyLpAvailable = <?php echo $anyLpAvailable ?>;
			var learningPoints = initialLearningPoints = <?php echo $userLearningPoints ?>;
			var characterUrl = '<?php echo $characterUrl ?>';
			var abilities = {
				'healthPoints' : <?php echo $user['user_totalHP'] ?>,
				'strength' : <?php echo $user['user_strength'] ?>,
				'magic' : <?php echo $user['user_magic'] ?>
			};
			
			var settings = {
				userSkinFile : '<?php echo $userSkinFile ?>',
				skins : <?php echo $skinsJSON; ?>,
			}
			$(document).ready(function(){
				//preload skins
				var skinsJson = settings.skins;
				for(i = 0 ; i < skinsJson.length; i++){
					var fileName = skinsJson[i];
					var skinPath = '../resources/images/skins/' + fileName;

					var preloadDiv = $('<div style=\'background-image: url("'+ skinPath +'")\' class=\'preloaded\'></div>');
					$('body').append(preloadDiv);
				}
			});
			initialAbilities = $.extend( {}, abilities ); //Will be used to compare changes in abilities 

		</script>
	</body>
	
</html>