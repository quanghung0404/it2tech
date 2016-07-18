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
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<dialog>
    <width>450</width>
    <height>200</height>
    <selectors type="json">
    {
        "{submitButton}": "[data-submit-button]",
        "{cancelButton}": "[data-cancel-button]",
        "{form}": "[data-switch-category-form]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{cancelButton} click": function() {
            this.parent.close();
        },

        "{submitButton} click": function() {
            this.form().submit();
        }
    }
    </bindings>
    <title><?php echo JText::_('COM_EASYSOCIAL_EVENTS_SWITCH_CATEGORY_DIALOG_TITLE'); ?></title>
    <content>
        <div class="clearfix">
            <form name="switchCategory" method="post" action="index.php" data-switch-category-form>
            <p>
                <?php echo JText::_( 'COM_EASYSOCIAL_EVENTS_SWITCH_CATEGORY_DIALOG_DESC' );?>
            </p>

            <div class="form-group">
                <label for="total" class="col-md-3 fd-small"><?php echo JText::_( 'COM_EASYSOCIAL_EVENTS_SELECT_CATEGORY' );?></label>
                <div class="col-md-9">
                    <select class="form-control input-sm" autocomplete="off" name="category" id="category">
                        <?php foreach ($categories as $category) { ?>
                        <option value="<?php echo $category->id; ?>"><?php echo $category->get('title'); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div>
                <span class="label label-danger"><?php echo JText::_( 'COM_EASYSOCIAL_FOOTPRINT_IMPORTANT' );?></span>
                <br /><?php echo JText::_( 'COM_EASYSOCIAL_EVENTS_SWITCH_CATEGORY_DIALOG_FOOTNOTE' );?>
            </div>
            <input type="hidden" name="option" value="com_easysocial" />
            <input type="hidden" name="controller" value="events" />
            <input type="hidden" name="task" value="switchCategory" />
            <?php echo JHTML::_('form.token'); ?>

            <?php foreach( $ids as $id ){ ?>
            <input type="hidden" name="cid[]" value="<?php echo $id; ?>" />
            <?php } ?>
            </form>
        </div>
    </content>
    <buttons>
        <button data-cancel-button type="button" class="btn btn-es btn-sm"><?php echo JText::_('COM_EASYSOCIAL_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-es-primary btn-sm"><?php echo JText::_('COM_EASYSOCIAL_SWITCH_CATEGORY_BUTTON'); ?></button>
    </buttons>
</dialog>
