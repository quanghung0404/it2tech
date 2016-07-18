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
<?php if( $user->id != $this->my->id ){ ?>
<div class="mb-15">
	<?php echo $this->loadTemplate( 'site/profile/mini.header' , array( 'user' => $user ) ); ?>
</div>
<?php } ?>

<div class="es-container" data-profile-about>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>
		<div class="es-widget">
			<div class="es-widget-head">
				<div class="pull-left widget-title">
					<?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_ABOUT_USER' ); ?>
				</div>
			</div>
			<div class="es-widget-body">
				<ul class="widget-list fd-nav fd-nav-stacked">
					<?php if( $steps ){ ?>
						<?php $i = 0; ?>
						<?php foreach( $steps as $step ){ ?>
							<?php if( !$step->hide ) { ?>
							<li class="tab-item<?php echo $i == 0 ? ' active' : '';?>" data-profile-about-step-item data-for="<?php echo $step->id; ?>">
								<a href="javascript:void(0);"><?php echo $step->get( 'title' ); ?></a>
							</li>
							<?php } ?>
							<?php $i++; ?>
						<?php } ?>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>

	<div class="es-content pt-20">
		<?php $i = 0; ?>
		<?php foreach( $steps as $step ){ ?>
			<?php if( !$step->hide ) { ?>
			<div id="tab-<?php echo $step->id;?>" class="profile-data-box step-content tab-pane<?php echo $i == 0 ? ' active' :'';?>" data-id="<?php echo $step->id; ?>" data-profile-about-step-content>
				<?php if( $step->fields ){ ?>
					<?php $empty = true; ?>
    					<table class="table table-striped profile-data-table">
    						<tbody>
    							<?php foreach( $step->fields as $field ){ ?>
    								<?php if( !empty( $field->output ) ){ ?>
    										<?php echo $field->output; ?>
    									<?php $empty = false; ?>
    								<?php } ?>
    							<?php } ?>
    						</tbody>
    					</table>

					<?php if( $empty ){ ?>
					<div class="empty center">
						<i class="icon-es-empty-profile mb-10"></i>
						<div><?php echo JText::_( 'COM_EASYSOCIAL_PROFILE_ABOUT_EMPTY_INFORMATION' );?></div>
					</div>
					<?php } ?>
				<?php } else { ?>
				<?php } ?>
			</div>
			<?php } ?>
			<?php $i++; ?>
		<?php } ?>

	</div>
</div>
