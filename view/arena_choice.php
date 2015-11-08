<html>
	<head>
		<title>Select opponent</title>
		<link rel="icon" href="../resources/images/favicon.png" type="image/gif">
		<link href="../resources/css/style.css" type="text/css" rel="stylesheet"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
		<script src="../resources/js/misc.js" type="text/javascript"></script>
	</head>
	<body>
		<div class="right-top-buttons">
			<a href="<?php echo $this->generateUrl('character_view') ?>"><button class="button">Character</button></a>
			<a href="<?php echo $this->generateUrl('arena_info') ?>"><button class="button">Game info</button></a>
			<a href="<?php echo $this->generateUrl('user_logout') ?>"><button class="button">Log out</button></a>
		</div>
		<div class="arena-choice dark-bcg container">
			<div class="select-text">Select opponent</div>
			<div class="opponent-form">
				<form method="GET" action="<?php echo $arenaUrl ?>" name="opponent-form">
					<div class="form-group">
						<label>Opponent:</label>
						<select name="opponent" id="opponent-select">
							<?php
								foreach($opponents as $opponent){
									echo '<option value="'.$opponent['opponent_id'].'">'. $opponent['opponent_name'] .'</option>';
								}
							?>
						</select>
					</div>
					<div class="form-group">
						<label>Difficulty: </label>
						<select name="diff" id="difficulty-select">
							<option value="0">Easy</option>
							<option value="1">Medium</option>
							<option value="2">Hard</option>
						</select>
					</div>
					<div class="form-group">
						<button class="button" role="submit">Fight!</button>
					</div>
				</form>
			</div>
			<div class="opponent-details">
				<div class="opponent-name"></div>
				<div class="opponent-image-container"></div>
				<div class="opponent-description">
					<ul>
						<li>
							Difficulty : <span class="opponent-difficulty"></span>
						</li>
					</ul>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</body>
	<script>
		$(function(){
			var opponentSelect = $('#opponent-select');
			var opponentDetailsConainter = $('.opponent-details');
			
			var opponentNameContainer = opponentDetailsConainter.find('.opponent-name');
			var opponentImageContainer = opponentDetailsConainter.find('.opponent-image-container');
			var opponentDifficultyContainer = opponentDetailsConainter.find('.opponent-difficulty');

			loadOpponentInfo(opponentSelect.val());//Load information about selected opponent
			
			//Loads image and difficulty of the chosen opponent
			function loadOpponentInfo(opponentId){
				opponentNameContainer.html('');
				opponentImageContainer.html('');
				opponentImageContainer.showSimpleLoader();
				var arenaUrl = '<?php echo $arenaUrl ?>';

				$.ajax({
					url : arenaUrl,
					type : 'post',
					data : { opponentInfoId : opponentId },
					success : function(data){ appendOpponentInfo(data); },
					complete : function(){ },
					error : function(){
						alert('Can\'t find opponent');
					}
				});
			}
			
			function getOpponentDifficultyWord(opponentLevel){
				if(opponentLevel < 5){
					difficulty = 'Very easy';
				}else if(opponentLevel < 10){
					difficulty = 'Easy';
				}else if(opponentLevel < 20){
					difficulty = 'Medium';
				}else if(opponentLevel < 40){
					difficulty = 'Hard';
				}else if(opponentLevel < 80){
					difficulty = 'Very hard';
				}else if(opponentLevel > 80){
					difficulty = 'Infernal';
				}else{
					difficulty = 'Unknown';
				}
				
				return difficulty;
			}
			
			function appendOpponentInfo(data){
				data = JSON.parse(data);
				var opponentName = data.opponent_name;
				var opponentLevel = data. opponent_level;
				var opponentImage = data.opponent_image;
				
				//Difficulty will depend on opponent's level
				var difficultyWord = getOpponentDifficultyWord(opponentLevel);

				opponentNameContainer.html(opponentName);
				opponentDifficultyContainer.html(difficultyWord);
				var imageSrc = '../resources/images/opponents/'+ opponentImage;
				var opponentImage = $('<img class="opponent-image" src="'+ imageSrc +'" />');
				
				
				$('<img/>').attr('src', imageSrc).load(function() {
					$(this).remove(); // prevent memory leaks as @benweet suggested
					opponentImageContainer.html(opponentImage);
					opponentImageContainer.hideSimpleLoader();
				 	
				});
			}
			//Load chosen opponent info
			opponentSelect.on('change', function(){
				var opponentId = $(this).val();
				loadOpponentInfo(opponentId);
			});
			
		});
	</script>
</html>

