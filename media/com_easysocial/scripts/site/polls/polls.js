EasySocial.module('site/polls/polls', function($){

	var module = this;

    var lang = EasySocial.options.momentLang;

	EasySocial.require()
    .library('datetimepicker', 'moment/' + lang)
	.view('site/loading/small')
	.done(function($) {

		EasySocial.Controller('Polls',
		{
			defaultOptions: {
				// Check every 30 seconds by default.
				interval	: 30,

				// Properties
				checknew	: null,
				source      : null,
				sourceId    : null,
				autoload	: true,

				// Elements
				"{itemList}": "[data-polls-list]",
				"{copiedItem}": "[data-polls-item-copied]",

                // inputs
                "{pollTitle}" : "[data-polls-title]",
                "{pollItemList}" : "[data-polls-item]",
                "{pollItem}" : "[data-polls-item-text]",
                "{pollMultiple}" : "[data-polls-multiple]",
                "{pollExpiry}": "[data-polls-expirydate]",
                "{itemTobeRemoved}": "[data-polls-tobe-removed]",
                "{pollSourceId}": "[data-polls-sourceid]",


                //hidden inputs
                "{pollId}": "[data-polls-id]",
                "{pollUid}": "[data-polls-uid]",
                "{pollElement}": "[data-polls-element]",

				// buttons
				"{addItem}": "[data-polls-add]",
				"{deleteItem}": "[data-polls-item-delete]",

				view: {
					loadingContent: "site/loading/small"
				}
			}
		}, function(self) {
			return {

				init : function()
				{
					// Implement stream item controller.
					// self.item().addController(EasySocial.Controller.Stream.Item, {
					// 	"{parent}": self
					// });


                    self.pollExpiry().addController('EasySocial.Controller.Polls.Datetime', {
                        '{parent}': self
                    });
				},

				"{deleteItem} click": function(ele, event) {
					//lets check if this is the last item or not.
					// if yes, do not delete.
					// var len = $("[data-polls-item]").length;
                    var len = self.pollItemList().length;

					if (len <= 1) {
						return;
					}

					// remove this item from the list.
					var item = $(ele).closest("[data-polls-item]");

                    var hasId = item.attr('data-id');

                    if (typeof hasId !== typeof undefined && hasId !== false) {
                        var itemId = item.data('id');

                        if (itemId != undefined || itemId != '') {
                            if (self.itemTobeRemoved) {
                                var temp = self.itemTobeRemoved().val();
                                temp = (temp == '') ? itemId : temp + ',' + itemId;
                                self.itemTobeRemoved().val(temp);
                            }
                        }
                    }

                    item.remove();

				},

				"{addItem} click": function(ele, event) {
					// lets copy the copied version and append into item list.
					var copied = self.copiedItem().clone();

					//remove data attribute.
					copied
						.removeAttr("data-polls-item-copied")
						.attr("data-polls-item", "")
						.show();

					//travel inside the input textbox and change the name.
					copied.find("input[name='copied']")
                        .attr("name", "items[]")
                        .attr("data-polls-item-text","");

					self.itemList().append(copied);
				},

                "toData" : function(){

                    var arritems = [];

                    var data = {
                        id: self.pollId().val(),
                        uid: self.pollUid().val(),
                        element: self.pollElement().val(),
                        title: self.pollTitle().val(),
                        items: arritems,
                        multiple: self.pollMultiple().is( ':checked' ) ? 1 : 0,
                        toberemove: '',
                        sourceid: self.pollSourceId().val()
                    };

                    self.pollExpiry().trigger('datetimeExport', [data]);

                    if (self.pollItem().length > 0) {
                        $.each(self.pollItem(), function(idx, item) {
                            var text = $(item);

                            // remove this item from the list.
                            var curItem = $(item).closest("[data-polls-item]");
                            var hasId = curItem.attr('data-id');
                            var itemId = '0';

                            if (typeof hasId !== typeof undefined && hasId !== false) {
                                itemId = curItem.data('id');
                            }

                            if (text.val() != '') {
                                finalItem = {"id": itemId, "text": text.val()};
                                arritems.push(finalItem);
                            }
                        });

                        if (self.itemTobeRemoved) {
                            var tobedelete = self.itemTobeRemoved().val();
                            data.toberemove = tobedelete;
                        }

                        data.items = arritems;
                    }

                    return data;
                },

                "validateForm" : function(){
                    var data = self.toData();

                    if ($.isEmpty(data.title)
                        || data.items.length == 0) {
                        return false;
                    }

                    if (data.items.length <= 0) {
                        return false;
                    }

                    return true;
                }

			}
		});


        EasySocial.Controller('Polls.Vote',
        {
            defaultOptions: {

                isMultiple: false,

                // Elements
                "{itemList}": "[data-polls-list]",
                "{resultList}": "[data-polls-results-list]",
                "{voteItem}" : "[data-vote-item]",
                "{voteOption}" : "[data-vote-item-option]",
                "{voteNotice}" : "[data-polls-notice]",
                "{resultBtnContainer}": "[data-polls-result-button-div]",

                "{editBtn}": "[data-polls-edit-button]",
                "{viewVoterBtn}" : "[data-poll-count-button]",

                view: {
                    loadingContent: "site/loading/small"
                }
            }
        }, function(self,opts,base) {
            return {

                init : function()
                {
                    // poll id
                    opts.id = base.data("id");

                    // console.log(self.options.isMultiple);
                    // console.log(opts.id);
                },

                "updateProgressBar" : function() {

                    var items = self.voteItem();
                    var total = 0;

                    $.each(items, function(idx, item) {
                        var itemCnt = $(item).data('count');
                        total = total + itemCnt;
                    });


                    $.each(items, function(idx, item) {

                        var itemCnt = $(item).data('count');
                        var id = $(item).data('id');
                        var percentage = (itemCnt / total) * 100;

                        var elemData = '[data-poll-bar-' + id + '] .progress-bar';
                        var elemLabel = '[data-poll-count-label-' + id + ']';

                        $(elemData).css('width', percentage + '%');
                        $(elemLabel).text(itemCnt);
                        self.viewVoterBtn().removeClass('hide');

                    });

                },

                "showResults" : function() {
                    self.resultList().removeClass('hide');
                    self.resultBtnContainer().addClass('hide');
                },

                "toData" : function(){

                    var arritems = [];

                    var data = {
                        title: self.pollTitle().val(),
                        items: arritems,
                        multiple: self.pollMultiple().is( ':checked' ) ? 1 : 0,
                        sourceid: self.pollSourceId().val()
                    };

                    self.pollExpiry().trigger('datetimeExport', [data]);

                    if (self.pollItem().length > 0) {
                        $.each(self.pollItem(), function(idx, item) {
                            var text = $(item);

                            if (text.val() != '') {
                                arritems.push(text.val());
                            }
                        });

                        data.items = arritems;
                    }

                    return data;
                },

                "{voteOption} change": function(ele, event){

                    var item = $(ele);

                    var isChecked = item.is(':checked') ? true : false;
                    var itemId = item.data('id');

                    if (isChecked) {
                        self.updateVote(itemId, 'vote', ele);
                    } else {
                        self.updateVote(itemId, 'unvote', ele);
                    }

                    if (isChecked && !self.options.isMultiple) {
                        // now we need to uncheck the other 'checked' item.
                        $.each(self.voteOption(), function(idx, item){
                            var opt = $(item);

                            if (opt.data('id') != itemId && opt.is(':checked')) {
                                selectItem = opt.data('id');
                                opt.prop('checked', false);

                                self.updateVote(selectItem, 'unvote', opt);
                            }
                        });
                    }

                    return;
                },

                "{viewVoterBtn} click": function(ele, event) {
                    var voteItem = $(ele).closest('[data-vote-item]');
                    var itemId = $(voteItem).data('id');


                    EasySocial.ajax('site/views/polls/voters', {
                        id: self.options.id,
                        itemid: itemId
                    })
                    .done(function(content) {

                        if ($.trim(content) == "") {
                            return;
                        }

                        var elemData = '[data-poll-voters-' + itemId + ']';

                        $(elemData)
                            .html(content)
                            .removeClass('hide');

                    })
                    .fail(function() {

                    });

                },

                "updateVote" : function(itemId, act, ele) {

                    EasySocial.ajax("site/controllers/polls/vote", {
                        id : self.options.id,
                        itemId : itemId,
                        act : act
                    })
                    .done(function(msg, items){

                        var divParent = $(ele).closest('[data-vote-item]');
                        var count = $(divParent).data('count');

                        if (act == 'vote') {
                            count = count + 1;
                        } else if (act == 'unvote') {
                            count = count - 1;
                        }

                        // lets update the count
                        $(divParent).data('count', count);

                        // console.log($(divParent).data('count'));

                        // update progress bar
                        self.updateProgressBar();


                        // show view button
                        // self.viewBtn().removeClass('hide');

                        // self.voteNotice()
                        //     .removeClass('hide')
                        //     .addClass('alert-success')
                        //     .text(msg)
                        //     .delay(5000)
                        //     .fadeOut();

                    })
                    .fail(function(msgObj){

                        self.voteNotice()
                            .removeClass('hide')
                            .text(msgObj.message);

                    });


                },


                "{editBtn} click": function(ele, event) {

                },

                "{resultBtn} click": function(ele, event) {
                    self.showResults();
                },

                "{voteBtn} click": function(ele, event) {

                    var selectItems = [];

                    $.each(self.voteOption(), function(idx, item){
                        var opt = $(item);

                        if (opt.is(':checked')) {
                            selectItems.push(opt.data('id'));
                        }
                    });

                    if (selectItems.length == 0) {
                        // nothing selected. abort action.
                        return;
                    }

                    // hide the vote button
                    self.voteBtn().hide();

                    //show result
                    self.showResults();

                    EasySocial.ajax("site/controllers/polls/vote", {
                        id     : self.options.id,
                        items   : selectItems
                    })
                    .done(function(msg, items){

                        // update progress bar
                        self.updateProgressBar(items);

                        // show view button
                        self.viewBtn().removeClass('hide');

                        self.voteNotice()
                            .removeClass('hide')
                            .addClass('alert-success')
                            .text(msg)
                            .delay(5000)
                            .fadeOut();

                    })
                    .fail(function(msgObj){

                        self.voteNotice()
                            .removeClass('hide')
                            .text(msgObj.message);

                    });
                }

            }
        });

        EasySocial.Controller('Polls.Datetime', {
            defaultOptions: {
                '{picker}': '[data-picker]',
                '{toggle}': '[data-picker-toggle]',
                '{datetime}': '[data-datetime]'
            }
        }, function(self) {
            return {
                init: function() {

                    var minDate = new $.moment();
                    var yearto = new Date().getFullYear() + 10;
                    var dateFormat = 'DD-MM-YYYY hh:mm A';

                    // Minus 1 on the date to allow today
                    minDate.date(minDate.date() - 1);

                    self.picker()._datetimepicker({
                        component: "es",
                        useCurrent: false,
                        format: dateFormat,
                        minDate: minDate,
                        maxDate: new $.moment({y: yearto}),
                        icons: {
                            time: 'glyphicon glyphicon-time',
                            date: 'glyphicon glyphicon-calendar',
                            up: 'glyphicon glyphicon-chevron-up',
                            down: 'glyphicon glyphicon-chevron-down'
                        },
                        sideBySide: false,
                        pickTime: 1,
                        minuteStepping: 1,
                        language: lang
                    });

                    var curActiveDateTime = self.element.data('value');
                    if (curActiveDateTime != '') {
                        var dateObj = $.moment(curActiveDateTime);
                        self.datetimepicker('setDate', dateObj);
                    }
                },

                datetimepicker: function(name, value) {
                    return self.picker().data('DateTimePicker')[name](value);
                },

                '{toggle} click': function() {
                    self.picker().focus();
                },

                '{picker} dp.change': function(el, ev) {
                    self.setDateValue(ev.date.toDate());

                    //self.parent.element.trigger('event' + $.String.capitalize(self.options.type), [ev.date]);
                },

                setDateValue: function(date) {
                    // Convert the date object into sql format and set it into the input
                    self.datetime().val(date.getFullYear() + '-' +
                                        ('00' + (date.getMonth()+1)).slice(-2) + '-' +
                                        ('00' + date.getDate()).slice(-2) + ' ' +
                                        ('00' + date.getHours()).slice(-2) + ':' +
                                        ('00' + date.getMinutes()).slice(-2) + ':' +
                                        ('00' + date.getSeconds()).slice(-2));
                },

                '{self} datetimeExport': function(el, ev, data) {
                    data['expirydate'] = self.datetime().val();
                }
            }
        })

		module.resolve();

	});
});
