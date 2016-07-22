<? defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?=@helper('behavior.mootools'); ?>
<script src="media://lib_koowa/js/koowa.js" />

<div class="ohanah event-<?=$event->id?>" >
	<?= @helper('module.injector', array('title' => '', 'placeholder' => 'ohanah-single-event-1', 'position' => $params->get('singleEventModulePosition1'))) ?>
	<?= @helper('module.injector', array('title' => '', 'placeholder' => 'ohanah-single-event-2', 'position' => $params->get('singleEventModulePosition2'))) ?>
	<?= @helper('module.injector', array('title' => '', 'placeholder' => 'ohanah-single-event-3', 'position' => $params->get('singleEventModulePosition3'))) ?>

	<? if ($joomlaVersion != '3.0') : ?>
		<? if (($params->get('loadJQuery') != '0') && (!JFactory::getApplication()->get('jquery'))) : ?>
			<? JFactory::getDocument()->addScript(JURI::root(1).'/media/com_ohanah/js/jquery.min.js');?>
			<? JFactory::getDocument()->addScript(JURI::root(1).'/media/com_ohanah/js/jquery-migrate-1.2.1.min.js');?>
			<? JFactory::getApplication()->set('jquery', true); ?>
		<? endif; ?>
	<? else : ?>
		<? JHtml::_('jquery.framework'); ?>
	<? endif ?>

	<script src="media://com_ohanah/js/jquery-ui-1.9.2/js/jquery-ui-1.9.2.custom.min.js" />
	<style src="media://com_ohanah/css/jquery-ui.css" />
	<style src="media://com_ohanah/css/screen.css" />

	<?= @template('default_header', array('event' => $event)); ?>
	<?  /* if some plugin is listening onContentAfterDisplay this is the place where it should put it's code
		     I'm looking at you, Komento guys! :)  */
			// Create temporary article
   		$item =& JTable::getInstance('content');
   		$item->text = '';
   		$item->parameters = new JRegistry('');
			$joomlaVersion = JVersion::isCompatible('1.6.0') ? '1.6' : '1.5';
			$item->event = new stdClass;
			// we cannot use params because some extension "unpack" it with toArray() and our params doesn't have that method.
			$params_for_event = JComponentHelper::getParams('com_ohanah');
			if ($joomlaVersion == '1.5') {
				$results = JFactory::getApplication()->triggerEvent('onAfterDisplayContent', array (&$item, &$params_for_event, 1));
			} else {
				$dispatcher	= JDispatcher::getInstance();
				JPluginHelper::importPlugin('content');
				$results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$params_for_event, 1));
			}
			$item->event->onContentAfterDisplay = trim(implode("\n", $results));
			echo $item->text;
		?>
	<? if ($params->get('enableComments')) : ?>
		<? if ($params->get('useFacebookComments')) : ?>
			<?
			 	$config =& JFactory::getConfig();
				$language = $config->get('language');
    			$languagesSupportedByFacebook = array('en-GB', 'pt-BR', 'sq-AL', 'ar-DZ', 'hy-HY', 'be-BY', 'bg-BG', 'ca-ES', 'zh-CN', 'hr-HR', 'cs-CZ', 'da-DK', 'nl-NL', 'eo-EO', 'et-EE', 'fi-FI', 'fr-FR', 'es-GL', 'de-DE', 'el-GR', 'iw-IL', 'hi-IN', 'hu-HU', 'is-IS', 'in-ID', 'ga-IE', 'it-IT', 'ja-JP', 'ko-KR', 'lv-LV', 'lt-LT', 'mk-MK', 'ms-MY', 'mt-MT', 'nb-NO', 'nn-NO', 'fa-FA', 'pl-PL', 'pt-PT', 'ro-RO', 'ru-RU', 'sr-RS', 'sk-SK', 'sl-SI', 'es-ES', 'sv-SE', 'th-TH', 'tr-TR', 'uk-UA', 'vi-VN');

    			if (!in_array($language, $languagesSupportedByFacebook)) {
    				$language = 'en-GB';
     			}
     		?>
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/<?=str_replace('-', '_', $language)?>/all.js#xfbml=1";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>

			<? if (JComponentHelper::getParams('com_ohanah')->get('itemid')) $itemid = '&Itemid='.JComponentHelper::getParams('com_ohanah')->get('itemid'); else $itemid = '&Itemid='.KRequest::get('get.Itemid', 'int'); ?>
			<? $url = 'http://'.$_SERVER['HTTP_HOST'].JRoute::_('index.php?option=com_ohanah&view=event&id='.$this->getView()->getModel()->getItem()->id.$itemid); ?>
			<? ($params->get('darkFB')) ? $darkComments = 'data-colorscheme="dark"' : $darkComments = '' ?>
			<div class="fb-comments" data-href="<?=$url?>" data-num-posts="2" data-width="470" <?=$darkComments?>></div>
		<? else : ?>
			<?= html_entity_decode($params->get('commentsCode')); ?>
		<? endif ?>
	<? endif ?>
</div>
