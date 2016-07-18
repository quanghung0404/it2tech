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
<div class="panels panel-default es-polls" data-polls-vote data-id="<?php echo $poll->id;?>" data-element="<?php echo $poll->element;?>" data-uid="<?php echo $poll->uid;?>">
	<form class="form-horizontal" name="pollsForm" id="pollsForm" data-polls-form>
	<div class="h5 es-polls-question-title">
	    <?php echo $poll->title; ?>
        <?php if ($isExpired) { ?>
            <span class="fd-small"> (<?php echo JText::_('COM_EASYSOCIAL_POLLS_VOTE_EXPIRED'); ?>)</span>
        <?php } ?>

        <?php if ($canedit) { ?>
            <a href="javascript:void(0);" class="small hide" data-polls-edit-button> [<?php echo JText::_('COM_EASYSOCIAL_POLLS_EDIT'); ?>]</a>
        <?php } ?>
	</div>
	<div class="panel-body">


        <div class="es-polls-questions-list<?php echo (!$canvote) ? ' is-disabled' : ''; ?>" data-polls-questions-list>
            <?php if ($items) { ?>
                <?php foreach($items as $item) { ?>

                    <div class="es-polls-item es-checkbox"
                         data-vote-item
                         data-id="<?php echo $item->id; ?>"
                         data-count="<?php echo $item->count; ?>"
                    >
                        <input type="checkbox"
                               name="optionsRadios"
                               data-vote-item-option
                               data-id="<?php echo $item->id; ?>"
                               id="item-checkbox-<?php echo $item->id; ?>"
                               <?php echo ($item->voted) ? ' checked="checked"' : ''; ?>
                               <?php echo (! $canvote) ? ' disabled="disabled"' : ''; ?>
                        >

                        <label for="item-checkbox-<?php echo $item->id; ?>">
                            <?php echo $item->value; ?>

                            <div class="es-polls-progress progress" data-poll-bar-<?php echo $item->id; ?>>
                                <div class="progress-bar progress-bar-primary" <?php echo ($isvoted)? 'style="width: ' . $item->percentage . '%"' : ''; ?>></div>
                            </div>

                            <div class="es-polls-voters hide" data-poll-voters-<?php echo $item->id; ?>>
                            </div>

                            <a class="es-polls-count <?php echo ($isvoted)? '' : 'hide'; ?>" href="javascript:void(0);" data-poll-count-button>
                                <span data-poll-count-label-<?php echo $item->id; ?>><?php echo $item->count; ?></span> <?php echo JText::_('COM_EASYSOCIAL_POLLS_VOTES_COUNT'); ?>
                            </a>
                        </label>
                    </div>


                <?php } ?>
            <?php } ?>
        </div>

        <div class="pull-right text-right">
            <div class="alert hide" data-polls-notice></div>
        </div>

	</div>
	</form>
</div>
