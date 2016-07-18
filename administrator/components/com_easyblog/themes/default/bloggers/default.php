<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-grid-eb>

    <div class="app-filter filter-bar form-inline">
        <div class="form-group">
            <?php echo $this->html('filter.search', $search); ?>
        </div>
        <div class="form-group">
            <?php echo $state; ?>
        </div>

        <div class="form-group pull-right">
            <?php echo $pagination->getLimitBox(); ?>
        </div>
    </div>

    <div class="panel-table">

        <?php if (!$browse) { ?>
        <div class="alert alert-warning">
             <strong>
                &nbsp;<i class="fa fa-support"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_BLOGGERS_LISTING_INFO'); ?>
                &nbsp;<a href="index.php?option=com_easyblog&view=acls" class="btn btn-sm btn-primary"><?php echo JText::_('COM_EASYBLOG_MANAGE_ACL_BUTTON');?></a>
            </strong>
        </div>
        <?php } ?>

        <table class="app-table table table-eb table-striped">
            <thead>
                <tr>
                    <?php if (!$browse) { ?>
                    <th width="5"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
                    <?php } ?>

                    <th style="text-align: left;">
                        <?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_NAME' ), 'a.name', $orderDirection, $order ); ?>
                    </th>

                    <?php if (!$browse) { ?>
                    <th width="1%"><?php echo JText::_( 'COM_EASYBLOG_FEATURED' ); ?></th>

                    <th style="text-align: center;" width="10%">
                        <?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_BLOGGERS_USERNAME' ) , 'a.username', $orderDirection, $order ); ?>
                    </th>

                    <th style="text-align: center;" width="15%">
                        <?php echo JText::_('COM_EASYBLOG_BLOGGERS_USER_GROUP');?>
                    </th>

                    <th class="center" width="10%">
                        <?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_EMAIL' ) , 'a.email', $orderDirection, $order ); ?>
                    </th>
                    <?php } ?>

                    <th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'ID', 'a.id', $orderDirection, $order ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if ($authors) { ?>
                    <?php $i = 0; ?>

                    <?php foreach ($authors as $author) { ?>
                    <tr data-item
                        data-id="<?php echo $author->id;?>"
                        data-title="<?php echo $author->getName();?>"
                    >
                        <?php if (!$browse) { ?>
                        <td class="center">
                            <?php echo $this->html('grid.id', $i++, $author->id); ?>
                        </td>
                        <?php } ?>

                        <td>
                            <div class="media">
                                <div class="media-object pull-left mr-20">
                                    <img src="<?php echo $author->getAvatar();?>" width="42" height="42" class="avatar img-rounded" />
                                </div>

                                <div class="media-body">
                                    <?php if ($browse) { ?>
                                        <a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $author->id;?>','<?php echo addslashes($this->escape($author->getName()));?>');"><?php echo $author->getName();?></a>
                                    <?php } else { ?>
                                        <a href="index.php?option=com_easyblog&view=bloggers&layout=form&id=<?php echo $author->id;?>"><?php echo $author->getName();?></a>
                                    <?php } ?>

                                    <?php if (!$browse) { ?>
                                    <p class="blogger-bio mt-5">
                                        <?php echo $author->getBiography();?> <?php echo JText::_('COM_EASYBLOG_ELLIPSES');?>
                                    </p>

                                    <div class="blogger-meta mt-5">
                                        <span class="mr-10">
                                            <i class="fa fa-list-alt"></i>&nbsp; <?php echo JText::sprintf('COM_EASYBLOG_BLOGGERS_POST_COUNT', $author->postCount);?>
                                        </span>
                                    </div>

                                    <?php } ?>
                                </div>
                            </div>
                        </td>

                        <?php if (!$browse) { ?>
                        <td class="nowrap hidden-phone center">
                            <?php echo $this->html('grid.featured', $author, 'bloggers', 'featured', array('bloggers.feature', 'bloggers.unfeature')); ?>
                        </td>

                        <td class="center">
                            <?php echo $author->user->username; ?>
                        </td>

                        <td class="center">
                            <?php echo $author->usergroups;?>
                        </td>

                        <td class="center">
                            <?php echo $author->user->email; ?>
                        </td>
                        <?php } ?>

                        <td class="center">
                            <?php echo $author->id; ?>
                        </td>

                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="12" class="text-center">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php if($browse): ?>
    <input type="hidden" name="tmpl" value="component" />
    <?php endif; ?>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="browse" value="<?php echo $browse;?>" />
    <input type="hidden" name="browsefunction" value="<?php echo $browsefunction;?>" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="" />
    <input type="hidden" name="view" value="bloggers" />
</form>
