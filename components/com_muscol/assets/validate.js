// JavaScript Document
window.addEvent('domready', function() {

	$('adminForm').addEvent('submit', function(e) {
		new Event(e).stop();
		
		var valid = document.formvalidator.isValid($('adminForm'));
		
		if(valid){
			
			$$('.showsaving').each(function(el) {
				el.style.display = 'inline' ;
			});
			
			$('adminForm').submit();
		}
		else{

			$$('.required').each(function(el) {
				if(el.hasClass('invalid')){
					el.focus();
					return false;	
				}
			});
		
		}
		
		
	});

});