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
<form name="adminForm" id="adminForm" method="post" data-table-grid>

	<?php if( $this->tmpl != 'component' ){ ?>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<?php echo $this->html( 'filter.search' , $search ); ?>
		</div>

		<div class="form-group pull-right">
			<div><?php echo $this->html( 'filter.limit' , $limit ); ?></div>
		</div>
	</div>
	<?php } ?>

	<?php if( $this->tmpl == 'component' ){ ?>
	<div class="app-filter filter-bar form-inline">
		<div class="form-group">
			<div class="input-group">
				<input type="text" name="search" class="form-control input-sm" />
				<span class="input-group-btn">
					<button class="btn btn-md btn-es"><?php echo JText::_( 'COM_EASYSOCIAL_SEARCH_BUTTON' ); ?></button>
				</span>
			</div>
		</div>
	</div>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table table table-eb table-striped">
			<thead>
				<tr>
					<?php if( $this->tmpl != 'component' ){ ?>
					<th width="1%" class="center">
						<input type="checkbox" name="toggle" class="checkAll" data-table-grid-checkall />
					</th>
					<?php } ?>

					<th width="5%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_ALBUM_COVER' ); ?>
					</th>

					<th>
						<?php echo $this->html( 'grid.sort' , 'a.title' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_TITLE') , $ordering , $direction ); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_( 'COM_EASYSOCIAL_TABLE_COLUMN_AUTHOR' ); ?>
					</th>

					<?php if( $this->tmpl != 'component' ){ ?>
					<th width="<?php echo $this->tmpl == 'component' ? '10%' : '5%';?>" class="center">
						<?php echo $this->html( 'grid.sort' , 'totalphotos' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_PHOTOS' ) , $ordering , $direction ); ?>
					</th>

					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'a.core' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CORE') , $ordering , $direction ); ?>
					</th>
					<th width="10%" class="center">
						<?php echo $this->html( 'grid.sort' , 'a.created' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CREATED') , $ordering , $direction ); ?>
					</th>
					<?php } ?>


					<th width="5%" class="center">
						<?php echo $this->html( 'grid.sort' , 'a.id' , JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ID'), $ordering , $direction ); ?>
					</th>
				</tr>
			</thead>

			<tbody>
			<?php if( $albums ){ ?>
				<?php $i = 0; ?>
				<?php foreach( $albums as $album ){ ?>
				<tr>
					<?php if( $this->tmpl != 'component' ){ ?>
					<td>
						<?php echo $this->html( 'grid.id' , $i++ , $album->id ); ?>
					</td>
					<?php } ?>

					<td class="center">
						<img src="<?php echo $album->getCover();?>" width="32" class="mr-5 mt-5" />
					</td>

					<td style="text-align:left;">
						<div class="row">
							<div>
								<?php if( $this->tmpl == 'component' ){ ?>
								<a href="javascript:void(0);"
									data-album-insert
									data-id="<?php echo $album->id;?>"
									data-alias="<?php echo $album->getAlias();?>"
									data-title="<?php echo $this->html( 'string.escape' , $album->get( 'title' ) );?>"
									data-avatar="<?php echo $this->html( 'string.escape' , $album->getCover());?>"
								>
								<?php } ?>
									<?php echo $album->get( 'title' ); ?>

								<?php if( $this->tmpl == 'component' ){ ?>
								</a>
								<?php } ?>

								<div class="fd-small">
									<?php echo $album->get( 'caption' ); ?>
								</div>
							</div>
						</div>
					</td>

					<td class="center">
						<a href="index.php?option=com_easysocial&view=users&layout=form&id=<?php echo $album->uid;?>" data-es-profile-tooltip="<?php echo $album->uid;?>"><?php echo $album->getCreator()->getName();?></a>
					</td>

					<?php if( $this->tmpl != 'component' ){ ?>
					<td class="center">
						<?php echo $album->totalphotos;?> <?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_PHOTOS' ); ?>
					</td>

					<td class="center">
						<?php echo $album->core;?>
					</td>
					<td class="center">
						<?php echo $album->created;?>
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $album->id;?>
					</td>
				</tr>
				<?php } ?>

			<?php } else { ?>
			<tr class="is-empty">
				<td class="empty center" colspan="8">
					<div><?php echo JText::_( 'COM_EASYSOCIAL_ALBUMS_NO_ALBUMS_FOUND_BASED_ON_SEARCH_RESULT' ); ?></div>
				</td>
			</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="8">
						<div class="footer-pagination">
							<?php echo $pagination->getListFooter(); ?>
						</div>
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
	<input type="hidden" name="view" value="albums" />
	<input type="hidden" name="controller" value="albums" />
</form>
