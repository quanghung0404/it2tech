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
            <?php echo $filterList; ?>
        </div>

        <div class="form-group pull-right">
            <?php echo $pagination->getLimitBox(); ?>
        </div>
    </div>

    <div class="panel-table">
        <table class="app-table table table-striped table-eb table-hover" data-table-grid>
            <thead>
                <tr>
                    <th width="1%" class="center">
                        <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                    </th>

                    <?php if( $filter != 'site' ){ ?>
                    <th>
                        <?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_COLUMN_' . strtoupper($filter)); ?>
                    </th>
                    <?php } ?>

                    <th width="10%">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_EMAIL'); ?>
                    </th>
                    <th width="20%">
                        <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_NAME'); ?>
                    </th>
                    <th width="5%" class="center nowrap">
                        <?php echo JText::_('COM_EASYBLOG_SUBSCRIPTION_DATE'); ?>
                    </th>
                    <th width="1%" class="center nowrap">
                        <?php echo JText::_('COM_EASYBLOG_ID'); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php if( $subscriptions ){ ?>
                <?php $i = 0; ?>
                <?php foreach( $subscriptions as $row ){ ?>
                <tr>
                    <td class="center">
                        <?php echo $this->html('grid.id', $i++, $row->id); ?>
                    </td>

                    <?php if( $filter != 'site' ){ ?>
                    <td>
                        <?php echo $row->bname;?><?php echo ($filter == 'blogger') ? ' (' . $row->busername. ')' : ''; ?>
                    </td>
                    <?php } ?>

                    <td>
                        <?php echo $row->email;?>
                    </td>

                    <td>
                        <?php echo (empty($row->name)) ? $row->fullname :  $row->name;?>
                    </td>

                    <td class="center">
                        <?php echo $row->created; ?>
                    </td>

                    <td class="center">
                        <?php echo $row->id;?>
                    </td>
                </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" align="center" class="empty">
                        <?php echo JText::_('COM_EASYBLOG_NO_SUBSCRIPTION_FOUND');?>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="11">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

<?php echo $this->html('form.action'); ?>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="view" value="subscriptions" />
<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
<input type="hidden" name="filter_order_Dir" value="" />
</form>
