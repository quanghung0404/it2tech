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
$originalOrders = array();
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
        <table class="app-table app-table-middle table table-eb table-striped table-hover">
            <thead>
                <tr>
                    <?php if (!$browse) { ?>
                    <th width="1%">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                    </th>
                    <?php } ?>

                    <th>
                        <?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_CATEGORY_TITLE' ) , 'title', $orderDirection, $order ); ?>
                    </th>

                    <th width="5%" class="center nowrap">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_DEFAULT'); ?>
                    </th>

                    <?php if (!$browse) { ?>
                    <th width="5%" class="center nowrap">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_PUBLISHED'); ?>
                    </th>

                    <th width="5%" class="center">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_POSTS'); ?>
                    </th>

                    <th width="5%">
                        <?php echo JText::_('COM_EASYBLOG_CATEGORIES_CHILD_COUNT'); ?>
                    </th>
                    <th width="5%" class="center nowrap">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_LANGUAGE'); ?>
                    </th>
                    <th width="10%" class="center">
                        <?php echo JHTML::_('grid.sort',   'Order', 'lft', 'desc', $order); ?>
                        <?php echo JHTML::_('grid.order',  $categories, '', 'category.saveorder'); ?>
                    </th>
                    <?php } ?>

                    <th class="center" width="10%">
                        <?php echo JHTML::_('grid.sort', JText::_( 'COM_EASYBLOG_CATEGORIES_AUTHOR') , 'created_by', $orderDirection, $order); ?>
                    </th>

                    <th width="1%" class="center">
                        <?php echo JText::_('COM_EASYBLOG_ID'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if( $categories ){ ?>
                    <?php $i = 0; ?>
                    <?php foreach ($categories as $row) { ?>
                    <tr>
                        <?php if (!$browse) { ?>
                        <td>
                            <?php echo $this->html('grid.id', $i, $row->id); ?>
                        </td>
                        <?php } ?>

                        <td align="left">
                            <?php echo str_repeat( '|&mdash;' , $row->depth ); ?>
                            <span class="editlinktip hasTip">
                            <?php if( $browse ){ ?>
                                <a href="javascript:void(0);" onclick="parent.<?php echo $browsefunction; ?>('<?php echo $row->id;?>','<?php echo addslashes($this->escape($row->title));?>');"><?php echo $row->title;?></a>
                            <?php } else { ?>
                                <a href="index.php?option=com_easyblog&view=categories&layout=form&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
                            <?php } ?>
                            </span>
                        </td>
                        <td class="center">
                            <?php echo $this->html('grid.featured', $row, 'category', 'default', 'category.makeDefault'); ?>
                        </td>

                        <?php if (!$browse) { ?>
                        <td class="center">
                            <?php echo $this->html('grid.published', $row, 'category', 'published'); ?>
                        </td>

                        <td class="center">
                            <?php echo $row->count; ?>
                        </td>

                        <td class="center">
                            <?php echo $row->child_count; ?>
                        </td>

                        <td class="center">
                            <?php if (!$row->language || $row->language == '*') { ?>
                                <?php echo JText::_('COM_EASYBLOG_LANGUAGE_ALL');?>
                            <?php } else { ?>
                                <?php echo $row->language;?>
                            <?php } ?>
                        </td>

                        <td class="order center">
                            <?php $orderkey = array_search($row->id, $ordering[$row->parent_id]); ?>

                            <?php $disabled = 'disabled="disabled"'; ?>
                            <input type="text" name="order[]" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="order-value input-xsmall"/>
                            <?php $originalOrders[] = $orderkey + 1; ?>

                            <?php if ($saveOrder) : ?>
                                <span class="order-up"><?php echo $pagination->orderUpIcon($i, isset($ordering[$row->parent_id][$orderkey - 1]), 'category.orderup', 'Move Up', $ordering); ?></span>
                                <span class="order-down"><?php echo $pagination->orderDownIcon($i, $pagination->total, isset($ordering[$row->parent_id][$orderkey + 1]), 'category.orderdown', 'Move Down', $ordering); ?></span>
                            <?php endif; ?>
                        </td>
                        <?php } ?>



                        <td class="center">
                            <?php if($browse) { ?>
                                <?php echo JFactory::getUser( $row->created_by )->name; ?>
                            <?php } else { ?>
                                <a href="<?php echo JRoute::_('index.php?option=com_easyblog&c=user&id=' . $row->created_by . '&task=edit'); ?>"><?php echo JFactory::getUser( $row->created_by )->name; ?></a>
                            <?php } ?>
                        </td>
                        <td align="center">
                            <?php echo $row->id;?>
                        </td>
                    </tr>
                        <?php $i++; ?>
                    <?php } ?>
                <?php } else { ?>
                <tr>
                    <td colspan="12" align="center">
                        <?php echo JText::_('COM_EASYBLOG_NO_CATEGORY_CREATED_YET');?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="12">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php echo $this->html('form.action'); ?>

    <?php if ($browse) { ?>
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="browseFunction" value="<?php echo $browsefunction;?>" />
    <?php } ?>
    <input type="hidden" name="browse" value="<?php echo $browse;?>" />
    <input type="hidden" name="boxchecked" />
    <input type="hidden" name="view" value="categories" />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="" />
    <input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
</form>
