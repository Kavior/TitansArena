$(window).load(function(){
	allowAttack = true;
	//Fire beam spell
	$('#beam').click(function(){
		if(allowAttack && !waitForNextAction('beam') && user.isAlive())
			fireBeam();
	});
});

function waitTillNextAttack(){
	allowAttack = false;
	setTimeout(function(){
		allowAttack = true;
	}, settings.MIN_ATTACK_DELAY);
}

//Fire beam spell
fireBeam = function(){
	//If there is no timeout set for that spell
	if(!waitForNextAction('beam', settings.BEAM_DELAY)){
		existingBeam = arena.find('.beam');
		if(existingBeam.length > -1)
			existingBeam.remove();
			
		$('#arena').append(beamField);

		for(var opponent in opponentsCollection){
			var beamDmg = Math.ceil( user.calcMagicDamage() * 2.2 + user.level * getRandomArbitrary(15, 40) );
			opponentsCollection[opponent].hurt(beamDmg);
		}
		//Spell visual effects lasts for 1000 ms
		setTimeout(function(){
			beamField.remove();
		}, 1000);
	}
}

$.fn.killPlayerDiv = function(){
	$(this).stop(true, false);
	$(this).fadeOut(2000, function() { $(this).remove(); });
}

//Attach bullet to element
function appendBullet(element, x, y){
	var newBullet = $(bullet.replace('__top__', x).replace('__left__', y));
	element.append(newBullet);
	return newBullet;
}

/*
 * Those are points shown above the player - hitPoints, experience, healing points etc...
 */
$.fn.showPoints = function(points, type){
	if(typeof type == 'undefined')	type = 'default';
	var pointsDiv = $('<div class="show-points points-'+ type +'">'+ points +'</div>');
	//Container for those points inside a player div
	var showPoints = $(this).find('.show-points');

	if(showPoints.length > 0 ){ // if it is not the fist points div, insert new element in the bottom (closest to player)
		pointsDiv.insertBefore(showPoints.first());
		var lastPoints = showPoints.first();
	}else{ //if it is first, append it to the element
		$(this).append(pointsDiv);
		var lastPoints = pointsDiv;
	}

	lastPoints.animate({ //Fade out and remove last point
		'top -=' : 50,
		'opacity' : 0
	}, settings.MIN_ATTACK_DELAY * 2, function(){pointsDiv.remove()});
}
	
$.fn.showDamage = function(damage){
	$(this).initHpBar();
	$(this).showPoints(damage , 'damage');
}

/*
 * Checks if the element hit the opponent, for example bullet when shooting
 */
$.fn.handleOpponentHit = function(){
	var thisBullet = $(this);

	$('.opponent').each(function(){
		var opponentArea = ($(this).height() + $(this).width())/2;
		if(countDistance(thisBullet, $(this))  < opponentArea ){
			if(elementsCollide(thisBullet, $(this))){
				thisBullet.remove();
				var opponentObject = opponentsCollection[$(this).data('id')];
				if(typeof opponentObject != 'undefined'){
					opponentObject.hurt(user.calcDamage());
				}
			}
		}
	});
}

$.fn.meeleAttackUser = function(){
	if(countDistance($('#player'), $(this)) < settings.MIN_MEELE_DISTANCE){
		opponentDmg = $(this).getOpponentObject().calcMeeleDamage();
		user.hurt(opponentDmg);
	}
};

$(function(){
	//Perform a shot
	$.fn.initShot = function(e){
		var playerDiv = $('#player');
		var playerPosition = playerDiv.position();
		var playerTop = playerPosition.top + playerDiv.height() / 2;
		var playerLeft = playerPosition.left + playerDiv.width() / 2;
		//Bullet starts from player
		newBullet = appendBullet($('#arena'), playerTop, playerLeft);
		
		//Bullet shots towards mouse position
		var pos   = $(this).offset();
	    var elPos = { X:pos.left , Y:pos.top };
	    var mPos  = { X:e.clientX-elPos.X, Y:e.clientY-elPos.Y };

		var x = mPos.X;
		var y = mPos.Y;
		var dist = countDistance(playerDiv, $(this));
		
		//Shot speed, default = 1
		speedMultiplier = 1;
		
		/*
		 * Animation time depanding on distance, so there are no 
		 * differences in speed 
		 */
		var animationTime = Math.floor(1/speedMultiplier * dist);
		newBullet.animate({
			'left': x,
			'top' : y
		}, animationTime, 'linear', function(){ //Clear the shooting interval and destroy the bullet after shot
			if(typeof(opponentInterval) != 'undefined' && $('.bullet:animated').length == 1)
				clearInterval(opponentInterval);
			$(this).fadeOut(100, function() { $(this).remove(); });
		});
		
		//This interval will check if bullet hit the opponent. If so, it calls a proper function to handle it.
		var opponentInterval = setInterval(function() {
			$('.bullet').each(function(){
					$(this).handleOpponentHit();
			});
		}, settings.MIN_SHOT_INTERVAL);
	}
});
