<?php
/**
 * @author Joomla! Extensions Store
 * @package JMAP::modules::mod_jmap
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Manage partial language translations
$jLang = JFactory::getLanguage();
$jLang->load('com_jmap', JPATH_BASE . '/components/com_jmap', 'en-GB', true, true);
if($jLang->getTag() != 'en-GB') {
	$jLang->load('com_jmap', JPATH_BASE, null, true, false);
	$jLang->load('com_jmap', JPATH_BASE . '/components/com_jmap', null, true, false);
}
$doc = JFactory::getDocument();
$currentVersion = strval(simplexml_load_file(JPATH_BASE . '/components/com_jmap/jmap.xml')->version);
$doc->addScriptDeclaration ( 'function jmapCompareVersions(r,e){for(var n=r.split("."),t=e.split("."),l=0;l<n.length;++l)n[l]=Number(n[l]);for(var l=0;l<t.length;++l)t[l]=Number(t[l]);return 2==n.length&&(n[2]=0),2==t.length&&(t[2]=0),n[0]>t[0]?!0:n[1]>t[1]?!0:n[2]>t[2]?!0:!1};jQuery.get("index.php?option=com_jmap&task=cpanel.getUpdates&format=raw",function(a){a&&"object"==typeof a&&(jQuery("span[data-bind=jmap_version]").html(a.latest),jmapCompareVersions(a.latest,"'.$currentVersion.'")?jQuery("i.icon-cancel, span[data-status=outdated]").show():jQuery("i.icon-checkmark, span[data-status=updated]").show())},"json");');

if (version_compare ( JVERSION, '3.0', '>=' )) : 
JHtml::_('jquery.framework');
?>
	<div class="sidebar-nav quick-icons">
		<h2 class="nav-header">JSitemap</h2>
		<ul class="nav nav-list">
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_jmap'); ?>">
				<img style="width:24px; height:24px;" alt="" src="<?php echo JUri::base() . 'components/com_jmap/images/jmap-32x32.png'?>" />
				<span><?php echo JText::_('COM_JMAP_CPANEL');?></span>
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_jmap&task=config.display'); ?>">
				<img style="width:24px; height:24px;" alt="" src="<?php echo JUri::base() . 'components/com_jmap/images/icon-32-config.png'?>" />
				<span><?php echo JText::_('COM_JMAP_CONFIG');?></span>
			</a>
			<a href="<?php echo JRoute::_('index.php?option=com_jmap'); ?>">
				<?php echo JText::_('COM_JMAP_MODULEPANEL_STATE');?>
			</a>
		</li>
		</ul>
	</div>
<?php else : 
$doc->addScript(JURI::root() . '/components/com_jmap/js/jquery.js');
?>
	<div id="cpanel">
		<div class="icon-wrapper" style="float:left;">
			<div class="icon">
				<a href="<?php echo JRoute::_('index.php?option=com_jmap'); ?>">
					<img style="width:48px; height:48px;" alt="" src="" />
					<span>test</span>
				</a>
			</div>
		</div>
	</div>
<?php endif; ?>
