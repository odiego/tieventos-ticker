if(jQuery){
	jQuery(document).ready(function($) {
			
		var tickerObj    = $('div.tieventos-ticker');
		var listMaxItens = tickerObj.attr('data-qtd');
		var effectType   = tickerObj.attr('data-effect');
		var list 		 = tickerObj.find('ul');
		var listObjects  = tickerObj.find('ul > li');	
		var listSize     = listObjects.size();
		
		listObjects.each(function( index, value ) {
			if (index < listMaxItens)
				$(this).fadeIn();
		});
		
		var lastVisibileElement = $('div.tieventos-ticker > ul > li:visible:last');
		lastVisibileElement.css({'border': 'none'});
		
		function transitionsEvents(effectType){
			var effectType = effectType;
		
			if(effectType == 'fade'){
				var listObjects  = tickerObj.find('ul > li');
				var firstObject = listObjects.first();
				firstObject.fadeOut(2000, function() { $(this).appendTo(list).fadeIn(); });
			}
			if(effectType == 'movedown'){
				var listObjects  = tickerObj.find('ul > li');
				var firstObject = listObjects.first();
				listObjects.first().slideUp( function () { $(this).appendTo(list).slideDown(); });			
			}
			if(effectType == 'movesides'){
				//TODO	
			}
		};
		
		if(listSize > listMaxItens)
			setInterval(function(){ transitionsEvents(effectType); }, 5000);			
	});	
}else{
	alert('Jquery is required for TIEventos widget');
}