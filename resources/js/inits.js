$(window).load(function(){
	//Opponents will attack user with a set delay
	setInterval(function(){
		$('.opponent').each(function(){
			if($(this).getOpponentObject().isAlive())
				$(this).meeleAttackUser(); 
		});
	}, settings.MIN_ATTACK_DELAY);
	
	
	//Opponents will follow user
	setInterval(function(){ 
		$('.opponent').followUser();
	}, 120)	
});	
