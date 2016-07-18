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
<form action="index.php" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>

	<div class="app-filter filter-bar form-inline<?php echo $callback ? ' mt-20' : '';?>">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>
	</div>

	<div id="profilesTable" class="panel-table" data-profiles>
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<?php if( !$callback ){ ?>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th style="text-align: left;">
						<?php echo $this->html( 'grid.sort' , 'title' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_TITLE' ) , $ordering , $direction ); ?>
					</th>

					<?php if (!$callback) { ?>
					<th width="20%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ACTIONS' ); ?>
					</th>
					<?php } ?>

					<th width="10%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CATEGORY' ); ?>
					</th>

					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED_BY' ); ?>
					</th>

					<?php if( !$callback ){ ?>
					<th width="15%" class="center">
						<?php echo $this->html( 'grid.sort' , 'created' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_CREATED' ) , $ordering , $direction ); ?>
					</th>
					<?php } ?>

					<th width="<?php echo $callback ? '10%' : '5%';?>" class="center">
						<?php echo $this->html( 'grid.sort' , 'id' , JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_ID' ) , $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>
			<tbody>

				<?php if( $groups ){ ?>
					<?php $i = 0; ?>
					<?php foreach( $groups as $group ){ ?>
					<tr class="row<?php echo $i; ?>"
						data-profiles-item
						data-grid-row
						data-title="<?php echo $this->html( 'string.escape' , $group->getName() );?>"
						data-id="<?php echo $group->id;?>"
					>
						<?php if( !$callback ){ ?>
						<td align="center" valign="top">
							<?php echo $this->html( 'grid.id' , $i , $group->id ); ?>
						</td>
						<?php } ?>

						<td>
							<div class="media">
								<div class="media-object pull-left">
									<img src="<?php echo $group->getAvatar();?>" class="es-avatar mr-10" />
								</div>

								<div class="media-body">
									<?php if( $group->isOpen() ){ ?>
									<span class="label label-success"><i class="fa fa-globe"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_OPEN_GROUP' ); ?></span>
									<?php } ?>

									<?php if( $group->isClosed() ){ ?>
									<span class="label label-danger"><i class="fa fa-lock"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_CLOSED_GROUP' ); ?></span>
									<?php } ?>

									<?php if( $group->isInviteOnly() ){ ?>
									<span class="label label-danger"><i class="fa fa-lock"></i> <?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_INVITE_GROUP' ); ?></span>
									<?php } ?>

									<a href="<?php echo FRoute::_( 'index.php?option=com_easysocial&view=groups&layout=form&id=' . $group->id );?>" data-group-insert data-id="<?php echo $group->id;?>"><?php echo $group->getName(); ?></a>

									<p class="mt-5 fd-small">
										<?php echo $group->description;?>
									</p>
								</div>
							</div>
						</td>

						<?php if( !$callback ){ ?>
						<td class="center">
							<a href="javascript:void(0);" class="btn btn-sm btn-es-success" data-pending-approve>
								<?php echo JText::_( 'COM_EASYSOCIAL_USER_APPROVE_BUTTON' ); ?>
							</a>

							<a href="javascript:void(0);" class="btn btn-sm btn-es-danger ml-5" data-pending-reject>
								<?php echo JText::_( 'COM_EASYSOCIAL_USER_REJECT_BUTTON' ); ?>
							</a>
						</td>

						<td class="center">
							<a href="index.php?option=com_easysocial&view=groups&layout=categoryForm&id=<?php echo $group->getCategory()->id;?>"><?php echo $group->getCategory()->get('title'); ?></a>
						</td>

						<td class="center">
							<?php echo $this->html( 'html.user' , $group->getCreator()->id );?>
						</td>

						<td class="center">
							<?php echo FD::date( $group->created )->format( JText::_( 'DATE_FORMAT_LC1' ) );?>
						</td>
						<?php } ?>

						<td class="center">
							<?php echo $group->id;?>
						</td>
					</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<tr class="is-empty">
						<td colspan="8" class="center empty">
							<?php echo JText::_( 'COM_EASYSOCIAL_GROUPS_NO_PENDING_GROUPS_CURRENTLY' );?>
						</td>
					</tr>
				<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8" class="center">
						<div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
	<input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
	<input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
	<input type="hidden" name="task" value="" data-table-grid-task />
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="groups" />
	<input type="hidden" name="controller" value="groups" />
</form>
