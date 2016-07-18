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
<?php if( $forms ){ ?>
<div class="tab-box<?php echo $sidebarTabs ? ' tab-box-sidenav' : ' tab-box-alt';?>">
	<div class="tabbable">
		<ul class="nav nav-tabs nav-tabs-icons<?php echo $sidebarTabs ? ' nav-tabs-side' : '';?>">
			<?php $i = 0; ?>
			<?php foreach( $forms as $form ){ ?>
				<li class="tab-item<?php echo $i == 0 && !$active || $active == strtolower(str_ireplace($invalidKeys , '' , $form->title ) ) ? ' active' : '';?>"
					data-form-tabs-<?php echo $uid;?>
					data-item="<?php echo strtolower( str_ireplace($invalidKeys , '' , $form->title ) );?>">
					<a href="#<?php echo strtolower( str_ireplace($invalidKeys , '' , $form->title ) );?>-tabs" data-bs-toggle="tab"><?php echo JText::_( $form->title ); ?></a>
				</li>
				<?php $i++;?>
			<?php } ?>
		</ul>

		<div class="tab-content<?php echo $sidebarTabs ? ' tab-content-side' : '';?>">
			<?php $i = 0; ?>
			<?php foreach( $forms as $form ){ ?>
				<div class="tab-pane<?php echo $i == 0 && !$active || $active == strtolower( str_ireplace($invalidKeys , '' , $form->title ) ) ? ' active in' : '';?>"
					id="<?php echo strtolower( str_ireplace($invalidKeys , '' , $form->title ) );?>-tabs"
				>
					<?php if( isset( $form->desc ) ){ ?>
					<p class="fd-small mb-10 mt-5"><?php echo JText::_( $form->desc );?></p>
					<?php } ?>

					<?php if( isset( $form->fields ) && $form->fields ){ ?>
					<table class="table table-striped table-noborder">
						<tbody>
						<?php foreach( $form->fields as $field ){ ?>
						<tr>
							<td width="25%" valign="top">
								<?php if( isset( $field->label ) ){ ?>
								<label for="<?php echo $field->name;?>"><?php echo JText::_( $field->label ); ?></label>
								<?php } ?>
							</td>
							<td width="1%" valign="top">
								<?php if( isset( $field->tooltip) ){ ?>
								<i data-placement="bottom" data-title="<?php echo JText::_( $field->label );?>"
									data-content="<?php echo JText::_( $field->tooltip );?>"
									data-es-provide="popover" class="icon-es-help pull-left"></i>
								<?php } ?>
							</td>
							<td valign="top">
								<?php if( isset( $field->output ) && $field->output ){ ?>
									<?php echo $field->output;?>
								<?php } else { ?>
									<?php echo $this->loadTemplate( 'admin/forms/types/' . $field->type , array( 'params' => $params , 'field' => $field ) ); ?>
								<?php } ?>
							</td>
						</tr>
						<?php } ?>
						</tbody>
					</table>
					<?php } ?>
				</div>
				<?php $i++;?>
			<?php } ?>
		</div>

	</div>
</div>
<?php } ?>
