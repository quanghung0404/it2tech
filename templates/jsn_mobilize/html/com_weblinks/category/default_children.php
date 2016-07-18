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
if (count($this->children[$this->category->id]) > 0 && $this->maxLevel != 0) :
?>

<ul class="jsn-infolist">
	<?php foreach($this->children[$this->category->id] as $id => $child) : ?>
	<?php
	if($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) :
	if(!isset($this->children[$this->category->id][$id + 1]))
	{
		$class = ' class="last"';
	}
	?>
	<li<?php echo $class; ?>>
		<?php $class = ''; ?>
		<a class="category" href="<?php echo JRoute::_(WeblinksHelperRoute::getCategoryRoute($child->id));?>"> <?php echo $this->escape($child->title); ?></a>
		<?php if ($this->params->get('show_subcat_desc') == 1) :?>
		<?php if ($child->description) : ?>
		<div class="category-desc"> <?php echo JHtml::_('content.prepare', $child->description); ?> </div>
		<?php endif; ?>
		<?php endif; ?>
		<?php if ($this->params->get('show_cat_num_links') == 1) :?>
		<dl class="weblink-count">
			<dt> <?php echo JText::_('COM_WEBLINKS_NUM'); ?></dt>
			<dd><?php echo $child->numitems; ?></dd>
		</dl>
		<?php endif; ?>
		<?php if(count($child->getChildren()) > 0 ) :
				$this->children[$child->id] = $child->getChildren();
				$this->category = $child;
				$this->maxLevel--;
				echo $this->loadTemplate('children');
				$this->category = $child->getParent();
				$this->maxLevel++;
			endif; ?>
	</li>
	<?php endif; ?>
	<?php endforeach; ?>
</ul>
<?php endif;