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

defined('_JEXEC') or die('Restricted access');
?>

<?php $gridPath=1; ?> 

<?php if ($config->get('main_ratings')) { ?>
<script type="text/javascript">
EasyBlog.require()
.script('ratings')
.done(function($) {

    $('#fd.mod_easybloglatestblogs [data-rating-form]').implement(EasyBlog.Controller.Ratings);
});
</script>
<?php } ?>

<div id="fd" class="eb eb-mod mod_easybloglatestblogs<?php echo $params->get('moduleclass_sfx'); ?>  <?php echo $params->get('module_responsive') ? ' eb-responsive' : '';?>">

	<?php if ($posts) { ?>
	<div class="eb-mod<?php if ($layout == 'horizontal') { echo " mod-items-grid clearfix"; } ?>">
		<?php foreach ($posts as $post) { ?>
			<?php require(JModuleHelper::getLayoutPath('mod_easybloglatestblogs', 'default_item')); ?>
		<?php } ?>
	</div>
	<?php } else { ?>
		<?php if (in_array($filterType, array(0,2)) || in_array($filterType, array('recent', 'category'))) { ?>
			<?php echo JText::_('MOD_LATESTBLOGS_NO_POST'); ?>
		<?php } ?>

		<?php if ($filterType == 1 || $filterType == 'author') { ?>
			<?php echo JText::_('MOD_LATESTBLOGS_NO_BLOGGER'); ?>
		<?php } ?>
	<?php } ?>

	<?php if ($params->get('allentrieslink')) { ?>
	<hr />
	<div>
		<a href="<?php echo EBR::_('index.php?option=com_easyblog&view=latest');?>">
			<?php echo JText::_('MOD_LATESTBLOGS_VIEW_ALL_ENTRIES'); ?>
		</a>
	</div>
	<?php } ?>
</div>
