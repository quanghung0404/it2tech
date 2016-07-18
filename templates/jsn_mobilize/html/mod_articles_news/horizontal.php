<?php
/**
 * @version     $Id$
 * @package     JSN_Mobilize
 * @subpackage  SystemPlugin
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */
// no direct access
defined('_JEXEC') or die;

// Load template framework


$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (!JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="jsn-mod-newsflash jsn-horizontal-container">
<?php $column_width = 99.9/count($list); ?>
<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :
	$item = $list[$i]; ?>
	<div class="jsn-article-container" style="float: left; width: <?php echo $column_width;?>%;">
	<?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item');

	if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>
	<?php endif; ?>
	</div>
<?php endfor; ?>
</div>
<?php else : ?>
<ul class="newsflash-horiz<?php echo $params->get('moduleclass_sfx'); ?>">
<?php for ($i = 0, $n = count($list); $i < $n; $i ++) :
	$item = $list[$i]; ?>
	<li>
	<?php require JModuleHelper::getLayoutPath('mod_articles_news', '_item');

	if ($n > 1 && (($i < $n - 1) || $params->get('showLastSeparator'))) : ?>

	<span class="article-separator">&#160;</span>

	<?php endif; ?>
	</li>
<?php endfor; ?>
</ul>
<?php endif; ?>