<?php
/**
 * @version    $Id$
 * @package    JSN_EasySlider
 * @author     JoomlaShine Team <support@joomlashine.com>
 * @copyright  Copyright (C) 2012 JoomlaShine.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.joomlashine.com
 * Technical Support:  Feedback - http://www.joomlashine.com/contact-us/get-support.html
 */

// No direct access to this file
defined('_JEXEC') or die( 'Restricted access' );

JSNHtmlAsset::addStyle(JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/font-awesome/css/font-awesome.css');

// Display messages
if ( JFactory::getApplication()->input->getInt('ajax') != 1 )
{
	echo $this->msgs;
}

?>
	<div id="jsn-page-list" class="jsn-master jsn-page-list jsn-easyslider-hide" token="<?php echo JFactory::getSession()->getFormToken();?>">
		<div class="jsn-bootstrap">
			<form class="form-inline" action="<?php echo JRoute::_('index.php?option=com_easyslider&view=sliders'); ?>"
			      method="post" name="adminForm" id="adminForm">
				<?php
				$pathRootImage = JURI::root();

				$JSNItemList = new JSNItemlistGenerator($this->getModel());

				$JSNItemList->addColumn('', 'slider_id', 'checkbox', array( 'checkall' => true, 'name' => 'cid[]', 'class' => 'jsn-column-select', 'onclick' => 'Joomla.isChecked(this.checked);' ));
				$JSNItemList->addColumn('', null, 'images', array( 'class' => 'jsn-column-icon', 'srcRoot' => ( JSNES_ASSETS_URL . 'images/es-icon-large.png' ) ));

				$JSNItemList->addColumn('JSN_EASYSLIDER_SLIDER_TITLE', 'slider_title', 'link', array( 'sortTable' => 'il.slider_title', 'class' => 'jsn-column-title', 'link' => 'index.php?option=com_easyslider&view=slider&layout=edit&slider_id={$slider_id}' ));
				$JSNItemList->addColumn('JSN_EASYSLIDER_POSITION', 'ordering', 'ordering', array( 'sortTable' => 'il.ordering', 'class' => 'jsn-column-ordering', 'classHeader' => 'header-orders' ));

				$JSNItemList->addColumn('JSN_EASYSLIDER_SLIDER_ID', 'slider_id', '', array( 'class' => 'jsn-column-id', 'classHeader' => 'header-2percent', 'sortTable' => 'il.slider_id' ));

				$JSNItemList->addColumn('JSN_EASYSLIDER_PUBLISHED', 'published', 'published', array( 'classHeader' => 'header-5percent', 'class' => 'jsn-column-published' ));
				$JSNItemList->addColumn('JSN_EASYSLIDER_ACTION', '', 'custom', array( 'class' => 'jsn-column-medium', 'classHeader' => 'header-5percent', 'obj' => $this, 'method' => 'renderBtnAddToModule' ));

				echo $JSNItemList->generateFilter();
				echo $JSNItemList->generate();
				?>
				<?php echo JHTML::_('form.token'); ?>
			</form>
			<div id="jsnes-import-export">
				<!-- Modal -->
				<div class="modal fade hidden" role="dialog">
					<div class="modal-dialog">

						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<button type="button" class="close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title">Modal Header</h4>
							</div>
							<div class="modal-body">
								<p>Some text in the modal.</p>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default jsnes-close" data-dismiss="modal">Close</button>
								<button type="button" class="btn btn-success jsnes-save" data-dismiss="modal">OK</button>
							</div>
						</div>

					</div>
				</div>

			</div>
		</div>

		<script type="text/javascript" src="<?php echo JSNES_ASSETS_URL . 'slider/js/view/import-export.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo JSNES_PLG_SYSTEM_ASSETS_URL . 'js/lib/migrate.js' ?>"></script>
		<script type="text/javascript"
		        src="<?php echo JSNES_PLG_SYSTEM_ASSETS_URL . 'lib/underscore/underscore-min.js' ?>"></script>
		<script>
			var data_migration_map_0_1 = {
				'slides': {
					attr: 'slides',
					map: {
						'items': {
							attr: 'items',
							map: {}
						}
					}
				}
			}
			var data_migration_map_1_2 = {

				'settings': undefined,
				'itemStyles': undefined,
				'itemAnimations': undefined,
				'textStyles': undefined,
				'autoplayVideo': undefined,
				'title': undefined,

				'fullWidth': 'layout.fullWidth',
				'fullHeight': 'layout.fullHeight',

				"width": undefined,
				"height": undefined,
				"minWidth": undefined,
				"maxWidth": undefined,
				"minHeight": undefined,
				"maxHeight": undefined,

				"canvasWidth": function ( value, data ) {
					data.layout.desktop_w = parseInt(value);
				},
				"canvasHeight": function ( value, data ) {
					data.layout.desktop_h = parseInt(value);
				},

				"tabletMode": 'layout.tablet',
				"tabletUnder": function ( value, data ) {
					data.layout.tablet_under = parseInt(value);
				},
				"tabletWidth": 'layout.tablet_w',
				"tabletHeight": 'layout.tablet_h',

				"mobileMode": 'layout.mobile',
				"mobileUnder": function ( value, data ) {
					data.layout.mobile_under = parseInt(value);
				},
				"mobileWidth": 'layout.mobile_w',
				"mobileHeight": 'layout.mobile_h',

				"responsiveEditMode": undefined,
				"viewportOffsetX": undefined,
				"viewportOffsetY": undefined,
				"zoom": undefined,
				'fonts': undefined,

				'slides': {
					attr: 'slides',
					map: {

						"active": 'active',
						"index": 'index',
						"currentTime": undefined,

						"backgroundColor": "background.color",
						'backgroundImage.url': "background.image.src",
						"backgroundPosition": "background.position",
						"backgroundSize": "background.size",

						"transition": undefined,

						"transition.effect": "transition.fade",
						"transition.timing": "transition.easing",
						"transition.delay": 'duration',
						"transition.duration": 'transition.duration',

						"transition.type": undefined,
						"transition.rows": undefined,
						"transition.cols": undefined,
						"transition.delayRandom": undefined,
						"transition.delayY": undefined,
						"transition.delayX": undefined,
						"transition.cubeDepth": undefined,
						"transition.cubeAnimation": undefined,
						"transition.cubeFace": undefined,
						"transition.cubeAxis": undefined,
						"transition.cubeRotate": undefined,

						'items': {
							attr: 'items',
							map: {
								'type': 'type',
								'selected': 'selected',
								"textType": undefined,
								'tagName': undefined,

								"name": "name",
								'lock': 'lock',

								'origin': undefined,
								'items': undefined,

								'customID': 'attr.id',
								'customClassName': 'attr.class',
								'link': 'attr.href',
								'linkTarget': 'attr.target',

								'index': function ( value, data ) {
									data.index = -value;
								},
								'content': function( value, data ) {
									switch (data.type) {
										case 'text':
											data.content = '<div>' + value + '</div>';
											break;
										default:
											data.name = value;
											break;
									}
								},

								'show': function ( value, data ) {
									data.hidden = !value;
								},
								style: function ( value, data ) {
									data.style_desktop = { flex: {} };
								},
								style_T: function ( value, data ) {
									data.style_tablet = { flex: {} };
								},
								style_M: function ( value, data ) {
									data.style_mobile = { flex: {} };
								},

								"video": function ( value, data ) {
									data.style_desktop.video = {};
								},
								"video.url": undefined,
								"video.volume": 'style_desktop.background.video.volume',
								"video.autoplay": 'style_desktop.background.video.autoplay',
								"video.loop": 'style_desktop.background.video.loop',
								"video.controls": 'style_desktop.background.video.controls',
								"video.type": function ( value, data, oldData ) {
									switch ( value ) {
										case 'youtube':
											data.style_desktop.background.video.youtube = oldData.video.url;
											data.style_desktop.background.video.selector = 'provider';
											data.style_desktop.background.color = '#000000';
											break;
										case 'vimeo':
											data.style_desktop.background.video.vimeo = oldData.video.url;
											data.style_desktop.background.video.selector = 'provider';
                                            data.style_desktop.background.color = '#000000';
											break;
										case 'local':
											data.style_desktop.background.video.mp4 = oldData.video.url;
											data.style_desktop.background.video.selector = 'local';
                                            data.style_desktop.background.color = '#000000';
											break;
									}
								},

								"image": undefined,
								"image.type": undefined,
								"image.url": "style_desktop.background.image.src",

								'animation': function( value, data ) {
									data.animation = {
										in: {},
										out: {},
									}
								},
								'build': undefined,
								"build.outEffect": "animation.out.effect",
								"build.outStart": 'animation.out.delay',
								"build.outEnd": function ( value, data ) {
									data.animation.out.duration = value - data.animation.out.delay;
								},
								"build.outEasing": "animation.out.easing",
								"build.outTransform": "animation.out.transform",
								"build.outTransform.opacity": function ( value, data ) {
									data.animation.out.transform.opacity = 0;
								},

								"build.inEffect": 'animation.in.effect',
								"build.inStart": 'animation.in.delay',
								"build.inEnd": function ( value, data ) {
									data.animation.in.duration = value - data.animation.in.delay;
								},
								"build.inEasing": "animation.in.easing",
								"build.inTransform": "animation.in.transform",
								"build.inTransform.opacity": function ( value, data ) {
									data.animation.in.transform.opacity = 0;
								},
							}
						}
					}
				}
			};

			var item_style_mapping = {
				'visibility': 'visibility',


				'background': 'background.color',

				'fontSize': function(value,data) {
					data.font = {
						size: parseInt(value)
					}
				},
				'color': 'font.color',
				'fontFamily': 'font.family',
				'fontWeight': 'font.weight',
				'fontStyle': 'font.style',

				'lineHeight': 'line_height',
				'letterSpacing': 'letter_spacing',

				'padding': function ( value, data ) {
					data.padding = {
						top: value,
						left: value,
						right: value,
						bottom: value,
					}
				},

				'borderStyle': 'border.style',
				'borderWidth': 'border.width',
				'borderColor': 'border.color',
				'borderRadius': 'border.radius',

				'textAlign': function(value,data) {
					switch (value) {
						case 'left':
							data.flex.layout = 'row';
							data.flex.justifyContent = 'start';
							break;
						case 'right':
							data.flex.layout = 'row';
							data.flex.justifyContent = 'end';
							break;
						case 'center':
							// Do nothing
							break;
					}
				},

				'top': function( value, newData, oldData, rootData, styleAttr, newItemData, oldItemData) {
					var slideHeight;
					switch (styleAttr) {
						case 'style':
							slideHeight = parseInt(rootData.canvasHeight);
							break;
						case 'style_M':
							slideHeight = parseInt(rootData.mobileHeight != null ? rootData.mobileHeight : rootData.canvasHeight);
							break;
						case 'style_T':
							slideHeight = parseInt(rootData.tabletHeight != null ? rootData.tabletHeight : rootData.canvasHeight);
							break;
					}
					var itemHeight = getPxValue(oldData.height, slideHeight);
					var origin = !oldItemData.origin ? [0,0] : oldItemData.origin.split(',').map(function(n) {
						return parseFloat(n);
					});
					if (typeof origin == 'object' ) {
						origin = origin[1];
					}

					if (typeof value == 'number' ) {
						value = value.toString();
					}
					if (value.indexOf('%') >= 0) {
						newData.position = { y: parseFloat(value) / 100 };
						newData.offset = { y: origin * -itemHeight };
					}
					else if (value.indexOf('px') >= 0) {
						newData.position = { y: 0.5 };
						newData.offset = { y: parseInt(value) + (origin * -itemHeight) + (-0.5 * slideHeight) };
					}

				},

				'left': function( value, newData, oldData, rootData, styleAttr, newItemData, oldItemData) {
					var slideWidth;
					switch (styleAttr) {
						case 'style':
							slideWidth = parseInt(rootData.canvasWidth);
							break;
						case 'style_M':
							slideWidth = parseInt(rootData.mobileWidth != null ? rootData.mobileWidth : rootData.canvasWidth);
							break;
						case 'style_T':
							slideWidth = parseInt(rootData.tabletWidth != null ? rootData.tabletWidth : rootData.canvasWidth);
							break;
					}
					var itemWidth = getPxValue(oldData.width, slideWidth);
					var origin = !oldItemData.origin ? [0,0] : oldItemData.origin.split(',').map(function(n) {
						return parseFloat(n);
					});

					if (typeof origin == 'object' ) {
						origin = origin[0];
					}

					if (typeof value == 'number' ) {
						value = value.toString();
					}
					if (value.indexOf('%') >= 0) {
						newData.position = newData.position || {};
						newData.offset = newData.offset || {};

						newData.position.x = parseFloat(value) / 100;
						newData.offset.x = (origin * -itemWidth);
					}
					else if (value.indexOf('px') >= 0) {
						newData.position = newData.position || {};
						newData.offset = newData.offset || {};

						newData.position.x = 0.5;
						newData.offset.x = parseInt(value) + (origin * -itemWidth) + (-0.5 * slideWidth);
					}
				},

				width: function(value, newData, oldData, rootData, styleAttr, newItemData, oldItemData) {
					var slideWidth;
					switch (styleAttr) {
						case 'style':
							slideWidth = parseInt(rootData.canvasWidth);
							break;
						case 'style_M':
							slideWidth = parseInt(rootData.mobileWidth != null ? rootData.mobileWidth : rootData.canvasWidth);
							break;
						case 'style_T':
							slideWidth = parseInt(rootData.tabletWidth != null ? rootData.tabletWidth : rootData.canvasWidth);
							break;
					}
					newData.width = getPxValue(value, slideWidth)
				},

				height: function(value, newData, oldData, rootData, styleAttr, newItemData, oldItemData) {
					var slideHeight;
					switch (styleAttr) {
						case 'style':
							slideHeight = parseInt(rootData.canvasHeight);
							break;
						case 'style_M':
							slideHeight = parseInt(rootData.mobileHeight != null ? rootData.mobileHeight : rootData.canvasHeight);
							break;
						case 'style_T':
							slideHeight = parseInt(rootData.tabletHeight != null ? rootData.tabletHeight : rootData.canvasHeight);
							break;
					}
					newData.height = getPxValue(value, slideHeight)
				}
			}

			_({
				'style': 'style_desktop',
				'style_M': 'style_mobile',
				'style_T': 'style_tablet',

			}).each(function( toAttr, fromAttr ) {
				_(item_style_mapping).each(function( value, key ) {
					switch (typeof value) {
						case 'string':
							data_migration_map_1_2.slides.map.items.map[fromAttr + '.' + key] = toAttr + '.' + value;
							break;
						case 'function':
							data_migration_map_1_2.slides.map.items.map[fromAttr + '.' + key] = function( v, n, o, r ) {
								value(v, n[toAttr], o[fromAttr], r, fromAttr, n, o);
							}
							break;
					}
				})
			})

			function getPxValue(input,size) {
				if ( typeof input == 'undefined' )
					return '';
				if ( typeof input == 'number' )
					return input;
				if (input.indexOf('px') >= 0)
					return parseFloat(input);
				if (input.indexOf('em') >= 0)
					return parseFloat(input) * 14;
				if (input.indexOf('%') >= 0)
					return parseFloat(input) / 100 * size;
			}

		</script>
	</div>
<?php
if ( count($this->sliders) )
{
	$preConvertedData = array();
	foreach ( $this->sliders as $slider )
	{
		$preConvertedData [ $slider->slider_id ] = $slider->slider_data;
	}

	if ( count($preConvertedData) )
	{
		?>
		<script type="text/javascript">
			const JSNES_SlidersData = <?php echo json_encode($preConvertedData); ?>;
			const JSNES_UrlRoot = "<?php echo JURI::root(); ?>";
		</script>
		<?php
	}
}
?>
<?php
// Display footer
JSNHtmlGenerate::footer();