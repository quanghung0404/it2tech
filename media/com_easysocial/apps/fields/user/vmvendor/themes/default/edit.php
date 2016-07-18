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
<div class="form-group <?php echo !empty( $error ) ? 'has-error' : '';?>"
    data-field
    data-field-<?php echo $field->id; ?>
    data-edit-field
    data-edit-field-<?php echo $field->id; ?>
    <?php if( !isset( $options['check'] ) || $options['check'] !== false ) { ?>data-check<?php } ?>
>

    <label class="col-sm-3 control-label" for="es-fields-<?php echo $field->id;?>">
        <?php if( ( isset( $options['required'] ) && $options['required'] ) || ( !isset( $options['required'] ) && $field->isRequired() ) ){ ?>
        <span><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_REQUIRED_SYMBOL' );?></span>
        <?php } ?>

        <?php echo JText::_('Shop Name');?>
    </label>


    <div class="col-xs-12 col-sm-8 data" data-content>
        <input type="text" class="form-control" placeholder="Enter shop name" />
    </div>

    <?php if( !isset( $options['error'] ) || $options['error'] !== false ) {
        echo $this->includeTemplate( 'site/fields/error' );
    } ?>

    <?php if( $params->get( 'display_description' ) ) {
        echo $this->includeTemplate( 'site/fields/description' );
    } ?>
</div>


<div class="form-group <?php echo !empty( $error ) ? 'has-error' : '';?>"
    data-field
    data-field-<?php echo $field->id; ?>
    data-edit-field
    data-edit-field-<?php echo $field->id; ?>
    <?php if( !isset( $options['check'] ) || $options['check'] !== false ) { ?>data-check<?php } ?>
>

    <label class="col-sm-3 control-label" for="es-fields-<?php echo $field->id;?>">
        <?php if( ( isset( $options['required'] ) && $options['required'] ) || ( !isset( $options['required'] ) && $field->isRequired() ) ){ ?>
        <span><?php echo JText::_( 'COM_EASYSOCIAL_REGISTRATION_REQUIRED_SYMBOL' );?></span>
        <?php } ?>

        <?php echo JText::_('Shop Name');?>
    </label>


    <div class="col-xs-12 col-sm-8 data" data-content>
        <input type="text" class="form-control" placeholder="Enter shop name" />
    </div>

    <?php if( !isset( $options['error'] ) || $options['error'] !== false ) {
        echo $this->includeTemplate( 'site/fields/error' );
    } ?>

    <?php if( $params->get( 'display_description' ) ) {
        echo $this->includeTemplate( 'site/fields/description' );
    } ?>
</div>
