$(function(){
	//Chosen opponent enters the arena at a set position 
	addChosenOpponent(299, 100);
});

function addChosenOpponent(x, y){
	var chosenOpponent = settings.CHOSEN_OPPONENT;
	var difficulty = settings.DIFFICULTY;
	
	var opponentLevel = chosenOpponent.opponent_level;
	var opponentStrength = adjustAbility(chosenOpponent.opponent_strength, difficulty) / 80;
	var opponentHP = adjustAbility(chosenOpponent.opponent_HP, difficulty) / 5;
	var opponentXP = adjustAbility(chosenOpponent.opponent_experience, difficulty) * 0.1;
	var opponentImage = chosenOpponent.opponent_image;
	var opponentSize = chosenOpponent.opponent_size > 0 ? chosenOpponent.opponent_size : 80;

	var newOpponent = addOpponent(x, y, opponentSize, 
		{	level : opponentLevel, 
			strength : opponentStrength, 
			totalHealthPoints : opponentHP, 
			experience : opponentXP 
		});
		
	var opponentDiv = $(newOpponent.playerDiv);
	//Path of opponent image
	var imgPath = 'resources/images/opponents/' + opponentImage;
	adjustPlayerSize(imgPath, opponentSize, opponentDiv); // Adjust div size according to image dimensions ratio and opponent size
	
	//Set opponent image
	if(opponentImage.length > 0){	
		var css = opponentDiv.attr('style');
		var newCss = css + ' background-color: transparent; border: none; background-image: url("' + imgPath + '");';
		
		$(newOpponent.playerDiv).attr('style', newCss)
	}
		
}

//Adjust the ability of opponent depending on user level and chosen difficulty
function adjustAbility(ability, difficulty){
	var userLevel = settings.USER_LEVEL;
	return Math.floor( (ability * ( (difficulty + 1)/ 2 ) ) * (Math.pow(userLevel, 2) / 17 + 1) );
}