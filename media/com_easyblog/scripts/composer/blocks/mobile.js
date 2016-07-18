EasyBlog.module("composer/blocks/mobile", function($) {

    var module = this;

    EasyBlog.Controller("Composer.Blocks.Mobile", {
        defaultOptions: $.extend({
            "{blipp}": "[data-eb-blipp]",
            "{viewport}": ".eb-composer-viewport",
            "{actionBar}": "[data-eb-composer-actions]"
        }, EBD.selectors),
    }, function(self, opts, base, composer, blocks, currentBlock) { 

        return {

            init: function() {
                blocks = self.blocks;
                composer = blocks.composer;
            },

            "{blipp} mousedown": function(blipp, event) {

                event.stopPropagation();
                event.preventDefault();

                var action;

                $(document)
                    .off("mousemove.blip mouseup.blip")
                    .on("mousemove.blip", function(event){

                        var position = $.getPointerPosition(event),
                            viewport = self.viewport(),
                            viewportOffset = viewport.offset(),
                            topDropArea = 30,
                            rightDropArea = viewport.width() - 10,
                            bottomDropArea = viewport.height() - 30,
                            leftDropArea = viewportOffset.left + 30;

                        // console.log(self.viewport().offset().left, position);

                        // Position blip
                        var leftForBlip = position.x - viewportOffset.left;
                        var topForBlip = position.y - viewportOffset.top;
                        
                        blipp.css("left", leftForBlip);
                        blipp.css("top", topForBlip);

                        action = null;

                        // If the y axis is in the top region, we want to allow the user to create a block
                        if (position.y < topDropArea) {
                            action = 'addBlock';
                        }

                        // If the y axis is in the bottom region, we want to allow the user to remove a block
                        if (position.y > bottomDropArea) {
                            action = 'removeBlock';
                        }

                        // If the x axis is in the left area region, we want to allow the user to view the document explorer
                        if (position.x < leftDropArea) {
                            action = 'viewBlockTree';
                        }

                        if (position.x > rightDropArea) {
                            action = 'moveBlock';
                        }
                    })
                    .on("mouseup.blip", function(){

                        $(document).off('mousemove.blip');

                        // Reset
                        self.actionBar().show();
                        composer.blocks.droppable.destroyDropzones();
                        composer.sidebar.deactivate('blocks');
                        composer.sidebar.deactivate('explorer');

                        // Perform specific actions when blipp is dropped on the addBlock region
                        if (action == 'addBlock') {
                            composer.sidebar.activate('blocks');
                            composer.blocks.droppable.populateDropzones();

                            self.trigger('ComposerMobileAddBlock');

                            return;
                        }

                        // When moving block, we need to display the dropzones and remove the selected block that the user clicked
                        if (action == 'moveBlock') {

                            // Display the dropzones
                            composer.blocks.droppable.populateDropzones();

                            // Override the behavior when a block is clicked
                            blocks.getAllBlocks().on("click.tapremove", function(event){
                                event.stopPropagation(); 
                                var block = $(this);

                                // Hide the block
                                block.addClass('hide');
                            });

                            return;
                        }


                        // Perform specific actions when blipp is dropped on the removeBlock region
                        if (action == 'removeBlock') {

                            // Hide the action bar
                            self.actionBar().hide();

                            blocks.getAllBlocks().on("click.tapremove", function(event){
                                event.stopPropagation(); 
                                var block = $(this);
                                blocks.removeBlock(block);
                            });

                            self.trigger('ComposerMobileRemoveBlock');

                            return;
                        }

                        // Renders the document explorer
                        if (action == 'viewBlockTree') {
                            EasyBlog.Composer.sidebar.activate("explorer");

                            self.trigger('ComposerMobileViewTree');

                            return;
                        }
                    });
            }
        }
    });

    module.resolve();

});
