(function(){

// module factory: start

var moduleFactory = function($) {
// module body: start

var module = this; 
var exports = function() { 

$.fn.pageslide = function(content, direction) {

    var viewport = this.find(".pageslide-viewport"),

        page =
            // Create page
            $('<div class="pageslide-page active"></div>')
                .append(content)

                // Insert page to viewport
                [direction=="prev" ? "prependTo" : "appendTo"](viewport)

                // Get all siblings
                .siblings()

                // Immediately detach page that are already deactivating
                .filter(".is-deactivating")
                .detach()
                .end()

                // Add is-deactivating state to remaining siblings
                .addClass("is-deactivating")

                // And remove its active class
                .removeClass("active")
                .end();

        viewport.trigger("pageslidestart");

        // Get container and switch class
        container = this.switchClass("fx-" + direction);

        setTimeout(function(){
            container.removeClass("fx-prev fx-next");
            page.siblings().detach();
            viewport.trigger("pageslidestop");
        }, 500);

    return this;
};
}; 

exports(); 
module.resolveWith(exports); 

// module body: end

}; 
// module factory: end

FD40.module("pageslide", moduleFactory);

}());