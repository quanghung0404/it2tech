<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POST_TYPES_TAB_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
	                        <div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_PRIORITY_TITLE'); ?>
	                        </div>
	                        <div class="col-md-7">
								<input type="text" class="form-control" id="title" name="title" size="55" maxlength="255" placeholder="<?php echo JText::_('COM_EASYDISCUSS_PRIORITY_TITLE_PLACEHOLDER');?>" value="<?php echo $priority->title;?>" />
							</div>
						</div>

						<div class="form-group">
	                        <div class="col-md-5 control-label">
	                            <?php echo $this->html('form.label', 'COM_EASYDISCUSS_PRIORITY_COLOR'); ?>
	                        </div>
	                        <div class="col-md-6">
								<div class="input-group colorpicker-component colorpicker-element" data-ed-priority-colorpicker>
									<input type="text" name="color" maxlength="255" value="<?php echo $priority->color;?>" class="form-control" />
									<span class="input-group-addon"><i></i></span>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'priorities', '', ''); ?>
	<input type="hidden" name="id" value="<?php echo $priority->id ?>" />

</form>
