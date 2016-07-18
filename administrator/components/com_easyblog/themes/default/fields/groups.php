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
<form action="index.php?option=com_easyblog" method="post" name="adminForm" id="adminForm" data-grid-eb>


    <div class="app-filter filter-bar form-inline">
        <div class="form-group">
            <?php echo $this->html('filter.search', $search); ?>
        </div>

        <div class="form-group">
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

                <th width="15%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_PUBLISHED'); ?>
                </th>
                <th width="15%" class="center nowrap">
                    <?php echo JText::_('COM_EASYBLOG_TABLE_COLUMN_TOTAL_FIELDS'); ?>
                </th>
                <th width="1%" class="center">
                    <?php echo JText::_('COM_EASYBLOG_ID');?>
                </th>
            </thead>
            <tbody>
                <?php if ($groups) { ?>
                    <?php $i = 0; ?>
                    <?php foreach ($groups as $group) { ?>
                    <tr>
                        <td width="1%" class="center nowrap">
                            <?php echo $this->html('grid.id', $i, $group->id);?>
                        </td>
                        <td>
                            <a href="index.php?option=com_easyblog&view=fields&layout=groupForm&id=<?php echo $group->id;?>"><?php echo $group->title;?></a>
                        </td>

                        <td class="center nowrap">
                            <?php echo $this->html('grid.published', $group, 'fields', 'state', array('fields.publishgroup', 'fields.unpublishgroup')); ?>
                        </td>

                        <td class="center">
                            <?php echo $group->getTotalFields(); ?>
                        </td>

                        <td class="center">
                            <?php echo $group->id; ?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="5" class="empty">
                            <?php echo JText::_('COM_EASYBLOG_FIELDS_NO_FIELDGROUPS_CREATED_YET');?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                        <?php echo $pagination->getListFooter(); ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php echo $this->html('form.action'); ?>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="view" value="fields" />
    <input type="hidden" name="layout" value="groups" />
</form>
