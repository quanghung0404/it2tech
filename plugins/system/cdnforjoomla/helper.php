<?php
/**
 * Plugin Helper File
 *
 * @package         CDN for Joomla!
 * @version         4.0.5PRO
 *
 * @author          Peter van Westen <peter@nonumber.nl>
 * @link            http://www.nonumber.nl
 * @copyright       Copyright © 2015 NoNumber All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

require_once JPATH_PLUGINS . '/system/nnframework/helpers/functions.php';
require_once JPATH_PLUGINS . '/system/nnframework/helpers/protect.php';

NNFrameworkFunctions::loadLanguage('plg_system_cdnforjoomla');

/**
 * Plugin that replaces media urls with CDN urls
 */
class PlgSystemCDNforJoomlaHelper
{
	var $params = null;
	var $pass = false;

	public function __construct(&$params)
	{
		$this->params = $params;

		$hascdn = preg_replace(array('#^.*\://#', '#/$#'), '', $this->params->cdn);

		// return if cdn field has no value
		if (!$hascdn)
		{
			return;
		}

		$this->domain        = 0;
		$this->params->https = (
			(!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) != 'off')
			|| (isset($_SERVER['SSL_PROTOCOL']))
			|| (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
			|| (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) == 'https')
		);

		$this->params->tag_attribs = $this->getSearchTagAttributes();

		$this->initSetList();

		if (empty($this->params->sets))
		{
			return;
		}

		$this->pass = true;
	}

	function initSetList()
	{
		$this->params->sets = array();

		$nr_of_sets = 5;
		for ($i = 1; $i <= $nr_of_sets; $i++)
		{
			$this->params->sets[] = $this->initSetParams($i);
		}

		$this->removeEmptySets();
	}

	function removeEmptySets()
	{
		foreach ($this->params->sets as $i => $set)
		{
			if (!empty($set) && !empty($set->searches))
			{
				continue;
			}

			unset($this->params->sets[$i]);
		}
	}

	function initSetParams($setid = 1)
	{
		$setid = ($setid <= 1) ? '' : '_' . (int) $setid;

		if ($setid && (!isset($this->params->{'use_extra' . $setid}) || !$this->params->{'use_extra' . $setid}))
		{
			return false;
		}

		$filetypes  = str_replace('-', ',', implode(',', $this->params->{'filetypes' . $setid}));
		$filetypes  = explode(',', $filetypes);
		$extratypes = preg_replace('#\s#', '', $this->params->{'extratypes' . $setid});

		if ($extratypes)
		{
			$filetypes = array_merge($filetypes, explode(',', $extratypes));
		}

		$filetypes = array_unique(array_diff($filetypes, array('', 'x')));

		if (empty($filetypes))
		{
			return false;
		}

		$params            = new stdClass;
		$params->filetypes = $filetypes;

		$params->enable_in_scripts = $this->params->{'enable_in_scripts' . $setid};

		$params->protocol = 'http://';

		$params->enable_https = $this->params->{'enable_https' . $setid};
		if ($this->params->https && !$params->enable_https)
		{
			return $params;
		}

		$keep_https = $params->enable_https ? $this->params->{'keep_https' . $setid} : 0;
		if (
			$params->enable_https == 2
			|| ($this->params->https && $keep_https)
		)
		{
			$params->protocol = 'https://';
		}
		$params->enable_versioning = $this->params->{'enable_versioning' . $setid};

		$params->cdn         = preg_replace('#/$#', '', $this->params->{'cdn' . $setid});
		$params->ignorefiles = explode(',', str_replace(array('\n', ' '), array(',', ''), $this->params->{'ignorefiles' . $setid}));

		$root         = preg_replace(array('#^/#', '#/$#'), '', $this->params->{'root' . $setid}) . '/';
		$params->root = preg_replace('#^/#', '', $root);

		$params->searches    = array();
		$params->js_searches = array();

		$this->params->set = $params;

		$this->setFiletypeSearches();
		$this->setCdnPaths();

		return $this->params->set;
	}

	public function onAfterRender()
	{
		if (!$this->pass)
		{
			return;
		}

		$html = JResponse::getBody();

		$this->replace($html);
		$this->cleanLeftoverJunk($html);

		JResponse::setBody($html);
	}

	function replace(&$string)
	{
		if (is_array($string))
		{
			$this->replaceInList($string);

			return;
		}

		$string_array = $this->protectString($string);
		foreach ($string_array as $i => &$substring)
		{
			if ($i % 2)
			{
				continue;
			}

			$string_array[$i] = $this->replaceBySetList($substring);
		}

		$string = implode('', $string_array);
	}

	function replaceInList(&$array)
	{
		foreach ($array as &$val)
		{
			$this->replace($val);
		}
	}

	function replaceBySetList($string)
	{
		foreach ($this->params->sets as $set)
		{
			$this->params->set = $set;
			$this->replaceBySet($string);
		}

		return $string;
	}

	function replaceBySet(&$string)
	{
		if ($this->params->https && !$this->params->set->enable_https)
		{
			return;
		}

		$this->replaceBySearchList($string, $this->params->set->searches);

		if (!empty($this->params->set->enable_in_scripts) && strpos($string, '<script') !== false)
		{
			$this->replaceInJavascript($string);
		}
	}

	function replaceInJavascript(&$string)
	{
		$regex = '#<script(?:\s+(language|type)\s*=[^>]*)?>.*?</script>#si';

		preg_match_all($regex, $string, $stringparts, PREG_SET_ORDER);

		if (empty($stringparts))
		{
			return;
		}

		foreach ($stringparts as $stringpart)
		{
			$this->replaceInJavascriptStringPart($string, $stringpart);
		}
	}

	function replaceInJavascriptStringPart(&$string, $stringpart)
	{
		$newstr = $stringpart['0'];

		if (!$this->replaceBySearchList($newstr, $this->params->set->js_searches))
		{
			return;
		}

		$string = str_replace($stringpart['0'], $newstr, $string);
	}

	function replaceBySearchList(&$string, &$searches)
	{
		$changed = 0;

		foreach ($searches as $search)
		{
			$changed = $this->replaceBySearch($string, $search);
		}

		return $changed;
	}

	function replaceBySearch(&$string, &$search)
	{
		preg_match_all($search, $string, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return false;
		}

		$changed = false;

		foreach ($matches as $match)
		{
			list($file, $query) = $this->getFileParts($match['3']);

			if (!$file || $this->fileIsIgnored($file))
			{
				continue;
			}

			if ($this->params->set->enable_versioning && file_exists(JPATH_SITE . '/' . $file))
			{
				$query[] = filemtime(JPATH_SITE . '/' . $file);
			}

			$file = $this->getCdnUrl($file)
				. '/' . $this->addQueryToFile($file, $query);

			$string = str_replace(
				$match['0'],
				$match['1'] . $file . $match['4'],
				$string
			);

			$changed = true;
		}

		return $changed;
	}

	function getFileParts($file)
	{
		$file = trim($file);

		if (!$file)
		{
			return array(null, null);
		}

		if (strpos($file, '?') === false)
		{
			return array($file, null);
		}

		list($file, $query) = explode('?', $file, 2);
		$query = explode('&', $query);

		return array($file, $query);
	}

	function addQueryToFile($file, $query = array())
	{
		$file = trim($file);

		if (empty($query))
		{
			return $file;
		}

		return $file . '?' . implode('&', $query);
	}

	function fileIsIgnored($file)
	{
		foreach ($this->params->set->ignorefiles as $ignore)
		{
			if ($ignore && (strpos($file, $ignore) !== false || strpos(htmlentities($file), $ignore) !== false))
			{
				return true;
			}
		}

		return false;
	}

	/*
	 * Searches are replaced by:
	 * '\1http(s)://' . $this->params->cdn . '/\3\4'
	 * \2 is used to reference the possible starting quote
	 */
	function setFiletypeSearches()
	{
		if (empty($this->params->set->filetypes))
		{
			return;
		}

		$urls = $this->getUrlList();

		foreach ($urls as $url)
		{
			$this->setSearchesByUrl($url);
		}
	}

	/*
	 * Searches are replaced by:
	 * '\1http(s)://' . $this->params->cdn . '/\3\4'
	 * \2 is used to reference the possible starting quote
	 */
	function getUrlList()
	{
		// Domain url or root path
		$url = preg_quote(str_replace('https://', 'http://', JUri::root()), '#');
		$url .= '|' . preg_quote(str_replace(array('http://', 'https://'), '//', JUri::root()), '#');

		if ($this->params->set->enable_https)
		{
			$url .= '|' . preg_quote(str_replace('http://', 'https://', JUri::root()), '#');
		}

		if (JUri::root(1))
		{
			$url .= '|' . preg_quote(JUri::root(1) . '/', '#');
		}

		$filetypes = implode('|', $this->params->set->filetypes);
		$root      = preg_quote($this->params->set->root, '#');

		$urls = array();

		// Absolute path
		$urls[] = '(?:' . $url . ')' . $root . '([^ \?QUOTES]+\.(?:' . $filetypes . ')(?:\?[^QUOTES]*)?)';
		// Relative path
		$urls[] = 'LSLASH' . $root . '([a-z0-9-_]+/[^ \?QUOTES]+\.(?:' . $filetypes . ')(?:\?[^QUOTES]*)?)';
		// Relative path - file in root
		$urls[] = 'LSLASH' . $root . '([a-z0-9-_]+[^ \?\/QUOTES]+\.(?:' . $filetypes . ')(?:\?[^QUOTES]*)?)';

		return $urls;
	}

	function setSearchesByUrl($url)
	{

		$url_regex = '\s*' . str_replace('QUOTES', '"\'', $url) . '\s*';

		if ($this->params->set->enable_in_scripts)
		{
			$url_regex_js                     = str_replace('LSLASH', '', $url_regex);
			$this->params->set->js_searches[] = '#((["\']))' . $url_regex_js . '(["\'])#i'; // "..."
		}

		$url_regex                 = str_replace('LSLASH', '/?', $url_regex);
		$url_regex_can_have_spaces = str_replace('[^ ', '[^', $url_regex);

		$this->params->set->searches[] = '#((?:' . $this->params->tag_attribs . ')\s*(["\']))' . $url_regex_can_have_spaces . '(\2)#i'; // attrib="..."
		$this->params->set->searches[] = '#((?:' . $this->params->tag_attribs . ')())' . $url_regex . '([\s|>])#i'; // attrib=...
		$this->params->set->searches[] = '#(url\(\s*((?:["\'])?))' . $url_regex_can_have_spaces . '(\2\s*\))#i'; // url(...) or url("...")

		// add ')' to the no quote checks
		$url_regex                     = '\s*' . str_replace('QUOTES', '"\'\)', $url) . '\s*';
		$this->params->set->searches[] = '#(url\(\s*())' . $url_regex . '(\s*\))#i'; // url(...)
	}

	function getSearchTagAttributes()
	{
		$attributes = array(
			'href=',
			'src=',
			'data-src=',
			'data-srcset=',
			'data-original=',
			'longdesc=',
			'poster=',
			'@import',
			'name="movie" value=',
			'property="og:image" content=',
			'TileImage" content=',
			'rel="{\'link\':',
		);

		return str_replace(array('"', '=', ' '), array('["\']?', '\s*=\s*', '\s+'), implode('|', $attributes));
	}

	function getCdnUrl($file)
	{
		$cdns = $this->params->set->cdns;

		if (count($cdns) > 1)
		{
			// Make sure a file is always served from the same cdn server to leverage browser caching
			$cdns = array($cdns[hexdec(substr(hash('md2', $file), -4)) % count($cdns)]);
		}

		return $this->params->set->protocol . $cdns['0'];
	}

	function setCdnPaths()
	{
		$this->params->set->cdns = explode(',', $this->params->set->cdn);
		foreach ($this->params->set->cdns as $i => $cdn)
		{
			$cdn = preg_replace('#^.*\://#', '', trim($cdn));
			$this->replaceDomainVars($cdn);
			$this->params->set->cdns[$i] = $cdn;
		}
	}

	function replaceDomainVars(&$cdn)
	{
		preg_match_all('#(\.?)\{([^\}]*)\}(\.?)#', $cdn, $matches, PREG_SET_ORDER);

		if (empty($matches))
		{
			return;
		}

		foreach ($matches as $match)
		{
			$cdn = str_replace($match['0'], $this->getDomainVar($match), $cdn);
		}
	}

	function getDomainVar($match)
	{
		$domain = $this->getDomain();

		if (isset($domain->{$match['2']}) && $domain->{$match['2']})
		{
			return $match['1'] . $domain->{$match['2']} . $match['3'];
		}

		if ($match['1'] && $match['3'])
		{
			return '.';
		}

		return '';
	}

	var $slds = array('a', 'ab', 'abo', 'ac', 'ad', 'adm', 'adv', 'aero', 'agr', 'ah', 'alt', 'am', 'amur', 'arq', 'art', 'arts', 'asn', 'assn', 'asso', 'ato', 'av', 'b', 'bbs', 'bc', 'bd', 'bel', 'bio', 'bir', 'biz', 'bj', 'bl', 'blog', 'bmd', 'c', 'cat', 'cbg', 'cc', 'chel', 'cim', 'city', 'ck', 'club', 'cn', 'cng', 'cnt', 'co', 'com', 'conf', 'coop', 'cq', 'cr', 'cri', 'cv', 'cym', 'd', 'db', 'de', 'dn', 'dni', 'dp', 'dr', 'du', 'e', 'ebiz', 'ecn', 'ed', 'edu', 'eng', 'ens', 'es', 'esp', 'est', 'et', 'etc', 'eti', 'eun', 'f', 'fam', 'far', 'fed', 'fi', 'fin', 'firm', 'fj', 'flog', 'fm', 'fnd', 'fot', 'fr', 'fs', 'fst', 'g', 'g12', 'game', 'gd', 'gda', 'geek', 'gen', 'ggf', 'go', 'gob', 'gok', 'gon', 'gop', 'gos', 'gouv', 'gov', 'govt', 'gp', 'gr', 'grp', 'gs', 'gub', 'gv', 'gx', 'gz', 'h', 'ha', 'hb', 'he', 'hi', 'hl', 'hn', 'hs', 'i', 'id', 'idf', 'idn', 'idv', 'if', 'imb', 'imt', 'in', 'inca', 'ind', 'inf', 'info', 'ing', 'int', 'intl', 'iq', 'ir', 'isa', 'isla', 'it', 'its', 'iwi', 'jar', 'jeju', 'jet', 'jl', 'jobs', 'jor', 'js', 'jus', 'jx', 'k', 'k12', 'kchr', 'kg', 'kh', 'khv', 'kids', 'kiev', 'km', 'komi', 'kr', 'ks', 'kv', 'kzn', 'l', 'law', 'lea', 'lel', 'lg', 'ln', 'lodz', 'lp', 'ltd', 'lviv', 'm', 'ma', 'mari', 'mat', 'mb', 'me', 'med', 'mi', 'mil', 'mk', 'mob', 'mobi', 'mod', 'mpm', 'ms', 'msk', 'muni', 'mus', 'n', 'name', 'nat', 'nb', 'ne', 'nel', 'net', 'news', 'nf', 'ngo', 'nhs', 'nic', 'nis', 'nl', 'nls', 'nm', 'nnov', 'nom', 'nome', 'not', 'nov', 'ns', 'nsk', 'nsn', 'nt', 'ntr', 'nu', 'nw', 'nx', 'o', 'od', 'odo', 'og', 'om', 'omsk', 'on', 'or', 'org', 'orgn', 'ov', 'p', 'pb', 'pe', 'per', 'perm', 'pix', 'pl', 'plc', 'plo', 'pol', 'pp', 'ppg', 'prd', 'priv', 'pro', 'prof', 'psc', 'psi', 'ptz', 'pub', 'publ', 'pwr', 'qc', 'qh', 'qsl', 'r', 're', 'rec', 'red', 'res', 'rg', 'rnd', 'rnrt', 'rns', 'rnu', 'rs', 'rv', 's', 'sa', 'sc', 'sch', 'sci', 'scot', 'sd', 'sec', 'sh', 'sk', 'sld', 'slg', 'sn', 'soc', 'spb', 'srv', 'stv', 'sumy', 'sx', 't', 'te', 'tel', 'test', 'tj', 'tm', 'tmp', 'tom', 'trd', 'tsk', 'tula', 'tur', 'tuva', 'tv', 'tver', 'tw', 'u', 'udm', 'unbi', 'univ', 'unmo', 'unsa', 'untz', 'unze', 'vet', 'vlog', 'vn', 'vrn', 'w', 'war', 'waw', 'web', 'wiki', 'wroc', 'www', 'x', 'xj', 'xz', 'y', 'yk', 'yn', 'z', 'zj', 'zlg', 'zp', 'zt');

	function getDomain()
	{
		if ($this->domain)
		{
			return $this->domain;
		}

		$this->domain             = new stdClass;
		$this->domain->fulldomain = JUri::getInstance()->getHost();
		$this->domain->subdomain  = '';
		$this->domain->domain     = $this->domain->fulldomain;
		$this->domain->extension  = '';

		$parts = array_reverse(explode('.', $this->domain->fulldomain));

		if (count($parts) < 2)
		{
			return $this->domain;
		}

		$this->domain->extension = array_shift($parts);

		if (in_array($parts['0'], $this->slds))
		{
			$this->domain->extension = array_shift($parts) . '.' . $this->domain->extension;
		}

		$subs = 0;

		while (!empty($parts))
		{
			$this->domain->{str_repeat('sub', $subs++) . 'domain'} = array_shift($parts);
		}

		$this->domain->subdomain = $this->domain->subdomain ? $this->domain->subdomain : 'www';

		return $this->domain;
	}

	/**
	 * Just in case you can't figure the method name out: this cleans the left-over junk
	 */
	function cleanLeftoverJunk(&$string)
	{
		$string = str_replace(array('{nocdn}', '{/nocdn}'), '', $string);
	}

	function protectString($string)
	{
		if (NNProtect::isEditPage())
		{
			$string = preg_replace('#(<' . 'form [^>]*(id|name)="(adminForm|postform)".*?</form>)#si', '{nocdn}\1{/nocdn}', $string);
		}

		if (strpos($string, '{nocdn}') === false || strpos($string, '{/nocdn}') === false)
		{
			$string = str_replace(array('{nocdn}', '{/nocdn}'), '', $string);

			return array($string);
		}

		$string = str_replace(array('{nocdn}', '{/nocdn}'), '[[CDN_SPLIT]]', $string);

		return explode('[[CDN_SPLIT]]', $string);
	}
}
