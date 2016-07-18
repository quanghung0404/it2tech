EasyBlog.module("composer/panels/seo", function($){

    var module = this;

    EasyBlog.require()
        .library("textboxlist")
        .done(function(){

            EasyBlog.Controller("Composer.Panels.Seo", {
                defaultOptions: {
                    // Templates
                    "{keywordTemplate}": "[data-keyword-template]",

                    // Meta description
                    "{metaInput}": "[data-meta-description]",
                    "{metaCounter}": "[data-meta-counter]",

                    // Meta keywords
                    "{keywordCounter}": "[data-keyword-counter]",
                    "{textboxlist}": "[data-eb-composer-seo-keywords-textboxlist]",
                    "{autofillButton}": "[data-eb-composer-seo-keywords-autofill-button]",
                    "{jsondata}": "[data-eb-composer-keywords-jsondata]"
                }
            }, function(self, opts, base, suggestions, selection, tagger) {

                return {

                    init: function() {

                        if (self.metaInput().length == 0) {
                            // this mean the seo panel disabled.
                            return;
                        }

                        // Get the tag template
                        $.template('composer/textboxlist/keywords', self.keywordTemplate().detach().html());

                        self.textboxlist()
                            .textboxlist({
                                component: "eb",
                                view: {
                                    item: 'composer/textboxlist/keywords'
                                }
                            });

                        self.tagger = self.textboxlist().textboxlist("controller");

                        var i = 0;
                        var keywords = JSON.parse(self.jsondata().val());

                        $.each(keywords, function(i, title) {
                            self.tagger.addItem(title);
                        });
                    },

                    // Slightly debounced so mass add/removal only gets executed once
                    "{textboxlist} listChange": $.debounce(function() {
                        self.keywordCounter()
                            .html(self.tagger.getAddedItems().length);
                    }, 15),

                    "{metaInput} keyup": function(el, event) {
                        var length = $(el).val().length;

                        self.metaCounter().html(length);
                    },

                    "{autofillButton} click": function(autofillButton) {
                        var tagger = self.textboxlist().textboxlist("controller");
                        var content = EasyBlog.Composer.document.getText();
                        var parent = $(autofillButton).parent();

                        // Show loading
                        $(parent).addClass('is-loading');

                        EasyBlog.ajax('site/views/composer/suggestKeywords', {
                            "data": content
                        }).done(function(keywords){

                            $(parent).removeClass('is-loading');

                            if (keywords) {
                                $.each(keywords, function(i, tag) {
                                    tagger.addItem(tag.title);
                                });

                                self.metaCounter().text(keywords.length);
                            }
                        });
                    },

                    "{self} composerSave": function(base, event, save) {

                        if (self.metaInput().length == 0) {
                            save.data.keywords = '';
                        } else {
                            save.data.keywords = $.pluck(self.tagger.getAddedItems(), "title").join(", ");
                        }
                    }
                }
            });

            module.resolve();

        });

});
