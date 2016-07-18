<?php
/**
 * Plugin Helper File: Text
 *
 * @package         Dummy Content
 * @version         2.1.2PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemDummyContentHelperText
{
	var $helpers = array();

	public function __construct()
	{
		require_once __DIR__ . '/helpers.php';
		$this->helpers = PlgSystemDummyContentHelpers::getInstance();
	}

	/**
	 * Generates by default by random parahraphs
	 *
	 * @param  integer $count the number of paragraphs to create
	 *
	 * @return string
	 */
	public function paragraphs($count = 5)
	{
		if (!$count)
		{
			return '';
		}

		$arr = array();

		for ($i = 0; $i < $count; $i++)
		{
			$paragraph = $this->sentences(mt_rand(2, 8), true);
			$arr[]     = trim($paragraph);
		}

		return '<p>' . trim(implode('</p>' . "\n" . '<p>', $arr)) . '</p>';
	}

	/**
	 * Create by default five sentices
	 *
	 * @param  integer $count the number of sentences to create
	 *
	 * @return string
	 */
	public function sentences($count = 5)
	{
		if (!$count)
		{
			return '';
		}

		$sentences = array();

		for ($i = 0; $i < $count; $i++)
		{
			if ($this->helpers->get('wordlist')->isSentenceList())
			{
				$sentences[] = $this->words(1, true);
				continue;
			}

			//Randomly add commas to the sentence in logical places
			$rand = mt_rand(0, 3);
			switch (true)
			{
				case ($rand === 2):
					$sentence = $this->words(mt_rand(3, 8), true);
					if (!in_array(substr(trim($sentence), -1), array('.', ',', ';', '!', '?')))
					{
						$sentence .= ',';
					}
					$sentence .= ' ' . $this->words(mt_rand(4, 12), true);
					break;

				case ($rand === 3):
					$sentence = $this->words(mt_rand(2, 4), true);
					if (!in_array(substr(trim($sentence), -1), array('.', ',', ';', '!', '?')))
					{
						$sentence .= ',';
					}

					$sentence .= ' ' . $this->words(mt_rand(3, 4), true);
					if (!in_array(substr(trim($sentence), -1), array('.', ',', ';', '!', '?')))
					{
						$sentence .= ',';
					}
					$sentence .= ' ' . $this->words(mt_rand(3, 8), true);
					break;

				default:
					$sentence = $this->words(mt_rand(5, 20), true);

					break;
			}

			$sentence = JString::ucfirst($sentence);

			//Ocassionally use a semi-colon or exclamation mark
			if (!in_array(substr($sentence, -1), array('.', ',', ';', '!', '?')))
			{
				switch (mt_rand(0, 10))
				{
					case 0:
						$sentence .= ';';
						break;
					case 1:
						$sentence .= '!';
						break;
					default:
						$sentence .= '.';
				}
			}

			$sentences[] = $sentence;
		}

		$sentences = trim(implode(' ', $sentences));

		// Make sure a semicolon is not the last character
		if (in_array(substr($sentences, -1), array(',', ';')))
		{
			$sentences = substr($sentences, 0, -1) . '.';
		}

		return $sentences;
	}

	/**
	 * Generate by default 5 random words
	 *
	 * @param  integer $count the number of words to create
	 *
	 * @return string
	 */
	public function words($count = 5, $finish_sentence = false, $use_diacritics = true)
	{
		if (!$count)
		{
			return '';
		}

		$wordlist = $this->helpers->get('wordlist')->getList();

		$words = '';
		for ($i = 0; $i < $count; $i++)
		{
			$word = $wordlist[mt_rand(0, count($wordlist) - 1)];

			// Correct stuff for list items containing multiple words
			if (strpos($word, ' ') !== false)
			{
				$word_parts = explode(' ', $word);
				$i += count($word_parts) - 1;
				if ($i >= $count && !$finish_sentence)
				{
					$diff = ($i - $count) + 1;
					$word = implode(' ', array_slice($word_parts, 0, count($word_parts) - $diff));
				}
			}
			$words .= $word . ' ';
		}

		if ($use_diacritics)
		{
			$this->helpers->get('diacritics')->replace($words);
		}

		return trim($words);
	}

	/**
	 * Generate by default five capitilized words
	 *
	 * @param  integer $count the number of words to create
	 *
	 * @return string
	 */
	public function title($count = 5)
	{
		if (!$count)
		{
			return '';
		}

		$title = $this->words($count, true);

		return ucwords($title);
	}

	/**
	 * Generates a title inside a heading element
	 *
	 * @return string
	 */
	public function heading($count = 5, $level = 1)
	{
		if (!$count)
		{
			return '';
		}

		return '<h' . (int) $level . '>' . $this->title($count) . '</h' . (int) $level . '>';
	}

	/**
	 * Generates a list of elements
	 *
	 * @return string
	 */
	public function alist($count = 0, $type = '')
	{
		$types = array('ul', 'ol');
		$type  = $type ?: $types[mt_rand(0, 1)];

		$count = ($count > 1 && $count != 'random') ? $count : mt_rand(2, 10);

		$html   = array();
		$html[] = '<' . $type . '>';
		for ($i = 0; $i < $count; $i++)
		{

			$html[] = '<li>' . $this->words(mt_rand(3, 10), true) . '</li>';
		}
		$html[] = '</' . $type . '>';

		return implode('', $html);
	}

	/**
	 * Generates fake email address
	 *
	 * @return string
	 */
	public function email($count = 0)
	{
		$endings = array('com', 'net', 'org', 'co.uk', 'nl');
		if (mt_rand(0, 5) === 0)
		{
			$email = $this->words(1, false, false);
			if (mt_rand(0, 2) === 0)
			{
				$email .= '+';
			}
			else
			{
				$email .= '.';
			}
			$email .= $this->words(1, false, false);
		}
		else
		{
			$email = str_replace(" ", "", $this->words(mt_rand(1, 2), false, false));
		}
		$email .= '@';
		if (mt_rand(0, 3) === 0)
		{
			$email .= str_replace(" ", "-", $this->words(2, false, false));
		}
		else
		{
			$email .= str_replace(" ", "", $this->words(mt_rand(1, 2), false, false));
		}
		$email .= '.' . $endings[mt_rand(0, 3)];

		return $email;
	}

	/**
	 * Generates a kitchen sink (mixed headings/paragraphs/lists
	 *
	 * @return string
	 */
	public function kitchenSink($count = 0)
	{
		$html = array();

		$numbers = array(4, 3, 2, 1);

		$numbers = array_merge($numbers, array_fill(0, 4, 0));
		$numbers = array_slice($numbers, 0, mt_rand(3, 7), true);
		shuffle($numbers);

		$heading = 0;
		foreach ($numbers as $number)
		{
			$html[] = $this->kitchenSinkItem($heading, $number);
		}

		return implode('', $html);
	}

	public function kitchenSinkItem(&$heading, $number = 0)
	{
		$html = array();

		$heading = max(1, rand($heading - 1, $heading + 1));
		$html[]  = $this->heading(mt_rand(2, 5), $heading);

		$number = $number ?: mt_rand(1, 4);
		switch ($number)
		{
			case 1:
				$html[] = $this->paragraphs(mt_rand(1, 3));
				break;
			case 2:
				$html[] = $this->paragraphs(mt_rand(0, 1));
				$html[] = $this->alist(mt_rand(2, 6));
				$html[] = $this->paragraphs(mt_rand(0, 1));
				break;
			case 3:
				$html[] = $this->paragraphs(mt_rand(1, 2));
				$email  = $this->email();
				$html[] = '<a href="mailto:' . $email . '">' . $email . '</a>';
				break;
			case 4:
				$html[] = $this->paragraphs(mt_rand(0, 1));
				$options = (object) array(
					'width'  => mt_rand(10, 60) * 10,
					'height' => mt_rand(10, 60) * 10,
				);

				$html[] = $this->helpers->get('image')->render($options);
				$html[] = $this->paragraphs(mt_rand(0, 1));
				break;
		}

		return implode('', $html);
	}
}
