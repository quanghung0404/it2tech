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
defined('_JEXEC') or die('Unauthorized Access');

// Prepare all the options here

// limit options. in minute
$limitOptions = array(
		$settings->makeOption('5 items', '5'),
		$settings->makeoption('10 items', '10'),
		$settings->makeoption('15 items', '15'),
		$settings->makeoption('20 items', '20'),
		$settings->makeoption('25 items', '25'),
		$settings->makeoption('30 items', '30'),
		$settings->makeoption('35 items', '35'),
		$settings->makeoption('40 items', '40'),
		$settings->makeoption('45 items', '45'),
		$settings->makeoption('50 items', '50'),
		'help' => true,
		'class' => 'form-control input-sm input-medium',
	);

// archive duration. in month
$archiveOptions = array(
		$settings->makeOption('3 months', '3'),
		$settings->makeoption('6 months', '6'),
		$settings->makeoption('12 months', '12'),
		$settings->makeoption('18 months', '18'),
		$settings->makeoption('24 months', '24'),
		'help' => true,
		'class' => 'form-control input-sm input-medium',
	);

// pagination style
// limit options. in minute
$styleOptions = array(
		$settings->makeOption('Load more', 'loadmore'),
		$settings->makeoption('Normal pagination', 'page'),
		'help' => true,
		'class' => 'form-control input-sm input-medium'
	);

// limit options. in minute
$sortOptions = array(
		$settings->makeOption('Last modified date', 'modified'),
		$settings->makeoption('New creation date', 'created'),
		'help' => true,
		'class' => 'form-control input-sm input-medium'
	);


echo $settings->renderTabs(array(
		'general' => $settings->renderPage(
						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('General Features'),
								//$settings->renderSetting('Follow Enabled', 'stream.follow.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Comments Enabled', 'stream.comments.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Likes Enabled', 'stream.likes.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Repost Enabled', 'stream.repost.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Sharing Enabled', 'stream.sharing.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Allow Guest View Comments', 'stream.comments.guestview', 'boolean', array('help' => true)),
								$settings->renderSetting('Allow Bookmarks', 'stream.bookmarks.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Display Timestamp', 'stream.timestamp.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Display RSS', 'stream.rss.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Pin Enable', 'stream.pin.enabled', 'boolean', array('help' => true))
							)
						),

						$settings->renderColumn(
							$settings->renderSection(
								$settings->renderHeader('Translations', 'Translations Info'),
								$settings->renderSetting('Enable Bing Translations', 'stream.translations.bing', 'boolean', array('help' => true)),
								$settings->renderSetting('Always Show Translations Link', 'stream.translations.explicit', 'boolean', array('help' => true)),
								$settings->renderSetting('Bing Client ID', 'stream.translations.bingid', 'input', array('help' => true, 'class' => 'form-control input-sm')),
								$settings->renderSetting('Bing Client Secret', 'stream.translations.bingsecret', 'input', array('help' => true, 'class' => 'form-control input-sm'))
							),
							$settings->renderSection(
								$settings->renderHeader('Content'),
								$settings->renderSetting('No Follow', 'stream.content.nofollow', 'boolean', array('help' => true)),
								$settings->renderSetting('Truncation', 'stream.content.truncate', 'boolean', array('help' => true)),
								$settings->renderSetting('Truncation Length', 'stream.content.truncatelength', 'input', array('help' => true, 'class' => 'form-control input-sm input-short text-center', 'unit' => 'Characters'))
							),
							$settings->renderSection(
								$settings->renderHeader('Archive'),
								$settings->renderSetting('Archive Enable', 'stream.archive.enabled', 'boolean', array('help' => true)),
								$settings->renderSetting('Archive Duration', 'stream.archive.duration', 'list', $archiveOptions)
							)
						)

					),
		'form' => $settings->renderPage(
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Story Form'),
									$settings->renderSetting('Display Mentions', 'stream.story.mentions', 'boolean', array('help' => true)),
									$settings->renderSetting('Display Location', 'stream.story.location', 'boolean', array('help' => true)),
									$settings->renderSetting('Enable Moods', 'stream.story.moods', 'boolean', array('help' => true)),
									$settings->renderSetting('Enter key submits form', 'stream.story.entertosubmit', 'boolean', array('help' => true))
								)
							)
					),
		'notifications' => $settings->renderPage(
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('New updates'),
									$settings->renderSetting('Enabled', 'stream.updates.enabled', 'boolean', array('help' => true)),
									$settings->renderSetting('Interval', 'stream.updates.interval', 'input', array('help' => true, 'class' => 'form-control input-sm input-short', 'unit' => 'Seconds'))
								)
							)
					),
		'pagination' => $settings->renderPage(
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Pagination'),
									$settings->renderSetting('Pagination style', 'stream.pagination.style', 'list', $styleOptions),
									$settings->renderSetting('Auto Load When Scroll', 'stream.pagination.autoload', 'boolean', array('help' => true)),
									$settings->renderSetting('Data fetch limit', 'stream.pagination.pagelimit', 'list', $limitOptions),
									$settings->renderSetting('Items sorting', 'stream.pagination.sort', 'list', $sortOptions)
								)
							),
							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Aggregation'),
									$settings->renderSetting('Enable', 'stream.aggregation.enabled', 'boolean', array('help' => true)),
									$settings->renderSetting('Duration', 'stream.aggregation.duration', 'input', array('help' => true, 'class' => 'form-control input-sm input-short', 'unit' => 'Minutes'))
								)
							)
					),
		'activitylogs' => $settings->renderPage(

							$settings->renderColumn(
								$settings->renderSection(
									$settings->renderHeader('Pagination', 'Activity Log Pagination Description'),
									$settings->renderSetting('Backend user activities limit', 'activity.pagination.max', 'input', array('unit'=> true, 'help' => true, 'class' => 'form-control input-sm input-short text-center')),
									$settings->renderSetting('Frontend data fetch limit', 'activity.pagination.limit', 'input', array('unit'=> true, 'help' => true, 'class' => 'form-control input-sm input-short text-center'))
								)
							)

					)
	)
);

