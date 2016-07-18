<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow
 * @version $Id: images_source_external_helper.php 9013 2011-10-18 07:26:43Z trungnq $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
interface JSNImageSourceExternalHelper
{
	public function getAvaiableProfiles();

	public function getSourceTable();

	public function getOriginalInfoImages($config = array());
}