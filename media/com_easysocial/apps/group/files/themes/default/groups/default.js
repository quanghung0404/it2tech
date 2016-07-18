
EasySocial.require()
.script("site/explorer")
.done( function($)
{
	$("[data-bs-explorer]")
		.explorer({
			url: "site/controllers/playground/explorer"
		})
		.on( 'fileUse' , function( event , id , file , data )
		{
			alert("Listen to this event using:\n\"{explorer} fileUse\": function(){}\n\n" + "You selected file " + data.name + ".");
		});
});