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


class PlgSystemDummyContentHelperDiacritics
{
	var $list = array();
	var $type = '';

	public function setType($diacritics)
	{
		$this->type = $diacritics;
	}

	public function replace(&$string)
	{
		if (!$diacritics = $this->getDiacritics())
		{
			return;
		}

		$string = preg_replace_callback(
			'#(?:' . implode('|', array_keys($diacritics)) . ')#',
			function ($match) use ($diacritics)
			{
				$char = $match['0'];
				if (rand(0, 4))
				{
					return $char;
				}

				return $diacritics[$char][array_rand($diacritics[$char])];
			},
			$string
		);
	}

	public function getDiacritics()
	{
		$this->type = trim(preg_replace('#[^a-z0-9]#', '', strtolower($this->type)));

		if (isset($this->list[$this->type]))
		{
			return $this->list[$this->type];
		}

		$list = $this->getList();

		if (!isset($list[$this->type]))
		{
			return false;
		}

		$diacritics = array();
		foreach ($list[$this->type] as $diacritic)
		{
			$key = $diacritic['0'];
			if (!isset($diacritics[$key]))
			{
				$diacritics[$key] = array();
			}

			$diacritics[$key][] = $diacritic['1'];

			if (isset($diacritic['2']))
			{
				$key = strtoupper($key);
				if (!isset($diacritics[$key]))
				{
					$diacritics[$key] = array();
				}

				$diacritics[$key][] = $diacritic['2'];
			}
		}
		$this->list[$this->type] = $diacritics;

		return $this->list[$this->type];
	}

	public function getList()
	{
		// Character sets taken from typeit.org
		return array(
			'czech' => array(
				array('a', '&#x00E1;', '&#x00C1;'),
				array('c', '&#x010D;', '&#x010C;'),
				array('d', '&#x010F;', '&#x010E;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('e', '&#x011B;', '&#x011A;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('n', '&#x0148;', '&#x0147;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('r', '&#x0159;', '&#x0158;'),
				array('s', '&#x0161;', '&#x0160;'),
				array('t', '&#x0165;', '&#x0164;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('u', '&#x016F;', '&#x016E;'),
				array('y', '&#x00FD;', '&#x00DD;'),
				array('z', '&#x017E;', '&#x017D;'),
			),

			'danish' => array(
				array('a', '&#x00E5;', '&#x00C5;'),
				array('ae', '&#x00E6;', '&#x00C6;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('o', '&#x00F8;', '&#x00D8;'),
			),

			'dutch' => array(
				array('e', '&#x00E9;', '&#x00C9;'),
				array('e', '&#x00EB;', '&#x00CB;'),
				array('i', '&#x00EF;', '&#x00CF;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('u', '&#x00FC;', '&#x00DC;'),
			),

			'esperanto' => array(
				array('c', '&#x0109;', '&#x0108;'),
				array('g', '&#x011D;', '&#x011C;'),
				array('h', '&#x0125;', '&#x0124;'),
				array('j', '&#x0135;', '&#x0134;'),
				array('s', '&#x015D;', '&#x015C;'),
				array('u', '&#x016D;', '&#x016C;'),
			),

			'finnish' => array(
				array('a', '&#x00E4;', '&#x00C4;'),
				array('a', '&#x00E5;', '&#x00C5;'),
				array('o', '&#x00F6;', '&#x00D6;'),
			),

			'french' => array(
				array('a', '&#x00E0;', '&#x00C0;'),
				array('a', '&#x00E2;', '&#x00C2;'),
				array('ae', '&#x00E6;', '&#x00C6;'),
				array('c', '&#x00E7;', '&#x00C7;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('e', '&#x00E8;', '&#x00C8;'),
				array('e', '&#x00EA;', '&#x00CA;'),
				array('e', '&#x00EB;', '&#x00CB;'),
				array('i', '&#x00EF;', '&#x00CF;'),
				array('i', '&#x00EE;', '&#x00CE;'),
				array('o', '&#x00F4;', '&#x00D4;'),
				array('oe', '&#x0153;', '&#x0152;'),
				array('u', '&#x00F9;', '&#x00D9;'),
				array('u', '&#x00FB;', '&#x00DB;'),
				array('u', '&#x00FC;', '&#x00DC;'),
				array('y', '&#x00FF;', '&#x0178;'),
			),

			'german' => array(
				array('a', '&#x00E4;', '&#x00C4;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('u', '&#x00FC;', '&#x00DC;'),
			),

			'hungarian' => array(
				array('a', '&#x00E1;', '&#x00C1;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('o', '&#x0151;', '&#x0150;'),
				array('u', '&#x00FC;', '&#x00DC;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('u', '&#x0171;', '&#x0170;'),
			),

			'icelandic' => array(
				array('a', '&#x00E1;', '&#x00C1;'),
				array('ae', '&#x00E6;', '&#x00C6;'),
				array('eth', '&#x00F0;', '&#x00D0;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('y', '&#x00FD;', '&#x00DD;'),
			),

			'italian' => array(
				array('a', '&#x00E0;', '&#x00C0;'),
				array('e', '&#x00E8;', '&#x00C8;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('i', '&#x00EC;', '&#x00CC;'),
				array('o', '&#x00F2;', '&#x00D2;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('u', '&#x00F9;', '&#x00D9;'),
			),

			'maori' => array(
				array('a', '&#x0101;', '&#x0100;'),
				array('e', '&#x0113;', '&#x0112;'),
				array('i', '&#x012B;', '&#x012A;'),
				array('o', '&#x014D;', '&#x014C;'),
				array('u', '&#x016B;', '&#x016A;'),
			),

			'norwegian' => array(
				array('a', '&#x00E5;', '&#x00C5;'),
				array('ae', '&#x00E6;', '&#x00C6;'),
				array('a', '&#x00E2;', '&#x00C2;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('e', '&#x00E8;', '&#x00C8;'),
				array('e', '&#x00EA;', '&#x00CA;'),
				array('o', '&#x00F8;', '&#x00D8;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('o', '&#x00F2;', '&#x00D2;'),
				array('o', '&#x00F4;', '&#x00D4;'),
			),

			'polish' => array(

				array('a', '&#x0105;', '&#x0104;'),
				array('c', '&#x0107;', '&#x0106;'),
				array('e', '&#x0119;', '&#x0118;'),
				array('l', '&#x0142;', '&#x0141;'),
				array('n', '&#x0144;', '&#x0143;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('s', '&#x015B;', '&#x015A;'),
				array('z', '&#x017A;', '&#x0179;'),
				array('z', '&#x017C;', '&#x017B;'),
			),

			'portuguese' => array(
				array('a', '&#x00E3;', '&#x00C3;'),
				array('a', '&#x00E1;', '&#x00C1;'),
				array('a', '&#x00E2;', '&#x00C2;'),
				array('a', '&#x00E0;', '&#x00C0;'),
				array('c', '&#x00E7;', '&#x00C7;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('e', '&#x00EA;', '&#x00CA;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('o', '&#x00F5;', '&#x00D5;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('o', '&#x00F4;', '&#x00D4;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('u', '&#x00FC;', '&#x00DC;'),
			),

			'romanian' => array(
				array('a', '&#x0103;', '&#x0102;'),
				array('a', '&#x00E2;', '&#x00C2;'),
				array('i', '&#x00EE;', '&#x00CE;'),
				array('s', '&#x0219;', '&#x0218;'),
				array('s', '&#x015F;', '&#x015E;'),
				array('t', '&#x0163;', '&#x0162;'),
				array('t', '&#x021B;', '&#x021A;'),
			),

			'russian' => array(
				array('a', '&#x0430;', '&#x0410;'),
				array('b', '&#x0431;', '&#x0411;'),
				array('v', '&#x0432;', '&#x0412;'),
				array('g', '&#x0433;', '&#x0413;'),
				array('d', '&#x0434;', '&#x0414;'),
				array('ye', '&#x0435;', '&#x0415;'),
				array('yo', '&#x0451;', '&#x0401;'),
				array('zh', '&#x0436;', '&#x0416;'),
				array('z', '&#x0437;', '&#x0417;'),
				array('i', '&#x0438;', '&#x0418;'),
				array('j', '&#x0439;', '&#x0419;'),
				array('k', '&#x043A;', '&#x041A;'),
				array('l', '&#x043B;', '&#x041B;'),
				array('m', '&#x043C;', '&#x041C;'),
				array('n', '&#x043D;', '&#x041D;'),
				array('o', '&#x043E;', '&#x041E;'),
				array('p', '&#x043F;', '&#x041F;'),
				array('r', '&#x0440;', '&#x0420;'),
				array('s', '&#x0441;', '&#x0421;'),
				array('t', '&#x0442;', '&#x0422;'),
				array('u', '&#x0443;', '&#x0423;'),
				array('f', '&#x0444;', '&#x0424;'),
				array('h', '&#x0445;', '&#x0425;'),
				array('c', '&#x0446;', '&#x0426;'),
				array('ch', '&#x0447;', '&#x0427;'),
				array('sh', '&#x0448;', '&#x0428;'),
				array('shch', '&#x0449;', '&#x0429;'),
				array('y', '&#x044B;', '&#x042B;'),
				array('e', '&#x044D;', '&#x042D;'),
				array('yu', '&#x044E;', '&#x042E;'),
				array('ya', '&#x044F;', '&#x042F;'),
			),

			'spanish' => array(
				array('a', '&#x00E1;', '&#x00C1;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('n', '&#x00F1;', '&#x00D1;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('u', '&#x00FC;', '&#x00DC;'),
			),

			'swedish' => array(
				array('a', '&#x00E4;', '&#x00C4;'),
				array('a', '&#x00E5;', '&#x00C5;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('o', '&#x00F6;', '&#x00D6;'),
			),

			'turkish' => array(
				array('c', '&#x00E7;', '&#x00C7;'),
				array('g', '&#x011F;', '&#x011E;'),
				array('i', '&#x0131;', 'I'),
				array('i', '&#x0130;'),
				array('i', '&#x0131;', '&#x0130;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('s', '&#x015F;', '&#x015E;'),
				array('u', '&#x00FC;', '&#x00DC;'),
			),

			'welsh' => array(
				array('a', '&#x00E2;', '&#x00C2;'),
				array('e', '&#x00EA;', '&#x00CA;'),
				array('i', '&#x00EE;', '&#x00CE;'),
				array('o', '&#x00F4;', '&#x00D4;'),
				array('u', '&#x00FB;', '&#x00DB;'),
				array('w', '&#x0175;', '&#x0174;'),
				array('y', '&#x0177;', '&#x0176;'),
				array('a', '&#x00E4;', '&#x00C4;'),
				array('e', '&#x00EB;', '&#x00CB;'),
				array('i', '&#x00EF;', '&#x00CF;'),
				array('o', '&#x00F6;', '&#x00D6;'),
				array('u', '&#x00FC;', '&#x00DC;'),
				array('w', '&#x1E85;', '&#x1E84;'),
				array('y', '&#x00FF;', '&#x0178;'),
				array('a', '&#x00E1;', '&#x00C1;'),
				array('e', '&#x00E9;', '&#x00C9;'),
				array('i', '&#x00ED;', '&#x00CD;'),
				array('o', '&#x00F3;', '&#x00D3;'),
				array('u', '&#x00FA;', '&#x00DA;'),
				array('w', '&#x1E83;', '&#x1E82;'),
				array('y', '&#x00FD;', '&#x00DD;'),
				array('a', '&#x00E0;', '&#x00C0;'),
				array('e', '&#x00E8;', '&#x00C8;'),
				array('i', '&#x00EC;', '&#x00CC;'),
				array('o', '&#x00F2;', '&#x00D2;'),
				array('u', '&#x00F9;', '&#x00D9;'),
				array('w', '&#x1E81;', '&#x1E80;'),
				array('y', '&#x1EF3;', '&#x1EF2;'),
			),
		);
	}
}
