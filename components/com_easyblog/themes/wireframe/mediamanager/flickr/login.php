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
<div class="eb-mm-folder" data-eb-mm-folder data-key="flickr">
    <div class="eb-composer-toolbar">
        <div>

            <div class="eb-composer-toolbar-set is-primary row-table" data-name="media-folder">
                <div class="col-cell cell-tight toolbar-left">
                    <div class="eb-composer-toolbar-group row-table">
                        <div class="eb-composer-toolbar-item col-cell is-button eb-mm-folder-back-button" data-eb-mm-folder-back-button>
                            <i class="fa fa-chevron-left"></i>
                            <span>Back</span>
                        </div>
                        <div class="eb-composer-toolbar-item col-cell">
                            <i class="fa fa-flickr"></i>
                            <span><?php echo JText::_('COM_EASYBLOG_MM_FLICKR');?></span>
                        </div>
                    </div>
                </div>

                <div class="col-cell toolbar-center">&nbsp;</div>
            </div>

        </div>
    </div>

    <div class="eb-composer-viewport" data-scrolly="y">
        <div class="eb-composer-viewport-content" data-scrolly-viewport>
            <div class="eb-mm-folder-content-panel pa-15">
                <div>
                    <p>
                        <strong><?php echo JText::_('COM_EASYBLOG_MM_AUTHORIZE_FLICKR_ACCOUNT');?></strong>
                    </p>
                    <p>
                        <?php echo JText::_('COM_EASYBLOG_MM_AUTHORIZE_FLICKR_ACCOUNT_INFO'); ?>
                    </p>
                    <button data-flickr-login class="btn btn-primary" data-url="<?php echo $login; ?>">
                        <i class="fa fa-flickr"></i>&nbsp; <?php echo JText::_('COM_EASYBLOG_MM_SIGN_IN_TO_FLICKR'); ?>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>