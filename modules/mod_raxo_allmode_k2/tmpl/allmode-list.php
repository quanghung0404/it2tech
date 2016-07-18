<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Template
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @subpackage	All-mode List Template
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


// no direct access
defined('_JEXEC') or die;

// add template CSS
JHtml::stylesheet('modules/mod_raxo_allmode_k2/tmpl/allmode-list/allmode-list.css');
?>


<?php if ($toplist || $list) { ?>
<ul class="allmode-items">

	<?php																		// All-mode TOP Items Output
	foreach ($toplist as $item) { ?>

		<li class="allmode-topitem">

			<?php if ($item->image) { ?>
			<div class="allmode-img"><?php echo $item->image; ?></div>
			<?php } ?>

			<?php if ($item->date || $item->category || $item->author || $item->hits || $item->comments_count || $item->rating_value) { ?>
			<div class="allmode-info">

				<?php if ($item->date) { ?>
				<span class="allmode-date"><?php echo $item->date; ?></span>
				<?php } ?>

				<?php if ($item->category) { ?>
				<span class="allmode-category"><?php echo $item->category; ?></span>
				<?php } ?>

				<?php if ($item->author) { ?>
				<span class="allmode-author"><?php echo $item->author; ?></span>
				<?php } ?>

				<?php if ($item->hits) { ?>
				<span class="allmode-hits"><?php echo $item->hits; ?></span>
				<?php } ?>

				<?php if ($item->comments_count) { ?>
				<span class="allmode-comments"><?php echo $item->comments_count; ?></span>
				<?php } ?>

				<?php if ($item->rating_value) { ?>
				<span class="allmode-rating"><?php echo $item->rating_value; ?></span>
				<?php } ?>

			</div>
			<?php } ?>

			<?php if ($item->title) { ?>
			<h3 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h3>
			<?php } ?>

			<?php if ($item->text) { ?>
			<div class="allmode-text"><?php echo $item->text; ?></div>
			<?php } ?>

			<a href="<?php echo $item->link; ?>" class="allmode-readmore"></a>

		</li>

	<?php } ?>


	<?php																		// All-mode Items Output
	foreach ($list as $item) { ?>

		<li class="allmode-item">

			<?php if ($item->image) { ?>
			<div class="allmode-img"><?php echo $item->image; ?></div>
			<?php } ?>
			
			<?php if ($item->date) { ?>
			<div class="allmode-date"><?php echo $item->date; ?></div>
			<?php } ?>

			<div class="allmode-right">

				<?php if ($item->title) { ?>
				<h4 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
				<?php } ?>

				<?php if ($item->category || $item->author || $item->hits || $item->comments_count || $item->rating_value) { ?>
				<div class="allmode-info">

					<?php if ($item->category) { ?>
					<span class="allmode-category"><?php echo $item->category; ?></span>
					<?php } ?>

					<?php if ($item->author) { ?>
					<span class="allmode-author"><?php echo $item->author; ?></span>
					<?php } ?>

					<?php if ($item->hits) { ?>
					<span class="allmode-hits"><?php echo $item->hits; ?></span>
					<?php } ?>

					<?php if ($item->comments_count) { ?>
					<span class="allmode-comments"><?php echo $item->comments_count; ?></span>
					<?php } ?>

					<?php if ($item->rating_value) { ?>
					<span class="allmode-rating"><?php echo $item->rating_value; ?></span>
					<?php } ?>

				</div>
				<?php } ?>

				<?php if ($item->text) { ?>
				<div class="allmode-text"><?php echo $item->text; ?></div>
				<?php } ?>

			</div>

			<a href="<?php echo $item->link; ?>" class="allmode-readmore"></a>

		</li>

	<?php } ?>

</ul>
<?php } ?>