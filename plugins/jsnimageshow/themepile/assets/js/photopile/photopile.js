//
// File: photopile.js
// Auth: Brian W. Howell
// Date: 8 May 2014
//
// Photopile image gallery
//
var photopile = (function ($) {

    //---------------------------------------------------------------------------------------------
    //  PHOTOPILE SETTINGS
    //---------------------------------------------------------------------------------------------

    var _ = {
        // Thumbnails
        numLayers: 1,          // number of layers in the pile (max zindex)
        thumbOverlap: 50,         // overlap amount (px)
        thumbRotation: 45,         // maximum rotation (deg)
        thumbBorderWidth: 2,          // border width (px)
        thumbBorderColor: '#FFFFFF',    // border color
        thumbBorderHover: '#EAEAEA',  // border hover color
        thumbShadow: true,
        thumbShadowColor: '#000000',
        thumbWidth: 130,
        thumbHeight: 130,
        draggable: false,       // enable draggable thumbnails
        // Photo container
        fadeDuration: 200,        // speed at which photo fades (ms)
        pickupDuration: 500,        // speed at which photo is picked up & put down (ms)
        photoZIndex: 9999,        // z-index (show above all)
        photoBorder: 10,         // border width around fullsize image
        photoBorderColor: '#FFFFFF',    // border color
        // Show description and title
        showInfo: true,       // include photo description (alt tag) in photo container      
        // Auto Play
        autoPlayGallery: false,       // autoplay the photopile
        autoPlaySpeed: 5000,       // ms
        // Click to action
        clickAction: 'show_original_image',
        openLinkIn: 'current_browser',
        // Some custom param
        resetRotation: true,
        classContainer: '',
		rootURL: '',
        // Images
        loading: '/plugins/jsnimageshow/themepile/assets/images/photopile/loading.gif'  // path to img displayed while gallery/thumbnails loads
    };

    //---- END SETTINGS ----

    // Initializes Photopile
    function init(wrapper, setttings) {
        var ul = wrapper.find('ul.photopile');
        setttings = typeof setttings !== 'undefined' ? setttings : {};
        _ = $.extend({}, _, setttings);
        ul.data('settings', _);

        // display gallery loading image in container div while loading
        wrapper.css({
            'background-repeat': 'no-repeat',
            'background-position': '50%, 50%',
            'background-image': 'url(' + _.rootURL + _.loading + ')'
        });

        // initialize thumbnails and photo container
        ul.data('thumbBorderColor', _.thumbBorderColor);
        ul.data('thumbBorderHover', _.thumbBorderHover);
        ul.data('classContainer', _.classContainer);
        ul.children().each(function () {
            thumb.init($(this), ul);
        });

        photo.init(ul);

        // once gallery has loaded completely
        $(window).load(function () {
            wrapper.css({  // style container
                'background-image': 'none'
            }).children().css({  // display thumbnails
                'opacity': '0',
                'display': 'inline-block'
            }).fadeTo(_.fadeDuration, 1);
            navigator.init(wrapper);  // init navigator
			
            if (_.autoPlayGallery) {
                autoplay(ul);
            }
        });

    } // init

    function autoplay(ul) {
        var nextThumb = ul.children().first();
        var _ = ul.data('settings');
        window.setInterval(function () {
            nextThumb.children().first().click();
            if (nextThumb.hasClass('last')) {
                nextThumb = ul.children().first();
            } else {
                nextThumb = nextThumb.next();
            }
        }, 5000);
    }

    //-----------------------------------------------------
    // THUMBNAIL
    // List-item containing a link to a fullsize image
    //-----------------------------------------------------

    var thumb = {

        active: 'photopile-active-thumbnail',  // active (or clicked) thumbnail class name

        // Initializes thumbnail.
        init: function (thumb, ul) {
            var _ = ul.data('settings');
            self = this;
            thumb.children().css('padding', _.thumbBorderWidth + 'px');
            self.bindUIActions(thumb, _);
            self.setRotation(thumb, _);
            self.setOverlap(thumb, ul);
            self.setRandomZ(thumb, _);

            // make draggable
            if (_.draggable) {
                var x = 0;
                var velocity = 0;
                thumb.draggable({
                    start: function (event, ui) {
                        thumb.addClass('preventClick');
                        thumb.css('z-index', _.numLayers + 2);

                        // unbind mouseover/out so thumb remains above pile
                        ul.children().each(function () {
                            thumb.unbind("mouseover", self.bringToTop);
                            thumb.unbind("mouseout", self.moveDownOne);
                        });
                    },
                    drag: function (event, ui) {
                        velocity = (ui.offset.left - x) * 1.2;
                        var ratio = parseInt(velocity * 100 / 360);
                        thumb.css('transform', 'rotateZ(' + (ratio) + 'deg)');
                        x = ui.offset.left;
                    },
                    stop: function (event, ui) {
                        thumb.css('z-index', numLayers + 1);

                        // re-bind mouseover/out so thumb is moved to top of pile on hover
                        ul.children().each(function () {
                            thumb.bind("mouseover", self.bringToTop);
                            thumb.bind("mouseout", self.moveDownOne);
                        });

                    }
                });
            }
            thumb.css('background', _.thumbBorderColor);
            if (_.thumbShadow) {
                thumb.css('box-shadow', '0 0 5px ' + _.thumbShadowColor);
            } else {
                thumb.css('box-shadow', 'none');
            }
            if (_.thumbHeight > 0) {
                thumb.find('a').css('max-height', _.thumbHeight + "px");
				thumb.find('img').css('max-height', _.thumbHeight + "px");
            }
            if (_.thumbWidth > 0) {
                thumb.find('a').css('max-width', _.thumbWidth + "px");
				thumb.find('img').css('max-width', _.thumbWidth + "px");
            }
        },

        // Binds UI actions to thumbnail.
        bindUIActions: function (thumb, _) {
            var self = this;

            thumb.bind("mouseover", self.bringToTop);
            thumb.bind("mouseout", self.moveDownOne);

            if (_.clickAction == 'show_original_image') {
                // Pickup the thumbnail on click (if not being dragged).
                thumb.click(function (e) {
                    e.preventDefault();
                    if ($(this).hasClass('preventClick')) {
                        $(this).removeClass('preventClick');
                    } else {
                        if ($(this).hasClass(self.active)) return;
                        photo.pickup($(this));
                    }
                });
            } else if (_.clickAction == 'no_action') {
                thumb.find('a').on('click', function () {
                    return false;
                });
            } else {
                if (_.openLinkIn == 'current_browser') {
                    thumb.find('a').attr('target', '_self');
                } else {
                    thumb.find('a').attr('target', '_blank');
                }
            }

            // Prevent user from having to double click thumbnail after dragging.
            thumb.mousedown(function (e) {
                $(this).removeClass('preventClick');
            });

        }, // bindUIActions

        bringToTop: function () {
            $(this).css({
                'background': $(this).parent().data('settings').thumbBorderHover,
                'z-index': _.numLayers + 1
            });
        },

        moveDownOne: function () {
            $(this).css({
                'background': $(this).parent().data('settings').thumbBorderColor,
                'z-index': _.numLayers
            });
        },

        // Setters for various thumbnail properties.
        setOverlap: function (thumb, ul) {
            var _ = ul.data('settings');
            thumb.css('margin', ((_.thumbOverlap * -1) / 2) + 'px');
            ul.parent().css('padding', _.thumbOverlap)
        },
        setZ: function (thumb, layer) {
            thumb.css('z-index', layer);
        },
        setRandomZ: function (thumb, _) {
            thumb.css({'z-index': Math.floor((Math.random() * _.numLayers) + 1)});
        },
        setRotation: function (thumb, _) {
            if (_.resetRotation || thumb.css('transform') == 'none') {
                var min = -1 * _.thumbRotation;
                var max = _.thumbRotation;
                var randomRotation = Math.floor(Math.random() * (max - min + 1)) + min;
                thumb.css({'transform': 'rotate(' + randomRotation + 'deg)'});
            }
        },

        // ----- Active thumbnail -----

        // Sets the active thumbnail.
        setActive: function (thumb) {
            thumb.addClass(this.active);
        },

        // Getters for active thumbnail properties
        getActiveOffset: function () {
            return $('li.' + this.active).offset();
        },
        getActiveHeight: function () {
            return $('li.' + this.active).height();
        },
        getActiveWidth: function () {
            return $('li.' + this.active).width();
        },
        getActiveImgSrc: function () {
            return $('li.' + this.active).children().first().attr('href');
        },
        getActiveRotation: function () {
            var transform = $('li.' + this.active).css("transform");
            var values = transform.split('(')[1].split(')')[0].split(',');
            var angle = Math.round(Math.asin(values[1]) * (180 / Math.PI));
            return angle;
        },

        // Gets the active thumbnail if set, or returns false.
        getActive: function () {
            return ($('li.' + this.active)[0]) ? $('li.' + this.active).first() : false;
        },

        // Returns a shift amount used to better position the photo container
        // on top of the active thumb. Needed because offset is skewed by thumbnail's rotation.
        getActiveShift: function () {
            return ( this.getActiveRotation() < 0 )
                ? -( this.getActiveRotation(thumb) * 0.40 )
                : ( this.getActiveRotation(thumb) * 0.40 );
        },

        // Removes the active class from all thumbnails.
        clearActiveClass: function () {
            $('li.' + this.active).fadeTo(_.fadeDuration, 1).removeClass(this.active);
        }

    }; // thumbnail

    //--------------------------------------------------------------------
    // PHOTO CONTAINER
    // Dynamic container div wrapping an img element that displays the
    // fullsize image associated with the active thumbnail
    //--------------------------------------------------------------------

    var photo = {

        // Photo container elements

        container: $('<div id="photopile-active-image-container"/>'),
        image: $('<img id="photopile-active-image" />'),
        info: $('<div id="photopile-active-image-info"/>'),

        isPickedUp: false,  // track if photo container is currently viewable
        fullSizeWidth: null,   // will hold width of active thumbnail's fullsize image
        fullSizeHeight: null,   // will hold height of active thumbnail's fullsize image
        windowPadding: 40,     // minimum space between container and edge of window (px)

        // Adds photo container elements to DOM.
        init: function (ul) {
            var _ = ul.data('settings');
            // append and style photo container
            $('body').append(this.container);
            this.container.css({
                'display': 'none',
                'position': 'absolute',
                'padding': _.thumbBorderWidth,
                'z-index': _.photoZIndex,
                'background': _.photoBorderColor,
                'background-image': 'url(' + _.rootURL + _.loading + ')',
                'background-repeat': 'no-repeat',
                'background-position': '50%, 50%'
            });

            // append and style image
            this.container.append(this.image);
            this.image.css('display', 'block');

            // append and style info div
            if (_.showInfo) {
                this.container.append(this.info);

                this.info.css('opacity', '0');
            }
        }, // init

        // Simulates picking up a photo from the photopile.
        pickup: function (thumbnail) {
            var _ = thumbnail.parent().data('settings');
            var self = this;
            if (self.isPickedUp) {
                // photo already picked up. put it down and then pickup the clicked thumbnail
                self.putDown(function () {
                    self.pickup(thumbnail);
                });
            } else {
                self.isPickedUp = true;
                thumb.clearActiveClass();
                thumb.setActive(thumbnail);
                self.loadImage(thumb.getActiveImgSrc(), function () {
                    self.image.fadeTo(_.fadeDuration, '1');
                    self.enlarge();
                    $('body').bind('click', function () {
                        self.putDown();
                    }); // bind putdown event to body
                });
            }
        }, // pickup

        // Simulates putting a photo down, or returning to the photo pile.
        putDown: function (callback) {
            var _ = thumb.getActive().parent().data('settings');
            self = this;
            $('body').unbind();
            self.hideInfo();
            navigator.hideControls();
            thumb.setZ(thumb.getActive(), _.numLayers);
            self.container.stop().animate({
                'top': thumb.getActiveOffset().top + thumb.getActiveShift(),
                'left': thumb.getActiveOffset().left + thumb.getActiveShift(),
                'width': thumb.getActiveWidth() + 'px',
                'height': thumb.getActiveHeight() + 'px',
                'padding': _.thumbBorderWidth + 'px'
            }, _.pickupDuration, function () {
                self.isPickedUp = false;
                thumb.clearActiveClass();
                self.container.fadeOut(_.fadeDuration, function () {
                    if (callback) callback();
                });
            });
        },

        // Handles the loading of an image when a thumbnail is clicked.
        loadImage: function (src, callback) {
            var self = this;
            self.image.css('opacity', '0');         // Image is not visible until
            self.startPosition();                   // the container is positioned,
            var img = new Image;                    // the source is updated,
            img.src = src;                          // and the image is loaded.
            img.onload = function () {               // Restore visibility in callback
                self.fullSizeWidth = this.width;
                self.fullSizeHeight = this.height;
                self.setImageSource(src);
                if (callback) callback();
            };
        },

        // Positions the div container over the active thumb and brings it into view.
        startPosition: function () {
            var _ = thumb.getActive().parent().data('settings');
            this.container.css({
                'top': thumb.getActiveOffset().top + thumb.getActiveShift(),
                'left': thumb.getActiveOffset().left + thumb.getActiveShift(),
                'transform': 'rotate(' + thumb.getActiveRotation() + 'deg)',
                'width': thumb.getActiveWidth() + 'px',
                'height': thumb.getActiveHeight() + 'px',
                'padding': _.thumbBorderWidth
            }).fadeTo(_.fadeDuration, '1');
            thumb.getActive().fadeTo(_.fadeDuration, '0');
        },

        // Enlarges the photo container based on window and image size (loadImage callback).
        enlarge: function () {
            var windowHeight = window.innerHeight ? window.innerHeight : $(window).height(); // mobile safari hack
            var availableWidth = $(window).width() - (2 * this.windowPadding);
            var availableHeight = windowHeight - (2 * this.windowPadding);
            if ((availableWidth < this.fullSizeWidth) && ( availableHeight < this.fullSizeHeight )) {
                // determine which dimension will allow image to fit completely within the window
                if ((availableWidth * (this.fullSizeHeight / this.fullSizeWidth)) > availableHeight) {
                    this.enlargeToWindowHeight(availableHeight);
                } else {
                    this.enlargeToWindowWidth(availableWidth);
                }
            } else if (availableWidth < this.fullSizeWidth) {
                this.enlargeToWindowWidth(availableWidth);
            } else if (availableHeight < this.fullSizeHeight) {
                this.enlargeToWindowHeight(availableHeight);
            } else {
                this.enlargeToFullSize();
            }
        }, // enlarge

        // Updates the info div text and makes visible within the photo container.
        showInfo: function () {
            var thumbActive = thumb.getActive();
			
            var _ = thumbActive.parent().data('settings');
			
			var showTitle = thumbActive.attr('data-show-title');
			var showDesc = thumbActive.attr('data-show-description');
            if (showTitle == 'true') {
					if (this.info.find('h3').length == 0)
					{
						if (this.info.find('p').length != 0)
						{
							this.info.find('p').remove();
						}
						this.info.append('<h3></h3>');
					}
                    this.info.children('h3')
                        .html(thumbActive.children('a').children('img').attr('data-title'))
						.removeAttr('class')
                        .attr('class', '')
                        .addClass('jsn-pile-active-title-' + thumbActive.attr('data-random-number'));
               }
			   else
			   {
					if (this.info.find('h3').length != 0)
					{
						this.info.find('h3').remove();
					}
			   }
               if (showDesc == 'true') 
			   {
					if (this.info.find('p').length == 0)
					{
						this.info.append('<p></p>');
					}
					
                    this.info.children('p')
                        .html(thumbActive.children('a').children('img').attr('data-desc'))
						.removeAttr('class')
                        .attr('class', '')
                        .addClass('jsn-pile-active-desc-' + thumbActive.attr('data-random-number'));
                }
				else
				{
					if (this.info.find('p').length != 0)
					{
						this.info.find('p').remove();
					}
				}				
                this.info.css({
                    'bottom': _.photoBorder + 'px',
                    'width': this.container.width() + 'px',
                    'margin-top': -(this.info.height()) + 'px'
                }).fadeTo(_.fadeDuration, 1);

        },

        // Hides the info div.
        hideInfo: function () {
            var _ = thumb.getActive().parent().data('settings');
            if (_.showInfo) {
                this.info.fadeTo(_.fadeDuration, 0);
            }
        },

        // Fullsize image will fit in window. Display it and show nav controls.
        enlargeToFullSize: function () {
            var _ = thumb.getActive().parent().data('settings');
            self = this;
            self.container.css('transform', 'rotate(0deg)').animate({
                'top': ($(window).scrollTop()) + ($(window).height() / 2) - (self.fullSizeHeight / 2),
                'left': ($(window).scrollLeft()) + ($(window).width() / 2) - (self.fullSizeWidth / 2),
                'width': (self.fullSizeWidth - (2 * _.photoBorder)) + 'px',
                'height': (self.fullSizeHeight - (2 * _.photoBorder)) + 'px',
                'padding': _.photoBorder + 'px'
            }, function () {
                self.showInfo();
                navigator.showControls();
            });
        },

        // Fullsize image width exceeds window width. Display it and show nav controls.
        enlargeToWindowWidth: function (availableWidth) {
            var _ = thumb.getActive().parent().data('settings');
            self = this;
            var adjustedHeight = availableWidth * (self.fullSizeHeight / self.fullSizeWidth);
            self.container.css('transform', 'rotate(0deg)').animate({
                'top': $(window).scrollTop() + ($(window).height() / 2) - (adjustedHeight / 2),
                'left': $(window).scrollLeft() + ($(window).width() / 2) - (availableWidth / 2),
                'width': availableWidth + 'px',
                'height': adjustedHeight + 'px',
                'padding': _.photoBorder + 'px'
            }, function () {
                self.showInfo();
                navigator.showControls();
            });
        },

        // Fullsize image height exceeds window height. Display it and show nav controls.
        enlargeToWindowHeight: function (availableHeight) {
            var _ = thumb.getActive().parent().data('settings');
            self = this;
            var adjustedWidth = availableHeight * (self.fullSizeWidth / self.fullSizeHeight);
            self.container.css('transform', 'rotate(0deg)').animate({
                'top': $(window).scrollTop() + ($(window).height() / 2) - (availableHeight / 2),
                'left': $(window).scrollLeft() + ($(window).width() / 2) - (adjustedWidth / 2),
                'width': adjustedWidth + 'px',
                'height': availableHeight + 'px',
                'padding': _.photoBorder + 'px'
            }, function () {
                self.showInfo();
                navigator.showControls();
            });
        },

        // Sets the photo container's image source.
        setImageSource: function (src) {
            this.image.attr('src', src).css({
                'width': '100%',
                'height': '100%',
                'margin-top': '0'
            });
        }

    } // photo

    //----------------------------------------------------------------------
    // NAVIGATOR
    // Collection of div elements used to navigate the photos in gallery
    //----------------------------------------------------------------------

    var navigator = {

        // Navigator controls.
        next: $('<div id="photopile-nav-next" />'),
        prev: $('<div id="photopile-nav-prev" />'),

        init: function (wrapper) {
            photo.container.append(this.next);           // add next control button
            photo.container.append(this.prev);           // add prev control button
            wrapper.find('ul.photopile li:first').addClass('first');  // add 'first' class to first thumb
            wrapper.find('ul.photopile li:last').addClass('last');    // add 'last' class to last thumb
            this.bindUIActions();
        },

        bindUIActions: function () {
            // Bind next/prev event to the left and right arrow controls
            this.next.click(function (e) {
                e.preventDefault();
                navigator.pickupNext();
            });
            this.prev.click(function (e) {
                e.preventDefault();
                navigator.pickupPrev();
            });

            $(document.documentElement).keyup(function (e) {
                if (e.keyCode == 39) {
                    navigator.pickupNext();
                } // right arrow clicks
                if (e.keyCode == 37) {
                    navigator.pickupPrev();
                } // left arrow clicks
            });
        }, // bindUIActions

        pickupNext: function () {
            var activeThumb = thumb.getActive();
            if (!activeThumb) return;
            if (activeThumb.hasClass('last')) {
                photo.pickup(activeThumb.parent().children().first());  // pickup first
            } else {
                photo.pickup(activeThumb.next('li')); // pickup next
            }
        },

        pickupPrev: function () {
            var activeThumb = thumb.getActive();
            if (!activeThumb) return;
            if (activeThumb.hasClass('first')) {
                photo.pickup(activeThumb.parent().children().last());  // pickup last
            } else {
                photo.pickup(activeThumb.prev('li')); // pickup prev
            }
        },

        hideControls: function () {
            this.next.css('opacity', '0');
            this.prev.css('opacity', '0');
			this.next.parent().css('overflow', 'hidden');
        },

        showControls: function () {
			this.next.parent().css('overflow', 'visible');
            this.next.css('opacity', '100');
            this.prev.css('opacity', '100');
        }

    }; // navigator

    return {
        scatter: init,
        autoplay: autoplay
    }

})(jsnThemePilejQuery); // photopile
