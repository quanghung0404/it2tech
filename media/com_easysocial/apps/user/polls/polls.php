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

FD::import( 'admin:/includes/apps/apps' );

require_once( dirname( __FILE__ ) . '/helper.php' );

class SocialUserAppPolls extends SocialAppItem
{

    /**
     * Processes a before saved story.
     *
     * @since   1.4
     * @access  public
     * @param   string
     * @return
     */
    public function onBeforeStorySave(&$template, &$stream, &$content)
    {
        $params = $this->getParams();

        // Determine if we should attach ourselves here.
        if (!$params->get('story_polls', true)) {
            return;
        }

        $in = FD::input();

        $title = $in->getString('polls_title', '');
        $multiple = $in->getInt('polls_multiple', 0);
        $expiry = $in->getString('polls_expirydate', '');
        $items = $in->get('polls_items', array(), 'array');
        $element = $in->getString('polls_element', 'stream');
        $uid = $in->getInt('polls_uid', 0);

        if (empty($title) || empty($items)) {
            return;
        }
        // var_dump($items);exit;

        $my = FD::user();

        $poll = FD::get('Polls');
        $polltmpl = $poll->getTemplate();

        $polltmpl->setTitle($title);
        $polltmpl->setCreator($my->id);
        $polltmpl->setContext($uid, $element);
        $polltmpl->setMultiple($multiple);
        if ($expiry) {
            $polltmpl->setExpiry($expiry);
        }

        if ($items) {
            foreach($items as $itemOption) {
                $polltmpl->addOption($itemOption);
            }
        }

        // polls creation option
        $saveOptions = array('createStream' => false);

        $pollTbl = $poll->create($polltmpl, $saveOptions);

        $template->context_type = SOCIAL_TYPE_POLLS;
        $template->context_id = $pollTbl->id;

        $params = array(
            'poll' => $pollTbl
        );

        $template->setParams(FD::json()->encode($params));
    }

	/**
	 * Processes a saved story.
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(&$stream, &$streamItem, &$template)
	{
		$params = $this->getParams();

		// Determine if we should attach ourselves here.
		if (!$params->get('story_polls', true)) {
			return;
		}

        if ( ($streamItem->context_type != SOCIAL_TYPE_POLLS) || (! $streamItem->context_id)) {
            return;
        }

        //load poll item and assign uid.
        $poll = FD::table('Polls');
        $state = $poll->load($streamItem->context_id);

        if ($state) {
            $poll->uid = $streamItem->uid;
            $poll->store();

            // reset the stream privacy to use polls.view privacy instead of story.view
            // $poll->updateStreamPrivacy($streamItem->uid);
        }

		return true;
	}


	/**
	 * Responsible to return the favicon object
	 *
	 * @since	1.4
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getFavIcon()
	{
		$obj = new stdClass();
		$obj->color = '#5580BE';
		$obj->icon = 'fa-pie-chart';
		$obj->label = 'APP_USER_GROUPS_STREAM_TOOLTIP';

		return $obj;
	}


    /**
     * Prepares the stream item.
     *
     * @since   1.4
     * @access  public
     */
    public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
    {
        if ($item->context != SOCIAL_TYPE_POLLS) {
            return;
        }

        // Determines if the stream should be generated
        $params     = $this->getParams();

        if ( !$params->get( 'stream_' . $item->verb, true)) {
            return;
        }

        $my = FD::user();
        $privacy = $my->getPrivacy();

        // privacy validation
        if ($includePrivacy && !$privacy->validate( 'polls.view', $item->contextId , SOCIAL_TYPE_POLLS, $item->actor->id)) {
            return;
        }

        $permalink  = FRoute::stream(array('layout' => 'item', 'id' => $item->uid));
        $pollId = $item->contextId;
        $actor = $item->actor;

        $poll = FD::get('Polls');
        $content = $poll->getDisplay($pollId);

        $item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->label = JText::_('APP_POLLS_STREAM', true);

        $pollTbl = FD::table('Polls');
        $pollTbl->load($pollId);

        // Get the cluster
        $cluster = $item->getCluster();

        // Prepare the namespace
        $group = $item->cluster_type ? $item->cluster_type : SOCIAL_TYPE_USER;

        $this->set('actor', $actor);
        $this->set('poll', $pollTbl);
        $this->set('content', $content);
        $this->set('permalink', $permalink);
        $this->set('cluster', $cluster);
        $this->set('cluster_type', $group);

        $titleNamespace = 'streams/' . $group . '/title.' . $item->verb;
        $contentNamespace = 'streams/' . $group . '/preview.vote';

        $item->title    = parent::display($titleNamespace);
        $item->content  = parent::display($contentNamespace);

        // we need to determine if current user can edit this poll or not.
        $item->editablepoll = ($my->id == $pollTbl->created_by || $my->isSiteAdmin()) ? true : false;

        if ($includePrivacy) {
            $item->privacy  = $privacy->form( $item->contextId, $item->context, $item->actor->id, 'polls.view', false, $item->uid );
        }

        return true;
    }


	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel( $story )
	{
		$params 	= $this->getParams();

        // We only allow polls creation on dashboard, which means if the story target and current logged in user is different, then we don't show this
        // Empty target is also allowed because it means no target.
        if (!empty($story->target) && $story->target != FD::user()->id) {
            return;
        }

		// Determine if we should attach ourselves here.
		if (!$params->get('story_polls', true)) {
			return;
		}

        // check if user has the acl to create poll or not.
        $my = FD::user();
        $access = $my->getAccess();
        if (! $access->allowed('polls.create')) {
            return;
        }

		// Create plugin object
		$plugin = $story->createPlugin( 'polls' , 'panel');

		// We need to attach the button to the story panel
		$theme = FD::themes();

		// content. need to get the form from poll lib.
		$poll = FD::get('Polls');
		$form = $poll->getForm(SOCIAL_TYPE_STREAM, 0);
		$theme->set('form', $form);

		// Attachment script
        $script = ES::script();

        $button = $theme->output('site/polls/story/button');
        $form = $theme->output('site/polls/story/form');

        $plugin->setHtml($button, $form);
        $plugin->setScript($script->output('site/polls/story/plugin'));

		return $plugin;
	}
}
