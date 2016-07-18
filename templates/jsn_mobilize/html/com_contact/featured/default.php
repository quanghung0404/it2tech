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

JHtml::addIncludePath(JPATH_COMPONENT. DIRECTORY_SEPARATOR .'helpers');

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>
<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
<div class="blog-featured<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading') != 0 ): ?>
	<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	</h1>
<?php endif; ?>

<?php echo $this->loadTemplate('items'); ?>
<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->pagesTotal > 1)) : ?>
	<div class="center">

		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter">
				<?php echo $this->pagination->getPagesCounter(); ?>
			</p>
		<?php  endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>

</div>
<?php else : ?>
<div class="com-contact <?php echo $this->pageclass_sfx; ?>">
	<div class="contact-category<?php echo $this->pageclass_sfx;?>">
		<?php if ($this->params->def('show_page_heading', 1)) : ?>
		<h2 class="componentheading"> <?php echo $this->escape($this->params->get('page_heading')); ?> </h2>
		<?php endif; ?>
		<?php echo $this->loadTemplate('items'); ?>
		<?php if ($this->params->def('show_pagination', 2) == 1  || ($this->params->get('show_pagination') == 2 && $this->pagination->get('pages.total') > 1)) : ?>
		<div class="jsn-pagination-container"> <?php echo $this->pagination->getPagesLinks(); ?>
			<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="jsn-pageinfo"> <?php echo $this->pagination->getPagesCounter(); ?> </p>
			<?php  endif; ?>
		</div>
		<?php endif; ?>
	</div>
</div>
<?php endif; ?>