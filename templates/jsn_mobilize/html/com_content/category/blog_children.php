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
$class = ' class="first"';

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();

?>
<?php if (count($this->children[$this->category->id]) > 0 && $this->maxLevel != 0) : ?>
	<?php if (JSNMobilizeTemplateHelper::isJoomla3()):
	JHtml::_('bootstrap.tooltip');
	$lang	= JFactory::getLanguage(); ?>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
		if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>
		<div<?php echo $class; ?>>
			<?php $class = ''; ?>
			<?php if ($lang->isRTL()) : ?>
			<h3 class="page-header item-title">
				<?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
					<span class="badge badge-info tip hasTooltip" title="<?php echo JText::_('COM_CONTENT_NUM_ITEMS'); ?>">
						<?php echo $child->getNumItems(true); ?>
					</span>
				<?php endif; ?>
				<a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id)); ?>">
				<?php echo $this->escape($child->title); ?></a>

				<?php if (count($child->getChildren()) > 0) : ?>
					<a href="#category-<?php echo $child->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right"><span class="icon-plus"></span></a>
				<?php endif;?>
			<?php else : ?>
			<h3 class="page-header item-title"><a href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id));?>">
				<?php echo $this->escape($child->title); ?></a>
				<?php if ( $this->params->get('show_cat_num_articles', 1)) : ?>
				<span class="badge badge-info tip hasTooltip" title="<?php echo JText::_('COM_CONTENT_NUM_ITEMS'); ?>">
					<?php echo $child->getNumItems(true); ?>
				</span>
				<?php endif; ?>

				<?php if (count($child->getChildren()) > 0) : ?>
				<a href="#category-<?php echo $child->id;?>" data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right"><span class="icon-plus"></span></a>
				<?php endif;?>
			<?php endif;?>
			</h3>

			<?php if ($this->params->get('show_subcat_desc') == 1) : ?>
			<?php if ($child->description) : ?>
				<div class="category-desc">
					<?php echo JHtml::_('content.prepare', $child->description, '', 'com_content.category'); ?>
				</div>
			<?php endif; ?>
			<?php endif; ?>

			<?php if (count($child->getChildren()) > 0) : ?>
			<div class="collapse fade" id="category-<?php echo $child->id; ?>">
				<?php
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;
				if ($this->maxLevel != 0) :
					echo $this->loadTemplate('children');
				endif;
				$this->category = $child->getParent();
				$this->maxLevel++;
				?>
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php else : ?>
	<ul>
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
		<?php
		if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
			if (!isset($this->children[$this->category->id][$id + 1])) :
				$class = ' class="last"';
			endif;
		?>
		<li<?php echo $class; ?>>
			<?php $class = ''; ?>
			<a class="category" href="<?php echo JRoute::_(ContentHelperRoute::getCategoryRoute($child->id));?>">
				<?php echo $this->escape($child->title); ?></a>

			<?php if ($this->params->get('show_subcat_desc') == 1) :?>
			<?php if ($child->description) : ?>
				<div class="category-desc">
					<?php echo JHtml::_('content.prepare', $child->description); ?>
				</div>
			<?php endif; ?>
            <?php endif; ?>

			<?php if ( $this->params->get('show_cat_num_articles',1)) : ?>
			<dl>
				<dt>
					<?php echo JText::_('COM_CONTENT_NUM_ITEMS') ; ?>
				</dt>
				<dd>
					<?php echo $child->getNumItems(true); ?>
				</dd>
			</dl>
			<?php endif ; ?>

			<?php if (count($child->getChildren()) > 0):
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;
				if ($this->maxLevel != 0) :
					echo $this->loadTemplate('children');
				endif;
				$this->category = $child->getParent();
				$this->maxLevel++;
			endif; ?>
		</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
<?php endif; ?>