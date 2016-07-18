<?php
$jsnutils	= JSNTplUtils::getInstance();
$doc		= $this->_document;

// Count module instances
$doc->hasRight		= $jsnutils->countModules('right');
$doc->hasLeft		= $jsnutils->countModules('left');
$doc->hasPromo		= $jsnutils->countModules('promo');
$doc->hasPromoLeft	= $jsnutils->countModules('promo-left');
$doc->hasPromoRight	= $jsnutils->countModules('promo-right');
$doc->hasInnerLeft	= $jsnutils->countModules('innerleft');
$doc->hasInnerRight	= $jsnutils->countModules('innerright');

// Define template colors
$doc->templateColors	= array('pink', 'red', 'green', 'brown', 'orange', 'cyan');

if (isset($doc->sitetoolsColorsItems))
{
	$this->_document->templateColors = $doc->sitetoolsColorsItems;
}

// Apply K2 style
if ($jsnutils->checkK2())
{
	$doc->addStylesheet($doc->templateUrl . "/ext/k2/jsn_ext_k2.css");
}
// Apply VM style
if ($jsnutils->checkVM())
{
	$doc->addStylesheet($doc->templateUrl . "/ext/vm/jsn_ext_vm.css");
}

// Start generating custom styles
$customCss	= '';

// setup main layouts width
if ($doc->customWidth != 'responsive')
{
	$customCss .= '
#jsn-page {
	min-width: ' . $doc->customWidth . ';
}
#jsn-pos-topbar,
#jsn-pos-top,
#jsn-header-bottom-inner,
#jsn-content_inner,
#jsn-promo-inner,
#jsn-pos-content-top,
#jsn-pos-promo_inner,
#jsn-content-bottom-inner,
#jsn-footer-inner {
	width: ' . $doc->customWidth . ';
}';
}

// Setup main menu width parameter
if ($doc->mainMenuWidth)
{
	$menuMargin = $doc->mainMenuWidth + 1;

	$customCss .= '
		div.jsn-modulecontainer ul.menu-mainmenu ul li,
		div.jsn-modulecontainer ul.menu-mainmenu ul {
			width: ' . $doc->mainMenuWidth . 'px;
		}
		div.jsn-modulecontainer ul.menu-mainmenu ul ul {
		';
		if($doc->direction == 'ltr'){
			$customCss .= '
				margin-left: ' . $menuMargin . 'px;
				margin-left: ' . $doc->mainMenuWidth . 'px\9;
			';
		}
		if($doc->direction == 'rtl'){
			$customCss .= '
				margin-right: ' . $menuMargin . 'px;
				margin-right: ' . $doc->mainMenuWidth . 'px\9;
			';
		}
		$customCss .= '
		}
		#jsn-pos-toolbar div.jsn-modulecontainer ul.menu-mainmenu ul ul {
		';
		if($doc->direction == 'ltr'){
			$customCss .= '
				margin-right: '.$menuMargin.'px;
				margin-right: '.$doc->mainMenuWidth.'px\9;
				margin-left : auto';
		}
		if($doc->direction == 'rtl'){
			$customCss .= '
				margin-left : '.$menuMargin.'px;
				margin-left : '.$doc->mainMenuWidth.'px\9;
				margin-right: auto';
		}
		$customCss .= '
		}
	';
}

// Setup slide menu width parameter
if ($doc->sideMenuWidth)
{
	$sideMenuMargin = $doc->sideMenuWidth;

	$customCss .= '
		div.jsn-modulecontainer ul.menu-sidemenu ul,
		div.jsn-modulecontainer ul.menu-sidemenu ul li {
			width: ' . $doc->sideMenuWidth . 'px;
		}
		div.jsn-modulecontainer ul.menu-sidemenu > li > ul {
			right: -' . $sideMenuMargin . 'px;
			right: -' .$doc->sideMenuWidth. 'px\9;
		}
		body.jsn-direction-rtl div.jsn-modulecontainer ul.menu-sidemenu > li > ul {
			left: -' . $sideMenuMargin . 'px;
			left: -' .$doc->sideMenuWidth. 'px\9;
			right: auto;
		}
		div.jsn-modulecontainer ul.menu-sidemenu ul ul {
		';
		if($doc->direction == 'ltr'){
			$customCss .= '
				margin-left: ' . $sideMenuMargin . 'px;
				margin-left: '.$doc->sideMenuWidth.'px\9;
			';
		}
		if($doc->direction == 'rtl'){
			$customCss .= '
				margin-right: ' . $sideMenuMargin . 'px;
				margin-right: '.$doc->sideMenuWidth.'px\9;
			';
		}
		$customCss .= '
		}
	';
}

// Include CSS3 support for IE browser
if ($doc->isIE)
{
	$customCss .= '
		.text-box,
		.text-box-highlight,
		.text-box-highlight:hover,
		div[class*="box-"] div.jsn-modulecontainer_inner,
		div[class*="solid-"] div.jsn-modulecontainer_inner {
			behavior: url(' . $doc->rootUrl . '/templates/'.strtolower($doc->template).'/css/PIE.htc);
		}
		.link-button {
			zoom: 1;
			position: relative;
			behavior: url(' . $doc->rootUrl . '/templates/'.strtolower($doc->template).'/css/PIE.htc);
		}
	';
}

$doc->addStyleDeclaration(trim($customCss, "\n"));
