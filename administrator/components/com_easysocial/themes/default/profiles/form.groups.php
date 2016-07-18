<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="row">
    <div class="col-md-8">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_INFO');?></p>
            </div>

            <div class="panel-body">
                <table class="table table-panel table-striped table-es table-hover">
                    <thead>
                        <tr>
                            <th>
                                <?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_TITLE'); ?>
                            </th>
                            <th width="1%">
                                &nbsp;
                            </th>
                        </tr>
                    </thead>
                    <tbody class="<?php echo !$defaultGroups ? ' is-empty' : '';?>" data-profile-groups>
                        
                        <?php if ($defaultGroups) { ?>
                            <?php echo $this->output('admin/profiles/form.groups.item', array('groups' => $defaultGroups)); ?>
                        <?php } ?>

                        <tr data-groups-empty>
                            <td colspan="3" class="empty">
                                <?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_EMPTY');?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel">
            <div class="panel-head">
                <b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_SEARCH');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_SEARCH_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="textboxlist" data-groups-suggest>
                    <input type="text" class="form-control input-sm textboxlist-textField" autocomplete="off" placeholder="<?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_GROUPS_TYPE');?>" data-textboxlist-textField disabled />
                </div>

                <hr />

                <div>
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm" data-insert-groups><?php echo JText::_('COM_EASYSOCIAL_INSERT_GROUPS');?></a>
                </div>
            </div>
        </div>
    </div>

</div>