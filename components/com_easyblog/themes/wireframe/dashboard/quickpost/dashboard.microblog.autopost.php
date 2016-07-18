<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');
?>
<?php
	if( $system->config->get( 'integrations_linkedin_centralized' ) || $system->config->get( 'integrations_facebook_centralized' ) || $system->config->get( 'integrations_twitter_centralized' )
	|| $system->config->get( 'integrations_linkedin_centralized_and_own') || $system->config->get( 'integrations_facebook_centralized_and_own') || $system->config->get( 'integrations_twitter_centralized_and_own' ) ) { ?>
<div class="social-publish microblog prel">
	<a href="javascript:void(0);" class="buttons"><?php echo JText::_( 'COM_EASYBLOG_SHARE_TO' ); ?><i></i></a>
	<div class="social-publish-options pabs">
	<?php if( $system->config->get( 'integrations_linkedin_centralized' ) || $system->config->get( 'integrations_facebook_centralized' ) || $system->config->get( 'integrations_twitter_centralized') )
	{
    ?>
		<div class="publish-centralized option">
			<b><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS');?></b>
			<div><?php echo JText::_( 'COM_EASYBLOG_CENTRALIZED_PUBLISH_OPTIONS_DESC' );?></div>
			<div>
				<span class="ui-highlighter publish-to in-block mrm">
					<?php if( $system->config->get( 'integrations_facebook_centralized' ) ){ ?>
	                <span class="ui-span<?php echo ( $system->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="centralized[]" value="facebook" id="centralized-facebook"<?php echo ( $system->config->get( 'integrations_facebook_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="centralized-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
	            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
            			</label>
	                </span>
	                <?php } ?>

	                <?php if( $system->config->get( 'integrations_twitter_centralized' ) ){ ?>
	                <span class="ui-span<?php echo ( $system->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="centralized[]" value="twitter" id="centralized-twitter"<?php echo ( $system->config->get( 'integrations_twitter_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="centralized-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
	            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
            			</label>
	                </span>
	                <?php }?>

	                <?php if( $system->config->get( 'integrations_linkedin_centralized' ) ){ ?>
	                <span class="ui-span<?php echo ( $system->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="centralized[]" value="linkedin" id="centralized-linkedin"<?php echo ( $system->config->get( 'integrations_linkedin_centralized_auto_post' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="centralized-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
	            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
            			</label>
	                </span>
	                <?php } ?>
	            </span>

				<div class="clear"></div>
			</div>
		</div>
	<?php
	}
	?>
		<div class="publish-personal option">
			<b><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS');?></b>
			<div><?php echo JText::_( 'COM_EASYBLOG_PERSONAL_PUBLISH_OPTIONS_DESC' );?></div>
			<div>
				<span class="ui-highlighter publish-to in-block mrm">
					<?php if(
							$this->acl->get('update_facebook') && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ||
							$this->acl->get('update_twitter') && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ||
							$this->acl->get('update_linkedin') && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' ) ){
					?>

					<?php if( $this->acl->get('update_facebook') && $system->config->get( 'integrations_facebook' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'FACEBOOK' ) ){?>
	                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'FACEBOOK' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="socialshare[]" value="facebook" id="socialshare-facebook"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'FACEBOOK' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="socialshare-facebook" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?>">
	            			<i class="ir ico-fb"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_FACEBOOK' ); ?></i>
            			</label>
	                </span>
	                <?php } ?>

	                <?php if( $this->acl->get('update_twitter') && $system->config->get( 'integrations_twitter' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'TWITTER' ) ){?>
	                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'TWITTER' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="socialshare[]" value="twitter" id="socialshare-twitter"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'TWITTER' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="socialshare-twitter" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?>">
	            			<i class="ir ico-tw"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_TWITTER' ); ?></i>
            			</label>
	                </span>
	                <?php } ?>

	                <?php if( $this->acl->get('update_linkedin') && $system->config->get( 'integrations_linkedin' ) && EasyBlogHelper::getHelper( 'SocialShare' )->isAssociated( $system->my->id , 'LINKEDIN' )  ){?>
	                <span class="ui-span<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'LINKEDIN' ) && empty($blog->id)) ? ' active' : '';?>">
	            		<input type="checkbox" name="socialshare[]" value="linkedin" id="socialshare-linkedin"<?php echo (EasyBlogHelper::getHelper( 'SocialShare' )->hasAutoPost( $system->my->id , 'LINKEDIN' ) && empty($blog->id)) ? ' checked="checked"' : '';?> onclick="eblog.dashboard.socialshare.setActive( this );" />
	            		<label for="socialshare-linkedin" title="<?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?>">
	            			<i class="ir ico-ln"><?php echo JText::_( 'COM_EASYBLOG_SOCIALSHARE_LINKEDIN' ); ?></i>
            			</label>
	                </span>
	                <?php } ?>

	                <?php } else { ?>
						<?php if(
									($this->acl->get('update_facebook') && $system->config->get( 'integrations_facebook_centralized_and_own' ) ) ||
									($this->acl->get('update_twitter') && $system->config->get( 'integrations_twitter_centralized_and_own' ) ) ||
									($this->acl->get('update_linkedin') && $system->config->get( 'integrations_linkedin_centralized_and_own' ) ) ){ ?>
	                		<div class="eblog-message"><a href="<?php echo EBR::_('index.php?option=com_easyblog&view=dashboard&layout=profile'); ?>#widget-profile-facebook" target="_blank"><?php echo JText::sprintf('COM_EASYBLOG_DASHBOARD_SETUP_SOCIAL_INTEGRATION_LINK_TEXT') ?></a></div>
	                	<?php } ?>
    				<?php } ?>
	            </span>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>
<?php } ?>
