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
 * Google stats service
 *
 * @package JMAP::SEOSTATS::administrator::components::com_jmap
 * @subpackage seostats
 * @subpackage services
 * @subpackage google
 * @since 3.0
 */
class JMapSeostatsServicesGoogle extends JMapSeostats {
	/**
	 * Gets the Google Pagerank
	 *
	 * @param string $url
	 *        	String, containing the query URL.
	 * @return integer Returns the Google PageRank.
	 */
	public static function getPageRank($url = false) {
		$gtb = new JMapSeostatsServicesGooglePagerank ( parent::getUrl ( $url ) );
		$result = $gtb->getPageRank ();
		
		return $result != null ? $result : static::noDataDefaultValue ();
	}
	
	/**
	 * Returns the total amount of results for a Google 'site:'-search for the object URL.
	 *
	 * @param string $url
	 *        	String, containing the query URL.
	 * @return integer Returns the total site-search result count.
	 */
	public static function getSiteindexTotal($url = false) {
		$url = parent::getUrl ( $url );
		$query = urlencode ( "site:{$url}" );
		
		return self::getSearchResultsTotal ( $query );
	}
	
	/**
	 * Returns the total amount of results for a Google 'link:'-search for the object URL.
	 *
	 * @param string $url
	 *        	String, containing the query URL.
	 * @return integer Returns the total link-search result count.
	 */
	public static function getBacklinksTotal($url = false) {
		$url = parent::getUrl ( $url );
		$query = urlencode ( "link:{$url}" );
		
		return self::getSearchResultsTotal ( $query );
	}
	
	/**
	 * Returns total amount of results for any Google search,
	 * requesting the deprecated Websearch API.
	 *
	 * @param string $url
	 *        	String, containing the query URL.
	 * @return integer Returns the total search result count.
	 */
	public static function getSearchResultsTotal($url = false) {
		$url = parent::getUrl ( $url );
		$url = sprintf ( JMapSeostatsServices::$GOOGLE_APISEARCH_URL, 1, $url );
		
		$ret = static::_getPage ( $url );
		
		$obj = json_decode ( $ret );
		return ! isset ( $obj->responseData->cursor->estimatedResultCount ) ? parent::noDataDefaultValue () : intval ( $obj->responseData->cursor->estimatedResultCount );
	}
	
	/**
	 * Public interface to get containing detailed results parsed and formatted for any Google search SERP
	 *
	 * @access public
	 * @param string $query The containing the search query.
	 * @param int $pageNumber The SERP page number requested
	 * @return array $customHeaders The custom headers for country and language to get SERP for
	 */
	public static function getSerps($query, $pageNumber = 0, $customHeaders = array()) {
		return JMapSeostatsServicesGoogleSearch::getSerps ( $query, $pageNumber, $customHeaders );
	}
}
