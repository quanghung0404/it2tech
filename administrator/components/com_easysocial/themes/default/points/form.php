<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="adminForm" id="adminForm" class="pointsForm" method="post" enctype="multipart/form-data">
<div class="row">

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_GENERAL' );?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_COMMAND' );?>
					</label>
					<div class="col-md-7">
						<input type="text" class="form-control input-sm" value="<?php echo $point->command;?>" name="command" disabled="disabled" />
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_EXTENSION' );?>
					</label>
					<div class="col-md-7">
						<input type="text" class="form-control input-sm" value="<?php echo $point->extension;?>" name="extension" disabled="disabled" />
					</div>
				</div>

				<div class="form-group">
					<label for="points-title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_TITLE' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_TITLE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_TITLE_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="form-control input-sm" value="<?php echo $point->title;?>" name="title" id="points-title" />
					</div>
				</div>

				<div class="form-group">
					<label for="points-points" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_POINTS' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_POINTS' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_POINTS_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="form-control input-mini text-center" value="<?php echo $point->points;?>" name="points" id="points-points" />
					</div>
				</div>

				<div class="form-group">
					<label for="points-alias" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_ALIAS' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_ALIAS' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_ALIAS_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="form-control input-sm" value="<?php echo $point->alias;?>" name="alias" id="points-alias" />
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_DESCRIPTION' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_DESCRIPTION' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_DESCRIPTION_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<textarea name="description" class="form-control input-sm"><?php echo $point->description;?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_CREATED' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_CREATED' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_CREATED_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'form.calendar' , 'created' , $point->created ); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_STATE' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_STATE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_POINTS_FORM_STATE_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'grid.boolean' , 'state' , $point->state ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<?php if ($params) { ?>
			<div class="panel">
				<div class="panel-head">
					<b><?php echo JText::_('COM_EASYSOCIAL_POINTS_FORM_PARAMS');?></b>
				</div>

				<div class="panel-body">
				<?php foreach($params as $key => $param){ ?>
					<div class="form-group">
						<label for="points-<?php echo $key;?>" class="col-md-5">
							<?php echo $param['title']; ?>
							<i data-placement="bottom"
								data-title="<?php echo $this->html('string.escape', $param['title']);?>"
								data-content="<?php echo $this->html('string.escape', $param['desc']);?>"
								data-es-provide="popover"
								class="fa fa-question-circle pull-right"
								data-original-title=""></i>
						</label>
						<div class="col-md-7">
							<input type="text" name="params[<?php echo $key;?>]" class="input-sm form-control input-mini text-center"
								placeholder="<?php echo $param['default'];?>"
								value="<?php echo isset($param['value']) ? $param['value'] : $param['default'];?>"
							/>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>

</div>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="points" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $point->id; ?>" />
<?php echo JHTML::_( 'form.token' );?>

</form>
