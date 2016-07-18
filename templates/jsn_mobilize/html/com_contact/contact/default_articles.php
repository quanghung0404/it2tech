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
defined('_JEXEC') or die;
$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();


?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
require_once JPATH_SITE . '/components/com_content/helpers/route.php';
<?php endif; ?>
<?php if ($this->params->get('show_articles')) : ?>
<div class="contact-articles">
	<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
		<ul class="nav nav-tabs nav-stacked">
		<?php foreach ($this->item->articles as $article) :	?>
			<li>
				<?php echo JHtml::_('link', JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug)), htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8')); ?>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php else : ?>
		<ol>
		<?php foreach ($this->item->articles as $article) :	?>
			<li>
			<?php $link = JRoute::_('index.php?option=com_content&view=article&id='.$article->id); ?>
			<?php echo '<a href="'.$link.'">' ?>
				<?php echo $article->text = htmlspecialchars($article->title, ENT_COMPAT, 'UTF-8'); ?>
				</a>
			</li>
		<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</div>
<?php endif; ?>