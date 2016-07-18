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
                <b><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_APPS');?></b>
                <p><?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_APPS_INFO');?></p>
            </div>

            <div class="panel-body">
                <div class="panel-table">
                    <table class="table table-panel table-label table-striped table-es table-hover">
                        <thead>
                            <tr>
                                <th width="1%">&nbsp;</th>
                                <th>
                                    <?php echo JText::_('COM_EASYSOCIAL_PROFILES_DEFAULT_APPS_TITLE'); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="<?php echo !$defaultGroups ? ' is-empty' : '';?>" data-profile-groups>
                            <?php foreach ($apps as $app) { ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="apps[]" 
                                        id="<?php echo $app->id;?>" 
                                        value="<?php echo $app->id;?>" 
                                        data-id="<?php echo $app->id;?>" 
                                        data-profiles-app-item-shadow 
                                        <?php echo (in_array($app->id, $selectedApps)) ? ' checked="checked"' : '';?>
                                    />
                                </td>
                                <td>
                                    <b><?php echo $app->title;?></b>
                                    <div><?php echo $app->getMeta()->desc;?></div>
                                    <label for="<?php echo $app->id;?>"></label>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>