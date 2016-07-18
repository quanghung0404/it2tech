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
<script type="text/javascript">
// EasyBlog.require().script('legacy').done(function($){
// 	ejax.load( 'archive' , 'loadCalendar', 'component', '<?php echo $itemId; ?>', 'small', 'blog', '' );
// });
</script>
<!-- 
<div id="ezblog-body">
	<div id="ezblog-label" class="latest-post clearfix">
		<span><?php echo JText::_( 'COM_EASYBLOG_ARCHIVE_PAGE_TITLE' ); ?></span>
		<a href="<?php echo EBR::_( 'index.php?option=com_easyblog&view=archive' );?>" class="float-r"><?php echo JText::_( 'COM_EASYBLOG_SWITCH_TO_LIST_VIEW' ); ?></a>
	</div>

	<div id="easyblogcalendar-component-wrapper" class="com_easyblogcalendar mtl">
		<div style="text-align:center;"><?php echo JText::_('COM_EASYBLOG_ARCHIVE_CALENDAR_LOADING'); ?></div>
		<div style="text-align:center;"><img src="<?php echo rtrim(JURI::root(), '/').'/components/com_easyblog/assets/images/loader.gif' ?>" /></div>
	</div>
	<div class="clearfix"></div>
</div> 
-->

<table class="eb-calendar table table-bordered" width="100%" border="1">
	<tbody>
		<tr class="eb-calendar-control">
			<td class="eb-calendar-previous text-center">
				<a class="btn-previous-month" href="#">
					<i class="fa fa-chevron-left"></i>
				</a>
			</td>
			<td class="eb-calendar-month text-center" colspan="5">
				<a class="btn-select-month" href="#">
					July 2014
				</a>
			</td>
			<td class="eb-calendar-next text-center">
				<a class="btn-next-month" href="#">
					<i class="fa fa-chevron-right"></i>
				</a>
			</td>
		</tr>

		<tr class="eb-calendar-days">
			<td class="text-center day-of-week">
				Mo
			</td>
			<td class="text-center day-of-week">
				Tu
			</td>
			<td class="text-center day-of-week">
				We
			</td>
			<td class="text-center day-of-week">
				Th
			</td>
			<td class="text-center day-of-week">
				Fr
			</td>
			<td class="text-center day-of-week">
				Sa
			</td>
			<td class="text-center day-of-week">
				Su
			</td>
		</tr>

		<tr>
			<td class="empty">
				<small class="other-day">
				</small>
			</td>
			<td class="day has-posts">
				<div>
					1
					<b>5 posts</b>

					<div class="eb-calendar-tooltips">
						<span>
							<i class="fa fa-calendar"></i>
							&nbsp;
							Tue, July 1, 2014
						</span>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Technology hasn't change us</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Some modifications can change your life</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">About us</a>
						</div>
					</div>
				</div>
			</td>
			<td class="day">
				<div>
				2
				</div>
			</td>
			<td class="day">
				<div>
				3
				</div>
			</td>
			<td class="day">
				<div>
				4
				</div>
			</td>
			<td class="day">
				<div>
				5
				</div>
			</td>
			<td class="day">
				<div>
				6
				</a>
			</td>
		</tr>
		<tr>
			<td class="day">
				<div>
				7
				</div>
			</td>
			<td class="day">
				<div>
				8
				</div>
			</td>
			<td class="day">
				<div>
				9
				</div>
			</td>
			<td class="day">
				<div>
				10
				</div>
			</td>
			<td class="day">
				<div>
				11
				</div>
			</td>
			<td class="day">
				<div>
				12
				</div>
			</td>
			<td class="day">
				<div>
				13
				</a>
			</td>
		</tr>
		<tr>
			<td class="day">
				<div>
				14
				</div>
			</td>
			<td class="day">
				<div>
				15
				</div>
			</td>
			<td class="day">
				<div>
				16
				</a>
			</td>
			<td class="day has-posts">
				<div>
					17
					<b>2 posts</b>
					
					<div class="eb-calendar-tooltips">
						<span>
							<i class="fa fa-calendar"></i>
							&nbsp;
							Tue, July 1, 2014
						</span>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Technology hasn't change us</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Some modifications can change your life</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">About us</a>
						</div>
					</div>
				</div>
			</td>
			<td class="day">
				<div>
				18
				</div>
			</td>
			<td class="day has-posts">
				<div>
					19
					<b>24 posts</b>
					
					<div class="eb-calendar-tooltips">
						<span>
							<i class="fa fa-calendar"></i>
							&nbsp;
							Tue, July 1, 2014
						</span>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Technology hasn't change us</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">Some modifications can change your life</a>
						</div>
						<div>
							<i class="fa fa-file-text text-muted"></i>
							&nbsp;
							<a href="#">About us</a>
						</div>
					</div>
				</div>
			</td>
			<td class="day">
				<div>
				20
				</a>
			</td>
		</tr>
		<tr>
			<td class="day">
				<div>
				21
				</div>
			</td>
			<td class="day">
				<div>
				22
				</div>
			</td>
			<td class="day today">
				<div>
				23
				</div>
			</td>
			<td class="day">
				<div>
				24
				</div>
			</td>
			<td class="day">
				<div>
				25
				</div>
			</td>
			<td class="day">
				<div>
				26
				</div>
			</td>
			<td class="day">
				<div>
				27
				</a>
			</td>
		</tr>
		<tr>
			<td class="day">
				<div>
				28
				</div>
			</td>
			<td class="day">
				<div>
				29
				</div>
			</td>
			<td class="day">
				<div>
				30
				</div>
			</td>
			<td class="day">
				<div>
				31
				</a>
			</td>
			<td class="empty">
				<small class="other-day">
				</small>
			</td>
			<td class="empty">
				<small class="other-day">
				</small>
			</td>
			<td class="empty">
				<small class="other-day">
				</small>
			</td>
		</tr>
	</tbody>
</table>