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

$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();


$class = ' class="first"';
if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
<ul <?php if (JSNMobilizeTemplateHelper::isJoomla3()){ echo 'class="list-striped list-condensed"';} ?>>
<?php foreach($this->items[$this->parent->id] as $id => $item) : ?>
	<?php
	if($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) :
	if(!isset($this->items[$this->parent->id][$id + 1]))
	{
		$class = ' class="last"';
	}
	?>
	<li<?php echo $class; ?>>
	<?php $class = ''; ?>
		<?php if (JSNMobilizeTemplateHelper::isJoomla3()): ?>
			<h4 class="item-title">
				<a href="<?php echo JRoute::_(ContactHelperRoute::getCategoryRoute($item->id)); ?>">
				<?php echo $this->escape($item->title); ?>
				</a>
	
				<?php if ($this->params->get('show_cat_items_cat') == 1) :?>
					<span class="badge badge-info pull-right" title="<?php echo JText::_('COM_CONTACT_COUNT'); ?>"><?php echo $item->numitems; ?></span>
				<?php endif; ?>
			</h4>
	
			<?php if ($this->params->get('show_subcat_desc_cat') == 1) : ?>
				<?php if ($item->description) : ?>
					<small class="category-desc">
						<?php echo JHtml::_('content.prepare', $item->description, '', 'com_contact.categories'); ?>
					</small>
				<?php endif; ?>
			<?php endif; ?>
		<?php else : ?>
			<a class="category" href="<?php echo JRoute::_(ContactHelperRoute::getCategoryRoute($item->id));?>">
				<?php echo $this->escape($item->title); ?></a>
	
			<?php if ($this->params->get('show_subcat_desc_cat') == 1) :?>
			<?php if ($item->description) : ?>
				<div class="category-desc">
					<?php echo JHtml::_('content.prepare', $item->description); ?>
				</div>
			<?php endif; ?>
	        <?php endif; ?>
	        
			<?php if ($this->params->get('show_cat_items_cat') == 1) :?>
				<dl class="contact-count"><dt>
					<?php echo JText::_('COM_CONTACT_COUNT'); ?></dt>
					<dd><?php echo $item->numitems; ?></dd>
				</dl>
			<?php endif; ?>
		<?php endif; ?>
		<?php if(count($item->getChildren()) > 0) :
			$this->items[$item->id] = $item->getChildren();
			$this->parent = $item;
			$this->maxLevelcat--;
			echo $this->loadTemplate('items');
			$this->parent = $item->getParent();
			$this->maxLevelcat++;
		endif; ?>

	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>