<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div id="fd" class="es mod-es-dating-search module-social<?php echo $suffix;?> es-responsive" data-mod-dating-search>
<form method="get" action="<?php echo JRoute::_('index.php'); ?>" name="frmSearch" class="form-horizontal es-dating-search-form es-responsive">
	<?php if ($params->get('searchname', 1) && $config->get('users.displayName') == 'realname' && isset($modFields['joomla_fullname'])) {
		$field = $modFields['joomla_fullname']; ?>
	<div data-mod-dating-search-item class="form-group form-group-sm">
		<label class="control-label col-sm-3"><?php echo JText::_($field->title);?></label>
        <div class="col-sm-9">
            <input class="form-control input-sm" type="text" value="<?php echo (isset($modUserData[$field->element]['condition'])) ? $modUserData[$field->element]['condition'] : ''?>"
                placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_DATING_SEARCH_ENTER_SOME_VALUE' , true );?>" name="conditions[]" data-condition
            />
        </div>
		<input class="form-control input-sm" type="hidden" value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>" name="criterias[]" data-criterias />
		<input class="form-control input-sm" type="hidden" value="name" name="datakeys[]" data-datakeys />
		<input class="form-control input-sm" type="hidden" value="contain" name="operators[]" data-operators />

	</div>
	<?php } ?>

    <?php if ($params->get('searchname', 1) && $config->get('users.displayName') == 'username' && isset($modFields['joomla_username'])) {
        $field = $modFields['joomla_username']; ?>
    <div data-mod-dating-search-item class="form-group form-group-sm">
        <label class="control-label col-sm-3"><?php echo JText::_($field->title);?></label>
        <div class="col-sm-9">
            <input class="form-control input-sm" type="text" value="<?php echo (isset($modUserData[$field->element]['condition'])) ? $modUserData[$field->element]['condition'] : ''?>"
                placeholder="<?php echo JText::_( 'MOD_EASYSOCIAL_DATING_SEARCH_ENTER_SOME_VALUE' , true );?>" name="conditions[]" data-condition
            />
        </div>
        <input class="form-control input-sm" type="hidden" value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>" name="criterias[]" data-criterias />
        <input class="form-control input-sm" type="hidden" value="" name="datakeys[]" data-datakeys />
        <input class="form-control input-sm" type="hidden" value="equal" name="operators[]" data-operators />

    </div>
    <?php } ?>

	<?php if ($params->get('searchgender', 1) && isset($modFields['gender'])) {
		$field = $modFields['gender'];
		$userData = (isset($modUserData[$field->element]['condition'])) ? $modUserData[$field->element]['condition'] : '';
	?>

	<div data-mod-dating-search-item class="form-group form-group-sm">
		<label class="control-label col-sm-3"><?php echo JText::_($field->title);?></label>
        <div class="col-sm-9">
            <label class="radio-inline">
                <input type="radio" name="search-gender" value="1" <?php echo ($userData == "1") ? 'checked="checked"' : ''; ?> data-gender-radio /> <?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_GENDER_MEN'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="search-gender" value="2" <?php echo ($userData == "2") ? 'checked="checked"' : ''; ?> data-gender-radio /> <?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_GENDER_WOMEN'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="search-gender" value="3" <?php echo ($userData == "3") ? 'checked="checked"' : ''; ?> data-gender-radio /> <?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_GENDER_OTHERS'); ?>
            </label>
        </div>
		<input class="form-control input-sm" type="hidden" value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>" name="criterias[]" data-criterias />
		<input class="form-control input-sm" type="hidden" value="" name="datakeys[]" data-datakeys />
		<input class="form-control input-sm" type="hidden" value="equal" name="operators[]" data-operators />
        <input class="form-control input-sm" type="hidden" value="<?php echo $userData;?>" name="conditions[]" data-condition />

	</div>
	<?php } ?>

	<?php if ($params->get('searchage', 1) && isset($modFields['birthday'])) {
		$field = $modFields['birthday'];

		$start = '';
		$end = '';
		$dates = (isset($modUserData[$field->element]['condition'])) ? $modUserData[$field->element]['condition'] : '';
		if ($dates) {
			$userDates = explode('|', $dates );

			$start = $userDates[0];
			$end = (isset($userDates[1])) ? $userDates[1] : '';
		}
	?>
	<div data-mod-dating-search-item class="form-group form-group-sm">
		<label class="control-label col-sm-3"><?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_AGE'); ?>:</label>
        <div class="es-control-input col-sm-9">
            <div class="input-col">
                <input class="input-sm" type="number" min="1" max="150" placeholder="<?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_AGE_FROM'); ?>" value="<?php echo $start;?>" name="frmStart" data-start />
            </div>
            <div class="input-col">
                <input class="input-sm" type="number" min="1" max="150" placeholder="<?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_AGE_TO'); ?>" value="<?php echo $end;?>" name="frmEnd" data-end />
            </div>
        </div>
		<input class="form-control input-sm" type="hidden" value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>" name="criterias[]" data-criterias />
		<input class="form-control input-sm" type="hidden" value="" name="datakeys[]" data-datakeys />
		<input class="form-control input-sm" type="hidden" value="between" name="operators[]" data-operators />
		<input class="form-control input-sm" type="hidden" value="<?php echo $dates;?>" name="conditions[]" data-condition />

	</div>
	<?php } ?>

	<?php if ($params->get('searchdistance', 1) && isset($modFields['address'])) {
		$field = $modFields['address']; ?>
	<div data-mod-dating-search-item class="form-group form-group-sm mod-search-distance">
		<label class="control-label col-sm-3"><?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_DISTANCE_WITHIN_' . $searchUnit); ?>:</label>
        <div class="col-sm-9">
            <div>
                <input class="form-control input-sm" type="number"
                       placeholder="<?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_DISTANCE_ENTER_VALUE');?>"
                       value="<?php echo (isset($modUserData[$field->element]['distance'])) ? $modUserData[$field->element]['distance'] : ''?>"
                       name="frmDistance"
                       data-distance />
            </div>


            <input class="form-control input-sm" type="hidden" value="<?php echo $field->unique_key;?>|<?php echo $field->element;?>" name="criterias[]" data-criterias />
            <input class="form-control input-sm" type="hidden" value="distance" name="datakeys[]" data-datakeys />
            <input type="hidden" name="operators[]" value="lessequal" data-operators>

            <input class="form-control input-sm<?php echo (isset($modUserData[$field->element]['address'])) ? '' : ' hide'; ?>"
                   type="hidden" value="<?php echo (isset($modUserData[$field->element]['address'])) ? $modUserData[$field->element]['address'] : ''?>" name="frmAddress" data-address />
            <input class="form-control input-sm" type="hidden" value="<?php echo (isset($modUserData[$field->element]['latitude'])) ? $modUserData[$field->element]['latitude'] : ''?>" name="frmLatitude" data-latitude />
            <input class="form-control input-sm" type="hidden" value="<?php echo (isset($modUserData[$field->element]['longitude'])) ? $modUserData[$field->element]['longitude'] : ''?>" name="frmLongitude" data-longitude />

            <?php $tmpCondition = isset($modUserData[$field->element]['condition']) ? $modUserData[$field->element]['condition'] : ''; ?>
            <input data-condition type="hidden" class="form-control input-sm" name="conditions[]" value="<?php echo $modules->html('string.escape', $tmpCondition);?>" />


            <div data-location-label class=" mt-10 full-width">

                <div class="input-group input-group-sm mod-search-distance__location-label-input-group">
                    <input type="text" class="form-control input-sm" placeholder="<?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_DISTANCE_ENTER_LOCATION'); ?>"
                           autocomplete="off" data-location-textfield  value=""/>
                    <span class="input-group-btn mod-search-distance__location-label-input-group-btn">
                        <button class="btn btn-default" type="button" data-location-detect>
                            <i class="fa fa-flash" data-loaction-icon></i> <span class="mod-search-distance__location-detect-text"><?php echo JText::_('MOD_EASYSOCIAL_DATING_SEARCH_DETECT_LOCATION'); ?></span>
                        </button>
                    </span>
                </div>
                <div class="es-location-autocomplete has-shadow is-sticky" data-location-autocomplete>
                    <b><b></b></b>
                    <div class="es-location-suggestions" data-location-suggestions>
                    </div>
                </div>
            </div>
        </div>



	</div>
	<?php } ?>

	<div class="mb-10 fd-cf">
		<button class="btn btn-es-primary pull-right" type="submit" data-advsearch-button><?php echo JText::_( 'MOD_EASYSOCIAL_DATING_SEARCH_SEARCH_BUTTON' );?></button>
	</div>

	<?php echo $modules->html( 'form.token' ); ?>
	<?php echo $modules->html( 'form.itemid' ); ?>
	<input type="hidden" name="option" value="com_easysocial" />
	<input type="hidden" name="view" value="users" />
	<input type="hidden" name="layout" value="search" />
</form>
</div>
