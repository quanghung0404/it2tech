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
<div class="tab-box tab-box-alt">
    <div class="tab-content tab-content-side">
        <div class="row">
            <div class="col-lg-7">
                <div class="panel">
                    <div class="panel-body">
                        <?php $x = 0;?>
                        <?php foreach( $steps as $step ){ ?>
                            <div id="step-<?php echo $step->id;?>" class="tab-pane<?php echo $x == 0 ? ' active' : '';?>" data-profile-adminedit-fields-content data-stepcontent data-for="<?php echo $step->id; ?>">
                            <?php foreach( $step->fields as $field ) { ?>
                                <?php if( !empty( $field->output ) ) { ?>
                                        <div data-profile-adminedit-fields-item data-element="<?php echo $field->element; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>">
                                            <?php echo $field->output; ?>
                                        </div>
                                <?php } ?>
                            <?php } ?>
                            </div>
                            <?php $x++; ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>