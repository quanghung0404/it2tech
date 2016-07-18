<?php
// namespace administrator\components\com_jmap\framework\seostats\services\google;
/**
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Google pagerank stats service
 * It works with a class bundle and multiple HASH algos generation
 * Embed request routine curl based
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapSeostatsServicesGooglePagerank {
	// Top level domain
	private static $PREFERED_TLD = "com";
	
	// 2 toolbar server hostnames, as found in the toolbar source code.
	private static $SERVER_HOSTS = '["toolbarqueries.google.","alt1.toolbarqueries.google."]';
	
	// 138 toolbar server top level domains, as found in the toolbar source code.
	private static $SERVER_TLDS = '["com","ae","com.af","com.ag","com.ai","am","com.ar","as","at","com.au","az","ba","com.bd","be","bg","com.bh","bi","com.bo","com.br","bs","co.bw","com.bz","ca","cd","cg","ch","ci","co.ck","cl","com.co","co.cr","com.cu","cz","de","dj","dk","dm","com.do","com.ec","ee","com.eg","es","com.et","fi","com.fj","fm","fr","co.uk","gg","com.gi","gl","gm","gr","com.gt","com.hk","hn","hr","ht","hu","co.id","ie","co.il","co.im","co.in","is","it","co.je","com.jm","jo","co.jp","co.ke","kg","co.kr","kz","li","lk","co.ls","lt","lu","lv","com.ly","co.ma","mn","ms","com.mt","mu","mw","com.mx","com.my","com.na","com.nf","com.ni","nl","no","com.np","co.nz","com.om","com.pa","com.pe","com.ph","com.pk","pl","pn","com.pr","pt","com.py","com.qa","ro","ru","rw","com.sa","sc","se","com.sg","sh","si","sk","sm","sn","com.sv","co.th","com.tj","tm","to","com.tr","tt","com.tw","com.ua","co.ug","com.uy","co.uz","com.vc","co.ve","vg","co.vi","com.vn","co.za","co.zm"]';
	
	// Service request path as found in the toolbar source code.
	private static $SERVER_PATH = "/tbr";
	
	// Request query string as found in the toolbar source code.
	private static $QUERY_STRING = "?features=Rank&client=navclient-auto&ch=%s&q=info:%s";
	
	// Google's client-specific suggestion of a prefered top level domain (as found in tb source code).
	private static $SUGGEST_TLD_URL = "https://www.google.com/searchdomaincheck?format=domain&sourceid=navclient-ff";
	
	// objects vars
	private $QUERY_URL, $URL_HASHES, $GTB_SUGESSTED_TLD, $GTB_QUERY_STRINGS, $GTB_SERVER;
	
	/**
	 * Setter function for the url key
	 *
	 * @access private
	 * @return Boolean returns true if input string validated as url, else false.
	 */
	private function setQueryURL($a) {
		$this->QUERY_URL = $a;
		$b = array (
				'jenkins' => self::GPR_jenkinsHash (),
				'jenkins2' => self::GPR_jenkinsHash2 (),
				'ie' => self::GPR_ieHash (),
				'awesome' => self::GPR_awesomeHash () 
		);
		$this->URL_HASHES = $b;
		return ( bool ) self::setQueryStrings ( $a, $b );
	}
	
	/**
	 * Prepare the hashed query strings for requests
	 *
	 * @access private
	 * @return Boolean returns true if input string validated as url, else false.
	 */
	private function setQueryStrings($a, $b) {
		$qs = array ();
		foreach ( $b as $k => $v ) { // Foreach hash key value...
			if (is_string ( $v ) && strlen ( $v ) > 0) {
				// ...format a query string.
				$qs [] = sprintf ( self::$QUERY_STRING, $v, urlencode ( $a ) );
			}
		}
		if (sizeof ( $qs ) > 0) {
			$this->GTB_QUERY_STRINGS = $qs;
			return true;
		}
		return false;
	}
	
	/**
	 * Main class responsibility, start page rank request and calculation process
	 *
	 * @access public
	 * @return int
	 */
	public function getPageRank() {
		$host = $this->GTB_SERVER ['host'] [0];
		$tld = (strlen ( $this->GTB_SUGESSTED_TLD ) > 0) ? $this->GTB_SUGESSTED_TLD : $this->PREFERED_TLD;
		$path = $this->GTB_SERVER ['path'];
		$tbUrl = 'http://' . $host . $tld . $path;
		$qStrings = $this->GTB_QUERY_STRINGS;
		
		for($i = 0; $i < 3; $i ++) {
			if (! isset ( $qStrings [$i] )) {
				break;
			}
			$PR = self::getToolbarPageRank ( $tbUrl . $qStrings [$i] );
			if ($PR === false) {
				continue;
			}
			return $PR;
		}
	}

	/**
	 * @access public
	 * @return boolean
	 */
	public function getToolbarPageRank($toolbarUrl) {
		$ret = JMapGtbRequest::_get ( $toolbarUrl );
		$pagerank = trim ( substr ( $ret, 9 ) );
		return ($this->isResultValid ( $pagerank )) ? $pagerank : false;
	}
	
	/**
	 * @access public
	 * @return boolean
	 */
	public function isResultValid($result) {
		return preg_match ( '/^[0-9]/', $result ) || $result === "";
	}
	
	/**
	 * getHash - Get a single hash key value string from object's 'URL_HASHES' array.
	 *
	 * @access public
	 */
	public function getHash($k) {
		$array = $this->URL_HASHES;
		return $array [$k];
	}
	
	/**
	 * getTbrTldSuggestion - Get Google's suggestion which top level domain to use.
	 *
	 * @access public
	 * @return Array Array containing all available Toolbar server top level domains.
	 */
	public function getTbrTldSuggestion() {
		$tmp = explode ( ".google.", JMapGtbRequest::_get ( self::$SUGGEST_TLD_URL ) );
		return isset ( $tmp [1] ) ? trim ( $tmp [1] ) : 'com';
	}
	
	/**
	 * Get the awesome HASH
	 * 
	 * @access public
	 * @return string
	 */
	public function GPR_awesomeHash() {
		if ($this->QUERY_URL) {
			return JMapGtbAwesomeHash::awesomeHash ( $this->QUERY_URL );
		} else {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
	}
	
	/**
	 * Get the jenkins HASH
	 * 
	 * @access public
	 * @return string
	 */
	public function GPR_jenkinsHash() {
		if ($this->QUERY_URL) {
			return JMapGtbJenkinsHash::jenkinsHash ( $this->QUERY_URL );
		} else {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
	}
	
	/**
	 * Get the jenkins2 HASH
	 *
	 * @access public
	 * @return string
	 */
	public function GPR_jenkinsHash2() {
		if ($this->QUERY_URL) {
			return JMapGtbJenkinsHash::jenkinsHash2 ( $this->QUERY_URL );
		} else {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
	}
	
	/**
	 * Get the ie HASH
	 *
	 * @access public
	 * @return string
	 */
	public function GPR_ieHash() {
		if ($this->QUERY_URL) {
			return JMapGtbIeHash::ieHash ( $this->QUERY_URL );
		} else {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
	}
	
	/**
	 * Initialize a new object of the class 'JMapSeostatsServicesGooglePagerank'.
	 *
	 * @access public
	 */
	public function __construct($a = NULL) {
		if (NULL === $a) {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
		$this->GTB_SERVER = array ( // setup the toolbar server vars
				"host" => JMapGtbHelper::_json_decode ( self::$SERVER_HOSTS ),
				"tld" => JMapGtbHelper::_json_decode ( self::$SERVER_TLDS ),
				"path" => self::$SERVER_PATH 
		); // setup the client preferences
		if (! in_array ( self::$PREFERED_TLD, $this->GTB_SERVER ['tld'] )) {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		} else {
			$this->GTB_SUGESSTED_TLD = self::getTbrTldSuggestion ();
		}
		$init = self::setQueryURL ( $a ); // setup the query url
		if (true !== $init) {
			throw new JMapException ( JText::_('COM_JMAP_ERROR_GTB_HASH'), 'notice' );
		}
	}
}

/**
 * JMapGtbAwesomeHash Hash a variable-length key into a 32-bit value.
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapGtbAwesomeHash extends JMapSeostatsServicesGooglePagerank {
	// hash seed, used by the "awesomeHash" algrorithm
	static $HASH_SEED = "Mining PageRank is AGAINST GOOGLE'S TERMS OF SERVICE. Yes, I'm talking to you, scammer.";
	
	/**
	 * Validates input, pass it to the hash function and return the result
	 *
	 * @access public
	 * @return string
	 */
	public static function awesomeHash($a) {
		$b = 16909125;
		for($c = 0; $c < strlen ( $a ); $c ++) {
			$b ^= (JMapGtbHelper::charCodeAt ( self::$HASH_SEED, ($c % 87) )) ^ (JMapGtbHelper::charCodeAt ( $a, $c ));
			$b = JMapGtbHelper::unsignedRightShift ( $b, 23 ) | $b << 9;
		}
		return '8' . JMapGtbHelper::hexEncodeU32 ( $b );
	}
	
}

/**
 * Hash a variable-length key into a 32-bit value.
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapGtbJenkinsHash extends JMapSeostatsServicesGooglePagerank {
	/**
	 * Mix three 32-bit values reversibly
	 *
	 * @access private
	 * @static
	 * @return string
	 */
	private static function hashmixJenkins2($a, $b, $c) {
		$a -= $b;
		$a -= $c;
		$a ^= JMapGtbHelper::unsignedRightShift ( $c, 13 );
		$b -= $c;
		$b -= $a;
		$b ^= JMapGtbHelper::leftShift32 ( $a, 8 );
		$c -= $a;
		$c -= $b;
		$c ^= JMapGtbHelper::unsignedRightShift ( ($b & 0x00000000FFFFFFFF), 13 );
		$a -= $b;
		$a -= $c;
		$a ^= JMapGtbHelper::unsignedRightShift ( ($c & 0x00000000FFFFFFFF), 12 );
		$b -= $c;
		$b -= $a;
		$b = ($b ^ (JMapGtbHelper::leftShift32 ( $a, 16 ))) & 0x00000000FFFFFFFF;
		$c -= $a;
		$c -= $b;
		$c = ($c ^ (JMapGtbHelper::unsignedRightShift ( $b, 5 ))) & 0x00000000FFFFFFFF;
		$a -= $b;
		$a -= $c;
		$a = ($a ^ (JMapGtbHelper::unsignedRightShift ( $c, 3 ))) & 0x00000000FFFFFFFF;
		$b -= $c;
		$b -= $a;
		$b = ($b ^ (JMapGtbHelper::leftShift32 ( $a, 10 ))) & 0x00000000FFFFFFFF;
		$c -= $a;
		$c -= $b;
		$c = ($c ^ (JMapGtbHelper::unsignedRightShift ( $b, 15 ))) & 0x00000000FFFFFFFF;
		return array (
				$a,
				$b,
				$c
		);
	}
	
	/**
	 * Validates input, pass it to the hash function and return the result.
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function jenkinsHash($a) {
		$b = JMapGtbHelper::strOrds ( "info:" . $a );
		return self::_jenkinsHash ( $b );
	}
	
	/**
	 * Validates input, pass it to the hash function and return the result.
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function jenkinsHash2($a) {
		$ch = sprintf ( "%u", self::_jenkinsHash ( $a, false ) );
		$ch = ((JMapGtbHelper::leftShift32 ( ($ch / 7), 2 )) | ((JMapGtbHelper::_fmod ( $ch, 13 )) & 7));
		$buf = array (
				$ch 
		);
		for($i = 1; $i < 20; $i ++) {
			$buf [$i] = $buf [$i - 1] - 9;
		}
		return sprintf ( "6%u", self::_jenkinsHash ( JMapGtbHelper::c32to8bit ( $buf ), false ) );
	}
	
	/**
	 * Implements jenkish hash function
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function _jenkinsHash($key, $encode = true) {
		$url = $key;
		$length = sizeof ( $url ); // the key's length
		$a = $b = 0x000000009E3779B9; // the golden ratio; an arbitrary value
		$c = 0x00000000E6359A60; // the previous hash, or an arbitrary value
		$k = 0;
		$len = $length;
		while ( $len >= 12 ) { // handle most of the key
			$a += $url [$k + 0];
			$a += JMapGtbHelper::leftShift32 ( $url [$k + 1], 8 );
			$a += JMapGtbHelper::leftShift32 ( $url [$k + 2], 16 );
			$a += JMapGtbHelper::leftShift32 ( $url [$k + 3], 24 );
			$b += $url [$k + 4];
			$b += JMapGtbHelper::leftShift32 ( $url [$k + 5], 8 );
			$b += JMapGtbHelper::leftShift32 ( $url [$k + 6], 16 );
			$b += JMapGtbHelper::leftShift32 ( $url [$k + 7], 24 );
			$c += $url [$k + 8];
			$c += JMapGtbHelper::leftShift32 ( $url [$k + 9], 8 );
			$c += JMapGtbHelper::leftShift32 ( $url [$k + 10], 16 );
			$c += JMapGtbHelper::leftShift32 ( $url [$k + 11], 24 );
			$mix = self::hashmixJenkins2 ( $a, $b, $c );
			$a = $mix [0];
			$b = $mix [1];
			$c = $mix [2];
			$len -= 12;
			$k += 12;
		}
		$c += $length; // handle the last 11 bytes
		switch ($len) { // all the case statements fall through
			case 11 :
				$c += JMapGtbHelper::leftShift32 ( $url [$k + 10], 24 );
			case 10 :
				$c += JMapGtbHelper::leftShift32 ( $url [$k + 9], 16 );
			case 9 :
				$c += JMapGtbHelper::leftShift32 ( $url [$k + 8], 8 );
			// the first byte of $c is reserved for the length
			case 8 :
				$b += JMapGtbHelper::leftShift32 ( $url [$k + 7], 24 );
			case 7 :
				$b += JMapGtbHelper::leftShift32 ( $url [$k + 6], 16 );
			case 6 :
				$b += JMapGtbHelper::leftShift32 ( $url [$k + 5], 8 );
			case 5 :
				$b += $url [$k + 4];
			case 4 :
				$a += JMapGtbHelper::leftShift32 ( $url [$k + 3], 24 );
			case 3 :
				$a += JMapGtbHelper::leftShift32 ( $url [$k + 2], 16 );
			case 2 :
				$a += JMapGtbHelper::leftShift32 ( $url [$k + 1], 8 );
			case 1 :
				$a += $url [$k + 0];
			// case 0: nothing left to add
		}
		$mix = self::hashmixJenkins2 ( $a, $b, $c );
		$ch = JMapGtbHelper::mask32 ( $mix [2] );
		$ch = ($encode !== true) ? $ch : sprintf ( "6%u", $ch );
		return $ch;
	}
}


/**
 * Hash a variable-length key into a 32-bit value.
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapGtbIeHash extends JMapSeostatsServicesGooglePagerank {
	/**
	 * Validates input, pass it to the hash function and return the result.
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function ieHash($a) {
		return self::_ieHash ( $a );
	}
	
	/**
	 * Checksum algorithm used in the IE version of the Google Toolbar
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function _ieHash($a) {
		$NumHashString = sprintf ( '%u', self::hashmixIE ( $a ) );
		$NumHashLength = strlen ( $NumHashString );
		$CheckByte = 0;
		for($i = ($NumHashLength - 1); $i >= 0; $i --) {
			$Num = $NumHashString {$i};
			$CheckByte += (1 === ($i % 2)) ? ( int ) ((($Num * 2) / 10) + (($Num * 2) % 10)) : $Num;
		}
		$CheckByte %= 10;
		if ($CheckByte !== 0) {
			$CheckByte = 10 - $CheckByte;
			if (($NumHashLength % 2) === 1) {
				if (($CheckByte % 2) === 1) {
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}
		return '7' . $CheckByte . $NumHashString;
	}
	
	/**
	 * Generates a hash for a url provided by msieHash
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function hashmixIE($url) {
		$c1 = JMapGtbHelper::strToNum ( $url, 0x1505, 0x21 );
		$c2 = JMapGtbHelper::strToNum ( $url, 0, 0x1003f );
		$c1 = JMapGtbHelper::unsignedRightShift ( $c1, 2 );
		$c1 = (JMapGtbHelper::unsignedRightShift ( $c1, 4 ) & 0x3ffffc0) | ($c1 & 0x3f);
		$c1 = (JMapGtbHelper::unsignedRightShift ( $c1, 4 ) & 0x3ffc00) | ($c1 & 0x3ff);
		$c1 = (JMapGtbHelper::unsignedRightShift ( $c1, 4 ) & 0x3c000) | ($c1 & 0x3fff);
		$t1 = (JMapGtbHelper::leftShift32 ( (JMapGtbHelper::leftShift32 ( ($c1 & 0x3c0), 4 ) | ($c1 & 0x3c)), 2 )) | ($c2 & 0xf0f);
		$t2 = (JMapGtbHelper::leftShift32 ( (JMapGtbHelper::leftShift32 ( ($c1 & 0xffffc000), 4 ) | ($c1 & 0x3c00)), 0xa )) | ($c2 & 0xf0f0000);
		return JMapGtbHelper::mask32 ( ($t1 | $t2) );
	}
}

/**
 * Various helper methods for bitwise, bitshift and int operations.
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapGtbHelper extends JMapSeostatsServicesGooglePagerank {
	/**
	 * Safe bit-wise left shift
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function leftShift32($x, $y) {
		$n = $x << $y;
		if (PHP_INT_MAX != 0x80000000) {
			$n = - (~ ($n & 0x00000000FFFFFFFF) + 1);
		}
		return ( int ) $n;
	}
	
	/**
	 * Unsigned right bit shift
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function unsignedRightShift($x, $y) {
		// convert to 32 bits
		if (0xffffffff < $x || - 0xffffffff > $x) {
			$x = JMapGtbHelper::_fmod ( $x, 0xffffffff + 1 );
		} // convert to unsigned integer
		if (0x7fffffff < $x) {
			$x -= 0xffffffff + 1.0;
		} elseif (- 0x80000000 > $x) {
			$x += 0xffffffff + 1.0;
		} // do right shift
		if (0 > $x) {
			$x &= 0x7fffffff; // remove sign bit before shift
			$x >>= $y; // right shift
			$x |= 1 << (31 - $y); // set shifted sign bit
		} else {
			$x >>= $y; // use normal right shift
		}
		return ( int ) $x;
	}
	
	/**
	 * mask32 - On 64-bit platforms, masks integer $a and complements bits
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function mask32($a) {
		if (PHP_INT_MAX != 0x0000000080000000) { // 2147483647
			$a = - (~ ($a & 0x00000000FFFFFFFF) + 1);
		}
		return ( int ) $a;
	}
	
	/**
	 * Returns the hexadecimal string representation for U32 integer $a
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function hexEncodeU32($a) {
		$b = self::toHex8 ( self::unsignedRightShift ( $a, 24 ) );
		$b .= self::toHex8 ( self::unsignedRightShift ( $a, 16 ) & 255 );
		$b .= self::toHex8 ( self::unsignedRightShift ( $a, 8 ) & 255 );
		return $b . self::toHex8 ( $a & 255 );
	}
	
	/**
	 * Returns the hexadecimal string representation for integer $a
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function toHex8($a) {
		return ($a < 16 ? "0" : "") . dechex ( $a );
	}
	
	/**
	 * Unicode/multibyte capable equivelant of ord()
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function charCodeAt($a, $b) {
		$a = mb_convert_encoding ( $a, "UCS-4BE", "UTF-8" );
		$c = unpack ( "N", mb_substr ( $a, $b, 1, "UCS-4BE" ) );
		return $c [1];
	}
	
	/**
	 * Turns a string of unicode characters into an array of ordinal values
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function strOrds($a) {
		$b = array ();
		$a = mb_convert_encoding ( $a, "UCS-4BE", "UTF-8" );
		for($i = 0; $i < mb_strlen ( $a, "UCS-4BE" ); $i ++) {
			// Now we have 4 bytes. Find their total numeric value.
			$c = unpack ( "N", mb_substr ( $a, $i, 1, "UCS-4BE" ) );
			$b [] = $c [1];
		}
		return $b;
	}
	
	/**
	 * Converts an array of 32-bit integers into an array with 8-bit values.
	 * Equivalent to (BYTE *)arr32
	 *
	 * @access public
	 * @static
	 * @return array
	 */
	public static function c32to8bit($arr32) {
		for($i = 0; $i < sizeof ( $arr32 ); $i ++) {
			for($bitOrder = $i * 4; $bitOrder <= $i * 4 + 3; $bitOrder ++) {
				$arr8 [$bitOrder] = $arr32 [$i] & 255;
				$arr32 [$i] = self::unsignedRightShift ( $arr32 [$i], 8 );
			}
		}
		return $arr8;
	}
	
	/**
	 * Convert a string into a 32-bit integer
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function strToNum($str, $c, $k) {
		$int32unit = 4294967296; // 2^32
		for($i = 0; $i < strlen ( $str ); $i ++) {
			$c *= $k;
			if ($c >= $int32unit) {
				$c = ($c - $int32unit * ( int ) ($c / $int32unit));
				// if $c is less than -2^31
				$c = ($c < 0x0000000080000000) ? ($c + $int32unit) : $c;
			}
			$c += JMapGtbHelper::charCodeAt ( $str, $i );
		}
		return $c;
	}
	
	/**
	 * @access public
	 * @static
	 * @return string
	 */
	public static function _fmod($x, $y) {
		$i = floor ( $x / $y );
		return ( int ) ($x - $i * $y);
	}
	
	/**
	 * Returns $n random values from array $a
	 * 
	 * @access public
	 * @static
	 * @return string
	 */
	public static function array_rand_val($a, $n = 1) {
		shuffle ( $a );
		$b = array ();
		for($i = 0; $i < $n; $i ++) {
			$b [] = $a [$i];
		}
		return $n == 1 ? $b [0] : $b;
	}
	
	/**
	 * Returns $n random values from assoc array $a
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function array_rand_val_assoc($a, $n = 1) {
		$k = array_keys ( $a );
		shuffle ( $k );
		$b = array ();
		for($i = 0; $i < $n; $i ++) {
			$b [$k [$i]] = $a [$k [$i]];
		}
		return $b;
	}
	
	/**
	 * use regex to match values from string, if native json_decode is not available
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function _json_decode($a) {
		if (!function_exists ( 'json_decode' )) {
			$m = array ();
			preg_match_all ( '#"(.*?)"#si', $a, $m );
			return (isset ( $m [1] ) && sizeof ( $m [1] ) > 0) ? $m [1] : false;
		} else {
			return json_decode ( $a );
		}
	}
}

/**
 * Connection helper methods
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapGtbRequest extends JMapSeostatsServicesGooglePagerank {
	/**
	 * Simple get request HTTP curl based
	 *
	 * @access public
	 * @static
	 * @return string
	 */
	public static function _get($url) {
		$ua = sprintf ( 'JSitemap Professional %s http://storejextensions.org', strval(simplexml_load_file(JPATH_COMPONENT_ADMINISTRATOR . '/jmap.xml')->version) );
		
		$ch = curl_init ( $url );
		curl_setopt ( $ch, CURLOPT_USERAGENT, $ua );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
		if(!ini_get('open_basedir')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		curl_setopt ( $ch, CURLOPT_MAXREDIRS, 2 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		$str = curl_exec ( $ch );
		$httpCode = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
		curl_close ( $ch );
		
		// Connection success?
		$isDebug = JComponentHelper::getParams('com_jmap')->get('enable_debug', 0);
		if((int) $httpCode != 200 & !$str & $isDebug) {
			throw new JMapException(JText::_('COM_JMAP_NO_SERVICE_ANSWER'), 'notice');
		}
		
		return $str;
	}
}