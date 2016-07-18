<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
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
            <?php echo $filterGroups; ?>
        </div>

        <div class="form-group pull-right">
            <?php echo $pagination->getLimitBox(); ?>
        </div>
    </div>

    <div class="panel-table">
        <table class="app-table table table-striped table-eb" data-table-grid>
            <thead>
                <th width="5">
                    <input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" />
                </th>

                <th style="text-align: left;">
                    <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TITLE');?>
                </th>

                <th width="5%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
                </th>
                <th width="5%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_REQUIRED'); ?>
                </th>
                <th width="15%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_FIELD_GROUP'); ?>
                </th>
                <th width="15%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_FIELD_TYPE'); ?>
                </th>
                <th width="1%" class="center">
                    <?php echo JText::_('COM_EASYBLOG_ID');?>
                </th>
            </thead>
            <tbody>
                <?php if ($fields) { ?>
                    <?php $i = 0; ?>
                    <?php foreach ($fields as $field) { ?>
                    <tr>
                        <td width="1%" class="center nowrap">
                            <?php echo $this->html('grid.id', $i, $field->id);?>
                        </td>
                        <td>
                            <a href="index.php?option=com_easyblog&view=fields&layout=form&id=<?php echo $field->id;?>"><?php echo JText::_($field->title);?></a>
                        </td>

                        <td class="center nowrap">
                            <?php echo $this->html('grid.published', $field, 'fields', 'state'); ?>
                        </td>

                        <td class="center nowrap">
                            <?php echo $this->html('grid.published', $field, 'fields', 'required', array('fields.setRequired', 'fields.removeRequired')); ?>
                        </td>

                        <td class="center">
                            <?php echo $field->getGroupTitle(); ?>
                        </td>

                        <td class="center">
                            <span class="label label-primary"><?php echo ucfirst(JText::_($field->type)); ?></span>
                        </td>

                        <td class="center">
                            <?php echo $field->id; ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="7" class="empty">
                            <?php echo JText::_('COM_EASYBLOG_FIELDS_NO_FIELDS_CREATED_YET');?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="view" value="fields" />
    <input type="hidden" name="layout" value="fields" />
</form>
