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
				<b><?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_GENERAL' );?></b>
			</div>

			<div class="panel-body">
				<div class="form-group">
					<label for="title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_TITLE' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_TITLE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_TITLE_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="input-sm form-control" value="<?php echo $badge->title;?>" name="title" id="title"
						placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ACCESS_RULE_RULE_TITLE_PLACEHOLDER' , true );?>" />
					</div>
				</div>

				<div class="form-group">
					<label for="alias" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_ALIAS' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_ALIAS' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_ALIAS_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="input-sm form-control" value="<?php echo $badge->alias;?>" name="alias" id="alias"
						placeholder="<?php echo JText::_( 'COM_EASYSOCIAL_ACCESS_RULE_RULE_TITLE_PLACEHOLDER' , true );?>" />
					</div>
				</div>

				<div class="form-group">
					<label for="frequency" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_FREQUENCY' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_FREQUENCY' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_FREQUENCY_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<input type="text" class="input-mini center input-sm" value="<?php echo $badge->frequency;?>" id="frequency" name="frequency" /> <?php echo JText::_( 'COM_EASYSOCIAL_TIMES' ); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="description" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_DESCRIPTION' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_DESCRIPTION' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_DESCRIPTION_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<textarea name="description" id="description" class="input-sm form-control"><?php echo $badge->description;?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="description" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_HOW_TO' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_HOW_TO' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_HOW_TO_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<textarea name="howto" id="howto" class="input-sm form-control"><?php echo $badge->howto;?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_CREATED' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_CREATED' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_CREATED_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'form.calendar' , 'created' , $badge->created ); ?>
					</div>
				</div>

				<div class="form-group">
					<label for="page_title" class="col-md-5">
						<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_STATE' );?>
						<i data-placement="bottom"
							data-title="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_STATE' , true );?>"
							data-content="<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_STATE_DESC' , true );?>"
							data-es-provide="popover"
							class="fa fa-question-circle pull-right"
							data-original-title=""></i>
					</label>
					<div class="col-md-7">
						<?php echo $this->html( 'grid.boolean' , 'state' , $badge->state ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<div class="panel-head">
				<b><?php echo JText::_( 'About Badge' );?></b>
			</div>

			<div class="panel-body">
				<table class="table table-striped table-noborder">
					<tbody>
						<tr>
							<td width="20%">
								<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_COMMAND' );?>:
							</td>
							<td>
								<strong><?php echo $badge->command; ?></strong>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_EXTENSION' ); ?>:
							</td>
							<td>
								<strong><?php echo $badge->getExtensionTitle(); ?></strong>
							</td>
						</tr>
						<tr>
							<td>
								<?php echo JText::_( 'COM_EASYSOCIAL_BADGES_FORM_ACHIEVERS' ); ?>:
							</td>
							<td>
								<strong><?php echo $badge->getTotalAchievers();?> <?php echo JText::_( 'COM_EASYSOCIAL_ACHIEVERS' ); ?></strong>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>

<input type="hidden" name="option" value="com_easysocial" />
<input type="hidden" name="controller" value="badges" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="id" value="<?php echo $badge->id; ?>" />
<?php echo JHTML::_( 'form.token' );?>

</form>
