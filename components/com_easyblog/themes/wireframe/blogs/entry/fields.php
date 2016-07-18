<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($fields) { ?>
<div class="eb-entry-fields">
	<?php foreach ($fields as $field) { ?>
		<?php if ($field->group->hasValues($post)) { ?>
		<h4 class="eb-section-heading reset-heading"><?php echo $field->group->getTitle();?></h4>
		<ul class="eb-fields-list eb-responsive reset-list">
			<?php foreach ($field->fields as $customField) { ?>

				<?php $fieldValue = $customField->getDisplay($post); ?>
				<?php if ($fieldValue) { ?>
				<li>
					<label><?php echo $customField->title;?></label>
					<div><?php echo $fieldValue;?></div>
				</li>
				<?php } ?>
			<?php } ?>
		</ul>
		<?php } ?>
	<?php } ?>
</div>
<?php } ?>
