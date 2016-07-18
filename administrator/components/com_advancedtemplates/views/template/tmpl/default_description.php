<?php
/**
 * @package         Advanced Template Manager
 * @version         1.6.4PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>

<div class="pull-left">
	<?php echo JHtml::_('templates.thumb', $this->template->element, $this->template->client_id); ?>
</div>
<h2><?php echo ucfirst($this->template->element); ?></h2>
<?php $client = JApplicationHelper::getClientInfo($this->template->client_id); ?>
<p><?php $this->template->xmldata = AdvancedTemplatesHelper::parseXMLTemplateFile($client->path, $this->template->element); ?></p>
<p><?php echo JText::_($this->template->xmldata->description); ?></p>
