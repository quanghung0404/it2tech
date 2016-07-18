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
<div class="es-container es-conversation-list" data-conversations>
	<a href="javascript:void(0);" class="btn btn-block btn-es-inverse btn-sidebar-toggle" data-sidebar-toggle>
		<i class="fa fa-grid-view  mr-5"></i> <?php echo JText::_( 'COM_EASYSOCIAL_SIDEBAR_TOGGLE' );?>
	</a>
	<div class="es-sidebar" data-sidebar>

		<?php echo $this->render( 'module' , 'es-conversations-sidebar-top' ); ?>

		<div class="conversation-sidebar" data-conversations-mailbox>
			<div class="es-widget">
				<?php if( $this->access->allowed( 'conversations.create' ) ){ ?>
				<div class="es-widget-create mr-10">
					<a class="btn btn-sm btn-es-primary btn-block composeConversation" href="<?php echo FRoute::conversations( array( 'layout' => 'compose' ) );?>">
						<?php echo JText::_( 'COM_EASYSOCIAL_COMPOSE_BUTTON' ); ?>
					</a>
				</div>
				<hr class="es-hr mt-15 mb-10" />
				<?php } ?>

				<ul class="es-widget-filter fd-reset-list conversationList">
					<li class="mailbox-item<?php echo $active == 'inbox' ? ' active' : '';?>"
						data-mailboxItem
						data-mailbox="inbox"
						data-title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_INBOX' , true );?><?php echo $totalNewInbox > 0 ? ' (' . $totalNewInbox . ')' : '';?>"
						data-url="<?php echo FRoute::conversations();?>">
						<a href="javascript:void(0);">
							<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_INBOX' ); ?>
							<span data-mailboxItem-counter>
								<?php if( $totalNewInbox > 0){ ?>
								(<?php echo $totalNewInbox;?>)
								<?php } ?>
							</span>
						</a>
					</li>

					<li class="mailbox-item<?php echo $active == 'archives' ? ' active' : '';?>"
						data-mailboxItem
						data-mailbox="archives"
						data-title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_CONVERSATIONS_ARCHIVES' , true );?><?php echo $totalNewArchives > 0 ? ' (' . $totalNewArchives . ')' : '';?>"
						data-url="<?php echo FRoute::conversations( array( 'layout' => 'archives' ) );?>">
						<a href="javascript:void(0);">
							<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_ARCHIVES' );?>
							<span data-mailboxItem-counter>
								<?php if( $totalNewArchives > 0){ ?>
								(<?php echo $totalNewArchives;?>)
								<?php } ?>
							</span>
						</a>
					</li>
				</ul>
			</div>
		</div>

		<?php echo $this->render( 'module' , 'es-conversations-sidebar-bottom' ); ?>
	</div>

	<div class="es-content<?php echo !$conversations ? ' is-empty' : '';?><?php echo $active == 'archives' ? ' layout-archives' : '';?>" data-conversations-content>
		<?php echo $this->render( 'module' , 'es-conversations-before-contents' ); ?>

		<div class="conversation-tool-header pa-10">

			<div class="row-table">
				<div class="col-cell cell-tight">
					<input type="checkbox" id="checkAll" class="item-check" name="checkAll" data-conversations-checkAll style="margin: 0 10px 0 0;" />
				</div>

				<div class="conversation-actions" data-conversations-actions>
					<a href="javascript:void(0);" class="btn btn-es btn-sm" data-conversations-unarchive><?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_UNARCHIVE_SELECTED');?></a>

					<a href="javascript:void(0);" class="btn btn-es btn-sm" data-conversations-archive><?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_ARCHIVE_SELECTED'); ?></a>

					<a href="javascript:void(0);" class="btn btn-es btn-sm" data-conversations-delete title="<?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_DELETE_SELECTED');?>">
						<i class="fa fa-trash"></i>
					</a>

					<div class="btn-group">
						<a href="javascript:void(0);" class="btn btn-es btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
							<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MORE_ACTIONS' ); ?>
							<i class="fa fa-caret-down "></i>
						</a>
						<ul class="dropdown-menu">
							<li data-conversations-read>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MARK_AS_READ' );?>
								</a>
							</li>
							<li data-conversations-unread>
								<a href="javascript:void(0);">
									<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_MARK_AS_UNREAD' );?>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<label class="col-cell cell-label" for="checkAll">
					<?php echo JText::_('COM_EASYSOCIAL_CONVERSATIONS_CHECKALL'); ?>
				</label>

				<div class="col-cell cell-tight cell-filter">
					<ul class="fd-reset-list conversation-filters">
						<!--
						<li class="filter-meta">
							<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER' );?>:
						</li>
						-->
						<li <?php echo ( $filter == '' || $filter == 'all' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="all">
							<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_ALL' );?></a>
						</li>
						<li <?php echo ( $filter == 'unread' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="unread">
							<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_UNREAD' );?></a>
						</li>
						<li <?php echo ( $filter == 'read' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="read">
							<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_READ' );?></a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<ul class="filter-mobile fd-reset-list conversation-filters">
			<li class="filter-meta">
				<?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER' );?>:
			</li> 
			<li <?php echo ( $filter == '' || $filter == 'all' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="all">
				<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_ALL' );?></a>
			</li>
			<li <?php echo ( $filter == 'unread' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="unread">
				<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_UNREAD' );?></a>
			</li>
			<li <?php echo ( $filter == 'read' ) ? 'class="active"' : ''; ?> data-conversations-filter data-filter="read">
				<a href="javascript:void(0);" class="filterItem"><?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_FILTER_READ' );?></a>
			</li>
		</ul>
		<div class="pa-10">
			<div class="text-center loading-wrap">
				<i class="loading-indicator fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_LOADING' );?></i>
			</div>

			<ul class="conversation-list fd-reset-list" data-conversations-list>
				<?php echo $this->includeTemplate( 'site/conversations/default.item' ); ?>
			</ul>

			<div class="text-center empty">
				<div>
					<i class="icon-es-mailbundle mr-10"></i> <?php echo JText::_( 'COM_EASYSOCIAL_CONVERSATIONS_EMPTY_CONVERSATION_LIST' );?>
				</div>
			</div>


		</div>

		<?php echo $this->render( 'module' , 'es-conversations-after-contents' ); ?>
	</div>

</div>
