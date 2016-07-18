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
            <?php echo $this->html('filter.lists', $filterType, 'filter_type', $type, JText::_( 'COM_EASYBLOG_SELECT_TYPE' ), ''); ?>
        </div>

        <div class="form-group pull-right">
            <?php echo $pagination->getLimitBox(); ?>
        </div>
    </div>

    <div class="panel-table">
        <table class="app-table table table-striped table-eb" data-table-grid>
            <thead>
                <tr>
                    <th width="1%" align="center" style="text-align: center;">
                        <input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
                    </th>
                    <th class="title" style="text-align: left;" width="30%">
                        <?php echo JText::_('COM_EASYBLOG_META_TITLE'); ?>
                    </th>
                    <th class="title" style="text-align: center;" width="5%">
                        <?php echo JText::_('COM_EASYBLOG_META_INDEXING'); ?>
                    </th>
                    <th width="10%" class="center">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TYPE');?>
                    </th>
                    <th width="1%" class="text-center">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_ID');?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if ($metas) { ?>
                    <?php $i = 0; ?>

                    <?php foreach ($metas as $row) { ?>
                    <tr>
                        <td class="center">
                            <?php
                                $checkedOut = ( $row->type == 'view') ? true : false;
                                echo JHTML::_('grid.id', $i , $row->id, $checkedOut);
                            ?>
                        </td>

                        <td align="left">
                            <div>
                                <a href="index.php?option=com_easyblog&view=metas&layout=form&id=<?php echo $row->id;?>"><?php echo $row->title; ?></a>
                            </div>

                            <p class="mt-5">
                                <?php echo $row->description ? $row->description : JText::_( 'COM_EASYBLOG_NOT_DEFINED' ); ?>
                            </p>

                            <?php if ($row->keywords) { ?>
                            <div><?php echo $row->keywords;?></div>
                            <?php } ?>
                        </td>

                        <td class="nowrap hidden-phone center">
                            <?php echo $this->html('grid.published', $row, 'meta', 'indexing', array('meta.addIndexing', 'meta.removeIndexing')); ?>
                        </td>

                        <td class="center">
                            <?php echo ucfirst($row->type); ?>
                        </td>

                        <td class="center">
                            <?php echo $row->id;?>
                        </td>

                    </tr>
                        <?php $i++; ?>
                    <?php }?>

                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            <?php echo JText::_('COM_EASYBLOG_NO_META_TAGS_INDEXED_YET');?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-center">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="option" value="com_easyblog" />
    <input type="hidden" name="view" value="metas" />
    <input type="hidden" name="task" value="" data-table-grid-task />
    <input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
    <input type="hidden" name="filter_order_Dir" value="" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
