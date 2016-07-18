
EasyBlog.ready(function($){

    window.insertTag = function( id , name )
    {
    	$( '#item_team' ).val( id );
    	$( '#team_name' ).html( '<span class="label label-primary">' + name + '</span>');

    	$.Joomla("squeezebox").close();
    }

    window.insertMember = function( id , name )
    {
        $( '#item_creator' ).val( id );
        $( '#author_name' ).html( '<span class="label label-primary">' + name + '</span>');

        $.Joomla("squeezebox").close();
    }

    window.insertCategory = function( id , name )
    {
    	$( '#item_category' ).val( id );
    	$( '#category_name' ).html( name );

    	$.Joomla("squeezebox").close();
    }

});