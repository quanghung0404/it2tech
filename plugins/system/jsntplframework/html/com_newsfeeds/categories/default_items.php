<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app 		= JFactory::getApplication();
$template 	= $app->getTemplate();
$jsnUtils   = JSNTplUtils::getInstance();
if ($jsnUtils->isJoomla3()):
JHtml::_('bootstrap.tooltip');	
endif;
$class = ' class="first"';
if (count($this->items[$this->parent->id]) > 0 && $this->maxLevelcat != 0) :
?>
<ul class="jsn-infolist">
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
		<a class="category" href="<?php echo JRoute::_(NewsfeedsHelperRoute::getCategoryRoute($item->id));?>" class="category">
			<?php echo $this->escape($item->title); ?></a>
					<?php if ($this->params->get('show_cat_items_cat') == 1) :?>
						<span class="badge badge-info tip hasTooltip" title="<?php echo JHtml::tooltipText('COM_NEWSFEEDS_NUM_ITEMS'); ?>">
							<?php echo $item->numitems; ?>
						</span>
					<?php endif; ?>
					<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) : ?>
						<a id="category-btn-<?php echo $item->id;?>" href="#category-<?php echo $item->id;?>" 
							data-toggle="collapse" data-toggle="button" class="btn btn-mini pull-right"><span class="icon-plus"></span></a>
					<?php endif;?>
		<?php if ($this->params->get('show_subcat_desc_cat') == 1) :?>
		<?php if ($item->description) : ?>
			<div class="category-desc">
				<<?php echo JHtml::_('content.prepare', $item->description, '', 'com_newsfeeds.categories'); ?>
			</div>
		<?php endif; ?>
        <?php endif; ?>
				<?php if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) :?>
					<div class="collapse fade" id="category-<?php echo $item->id;?>">
					<?php
					$this->items[$item->id] = $item->getChildren();
					$this->parent = $item;
					$this->maxLevelcat--;
					echo $this->loadTemplate('items');
					$this->parent = $item->getParent();
					$this->maxLevelcat++;
					?>
					</div>
				<?php endif; ?>


	</li>
	<?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>