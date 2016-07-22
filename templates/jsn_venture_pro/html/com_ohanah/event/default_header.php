<? defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<? jimport('joomla.html.parameter'); ?>
<? jimport( 'joomla.filesystem.file' ); ?>

<?
$params = JComponentHelper::getParams('com_ohanah');

if (isset($module)) $isModule = true; else $isModule = false;
if (!$isModule && (KRequest::get('get.view', 'string') == 'event')) $isSingle = true; else $isSingle = false;
if (!$isModule && (KRequest::get('get.view', 'string') == 'registration')) $isRegistration = true; else $isRegistration = false;
if (!$isModule && (KRequest::get('get.view', 'string') == 'events')) $isList = true; else $isList = false;

if (($isRegistration && $params->get('showLinkToCategoryInRegistration', 1)) || ($isList && $params->get('showLinkToCategoryInList', 1)) || ($isSingle && $params->get('showLinkToCategoryInSingle', 1)) || ($isModule && $params->get('showLinkToCategoryInModule', 1))) { 	$showLinkToCategory = true; } else $showLinkToCategory = false;
if (($isRegistration && $params->get('showLinkToVenueInRegistration', 1)) || ($isList && $params->get('showLinkToVenueInList', 1)) || ($isSingle && $params->get('showLinkToVenueInSingle', 1)) || ($isModule && $params->get('showLinkToVenueInModule', 1))) { $showLinkToVenue = true; } else $showLinkToVenue = false;
if (($isRegistration && $params->get('showLinkToRecurringSetInRegistration', 1)) || ($isList && $params->get('showLinkToRecurringSetInList', 1)) || ($isSingle && $params->get('showLinkToRecurringSetInSingle', 1)) || ($isModule && $params->get('showLinkToRecurringSetInModule', 1))) { $showLinkToRecurringSet = true; } else $showLinkToRecurringSet = false;
if (($isRegistration && $params->get('showLinkToSaveToCalInRegistration', 1)) || ($isList && $params->get('showLinkToSaveToCalInList', 1)) || ($isSingle && $params->get('showLinkToSaveToCalInSingle', 1)) || ($isModule && $params->get('showLinkToSaveToCalInModule', 1))) { $showLinkToSaveToCal = true; } else $showLinkToSaveToCal = false;
if (($isRegistration && $params->get('showCostInfoInRegistration', 1)) || ($isList && $params->get('showCostInfoInList', 1)) || ($isSingle && $params->get('showCostInfoInSingle', 1)) || ($isModule && $params->get('showCostInfoInModule', 1))) { $showCostInfo = true; } else $showCostInfo = false;
if (($isRegistration && $params->get('showPlacesLeftInRegistration', 1)) || ($isList && $params->get('showPlacesLeftInList', 1)) || ($isSingle && $params->get('showPlacesLeftInSingle', 1)) || ($isModule && $params->get('showPlacesLeftInModule', 1))) { $showPlacesLeft = true; } else $showPlacesLeft = false;
if (($isRegistration && $params->get('showEventPictureInRegistration', 1)) || ($isList && $params->get('showEventPictureInList', 1)) || ($isSingle && $params->get('showEventPictureInSingle', 1)) || ($isModule && $params->get('showEventPictureInModule', 1))) { $showEventPicture = true; } else { $showEventPicture = false;}
if (($isRegistration && $params->get('showEventFullDescriptionInRegistration', 0)) || ($isList && $params->get('showEventFullDescriptionInList', 0)) || ($isSingle && $params->get('showEventFullDescriptionInSingle', 1)) || ($isModule && $params->get('showEventFullDescriptionInModule', 0))) { $showEventFullDescription = true; } else $showEventFullDescription = false;
if (($isRegistration && $params->get('showEventDescriptionSnippetInRegistration', 1)) || ($isList && $params->get('showEventDescriptionSnippetInList', 1)) || ($isSingle && $params->get('showEventDescriptionSnippetInSingle', 0)) || ($isModule && $params->get('showEventDescriptionSnippetInModule', 1))) { $showEventDescriptionSnippet = true; } else $showEventDescriptionSnippet = false;
if (($isRegistration && $params->get('showEventDateInRegistration', 1)) || ($isList && $params->get('showEventDateInList', 1)) || ($isSingle && $params->get('showEventDateInSingle', 1)) || ($isModule && $params->get('showEventDateInModule', 1))) { $showEventDate = true; } else $showEventDate = false;
if (($isRegistration && $params->get('showEventAddressInRegistration', 1)) || ($isList && $params->get('showEventAddressInList', 1)) || ($isSingle && $params->get('showEventAddressInSingle', 1)) || ($isModule && $params->get('showEventAddressInModule', 1))) { $showEventAddress = true; } else $showEventAddress = false;
if (($isRegistration && $params->get('showBigDateBadgeInRegistration', 0)) || ($isList && $params->get('showBigDateBadgeInList', 0)) || ($isSingle && $params->get('showBigDateBadgeInSingle', 0)) || ($isModule && $params->get('showBigDateBadgeInModule', 0))) { $showBigDateBadge = true; } else $showBigDateBadge = false;
if (($isRegistration && $params->get('showReadMoreInRegistration', 0)) || ($isList && $params->get('showReadMoreInList', 1)) || ($isSingle && $params->get('showReadMoreInSingle', 1)) || ($isModule && $params->get('showReadMoreInModule', 1))) { $showReadMoreLink = true; } else $showReadMoreLink = false;
if ($params->get('showRegisterInList', 1)) $showRegisterInList = true; else $showRegisterInList = false;
if ($params->get('showRegisterInModule', 1)) $showRegisterInModule = true; else $showRegisterInModule = false;
if (($isRegistration && $params->get('showTimeInRegistration', 1)) || ($isList && $params->get('showTimeInList', 0)) || ($isSingle && $params->get('showTimeInSingle', 0)) || ($isModule && $params->get('showTimeInModule', 0))) { $showTime = true; } else $showTime = false;
if (($isRegistration && $params->get('shortMonthInRegistration', 1)) || ($isList && $params->get('shortMonthInList', 0)) || ($isSingle && $params->get('shortMonthInSingle', 0)) || ($isModule && $params->get('shortMonthInModule', 0))) { $shortMonth = true; } else $shortMonth = false;

$pageparameters =& JFactory::getApplication()->getPageParameters();

if (!$event->id && $id = $pageparameters->get('id')) {
	$event = $this->getService('com://site/ohanah.model.events')->set('id', $id)->getItem();
}

if ($params->get('loadJquery')) {
	if (JVersion::isCompatible('3.0')) {
    	JHtml::_('jquery.framework');
	} else {
	    if (!JFactory::getApplication()->get('jquery'))  {
	        JFactory::getDocument()->addScript(JURI::root(1).'/media/com_ohanah/js/jquery.min.js');
	        JFactory::getDocument()->addScript(JURI::root(1).'/media/com_ohanah/js/jquery-migrate-1.2.1.min.js');
	        JFactory::getApplication()->set('jquery', true);
	    }
	}
}

JFactory::getDocument()->addScript(JURI::root(1).'/media/com_ohanah/jquery-lightbox-0.5/js/jquery.lightbox-0.5.min.js');
JFactory::getDocument()->addStyleSheet(JURI::root(1).'/media/com_ohanah/jquery-lightbox-0.5/css/jquery.lightbox-0.5.css');

$featured_class = "";
if ($event->featured) {$featured_class = " featured_event";}
$single_class = "";
if ($isSingle ) {$single_class = " single_event_view";}
$soldout_class = "";
if ($event->limit_number_of_attendees AND !($event->countAttendees() < $event->attendees_limit)) {
	$soldout_class = " soldout";
}

if (!function_exists('endswith')) {
	function endswith($string, $test) {
	    $strlen = strlen($string);
	    $testlen = strlen($test);
	    if ($testlen > $strlen) return false;
	    return substr_compare($string, $test, -$testlen) === 0;
	}
}

if (!function_exists('remove_trailing_tag')) {
	function remove_trailing_tag($text) {
		if (endswith($text, '<br /')) { $text = substr($text, 0, strlen($text) - 5); }
		if (endswith($text, '<br ')) { $text = substr($text, 0, strlen($text) - 4); }
		if (endswith($text, '<br')) { $text = substr($text, 0, strlen($text) - 3); }
		if (endswith($text, '<b')) { $text = substr($text, 0, strlen($text) - 2); }
		if (endswith($text, '<')) { $text = substr($text, 0, strlen($text) - 1); }

		return $text;
	}
}
?>

<div class="event_detail_container <?=$featured_class?> <?=$single_class?> <?="cat-".$event->ohanah_category_id;?> <?="venue-".$event->ohanah_venue_id;?><?=$soldout_class?>" itemscope itemtype="http://schema.org/Event">

	<? if ($showBigDateBadge) : ?>
		<div class="event_date_flyer_container">
			<div class="event_date"  id="event_date_day">
				<div class="event_date_day">
					<? $eventDateTimeStamp = strtotime($event->date); ?>
					<?= strftime('%d', $eventDateTimeStamp) ?>
				</div>
				<div class="event_date_month">
					<?= JText::_(date('F', $eventDateTimeStamp)."_SHORT") ?>
				</div>
				<div class="event_date_year">
					<?= strftime('%Y', $eventDateTimeStamp) ?>
				</div>
			</div>
		</div>
	<? endif ?>

	<div class="event_detail_title">
		<span  itemprop="name" style="display:none;"><?=$event->title?></span>
		<? if ($params->get('itemid')) $itemid = '&Itemid='.$params->get('itemid'); else $itemid = '&Itemid='.KRequest::get('get.Itemid', 'int'); ?>
		<? if (!$isSingle && !$isRegistration) : ?>
			<h2><a href="<?=@route('option=com_ohanah&view=event&id='.$event->id.$itemid)?>" itemprop="url"><?=$event->title?></a></h2>
		<? endif ?>
	</div>

	<? if (!$event->isPast()) : ?>
		<div class="ohanah-event-ticket-info">
		<? if ($showPlacesLeft) : ?>
			<? if ($event->limit_number_of_attendees) : ?>
				<span class="ohanah-event-places-left"><?=JText::_('OHANAH_PLACES_LEFT')?>: <? $diff = ($event->attendees_limit - $event->countAttendees()); if ($diff < 0) $diff = 0; echo $diff ?></span>
				<? if ($showCostInfo) : ?><? endif ?>
			<? endif ?>
		<? endif ?>

		<? if ($showCostInfo) : ?>
			<span class="ohanah-event-ticket-cost"><? if ($event->ticket_cost) : ?><?=$event->ticket_cost?> <span class="currency"><?=$event->payment_currency?></span><? else : ?><?=JText::_('OHANAH_FREE')?><? endif ?></span>
		<? endif ?>
		</div>
	<? endif ?>

	<? if ($showEventDate || $showTime) : ?>
	<div class="event_detail_time">
		<div class="date_icon"></div>
		<h3 style="display:inline">
			<?=@helper('com://admin/ohanah.template.helper.datetime.format2Dates', array(
					'startDate' => $event->date,
					'endDate' => $event->end_date,
					'startTime' => $event->start_time,
					'endTime' => $event->end_time,
					'showTime' => $showTime,
					'showDate' => $showEventDate,
					'showEnd' => $event->end_time_enabled,
					'shortMonth' => $shortMonth,
				));
			?>
		</h3>
	</div>
	<? endif ?>

	<? if ($showLinkToSaveToCal) : ?>
		<span class="save_to_cal">
			<? $url = @route('option=com_ohanah&view=event&id='.$event->id); ?>
			<? if (strpos($url, '?')) $url .= '&format=ics'; else $url .= '?format=ics'; ?>
			<h3 style="display:inline"><a href="<?=$url?>"><?=JText::_('OHANAH_SAVE_TO_CAL')?></a></h3>
		</span>
	<? endif ?>

	<? if ($showLinkToCategory) : ?>
		<span class="ohanah-event-category-link"><a href="<?=@route('option=com_ohanah&view=events&ohanah_category_id='.$event->ohanah_category_id.'&ohanah_venue_id=&filterEvents=notpast'.$itemid)?>"><?=@service('com://site/ohanah.model.categories')->id($event->ohanah_category_id)->getItem()->title?></a></span>
	<? endif ?>

	<? if ($showLinkToVenue) : ?>
		<? if ($event->ohanah_venue_id) : ?><span class="ohanah-event-venue-link"><? if ($showLinkToCategory) : ?><span class="event-dot-link">&nbsp;</span><? endif ?><span class="atvenue"></span>  <a href="<?=@route('option=com_ohanah&view=events&ohanah_venue_id='.$event->ohanah_venue_id.'&ohanah_category_id=&filterEvents=notpast'.$itemid)?>"><?=@service('com://site/ohanah.model.venues')->id($event->ohanah_venue_id)->getItem()->title?></a></span><? endif ?>
	<? endif ?>

	<? if ($showLinkToRecurringSet) : ?>
		<? if ($event->isRecurring()) : ?><span class="ohanah-event-recurrent-link"><? if ($showLinkToCategory || $showLinkToVenue) : ?>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<? endif ?></span>
			<? if ($event->recurringParent) : ?>
				<span class="ohanah-event-recurrent-link"><a href="<?=@route('option=com_ohanah&view=events&recurringSet='.$event->recurringParent.$itemid)?>"><?=JText::_('OHANAH_RECURRING_SET')?></a></span>
			<? else : ?>
				<span class="ohanah-event-recurrent-link"><a href="<?=@route('option=com_ohanah&view=events&recurringSet='.$event->id.$itemid)?>"><?=JText::_('OHANAH_RECURRING_SET')?></a></span>
			<? endif ?>
		<? endif ?>
	<? endif ?>
	
	<? if ($showEventAddress) : ?>
	<div class="event_detail_location">
		<div class="location_icon"></div>

		<h3 itemprop="location">
			<?= $event->calculateLocation() ?>
		</h3>
	</div>
	<? endif ?>


	<? if ($isSingle) : ?>
		<? if ($showBigDateBadge) : ?><div class="date-badge-spacer"><br /><br /></div><? endif ?>
		<? if ($params->get('showButtonTwitter') || $params->get('showButtonGoogle') || $params->get('showButtonFacebook')) : ?><div class="ohanah-social-buttons-wrapper"><? endif ?>
			<? if ($params->get('showButtonTwitter')) : ?>
				<div>
					<a href="https://twitter.com/share" class="twitter-share-button" data-count="none">Tweet</a>
					<script src="https://platform.twitter.com/widgets.js" />
				</div>
			<? endif ?>
			<? if ($params->get('showButtonGoogle')) : ?>
				<div>
					<g:plusone size="medium" annotation="none"></g:plusone>
					<script type="text/javascript">
					  (function() {
					    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
					    po.src = 'https://apis.google.com/js/plusone.js';
					    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
				</div>
			<? endif ?>
			<? if ($params->get('showButtonFacebook')) : ?>
				<?
				 	$config =& JFactory::getConfig();
					$language = $config->get('language');
	    			$languagesSupportedByFacebook = array('en-GB', 'pt-BR', 'sq-AL', 'ar-DZ', 'hy-HY', 'be-BY', 'bg-BG', 'ca-ES', 'zh-CN', 'hr-HR', 'cs-CZ', 'da-DK', 'nl-NL', 'eo-EO', 'et-EE', 'fi-FI', 'fr-FR', 'es-GL', 'de-DE', 'el-GR', 'iw-IL', 'hi-IN', 'hu-HU', 'is-IS', 'in-ID', 'ga-IE', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'lt-LT', 'mk-MK', 'ms-MY', 'mt-MT', 'nb-NO', 'nn-NO', 'fa-FA', 'pl-PL', 'pt-PT', 'ro-RO', 'ru-RU', 'sr-RS', 'sk-SK', 'sl-SI', 'es-ES', 'sv-SE', 'th-TH', 'tr-TR', 'uk-UA', 'vi-VN');

	    			if (!in_array($language, $languagesSupportedByFacebook)) {
	    				$language = 'en-GB';
	     			}
	     		?>

				<div style="float: left; margin-left: 10px;">
					<div id="fb-root"></div>
					<script>(function(d, s, id) {
					  var js, fjs = d.getElementsByTagName(s)[0];
					  if (d.getElementById(id)) {return;}
					  js = d.createElement(s); js.id = id;
					  js.src = "//connect.facebook.net/<?=str_replace('-', '_', $language)?>/all.js#xfbml=1";
					  fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));</script>

					<div class="fb-like" data-send="false" data-layout="button_count" data-width="40" data-show-faces="true"></div>
				</div>
			<? endif ?>

		<? if ($params->get('showButtonTwitter') || $params->get('showButtonGoogle') || $params->get('showButtonFacebook')) : ?></div> <!-- ohanah-social-buttons-wrapper --><? endif ?>
	<? endif ?>
	<? // prepare event picture and fallback to default ones if needed ?>
	<?
		if ($event->picture) {
			$picture = "media://com_ohanah/attachments/".$event->picture;
			$picture_thumb = "media://com_ohanah/attachments_thumbs/".$event->picture;
		} elseif (JFile::exists(JPATH_SITE."/media/com_ohanah/images/default_event_image-cat-$event->ohanah_category_id.png")) {
			$picture = "media://com_ohanah/images/default_event_image-cat-$event->ohanah_category_id.png";
			$picture_thumb = "media://com_ohanah/images/default_event_image_thumb-cat-$event->ohanah_category_id.png";
		}	elseif (JFile::exists(JPATH_BASE."/media/com_ohanah/images/default_event_image.png")) {
			$picture = "media://com_ohanah/images/default_event_image.png";
			$picture_thumb = "media://com_ohanah/images/default_event_image_thumb.png";
		} else {
			$picture = "";
			$picture_thumb = "";
		}
		// also prepare param
		if ($showEventPicture && $picture != "" && !$params->get("useFullWidthEventImage")) : ?>
			<a class="ohanah_modal" href="<?=$picture?>"  itemprop="image"><div class="event-photos" style="background: url('<?=$picture?>') no-repeat; background-size: 100% auto;"></div></a>
		<? endif ?>
		<? // if we choose to display big image ?>
		<? if ($showEventPicture && $picture != "" && $params->get("useFullWidthEventImage")) : ?>
			<a  href="<?=@route('option=com_ohanah&view=event&id='.$event->id.$itemid)?>"  itemprop="image"><div class="event-photos" style="border: none;height: auto;width: auto;float: none;margin: 10px 0 20px;"><img src="<?=$picture?>" alt="<?=$event->title?>"/></div></a>
		<? endif ?>
	<? if ($showEventDescriptionSnippet) : ?>
		<? $desc = $event->description; ?>
		<? $desc = preg_replace("/\{[^\)]+\}/","", $desc) ?>
		<? if (extension_loaded('mbstring')) : ?>
			<? $desc = mb_substr(strip_tags($desc, '<br>'), 0, 350)?>
			<? $desc = remove_trailing_tag($desc); ?>
			<? if (mb_strlen($desc) == 350) $desc .= '...'; ?>
		<? else : ?>
			<? $desc = substr(strip_tags($desc, '<br>'), 0, 350)?>
			<? $desc = remove_trailing_tag($desc); ?>
			<? if (strlen($desc) == 350) $desc .= '...'; ?>
		<? endif ?>
		<div itemprop="description" class="ohanah-event-short-description">
		<?=$desc?>
		</div>
	<? endif ?>

	<? if ($showEventFullDescription) : ?>
		<?
		$description = $event->description;

		// Create temporary article
   		$item =& JTable::getInstance('content');

   		$item->parameters = new JRegistry('');
   		$item->text = '<!--{emailcloak=off}-->'.$description;

		$joomlaVersion = JVersion::isCompatible('1.6.0') ? '1.6' : '1.5';
		if ($joomlaVersion == '1.5') {
			$results = JFactory::getApplication()->triggerEvent('onPrepareContent', array (&$item, &$params, 1));
		} else {
			$dispatcher	= JDispatcher::getInstance();
			JPluginHelper::importPlugin('content');
			$results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$params, 1));
			$results = $dispatcher->trigger('onContentBeforeDisplay', array ('com_content.article', &$item, &$params, 1));
		}
   		$description = $item->text;
		?>
		<div itemprop="description" class="ohanah-event-full-description">
			<?=$description?>
		</div>
	<? endif ?>

	<div class="event-spacer"></div>
	<div id="event-readmore-info">
		<? // in registration there is no need for read more or register button ?>
		<? if (!$isRegistration) : ?>
			<span class="ohanah-registration-link">
				<? if ($isSingle || (!$isModule && $showRegisterInList) || ($isModule && $showRegisterInModule)) : ?>
					<? if ($event->who_can_register == '0' || ($event->who_can_register == '1' && !JFactory::getUser()->guest)) : ?>
						<?
						if ($event->get('payment_gateway') != 'none' && $event->ticket_cost) $text = JText::_('OHANAH_BUY_TICKETS');
						else $text = JText::_('OHANAH_REGISTER');
						?>

						<? if ($event->registration_system == 'custom') : ?>
							<? if ($event->custom_registration_url) : ?>
								<? $date = new KDate(); ?>
								<? if ($event->isPast() || (($event->close_registration_day != '0000-00-00') && ($event->close_registration_day != '1970-01-01') && ($date->format('%Y-%m-%d') > $event->close_registration_day))) : ?>
								<? else : ?>
									<?
										$url = $event->custom_registration_url;
										$url = str_replace('{EVENT_ID}', $event->id, $url);
									?>
									<?= @helper('com://site/ohanah.template.helper.button.button', array('type' => 'link', 'text' => $text, 'link' =>$url,  'targetBlank' => $params->get('custom_reg_url_target_blank'))); ?>
								<? endif ?>
							<? endif ?>
						<? else : ?>
							<? if (!$event->limit_number_of_attendees or $event->countAttendees() < $event->attendees_limit) : ?>
								<? $date = new KDate(); ?>
								<? if ($event->isPast() || (($event->close_registration_day != '0000-00-00') && ($event->close_registration_day != '1970-01-01') && ($date->format('%Y-%m-%d') > $event->close_registration_day))) : ?>
								<? else : ?>
									<?= @helper('com://site/ohanah.template.helper.button.button', array('type' => 'link', 'text' => $text, 'link' => @route('option=com_ohanah&view=registration&ohanah_event_id='.$event->id.$itemid))); ?>
								<? endif ?>
							<? else : ?>
								&nbsp;&nbsp;|&nbsp;&nbsp;<?=JText::_('OHANAH_TICKETS_SOLD_OUT')?>
							<? endif; ?>
						<? endif ?>
					<? endif ?>
					<? if ($event->who_can_register == '1' && JFactory::getUser()->guest && ($params->get('onlyMembersText', '') != '')) : ?>
						<?=$params->get('onlyMembersText', '');?>
					<? endif; ?>
				<? else : ?>
					<? if ($showReadMoreLink) : ?>
						<?= @helper('com://site/ohanah.template.helper.button.button', array('type' => 'link', 'text' => JText::_('OHANAH_READ_MORE'), 'link' => @route('option=com_ohanah&view=event&id='.$event->id.$itemid))); ?>
					<? endif ?>
				<? endif ?>
			</span>
		<? endif ?>
	</div>
	<?
			if ($isSingle && ($params->get('onlyMembersModule', 0) == 1) && JFactory::getUser()->guest) {
				jimport( 'joomla.application.module.helper' );
				$module = JModuleHelper::getModule( 'login' );
				if ($module) {
					$attribs['style'] = 'xhtml';
					echo JModuleHelper::renderModule( $module, $attribs );
				}
			}
	?>
</div>

<?

/* Facebook integration
	If we are in single event view we will add Open Graph tags.
	If JFBConnect plugin is present then we will add it's {tags}, thus not duplicating og:tags

*/
	// global docs
	if ($isSingle) {

		// check for JFBConnect plugin
		$jfbc = false;
		if (JPluginHelper::isEnabled('system', 'jfbcsystem')) $jfbc = true;

		$doc =& JFactory::getDocument();

		// making description
		// shorten it to 305 chars since facebook will pull 300, linked in 225 and google plus 200 so it's enought

		$fbdesc = $event->description;
		$fbdesc = preg_replace("/\{[^\)]+\}/","", $fbdesc);
		if (extension_loaded('mbstring')) {
			$fbdesc = mb_substr(strip_tags($fbdesc, "<br>"), 0, 305);
		} else {
			$fbdesc = substr(strip_tags($fbdesc, "<br>"), 0, 305);
		}
		$newLines = Array("<br>", "<br />", "<br >");
		$fbdesc = str_replace($newLines, " ", $fbdesc);
		// we must clear " " since it will break the code
		$fbdesc = preg_replace('/"/',"", $fbdesc);
		$fbdesc = trim($fbdesc);
		if (!$jfbc) {
			// adding type as article (no event unfortunatelly)
			$doc->addCustomTag( '<meta property="og:type" content="article" />');

			// adding current url as url
			if (isset($_SERVER['HTTPS'])) {
			  $protocol = "https://";
			} else {
			  $protocol = "http://";
			}
			$fburl = KRequest::url();
			$doc->addCustomTag( '<meta property="og:url" content="'.$fburl.'" />');

			// adding locale
			$lang = JFactory::getLanguage();
      $locale = $lang->getTag();
      $locale = str_replace("-", "_", $locale);
			$doc->addCustomTag( '<meta property="og:locale" content="'.$locale.'" />');

			// title
			$doc->addCustomTag( '<meta property="og:title" content="'.$event->title.'" />' );

			// description
			$doc->addCustomTag( '<meta property="og:description" content="'.$fbdesc.'" />' );

			// image (if any); path must be absolute!
			if ($event->picture && $showEventPicture) {
				$doc->addCustomTag( '<meta property="og:image" content="'.JURI::base().'media/com_ohanah/attachments/'.$event->picture.'" />' );
			}

			// appid
			$doc->addCustomTag( '<meta property="fb:app_id" content="'.$params->get('fbAppId').'" />' );

		} else { // we have JFBConnect plugin so we will use it's tags
			// no need for type, url or locale, jfbc is doing it automatically
			echo "{JFBCGraph title=".$event->title."}";
			echo "{JFBCGraph image=".JURI::base().'media/com_ohanah/attachments/'.$event->picture."}";
			// since JFBConnect doesn't like multiline tags, we must strip them out
			$fbdesc = trim($fbdesc);
			$fbdesc = str_replace("\n", " ", $fbdesc);
			$fbdesc = str_replace("\r", "", $fbdesc);
			echo "{JFBCGraph description=".$fbdesc."}";
		}
	}
/* end of facebook integration */
?>
