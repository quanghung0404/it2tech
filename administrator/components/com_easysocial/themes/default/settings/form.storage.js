
<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

// Implementation code runs here!
EasySocial.require()
.done(function($){

	$( '#storageAvatar' ).change(function(){
		var val = $(this).val();

		$( '#storage-' + val ).toggle();
	});

});
