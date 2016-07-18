EasyBlog.module("layout/placeholder", function($) {

var module = this;

if ($.IE <= 9) {

    var inputWithPlaceholder = "textarea[placeholder]:not(.placeholder), :input[placeholder]:not(.placeholder)";

    EasyBlog.require()
        .library("placeholder")
        .done(function(){

            EasyBlog.fixPlaceholder = function(){
                $(inputWithPlaceholder).placeholder();
            };

            // Initialize placeholder on all input with placeholder
            $(EasyBlog.fixPlaceholder);

            // For input/textarea with placeholder that are rendered later,
            // initialize placeholder on focus
            $(document).on("mouseover", inputWithPlaceholder, function(){
                $(this).placeholder();
            });
        });
}

module.resolve();

});
