
opponentsCollection = [];
var bullet = '<div class="bullet" style="top: __top__px; left: __left__px;"></div>';

var user = new Player({
	level : settings.USER_LEVEL,
	strength : settings.USER_STRENGTH,
	playerDiv : '#player',
	totalHealthPoints : settings.USER_TOTAL_HP,
	magic : settings.USER_MAGIC
});

var actionsDelays = [];
var arena = $('#arena');
var arenaWidth = arena.width();
var arenaHeight = arena.height();
var beamField = $('<div class="beam"></div>');

$(window).load(function(){
	adjustPlayerSize(settings.USER_SKIN, $('#player').width(), $('#player'));
	var arena = $('#arena');
	var arenaWidth = arena.width();
	var arenaHeight = arena.height();
	
	$('.attackType').on('keydown', function(e){ //Prevent from changing input with arrows
	  e.preventDefault();
	  arena.focus();
	});
	
	settings.USER = user;
	$('#player').initHpBar();

	var maximalAbsoluteValue = arena.width() - $('#player').width(), //Prevent from exceeding the arena
    	keys = {},
    	intervalDistance = settings.USER_SPEED;
	
	keyPressed = false;
	
	$(window).keydown(function(e) { 
		keys[e.which] = true; 
		
		//$('.opponent').eq(0).followUser();	
	});
	
	$(window).keyup(function(e) { 
		keys[e.which] = false; 
	});
	
	var maximalLeftValue = arena.width() - $('#player').width()/2;
	var maximalTopValue = arena.height() - $('#player').height();
	function getNewPosition(oldValue,a,b) { //a and b are the keys
	    var newValue = parseInt(oldValue, 10) - (keys[a] ? intervalDistance : 0) + (keys[b] ? intervalDistance : 0);
	    if(a == 37 || b == 39){//left or right
	    	return newValue < 0 ? 0 : newValue > maximalLeftValue ? maximalLeftValue : newValue;
	    }else if(a == 38 || b == 40){//top or bottom
	    	return newValue < 0 ? 0 : newValue > maximalTopValue ? maximalTopValue : newValue;
	    }
	}
	
	$.fn.setNewPosition = function(){
		var playerDiv = $('#player');
		if(playerDiv.length > 0){
			var userCurrentLeft = playerDiv.position().left;
			var userCurrentTop = playerDiv.position().top;
			var leftAfterMove = getNewPosition(userCurrentLeft, 37, 39);
			var topAfterMove = getNewPosition(userCurrentTop, 38, 40);
			
			var newUserPosition = { left : leftAfterMove, top : topAfterMove, width : playerDiv.width(), height : playerDiv.height() };
			var collide = false;
	
			$('.opponent').each(function(){
				collide = elementsCollide($(this), newUserPosition); //Check for collision between user pos after move and opponents pos
				if(collide){ 
					return false;
				}
			});
			
			if(!collide){ //Move if no collisions detected
				$(this).css({
			        left: function(i,oldValue) { return getNewPosition(oldValue, 37, 39); }, //for left and right arrow
			        top: function(i,oldValue) { return getNewPosition(oldValue, 38, 40); } // for up and down arrow
			    });
				//if(anyMoveKeyPressed())
			    	//$('.opponent').eq(0).followUser();	
			}
		}
	}
	
	//Watch for user move controlls
	setInterval(function() {
		$('#player').setNewPosition(); 
	}, 20);

/*
	function anyMoveKeyPressed(){
		return keys[37] || keys[38] || keys[39] || keys[40];
	}
*/
	var arenaTop = parseInt(arena.position().top);
	var arenaLeft = parseInt(arena.position().left);
	var arenaRight = arenaLeft + arena.width();
	var arenaBottom = arenaTop + arena.height();
	
	//Click on arena will cause either a shot or a meele hit
	arena.click(function(event){
		if(allowAttack){
			waitTillNextAttack();
			var target = $(event.target);
			var meeleChosen = $('.attackType:checked').val() == 0;
			if(meeleChosen){
				if(isMeeleAttackTriggered(target)){
					var targetOpponentObject = target.getOpponentObject();
					targetOpponentObject.hurt(user.calcMeeleDamage());
				}
			}else{
				$(this).initShot(event);
			}	
		}
	});
	
	function isMeeleAttackTriggered(target){
		
		var clickedOnOpponent = target.hasClass('opponent');
		var isOpponentWithinARange = countDistance($('#player'), target) <= settings.MIN_MEELE_DISTANCE;
		
		if( clickedOnOpponent && isOpponentWithinARange)
			return true
		return false;
	}
	/*
	setInterval(function(){
		$('.opponent').each(function(){
			var firstOpponent = $(this);
			$('.opponent').not(firstOpponent).each(function(){
				var secondOpponent = $(this);
				
				if(elementsCollide(firstOpponent, secondOpponent)){

					goApart(firstOpponent, secondOpponent);
				}
			});
		});
	}, 660);
	*/
	$(document).on('keydown', function(e){
		//Healing
		if(e.which == 17 && !waitForNextAction('user_heal')){
			user.selfHeal();
			waitForNextAction('user_heal', settings.MIN_ATTACK_DELAY);
		}
	});
	
});

/*
 * Function can be either setter or getter, depending if the time argument is passed
 * It sets the timeout till next action or checks if there is a waiting
 */
function waitForNextAction(action, time){
	if(typeof time == 'undefined'){
		return typeof actionsDelays[action] != 'undefined' ? actionsDelays[action] : false; //just return if waiting
	}else{
		actionsDelays[action] = true;
		
		setTimeout(function(){
			actionsDelays[action] = false; //set waiting to false after time has passed
		}, time);
	}
}
/*
 * Any player on the arena, it can be logged user or the opponent
 */
function Player(data){
	this.level = typeof data.level != 'undefined'  && !isNaN(data.level) ? data.level : 0;
	this.experience = typeof data.experience != 'undefined'  && !isNaN(data.experience) ? data.experience : 0;
	this.strength = typeof data.strength != 'undefined' && !isNaN(data.strength) ? data.strength : 10;
	this.magic = typeof data.magic != 'undefined' && !isNaN(data.magic) ? data.magic : 10;
	this.totalHealthPoints = typeof data.totalHealthPoints != 'undefined' && !isNaN(data.totalHealthPoints) ? data.totalHealthPoints : 100;
	this.healthPoints = typeof data.healthPoints != 'undefined'  && !isNaN(data.healthPoints) ? data.healthPoints : this.totalHealthPoints;
	this.playerDiv = typeof data.playerDiv !== 'undefined' ? data.playerDiv : null;
	
	this.hurt = function(damage){
		if(this.isAlive() && this.playerDiv !== null){
			var hpAfterDamage = this.healthPoints - damage;
			this.healthPoints = this.healthPoints < damage ? 0 : hpAfterDamage;
			$(this.playerDiv).showDamage(damage);
			$(this.playerDiv).initHpBar();
			
			//Player was killed
			if(hpAfterDamage <= 0){ 
				$(this.playerDiv).killPlayerDiv(); 
				if(this.isUser)//If the killed player was user
					sendDataToController('playerKilled=true');
				else//The killed player was not the logged user, so he gets an experience or killed opponent
					user.addExperience(this.experience);
			}
		}
		return this;
	};
	
	this.isUser = this.playerDiv == '#player'; //Check if the player is logged user
	
	this.heal = function(points){
		if($(this.playerDiv) !== null && this.isAlive()){
			var healthAfterHeal = this.healthPoints + points;
			this.healthPoints =  healthAfterHeal > this.totalHealthPoints ? this.totalHealthPoints : healthAfterHeal;
			$(this.playerDiv).showPoints(points);
			$(this.playerDiv).initHpBar(); //Health bar change ater heal
		}
		return this;
	};
	
	//Power of self-healing depends on magic level and level
	this.selfHeal = function(){
		var healAmount = Math.floor( ( getRandomArbitrary(1.2, 2.2) * this.magic ) * (this.level/50 + 1) );
		this.heal(healAmount);
	};
	
	//Overall damage
	this.calcDamage = function(){
		return Math.floor(this.level * Math.random() + this.strength * Math.random());
	};
	
	this.calcMagicDamage = function(){
		return  Math.floor(this.level * 1.2 * getRandomArbitrary(0.4, 1) + this.magic * (getRandomArbitrary(0.8, 2)));
	};
	
	this.calcMeeleDamage = function(){
		return this.calcDamage() * 3;
	};
	
	this.getPercentageHp = function(){
		return Math.floor( this.healthPoints / this.totalHealthPoints * 100 );
	};
	
	this.addExperience = function(exp){
		var exp = Math.floor(exp);
		$(this.playerDiv).showPoints(exp , 'special'); //Show how much experience the player obtained
		var toSend = "addExperience=" + exp;
		
		var success = function(){
			this.experience += exp;
		}

		sendDataToController(toSend, success());
	};
	
	this.isAlive = function(){
		return this.healthPoints > 0;
	};
}
//Send data to character controller
function sendDataToController(data, success, error, complete){
	var xml = new XMLHttpRequest();
	xml.open("POST", settings.CHARACTER_URL, true);
	xml.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	
	xml.send(data);
	//Function on success
	if(typeof success !== 'undefined'){
		if (xml.readyState == 4 && xml.status == 200) { // When succeeed
			success;
				
		}
	}
}

function getRandomArbitrary(min, max) {
    return Math.random() * (max - min) + min;
}
/*
 * x , y - coordinates, 
 * size - integer (opponent size in px, this will be later adjusted to it's actual size ratio)
 * newOpponentData - level, strength etc...
 */
function addOpponent(x, y, size, newOpponentData){
	if(typeof newOpponentData == 'undefined'){
		 newOpponentData = [];
	}else{
		totalHealthPoints = newOpponentData.totalHealthPoints;
	}
	
	var opponentId = makeId();
	var opponentDefaults = { playerDiv : '.opponent[data-id="' + opponentId + '"]' };
	var opponentObject = new Player($.extend(newOpponentData, opponentDefaults));
	
	opponentsCollection[opponentId] = opponentObject;
	var percentageHp = opponentObject.getPercentageHp();
	
	var opponentDivData = {
		x : x,
		y : y,
		size : size,
		opponentId : opponentId
	};
	
	var opponentDiv = createOpponentDiv(opponentDivData);
	$('#arena').append(opponentDiv);
	opponentDiv.initHpBar();
	
	return opponentObject;
}

function createOpponentDiv(data){
	var opponentDiv = $('<div class="opponent noselect" style="left:'+ data.x +'px; bottom:'+ data.y + 
	'px; width:'+ data.size +'px; height:'+ data.size +'px;"'+
	' data-id="'+ data.opponentId +'"><div class="opponent-health health-bar">'+
	'<div class="hp-bar-inside"></div></div></div>');
	
	return opponentDiv;
}

/*
 * Add health points bar t the player depending on his actual HP
 */
$.fn.initHpBar = function(){
	var hpBar = $(this).find('.health-bar');
	
	if($(this).is('#player')){
		var playerObject = settings.USER;
	}else{
		var id = $(this).data('id');
		var playerObject = opponentsCollection[id]; //find object by id
	}
	
	if(typeof playerObject != 'undefined'){
		var percentHp = playerObject.getPercentageHp();

		var insideBar = hpBar.find('.hp-bar-inside');
		var cssWidth = percentHp + '%';
		insideBar.css('width', cssWidth); //set hp bar depending on health
	}
}
// Remove opponent - both DOM element and object
$.fn.removeOpponent = function(){
	var id = $(this).data('id');
	$(this).stop(true, false);
	$(this).fadeOut(100, function() { $(this).remove(); delete opponentsCollection[id]; });
	
}

//Created random id
function makeId(){
    var text = "";
    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

    for( var i=0; i < 5; i++ )
        text += possible.charAt(Math.floor(Math.random() * possible.length));

    return text;
}

//Check if element exceeds arena
$.fn.exceedsArena = function(){
	var elementTop = parseInt($(this).position().top);
	var elementLeft = parseInt($(this).position().left);
	var elementRight = elementLeft + $(this).width();
	var elementBottom = elementTop + $(this).height();
	
	return elementTop <= arenaTop || elementBottom >= arenaBottom || elementLeft <= arenaLeft || elementRight >= arenaRight;
}
/*
 * Checks for collision between two elements. The arguments can be either the DOM objects or the array of parameters. 
 * Parameters must be in format : { top : x, left : y, width : a, height : b }
 */
function elementsCollide(firstElement, secondElement){
	if(typeof firstElement.selector == 'undefined'){ //If the first element is not DOM object, but parameters
		var firstElementTop = Math.ceil(parseInt(firstElement.top));
		var firstElementLeft = Math.ceil(parseInt(firstElement.left));
		var firstElementWidth = Math.ceil(parseInt(firstElement.width));
		var firstElementHeight = Math.ceil(parseInt(firstElement.height));
		
	}else{
		var firstElementTop = Math.ceil(parseInt(firstElement.position().top));
		var firstElementLeft = Math.ceil(parseInt(firstElement.position().left));
		var firstElementWidth = Math.ceil(firstElement.width());
		var firstElementHeight = Math.ceil(firstElement.height());
	}
	
	if(typeof secondElement.selector == 'undefined'){ //If the second element is not DOM object, but parameters
		var secondElementTop = Math.ceil(parseInt(secondElement.top));
		var secondElementLeft = Math.ceil(parseInt(secondElement.left));
		var secondElementWidth = Math.ceil(parseInt(secondElement.width));
		var secondElementHeight = Math.ceil(parseInt(secondElement.height));
	}else{
		var secondElementTop = Math.ceil(parseInt(secondElement.position().top));
		var secondElementLeft = Math.ceil(parseInt(secondElement.position().left));
		var secondElementWidth = Math.ceil(secondElement.width());
		var secondElementHeight = Math.ceil(secondElement.height());
	}
	
	var firstElementRight = firstElementLeft + firstElementWidth;
	var firstElementBottom = firstElementTop + firstElementHeight;
	var secondElementRight = secondElementLeft + secondElementWidth;
	var secondElementBottom = secondElementTop + secondElementHeight;
	
	if (firstElementBottom < secondElementTop || firstElementTop > secondElementBottom || 
	 	firstElementRight < secondElementLeft || firstElementLeft > secondElementRight) 
	 	return false;
	 	
  	return true;
}

/*
 * Get js object representing opponent div
 */
$.fn.getOpponentObject = function(){
	return opponentsCollection[ $(this).data('id') ];
}

function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

function addRandomOpponent(){
	//Random coordinates
	var x = getRandomInt(0, $('#arena').width());
	var y = getRandomInt(0, $('#arena').height());
	
	var rs = 500 * ( Math.random() / ( Math.random() * 10 ) ); //random strength
	if(rs < 100){
		radius = rs/3;
	}else if(rs < 500){
		radius = rs/5;
	}else if(rs < 1500){
		radius = rs/10;
	}else{
		radius = rs/20;
	}
	var rhp = 1000 * ( Math.random() / ( Math.random() * 10 ) );// random strength
	var xp = (rhp + rs) / 10;
	data = { strength : rs,  totalHealthPoints : rhp, experience : xp};
	addOpponent(x, y, radius, data);
}

function countDistance(element1, element2){
	if(element1.length && element2.length){
		var x1 = element1.position().left;
		var y1 = element1.position().top;
		var x2 = element2.position().left;
		var y2 = element2.position().top;

		var middleFromTop1 = y1 + element1.height()/2;
		var middleFromLeft1 = x1 + element1.width()/2;
		
		var middleFromTop2 = y2 + element2.height()/2;
		var middleFromLeft2 = x2 + element2.width()/2;
		
		var xMiddlesDistance = Math.abs(middleFromLeft1 - middleFromLeft2);
		var yMiddlesDistance = Math.abs(middleFromTop1 - middleFromTop2);
		
		var dist = Math.sqrt( Math.pow(xMiddlesDistance, 2) + Math.pow(yMiddlesDistance, 2) ); 

		return dist;
	}
	return null;
}

/* Not used right now
function convertPosForAnimation(pos){
	if(pos < 0){
		
		pos = pos.toString().replace(/-/, '-=');
	}else{
		pos = '+=' + pos.toString();
	}	
	return pos;
}
*/

$.fn.followUser = function(){
	var thisOpponentObject  = $(this).getOpponentObject(); 
	playerDiv = $('#player');
	if(typeof thisOpponentObject != 'undefined' && thisOpponentObject.isAlive() && user.isAlive()){
		$(this).stop(true, false)
		var keptDistFromUser = $(this).width() * 0.02 + 15;

		var playerTop = playerDiv.position().top;
		var playerLeft = playerDiv.position().left;

		var thisElementTop = $(this).position().top;
		var thisElementLeft = $(this).position().left;
		
		
		var opponentPositionTowardsUser = getEl2PosTowardsEl1(playerDiv, $(this));
		var horizontalPositionTowardsUser = opponentPositionTowardsUser[1];
		var verticalPositionTowardsUser = opponentPositionTowardsUser[0];
		
		var topDiff = Math.abs(playerTop - thisElementTop) ;
		var leftDiff = Math.abs(playerLeft - thisElementLeft) ;
		
		switch(horizontalPositionTowardsUser){
			case 'left': //opponent is to the left of the player
				newLeft = thisElementLeft + leftDiff - $(this).width() - keptDistFromUser; 
				
			break;
			
			case 'right': //opponent is to the right of the player
				newLeft = thisElementLeft - leftDiff + playerDiv.width() + keptDistFromUser;
			break;
			
			default:
				newLeft = thisElementLeft;
		}
		
		switch(verticalPositionTowardsUser){
			case 'above': //opponent is above the player
				newTop = thisElementTop + topDiff - $(this).height() - keptDistFromUser; //console.log()
			break;
			
			case 'below': //opponent is below the player
				newTop = thisElementTop - topDiff + playerDiv.height() + keptDistFromUser;
				
			
			break;
			
			default:
				newTop = thisElementTop;
		}
		var thisElementNewBottom = newTop + $(this).height();
		var thisElementNewRight = newLeft + $(this).width();
		var arenaBottom = arenaHeight = $('#arena').height();
		var arenaRight = arenaWidth = $('#arena').width();
		
		//Prevent exceeding the arena
		if(thisElementNewBottom >= arenaBottom || newTop <= 0) //Vertically
			newTop = thisElementTop;
		
		if(newLeft <= 0 || thisElementNewRight >= arenaRight)//Horizontally
			newLeft = thisElementLeft;

		var dist = countDistance(playerDiv, $(this));
		opponentLevel = thisOpponentObject.level > 0 ? thisOpponentObject.level : 1;
		//var animationTime = Math.floor( ( dist * 15) / (1 + opponentLevel / 10) ) 
		
		var animationTime =  10000/opponentLevel + 15;
		if(animationTime < 320) animationTime = 230 //min time

		//Perform follow animation
		$(this).animate({
			'left': newLeft,
			'top': newTop
		}, animationTime, 'linear');
	}
}

function getEl2PosTowardsEl1(element1, element2){
	var directions = [];
	
	top1 = element1.position().top;
	left1 = element1.position().left;
	right1 = left1 + element1.width();
	bottom1 = top1 + element1.height();
	
	top2 = element2.position().top;
	left2 = element2.position().left;
	right2 = left2 + element2.width();
	bottom2 = top2 + element2.height();
	
	if(bottom2 < top1)//2 is above 1
		directions.push('above');
	else if(top2 > bottom1)//2 is below 1
		directions.push('below');
	else 
		directions.push('center');
		
	if(right2 < left1)//2 is on the left of 1
		directions.push('left');
	else if(left2 > right1)//2 is on the right
		directions.push('right');
	else
		directions.push('center');
		
	return directions;
}

// Adjust player div size according to its background image dimensions ratio and opponent size
function adjustPlayerSize(imgPath, opponentSize, opponentDiv){
	//The following steps are neccesary to get the image size
	var img = new Image();
	img.src = imgPath;

	img.onload = function() {
		imgWidth = this.width;
	    imgHeight = this.height;
		//This is done in order to maintain original size ratio beetwen div and image
		var sizeRatio = (imgWidth / imgHeight).toFixed(2);
	
		var newOpponentWidth = opponentSize * sizeRatio;
		var newOpponentHeight = opponentSize;
		opponentDiv.css({ width : newOpponentWidth, height : newOpponentHeight });
	};
}