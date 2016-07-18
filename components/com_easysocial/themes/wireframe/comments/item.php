<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<li class="fd-small es-comment" data-comments-item data-id="<?php echo $comment->id; ?>" data-child="<?php echo $comment->child; ?>">
    <div class="media">
        <div class="media-object pull-left">
            <div class="es-avatar es-avatar-sm" data-comments-item-avatar>
                <?php echo $this->loadTemplate('site/avatar/default', array('user' => $user)); ?>
                <a href="<?php echo $user->getPermalink();?>" title="<?php echo $this->html( 'string.escape' , $user->getName() );?>"></a>
            </div>
        </div>
        <div class="media-body">
            <div data-comments-item-commentFrame data-comments-item-frame>
                <div data-comments-item-author>
                    <a href="<?php echo $user->getPermalink(); ?>"><?php echo $user->getName(); ?></a>
                </div>

                <div class="es-comment-actions " data-comments-item-actions>
                    <div class="es-comment-actions-flyout">

                        <?php if ($this->my->id && ($this->access->allowed('comments.report')
                                                        || ($this->access->allowed( 'comments.edit' )
                                                        || ($isAuthor && $this->access->allowed('comments.editown')))
                                                        || ($deleteable || $this->access->allowed('comments.delete') || ($isAuthor && $this->access->allowed('comments.deleteown'))))) { ?>
                        <a class="es-comment-actions-toggle" href="javascript:void(0);" data-bs-toggle="dropdown"><i class="icon-es-comment-action"></i></a>
                        <ul class="fd-nav fd-nav-stacked pull-right es-comment-actions-nav dropdown-menu">
                            <?php if ($this->access->allowed('comments.report')) { ?>
                            <li>
                                <?php echo FD::reports()->getForm('com_easysocial', 'comments', $comment->id, JText::sprintf('COM_EASYSOCIAL_COMMENTS_REPORT_ITEM_TITLE' , $user->getName()), JText::_( 'COM_EASYSOCIAL_COMMENTS_REPORT_ITEM' ), '' , JText::_( 'COM_EASYSOCIAL_COMMENTS_REPORT_TEXT' ) , FRoute::external($comment->getPermalink())); ?>
                            </li>
                            <?php } ?>

                            <?php if ($this->access->allowed( 'comments.edit' ) || ( $isAuthor && $this->access->allowed('comments.editown'))) { ?>
                            <li class="btn-comment-edit" data-comments-item-actions-edit>
                                <a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_COMMENTS_ACTION_EDIT' ); ?></a>
                            </li>
                            <?php } ?>

                            <?php if( $deleteable || $this->access->allowed( 'comments.delete' ) || ( $isAuthor && $this->access->allowed( 'comments.deleteown' ) ) ) { ?>
                            <li class="btn-comment-delete" data-comments-item-actions-delete>
                                <a href="javascript:void(0);"><?php echo JText::_( 'COM_EASYSOCIAL_COMMENTS_ACTION_DELETE' ); ?></a>
                            </li>
                            <?php } ?>
                        </ul>

                        <?php } ?>
                    </div>
                </div>

                <div data-comments-item-comment><?php echo $comment->getComment(); ?></div>

                <?php if ($attachments && $this->config->get('comments.attachments')) { ?>
                <ul class="es-comment-attachments clearfix<?php echo count($attachments) > 1 ? ' is-multiple' : ''; ?>">
                    <?php foreach ($attachments as $attachment) { ?>
                    <li data-comment-attachment-item>
                        <?php if ($attachment->user_id == $this->my->id || $this->my->isSiteAdmin()) { ?>
                        <b href="javascript:void(0);" class="es-comment-attachment-remove" data-comment-attachment-delete data-id="<?php echo $attachment->id;?>"></b>
                        <?php } ?>

                        <?php if (count($attachments) > 1) { ?>
                            <a href="<?php echo $attachment->getURI();?>" target="_blank" style="background-image: url('<?php echo $attachment->getURI();?>')"
                                data-title="<?php echo $this->html('string.escape', $attachment->name);?>"
                                data-lightbox="comment-<?php echo $comment->id;?>"
                            >
                                <i class="fa fa-search"></i>
                            </a>
                        <?php } else { ?>
                            <a href="<?php echo $attachment->getURI();?>" target="_blank"
                                data-title="<?php echo $this->html('string.escape', $attachment->name);?>"
                                data-lightbox="comment-<?php echo $comment->id;?>"
                            >
                                <img src="<?php echo $attachment->getURI();?>" />
                                <i class="fa fa-search"></i>
                            </a>
                        <?php } ?>
                    </li>
                    <?php } ?>
                </ul>
                <?php } ?>

                <div class="es-comment-item-meta" data-comments-item-meta>
                    <div class="es-comment-item-date" data-comments-item-date>
                        <i class="icon-es-clock"></i>

                        <?php if ($comment->getPermalink()) { ?>
                            <a href="<?php echo $comment->getPermalink(); ?>" title="<?php echo $comment->getDate(false); ?>"><?php echo $comment->getDate(); ?></a>
                        <?php } else { ?>
                            <?php echo $comment->getDate(); ?>
                        <?php } ?>
                    </div>

                    <?php if( $this->config->get( 'comments.reply' ) ) { ?>
                    <div data-comments-item-reply class="es-comment-item-reply"><i class="icon-es-dialog"></i>
                        <a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_COMMENTS_REPLY');?></a>
                    </div>
                    <?php } ?>

                    <div class="es-comment-item-like" data-comments-item-like>
                        <i class="icon-es-heart"></i>
                        <a href="javascript:void(0);"><?php echo $likes->hasLiked() ? JText::_( 'COM_EASYSOCIAL_LIKES_UNLIKE' ) : JText::_( 'COM_EASYSOCIAL_LIKES_LIKE' ); ?></a>
                    </div>
                    <div data-comments-item-likeCount class="es-comment-item-likecount" data-original-title="<?php echo strip_tags( $likes->toString( null, true ) ); ?>" data-placement="top" data-es-provide="tooltip"><?php echo $likes->getCount(); ?></div>
                </div>
            </div>

            <div data-comments-item-loadReplies class="es-comment-item-loadreply" <?php if( !$comment->hasChild() ) { ?>style="display: none;"<?php } ?>>
                <a href="javascript:void(0);"><?php echo JText::_('COM_EASYSOCIAL_COMMENTS_VIEW_REPLIES');?></a>
            </div>

            <div data-comments-item-frame data-comments-item-editFrame style="display: none;">
            </div>

            <div data-comments-item-statusFrame data-comments-item-frame style="display: none;">
                <div class="alert alert-comment-error"></div>
            </div>
        </div>
    </div>
</li>
