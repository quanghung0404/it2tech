<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyBlogBlocks extends EasyBlog
{
	/**
	 * Retrieves a list of blocks available on the site.
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getAvailableBlocks()
	{
		static $blocks = null;

		if (is_null($blocks)) {
			$model = EB::model('Blocks');
			$blocks = $model->getAvailableBlocks();
		}

		return $blocks;
	}

	/**
	 * Retrieves a block handler
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function get(EasyBlogTableBlock $block)
	{
		if (!$block->element) {
			return;
		}

		require_once(dirname(__FILE__) . '/handlers/' . $block->element . '.php');

		$class = 'EasyBlogBlockHandler' . ucfirst($block->element);
		$handler = new $class($block);

		return $handler;
	}

	/**
	 * Retrieves a handler provided with the element
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getBlockByType($type)
	{
		static $loaded = null;

		if (is_null($loaded)) {
			$model = EB::model('blocks');
			$blocks = $model->loadAllBlocks();

			if ($blocks) {
				foreach($blocks as $block) {
					$tbl = EB::table('Block');
					$tbl->bind($block);

					$loaded[$block->element] = $tbl;
				}
			}
		}

		return $this->get($loaded[$type]);
	}

	/**
	 * Creates a new block
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function createBlock($type, $data=array(), $props=array())
	{
		// Create block
		$block = (object) array_merge(array('type' => $type), $props);

		// Let block handler fill up the rest of the details
		$handler = $this->getBlockByType($type);
		$block = $handler->updateBlock($block, $data);

		return $block;
	}

	/**
	 * Renders a block html code
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderViewableBlock($block, $stripTags = false)
	{
		// Get block handler
		$handler = $this->getBlockByType($block->type);

		// Block handler should be able to manipulate the html output of the block if they want to.
		$blockHtml = $handler->getHtml($block, $stripTags);

		// Render nested blocks
		$blockHtml = $this->renderNestedBlocks(EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml, $stripTags);

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml);

		return $html;
	}

	/**
	 * helper function to get block html for diff
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */

	private function getBlockContent($block)
	{
		$handler = $this->getBlockByType($block->type);

		$html = $handler->getHtml($block);

		// we need to replace &nbsp; or else the html diff lib will break.
        $html = JString::str_ireplace('&nbsp;', ' ', $html);

        return $html;
	}


	/**
	 * Compare and Renders the diff block
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderDiffBlocks($block, $arrBlocks)
	{

		// ini_set('xdebug.var_display_max_depth', -1);
		// ini_set('xdebug.var_display_max_children', -1);
		// ini_set('xdebug.var_display_max_data', -1);

		$blockHtml = '';
		// Get editable html from block handler
		if (array_key_exists($block->uid, $arrBlocks)) {

			$target = $arrBlocks[$block->uid];
			$targetText = $this->getBlockContent($target);

			$blockText = $this->getBlockContent($block);

			$blockHtml = EB::revisions()->compare($targetText, $blockText);

		} else {

			$blockText = $this->getBlockContent($block);

			$blockHtml = EB::revisions()->compare('', $blockText);
		}

		// for the diff, we need to handle the nesteblock abit different.
		if (isset($block->blocks)) {

			$nestedBlocks = $block->blocks;

			// Go through every nested block
			foreach ($nestedBlocks as $nestedBlock) {

				$nestedBlockHtml = $this->renderDiffBlocks($nestedBlock, $arrBlocks);
				// Replace nested block placeholder with nested block html
				$blockHtml = JString::str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
			}
		}

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_DIFF, $block, $blockHtml);

		// Render block data
		$html .= $this->renderBlockData($block);

		return $html;
	}

	/**
	 * Renders editable block html codes
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderEditableBlock($block, $renderData = true)
	{
		// Get block handler
		$handler = $this->getBlockByType($block->type);

		if (!$handler) {
			return;
		}

		// Get editable html from block handler
		$blockHtml = $handler->getEditableHtml($block);

		// Render nested blocks
		$blockHtml = $this->renderNestedBlocks(EASYBLOG_BLOCK_MODE_EDITABLE, $block, $blockHtml);

		// Render block container
		$html = $this->renderBlockContainer(EASYBLOG_BLOCK_MODE_EDITABLE, $block, $blockHtml);

		// Render block data
		if ($renderData) {
			$html .= $this->renderBlockData($block, $handler);
		}

		return $html;
	}

	/**
	 * Renders the block container to be used with the composer
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderBlockContainer($mode = EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml)
	{
		// Block type
		$blockType = 'data-type="' . $block->type . '"';

		// Block style
		$blockStyle = '';

		if (isset($block->style)) {
		    $blockStyle = 'style="' . $block->style . '"';
		}

		// Block nest
		$blockNest = '';

		if (isset($block->nested) && $block->nested) {
		    $blockNest .= ' is-nested';
		}

		if (isset($block->position)) {
		    $blockNest .= ' nest-' . $block->position;
		}

		if (isset($block->isolated) && $block->isolated) {
		    $blockNest .= ' is-isolated';
		}

		$blockUid = '';
		if (isset($block->uid)) {
			$blockUid = 'data-uid="' . $block->uid . '"';
		}

		// Block html
		$blockHtml = trim($blockHtml);

		$template = EB::template();
		$template->set('block', $block);
		$template->set('blockUid', $blockUid);
		$template->set('blockType', $blockType);
		$template->set('blockNest', $blockNest);
		$template->set('blockStyle', $blockStyle);
		$template->set('blockHtml', $blockHtml);

		return $template->output('site/document/blocks/' . $mode);
	}

	public function renderNestedBlocks($mode = EASYBLOG_BLOCK_MODE_VIEWABLE, $block, $blockHtml, $stripTags = false)
	{
		// If there are nested blocks
		if (isset($block->blocks)) {

			$nestedBlocks = $block->blocks;

			// Go through every nested block
			foreach ($nestedBlocks as $nestedBlock) {

				// Get nested block html
				switch ($mode) {

					// case EASYBLOG_BLOCK_MODE_DIFF:
					// 	$nestedBlockHtml = $this->renderDiffBlock($nestedBlock);
					// 	break;

					case EASYBLOG_BLOCK_MODE_VIEWABLE:
						$nestedBlockHtml = $this->renderViewableBlock($nestedBlock, $stripTags);
						break;

					case EASYBLOG_BLOCK_MODE_EDITABLE:
						$nestedBlockHtml = $this->renderEditableBlock($nestedBlock);
						break;
				}

				// Replace nested block placeholder with nested block html
				// $blockHtml = JString::str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
				$blockHtml = str_ireplace('<!--block' . $nestedBlock->uid . '-->', $nestedBlockHtml, $blockHtml);
			}
		}

		return $blockHtml;
	}

	/**
	 * Renders the inline block data which can be used by the js later.
	 * The data consists of a textarea with json encoded meta data from the block
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function renderBlockData($block, $handler = null)
	{
		// There are possibilities where the block needs to manipulate the output before json_encode it. For instance,
		// if the block data contains ", they need to be entities first.
		if ($handler && method_exists($handler, 'normalizeData')) {
			$block->data = $handler->normalizeData($block->data);
		}

		$out = '<textarea data-block>' . json_encode($block->data) . '</textarea>';

		return $out;
	}

	/**
	 * Formats blocks in the blog post.
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function format(EasyBlogPost &$blog, $blocks, $type = 'list')
	{
		static $_cache = array();

		if (!$blocks) {
			return array();
		}

		// Determines the total number of blocks
		$total = count($blocks);

		// Get the maximum number of blocks
		$max = $this->config->get('composer_truncation_blocks');

		// Determines if content truncation should happen
		if ($type == 'list' && $this->config->get('composer_truncation_enabled') && $max) {
			// Get the total number of blocks
			$blocks = array_splice($blocks, 0, $max);
		}

		// Default read more to false
		$blog->readmore = false;

		// Default contents
		$contents = '';

		foreach ($blocks as $item) {

			// If the read more is present at this point of time, we should skip processing the rest of the blocks
			if ($blog->readmore && $type == 'list') {
				continue;
			}

			// Load from cache
			if (!isset($_cache[$item->type])) {
				$tblElement = EB::table('Block');
				$tblElement->load(array('element' => $item->type));

				$_cache[$item->type] = $tblElement;
			}

			$table = $_cache[$item->type];
			$block = EB::blocks()->get($table);

			$contents .= $block->formatDisplay($item, $blog);
		}

		// If the total is larger than the iterated blocks, we need to display the read more
		if ($total > count($blocks)) {
			$blog->readmore = true;
		}

		return $contents;
	}
}
