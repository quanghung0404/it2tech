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
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="<?php echo FRoute::url(array('view' => 'regions', 'layout' => $layout)); ?>" method="post" name="adminForm" class="esForm" id="adminForm" data-table-grid>

    <div class="app-filter filter-bar form-inline">
        <div class="form-group">
            <?php echo $this->html('filter.search', $search); ?>
        </div>

        <div class="form-group">
            <strong><?php echo JText::_('COM_EASYSOCIAL_FILTER_BY'); ?> :</strong>
            <div>
                <?php echo $this->html('filter.published', 'state', $state); ?>
            </div>
        </div>

        <div class="form-group pull-right">
            <div><?php echo $this->html('filter.limit', $limit); ?></div>
        </div>
    </div>

    <div class="panel-table">
        <table class="app-table table table-eb table-striped">
            <thead>
                <tr>
                    <th width="1%" class="center">
                        <input type="checkbox" name="toggle" data-table-grid-checkall />
                    </th>

                    <th width="5%">
                        <?php echo $this->html('grid.sort', 'code', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_CODE'), $ordering, $direction); ?>
                    </th>

                    <th>
                        <?php echo $this->html('grid.sort', 'name', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_NAME'), $ordering, $direction); ?>
                    </th>

                    <?php if ($showOrdering) { ?>
                    <th class="center" width="10%">
                        <?php echo $this->html('grid.sort', 'ordering', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ORDERING'), $ordering, $direction); ?>
                    </th>
                    <?php } ?>

                    <th width="5%" class="center">
                        <?php echo $this->html('grid.sort', 'state', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_STATUS'), $ordering, $direction); ?>
                    </th>

                    <th width="5%" class="center">
                        <?php echo $this->html('grid.sort', 'id', JText::_('COM_EASYSOCIAL_TABLE_COLUMN_ID'), $ordering, $direction); ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($regions)) { ?>
                <?php $i = 0; ?>
                <?php foreach ($regions as $region) { ?>
                    <tr class="row<?php echo $i; ?> es-flyout" data-grid-row data-id="<?php echo $region->id; ?>">
                        <td class="center">
                            <?php echo $this->html('grid.id', $i, $region->id); ?>
                        </td>
                        <td class="center">
                            <?php echo $region->code; ?>
                        </td>
                        <td>
                            <a href="<?php echo FRoute::url(array('view' => 'regions', 'layout' => 'form', 'id' => $region->id)); ?>" class="btn btn-xs"><?php echo $region->name; ?></a><?php if ($childType) { ?> <a href="<?php echo FRoute::url(array('view' => 'regions', 'layout' => $childType, 'parent' => $region->uid)); ?>" class="btn btn-es btn-xs es-flyout-content"><i class="fa fa-globe"></i> <?php echo JText::_('COM_EASYSOCIAL_REGIONS_VIEW_CHILDREN_' . strtoupper($childType)); ?></a><?php } ?>
                        </td>
                        <?php if ($showOrdering) { ?>
                        <td class="order center">
                            <?php echo $this->html('grid.ordering', count($regions), ($i + 1), $ordering == 'ordering',  $region->ordering); ?>
                        </td>
                        <?php } ?>
                        <td class="center">
                            <?php echo $this->html('grid.published', $region, 'regions', 'state'); ?>
                        </td>
                        <td class="center">
                            <?php echo $region->id;?>
                        </td>
                    </tr>
                    <?php $i++; ?>
                <?php } ?>
                <?php } else { ?>
                    <tr class="is-empty">
                        <td colspan="<?php echo $showOrdering ? 6 : 5; ?>" class="center empty">
                            <?php echo JText::_('COM_EASYSOCIAL_REGIONS_NO_REGIONS_FOUND');?> <?php echo JText::sprintf('COM_EASYSOCIAL_REGIONS_TRY_INITIALISE_DATABASE', FRoute::url(array('view' => 'regions', 'layout' => 'init'))); ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?php echo $showOrdering ? 6 : 5; ?>" class="center">
                        <div class="footer-pagination"><?php echo $pagination->getListFooter(); ?></div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="ordering" value="<?php echo $ordering;?>" data-table-grid-ordering />
    <input type="hidden" name="direction" value="<?php echo $direction;?>" data-table-grid-direction />
    <input type="hidden" name="boxchecked" value="0" data-table-grid-box-checked />
    <input type="hidden" name="task" value="" data-table-grid-task />
    <input type="hidden" name="option" value="com_easysocial" />
    <input type="hidden" name="view" value="regions" />
    <input type="hidden" name="controller" value="regions" />
    <input type="hidden" name="layout" value="<?php echo $layout; ?>" />
    <input type="hidden" name="parent" value="<?php echo isset($parent) ? $parent->uid : 0; ?>" />
</form>
