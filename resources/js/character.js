$(window).load(function(){
	//If there are learning points to use
	if(anyLpAvailable){
		var buttons = '<button class="lp-change-button lp-plus">+</button><button class="lp-change-button lp-minus">-</button>'; 
		$('.lp-options').each(function(){
			$(this).show();
			//To distribute LP
			$(this).append(buttons);
		});
		
		var saveButton = $('#abilities-save');
		saveButton.on('click', function(){
			saveAbilities();
		});
		
		function saveAbilities(){
			var healthPoints = abilities['healthPoints'];
			var strength = abilities['strength'];
			var magic = abilities['magic'];
			
			$.ajax({
				url : characterUrl,
				type : 'post',
				data : { healthPoints : healthPoints, strength : strength, magic : magic, learningPoints : learningPoints },
				success : function(){
					location.reload();
				}
			});
		}
		//Clicked on + or - next to ability
		$(document).on('click', '.lp-change-button', function(){
			if($(this).hasClass('lp-plus') && learningPoints <= 0) return false; //There are no LP's
			saveButton.show();
			var abilityRow = $(this).closest('.ability-row');
			
			if($(this).hasClass('lp-plus')){
				useLearningPoint(abilityRow);
			}else if($(this).hasClass('lp-minus')){
				useLearningPoint(abilityRow, 'distract');
			}
		});
		
		function useLearningPoint(abilityRow, action){
			var ability = abilityRow.data('ability');
			var minusButton = abilityRow.find('.lp-minus');
			
			switch(ability){
				case 'healthPoints':
					change = 10;
				break;
				
				default:
					change = 1;
			}
			
			if(action == 'distract'){//Decrease ability
				change *= -1; //In order to distract points

				if(learningPoints + 1 <= initialLearningPoints && abilities[ability] + change >= initialAbilities[ability]){
					learningPoints += 1;
					if($('.lp-plus').is(':hidden'))
						$('.lp-plus').show();
				}else{
					minusButton.hide();
					cancelSpeedup();
				}
			}else{ //Increase ability
				if(learningPoints > 0){
					learningPoints -= 1;
					if(learningPoints == 0){
						$('.lp-plus').hide();
						cancelSpeedup();
					}				
					if(minusButton.is(':hidden'))
						minusButton.show();
	
				}
			}
			
			$('.lp-count').html(learningPoints);
			changeAbility(ability, change);
		}
		
		function changeAbility(ability, change){
			if(ability in abilities){//If such an ability exists
				if(abilities[ability] + change < initialAbilities[ability])
					return false;
				abilities[ability] = abilities[ability] + change;
				var abilityRowSelector = '.ability-' + ability;
				$(abilityRowSelector).find('.ability-value').html(abilities[ability]);
				
				switch(ability){
				
				}
				return true;
			}
		}
		
		function addHealthUnit(){
			changeAbility('health', 10);
		}
		
		function distractHealthUnit(){
			changeAbility('health', -10);
		}
		
		function addStrengthUnit(){
			changeAbility('strength', 1);
		}
		
		function distractStrengthUnit(){
			changeAbility('strength', -1);
		}
		
	}
	
	var saveSkinButton = $('<div class="save-skin">Save</div>');
	var changeSkinButton = $('.change-skin');
	var changeSkinArrows = $('<div class="change-arrow arrow-previous">&lt;</div><div class="change-arrow arrow-next">&gt;</div>');
	//Enable skin change after clicking on "change" button
	changeSkinButton.click(function(){
		var characterSkinContainer = $(this).closest('.character-skin');
		if(!characterSkinContainer.find('.change-arrow').length){
			characterSkinContainer.append(changeSkinArrows);
		}
		
		$(this).hide();		
		characterSkinContainer.append(saveSkinButton);
	});
	//Save a skin
	saveSkinButton.on('click', function(){
		$.ajax({
			type : 'post',
			url : characterUrl,
			data : { newSkin : skins[currentSkinIndex] },
			success : function(){
				location.reload();
			}
		});
		$(this).remove();
		changeSkinButton.show();
	});
	
	var skins = settings.skins;
	var currentSkinIndex = settings.skins.indexOf(settings.userSkinFile);
	var skinNameContainer = $('.skin-name');
	var skinImage = $('.skin-img');
	
	//Change skin arrow - previous or next
	$(document).on('click', '.change-arrow', function(){
		
		if($(this).hasClass('arrow-previous')){ //Previous skin
			var newSkinIndex = currentSkinIndex - 1;
		}else if($(this).hasClass('arrow-next')){ // Next skin
			var newSkinIndex = currentSkinIndex + 1;
		}
		if(newSkinIndex < 0) newSkinIndex = skins.length - 1;
		if(newSkinIndex > skins.length - 1) newSkinIndex = 0;
		loadSkin(skins[newSkinIndex]);
		currentSkinIndex = newSkinIndex;
		
	});
	
	//var characterSkin = $('.character-skin');
	//Loads the skin from a file. Skin name will be file name
	function loadSkin(fileName){
		var skinImg = $(document).find('.skin-img');
		skinImg.showSimpleLoader(); 
		
		var skinName = fileName.replace(/\.[a-zA-Z]{2,3}$/, ''); //Remove extension
		var skinPath = '../resources/images/skins/' + fileName;
		var skinUrlProperty = 'url("'+ skinPath +'")';
		
		//This allows to finish loading animation on the image
		$('<img/>').attr('src', skinPath).load(function() {
			$(this).remove(); // prevent memory leaks as @benweet suggested
			skinNameContainer.html(skinName);
		 	skinImage.css('background-image', skinUrlProperty);
		 	skinImg.hideSimpleLoader();
		 	
		});
	
		//skinImage.hideSimpleLoader();
	}

	var buttonPressInterval = null; 
	var longPressTimeout = null;
	var clickIntervalTime = 30;
	var buttonPressedLong = false;
	
	$(document).on( 'mousedown', '.lp-change-button',function(){
		if($(this).hasClass('lp-plus') && learningPoints <= 0){
			cancelSpeedup();
			return false;
		}
		var button = $(this);
		
		//If button is being pressed longer, change points fast
		if(!buttonPressedLong){
			longPressTimeout = setTimeout(function(){
				buttonPressedLong = true;
				buttonPressInterval = setInterval(function(){
					button.click(); 
				}, clickIntervalTime);
			}, 1000);
		}
	});
	
	$(document).on( 'mouseup',function(){
		cancelSpeedup();
	});
	
	//Stop counting points fast
	function cancelSpeedup(){
		buttonPressedLong = false;
		clearInterval(buttonPressInterval);
		clearTimeout(longPressTimeout);
		clickIntervalTime = 30;
	}
});
