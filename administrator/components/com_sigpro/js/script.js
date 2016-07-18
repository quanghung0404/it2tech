var $sig = jQuery.noConflict();

function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else
		var expires = "";
	document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
		c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0)
			return c.substring(nameEQ.length, c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name, "", -1);
}

// If we are in Joomla! 1.5 define the functions for validation
if ( typeof (Joomla) === 'undefined') {
	var Joomla = {};
	Joomla.submitbutton = function(pressbutton) {
		submitform(pressbutton);
	};

	Joomla.submitform = function(pressbutton) {
		submitform(pressbutton);
	};

	function submitbutton(pressbutton) {
		Joomla.submitbutton(pressbutton);
	}

}

// Use modal for all new actions
Joomla.submitbutton = function(pressbutton) {
	if (pressbutton === 'add') {
		$sig('#sigProModal').css('margin-left', '-' + ($sig('#sigProModal').outerWidth()) / 2 + 'px');
		window.scrollTo(0,0);
		$sig('#sigProModal').animate({
			'top' : '0'
		}, 'fast');
	} else {
		submitform(pressbutton);
	}
};

// Close modal function
function closeModal() {
	var height = $sig('#sigProModal').outerHeight() + 200;
	$sig('#sigProModal').animate({
		'top' : '-' + height + 'px'
	}, 'fast');
}

//Select K2 item from modal
function jSelectItem(id, title, object) {
	$sig('input[name=newFolder]').val(id);
	closeModal();
	submitform('create');
}

// Custom checkboxes
function setupLabel() {

	// Checkbox variables
	var checkBox = ".sigCheckbox";
	var checkBoxInput = checkBox + " input[type='checkbox']";
	var checkBoxChecked = "sigChecked";
	var checkBoxDisabled = "sigDisabled";

	// Radio variables
	var radio = ".sigRadio";
	var radioInput = radio + " input[type='radio']";
	var radioOn = "sigChecked";
	var radioDisabled = "sigDisabled";

	// Checkboxes
	if ($sig(checkBoxInput).length) {
		$sig(checkBox).each(function() {
			$sig(this).removeClass(checkBoxChecked);
			$sig(this).parents('.sigProGalleryImage').removeClass('selectedImage');
			$sig(this).parents('.sigProGallery').removeClass('sigProSelectedGal');
		});
		$sig(checkBoxInput + ":checked").each(function() {
			$sig(this).parents(checkBox).addClass(checkBoxChecked);
			$sig(this).parents('.sigProGalleryImage').toggleClass('selectedImage');
			$sig(this).parents('.sigProGallery').toggleClass('sigProSelectedGal');
		});
		$sig(checkBoxInput + ":disabled").each(function() {
			$sig(this).parents(checkBox).addClass(checkBoxDisabled);
			$sig(this).parents('.sigProGalleryImage').toggleClass('selectedImage');
			$sig(this).parents('.sigProGallery').toggleClass('sigProSelectedGal');
		});
	}
	/*
	 // Radios
	 if ($sig(radioInput).length) {
	 $sig(radio).each(function(){
	 $sig(this).removeClass(radioOn);
	 });
	 $sig(radioInput + ":checked").each(function(){
	 $(this).parent(radio).addClass(radioOn);
	 });
	 $sig(radioInput + ":disabled").each(function(){
	 $sig(this).parent(radio).addClass(radioDisabled);
	 });
	 };
	 */

}

// DOM ready
$sig(document).ready(function() {
	
	//Browser detection
	if((typeof($sig.browser) != 'undefined' && $sig.browser.msie) || (navigator.userAgent.indexOf("MSIE") != -1)) {
		var bodyBrowserClass = 'isIE isIE' + parseInt($sig.browser.version, 10);
		$sig('body').addClass(bodyBrowserClass);
	} 
	
	//Joomla! version Body class
	function JvClass() {
		if ($sig('#sigPro').hasClass('J25')) {// J 2.5.x
			$sig('body').addClass('isJVersion25');
		} else if ($sig('#sigPro').hasClass('J15')) {// J 1.5.x
			$sig('body').addClass('isJVersion15');
		} else {// J 3.1.x or similar, 3.1 ready
			$sig('body').addClass('isJVersion30');
		}
	}

	JvClass();

	// Append the correct Joomla! version on the logo
	if ($sig('p[align="center"]').length) {
		var JVersionNum = $sig('#content-box + p[align="center"]').text();
		JVersionNum = JVersionNum.replace("Joomla! ", "");
		$sig('#border-top .logo').append('<span class="sigJVersionSmall">' + JVersionNum + '</span>');
	}

	// Auto dimiss system messages after 3 seconds
	window.setTimeout(function() {
		if ($sig('#system-message').length > 0) {
			$sig('#system-message').fadeOut();
		}
		if ($sig('#system-message-container').length > 0) {
			$sig('#system-message-container').fadeOut();
		}
	}, 3000);

	// Drop-downs
	$sig('#sigLang').chosen({
		'disable_search' : true
	});
	$sig('#sorting').chosen({
		'disable_search' : true
	});

	//Custom checkboxes
	$sig("#sigPro").on("click", ".sigCheckbox", function() {
		setupLabel();
	});
	setupLabel();

	// Helper classes
	$sig('table.adminlist tr:even').find('td').addClass('tCellEven');
	$sig('table.adminlist tr:odd').find('td').addClass('tCellodd');

	$sig('.sigAnchor').click(function(e) {
		e.preventDefault();
		var target = $sig(this).attr('href');
		var top = $sig(target).offset().top - 200;
		$sig('html, body').animate({
			scrollTop : top
		}, 500);
	});

	// Cache selectors
	var lastId, topMenu = $sig(".sigSideNav"), topMenuHeight = topMenu.outerHeight() + 15,
	// All list items
	menuItems = topMenu.find("a"),
	// Anchors corresponding to menu items
	scrollItems = menuItems.map(function() {
		var item = $sig($sig(this).attr("href"));
		if (item.length) {
			return item;
		}
	});

	// Bind to scroll
	$sig(window).scroll(function() {
		// Get container scroll position
		var fromTop = $sig(this).scrollTop() + topMenuHeight + 100;

		// Get id of current scroll item
		var cur = scrollItems.map(function() {
			if ($sig(this).offset().top < fromTop)
				return this;
		});
		// Get the id of the current element
		cur = cur[cur.length - 1];
		var id = cur && cur.length ? cur[0].id : "";

		if (lastId !== id) {
			lastId = id;
			// Set/remove active class
			menuItems.parent().removeClass("active").end().filter("[href=#" + id + "]").parent().addClass("active");
		}
	});

	//Dynamically attach buttons to system messages
	$sig('#system-message > dd').append('<span class="sig-icon-cancel-squared sigIconDel" title="Close"><i class="hidden">Close</i></span>');
	// same behaviour for Joomla! 3.0
	$sig('#system-message-container .alert').append('<span class="sig-icon-cancel-squared sigIconDel" title="Close"><i class="hidden">Close</i></span>');

	$sig('.sigIconDel').click(function() {
		$sig(this).parents('dd.message').remove();
		$sig(this).parents('.alert').remove();
	});
	//if there is more than one message show the mass close button
	if ($sig('#system-message dd').length > 1) {
		$sig('#system-message').append('<div class="sigCloseMsg sigTextRight">Close All<span class="sig-icon-cancel-squared sigIconDelAll" title="Close"></span></div>');
		$sig('.sigIconDelAll').click(function() {
			$sig('#system-message-container').remove();
		});
	}
	//same for Joomla! 3.0 as well
	if ($sig('#system-message-container .alert').length > 1) {
		$sig('#system-message-container').append('<div class="sigCloseMsg sigTextRight">Close All<span class="sig-icon-cancel-squared sigIconDelAll" title="Close"></span></div>');
		$sig('.sigIconDelAll').click(function() {
			$sig('#system-message-container').remove();
		});
	}

	// Prevent form submission using ENTER
	$sig('#adminForm').bind('keypress', function(event) {
		if (event.keyCode === 13) {
			return false;
		}
	});

	// Selecting Galleries
	$sig('.sigProGallery input[type="checkbox"]').click(function() {
		$sig(this).parent().parent().toggleClass('sigProSelectedGal');
	});

	// Multi Select Galleries ( mass Adding/Removing classes)
	$sig('#sigPro.sigProGalleries #jToggler').click(function() {
		if ($sig(this).is(':checked')) {
			$sig('.sigProGallery').removeClass('sigProSelectedGal');
			$sig('.sigProGallery').addClass('sigProSelectedGal');
			$sig('.sigCheckbox').removeClass('sigChecked');
			$sig('.sigCheckbox').addClass('sigChecked');
			$sig('.sigProGrid').addClass('sigPaddingTop');
		} else {
			$sig('.sigProGallery').removeClass('sigProSelectedGal');
			$sig('.sigCheckbox').removeClass('sigChecked');
			$sig('.sigProGrid').removeClass('sigPaddingTop');
		}
	});

	// Showing the lower area of the toolbar
	$sig('#sigPro').on("click", 'input[type="checkbox"]', function() {
		var n = $sig("#sigPro .sigProGridColumn input:checked").length;

		// show/hide the lower toolbar area
		if (n > 0) {
			$sig('.sigProLowerToolbar').show();
			$sig('#selectedCount').text(n);
			$sig('.sigProGrid').addClass('sigPaddingTop');

			// proper language strings
			if (n === 1) {
				$sig('#sigSel1').removeClass('hidden');
				$sig('#sigSel2').addClass('hidden');
			} else {
				$sig('#sigSel2').removeClass('hidden');
				$sig('#sigSel1').addClass('hidden');
			}

			// Force close the sidebar if two or more are selected
			if (n > 1) {
				if ($sig('#sigSideBar').hasClass('openSideBar')) {
					$sig('#sigSideBar').removeClass('openSideBar');
					$sig('.sigProGrid').removeClass('sideBarIsOpen');
					$sig('.sigProToolbar').removeClass('sideBarIsOpen');
				}
			}

		} else {// hide the toolbar if no checkbox is checked
			$sig('.sigProLowerToolbar').hide();
			$sig('.sigProGrid').removeClass('sigPaddingTop');
		}
	});

	//Drawer menu
	$sig('.sideToggler').click(function() {
		$sig('#sigSideBar').toggleClass('openSideBar');
		$sig('.sigSlidingItem').toggleClass('sideBarIsOpen');
		return false;
	});

	// AJAX Validation when typing folder name
	$sig('#folder').bind('keyup change', function() {
		if (this.value != this.lastValue) {
			if (this.timer) {
				clearTimeout(this.timer);
			}
			$sig('.sigProProceedButton').css('display', 'none');
			$sig('#sigProValidationStatus').val('');
			$sig('.sigProValidation').html(sigProLanguage[0]).removeClass('sigProValidationFail').removeClass('sigProValidationSuccess').addClass('sigProValidationWorking');
			var url = 'index.php?option=com_sigpro&view=gallery&task=validate&type=' + $sig('input[name=type]').val() + '&folder=' + $sig(this).val() + '&format=json';
			if (this.value === '') {
				$sig('.sigProValidation').html('').removeClass('sigProValidationWorking');
				return;
			}
			this.timer = setTimeout(function() {
				$sig.ajax({
					url : url,
					dataType : 'json',
					type : 'get',
					success : function(response) {
						$sig('.sigProValidation').removeClass('sigProValidationWorking');
						if (response.status === 1) {
							$sig('.sigProValidation').addClass('sigProValidationSuccess');
							$sig('.sigProProceedButton').fadeIn('fast');
						} else {
							$sig('.sigProValidation').addClass('sigProValidationFail');
							$sig('.sigProProceedButton').css('display', 'none');
						}
						$sig('.sigProValidation').html(response.message);
						$sig('#sigProValidationStatus').val(response.status);
					}
				});
			}, 500);
			this.lastValue = this.value;
		}
	});

	// Save new folder
	$sig('.sigProProceedButton').click(function(event) {
		event.preventDefault();
		parent.closeModal();
		Joomla.submitbutton('create');
	});

	// Insert links events (when called by editor)
	var editor = $sig('input[name=editorName]').val();
	if (editor !== '') {
		$sig('.sigProInsertButton').click(function(event) {
			event.preventDefault();
			var gallery = $sig(this).data('path');
			var width = $sig('input[name=width]').val();
			var height = $sig('input[name=height]').val();
			var displayMode = $sig('select[name=displayMode]').val();
			var captionsMode = $sig('select[name=captionsMode]').val();
			var tag;
			if (width || height || displayMode || captionsMode) {
				tag = '{gallery}' + gallery + ':' + width + ':' + height + ':' + displayMode + ':' + captionsMode + '{/gallery}';
			} else {
				tag = '{gallery}' + gallery + '{/gallery}';
			}
			parent.jInsertEditorText(tag, editor);
			parent.$sig.fancybox.close();
		});
	}

	//Aspect Ratio Changing
	$sig('.sigViewLandscape').click(function(event) {
		event.preventDefault();
		$sig('.sigProGalleryImageLink').removeClass('sigPortrait');
		$sig('.sigProGalleryPreviewImage').removeClass('sigPortrait');

		//Toggle highlighted button
		$sig('.sigViewPortrait').removeClass('sigHighlighted');
		$sig(this).addClass('sigHighlighted');

		eraseCookie('sigRatio');
		createCookie('sigRatio', 'landscape', 30);
	});
	//same procedure for the other ratio button
	$sig('.sigViewPortrait').click(function(event) {
		event.preventDefault();
		$sig('.sigProGalleryImageLink').addClass('sigPortrait');
		$sig('.sigProGalleryPreviewImage').addClass('sigPortrait');

		$sig('.sigViewLandscape').removeClass('sigHighlighted');
		$sig(this).addClass('sigHighlighted');

		eraseCookie('sigRatio');
		createCookie('sigRatio', 'portrait', 30);
	});

	//Output the correct ratio
	var ratioCookie = readCookie('sigRatio');
	if (ratioCookie === 'portrait') {
		$sig('.sigViewPortrait').trigger('click');
	}

	// Close modal event
	$sig('.sigProModalCloseButton').click(function(event) {
		event.preventDefault();
		closeModal();
	});

	// Media manager
	// Calculating the correct height
	if ($sig('#sigProMediaManager').length) {
		//let's check if we in a modal
		if ($sig('body').hasClass('contentpane')) {
			var mediaHeight = 600
		} else if ($sig('body').hasClass('isJVersion30')) {
			//if not set the height dynamically for J!30
			var mediaOffset = $sig('#sigProMediaManager').offset();
			var mediaHeight = ($sig(window).height() - mediaOffset.top) - 137;
		} else {
			//if not set the height dynamically for other versions
			var mediaOffset = $sig('#sigProMediaManager').offset();
			var mediaHeight = ($sig(window).height() - mediaOffset.top) - 193;
		}
	}
	// and initializing our media manager
	if ($sig('#sigProMediaManager').length > 0) {
		var elf = $sig('#sigProMediaManager').elfinder({
			url : 'index.php?option=com_sigpro&view=media&task=connector',
			height : mediaHeight,
			lang : sigProMediaManagerLang,
			onlyMimes : ['image', 'text']
		}).elfinder('instance');
	}

	// Gallery view. Uploader, delete and preview gallery image
	if ($sig("#sigProUploader").length > 0) {
		// Uploader
		var token = $sig('#adminForm input[type=hidden]:last').attr('name');
		var name = $sig('#adminForm input[name=folder]').val();
		var type = $sig('#adminForm input[name=type]').val();
		var iOS = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
		$sig("#sigProUploader").pluploadQueue({
			runtimes : 'html5,flash,html4',
			url : 'index.php?option=com_sigpro&view=gallery&task=upload&folder=' + name + '&type=' + type + '&' + token + '=1&format=raw',
			max_file_size : SIGMaxFileSize,
			multiple_queues : true,
			filters : [{
				title : SIGImagesLabel,
				extensions : 'jpg,jpeg,gif,png'
			}],
			unique_names : iOS,
			flash_swf_url : 'components/com_sigpro/js/plupload/Moxie.swf',
			init : {
				UploadComplete : function(uploader, files) {
					$sig('.sigProGridColumn').each(function(index) {
						if (index % 4 === 0) {
							$sig(this).addClass('sigProGridFirstColumn');
						}
					});
				},
				FileUploaded : function(uploader, file, info) {
					var response = $sig.parseJSON(info.response);
					if (response.status === 0) {
						file.status = plupload.FAILED;
						file.name = file.name + ' - ' + response.error;
						uploader.trigger('QueueChanged');
						return false;
					}
					$sig('#sigPro').removeClass('sigProGalleryEmpty');
					var element = $sig('#sigProImageTemplate').clone();
					element.attr('id', 'sigProImage_' + file.name);
					element.find('.sigProImageContainer a').attr('href', response.path).attr('title', response.path).addClass('sigProPreviewButton');
					element.find('.sigProDeleteButton').attr('href', response.name);
					element.find('.sigProGalleryPreviewImage').css("background-image", "url(" + response.url + ")");
					//element.find('img').attr('src', response.path);
					//element.find('img').attr('alt', response.name);
					element.find('input.sigProFilename').val(response.name);
					element.find('input[name="image[]"]').val(response.name);
					element.find('.sigProImageNameValue').html(response.name);
					element.find('.sigProImageSizeValue').html(response.size);
					element.find('.sigProImageDimensionsValue').html(response.width + ' x ' + response.height);
					$sig('.sigProGrid').append(element);
					$sig('a.sigProPreviewButton').unbind('click');
					$sig('a.sigProPreviewButton').swipebox({hideBarsDelay: 0});
				}
			}

		});

		// Preview image in modal
		$sig('a.sigProPreviewButton').swipebox({hideBarsDelay: 0});

		// Delete image
		$sig('#sigPro').on('click', '.sigProDeleteButton', function(event) {
			event.preventDefault();
			var answer = confirm(SIGDeleteWarning);
			if (answer) {
				var container = $sig(this).parents('.sigProGalleryImage');
				$sig('input[name=task]').val('delete');
				$sig('input[name=file]').val($sig(this).attr('href'));
				var form = $sig('#adminForm');
				$sig.post(form.attr('action'), form.serialize() + '&format=raw', function(response) {
					$sig('input[name=file]').val('');
					if (response.status) {
						alert(response.message);
						$sig(container).fadeOut('slow', function() {
							$sig(container).remove();
							$sig('a.sigProPreviewButton').unbind('click');
							$sig('a.sigProPreviewButton').swipebox({hideBarsDelay: 0});
							if ($sig('.sigProGridColumn').length > 1) {
								$sig('.sigProGridColumn').removeClass('sigProGridFirstColumn');
								$sig('.sigProGridColumn').each(function(index) {
									if (index % 4 === 0) {
										$sig(this).addClass('sigProGridFirstColumn');
									}
								});
							} else {
								$sig('#sigPro').addClass('sigProGalleryEmpty');
							}

						});
					} else {
						alert(response.message);
					}
				}, 'json');
			}
		});

		// Language for labels
		$sig('#sigLang').change(function(event) {
			event.preventDefault();
			var url = 'index.php?option=com_sigpro&view=gallery&folder=' + $sig('input[name=folder]').val() + '&sigLang=' + $sig(this).val() + '&type=' + $sig('input[name=type]').val() + '&tmpl=' + $sig('input[name=tmpl]').val() + '&editorName=' + $sig('input[name=editorName]').val();
			var template = $sig('input[name=template]').val();
			if (template) {
				var redirect = url + '&template=' + template;
			} else {
				var redirect = url;
			}
			window.location = redirect;
		});
	}
});
