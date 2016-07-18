<?php defined( '_JEXEC' ) or die( 'Unauthorized Access' ); ?>

EasySocial.require()
.script( 'admin/indexer/indexer' )
.done(function($){

	$( '[data-indexer-container]' ).implement(EasySocial.Controller.Indexer);

});
