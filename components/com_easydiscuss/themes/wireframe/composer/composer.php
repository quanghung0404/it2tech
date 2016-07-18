<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-reply-form t-lg-mt--lg <?php echo $composer->classname; ?>"
     data-ed-composer-wrapper
     <?php echo $editorId;?>
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
     >
    <div role="alert" class="o-alert o-alert--icon t-hidden" data-ed-composer-alert></div>

    <div class="ed-reply-form__bd">

        <?php if ($operation == 'replying' || $operation == 'editing') { ?>
                
            <div class="ed-reply-form__title">
                <div class="o-avatar o-avatar--sm t-lg-mr--sm">
                     <img alt="<?php echo $my->getName(); ?>" src="<?php echo $my->getAvatar(); ?>">
                </div>
                <?php echo ($operation == 'replying') ? JText::_('COM_EASYDISCUSS_ENTRY_YOUR_RESPONSE') : JText::_('COM_EASYDISCUSS_EDIT_YOUR_RESPONSE'); ?>
            </div>

        <?php } ?>

		<form data-ed-composer-form name="dc_submit" autocomplete="off" class="" action="<?php echo JRoute::_('index.php');?>" method="post">


            <?php if ($this->config->get('main_anonymous_posting')) { ?>
            <div class="o-checkbox t-lg-mb--lg small">
                <input id="anonymous" type="checkbox" name="anonymous" value="1"<?php echo $post->anonymous ? ' checked="checked"' : '';?> />
                <label for="anonymous">
                    <?php echo JText::_('COM_EASYDISCUSS_POST_ANONYMOUSLY');?>
                </label>
            </div>
            <?php } ?>

            <?php if (!$this->my->id) { ?>
			<div class="discuss-form">
				<div class="o-row">
                    <div class="o-col t-lg-pr--md t-xs-pr--no">
                        <div class="form-group">
                            <input type="text" name="poster_name" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_NAME'); ?>" />
                        </div>
                    </div>

                    <div class="o-col t-lg-pr--md t-xs-pr--no">
                        <div class="form-group">
                            <input type="text" name="poster_email" class="form-control" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_EMAIL'); ?>" />
                        </div>
                    </div>
				</div>
  			</div>
            <?php } ?>

	        <div class="ed-editor">
	            <div class="ed-editor-widget ed-editor-widget--no-pad">
					<?php echo $composer->renderEditor(); ?>

					<?php echo $composer->renderTabs(); ?>
	            </div>

	            <?php echo $this->output('site/composer/forms/location', array('post' => $post, 'operation' => $operation)); ?>	           

                <?php if ($captcha->enabled() && $operation != 'editing') { ?>
                <div class="ed-editor-widget t-lg-mt--xl" data-ed-captcha-form>
                    <div class="ed-editor-widget__title">
                        <?php echo JText::_('COM_EASYDISCUSS_CAPTCHA_TITLE'); ?>
                    </div>
                    <div class="ed-editor-widget__note">
                        <?php echo JText::_('COM_EASYDISCUSS_CAPTCHA_INFO'); ?>
                    </div>

                    <?php echo $captcha->html();?>
                </div>
                <?php } ?>

	            <div class="ed-editor__ft">

                    <?php if ($operation == 'editing') { ?>
                    <button type="button" class="btn btn-default pull-left" data-ed-reply-cancel><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL');?></button>
                    <?php } ?>

					<button type="button" class="btn btn-primary pull-right" data-ed-reply-submit>
                        <?php if ($operation == 'replying') { ?>
                            <?php echo JText::_('COM_EASYDISCUSS_BUTTON_SUBMIT_RESPONSE'); ?>
                        <?php } else { ?>
                            <?php echo JText::_('COM_EASYDISCUSS_BUTTON_UPDATE_REPLY'); ?>
                        <?php } ?>
                    </button>
	            </div>
	        </div>

            <?php if ($post->id) { ?>
            <input type="hidden" name="id" value="<?php echo $post->id;?>" />
            <?php } ?>

            <?php if ($operation == "editing") { ?>
            <input type="hidden" name="seq" value="<?php echo $post->seq; ?>" />
            <?php } ?>

			<input type="hidden" name="parent_id" value="<?php echo $parent->id; ?>" />
	    </form>
    </div>
</div>
