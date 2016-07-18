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
<div data-field-address class="data-field-address">
<?php if ($params->get('use_maps')) { ?>
    <?php $hideLocationRemoveButton = (JFactory::getApplication()->isAdmin() && ($this->input->get('view', '', 'cmd') == 'profiles')) ? true : false; ?>
    <div class="es-locations" data-location-base>
        <div class="es-location-map" data-location-map>
            <div>
                <div class="es-location-map-image" data-location-map-image></div>
                <div class="es-location-map-actions">
                    <button class="btn btn-es btn-sm es-location-detect-button" type="button" data-location-detect><i class="fa fa-flash"></i> <?php echo JText::_('COM_EASYSOCIAL_DETECT_MY_LOCATION', true); ?></button>
                </div>
            </div>
        </div>

        <div class="es-location-form es-field-location-form has-border" data-location-form>
            <div class="es-location-textbox" data-location-textbox data-language="<?php echo FD::config()->get('general.location.language'); ?>">
                <input type="text" class="input-sm form-control" placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_SET_A_LOCATION'); ?>" autocomplete="off" data-location-textfield disabled <?php $fulladdress = !empty($value->address) ? $value->address : $value->toString(); if (!empty($fulladdress)) { ?>value="<?php echo $fulladdress; ?>"<?php } ?> />
                <div class="es-location-autocomplete has-shadow is-sticky" data-location-autocomplete>
                    <b><b></b></b>
                    <div class="es-location-suggestions" data-location-suggestions>
                    </div>
                </div>
            </div>
            <div class="es-location-buttons<?php echo ($hideLocationRemoveButton) ? ' hide' : '';?>">
                <i class="fd-loading"></i>
                <a class="es-location-remove-button" href="javascript: void(0);" data-location-remove><i class="fa fa-remove"></i></a>
            </div>
        </div>

        <input type="hidden" name="<?php echo $inputName; ?>" data-location-source value="<?php echo FD::string()->escape($value->toJson()); ?>" />
    </div>
<?php } else { ?>
    <ul class="input-vertical form-inline list-unstyled">
        <?php if ($params->get('show_address1')) { ?>
        <li>
            <input type="text" class="form-control input-sm validation keyup length-4"
            placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ADDRESS1_PLACEHOLDER', true);?>"
            name="<?php echo $inputName;?>[address1]"
            value="<?php echo FD::string()->escape($value->address1);?>"
            data-field-address-address1
            />
        </li>
        <?php } ?>
        <?php if ($params->get('show_address2')) { ?>
        <li>
            <input type="text" class="form-control input-sm validation keyup length-4"
            placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ADDRESS2_PLACEHOLDER', true);?>"
            name="<?php echo $inputName;?>[address2]"
            value="<?php echo FD::string()->escape($value->address2);?>"
            data-field-address-address2
            />
        </li>
        <?php } ?>

        <?php if ($params->get('show_city') && $params->get('show_zip')) { ?>
        <li class="mb-5">
            <input type="text" class="form-control input-sm validation keyup length-4"
            placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_CITY_PLACEHOLDER', true);?>"
            style="width:60%;margin-bottom:4px;"
            name="<?php echo $inputName;?>[city]"
            value="<?php echo FD::string()->escape($value->city);?>"
            data-field-address-city
            />

            <input type="text" class="form-control input-sm validation keyup length-4"
            placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ZIP_PLACEHOLDER', true);?>"
            style="width:38%;margin-bottom:4px;float:right;"
            name="<?php echo $inputName;?>[zip]"
            value="<?php echo FD::string()->escape($value->zip);?>"
            data-field-address-zip
            />
        </li>
        <?php } ?>

        <?php if ($params->get('show_city') xor $params->get('show_zip')) { ?>
        <li class="mb-5">
            <?php if ($params->get('show_city')) { ?>
            <input type="text" class="form-control input-sm validation keyup length-4"
            placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_CITY_PLACEHOLDER', true);?>"
            style="width:100%;margin-bottom:4px;"
            name="<?php echo $inputName;?>[city]"
            value="<?php echo FD::string()->escape($value->city);?>"
            data-field-address-city
            />
            <?php } ?>

            <?php if ($params->get('show_zip')) { ?>
                <input type="text" class="form-control input-sm validation keyup length-4"
                placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_ZIP_PLACEHOLDER', true);?>"
                style="width:100%;margin-bottom:4px;"
                name="<?php echo $inputName;?>[zip]"
                value="<?php echo FD::string()->escape($value->zip);?>"
                data-field-address-zip
                />
            <?php } ?>
        </li>
        <?php } ?>


        <?php if ($params->get('show_state') && $params->get('show_country')) { ?>
        <li class="mb-0">
            <select class="form-control input-sm" name="<?php echo $inputName;?>[country]"
            style="width:60%;margin-bottom:4px;"
            data-field-address-country>
                <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_SELECT_A_COUNTRY'); ?></option>
                <?php foreach($countries as $code => $title){ ?>
				<option value="<?php echo $title;?>"<?php echo $title == $value->country ? ' selected="selected"' : '';?>><?php echo $title;?></option>
                <?php } ?>
            </select>

            <?php if ($params->get('data_source') === 'regions') { ?>
                <select
                    class="form-control input-sm"
                    style="width:38%;margin-bottom:4px;float:right;"
                    name="<?php echo $inputName;?>[state]"
                    data-field-address-state
                >
                <?php if (!empty($states)) { ?>
                <?php foreach ($states as $code => $title) { ?>
                    <option value="<?php echo $title; ?>" <?php if ($value->state === $title) { ?>selected="selected"<?php } ?>><?php echo $title; ?></option>
                <?php } ?>
                <?php } else { ?>
                    <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER'); ?></option>
                    <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_COUNTRY_FIRST'); ?></option>
                <?php } ?>
                </select>
            <?php } else { ?>
                <input
                    type="text"
                    class="form-control input-sm validation keyup length-4"
                    placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER', true);?>"
                    style="width:49%;margin-bottom:4px;float:right;"
                    name="<?php echo $inputName;?>[state]"
                    value="<?php echo FD::string()->escape($value->state);?>"
                    data-field-address-state
                />
            <?php } ?>
        </li>
        <?php } ?>

        <?php if ($params->get('show_state') xor $params->get('show_country')) { ?>
        <li class="mb-0">
            <?php if ($params->get('show_country')) { ?>
                <select class="form-control input-sm" name="<?php echo $inputName;?>[country]"
                style="width:100%;margin-bottom:4px;"
                data-field-address-country>
                    <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_SELECT_A_COUNTRY'); ?></option>
                    <?php foreach($countries as $code => $title){ ?>
					<option value="<?php echo $title;?>"<?php echo $title == $value->country ? ' selected="selected"' : '';?>><?php echo $title;?></option>
                    <?php } ?>
                </select>
            <?php } ?>

            <?php if ($params->get('show_state')) { ?>
                <?php if ($params->get('data_source') === 'regions') { ?>
                    <select
                        class="form-control input-sm"
                        style="width:100%;margin-bottom:4px;"
                        name="<?php echo $inputName;?>[state]"
                        data-field-address-state
                    >
                    <?php if (!empty($states)) { ?>
                    <?php foreach ($states as $code => $title) { ?>
                        <option value="<?php echo $title; ?>" <?php if ($value->state === $state) { ?>selected="selected"<?php } ?>><?php echo $title; ?></option>
                    <?php } ?>
                    <?php } else { ?>
                        <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER'); ?></option>
                        <option value=""><?php echo JText::_('PLG_FIELDS_ADDRESS_PLEASE_SELECT_A_COUNTRY_FIRST'); ?></option>
                    <?php } ?>
                    </select>
                <?php } else { ?>
                    <input
                        type="text"
                        class="form-control input-sm validation keyup length-4"
                        placeholder="<?php echo JText::_('PLG_FIELDS_ADDRESS_STATE_PLACEHOLDER', true);?>"
                        style="width:100%;margin-bottom:4px;"
                        name="<?php echo $inputName;?>[state]"
                        value="<?php echo FD::string()->escape($value->state);?>"
                        data-field-address-state
                    />
                <?php } ?>
            <?php } ?>
        </li>
        <?php } ?>
    </ul>

<?php } ?>
</div>
