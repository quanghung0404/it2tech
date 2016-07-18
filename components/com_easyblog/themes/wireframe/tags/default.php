<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($this->params->get('tag_search', true) || $this->params->get('tag_sorting', true)) { ?>
<form name="tags" method="post" action="<?php echo JRoute::_('index.php'); ?>" class="eb-tags-filter row-table form-horizontal eb-responsive">
	<div class="col-cell">
		<?php if ($this->params->get('tag_search', true)) { ?>
		<div class="eb-tags-finder input-group">
			<input type="text" class="form-control" name="filter-tags" placeholder="<?php echo JText::_('COM_EASYBLOG_SEARCH_TAGS', true);?>" />
			<i class="fa fa-tags"></i>
			<div class="input-group-btn">
				<button type="button btn btn-primary" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_SEARCH_BUTTON', true);?></button>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="col-cell">
		<?php if ($this->params->get('tag_sorting', true)) { ?>
		<div class="eb-tags-sorter btn-group pull-right">
			<button type="button" class="btn btn-default dropdown-toggle_" data-bp-toggle="dropdown">
			<?php echo JText::_('COM_EASYBLOG_TAGS_SORT_BY');?> <span class="caret"></span>
			</button>

			<ul class="dropdown-menu" role="menu">
				<li>
					<a href="<?php echo EB::_($titleURL);?>">
					<?php if ($ordering == 'title') { ?>
					<i class="fa fa-check"></i>
					<?php } ?>
						<?php echo JText::_('COM_EASYBLOG_TAGS_ORDER_BY_TITLE');?>
					</a>
				</li>
				<li>
					<a href="<?php echo EB::_($postURL);?>">
						<?php if ($ordering == 'postcount') { ?>
						<i class="fa fa-check"></i>
						<?php } ?>
						<?php echo JText::_('COM_EASYBLOG_TAGS_ORDER_BY_POST_COUNT');?>
					</a>
				</li>
			</ul>
		</div>
		<?php } ?>
	</div>
	<?php echo $this->html('form.action', 'tags.query'); ?>
</form>
<?php } ?>

<?php if($tags) { ?>
<div class="eb-tags-list clearfix">
	<?php foreach ($tags as $tag) { ?>
	<div class="eb-tags-grid">
		<div class="eb-tags-item">
			<?php if ($this->config->get('main_rss') && $this->params->get('tag_rss', true)){ ?>
			<a href="<?php echo EB::feeds()->getFeedURL('index.php?option=com_easyblog&view=tags&layout=tag&id=' . $tag->getAlias(), false, 'tag');?>">
				<i class="fa fa-rss-square"></i>
			</a>
			<?php } ?>

			<a href="<?php echo $tag->getPermalink();?>" title="<?php echo $this->html('string.escape', $tag->title);?>">
				<b><?php echo JText::_($tag->title);?></b>

				<?php if ($this->params->get('tag_used_counter', true)) { ?>
				<i><?php echo $tag->post_count; ?></i>
				<?php } ?>
			</a>
		</div>
	</div>
	<?php } ?>
</div>
<?php } else { ?>
	<div class="eb-empty">
		<i class="fa fa-paper-plane-o"></i>
		<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_TAGS_AVAILABLE');?>
	</div>
<?php } ?>

