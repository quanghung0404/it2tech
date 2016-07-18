<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-page-header eb-responsive row-table">
	<div class="col-cell">
		<h2 class="reset-heading"><?php echo JText::_('COM_EASYBLOG_ARCHIVE_HEADING');?></h2>
	</div>
</div>

<hr class="clearfix">

<div class="eb-archives">
	<?php if ($posts) { ?>
		<?php foreach ($posts as $post) { ?>
		<div class="eb-archive">
			<?php echo $post->getIcon('eb-archive-type'); ?>

			<time class="eb-archive-date">
				<?php echo $this->html('string.date', $post->created, JText::_('DATE_FORMAT_LC1')); ?>
			</time>

			<h3 class="eb-archive-title reset-heading">
				<a href="<?php echo $post->getPermalink();?>"><?php echo $post->title;?></a>
			</h3>
		</div>
		<?php } ?>
	<?php } else { ?>
		<div class="eb-empty">
			<i class="fa fa-archive"></i>
			<?php echo JText::_('COM_EASYBLOG_NO_ARCHIVES_YET'); ?>
		</div>
	<?php } ?>
</div>

<?php if($pagination) {?>
	<!-- @module: easyblog-before-pagination -->
	<?php echo EB::renderModule('easyblog-before-pagination'); ?>

	<!-- Pagination items -->
	<?php echo $pagination;?>

	<!-- @module: easyblog-after-pagination -->
	<?php echo EB::renderModule('easyblog-after-pagination'); ?>
<?php } ?>
