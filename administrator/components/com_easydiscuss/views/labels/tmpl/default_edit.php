<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
EasyDiscuss
.require()
.library('ui/datepicker')
.done(function($){

	$( "#datepicker" ).datepicker({
		dateFormat: "DD, d MM, yy"
	});

	$.datepicker.setDefaults( $.datepicker.regional[ "" ] );

	EasyDiscuss(function($){
		$.Joomla( 'submitbutton' , function(action){
			if ( action != 'remove' || action == 'savePublishNew' ) {
				$.Joomla( 'submitform' , [action] );
				if( action == 'savePublishNew' ) {
					action = 'save';
					$( '#savenew' ).val( '1' );
				}
				$( "#datepicker" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
				$.Joomla( 'submitform' , [action] );
			}
		});
	});
});
</script>
<div class="adminform-body">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span6">
			<div class="panel">
				<div class="panel-head">
					<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
					<h6><?php echo JText::_('COM_EASYDISCUSS_LABEL'); ?></h6>
					<i class="icon-chevron-down"></i>
					</a>
				</div>

				<div id="option01" class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<label><?php echo JText::_( 'COM_EASYDISCUSS_LABEL_TITLE' ); ?></label>
							</div>
							<div class="col-md-7">
								<input type="text" class="input-large" name="title" size="55" maxlength="255" value="<?php echo $this->label->title;?>" />
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<label><?php echo JText::_( 'COM_EASYDISCUSS_LABEL_DESCRIPTION' ); ?></label>
							</div>
							<div class="col-md-7">
								<textarea name="description" class="form-control"><?php echo $this->label->description; ?></textarea>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<label><?php echo JText::_( 'COM_EASYDISCUSS_PUBLISHED' ); ?></label>
							</div>
							<div class="col-md-7">
								<?php echo $this->renderCheckbox( 'published' , $this->label->published ); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<label><?php echo JText::_( 'Created' ); ?></label>
							</div>
							<div class="col-md-7">
								<input type="text" id="datepicker" class="form-control" name="created_time" value="<?php echo DiscussDateHelper::toFormat($this->label->created, '%A, %B %d %Y'); ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="labels" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="label_id" value="<?php echo $this->label->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>
</div>
