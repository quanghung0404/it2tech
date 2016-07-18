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
<?php if (!$this->my->isSiteAdmin() && $this->my->getAccess()->get('events.moderate')) { ?>
<div class="alert alert-warning">
<?php echo JText::_('COM_EASYSOCIAL_EVENTS_SUBJECT_TO_APPROVAL'); ?>
</div>
<?php } ?>

<?php if (!empty($group)) { ?>
<h3 class="h3 well">
<?php echo JText::sprintf('COM_EASYSOCIAL_GROUPS_EVENTS_EVENT_FOR_GROUP', $this->html('html.group', $group)); ?>
</h3>
<?php } ?>

<form class="form-horizontal" enctype="multipart/form-data" method="post" action="<?php echo JRoute::_('index.php');?>" data-create-form>
    <div class="es-container es-events">

        <!-- Progress bar -->
        <div class="navbar es-stepbar">
            <div class="navbar-inner">
                <div class="navbar-collapse collapse">
                    <div class="media">
                        <div class="media-object pull-left">
                            <ul class="fd-nav">
                                <!-- Select a category -->
                                <li class="stepItem<?php echo $currentStep == SOCIAL_REGISTER_SELECTPROFILE_STEP ? ' active' : '';?><?php echo $currentStep > SOCIAL_REGISTER_SELECTPROFILE_STEP ||  $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"
                                    data-es-provide="popover"
                                    data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
                                    data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_PROGRESS_SELECT_CATEGORY', true);?>"
                                    data-content="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_PROGRESS_SELECT_CATEGORY_DESC', true);?>">

                                    <a href="<?php echo FRoute::events(array('layout' => 'create'));?>">
                                        <i class="fa fa-check"></i>
                                        <span class="step-number">0</span>
                                    </a>
                                </li>

                                <!-- Progress -->
                                <?php $counter = 1; ?>
                                <?php foreach ($steps as $step){ ?>
                                <?php
                                    $customClass = $step->sequence == $currentStep || $currentStep > $step->sequence || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active' : '';
                                    $customClass .= $step->sequence < $currentStep || $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? $customClass . ' past' : '';

                                    if ($stepSession->hasStepAccess($counter)) {
                                        // $link        = $step->sequence == $currentStep ? 'javascript:void(0);' : FRoute::registration(array('layout' => 'steps', 'step' => $step->sequence));
                                        $link = $step->sequence == $currentStep ? 'javascript:void(0);' : FRoute::events(array('layout' => 'steps', 'step' => $counter));
                                    } else {
                                        $link = 'javascript:void(0);';
                                    }
                                ?>
                                <li class="divider-vertical<?php echo $customClass;?>"></li>
                                <li class="stepItem<?php echo $customClass;?>"
                                    data-original-title="<?php echo JText::_($step->title, true);?>"
                                    data-content="<?php echo JText::_($step->description, true);?>"
                                    data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
                                    data-es-provide="popover">

                                    <a href="<?php echo $link;?>">
                                        <i class="fa fa-check"></i>
                                        <span class="step-number"><?php echo $counter; ?></span>
                                    </a>
                                </li>
                                <?php $counter++; ?>
                                <?php } ?>

                                <!-- Complete step -->
                                <li class="divider-vertical<?php echo $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"></li>
                                <li class="stepItem last<?php echo $currentStep == SOCIAL_REGISTER_COMPLETED_STEP ? ' active past' : '';?>"
                                    data-es-provide="popover"
                                    data-placement="<?php echo JFactory::getDocument()->getDirection() == 'rtl' ? 'left' : 'right';?>"
                                    data-original-title="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_PROGRESS_COMPLETED', true);?>"
                                    data-content="<?php echo JText::_('COM_EASYSOCIAL_EVENTS_CREATE_PROGRESS_COMPLETED_DESC', true);?>"
                                    >

                                    <a href="javascript:void(0);">
                                        <i class="fa fa-flag"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="media-body">
                            <div class="divider-vertical-last"></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Custom fields -->
        <?php if (!empty($fields)) { ?>
            <?php foreach ($fields as $field){ ?>
                <?php if( !empty( $field->output ) ) { ?>

                    <div data-create-field data-element="<?php echo $field->element; ?>" data-id="<?php echo $field->id; ?>" data-required="<?php echo $field->required; ?>" data-fieldname="<?php echo SOCIAL_FIELDS_PREFIX . $field->id; ?>">

                        <?php echo $field->output; ?>

                        <input type="hidden" name="cid[]" value="<?php echo $field->id;?>" />

                    </div>

                <?php } ?>
            <?php } ?>
        <?php } ?>

        <div class="form-group">
            <div class="col-sm-8 col-sm-offset-3 fd-small mt-20">
                <?php echo JText::_('COM_EASYSOCIAL_REGISTRATIONS_REQUIRED');?>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <?php if ($currentStep != 1){ ?>
            <button type="button" class="btn btn-es btn-medium pull-left" data-create-previous><?php echo JText::_('COM_EASYSOCIAL_PREVIOUS_BUTTON'); ?></button>
            <?php } ?>
            <button type="button" class="btn btn-es-primary btn-medium pull-right" data-create-submit><?php echo $currentIndex === $totalSteps || $totalSteps < 2 ? JText::_('COM_EASYSOCIAL_SUBMIT_BUTTON') : JText::_('COM_EASYSOCIAL_CONTINUE_BUTTON');?></button>
        </div>
    </div>

    <?php echo JHTML::_('form.token'); ?>
    <input type="hidden" name="currentStep" value="<?php echo $currentIndex; ?>" />
    <input type="hidden" name="controller" value="events" />
    <input type="hidden" name="task" value="saveStep" />
    <input type="hidden" name="option" value="com_easysocial" />
</form>

