<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="dash-activity">
	<div class="dash-activity-head">
		<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_RECENT_ACTIVITIES');?></b>
	</div>

	<ul class="dash-activity-filter list-unstyled">
		<li>
			<b><?php echo JText::_('COM_EASYBLOG_DASHBOARD_FILTERS');?>:</b>
		</li>
		<li class="active">
			<a href="#posts" id="posts-tab" role="tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_POSTS');?></a>
		</li>
		<li>
			<a href="#comments" role="tab" id="comments-tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_COMMENTS');?></a>
		</li>
		<li>
			<a href="#pending" role="tab" id="pending-tab" data-bp-toggle="tab"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_TAB_PENDING');?></a>
		</li>
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane in active" id="posts" aria-labelledby="posts-tab">
			<div class="dash-stream dash-stream-graph">
				<div  data-chart-posts style="height: 200px; width: 100%;"></div>
				<div data-chart-posts-legend></div>
			</div>

			<?php if ($posts) { ?>
				<?php foreach ($posts as $post) { ?>
				<div class="dash-stream">
					<a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=bloggers&layout=form&id='.$post->getAuthor()->id); ?>" class="dash-stream-avatar pull-left">
						<img src="<?php echo $post->getAuthor()->getAvatar();?>" class="img-circle" width="50" height="50" />
					</a>
					<div class="dash-stream-content">
						<div class="dash-stream-headline">
							<b><a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=bloggers&layout=form&id='.$post->getAuthor()->id); ?>"><?php echo $post->getAuthor()->getName();?></a></b>
							<?php echo JText::_('COM_EASYBLOG_DASHBOARD_PUBLISHED_A_POST'); ?>
						</div>
						<div class="dash-stream-clip">
							<i class="dash-stream-icon fa fa-file"></i>
							<a data-eb-composer href="<?php echo JRoute::_('index.php?option=com_easyblog&view=composer&tmpl=component&uid='.$post->uid); ?>" class="dash-stream-post-title"><?php echo $post->title;?></a>
							<div class="dash-stream-post-content">
								<?php echo JString::substr(strip_tags($post->getIntro(EASYBLOG_STRIP_TAGS)), 0, 250) . JText::_('COM_EASYBLOG_ELLIPSES');?>
							</div>
							<div class="dash-stream-post-meta">
								<?php echo JText::_('COM_EASYBLOG_DASHBOARD_POSTED_UNDER');?>: <a href="<?php echo JRoute::_('index.php?option=com_easyblog&view=categories&layout=form&id='.$post->getPrimaryCategory()->id); ?>"><?php echo $post->getPrimaryCategory()->title;?></a>
							</div>
						</div>

						<div class="dash-stream-time">
							<?php echo $this->html('string.date', $post->created, JText::_('DATE_FORMAT_LC2'));?>
						</div>
					</div>
				</div>

				<?php } ?>
			<?php } else { ?>
			<div class="dash-stream empty">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_POSTS_YET');?>
			</div>
			<?php } ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="comments" aria-labelledby="comments-tab">
			<div class="dash-stream dash-stream-graph">
				<div data-chart-comments style="height: 200px; width: 100%;"></div>
				<div data-chart-comments-legend></div>
			</div>

			<?php if ($comments) { ?>
				<?php foreach ($comments as $comment) { ?>
                <div class="dash-stream">
                    <a href="javascript:void(0);" class="dash-stream-avatar pull-left">
                        <img src="<?php echo $comment->getAuthorAvatar();?>" class="img-circle" width="50" height="50" />
                    </a>
                    <div class="dash-stream-content">
                        <div class="dash-stream-headline">
                            <b><a><?php echo $comment->getAuthorName();?></a></b>
                            <?php echo JText::_('COM_EASYBLOG_DASHBOARD_POSTED_COMMENT_IN'); ?>
                            <b>
                            	<a><?php echo $comment->getBlog()->title;?></a>
                            </b>
                        </div>
                        <div class="dash-stream-clip">
                            <i class="dash-stream-icon fa fa-comment"></i>
                            <?php echo $comment->getContent();?>
                        </div>
                        <div class="dash-stream-time">
                        	<?php echo $this->html('string.date', $comment->created, JText::_('DATE_FORMAT_LC2'));?>
                        </div>
                    </div>
                </div>
                <?php } ?>
			<?php } else { ?>
			<div class="dash-stream empty">
				<?php echo JText::_('COM_EASYBLOG_DASHBOARD_NO_COMMENTS_YET');?>
			</div>
			<?php } ?>
		</div>

		<div role="tabpanel" class="tab-pane" id="pending" aria-labelledby="pending-tab">
			<?php if ($pending) { ?>
				<?php foreach ($pending as $post) { ?>
					<div class="dash-activity-pending">
						<div class="dash-stream">
							<a href="javascript:void(0);" class="dash-stream-avatar pull-left">
								<img src="<?php echo $post->getAuthor()->getAvatar();?>" class="img-circle" width="50" height="50">
							</a>
							<div class="dash-stream-content">
								<div class="dash-stream-headline">
									<b><a><?php echo $post->getAuthor()->getName();?></a></b>
									<?php echo JText::_('COM_EASYBLOG_DASHBOARD_PENDING_SUBMITTED_FOR_REVIEW');?>
								</div>
								<div class="dash-stream-clip">
									<i class="dash-stream-icon fa fa-pencil"></i>
									<a class="dash-stream-post-title"><?php echo $post->title;?></a>
									<div class="dash-stream-post-content">
										<?php echo JString::substr($post->getIntro(EASYBLOG_STRIP_TAGS), 0, 250) . JText::_('COM_EASYBLOG_ELLIPSES');?>
									</div>
									<div class="dash-stream-post-meta">
										<?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_STATS_POSTED_UNDER', '<a href="' . $post->getPrimaryCategory()->getPermalink() . '">' . $post->getPrimaryCategory()->getTitle() . '</a>'); ?>
										&middot;
										<?php echo $this->html('string.date', $post->created, JText::_('DATE_FORMAT_LC2'));?>
									</div>
								</div>

								<div class="dash-stream-actions">
									<div>
										<div class="btn-group btn-group-sm">
											<a href="<?php echo $post->getEditLink();?>" class="btn btn-default" target="_blank"><?php echo JText::_('COM_EASYBLOG_EDIT_BUTTON');?></a>
											<button type="button" class="btn btn-default"><?php echo JText::_('COM_EASYBLOG_PREVIEW_BUTTON');?></button>
										</div>

										<button type="button" class="btn btn-sm btn-primary" data-id="<?php echo $post->id;?>" data-approve-post>
											<?php echo JText::_('COM_EASYBLOG_APPROVE_POST');?>
										</button>

										<button type="button" class="btn btn-sm btn-danger" data-id="<?php echo $post->id;?>" data-reject-post>
											<?php echo JText::_('COM_EASYBLOG_REJECT_POST');?>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="dash-stream empty">
					<?php echo JText::_('COM_EASYBLOG_NO_PENDING_POSTS_CURRENTLY'); ?>
				</div>
			<?php } ?>
		</div>
	</div>
	<!--END: TABS-->

</div>
