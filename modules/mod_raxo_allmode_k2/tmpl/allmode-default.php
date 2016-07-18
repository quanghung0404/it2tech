<?php
/**
 * =============================================================
 * RAXO All-mode K2 J3.x - Template
 * -------------------------------------------------------------
 * @package		RAXO All-mode K2
 * @subpackage	All-mode Default Template
 * @copyright	Copyright (C) 2009-2014 RAXO Group
 * @license		GNU General Public License v2.0
 * 				http://www.gnu.org/licenses/gpl-2.0.html
 * @link		http://www.raxo.org
 * =============================================================
 */


// no direct access
defined('_JEXEC') or die;

// add template CSS
JHtml::stylesheet('modules/mod_raxo_allmode_k2/tmpl/allmode-default/allmode-default.css');
?>


<?php if ($toplist) { ?>
<div class="allmode-topbox">
<?php																			// All-mode TOP Items Output
foreach ($toplist as $item) { ?>

	<div class="allmode-topitem">

		<?php if ($item->image) { ?>
		<div class="allmode-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->date || $item->hits || $item->comments_count || $item->rating) { ?>
		<div class="allmode-details">

			<?php if ($item->date) { ?>
			<span class="allmode-date"><?php echo $item->date; ?></span>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<span class="allmode-hits"><?php echo $item->hits; ?></span>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<span class="allmode-comments"><a href="<?php echo $item->comments_link; ?>"><?php echo $item->comments_count; ?></a></span>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<span class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h3 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h3>
		<?php } ?>

		<?php if ($item->category || $item->author) { ?>
		<div class="allmode-info">

			<?php if ($item->category) { ?>
			in <span class="allmode-category"><?php echo $item->category; ?></span>
			<?php } ?>

			<?php if ($item->author) { ?>
			by <span class="allmode-author"><?php echo $item->author; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-text"><?php echo $item->text; ?>

			<?php if ($item->readmore) { ?>
			<span class="allmode-readmore"><?php echo $item->readmore; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

	</div>

<?php } ?>
</div>
<?php } ?>


<?php if ($list) { ?>
<div class="allmode-itemsbox">
<?php																			// All-mode Items Output
foreach ($list as $item) { ?>

	<div class="allmode-item">

		<?php if ($item->image) { ?>
		<div class="allmode-img"><?php echo $item->image; ?></div>
		<?php } ?>

		<?php if ($item->date || $item->hits || $item->comments_count || $item->rating) { ?>
		<div class="allmode-details">

			<?php if ($item->date) { ?>
			<span class="allmode-date"><?php echo $item->date; ?></span>
			<?php } ?>

			<?php if ($item->hits) { ?>
			<span class="allmode-hits"><?php echo $item->hits; ?></span>
			<?php } ?>

			<?php if ($item->comments_count) { ?>
			<span class="allmode-comments"><a href="<?php echo $item->comments_link; ?>"><?php echo $item->comments_count; ?></a></span>
			<?php } ?>

			<?php if ($item->rating) { ?>
			<span class="allmode-rating" title="<?php echo @$item->rating_value; ?>"><?php echo $item->rating; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ($item->title) { ?>
		<h4 class="allmode-title"><a href="<?php echo $item->link; ?>"><?php echo $item->title; ?></a></h4>
		<?php } ?>

		<?php if ($item->category || $item->author) { ?>
		<div class="allmode-info">

			<?php if ($item->category) { ?>
			in <span class="allmode-category"><?php echo $item->category; ?></span>
			<?php } ?>

			<?php if ($item->author) { ?>
			by <span class="allmode-author"><?php echo $item->author; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

		<?php if ($item->text) { ?>
		<div class="allmode-text"><?php echo $item->text; ?>

			<?php if ($item->readmore) { ?>
			<span class="allmode-readmore"><?php echo $item->readmore; ?></span>
			<?php } ?>

		</div>
		<?php } ?>

	</div>

<?php } ?>
</div>
<?php } ?>