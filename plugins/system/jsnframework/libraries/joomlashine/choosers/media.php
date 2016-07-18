<?php
/**
 * @version     $Id: media.php 15683 2012-08-30 04:00:52Z cuongnm $
 * @package     JSN_Framework
 * @subpackage  Config
 * @author      JoomlaShine Team <support@joomlashine.com>
 * @copyright   Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license     GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// Set Joomla execution flag
define('_JEXEC', 1);

// Shorten directory separator constant
define('DS', DIRECTORY_SEPARATOR);

// Define base directory
define(
	'JPATH_BASE',
	str_replace(
		'/',
		DIRECTORY_SEPARATOR,
		str_replace(
			'plugins/system/jsnframework/libraries/joomlashine/choosers/media.php',
			'',
			str_replace('\\', '/', __FILE__)
		) . '/administrator'
	)
);

// Initialize Joomla framework
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

// Instantiate the application
$app = JFactory::getApplication('administrator');

// Access check
if ( ! JFactory::getUser()->authorise('core.manage', $app->input->getCmd('component')))
{
	jexit('Please login to administration panel first!');
}

// Initialize JSN Framework
require_once JPATH_ROOT . DS . 'plugins' . DS . 'system' . DS . 'jsnframework' . DS . 'jsnframework.php';

$dispatcher		= JDispatcher::getInstance();
$jsnframework	= new PlgSystemJSNFramework($dispatcher);

$jsnframework->onAfterInitialise();

// Initialize variables
$root		= '/' . trim($app->input->getVar('root'), '/');
$handler	= $app->input->getVar('handler');
$element	= $app->input->getVar('element');

// Define regular expression to filter image file
$regEx = '\.(jpg|png|gif|xcf|odg|bmp|jpeg|ico)$';

// Execute requested task
switch ($task = $app->input->getCmd('task'))
{
	case 'post.upload':
		// Check if uploaded file is image?
		if ( ! preg_match("/{$regEx}/i", $_FILES['file']['name']))
		{
			jexit(JText::_('JSN_EXTFW_GENERAL_UPLOADED_FILE_TYPE_NOT_SUPPORTED'));
		}

		// Move uploaded file to target directory
		jimport('joomla.filesystem.file');
		if ( ! JFile::upload($_FILES['file']['tmp_name'], JPATH_ROOT . $root . DS . $_FILES['file']['name']))
		{
			jexit(JText::_('JSN_EXTFW_GENERAL_MOVE_UPLOAD_FILE_FAIL'));
		}

		exit;
	break;

	case 'get.directory':
		// Get directory list
		$list = JFolder::folders(JPATH_ROOT . $root);

		// Initialize return value
		foreach ($list AS $k => $v)
		{
			$list[$k] = array('attr' => array('rel' => 'folder', 'id' => $v), 'data' => $v, 'state' => 'closed');
		}

		// Set necessary header
		header('Content-type: application/json; charset=utf-8');
		header("Cache-Control: no-cache, must-revalidate");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Pragma: no-cache");

		// Send response back
		jexit(json_encode($list));
	break;

	case 'get.media':
	default:
		if (empty($task) AND $current = $app->input->getVar('current'))
		{
			// Initialize current directory
			$current = explode(
				'/',
				$root != '/' ? str_replace(trim($root, '/') . '/', '', trim(dirname($current), '/')) : trim(dirname($current), '/')
			);
		}
		else
		{
			// Get media list
			$media = JFolder::files(JPATH_ROOT . $root, $regEx);

			// Initialize image URI
			foreach ($media AS $k => $v)
			{
				$media[$k] = str_replace(
					'\\',
					'/',
					str_replace('/plugins/system/jsnframework/libraries/joomlashine/choosers', '', trim(JURI::root(), '/')) . $root . DS . $v
				);
			}
		}
	break;
}

if ($task != 'get.media')
{
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title><?php echo JText::_('JSN_EXTFW_CHOOSERS_MEDIA'); ?></title>
	<meta name="author" content="JoomlaShine Team">
	<link href="../../../assets/3rd-party/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
	<link href="../../../assets/3rd-party/jquery-ui/css/ui-bootstrap/jquery-ui-1.8.16.custom.css" rel="stylesheet" />
	<link href="../../../assets/3rd-party/jquery.layout/css/layout-default-latest.css" rel="stylesheet" />
	<link href="../../../assets/joomlashine/css/jsn-gui.css" rel="stylesheet" />
	<!-- Load HTML5 elements support for IE6-8 -->
	<!--[if lt IE 9]>
		<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script src="../../../assets/3rd-party/jquery/jquery-1.7.1.min.js"></script>
	<script src="../../../assets/3rd-party/jquery-ui/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script src="../../../assets/3rd-party/jquery.layout/js/jquery.layout-latest.js"></script>
	<script src="../../../assets/3rd-party/jquery.jstree/jquery.jstree.js"></script>
	<script src="../../../assets/3rd-party/ajax-upload/ajaxupload.js"></script>
	<style type="text/css">
		html, body {
			margin: 0 auto;
			padding: 0;
			width: 640px;
			height: 472px;
		}
		#jsn-media-chooser {
			width: 640px;
			height: 472px;
		}
		#jsn-media-image-list {
			border: 0;
			height: 414px;
			overflow: auto;
		}
		#jsn-media-upload-form {
			margin: 7px 0;
			text-align: center;
		}
		#jsn-media-upload-status, .pane {
			display: none;
		}
		.pane-footer {
			border-top: 1px solid #bbb;
			height: 43px;
			overflow: auto;
		}
		.ui-layout-pane {
			padding: 0 !important;
			height: 470px !important;
		}
		.ui-layout-pane-center {
			margin-left: -2px !important;
		}
		.content-container {
			padding: 6px 9px;
		}
		.thumbnail img {
			width: 109px;
		}
	</style>
</head>

<body class="jsn-bootstrap">
	<div id="jsn-media-chooser" class="jsn-master">
		<div class="pane ui-layout-center">
			<div id="jsn-media-image-list" class="content-container">
<?php
}
?>
				<ul class="thumbnails">
<?php
if (isset($media) AND count($media))
{
	foreach ($media AS $file)
	{
?>
					<li><a class="thumbnail" href="javascript:void(0)"><img src="<?php echo $file; ?>" alt="<?php echo basename($file); ?>" /></a>
<?php
	}
}
else
{
?>
					<div class="alert"><?php echo JText::_('JSN_EXTFW_CHOOSERS_MEDIA_NO_ITEM'); ?></div>
<?php
}
?>
				</ul>
<?php
if ($task != 'get.media')
{
?>
			</div>
			<div class="pane-footer">
				<div id="jsn-media-upload-status" class="alert">
					<a class="close" title="<?php echo JText::_('JSN_EXTFW_GENERAL_CLOSE'); ?>" href="javascript:void(0);" onclick="$(this).parent().hide();">×</a>
					<p id="jsn-media-upload-status-message"></p>
				</div>
				<form id="jsn-media-upload-form" action="#" method="post" onsubmit="return false;">
					<input id="jsn-media-upload-button" type="file" size="20" />&nbsp;&nbsp;
					<button class="btn" onclick="JSNMediaUpload.submit();"><?php echo JText::_('JSN_EXTFW_GENERAL_UPLOAD'); ?></button>
				</form>
			</div>
		</div>
		<div class="pane ui-layout-west">
			<div id="jsn-media-directory-tree" class="content-container"></div>
		</div>
	</div>
	<script type="text/javascript">
		(function() {
			// Initialize layout
			$('#jsn-media-chooser').layout({
				applyDefaultStyles: true,
				resizable: false
			});

			// Initialize necessary variables for browsing and selecting image
			var server = '<?php echo trim(JURI::root(), '/'); ?>/media.php',
			root = '<?php echo $root; ?>',
			getActive = function(n) {
				var deep = [];
				while (n.length && n.attr('id') != 'jsn-media-directory-tree') {
					n.get(0).nodeName != 'LI' || deep.unshift(n.attr('id'));
					n = n.parent();
				}
				root == '/' || deep.unshift(root);
				return deep.join('/');
			},
			registerEvent = function() {
				// Register event handler for selecting image
				$('#jsn-media-image-list').delegate(
					'a',
					'click',
					function() {
						var selected = getActive($('.jstree-clicked', $('#jsn-media-directory-tree'))) + '/' + $(this).children('img').attr('alt');
						parent['<?php echo $handler; ?>'](selected, '#<?php echo $element; ?>');
					}
				);
			},
			getList = function(active) {
				$('#jsn-media-image-list').load(
					server,
					'task=get.media&root=' + getActive(active),
					registerEvent
				);
			};

			// Initialize directory tree
			$('#jsn-media-directory-tree').jstree({
				core: {
					initially_open: [<?php echo is_array($current) ? "'" . implode("', '", $current) . "'" : ''; ?>]
				},
				plugins: ['json_data', 'themes', 'ui'],
				json_data: {
					ajax: {
						url: server,
						data: function(n) {
							return {
								task: 'get.directory',
								root: getActive(n)
							};
						}
					}
				},
				themes: {
					theme: 'classic',
					url: '<?php echo str_replace('/libraries/joomlashine/choosers', '', trim(JURI::root(), '/')); ?>/assets/3rd-party/jquery.jstree/themes/classic/style.css'
				},
				ui: {
					initially_select: [<?php echo is_array($current) ? "'" . array_pop($current) . "'" : ''; ?>]
				}
			}).bind("select_node.jstree", function(event, data) {
				// Register event handler to load image files insides a directory
				getList(data.rslt.obj);
			});

			// Initialize media list
			registerEvent();

			// Initialize Ajax Upload
			window.JSNMediaUpload = new AjaxUpload($('#jsn-media-upload-button'), {
				action: '<?php echo trim(JURI::root(), '/') . '/media.php'; ?>?task=post.upload',
				name: 'file',
				autoSubmit: false,
				onSubmit: function(file, ext) {
					// Hide upload form, show upload status message
					$('#jsn-media-upload-form').hide();
					$('#jsn-media-upload-status').show();

					// Only allow uploading image file
					if (ext && /<?php echo $regEx; ?>/i.test(file)) {
						$('#jsn-media-upload-status-message').html(
							'<i class="jsn-icon16 icon-loading"></i> ' + '<?php echo JText::_('JSN_EXTFW_GENERAL_UPLOADING'); ?>' + ' ' + file
						);

						// Disable upload button
						this.disable();

						// Set additional data to post
						this.setData({
							root: getActive($('.jstree-clicked', $('#jsn-media-directory-tree')))
						});
					} else {
						// Show upload form
						$('#jsn-media-upload-form').show();

						// Enable upload button
						this.enable();

						$('#jsn-media-upload-status-message').html(
							'<i class="jsn-icon16 icon-remove"></i> ' + '<?php echo JText::_('JSN_EXTFW_GENERAL_UPLOADED_FILE_TYPE_NOT_SUPPORTED'); ?>'
						);

						return false;
					}
				},
				onComplete: function(file, response) {
					// Show upload form
					$('#jsn-media-upload-form').show();

					// Enable upload button
					this.enable();

					if (response == '') {
						// Add file to the list
						$('#jsn-media-upload-status-message').html('<i class="jsn-icon16 icon-ok"></i> ' + file);

						// Update image list
						getList($('.jstree-clicked', $('#jsn-media-directory-tree')));
					} else {
						// Add error message to the list
						$('#jsn-media-upload-status-message').html('<i class="jsn-icon16 icon-remove"></i> ' + response);
					}
				}
			});
		})();
	</script>
</body>
</html>
<?php
}
